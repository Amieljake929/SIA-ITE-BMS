<?php
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    header("Location: login.php");
    exit();
}
include '../../RIS_login/db_connect.php'; // not used here, ok lang
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>RIS - Admin Portal</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css" />
</head>
<body class="bg-gray-50 text-gray-800 font-sans flex flex-col min-h-screen">

  <!-- Top Bar -->
  <div class="bg-gradient-to-r from-green-800 to-green-900 text-white text-sm px-6 py-3 flex justify-between items-center shadow-md">
    <div class="flex-1"><span id="datetime" class="font-medium tracking-wide">LOADING...</span></div>
    <div class="flex-shrink-0"><img src="../../images/Bagbag.png" alt="Bagbag Logo" class="h-12 object-contain drop-shadow" /></div>
  </div>

  <!-- Main Header -->
  <header class="bg-white shadow-lg border-b border-green-100 px-6 py-4 relative">
    <div class="container mx-auto flex flex-col sm:flex-row items-center justify-between space-y-4 sm:space-y-0">
      <div class="flex items-center space-x-4">
        <button class="flex items-center justify-center w-10 h-10 rounded-full bg-yellow-500 text-gray-800 hover:bg-yellow-100 transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-yellow-300"
                onclick="window.location.href='../ris_admin_dashboard.php'" title="Home">
          <i class="fas fa-home text-white" style="font-size:1.2rem;"></i>
        </button>
        <h1 class="text-xl font-bold text-green-800">Programs | Admin (RIS → BMS)</h1>
      </div>

      <div class="relative inline-block text-right">
        <button id="userMenuButton" class="flex items-center font-medium cursor-pointer text-sm focus:outline-none whitespace-nowrap">
          <span class="text-gray-800">Logged in:</span>
          <span class="text-blue-700 ml-1"><?php echo htmlspecialchars($_SESSION['full_name']); ?></span>
          <i class="fas fa-chevron-down ml-2 text-gray-400"></i>
        </button>
        <div id="userDropdown" class="absolute right-0 mt-2 w-48 bg-white border border-gray-200 rounded-lg shadow-xl hidden z-10">
          <ul class="py-2 text-sm">
            <li><a href="#" class="block px-5 py-2 text-gray-700 hover:bg-green-50 hover:text-green-800 transition-colors duration-150 flex items-center"><i class="fas fa-user text-green-600 mr-3"></i> Profile</a></li>
            <li><a href="../../RIS_login/ris_logout.php" class="block px-5 py-2 text-gray-700 hover:bg-red-50 hover:text-red-800 transition-colors duration-150 flex items-center"><i class="fas fa-sign-out-alt text-red-600 mr-3"></i> Logout</a></li>
          </ul>
        </div>
      </div>
    </div>
  </header>

  <!-- MAIN CONTENT -->
  <main class="container mx-auto px-6 py-8 w-full max-w-7xl">

    <!-- Upload to BMS via API -->
    <section class="bg-white rounded-2xl shadow-lg p-6 mb-8">
      <h2 class="text-lg font-bold text-gray-800 mb-4 flex items-center">
        <i class="fa-solid fa-images mr-2 text-green-700"></i> Upload to BMS Programs
      </h2>

      <div id="status" class="hidden mb-4 px-4 py-3 rounded-lg text-sm"></div>

      <form id="uploadForm" class="space-y-4">
        <input type="hidden" name="uploaded_by" value="<?php echo (int)$_SESSION['user_id']; ?>">
        <div id="dropzone" class="border-2 border-dashed border-gray-300 rounded-xl p-6 text-center transition hover:border-green-400 hover:bg-green-50 cursor-pointer">
          <input id="fileInput" name="images[]" type="file" accept="image/*" multiple class="hidden" />
          <div class="flex flex-col items-center justify-center space-y-2">
            <i class="fa-solid fa-cloud-arrow-up text-3xl"></i>
            <p class="text-sm text-gray-600">Drag & drop images here or <span class="text-green-700 font-semibold underline" id="browseTrigger">browse</span></p>
            <p class="text-xs text-gray-400">JPEG/PNG/GIF/WEBP, up to 5 MB each</p>
          </div>
        </div>

        <div id="previewWrap" class="hidden">
          <h3 class="text-sm font-semibold text-gray-700 mt-4 mb-2">Selected files</h3>
          <div id="previewGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-6 gap-3"></div>
        </div>

        <div class="flex items-center justify-end gap-3 pt-2">
          <button type="reset" class="px-4 py-2 rounded-lg bg-gray-100 hover:bg-gray-200 text-gray-800 text-sm">Clear</button>
          <button id="uploadBtn" type="submit" class="px-5 py-2 rounded-lg bg-green-700 hover:bg-green-800 text-white text-sm disabled:opacity-50 disabled:cursor-not-allowed" disabled>
            <i class="fa-solid fa-upload mr-2"></i> Upload to BMS
          </button>
        </div>
      </form>
    </section>

    <!-- Gallery (from BMS) -->
    <section class="bg-white rounded-2xl shadow-lg p-6">
      <div class="flex items-center justify-between mb-4">
        <h2 class="text-lg font-bold text-gray-800 flex items-center">
          <i class="fa-solid fa-photo-film mr-2 text-green-700"></i> Gallery
        </h2>
        <p class="text-xs text-gray-500"><span id="galleryCount">0</span> image(s)</p>
      </div>

      <div id="noData" class="hidden text-gray-500 text-sm">No images available yet.</div>
      <div id="galleryGrid" class="grid grid-cols-2 sm:grid-cols-3 md:grid-cols-4 lg:grid-cols-5 xl:grid-cols-6 gap-4"></div>
    </section>
  </main>

  <footer class="bg-green-900 text-white text-center py-5 text-sm mt-auto">
    &copy; <?= date('Y') ?> Bagbag eServices. All rights reserved. <br class="sm:hidden"> | Empowering Communities Digitally.
  </footer>

  <script>
    // ================== CONFIG ==================
    const BASE_BMS   = '/ITE-SIA/BMS';
    const API_UPLOAD = BASE_BMS + '/staff/Programs/api_programs_upload.php';
    const API_LIST   = BASE_BMS + '/staff/Programs/api_programs_list.php';
    const API_DELETE = BASE_BMS + '/staff/Programs/api_programs_delete.php'; // NEW
    const API_TOKEN  = 'RIS_TO_BMS_PROGRAMS_UPLOAD_2025'; // must match API files

    // ================== Clock & Dropdown ==================
    function updateTime(){ const now=new Date(); const o={weekday:'long',year:'numeric',month:'long',day:'numeric',hour:'2-digit',minute:'2-digit',second:'2-digit'}; document.getElementById('datetime').textContent=now.toLocaleString('en-US',o).toUpperCase(); }
    setInterval(updateTime,1000); updateTime();
    const userMenuButton=document.getElementById('userMenuButton'), userDropdown=document.getElementById('userDropdown');
    if(userMenuButton){ userMenuButton.addEventListener('click',(e)=>{e.stopPropagation();userDropdown.classList.toggle('hidden');}); document.addEventListener('click',(e)=>{ if(!userMenuButton.contains(e.target)&&!userDropdown.contains(e.target)) userDropdown.classList.add('hidden');}); }

    // ================== Helpers ==================
    const statusBox=document.getElementById('status');
    function showStatus(msg, type){ // type: success|info|error
      const cls = type==='success' ? 'bg-green-50 text-green-800 border border-green-200'
                 : type==='info'  ? 'bg-blue-50 text-blue-800 border border-blue-200'
                 : 'bg-red-50 text-red-800 border border-red-200';
      statusBox.className = 'mb-4 px-4 py-3 rounded-lg text-sm ' + cls;
      statusBox.textContent = msg;
      statusBox.classList.remove('hidden');
    }

    // ================== Upload UI ==================
    const dropzone=document.getElementById('dropzone'), fileInput=document.getElementById('fileInput'), browseTrigger=document.getElementById('browseTrigger');
    const previewWrap=document.getElementById('previewWrap'), previewGrid=document.getElementById('previewGrid'), uploadBtn=document.getElementById('uploadBtn');

    function showPreview(files){
      previewGrid.innerHTML=''; const list=[...files].filter(f=>f.type.startsWith('image/'));
      if(!list.length){ previewWrap.classList.add('hidden'); uploadBtn.disabled=true; return; }
      list.forEach(f=>{ const url=URL.createObjectURL(f); const card=document.createElement('div'); card.className='relative rounded-lg overflow-hidden shadow'; card.innerHTML=`
          <div class="relative pt-[100%] bg-gray-100">
            <img src="${url}" class="absolute inset-0 w-full h-full object-cover" alt="">
          </div>
          <div class="px-2 py-1 text-[11px] text-gray-600 truncate">${f.name}</div>`; previewGrid.appendChild(card); });
      previewWrap.classList.remove('hidden'); uploadBtn.disabled=false;
    }
    ['dragenter','dragover'].forEach(evt=>dropzone.addEventListener(evt,e=>{e.preventDefault();e.stopPropagation();dropzone.classList.add('border-green-400','bg-green-50');}));
    ['dragleave','drop'].forEach(evt=>dropzone.addEventListener(evt,e=>{e.preventDefault();e.stopPropagation();dropzone.classList.remove('border-green-400','bg-green-50');}));
    dropzone.addEventListener('drop',e=>{ fileInput.files=e.dataTransfer.files; showPreview(fileInput.files); });
    dropzone.addEventListener('click',()=>fileInput.click());
    browseTrigger.addEventListener('click',e=>{e.stopPropagation();fileInput.click();});
    fileInput.addEventListener('change',()=>showPreview(fileInput.files));
    document.getElementById('uploadForm').addEventListener('reset',()=>{ previewGrid.innerHTML=''; previewWrap.classList.add('hidden'); uploadBtn.disabled=true; fileInput.value=''; statusBox.className='hidden mb-4 px-4 py-3 rounded-lg text-sm'; statusBox.textContent=''; });

    // Upload submit → BMS API
    document.getElementById('uploadForm').addEventListener('submit', async (e)=>{
      e.preventDefault();
      if(!fileInput.files || !fileInput.files.length) return;
      const fd=new FormData();
      for(const f of fileInput.files) fd.append('images[]', f);
      fd.append('uploaded_by', '<?php echo (int)$_SESSION['user_id']; ?>');

      uploadBtn.disabled=true;
      showStatus('Uploading...', 'info');

      try{
        const res=await fetch(API_UPLOAD,{ method:'POST', headers:{ 'Authorization':'Bearer '+API_TOKEN }, body:fd });
        const data=await res.json();
        if(data.ok){
          showStatus(`Uploaded ${data.count} image(s).`, 'success');
          fileInput.value=''; previewGrid.innerHTML=''; previewWrap.classList.add('hidden'); uploadBtn.disabled=true;
          loadGallery();
        }else{
          showStatus('Upload failed: '+(data.error || (data.errors?data.errors.join(" "):'Unknown error')), 'error');
        }
      }catch(err){
        showStatus('Network or server error.', 'error');
      }finally{
        uploadBtn.disabled=false;
      }
    });

    // ================== Gallery (from BMS) ==================
    const galleryGrid = document.getElementById('galleryGrid');
    const galleryCount= document.getElementById('galleryCount');
    const noData      = document.getElementById('noData');

    function formatStamp(s){
      const d = new Date((s||'').replace(' ', 'T'));
      if(isNaN(d)) return s||'';
      return d.toLocaleString('en-US',{month:'short',day:'2-digit',year:'numeric',hour:'2-digit',minute:'2-digit'});
    }
    function buildSrc(item){
      if (item.absolute_src) return item.absolute_src;
      let fp = (item.file_path||'').replace(/^\/+/, '');
      if (!fp.startsWith('Programs/')) fp = 'Programs/' + fp;
      return BASE_BMS + '/staff/' + fp;
    }

    async function loadGallery(){
      galleryGrid.innerHTML=''; noData.classList.add('hidden');
      try{
        const res=await fetch(`${API_LIST}?api_token=${encodeURIComponent(API_TOKEN)}`, {
          headers:{ 'Authorization':'Bearer '+API_TOKEN }
        });
        if(!res.ok){
          noData.classList.remove('hidden'); galleryCount.textContent='0';
          console.warn('List API HTTP error:', res.status, await res.text());
          return;
        }
        const data=await res.json();
        if(!data.ok){ noData.classList.remove('hidden'); galleryCount.textContent='0'; return; }

        galleryCount.textContent = data.count || 0;
        if(!data.items || !data.items.length){ noData.classList.remove('hidden'); return; }

        for(const item of data.items){
          const src = buildSrc(item);
          const card = document.createElement('div');
          card.className='group relative rounded-xl overflow-hidden shadow hover:shadow-lg transition';
          card.innerHTML = `
            <div class="relative pt-[100%] bg-gray-100">
              <img src="${src}" alt="program image" class="absolute inset-0 w-full h-full object-cover" loading="lazy" />
            </div>

            <!-- controls -->
            <div class="absolute top-2 right-2 opacity-0 group-hover:opacity-100 transition">
              <button type="button"
                      class="p-2 rounded-full bg-red-600 hover:bg-red-700 text-white shadow"
                      onclick="deleteImage(${item.id})"
                      title="Delete">
                <i class="fa-solid fa-trash"></i>
              </button>
            </div>

            <div class="px-3 py-2">
              <div class="text-xs text-gray-700 truncate" title="${item.file_name || ''}">${item.file_name || ''}</div>
              <div class="text-[11px] text-gray-400">${formatStamp(item.created_at || '')}</div>
            </div>`;
          galleryGrid.appendChild(card);
        }
      }catch(err){
        console.error('List API fetch error:', err);
        noData.classList.remove('hidden'); galleryCount.textContent='0';
      }
    }

    // ===== Delete handler =====
    async function deleteImage(id){
      if (!confirm('Delete this image?')) return;
      const fd = new FormData(); fd.append('id', id);
      try{
        const res = await fetch(`${API_DELETE}?api_token=${encodeURIComponent(API_TOKEN)}`, {
          method: 'POST',
          headers: { 'Authorization': 'Bearer ' + API_TOKEN },
          body: fd
        });
        const data = await res.json();
        if (data.ok){
          showStatus('Image deleted.', 'success');
          loadGallery();
        } else {
          showStatus('Delete failed: ' + (data.error || 'Unknown error'), 'error');
        }
      } catch (e){
        showStatus('Network or server error while deleting.', 'error');
      }
    }

    // Initial load
    loadGallery();
  </script>
</body>
</html>
