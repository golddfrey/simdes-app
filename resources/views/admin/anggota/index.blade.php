@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-8">
    <div class="flex items-center justify-between mb-4">
        <h1 class="text-2xl font-semibold">Data Anggota</h1>

        <form method="get" class="flex gap-2">
            <input type="text" name="q" value="{{ $q }}"
                   placeholder="Cari nama / NIK / status"
                   class="border rounded px-3 py-2 w-64">
            <button class="px-4 py-2 rounded bg-indigo-600 text-white">Cari</button>
        </form>
    </div>

    <div class="mb-4">
        <a href="{{ route('admin.anggota.pending.index') }}"
           class="px-4 py-2 rounded bg-amber-500 text-white hover:bg-amber-600">
            Lihat Pengajuan Anggota (Pending)
        </a>
    </div>

    <div class="bg-white shadow rounded overflow-hidden">
        <table class="min-w-full">
            <thead class="bg-gray-50">
            <tr>
                <th class="px-4 py-2 text-left text-sm text-gray-600">NIK</th>
                <th class="px-4 py-2 text-left text-sm text-gray-600">Nama</th>
                <th class="px-4 py-2 text-left text-sm text-gray-600">Status</th>
                <th class="px-4 py-2 text-left text-sm text-gray-600">Kepala Keluarga</th>
                <th class="px-4 py-2 text-left text-sm text-gray-600">Desa</th>
            </tr>
            </thead>
            <tbody class="divide-y divide-gray-100">
            @forelse ($anggota as $row)
                <tr>
                    <td class="px-4 py-2 text-sm">{{ $row->nik }}</td>
                    <td class="px-4 py-2 text-sm">{{ $row->nama }}</td>
                    <td class="px-4 py-2 text-sm">{{ $row->status_keluarga }}</td>
                    <td class="px-4 py-2 text-sm">
                        {{ optional($row->kepalaKeluarga)->nama }}
                    </td>
                    <td class="px-4 py-2 text-sm">
                        {{ optional(optional($row->kepalaKeluarga)->desa)->nama }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-6 text-center text-sm text-gray-500">
                        Belum ada data.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div class="mt-4">{{ $anggota->links() }}</div>
</div>
@endsection
