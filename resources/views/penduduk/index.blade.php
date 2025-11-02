{{-- resources/views/penduduk/index.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-6 py-6">
  <div class="flex items-center justify-between mb-6">
    <h2 class="text-2xl font-bold">Daftar Penduduk (KK + Anggota)</h2>

    <form method="GET" action="{{ route('penduduk.index') }}" class="flex items-center gap-2">
      <input name="q" value="{{ $q ?? '' }}" placeholder="Cari NIK atau Nama..." class="border rounded px-3 py-2 w-64" />
      <button class="bg-blue-600 text-white px-4 py-2 rounded">Cari</button>
    </form>
  </div>

  <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
    @foreach($residents as $r)
      <div class="bg-white rounded shadow p-4 flex flex-col justify-between">
        <div>
          <div class="flex items-center justify-between">
            <div>
              <div class="text-xs text-gray-500">NIK</div>
              <div class="font-semibold text-lg">{{ $r->nik ?? '-' }}</div>
            </div>

            <div class="text-right">
              <span class="text-xs text-gray-500">Tipe</span>
              <div class="text-sm font-medium">
                @if($r->source === 'kk')
                  Kepala Keluarga
                @else
                  Anggota
                @endif
              </div>
            </div>
          </div>

          <div class="mt-3">
            <div class="text-xs text-gray-500">Nama</div>
            <div class="font-medium">{{ $r->nama }}</div>
          </div>

          <div class="mt-2 text-sm text-gray-600">
            <div><strong>Jenis Kelamin:</strong>
              @if(($r->jenis_kelamin ?? null) === 'L') Laki-laki
              @elseif(($r->jenis_kelamin ?? null) === 'P') Perempuan
              @else - @endif
            </div>

            @if($r->source === 'kk')
              <div class="mt-1"><strong>RT/RW:</strong> {{ $r->rt ?? '-' }} / {{ $r->rw ?? '-' }}</div>
              <div class="mt-1"><strong>Alamat:</strong> {{ \Illuminate\Support\Str::limit($r->alamat ?? '-', 80) }}</div>
            @else
              <div class="mt-1"><strong>Status:</strong> {{ $r->status_keluarga ?? '-' }}</div>
              <div class="mt-1"><strong>Tempat Lahir:</strong> {{ $r->tempat_lahir ?? '-' }}</div>
              <div class="mt-1"><strong>Tgl Lahir:</strong> {{ $r->tanggal_lahir ?? '-' }}</div>
              <div class="mt-1"><strong>KK:</strong> {{ $r->kepala_nama ?? '-' }}</div>
            @endif
          </div>
        </div>

        <div class="mt-4 flex gap-2 justify-end">
          @if($r->source === 'kk')
            <a href="{{ route('penduduk.kk.show', $r->id) }}" class="px-3 py-2 border rounded hover:bg-gray-50">Detail</a>
            <a href="{{ route('penduduk.kk.export', $r->id) }}" class="px-3 py-2 bg-indigo-600 text-white rounded">Export PDF</a>
          @else
            <a href="{{ route('penduduk.anggota.show', $r->id) }}" class="px-3 py-2 border rounded hover:bg-gray-50">Detail</a>
            <a href="{{ route('penduduk.anggota.export', $r->id) }}" class="px-3 py-2 bg-indigo-600 text-white rounded">Export PDF</a>
          @endif
        </div>
      </div>
    @endforeach
  </div>

  <div class="mt-6">
    {{ $residents->links() }}
  </div>
</div>
@endsection
