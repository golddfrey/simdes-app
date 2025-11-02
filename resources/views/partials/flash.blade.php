{{-- resources/views/partials/flash.blade.php --}}
@php
  // ambil pesan session yang ingin ditampilkan (contoh: 'success', 'error', 'info')
  $flashSuccess = session('success') ?? null;
  $flashError = session('error') ?? null;
  $flashInfo = session('info') ?? null;

  // pilih prioritas: success > error > info
  $flashMessage = $flashSuccess ?? $flashError ?? $flashInfo ?? null;
  $flashType = $flashSuccess ? 'success' : ($flashError ? 'error' : ($flashInfo ? 'info' : null));
@endphp

@if($flashMessage)
  <div class="flash-container">
    {{-- Gunakan Alpine untuk show/hide, tapi isi JS dikemas dalam IIFE tanpa komentar --}}
    <div
      x-data="{ show: false, msg: '', type: '' }"
      x-init="(() => {
          // inisialisasi dari server-injected data (safe JSON)
          msg = {{ Illuminate\Support\Js::from($flashMessage) }};
          type = {{ Illuminate\Support\Js::from($flashType) }};
          show = true;
          // hide otomatis setelah 4 detik
          setTimeout(() => { show = false }, 4000);
      })()"
      x-show="show"
      x-transition:enter="transition transform ease-out duration-300"
      x-transition:enter-start="opacity-0 translate-y-2"
      x-transition:enter-end="opacity-100 translate-y-0"
      x-transition:leave="transition transform ease-in duration-200"
      x-transition:leave-start="opacity-100 translate-y-0"
      x-transition:leave-end="opacity-0 translate-y-2"
      class="flash-box"
      role="status"
      aria-live="polite"
    >
      <div class="flex items-start gap-3">
        <div class="flex-shrink-0">
          @if($flashType === 'success')
            <svg class="w-5 h-5 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
          @elseif($flashType === 'error')
            <svg class="w-5 h-5 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          @else
            <svg class="w-5 h-5 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1M12 8h.01" /></svg>
          @endif
        </div>

        <div>
          <div class="font-medium">
            @if($flashType === 'success') Berhasil
            @elseif($flashType === 'error') Gagal
            @else Info
            @endif
          </div>
          <div class="text-sm mt-1">
            {{-- tunjukkan pesan --}}
            {{ $flashMessage }}
          </div>
        </div>

        <div class="ml-4">
          {{-- tombol close manual --}}
          <button type="button" @click="show = false" class="text-gray-700 hover:text-gray-900">
            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
          </button>
        </div>
      </div>
    </div>
  </div>
@endif
