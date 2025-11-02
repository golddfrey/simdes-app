{{-- resources/views/penduduk/kk_show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-6">
  <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">

    @if(session('success'))
      <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <div class="flex items-start justify-between mb-4">
      <div>
        <h3 class="text-xl font-bold">Detail Kepala Keluarga</h3>
        <div class="text-sm text-gray-500">NIK: <span class="font-medium">{{ $kk->nik }}</span></div>
      </div>

      <div class="text-right">
        <div class="text-xs text-gray-500">Tercatat</div>
        <div class="text-sm text-gray-600">{{ optional($kk->created_at)->format('Y-m-d') }}</div>
      </div>
    </div>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <div class="text-xs text-gray-500">Nama</div>
        <div class="font-semibold text-lg">{{ $kk->nama }}</div>

        <div class="mt-3 text-sm">
          <div><strong>Jenis Kelamin:</strong>
            @if(($kk->jenis_kelamin ?? null) === 'L') Laki-laki
            @elseif(($kk->jenis_kelamin ?? null) === 'P') Perempuan
            @else - @endif
          </div>

          <div class="mt-1"><strong>Phone:</strong> {{ $kk->phone ?? '-' }}</div>
          <div class="mt-1"><strong>Alamat:</strong> {{ $kk->alamat ?? '-' }}</div>
          <div class="mt-1"><strong>RT / RW:</strong> {{ $kk->rt ?? '-' }} / {{ $kk->rw ?? '-' }}</div>
        </div>
      </div>

      <div>
        <div class="text-xs text-gray-500">Desa</div>
        <div class="font-medium">{{ optional($kk->desa)->nama ?? '-' }}</div>

        <div class="mt-6 flex gap-2">
          <a href="{{ route('penduduk.kk.export', $kk->id) }}" class="px-4 py-2 bg-indigo-600 rounded text-white">Export PDF</a>
          <a href="{{ route('penduduk.index') }}" class="px-4 py-2 border rounded">Kembali</a>
          <a href="{{ route('kk.edit', $kk->id) }}" class="px-4 py-2 bg-yellow-400 text-white rounded">Edit KK</a>
        </div>
      </div>
    </div>

    <hr class="my-6">

    <h4 class="font-semibold mb-3">Anggota Keluarga ({{ $kk->anggotas->count() }})</h4>

    <div id="anggota" class="space-y-3">
      @forelse($kk->anggotas as $a)
        <div class="p-4 border rounded">
          <div class="flex justify-between items-start">
            <div>
              <div class="font-medium">{{ $a->nama }}</div>
              <div class="text-xs text-gray-600">NIK: {{ $a->nik ?? '-' }}</div>
            </div>
            <div class="text-sm text-gray-500">{{ $a->status_keluarga ?? '-' }}</div>
          </div>

          <div class="mt-2 text-sm text-gray-700 grid grid-cols-1 md:grid-cols-2 gap-2">
            <div><strong>Jenis Kelamin:</strong> {{ $a->jenis_kelamin === 'L' ? 'Laki-laki' : ($a->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</div>
            <div><strong>Tempat Lahir:</strong> {{ $a->tempat_lahir ?? '-' }}</div>
            <div><strong>Tanggal Lahir:</strong> {{ $a->tanggal_lahir ?? '-' }}</div>
            <div class="text-right">
              <a href="{{ route('penduduk.anggota.show', $a->id) }}" class="text-sm px-3 py-1 border rounded hover:bg-gray-50">Detail</a>
              <a href="{{ route('penduduk.anggota.export', $a->id) }}" class="text-sm px-3 py-1 bg-indigo-600 text-white rounded">Export PDF</a>
            </div>
          </div>
        </div>
      @empty
        <div class="text-sm text-gray-500">Belum ada anggota tercatat.</div>
      @endforelse
    </div>

  </div>
</div>
@endsection
