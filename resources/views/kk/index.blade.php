{{-- resources/views/kk/index.blade.php --}}
@extends('layouts.app')

@section('title', 'Daftar Kepala Keluarga')

@section('content')
<div class="max-w-6xl mx-auto px-4 py-8">
  <h1 class="text-2xl font-bold mb-6">Daftar Kepala Keluarga</h1>

  <div class="space-y-4">
    @foreach($kepala_keluarga as $kk)
      <div class="bg-white rounded shadow-sm border p-4 relative">
        <div class="grid grid-cols-12 gap-4 items-start">
          <div class="col-span-3">
            <div class="text-xs text-gray-500">NIK</div>
            <div class="font-semibold text-sm break-words">{{ $kk->nik }}</div>
            <a href="javascript:void(0)" 
               class="toggle-anggota text-xs text-blue-600 hover:underline mt-2 inline-block"
               data-url="{{ route('kk.anggota', $kk->id) }}"
               data-count="{{ $kk->anggotas->count() ?? 0 }}">
               Tampilkan Anggota ({{ $kk->anggotas->count() ?? 0 }})
            </a>
          </div>

          <div class="col-span-6">
            <div class="text-xs text-gray-500">Nama</div>
            <div class="font-semibold text-sm">{{ strtoupper($kk->nama) }}</div>
            <div class="text-xs text-gray-400 mt-2">RT / RW</div>
            <div class="text-sm">{{ $kk->rt }} / {{ $kk->rw }}</div>
          </div>

          <div class="col-span-3 text-right">
            <a href="{{ route('kk.show', $kk->id) }}" class="inline-block px-3 py-1 border rounded text-sm mr-2">Detail</a>
            <a href="{{ route('kk.edit', $kk->id) }}" class="inline-block px-3 py-1 border rounded text-sm">Edit</a>
          </div>
        </div>

        {{-- Container untuk menampilkan anggota (hidden default) --}}
        <div class="anggota-wrapper mt-4 overflow-hidden transition-all duration-300" style="max-height: 0;">
          <div class="anggota-content space-y-3"></div>
        </div>
      </div>
    @endforeach
  </div>

  {{-- pagination --}}
  <div class="mt-6">
    {{ $kepala_keluarga->links() }}
  </div>
</div>

@push('styles')
<style>
.anggota-card {
  border: 2px solid rgba(0,0,0,0.6);
  border-radius: 6px;
  padding: 12px;
  animation: fadeInUp 300ms ease both;
  background: white;
}
@keyframes fadeInUp {
  from { transform: translateY(8px); opacity: 0; }
  to   { transform: translateY(0);   opacity: 1; }
}
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
  document.querySelectorAll('.toggle-anggota').forEach(btn => {
    btn.addEventListener('click', async function (e) {
      const url = this.dataset.url;
      const wrapper = this.closest('.bg-white').querySelector('.anggota-wrapper');
      const content = wrapper.querySelector('.anggota-content');

      if (wrapper.classList.contains('open')) {
        wrapper.style.maxHeight = '0px';
        wrapper.classList.remove('open');
        return;
      }

      if (content.dataset.filled === '1') {
        wrapper.classList.add('open');
        wrapper.style.maxHeight = wrapper.scrollHeight + 'px';
        return;
      }

      const oldText = this.innerHTML;
      this.innerHTML = 'Memuat...';

      try {
        const res = await fetch(url, {
          headers: { 'Accept': 'application/json' }
        });

        if (!res.ok) throw new Error('Network response was not ok');

        const json = await res.json();
        if (!json.success) throw new Error('Response tidak berisi data');

        const anggota = json.data || [];

        content.innerHTML = '';

        if (anggota.length === 0) {
          content.innerHTML = '<div class="text-sm text-gray-500">Belum ada data anggota.</div>';
        } else {
          anggota.forEach(a => {
            const nama = (a.nama || '—').toUpperCase();
            const nik = a.nik || '-';
            const status = a.status_keluarga || (a.status || '-');
            const tempat = a.tempat_lahir || '-';
            const tanggal = a.tanggal_lahir || '-';

            const html = `
              <div class="anggota-card">
                <div class="flex justify-between items-start">
                  <div>
                    <div class="font-bold text-md">${escapeHtml(nama)}</div>
                    <div class="text-xs text-gray-600">NIK: ${escapeHtml(nik)}</div>
                  </div>
                  <div class="text-right text-xs text-gray-500">
                    <div>${escapeHtml(status)}</div>
                  </div>
                </div>
                <div class="mt-2 text-sm text-gray-600">
                  <div>Tempat Lahir: ${escapeHtml(tempat)}</div>
                  <div>Tanggal Lahir: ${escapeHtml(tanggal)}</div>
                </div>
              </div>
            `;
            content.insertAdjacentHTML('beforeend', html);
          });
        }

        content.dataset.filled = '1';

        wrapper.classList.add('open');
        requestAnimationFrame(() => {
          wrapper.style.maxHeight = wrapper.scrollHeight + 'px';
        });

      } catch (err) {
        console.error(err);
        alert('Gagal memuat anggota: ' + err.message);
      } finally {
        this.innerHTML = oldText;
      }
    });
  });

  function escapeHtml(unsafe) {
    if (unsafe === null || unsafe === undefined) return '';
    return String(unsafe)
      .replace(/&/g, '&amp;')
      .replace(/</g, '&lt;')
      .replace(/>/g, '&gt;')
      .replace(/"/g, '&quot;')
      .replace(/'/g, '&#039;');
  }
});
</script>
@endpush

@endsection
