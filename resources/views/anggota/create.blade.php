@extends('layouts.app')

@section('title', 'Tambah Anggota (Pengajuan)')

@section('content')
<div class="max-w-2xl mx-auto">
    <h1 class="text-xl font-semibold mb-6">Pengajuan Tambah Anggota</h1>

    @if ($errors->any())
        <div class="mb-4 p-3 bg-red-50 text-red-700 rounded">
            <ul class="list-disc list-inside">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('kk.anggota.store') }}" method="POST" class="space-y-4">
        @csrf

        <div>
            <label class="block text-sm font-medium mb-1">NIK</label>
            <input type="text" name="nik" value="{{ old('nik') }}" maxlength="16" class="w-full border rounded p-2" placeholder="16 digit NIK" required>
        </div>

        <div>
            <label class="block text-sm font-medium mb-1">Nama</label>
            <input type="text" name="nama" value="{{ old('nama') }}" class="w-full border rounded p-2" required>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Jenis Kelamin</label>
                <select name="jenis_kelamin" class="w-full border rounded p-2" required>
                    <option value="">-- pilih --</option>
                    <option value="L" @selected(old('jenis_kelamin')=='L')>Laki-laki</option>
                    <option value="P" @selected(old('jenis_kelamin')=='P')>Perempuan</option>
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Status Keluarga</label>
                <select name="status_keluarga" class="w-full border rounded p-2" required>
                    <option value="">-- pilih --</option>
                    <option value="ISTRI" @selected(old('status_keluarga')=='ISTRI')>ISTRI</option>
                    <option value="ANAK" @selected(old('status_keluarga')=='ANAK')>ANAK</option>
                    <option value="KELUARGA LAIN" @selected(old('status_keluarga')=='KELUARGA LAIN')>KELUARGA LAIN</option>
                </select>
            </div>
        </div>

        <div class="grid grid-cols-2 gap-4">
            <div>
                <label class="block text-sm font-medium mb-1">Tempat Lahir</label>
                <input type="text" name="tempat_lahir" value="{{ old('tempat_lahir') }}" class="w-full border rounded p-2">
            </div>
            <div>
                <label class="block text-sm font-medium mb-1">Tanggal Lahir</label>
                <input type="date" name="tanggal_lahir" value="{{ old('tanggal_lahir') }}" class="w-full border rounded p-2">
            </div>
        </div>

        <div class="pt-2">
            <button class="px-4 py-2 bg-indigo-600 text-white rounded">Kirim Pengajuan</button>
            <a href="{{ route('kk.anggota.pending') }}" class="ml-3 text-indigo-600 underline">Lihat Pengajuan</a>
        </div>
    </form>
</div>
@endsection
