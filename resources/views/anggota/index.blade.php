
@extends('layouts.app')

@section('content')
<div class="max-w-5xl mx-auto py-8">
    <h1 class="text-2xl font-semibold mb-4">Anggota Keluarga Saya</h1>

    <div class="mb-4">
        <a href="{{ route('kk.anggota.create') }}"
           class="px-4 py-2 rounded bg-indigo-600 text-white hover:bg-indigo-700">
           + Ajukan Tambah Anggota
        </a>
        <a href="{{ route('kk.anggota.pending') }}"
           class="ml-2 px-4 py-2 rounded bg-gray-200 hover:bg-gray-300">
           Lihat Pengajuan
        </a>
    </div>

    <div class="bg-white shadow rounded overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">NIK</th>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Nama</th>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Status</th>
                <th class="px-4 py-2 text-left text-sm font-medium text-gray-600">Tanggal Lahir</th>
                <th class="px-4 py-2"></th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse ($anggota as $row)
                <tr>
                    <td class="px-4 py-2 text-sm">{{ $row->nik }}</td>
                    <td class="px-4 py-2 text-sm">{{ $row->nama }}</td>
                    <td class="px-4 py-2 text-sm">{{ $row->status_keluarga }}</td>
                    <td class="px-4 py-2 text-sm">{{ $row->tanggal_lahir }}</td>
                    <td class="px-4 py-2 text-right">
                        <a href="{{ route('kk.anggota.edit', $row->id) }}"
                           class="text-indigo-600 hover:underline text-sm">Ajukan Perubahan</a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td class="px-4 py-6 text-center text-sm text-gray-500" colspan="5">
                        Belum ada anggota.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">
        {{ $anggota->links() }}
    </div>
</div>
@endsection
