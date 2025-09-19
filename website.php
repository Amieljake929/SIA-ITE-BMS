<!DOCTYPE html>
<html lang="tl">
  <head>
    <meta charset="utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <title>Simple UI – Storyset Animations</title>

    <!-- Tailwind CSS via CDN -->
    <script src="https://cdn.tailwindcss.com"></script>

    <!-- Lottie Web Component (for Storyset animated JSONs) -->
    <script src="https://unpkg.com/@lottiefiles/lottie-player@latest/dist/lottie-player.js"></script>

    <style>
      /* Optional: smooth fade-in */
      .fade-in { animation: fadeIn 0.8s ease both; }
      @keyframes fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform:none; } }
    </style>
  </head>
  <body class="min-h-screen bg-gradient-to-b from-slate-50 to-white text-slate-800">
    <!-- Header -->
    <header class="sticky top-0 z-40 bg-white/70 backdrop-blur border-b border-slate-200">
      <div class="max-w-6xl mx-auto px-4 py-3 flex items-center justify-between">
        <h1 class="text-xl font-bold tracking-tight">Storyset Demo UI</h1>
        <nav class="flex items-center gap-3 text-sm">
          <a href="#hero" class="hover:underline">Home</a>
          <a href="#gallery" class="hover:underline">Gallery</a>
          <a href="#about" class="hover:underline">About</a>
        </nav>
      </div>
    </header>

    <!-- Hero Section with Animation -->
    <section id="hero" class="max-w-6xl mx-auto px-4 py-12 grid md:grid-cols-2 gap-8 items-center">
      <div class="fade-in">
        <h2 class="text-3xl md:text-4xl font-extrabold leading-tight">Muslim Graduation – Rafiki (exact image embedded) style</h2>
        <p class="mt-3 text-slate-600">
          Simple front‑end UI na gumagamit ng <span class="font-medium">Storyset</span> illustration/animation.
          I‑paste mo lang ang JSON animation URL mula sa Storyset (Animate) o gamitin ang SVG/PNG download para sa images.
        </p>
        <div class="mt-6 flex flex-wrap gap-2">
          <button id="btnPlay" class="px-4 py-2 rounded-2xl bg-slate-900 text-white hover:bg-slate-700">Play</button>
          <button id="btnPause" class="px-4 py-2 rounded-2xl bg-slate-200 hover:bg-slate-300">Pause</button>
          <button id="btnSpeed" class="px-4 py-2 rounded-2xl bg-slate-200 hover:bg-slate-300" data-speed="1">1x</button>
        </div>
        <p class="mt-4 text-xs text-slate-500">Free to use with attribution. See footer for proper credit.</p>
      </div>

      <!-- ANIMATION CARD -->
      <div class="fade-in">
        <div class="rounded-3xl bg-white shadow-lg p-4 md:p-6 border border-slate-100">
          <!--
            IMPORTANT NOTE:
            1) Open the provided link: https://storyset.com/illustration/muslim-graduation/rafiki
            2) Click "Animate" (kung available), customize, then click "Export" -> "Lottie JSON" or copy the hosted JSON URL.
            3) Replace the src below (placeholder) with your Storyset Lottie JSON link.
          -->
          <img
            src="https://stories.freepiklabs.com/storage/73171/Muslim-graduation_Mesa-de-trabajo-1.svg"
            alt="Muslim graduation – Rafiki style (Storyset)"
            class="w-full h-[320px] md:h-[360px] object-contain"
          />
          <div class="mt-3 flex items-center justify-between text-xs text-slate-500">
            <span>Rafiki SVG from Storyset</span>
            <a href="https://storyset.com/" target="_blank" rel="noopener" class="hover:underline">Illustrations by Storyset</a>
          </div>
        </div>
      </div>
    </section>

    <!-- Image Gallery -->
    <section id="gallery" class="max-w-6xl mx-auto px-4 py-10">
      <h3 class="text-2xl font-bold">Image Gallery</h3>
      <p class="text-slate-600 mt-1 text-sm">I‑lagay dito ang downloaded SVG/PNG mula sa Storyset page (Rafiki style) para consistent ang look.</p>

      <div class="mt-6 grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        <!-- CARD 1 -->
        <figure class="group rounded-2xl overflow-hidden bg-white border border-slate-100 shadow hover:shadow-md transition">
          <img
            src="https://stories.freepiklabs.com/storage/73171/Muslim-graduation_Mesa-de-trabajo-1.svg"
            alt="Storyset illustration placeholder"
            class="w-full h-40 object-contain p-4 group-hover:scale-[1.02] transition"
          />
          <figcaption class="px-3 pb-3 text-xs text-slate-500">Exact image from your link (Rafiki) – served directly from Storyset</figcaption>
        </figure>
        <!-- CARD 2 -->
        <figure class="group rounded-2xl overflow-hidden bg-white border border-slate-100 shadow hover:shadow-md transition">
          <img
            src="https://stories.freepiklabs.com/storage/83817/muslim-graduation-rafiki-12828.png"
            alt="Storyset illustration placeholder"
            class="w-full h-40 object-contain p-4 group-hover:scale-[1.02] transition"
          />
          <figcaption class="px-3 pb-3 text-xs text-slate-500">Rafiki thumbnail PNG from Storyset</figcaption>
        </figure>
        <!-- CARD 3 -->
        <figure class="group rounded-2xl overflow-hidden bg-white border border-slate-100 shadow hover:shadow-md transition">
          <img
            src="https://via.placeholder.com/600x400.svg?text=Graduation+Variant"
            alt="Storyset illustration placeholder"
            class="w-full h-40 object-contain p-4 group-hover:scale-[1.02] transition"
          />
          <figcaption class="px-3 pb-3 text-xs text-slate-500">You can mix static images + animations</figcaption>
        </figure>
        <!-- CARD 4 -->
        <figure class="group rounded-2xl overflow-hidden bg-white border border-slate-100 shadow hover:shadow-md transition">
          <img
            src="https://via.placeholder.com/600x400.svg?text=Custom+Colorway"
            alt="Storyset illustration placeholder"
            class="w-full h-40 object-contain p-4 group-hover:scale-[1.02] transition"
          />
          <figcaption class="px-3 pb-3 text-xs text-slate-500">Export SVG/PNG from Storyset editor</figcaption>
        </figure>
      </div>
    </section>

    <!-- About / Notes -->
    <section id="about" class="max-w-6xl mx-auto px-4 pb-16">
      <div class="rounded-3xl bg-white border border-slate-100 p-6 shadow">
        <h4 class="text-lg font-semibold">Notes / How to use</h4>
        <ol class="mt-2 list-decimal pl-5 text-sm text-slate-600 space-y-1">
          <li>Buksan ang link: <a class="text-slate-900 font-medium hover:underline" href="https://storyset.com/illustration/muslim-graduation/rafiki" target="_blank" rel="noopener">Muslim Graduation – Rafiki</a>.</li>
          <li>Para sa <span class="font-medium">animation</span>: i-click ang <em>Animate</em> (kung available), i‑customize, tapos <em>Export</em> → <em>Lottie JSON</em>. Palitan ang <code>src</code> ng <code>#gradAnim</code> ng JSON URL mo o local file path (hal. <code>/assets/muslim-graduation.json</code>).</li>
          <li>Para sa <span class="font-medium">images</span>: i‑download ang SVG/PNG mula Storyset editor, ilagay sa <code>/assets</code>, at palitan ang mga <code>&lt;img src=...&gt;</code> sa Gallery.</li>
          <li>Required ang attribution kapag free use. Panatilihin ang credit sa footer (o ayon sa Storyset policy).</li>
        </ol>
      </div>
    </section>

    <!-- Footer / Attribution -->
    <footer class="border-t border-slate-200">
      <div class="max-w-6xl mx-auto px-4 py-6 text-xs text-slate-500 flex flex-col sm:flex-row items-center justify-between gap-2">
        <span>© 2025 Your Site</span>
        <span>
          Illustrations by <a href="https://storyset.com/" class="font-medium text-slate-700 hover:underline" target="_blank" rel="noopener">Storyset</a>
          — please keep this attribution.
        </span>
      </div>
    </footer>

    <!-- Controls Script -->
    <script>
      const player = document.getElementById('gradAnim');
      const btnPlay = document.getElementById('btnPlay');
      const btnPause = document.getElementById('btnPause');
      const btnSpeed = document.getElementById('btnSpeed');

      btnPlay.addEventListener('click', () => player?.play?.());
      btnPause.addEventListener('click', () => player?.pause?.());
      btnSpeed.addEventListener('click', () => {
        const speeds = [0.5, 1, 1.5, 2];
        const curr = Number(btnSpeed.dataset.speed || '1');
        const idx = (speeds.indexOf(curr) + 1) % speeds.length;
        const next = speeds[idx];
        btnSpeed.dataset.speed = String(next);
        btnSpeed.textContent = `${next}x`;
        player?.setSpeed?.(next);
      });
    </script>
  </body>
</html>
