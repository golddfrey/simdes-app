{{-- resources/views/kk/create.blade.php --}}
@extends('layouts.app')

@section('content')
@php
  // Ambil old anggota jika ada (untuk rehydrate di Alpine)
  $oldAnggota = old('anggota', []);
@endphp

<div class="p-6">
  <div class="max-w-2xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-xl font-bold mb-4">Pendaftaran Kepala Keluarga</h1>

    {{-- Flash success --}}
    @if(session('success'))
      <div class="bg-green-100 text-green-800 p-3 rounded mb-4">{{ session('success') }}</div>
    @endif

    {{-- Internal error dari controller --}}
    @if($errors->has('internal'))
      <div class="bg-red-100 text-red-800 p-3 rounded mb-4">
        {{ $errors->first('internal') }}
      </div>
    @endif

    {{-- Summary validation errors --}}
    @if($errors->any())
      <div class="bg-red-50 text-red-800 p-3 rounded mb-4">
        <ul class="list-disc pl-5">
          @foreach($errors->all() as $err)
            <li>{{ $err }}</li>
          @endforeach
        </ul>
      </div>
    @endif

    <form action="{{ route('kk.store') }}" method="POST"
          x-data='{
            anggota: {!! json_encode($oldAnggota, JSON_HEX_TAG|JSON_HEX_APOS|JSON_HEX_AMP|JSON_HEX_QUOT) !!},
            addAnggota() {
              this.anggota.push({ nik: "", nama: "", jenis_kelamin: "", status_keluarga: "", tempat_lahir: "", tanggal_lahir: "" });
            }
          }'
    >
      @csrf

      {{-- NIK --}}
      <div class="mb-3">
        <label class="block text-sm font-medium">NIK</label>
        <input name="nik"
               class="w-full border p-2 rounded @error('nik') border-red-500 @enderror"
               value="{{ old('nik') }}"
               required>
        @error('nik')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- Nama --}}
      <div class="mb-3">
        <label class="block text-sm font-medium">Nama</label>
        <input name="nama"
               class="w-full border p-2 rounded @error('nama') border-red-500 @enderror"
               value="{{ old('nama') }}"
               required>
        @error('nama')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- Phone --}}
      <div class="mb-3">
        <label class="block text-sm font-medium">Phone</label>
        <input name="phone"
               class="w-full border p-2 rounded @error('phone') border-red-500 @enderror"
               value="{{ old('phone') }}">
        @error('phone')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- Alamat --}}
      <div class="mb-3">
        <label class="block text-sm font-medium">Alamat</label>
        <textarea name="alamat"
                  class="w-full border p-2 rounded @error('alamat') border-red-500 @enderror"
        >{{ old('alamat') }}</textarea>
        @error('alamat')
          <p class="text-sm text-red-600 mt-1">{{ $message }}</p>
        @enderror
      </div>

      {{-- Anggota dynamic --}}
      <div class="mb-3" x-data>
        <h2 class="font-semibold mb-2">Anggota Keluarga</h2>

        {{-- Jika ada old errors per anggota, summary di atas sudah menampilkan semuanya.
             Di bawah ini kita render anggota yang ada di Alpine (dari old() atau kosong). --}}
        <template x-for="(a, i) in anggota" :key="i">
          <div class="mb-2 p-3 border rounded" x-cloak>
            <div class="flex justify-between items-center mb-2">
              <strong x-text="'Anggota ' + (i+1)"></strong>
              <button type="button" class="text-red-600" @click="anggota.splice(i,1)">Hapus</button>
            </div>

            {{-- nik anggota (hidden) --}}
            <input type="hidden" :name="'anggota['+i+'][nik]'" x-model="a.nik">

            {{-- Nama anggota --}}
            <div class="mb-2">
              <label class="block text-sm">Nama</label>
              <input :name="'anggota['+i+'][nama]'" x-model="a.nama" class="w-full border p-2 rounded" required>
              {{-- Per-field anggota server-side errors: sulit menangkap index dinamis di blade,
                   oleh karena itu kita andalkan summary errors (di atas). --}}
            </div>

            {{-- Jenis Kelamin --}}
            <div class="mb-2">
              <label class="block text-sm">Jenis Kelamin</label>
              <select :name="'anggota['+i+'][jenis_kelamin]'" x-model="a.jenis_kelamin" class="w-full border p-2 rounded">
                <option value="">Pilih</option>
                <option value="L">Laki-laki</option>
                <option value="P">Perempuan</option>
              </select>
            </div>

            {{-- Status keluarga --}}
            <div class="mb-2">
              <label class="block text-sm">Status dalam keluarga</label>
              <input :name="'anggota['+i+'][status_keluarga]'" x-model="a.status_keluarga" class="w-full border p-2 rounded">
            </div>

            {{-- Tempat lahir --}}
            <div class="mb-2">
              <label class="block text-sm">Tempat Lahir</label>
              <input :name="'anggota['+i+'][tempat_lahir]'" x-model="a.tempat_lahir" class="w-full border p-2 rounded">
            </div>

            {{-- Tanggal lahir --}}
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

      <div class="mt-4">
        <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded">Submit</button>
      </div>
    </form>
  </div>
</div>

{{-- Fallback memuat Alpine jika belum ada (aman) --}}
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
