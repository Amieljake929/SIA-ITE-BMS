<?php
declare(strict_types=1);

header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: GET, POST, DELETE, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') { http_response_code(204); exit; }
header('Content-Type: application/json; charset=utf-8');

const API_TOKEN = 'RIS_TO_BMS_USERS_2025';

// Accept header OR query string
$auth  = $_SERVER['HTTP_AUTHORIZATION'] ?? ($_SERVER['REDIRECT_HTTP_AUTHORIZATION'] ?? '');
$qsTok = $_GET['api_token'] ?? '';
$token = '';
if (preg_match('/Bearer\s+(.+)/i', $auth, $m)) { $token = trim($m[1]); }
elseif (!empty($qsTok)) { $token = (string)$qsTok; }

if ($token !== API_TOKEN) {
  http_response_code(401);
  echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
  exit;
}

// connect to BMS DB
require_once __DIR__ . '/../login/db_connect.php';

function out($arr, $code = 200) {
  http_response_code($code);
  echo json_encode($arr, JSON_UNESCAPED_SLASHES);
  exit;
}

$method = $_SERVER['REQUEST_METHOD'];

if ($method === 'GET') {
  $role = isset($_GET['role']) ? trim($_GET['role']) : '';
  $allowedRoles = ['Resident','Official','Staff','BPSO'];

  if ($role !== '' && !in_array($role, $allowedRoles, true)) {
    out(['ok'=>false,'error'=>'Invalid role'], 400);
  }

  if ($role !== '') {
    $stmt = $conn->prepare(
      "SELECT id, full_name, email, role, status, created_at
       FROM users WHERE role = ? ORDER BY created_at DESC, id DESC"
    );
    $stmt->bind_param('s', $role);
  } else {
    $stmt = $conn->prepare(
      "SELECT id, full_name, email, role, status, created_at
       FROM users ORDER BY created_at DESC, id DESC"
    );
  }

  if (!$stmt->execute()) out(['ok'=>false,'error'=>'Query failed'], 500);
  $res = $stmt->get_result();

  $items = [];
  while ($r = $res->fetch_assoc()) {
    $items[] = [
      'id'         => (int)$r['id'],
      'full_name'  => (string)$r['full_name'],
      'email'      => (string)$r['email'],
      'role'       => (string)$r['role'],
      'status'     => (string)($r['status'] ?? ''),
      'created_at' => (string)$r['created_at']
    ];
  }
  out(['ok'=>true, 'count'=>count($items), 'items'=>$items]);
}

if ($method === 'POST') {
  $raw = file_get_contents('php://input');
  $d = json_decode($raw, true);
  if (!is_array($d)) out(['ok'=>false,'error'=>'Invalid JSON'], 400);

  $full_name = trim($d['full_name'] ?? '');
  $email     = trim($d['email'] ?? '');
  $password  = (string)($d['password'] ?? '');
  $role      = trim($d['role'] ?? '');
  $status    = trim($d['status'] ?? 'pending');

  $allowedRoles = ['Resident','Official','Staff','BPSO'];
  if ($full_name === '' || $email === '' || $password === '' || !in_array($role, $allowedRoles, true)) {
    out(['ok'=>false,'error'=>'Missing or invalid fields'], 400);
  }

  $pass_hash = password_hash($password, PASSWORD_BCRYPT);

  $stmt = $conn->prepare(
    "INSERT INTO users (full_name, email, password, role, status, created_at)
     VALUES (?, ?, ?, ?, ?, NOW())"
  );
  $stmt->bind_param('sssss', $full_name, $email, $pass_hash, $role, $status);

  if (!$stmt->execute()) out(['ok'=>false, 'error'=>'Insert failed'], 500);

  out(['ok'=>true, 'id'=>$stmt->insert_id]);
}

if ($method === 'DELETE') {
  $id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
  if ($id <= 0) out(['ok'=>false,'error'=>'Invalid id'], 400);

  $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
  $stmt->bind_param('i', $id);
  if (!$stmt->execute()) out(['ok'=>false, 'error'=>'Delete failed'], 500);

  out(['ok'=>true, 'deleted'=>1]);
}

out(['ok'=>false,'error'=>'Method not allowed'], 405);
