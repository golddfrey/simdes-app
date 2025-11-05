@extends('layouts.app')

@section('title', 'Daftar Pengajuan Anggota Baru')

@section('content')
<div class="max-w-5xl mx-auto">
    <h1 class="text-xl font-semibold mb-6">Daftar Pengajuan Anggota Baru</h1>

    @if(session('success'))
        <div class="mb-4 p-3 bg-green-50 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    @if(session('error'))
        <div class="mb-4 p-3 bg-red-50 text-red-700 rounded">
            {{ session('error') }}
        </div>
    @endif

    @if($pengajuan->isEmpty())
        <div class="bg-white p-4 rounded border">
            Tidak ada pengajuan baru saat ini.
        </div>
    @else
        <div class="bg-white rounded border">
            <table class="w-full text-sm">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="p-3 border-b text-left">NIK</th>
                        <th class="p-3 border-b text-left">Nama</th>
                        <th class="p-3 border-b text-left">Status</th>
                        <th class="p-3 border-b text-left">Kepala Keluarga</th>
                        <th class="p-3 border-b text-left">Diajukan</th>
                        <th class="p-3 border-b text-left">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($pengajuan as $p)
                        <tr>
                            <td class="p-3 border-b">{{ $p->data_json['nik'] ?? $p->nik }}</td>
                            <td class="p-3 border-b">{{ $p->data_json['nama'] ?? $p->nama }}</td>
                            <td class="p-3 border-b">{{ $p->data_json['status_keluarga'] ?? $p->status_keluarga }}</td>
                            <td class="p-3 border-b">{{ $p->kepalaKeluarga->nama ?? '-' }}</td>
                            <td class="p-3 border-b">{{ $p->created_at ? $p->created_at->format('d M Y H:i') : '-' }}</td>
                            <td class="p-3 border-b">
                                <div class="flex gap-2">
                                    <form action="{{ route('admin.anggota.pending.approve', $p->id) }}" method="POST" onsubmit="return confirm('Setujui pengajuan ini?');">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-green-600 text-white rounded">Approve</button>
                                    </form>

                                    <form action="{{ route('admin.anggota.pending.reject', $p->id) }}" method="POST" onsubmit="return confirm('Tolak pengajuan ini?');">
                                        @csrf
                                        <button type="submit" class="px-3 py-1 bg-red-600 text-white rounded">Tolak</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>

        <div class="mt-3">
            {{ $pengajuan->links() }}
        </div>
    @endif
</div>
@endsection
