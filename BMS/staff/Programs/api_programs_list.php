<?php
// C:\xampp\htdocs\ITE-SIA\BMS\staff\Programs\api_programs_list.php
declare(strict_types=1);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
header('Content-Type: application/json; charset=utf-8');

// SAME token as upload API
const API_TOKEN = 'RIS_TO_BMS_PROGRAMS_UPLOAD_2025';

// Accept Authorization header OR ?api_token=... fallback
$auth  = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$qsTok = $_GET['api_token'] ?? '';
$token = '';
if (preg_match('/Bearer\s+(.+)/i', $auth, $m)) { $token = trim($m[1]); }
elseif (!empty($qsTok)) { $token = (string)$qsTok; }

if ($token !== API_TOKEN) {
  http_response_code(401);
  echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
  exit;
}

// DB: must point to 'bms'
require_once '../../login/db_connect.php';

$sql = "SELECT id, file_name, file_path, created_at FROM programs
        ORDER BY created_at DESC, id DESC";
$res = $conn->query($sql);

$items = [];
while ($row = $res->fetch_assoc()) {
  $fp = ltrim($row['file_path'], '/'); // e.g. "uploads/programs/xxx.jpg" OR "Programs/uploads/programs/xxx.jpg"

  // Build absolute URL that RIS can use directly
  if (strpos($fp, 'Programs/') === 0) {
    // already includes "Programs/"
    $abs = '/ITE-SIA/BMS/staff/' . $fp;
  } else {
    // stored without "Programs/"
    $abs = '/ITE-SIA/BMS/staff/Programs/' . $fp;
  }

  $items[] = [
    'id'          => (int)$row['id'],
    'file_name'   => $row['file_name'],
    'file_path'   => $row['file_path'],
    'created_at'  => $row['created_at'],
    'absolute_src'=> $abs,
  ];
}

echo json_encode([
  'ok'    => true,
  'count' => count($items),
  'items' => $items
], JSON_UNESCAPED_SLASHES);
