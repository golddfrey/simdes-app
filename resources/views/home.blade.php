@extends('layouts.app')

@section('content')
  <!-- Hero Section -->
  <header class="text-white bg-blue-600 py-16 rounded-xl shadow mb-10">
    <div class="container mx-auto px-6">
      <h1 class="text-5xl font-bold mb-4">Sistem Informasi Desa</h1>
      <p class="text-xl mb-8">Mengelola data desa dengan lebih mudah dan efisien</p>
      <a href="{{ route('kk.create') }}" class="bg-white text-blue-600 px-6 py-3 rounded-lg font-semibold hover:bg-blue-50">
        Mulai Sekarang
      </a>
    </div>
  </header>

  <!-- Features Section -->
  <section class="container mx-auto px-6 py-8">
    <h2 class="text-3xl font-bold text-center mb-8">Fitur Utama</h2>
    <div class="grid md:grid-cols-3 gap-8">
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="font-bold text-xl mb-4">Manajemen Data</h3>
        <p class="text-gray-600">Kelola data penduduk dan administrasi desa dengan mudah</p>
      </div>
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="font-bold text-xl mb-4">Laporan</h3>
        <p class="text-gray-600">Generate laporan desa secara otomatis dan akurat</p>
      </div>
      <div class="bg-white p-6 rounded-lg shadow-md">
        <h3 class="font-bold text-xl mb-4">Pelayanan</h3>
        <p class="text-gray-600">Tingkatkan kualitas pelayanan kepada masyarakat</p>
      </div>
    </div>
  </section>
@endsection
