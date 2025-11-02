{{-- resources/views/kk/edit.blade.php --}}
@extends('layouts.app')

@section('content')
@php
  // Normalisasi tanggal agar tampil di input date dengan format YYYY-MM-DD
  $oldAnggota = $oldAnggota ?? [];
  $formatted = collect($oldAnggota)->map(function($a){
      if (!empty($a['tanggal_lahir'])) {
          $a['tanggal_lahir'] = \Illuminate\Support\Carbon::parse($a['tanggal_lahir'])->format('Y-m-d');
      }
      return $a;
  });
@endphp

<div class="p-6">
  <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-xl font-bold mb-4">Edit Kepala Keluarga</h1>

    @if(session('success'))
      <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    @if($errors->any())
      <div class="bg-red-50 text-red-800 p-3 rounded mb-4">
        <ul class="list-disc pl-5">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('kk.update', $kk->id) }}" method="POST"
          x-data='{
            anggota: {!! json_encode($formatted, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) !!},
            addAnggota() {
              this.anggota.push({ nik: "", nama: "", jenis_kelamin: "", status_keluarga: "", tempat_lahir: "", tanggal_lahir: "" });
            }
          }'>
      @csrf
      @method('PUT')

      {{-- NIK --}}
      <div class="mb-3">
        <label class="block text-sm font-medium">NIK</label>
        <input name="nik" class="w-full border p-2 rounded" value="{{ old('nik', $kk->nik) }}" required>
      </div>

      {{-- Nama --}}
      <div class="mb-3">
        <label class="block text-sm font-medium">Nama</label>
        <input name="nama" class="w-full border p-2 rounded" value="{{ old('nama', $kk->nama) }}" required>
      </div>

      {{-- Phone --}}
      <div class="mb-3">
        <label class="block text-sm font-medium">Phone</label>
        <input name="phone" class="w-full border p-2 rounded" value="{{ old('phone', $kk->phone) }}">
      </div>

      {{-- Alamat --}}
      <div class="mb-3">
        <label class="block text-sm font-medium">Alamat</label>
        <textarea name="alamat" class="w-full border p-2 rounded">{{ old('alamat', $kk->alamat) }}</textarea>
      </div>

      {{-- RT/RW --}}
      <div class="grid grid-cols-2 gap-4 mb-3">
        <div>
          <label class="block text-sm font-medium">RT</label>
          <input name="rt" class="w-full border p-2 rounded" value="{{ old('rt', $kk->rt) }}">
        </div>
        <div>
          <label class="block text-sm font-medium">RW</label>
          <input name="rw" class="w-full border p-2 rounded" value="{{ old('rw', $kk->rw) }}">
        </div>
      </div>

      {{-- Anggota dynamic --}}
      <div class="mb-3">
        <h2 class="font-semibold mb-2">Anggota Keluarga</h2>

        <template x-for="(a, i) in anggota" :key="i">
          <div class="mb-3 p-3 border rounded">
            <div class="flex justify-between items-center mb-2">
              <strong x-text="'Anggota ' + (i+1)"></strong>
              <button type="button" class="text-red-600" @click="anggota.splice(i,1)">Hapus</button>
            </div>

            <div class="mb-2">
              <label class="block text-sm">Nama</label>
              <input :name="'anggota['+i+'][nama]'" x-model="a.nama" class="w-full border p-2 rounded" required>
            </div>

            <div class="mb-2">
              <label class="block text-sm">NIK</label>
              <input :name="'anggota['+i+'][nik]'" x-model="a.nik" class="w-full border p-2 rounded">
            </div>

            <div class="mb-2">
              <label class="block text-sm">Jenis Kelamin</label>
              <select :name="'anggota['+i+'][jenis_kelamin]'" x-model="a.jenis_kelamin" class="w-full border p-2 rounded">
                <option value="">Pilih</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
              </select>
            </div>

            <div class="mb-2">
              <label class="block text-sm">Status dalam keluarga</label>
              <input :name="'anggota['+i+'][status_keluarga]'" x-model="a.status_keluarga" class="w-full border p-2 rounded">
            </div>

            <div class="mb-2">
              <label class="block text-sm">Tempat Lahir</label>
              <input :name="'anggota['+i+'][tempat_lahir]'" x-model="a.tempat_lahir" class="w-full border p-2 rounded">
            </div>

            <div class="mb-2">
              <label class="block text-sm">Tanggal Lahir</label>
              <input type="date" :name="'anggota['+i+'][tanggal_lahir]'" x-model="a.tanggal_lahir" class="w-full border p-2 rounded">
            </div>
          </div>
        </template>

        <div class="mt-2">
          <button type="button" class="px-3 py-2 bg-blue-600 text-white rounded" @click="addAnggota()">Tambah Anggota</button>
        </div>
      </div>

      <div class="mt-4 flex gap-2">
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Simpan</button>
        <a href="{{ route('kk.show', $kk->id) }}" class="px-4 py-2 border rounded">Batal</a>
      </div>
    </form>
  </div>
</div>

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
@endsection
