{{-- resources/views/penduduk/anggota_show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-6">
  <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">

    {{-- Pesan sukses --}}
    @if(session('success'))
      <div class="bg-green-100 text-green-800 p-3 rounded mb-4">
        {{ session('success') }}
      </div>
    @endif

    {{-- Header --}}
    <div class="flex items-start justify-between mb-4">
      <div>
        <h3 class="text-xl font-bold">Detail Penduduk — Anggota</h3>
        <div class="text-sm text-gray-500">Nama: <span class="font-medium">{{ $a->nama }}</span></div>
      </div>

      <div class="text-right">
        <div class="text-xs text-gray-500">ID</div>
        <div class="text-sm text-gray-600">{{ $a->id }}</div>
      </div>
    </div>

    {{-- Info utama --}}
    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <div class="text-xs text-gray-500">NIK</div>
        <div class="font-semibold mb-3">{{ $a->nik ?? '-' }}</div>

        <div class="text-xs text-gray-500">Status dalam keluarga</div>
        <div class="mb-3">{{ $a->status_keluarga ?? '-' }}</div>

        <div class="text-xs text-gray-500">Jenis Kelamin</div>
        <div class="mb-3">
          {{ $a->jenis_kelamin === 'L' ? 'Laki-laki' : ($a->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}
        </div>
      </div>

      <div>
        <div class="text-xs text-gray-500">Tempat Lahir</div>
        <div class="mb-3">{{ $a->tempat_lahir ?? '-' }}</div>

        <div class="text-xs text-gray-500">Tanggal Lahir</div>
        <div class="mb-3">{{ $a->tanggal_lahir ?? '-' }}</div>

        <div class="text-xs text-gray-500">Kepala Keluarga</div>
        <div class="mb-3">
          @if($a->kepalaKeluarga)
            <a href="{{ route('penduduk.kk.show', $a->kepalaKeluarga->id) }}" class="text-blue-600 hover:underline">
              {{ $a->kepalaKeluarga->nama }} (ID: {{ $a->kepalaKeluarga->id }})
            </a>
          @else
            -
          @endif
        </div>
      </div>
    </div>

    <hr class="my-6">

    {{-- aksi --}}
    <div class="flex gap-2">
      {{-- Pastikan route penduduk.anggota.export ada; jika tidak ubah route nama sesuai project --}}
      <a href="{{ route('penduduk.anggota.export', $a->id) }}" class="px-4 py-2 bg-indigo-600 text-white rounded">Export PDF</a>

      {{-- kembali ke daftar penduduk --}}
      <a href="{{ route('penduduk.index') }}" class="px-4 py-2 border rounded">Kembali</a>

      {{-- edit anggota (opsional) --}}
      @if(Route::has('penduduk.anggota.edit'))
        <a href="{{ route('penduduk.anggota.edit', $a->id) }}" class="px-4 py-2 bg-yellow-400 text-white rounded">Edit</a>
      @endif
    </div>

  </div>
</div>
@endsection
