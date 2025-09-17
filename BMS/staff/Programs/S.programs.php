<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'Staff') {
    header("Location: login.php");
    exit();
}

include '../../login/db_connect.php'; // dapat naka-point sa DB: bms

/* ==============================
   Paths (inside BMS/staff)
   ============================== */
$uploadDir = __DIR__ . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'programs';
$publicBase = 'uploads/programs'; // relative URL from staff/

/* Ensure upload dir exists */
if (!is_dir($uploadDir)) {
    @mkdir($uploadDir, 0775, true);
}

/* ==============================
   Create table if not exists
   ============================== */
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

/* ==============================
   CSRF + flash helpers
   ============================== */
if (empty($_SESSION['csrf'])) {
    $_SESSION['csrf'] = bin2hex(random_bytes(32));
}
$csrf = $_SESSION['csrf'];

function set_flash($type, $msg) {
    $_SESSION['flash_gallery'] = ['type' => $type, 'msg' => $msg];
}
function get_flash() {
    if (!empty($_SESSION['flash_gallery'])) {
        $f = $_SESSION['flash_gallery'];
        unset($_SESSION['flash_gallery']);
        return $f;
    }
    return null;
}

/* ==============================
   Utils
   ============================== */
function is_valid_image($tmpPath, &$extOut) {
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime  = finfo_file($finfo, $tmpPath);
    finfo_close($finfo);
    $map = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/gif'  => 'gif',
        'image/webp' => 'webp',
    ];
    if (isset($map[$mime])) {
        $extOut = $map[$mime];
        return true;
    }
    return false;
}

/* ==============================
   Handle POST (upload/delete)
   ============================== */
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if (!hash_equals($_SESSION['csrf'] ?? '', $_POST['csrf'] ?? '')) {
        set_flash('error', 'Invalid CSRF token.');
        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }

    /* ---- Upload ---- */
    if ($_POST['action'] === 'upload' && !empty($_FILES['images'])) {
        $files   = $_FILES['images'];
        $count   = count($files['name']);
        $ok      = 0; $errs = [];

        $stmt = $conn->prepare("INSERT INTO programs (file_name, file_path, uploaded_by) VALUES (?, ?, ?)");
        for ($i = 0; $i < $count; $i++) {
            if ($files['error'][$i] !== UPLOAD_ERR_OK) {
                $errs[] = 'Upload error for ' . htmlspecialchars($files['name'][$i]);
                continue;
            }
            if ($files['size'][$i] > 5 * 1024 * 1024) {
                $errs[] = htmlspecialchars($files['name'][$i]) . ' exceeds 5MB.';
                continue;
            }

            $ext = null;
            if (!is_valid_image($files['tmp_name'][$i], $ext)) {
                $errs[] = htmlspecialchars($files['name'][$i]) . ' is not a supported image.';
                continue;
            }

            $uniqueName = 'img_' . date('Ymd_His') . '_' . bin2hex(random_bytes(3)) . '.' . $ext;
            $destFs = $uploadDir . DIRECTORY_SEPARATOR . $uniqueName;               // filesystem path (Windows ok)
            $destUrl = $publicBase . '/' . $uniqueName;                              // relative URL for <img>

            if (!move_uploaded_file($files['tmp_name'][$i], $destFs)) {
                $errs[] = 'Failed to save ' . htmlspecialchars($files['name'][$i]) . '.';
                continue;
            }
            @chmod($destFs, 0644);

            $fileName = $files['name'][$i];
            $uploadedBy = intval($_SESSION['user_id']);

            $stmt->bind_param('ssi', $fileName, $destUrl, $uploadedBy);
            $stmt->execute();
            $ok++;
        }
        $stmt->close();

        if ($ok > 0) {
            set_flash('success', "Successfully uploaded {$ok} image(s)." . (count($errs) ? " Some failed." : ""));
        } else {
            set_flash('error', "No images uploaded. " . (count($errs) ? implode(' ', $errs) : ''));
        }
        header('Location: ' . $_SERVER['PHP_SELF']); // PRG
        exit();
    }

    /* ---- Delete ---- */
    if ($_POST['action'] === 'delete') {
        $id = isset($_POST['id']) ? (int)$_POST['id'] : 0;
        if ($id <= 0) {
            set_flash('error', 'Invalid image ID.');
            header('Location: ' . $_SERVER['PHP_SELF']);
            exit();
        }

        // Get file_path from DB
        $get = $conn->prepare("SELECT file_path FROM programs WHERE id = ?");
        $get->bind_param('i', $id);
        $get->execute();
        $res = $get->get_result();
        if ($row = $res->fetch_assoc()) {
            $filePathUrl = $row['file_path'];                  // e.g., uploads/programs/xxx.jpg
            $abs = $uploadDir . DIRECTORY_SEPARATOR . basename($filePathUrl); // safe join
            if (is_file($abs)) { @unlink($abs); }

            // Delete DB row
            $del = $conn->prepare("DELETE FROM programs WHERE id = ?");
            $del->bind_param('i', $id);
            $del->execute();
            $del->close();

            set_flash('success', 'Image deleted.');
        } else {
            set_flash('error', 'Image not found.');
        }
        $get->close();

        header('Location: ' . $_SERVER['PHP_SELF']);
        exit();
    }
}

