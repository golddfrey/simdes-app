@extends('layouts.app')

@section('title', 'Pengajuan Anggota Saya')

@section('content')
<div class="max-w-4xl mx-auto">
    <h1 class="text-xl font-semibold mb-6">Pengajuan Anggota</h1>

    @if (session('success'))
        <div class="mb-4 p-3 bg-green-50 text-green-700 rounded">
            {{ session('success') }}
        </div>
    @endif

    <div class="mb-4">
        <a href="{{ route('kk.anggota.create') }}" class="px-3 py-2 bg-indigo-600 text-white rounded">Buat Pengajuan Baru</a>
    </div>

    <div class="bg-white rounded border">
        <table class="w-full text-sm">
            <thead class="bg-gray-50">
                <tr>
                    <th class="p-2 border-b text-left">Waktu</th>
                    <th class="p-2 border-b text-left">NIK</th>
                    <th class="p-2 border-b text-left">Nama</th>
                    <th class="p-2 border-b text-left">JK</th>
                    <th class="p-2 border-b text-left">Status Keluarga</th>
                    <th class="p-2 border-b text-left">Status</th>
                    <th class="p-2 border-b text-left">Catatan</th>
                </tr>
            </thead>
            <tbody>
                @forelse ($list as $p)
                    @php 
                        // Pastikan data_json didecode dengan benar
                        // $d = json_decode($p->data_json, true);  
                        $d = $p->data_json ?? [];
                    @endphp
                    
                    <tr>
                        {{-- aman: jika created_at null tampilkan '-' --}}
                        <td class="p-2 border-b">
                            {{ $p->created_at ? $p->created_at->format('d M Y H:i') : '-' }}
                        </td>
                        <td class="p-2 border-b">{{ $d['nik'] ?? '-' }}</td>
                        <td class="p-2 border-b">{{ $d['nama'] ?? '-' }}</td>
                        <td class="p-2 border-b">{{ $d['jenis_kelamin'] ?? '-' }}</td>
                        <td class="p-2 border-b">{{ $d['status_keluarga'] ?? '-' }}</td>
                        <td class="p-2 border-b">
                            @if($p->status == 'pending')
                                <span class="text-yellow-700 bg-yellow-50 px-2 py-1 rounded">Pending</span>
                            @elseif($p->status == 'approved')
                                <span class="text-green-700 bg-green-50 px-2 py-1 rounded">Disetujui</span>
                            @else
                                <span class="text-red-700 bg-red-50 px-2 py-1 rounded">Ditolak</span>
                            @endif
                        </td>
                        <td class="p-2 border-b">{{ $p->notes ?? '-' }}</td>
                    </tr>
                @empty
                    <tr><td class="p-3" colspan="7">Belum ada pengajuan.</td></tr>
                @endforelse
                
            </tbody>
        </table>
    </div>

    <div class="mt-3">
        {{ $list->links() }}
    </div>
</div>
@endsection
