<?php
// C:\xampp\htdocs\ITE-SIA\BMS\staff\Programs\api_programs_upload.php
// REST-like API para mag-upload ng images sa BMS Programs gallery

declare(strict_types=1);

// ====== CORS (same-origin ok din; pwede mo higpitan pag na-deploy) ======
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type, Authorization');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit;
}
header('Content-Type: application/json; charset=utf-8');

// ====== Simple API auth (Bearer token) ======
const API_TOKEN = 'RIS_TO_BMS_PROGRAMS_UPLOAD_2025'; // <<< palitan mo sa parehong value sa RIS page

$auth = $_SERVER['HTTP_AUTHORIZATION'] ?? '';
$token = 'RIS_TO_BMS_PROGRAMS_UPLOAD_2025';
if (preg_match('/Bearer\s+(.+)/i', $auth, $m)) {
    $token = trim($m[1]);
} elseif (!empty($_POST['api_token'])) {
    // fallback form field (optional)
    $token = (string)$_POST['api_token'];
}
if ($token !== API_TOKEN) {
    http_response_code(401);
    echo json_encode(['ok' => false, 'error' => 'Unauthorized']);
    exit;
}

// ====== DB (BMS) ======
require_once '../../login/db_connect.php'; // must point to DB: bms

// Auto-create table if missing (same schema as staff uploader)
$createSql = "
CREATE TABLE IF NOT EXISTS `programs` (
  `id` INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `file_name` VARCHAR(255) NOT NULL,
  `file_path` VARCHAR(500) NOT NULL,
  `uploaded_by` INT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
";
$conn->query($createSql);

// ====== Paths (inside staff/Programs) ======
$uploadDir  = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'programs'; // FS
$publicBase = 'uploads/programs'; // ito ang ise-save sa DB (relative to staff/Programs)
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0775, true);
}

function is_valid_image(string $tmp, ?string &$extOut): bool {
    $extOut = null;
    $finfo  = finfo_open(FILEINFO_MIME_TYPE);
    $mime   = finfo_file($finfo, $tmp);
    finfo_close($finfo);
    $map = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];
    if (isset($map[$mime])) { $extOut = $map[$mime]; return true; }
    return false;
}

// ====== Handle upload ======
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['ok' => false, 'error' => 'Method not allowed']);
    exit;
}
if (empty($_FILES['images'])) {
    http_response_code(400);
    echo json_encode(['ok' => false, 'error' => 'No files uploaded']);
    exit;
}

$uploadedBy = isset($_POST['uploaded_by']) ? (int)$_POST['uploaded_by'] : null;
$files  = $_FILES['images'];
$count  = is_array($files['name']) ? count($files['name']) : 0;
$ok     = 0;
$items  = [];
$errors = [];

$stmt = $conn->prepare("INSERT INTO programs (file_name, file_path, uploaded_by) VALUES (?, ?, ?)");

for ($i = 0; $i < $count; $i++) {
    if ($files['error'][$i] !== UPLOAD_ERR_OK) {
        $errors[] = 'Upload error for ' . htmlspecialchars($files['name'][$i]);
        continue;
    }
    if ($files['size'][$i] > 5 * 1024 * 1024) { // 5MB
        $errors[] = htmlspecialchars($files['name'][$i]) . ' exceeds 5MB.';
        continue;
    }
    $ext = null;
    if (!is_valid_image($files['tmp_name'][$i], $ext)) {
        $errors[] = htmlspecialchars($files['name'][$i]) . ' is not a supported image.';
        continue;
    }

    $unique = 'img_' . date('Ymd_His') . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
    $toFs   = $uploadDir . DIRECTORY_SEPARATOR . $unique;            // filesystem
    $toUrl  = $publicBase . '/' . $unique;                           // DB value

    if (!move_uploaded_file($files['tmp_name'][$i], $toFs)) {
        $errors[] = 'Failed to save ' . htmlspecialchars($files['name'][$i]) . '.';
        continue;
    }
    @chmod($toFs, 0644);

    $origName = $files['name'][$i];
    $stmt->bind_param('ssi', $origName, $toUrl, $uploadedBy);
    $stmt->execute();

    $ok++;
    $items[] = [
        'id'         => $conn->insert_id,
        'file_name'  => $origName,
        'file_path'  => $toUrl,
        'public_src' => 'Programs/' . $toUrl, // useful kapag kakailanganin mo i-render via /ITE-SIA/BMS/staff/ + public_src
    ];
}
$stmt->close();

// Response
echo json_encode([
    'ok'       => $ok > 0,
    'uploaded' => $items,
    'errors'   => $errors,
    'count'    => $ok,
], JSON_UNESCAPED_SLASHES);
