<?php
session_start();
header('Content-Type: application/json');
ob_start();

if (empty($_SESSION['mfa_resident_id']) || empty($_SESSION['mfa_user_id'])) {
  if (ob_get_length()) ob_clean();
  echo json_encode(['success'=>false,'message'=>'Session expired. Please login again.']);
  exit;
}

require_once 'db_connect.php';
@ini_set('log_errors','1');
@ini_set('error_log', __DIR__.'/login_debug.log');
error_reporting(E_ALL);

// Cleanup expired tokens globally
$conn->query("DELETE FROM resident_mfa WHERE expires_at <= NOW()");

$residentId = (int)$_SESSION['mfa_resident_id'];
$userId     = (int)$_SESSION['mfa_user_id'];
$code       = trim($_POST['code'] ?? '');

if (!preg_match('/^\d{6}$/', $code)) {
  if (ob_get_length()) ob_clean();
  echo json_encode(['success'=>false,'message'=>'Invalid code format.']); exit;
}

try{
  // find matching, unexpired, unverified code for this resident
  $stmt = $conn->prepare("SELECT id FROM resident_mfa WHERE resident_id = ? AND code = ? AND verified = 0 AND expires_at > NOW() LIMIT 1");
  if (!$stmt) throw new Exception('prep mfa find: '.$conn->error);
  $stmt->bind_param('is', $residentId, $code);
  $stmt->execute(); $stmt->store_result();
  if ($stmt->num_rows === 0) {
    $stmt->close();
    if (ob_get_length()) ob_clean();
    echo json_encode(['success'=>false,'message'=>'Invalid or expired code.']); exit;
  }
  $stmt->bind_result($mfaId); $stmt->fetch(); $stmt->close();

  // mark verified + null code so it can't be reused
  $upd = $conn->prepare("UPDATE resident_mfa SET verified = 1, code = NULL WHERE id = ?");
  if (!$upd) throw new Exception('prep mfa update: '.$conn->error);
  $upd->bind_param('i', $mfaId); $upd->execute(); $upd->close();

  // grant full session (keep both user_id & resident_id)
  $_SESSION['user_id']     = $userId;
  $_SESSION['resident_id'] = $residentId;
  // reuse temp data saved during login
  $_SESSION['full_name']   = $_SESSION['mfa_full_name'] ?? ($_SESSION['full_name'] ?? '');
  $_SESSION['email']       = $_SESSION['mfa_email'] ?? ($_SESSION['email'] ?? '');
  $_SESSION['role']        = $_SESSION['mfa_role'] ?? 'resident';

  // clear MFA temp
  unset($_SESSION['mfa_user_id'], $_SESSION['mfa_resident_id'], $_SESSION['mfa_full_name'], $_SESSION['mfa_email'], $_SESSION['mfa_role'], $_SESSION['mfa_expires']);

  if (ob_get_length()) ob_clean();
  echo json_encode(['success'=>true,'redirect_url'=>'../resident/resident_dashboard.php']);
  exit;

} catch (Throwable $e){
  error_log('[MFA VERIFY FATAL] '.$e->getMessage());
  if (ob_get_length()) ob_clean();
  echo json_encode(['success'=>false,'message'=>'Something went wrong. Please try again later.']);
  exit;
}
