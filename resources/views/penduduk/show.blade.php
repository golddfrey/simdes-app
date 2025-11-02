{{-- resources/views/penduduk/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-6">
  <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    @if(session('success'))
      <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <h3 class="text-xl font-bold mb-4">Detail Kepala Keluarga</h3>

    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
      <div>
        <div class="text-xs text-gray-500">NIK</div>
        <div class="font-semibold text-lg">{{ $kk->nik }}</div>

        <div class="mt-3 text-sm">
          <div><strong>Nama:</strong> {{ $kk->nama }}</div>
          <div><strong>Jenis Kelamin:</strong> {{ ($kk->jenis_kelamin === 'L') ? 'Laki-laki' : (($kk->jenis_kelamin === 'P') ? 'Perempuan' : '-') }}</div>
          <div><strong>Phone:</strong> {{ $kk->phone ?? '-' }}</div>
          <div><strong>Alamat:</strong> {{ $kk->alamat ?? '-' }}</div>
          <div><strong>RT / RW:</strong> {{ $kk->rt ?? '-' }} / {{ $kk->rw ?? '-' }}</div>
        </div>
      </div>

      <div>
        <div class="text-xs text-gray-500">Desa</div>
        <div class="font-medium">{{ optional($kk->desa)->nama ?? '-' }}</div>

        <div class="mt-4 text-sm text-gray-500">Terdaftar: {{ optional($kk->created_at)->format('Y-m-d') }}</div>

        <div class="mt-6 flex gap-2">
          <a href="{{ route('penduduk.edit', $kk->id) }}" class="px-4 py-2 bg-yellow-400 rounded text-white">Edit</a>
          <a href="{{ route('penduduk.export', $kk->id) }}" class="px-4 py-2 bg-indigo-600 rounded text-white">Export PDF</a>
          <a href="{{ route('penduduk.index') }}" class="px-4 py-2 border rounded">Kembali</a>
        </div>
      </div>
    </div>

    <hr class="my-6" />

    <h4 class="font-semibold mb-3">Anggota Keluarga ({{ $kk->anggotas->count() }})</h4>

    @if($kk->anggotas->isEmpty())
      <div class="text-sm text-gray-500">Belum ada anggota tercatat.</div>
    @else
      <div class="space-y-3">
        @foreach($kk->anggotas as $a)
          <div class="p-4 border rounded">
            <div class="flex justify-between items-start">
              <div>
                <div class="font-medium">{{ $a->nama }}</div>
                <div class="text-sm text-gray-600">NIK: {{ $a->nik ?? '-' }}</div>
              </div>
              <div class="text-sm text-gray-500">ID: {{ $a->id }}</div>
            </div>

            <div class="mt-2 text-sm text-gray-700">
              <div><strong>Status:</strong> {{ $a->status_keluarga ?? '-' }}</div>
              <div><strong>Jenis Kelamin:</strong> {{ $a->jenis_kelamin === 'L' ? 'Laki-laki' : ($a->jenis_kelamin === 'P' ? 'Perempuan' : '-') }}</div>
              <div><strong>Tempat Lahir:</strong> {{ $a->tempat_lahir ?? '-' }}</div>
              <div><strong>Tanggal Lahir:</strong> {{ $a->tanggal_lahir ?? '-' }}</div>
            </div>
          </div>
        @endforeach
      </div>
    @endif

  </div>
</div>
@endsection
