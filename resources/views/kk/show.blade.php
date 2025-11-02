{{-- resources/views/kk/show.blade.php --}}
@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
  <div class="max-w-3xl mx-auto bg-white p-6 rounded shadow">
    @if(session('success'))
      <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    <h2 class="text-xl font-bold mb-4">Detail Kepala Keluarga</h2>

    <div class="grid md:grid-cols-2 gap-4 mb-6">
      <div>
        <div class="text-sm text-gray-500">NIK</div>
        <div class="font-medium">{{ $kk->nik }}</div>
      </div>
      <div>
        <div class="text-sm text-gray-500">Nama</div>
        <div class="font-medium">{{ $kk->nama }}</div>
      </div>

      <div>
        <div class="text-sm text-gray-500">Phone</div>
        <div>{{ $kk->phone ?? '-' }}</div>
      </div>

      <div>
        <div class="text-sm text-gray-500">Alamat</div>
        <div>{{ $kk->alamat ?? '-' }}</div>
      </div>

      <div>
        <div class="text-sm text-gray-500">RT / RW</div>
        <div>{{ $kk->rt ?? '-' }} / {{ $kk->rw ?? '-' }}</div>
      </div>

      <div>
        <div class="text-sm text-gray-500">Desa</div>
        <div>{{ optional($kk->desa)->nama ?? '-' }}</div>
      </div>
    </div>

    <div class="mt-4">
      <h3 class="font-semibold mb-2">Anggota Keluarga ({{ $kk->anggotas->count() }})</h3>

      @if($kk->anggotas->isEmpty())
        <div class="text-sm text-gray-500">Belum ada anggota tercatat.</div>
      @else
        <ul class="space-y-2">
          @foreach($kk->anggotas as $a)
            <li class="p-3 border rounded">
              <div class="font-medium text-lg">{{ $a->nama }}</div>
              <div class="text-sm text-gray-600">
                <span class="block">NIK: {{ $a->nik ?? '-' }}</span>
                <span class="block">Status: {{ $a->status_keluarga ?? '-' }}</span>
                <span class="block">Tempat Lahir: {{ $a->tempat_lahir ?? '-' }}</span>
                <span class="block">Tanggal Lahir: 
                  {{ $a->tanggal_lahir ? \Illuminate\Support\Carbon::parse($a->tanggal_lahir)->format('Y-m-d') : '-' }}
                </span>
              </div>
            </li>
          @endforeach
        </ul>
      @endif
    </div>

    <div class="mt-6 flex gap-2">
      <a href="{{ route('kk.edit', $kk->id) }}" class="px-4 py-2 bg-yellow-500 text-white rounded">Edit</a>

      <form action="{{ route('kk.destroy', $kk->id) }}" method="POST" onsubmit="return confirm('Yakin ingin menghapus data ini?');">
        @csrf
        @method('DELETE')
        <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded">Hapus</button>
      </form>

      <a href="{{ route('kk.index') }}" class="px-4 py-2 border rounded">Kembali</a>
    </div>
  </div>
</div>
@endsection
