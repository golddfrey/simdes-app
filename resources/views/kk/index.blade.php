{{-- resources/views/kk/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
  <div class="flex flex-col md:flex-row md:items-center md:justify-between gap-4 mb-6">
    <h2 class="text-2xl font-bold">Daftar Kepala Keluarga</h2>

    <div class="flex items-center gap-3 w-full md:w-auto">
      <!-- Live search input -->
      <input id="kk-search" type="search"
             placeholder="Cari NIK atau Nama..."
             value="{{ $q ?? '' }}"
             class="w-full md:w-80 border rounded-l px-3 py-2 focus:outline-none focus:ring"
             autocomplete="off" />

      <!-- fallback button (non-js) -->
      <button id="kk-search-btn" type="button" class="bg-blue-600 text-white px-4 rounded-r ml-1 hidden md:inline-block">Cari</button>

      <a href="{{ route('kk.create') }}" class="ml-3 inline-flex items-center gap-2 bg-green-600 hover:bg-green-700 text-white px-4 py-2 rounded">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
          <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
        </svg>
        Tambah Data
      </a>
    </div>
  </div>

  {{-- container for ajax-updated list (single source) --}}
  <div id="kk-list">
    @include('kk._list', ['kks' => $kks])
  </div>
</div>

{{-- ensure Alpine available --}}
<script>
  (function(){
    if (!window.Alpine) {
      var s = document.createElement('script');
      s.src = "https://unpkg.com/alpinejs@3.x.x/dist/cdn.min.js";
      s.defer = true;
      document.head.appendChild(s);
    }
  })();
</script>

{{-- Robust live-search + pagination handler (full) --}}
@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  const input = document.getElementById('kk-search');
  const listContainer = document.getElementById('kk-list');
  let debounceTimer = null;

  function debounce(fn, wait) {
    return function(...args) {
      clearTimeout(debounceTimer);
      debounceTimer = setTimeout(() => fn.apply(this, args), wait);
    };
  }

  // fetchList: flexible — handles JSON { html } OR full HTML page
  async function fetchList(url, pushHistory = false) {
    try {
      const fullUrl = new URL(url, location.origin).toString();
      const res = await fetch(fullUrl, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' },
        credentials: 'same-origin'
      });

      if (!res.ok) throw new Error('Network response was not ok');

      const contentType = res.headers.get('content-type') || '';

      // 1) If JSON, parse and inject html field
      if (contentType.includes('application/json')) {
        const data = await res.json();
        listContainer.innerHTML = data.html;
      } else {
        // 2) If HTML returned (e.g. whole page), parse and extract #kk-list fragment
        const text = await res.text();
        const parser = new DOMParser();
        const doc = parser.parseFromString(text, 'text/html');
        const fragment = doc.getElementById('kk-list');
        if (fragment) {
          listContainer.innerHTML = fragment.innerHTML;
        } else {
          // fallback: replace whole container with returned text (last resort)
          listContainer.innerHTML = text;
        }
      }

      // Reattach handlers for the fresh DOM
      attachPaginationHandlers();

      if (pushHistory) {
        history.pushState(null, '', fullUrl);
      }
    } catch (err) {
      console.error('Fetch error:', err);
    }
  }

  const onType = debounce(function(e){
    const qraw = e.target.value || '';
    const q = encodeURIComponent(qraw);
    const url = `${location.pathname}?q=${q}`;
    history.replaceState(null, '', url);
    fetchList(url);
  }, 300);

  input.addEventListener('input', onType);

  // fallback non-js button behaviour
  const searchBtn = document.getElementById('kk-search-btn');
  if (searchBtn) {
    searchBtn.addEventListener('click', function(){
      const q = encodeURIComponent(input.value || '');
      window.location.href = `${location.pathname}?q=${q}`;
    });
  }

  // Only intercept pagination links that point to the SAME index path (avoid detail/edit)
  function attachPaginationHandlers() {
    const links = listContainer.querySelectorAll('a[href]');
    links.forEach(link => {
      const href = link.getAttribute('href');
      if (!href) return;

      // Normalize and parse
      let urlObj;
      try {
        urlObj = new URL(href, location.origin);
      } catch(e) {
        return;
      }

      // Only intercept links that target the same pathname as current index (e.g. /kk)
      if (urlObj.pathname !== location.pathname) return;

      // This is likely a pagination or filtered-page link -> intercept
      // Replace existing node to avoid duplicate listeners
      const newLink = link.cloneNode(true);
      link.parentNode.replaceChild(newLink, link);

      newLink.addEventListener('click', function(ev) {
        // allow open-in-new-tab
        if (ev.ctrlKey || ev.metaKey || ev.button !== 0) return;

        ev.preventDefault();

        // sync input with q param if present
        const params = urlObj.searchParams;
        const q = params.get('q') || '';
        input.value = q;

        fetchList(urlObj.toString(), true);
      });
    });
  }

  // browser navigation back/forward
  window.addEventListener('popstate', function(){
    const params = new URL(location.href).searchParams;
    const q = params.get('q') || '';
    input.value = q;
    fetchList(location.href, false);
  });

  // initial attach
  attachPaginationHandlers();
});
</script>
@endpush
@endsection