/* ==============================
   List images from DB
   ============================== */
$images = [];
$q = $conn->query("SELECT id, file_name, file_path, created_at FROM programs ORDER BY created_at DESC, id DESC");
if ($q) {
    while ($r = $q->fetch_assoc()) { $images[] = $r; }
}
$flash = get_flash();
// $conn->close(); // optional
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Bagbag eServices - Official Portal</title>

  <!-- Tailwind CSS via CDN -->
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>
<body class="bg-gray-50 text-gray-800 font-sans flex flex-col min-h-screen">

  <!-- Top Bar -->
  <div class="bg-gradient-to-r from-green-800 to-green-900 text-white text-sm px-6 py-3 flex justify-between items-center shadow-md">
    <div class="flex-1">
      <span id="datetime" class="font-medium tracking-wide">LOADING...</span>
    </div>
    <div class="flex-shrink-0">
      <img src="../../images/Bagbag.png" alt="Bagbag Logo" class="h-12 object-contain drop-shadow" />
    </div>
  </div>

  <!-- Main Header -->
  <header class="bg-white shadow-lg border-b border-green-100 px-6 py-4 relative">
    <div class="container mx-auto flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
      <!-- Home Icon Button and Title -->
      <div class="flex items-center space-x-4">
        <button
          class="flex items-center justify-center w-10 h-10 rounded-full bg-yellow-500 text-gray-800 hover:bg-yellow-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-yellow-300"
          onclick="window.location.href='../staff_dashboard.php'"
          title="Home">
          <i class="fas fa-home text-white" style="font-size: 1.2rem;"></i>
        </button>
        <h1 class="text-xl font-bold text-green-800">Programs</h1>
      </div>

      <!-- User Info with Dropdown -->
      <div class="relative inline-block text-right">
        <button id="userMenuButton" class="flex items-center font-medium cursor-pointer text-sm focus:outline-none whitespace-nowrap">
          <span class="text-gray-800">Logged in:</span>
          <span class="text-blue-700 ml-1"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
          <i class="fas fa-chevron-down ml-2 text-gray-400"></i>
        </button>

        <div id="userDropdown" class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-xl hidden z-10">
          <ul class="py-2 text-sm">
            <li>
              <a href="#" class="block px-5 py-2 text-gray-700 hover:bg-green-50 hover:text-green-800 transition-colors duration-150 flex items-center">
                <i class="fas fa-user text-green-600 mr-3"></i> Profile
              </a>
            </li>
            <li>
              <a href="../../login/logout_official.php" class="block px-5 py-2 text-gray-700 hover:bg-red-50 hover:text-red-800 transition-colors duration-150 flex items-center">
                <i class="fas fa-sign-out-alt text-red-600 mr-3"></i> Logout
              </a>
            </li>
          </ul>
        </div>
      </div>
    </div>
  </header>

  <!-- =============== MAIN CONTENT: Upload + Gallery (DB-backed) =============== -->
  <main class="container mx-auto px-6 py-8 w-full max-w-7xl">
    <!-- Flash -->
    <?php if ($flash): ?>
      <div class="mb-6 rounded-lg px-4 py-3 text-sm <?php echo $flash['type'] === 'success' ? 'bg-green-50 text-green-800 border border-green-200' : 'bg-red-50 text-red-800 border border-red-200'; ?>">
        <i class="fas <?php echo $flash['type'] === 'success' ? 'fa-check-circle' : 'fa-exclamation-triangle'; ?> mr-2"></i>
        <?php echo htmlspecialchars($flash['msg']); ?>
      </div>
    <?php endif; ?>

    <!-- Upload Card -->
    <section class="bg-white rounded-2xl shadow-lg p-6 mb-8">
      <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
        <i class="fa-solid fa-images mr-2 text-green-700"></i> Upload Images
      </h2>

      <form id="uploadForm" method="POST" enctype="multipart/form-data" class="space-y-4">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf); ?>">
        <input type="hidden" name="action" value="upload">

        <!-- Dropzone -->
        <div id="dropzone"
             class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center transition hover:border-green-400 hover:bg-green-50 cursor-pointer">
          <input id="fileInput" name="images[]" type="file" accept="image/*" multiple class="hidden" />
          <div class="flex flex-col items-center justify-center space-y-2">
            <i class="fa-solid fa-cloud-arrow-up text-3xl"></i>
            <p class="text-sm text-gray-600">
              Drag & drop images here or
              <span class="text-green-700 font-semibold underline" id="browseTrigger">browse</span>
            </p>
            <p class="text-xs text-gray-400">JPEG/PNG/GIF/WEBP, up to 5 MB each</p>
          </div>
        </div>

        <!-- Selected Preview -->
        <div id="previewWrap" class="hidden">
          <h3 class="text-sm font-semibold text-gray-700 mt-4 mb-2">Selected files</h3>
          <div id="previewGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3"></div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
          <button type="reset"
                  class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm">
            Clear
          </button>
          <button id="uploadBtn" type="submit"
                  class="px-5 py-2 rounded-lg bg-green-700 hover:bg-green-800 text-white text-sm disabled:opacity-50 disabled:cursor-not-allowed"
                  disabled>
            <i class="fa-solid fa-upload mr-2"></i> Upload
          </button>
        </div>
      </form>
    </section>

    <!-- Gallery -->
    <section class="bg-white rounded-2xl shadow-lg p-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-gray-800 flex items-center">
          <i class="fa-solid fa-photo-film mr-2 text-green-700"></i> Gallery
        </h2>
        <p class="text-xs text-gray-500"><?php echo count($images); ?> image(s)</p>
      </div>

      <?php if (empty($images)): ?>
        <div class="text-gray-500 text-sm">No images yet. Upload above to get started.</div>
      <?php else: ?>
        <div class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4">
          <?php foreach ($images as $img): ?>
            <div class="group relative rounded-xl overflow-hidden shadow hover:shadow-lg transition">
              <!-- Square thumb container -->
              <div class="relative pt-[100%] bg-gray-100">
                <img
                  src="<?php echo htmlspecialchars($img['file_path']); ?>"
                  alt="program image"
                  class="absolute inset-0 w-full h-full object-cover" loading="lazy" />
              </div>

              <!-- Top-right controls -->
              <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
                <form method="POST" onsubmit="return confirm('Delete this image?');">
                  <input type="hidden" name="csrf" value="<?php echo htmlspecialchars($csrf); ?>">
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?php echo (int)$img['id']; ?>">
                  <button type="submit"
                          class="p-2 rounded-full bg-red-600 hover:bg-red-700 text-white shadow">
                    <i class="fa-solid fa-trash"></i>
                  </button>
                </form>
              </div>

              <!-- Footer filename + date -->
              <div class="px-3 py-2">
                <div class="text-xs text-gray-700 truncate" title="<?php echo htmlspecialchars($img['file_name']); ?>">
                  <?php echo htmlspecialchars($img['file_name']); ?>
                </div>
                <div class="text-[11px] text-gray-400">
                  <?php echo htmlspecialchars(date('M d, Y h:i A', strtotime($img['created_at']))); ?>
                </div>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      <?php endif; ?>
    </section>
  </main>

  <!-- Footer -->
  <footer class="bg-green-900 text-white text-center py-5 text-sm mt-auto">
    &copy; <?= date('Y') ?> Bagbag eServices. All rights reserved. <br class="sm:hidden"> | Empowering Communities Digitally.
  </footer>

  <!-- Mobile Menu (optional UX from your template) -->
  <div id="mobileMenu" class="fixed inset-0 bg-black bg-opacity-50 z-50 hidden flex items-center justify-center">
    <div class="bg-white w-4/5 max-w-xs rounded-lg shadow-xl p-6">
      <h3 class="text-lg font-bold text-gray-800 mb-4">Navigation</h3>
      <ul class="space-y-3">
        <li><a href="#" class="block text-green-700 hover:text-green-900 font-medium">Home</a></li>
        <li><a href="#" class="block text-green-700 hover:text-green-900 font-medium">Services</a></li>
        <li><a href="#" class="block text-green-700 hover:text-green-900 font-medium">About</a></li>
        <li><a href="#" class="block text-green-700 hover:text-green-900 font-medium">Contact</a></li>
        <li><a href="logout.php" class="block text-green-700 hover:text-green-900 font-medium">Logout</a></li>
      </ul>
      <button id="closeMenu" class="mt-4 text-red-500 text-sm">Close</button>
    </div>
  </div>

  <!-- JavaScript -->
  <script>
    // Clock
    function updateTime() {
      const now = new Date();
      const options = { weekday:'long', year:'numeric', month:'long', day:'numeric', hour:'2-digit', minute:'2-digit', second:'2-digit' };
      document.getElementById('datetime').textContent = now.toLocaleString('en-US', options).toUpperCase();
    }
    setInterval(updateTime, 1000); updateTime();

    // User Dropdown Toggle
    const userMenuButton = document.getElementById('userMenuButton');
    const userDropdown   = document.getElementById('userDropdown');
    if (userMenuButton) {
      userMenuButton.addEventListener('click', (e) => {
        e.stopPropagation();
        userDropdown.classList.toggle('hidden');
      });
      document.addEventListener('click', (e) => {
        if (!userMenuButton.contains(e.target) && !userDropdown.contains(e.target)) {
          userDropdown.classList.add('hidden');
        }
      });
    }

    // --------- Upload UI (drag & drop + preview) ---------
    const dropzone      = document.getElementById('dropzone');
    const fileInput     = document.getElementById('fileInput');
    const browseTrigger = document.getElementById('browseTrigger');
    const previewWrap   = document.getElementById('previewWrap');
    const previewGrid   = document.getElementById('previewGrid');
    const uploadBtn     = document.getElementById('uploadBtn');

    function showPreview(files) {
      previewGrid.innerHTML = '';
      const list = Array.from(files).filter(f => f.type.startsWith('image/'));
      if (!list.length) {
        previewWrap.classList.add('hidden');
        uploadBtn.disabled = true;
        return;
      }
      list.forEach(f => {
        const url = URL.createObjectURL(f);
        const card = document.createElement('div');
        card.className = 'relative rounded-lg overflow-hidden shadow';
        card.innerHTML = `
          <div class="relative pt-[100%] bg-gray-100">
            <img src="${url}" class="absolute inset-0 w-full h-full object-cover" alt="">
          </div>
          <div class="px-2 py-1 text-[11px] text-gray-600 truncate">${f.name}</div>
        `;
        previewGrid.appendChild(card);
      });
      previewWrap.classList.remove('hidden');
      uploadBtn.disabled = false;
    }

    ['dragenter','dragover'].forEach(evt => {
      dropzone.addEventListener(evt, e => {
        e.preventDefault(); e.stopPropagation();
        dropzone.classList.add('border-green-400','bg-green-50');
      });
    });
    ['dragleave','drop'].forEach(evt => {
      dropzone.addEventListener(evt, e => {
        e.preventDefault(); e.stopPropagation();
        dropzone.classList.remove('border-green-400','bg-green-50');
      });
    });
    dropzone.addEventListener('drop', e => {
      fileInput.files = e.dataTransfer.files;
      showPreview(fileInput.files);
    });
    dropzone.addEventListener('click', () => fileInput.click());
    browseTrigger.addEventListener('click', (e) => { e.stopPropagation(); fileInput.click(); });
    fileInput.addEventListener('change', () => showPreview(fileInput.files));
    document.getElementById('uploadForm').addEventListener('reset', () => {
      previewGrid.innerHTML = '';
      previewWrap.classList.add('hidden');
      uploadBtn.disabled = true;
      fileInput.value = '';
    });
  </script>
</body>
</html>
