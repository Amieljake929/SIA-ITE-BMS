<?php
// C:\xampp\htdocs\ITE-SIA\BMS\staff\Programs\api_programs_delete.php
declare(strict_types=1);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
header('Content-Type: application/json; charset=utf-8');

const API_TOKEN = 'RIS_TO_BMS_PROGRAMS_UPLOAD_2025'; // <<< same token as other APIs

// Accept token via header or query (?api_token=...)
$auth  = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$qsTok = $_GET['api_token'] ?? '';
$token = '';
if (preg_match('/Bearer\s+(.+)/i', $auth, $m)) { $token = trim($m[1]); }
elseif (!empty($qsTok)) { $token = (string)$qsTok; }

if ($token !== API_TOKEN) {
  http_response_code(401);
  echo json_encode(['ok'=>false,'error'=>'Unauthorized']);
  exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
  http_response_code(405);
  echo json_encode(['ok'=>false,'error'=>'Method not allowed']);
  exit;
}

$id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
if ($id <= 0) {
  http_response_code(400);
  echo json_encode(['ok'=>false,'error'=>'Invalid id']);
  exit;
}

require_once '../../login/db_connect.php'; // DB: bms

// get row
$stmt = $conn->prepare("SELECT file_path FROM programs WHERE id = ?");
$stmt->bind_param('i', $id);
$stmt->execute();
$res = $stmt->get_result();
if (!$row = $res->fetch_assoc()) {
  echo json_encode(['ok'=>false,'error'=>'Not found']);
  exit;
}
$stmt->close();

// build absolute FS path safely
$uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'programs'; // .../staff/Programs/uploads/programs
$rel = ltrim((string)$row['file_path'], '/'); // may be "uploads/programs/..." or "Programs/uploads/programs/..."

if (strpos($rel, 'Programs/') === 0) {
  $rel = substr($rel, strlen('Programs/')); // strip "Programs/"
}
$basename = basename($rel); // filenames are unique -> safe
$abs = $uploadDir . DIRECTORY_SEPARATOR . $basename;

$deletedFile = false;
if (is_file($abs)) { $deletedFile = @unlink($abs); }

// delete DB row
$del = $conn->prepare("DELETE FROM programs WHERE id = ?");
$del->bind_param('i', $id);
$del->execute();
$del->close();

echo json_encode([
  'ok' => true,
  'file_deleted' => $deletedFile,
  'id' => $id,
]);
