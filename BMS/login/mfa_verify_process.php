<?php
session_start();
header('Content-Type: application/json');
ob_start();

if ((empty($_SESSION['mfa_resident_id']) && empty($_SESSION['mfa_barangay_id'])) || empty($_SESSION['mfa_user_id'])) {
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
$conn->query("DELETE FROM barangay_mfa WHERE expires_at <= NOW()");

$userId = (int)$_SESSION['mfa_user_id'];
$code   = trim($_POST['code'] ?? '');

if (!preg_match('/^\d{6}$/', $code)) {
  if (ob_get_length()) ob_clean();
  echo json_encode(['success'=>false,'message'=>'Invalid code format.']); exit;
}

try{
  $mfaId = null;
  $redirect_url = '';

  // Check if it's a resident MFA
  if (!empty($_SESSION['mfa_resident_id'])) {
    $residentId = (int)$_SESSION['mfa_resident_id'];
    $stmt = $conn->prepare("SELECT id FROM resident_mfa WHERE resident_id = ? AND code = ? AND expires_at > NOW() LIMIT 1");
    if (!$stmt) throw new Exception('prep resident mfa find: '.$conn->error);
    $stmt->bind_param('is', $residentId, $code);
    $stmt->execute(); $stmt->store_result();
    if ($stmt->num_rows > 0) {
      $stmt->bind_result($mfaId); $stmt->fetch();
      $redirect_url = '../resident/resident_dashboard.php';
      $_SESSION['resident_id'] = $residentId;
    }
    $stmt->close();
  }

  // Check if it's a barangay MFA
  if (!$mfaId && !empty($_SESSION['mfa_barangay_id'])) {
    $barangayId = (int)$_SESSION['mfa_barangay_id'];
    $stmt = $conn->prepare("SELECT id FROM barangay_mfa WHERE barangay_id = ? AND code = ? AND expires_at > NOW() LIMIT 1");
    if (!$stmt) throw new Exception('prep barangay mfa find: '.$conn->error);
    $stmt->bind_param('is', $barangayId, $code);
    $stmt->execute(); $stmt->store_result();
    if ($stmt->num_rows > 0) {
      $stmt->bind_result($mfaId); $stmt->fetch();
      $role = strtolower(trim($_SESSION['mfa_role'] ?? ''));
      switch ($role) {
        case 'official':
          $redirect_url = '../official/official_dashboard.php';
          break;
        case 'staff':
          $redirect_url = '../staff/staff_dashboard.php';
          break;
        case 'bpso':
          $redirect_url = '../BPSO/BPSO_dashboard.php';
          break;
        default:
          $redirect_url = '../resident/resident_dashboard.php';
      }
    }
    $stmt->close();
  }

  if (!$mfaId) {
    if (ob_get_length()) ob_clean();
    echo json_encode(['success'=>false,'message'=>'Invalid or expired code.']); exit;
  }

  // mark verified (keep code for reuse within expiration window)
  // For resident MFA
  if (!empty($_SESSION['mfa_resident_id'])) {
    $upd = $conn->prepare("UPDATE resident_mfa SET verified = 1 WHERE id = ?");
    if (!$upd) throw new Exception('prep resident mfa update: '.$conn->error);
    $upd->bind_param('i', $mfaId); $upd->execute(); $upd->close();
  }
  // For barangay MFA
  elseif (!empty($_SESSION['mfa_barangay_id'])) {
    $upd = $conn->prepare("UPDATE barangay_mfa SET verified = 1 WHERE id = ?");
    if (!$upd) throw new Exception('prep barangay mfa update: '.$conn->error);
    $upd->bind_param('i', $mfaId); $upd->execute(); $upd->close();
  }

  // grant full session
  $_SESSION['user_id']   = $userId;
  // reuse temp data saved during login
  $_SESSION['full_name'] = $_SESSION['mfa_full_name'] ?? ($_SESSION['full_name'] ?? '');
  $_SESSION['email']     = $_SESSION['mfa_email'] ?? ($_SESSION['email'] ?? '');
  $_SESSION['role']      = $_SESSION['mfa_role'] ?? 'resident';

  // clear MFA temp
  unset($_SESSION['mfa_user_id'], $_SESSION['mfa_resident_id'], $_SESSION['mfa_barangay_id'], $_SESSION['mfa_full_name'], $_SESSION['mfa_email'], $_SESSION['mfa_role'], $_SESSION['mfa_expires']);

  if (ob_get_length()) ob_clean();
  echo json_encode(['success'=>true,'redirect_url'=>$redirect_url]);
  exit;

} catch (Throwable $e){
  error_log('[MFA VERIFY FATAL] '.$e->getMessage());
  if (ob_get_length()) ob_clean();
  echo json_encode(['success'=>false,'message'=>'Something went wrong. Please try again later.']);
  exit;
}
