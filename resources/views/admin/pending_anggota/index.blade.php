@extends('layouts.app')

@section('content')
<div class="max-w-6xl mx-auto py-8">
    <h1 class="text-2xl font-semibold">Daftar Pengajuan Anggota</h1>
    <table class="min-w-full">
        <thead>
            <tr>
                <th>NIK</th>
                <th>Nama</th>
                <th>Status</th>
                <th>Aksi</th>
            </tr>
        </thead>
        <tbody>
            @foreach($pengajuan as $p)
            <tr>
                <td>{{ $p->nik }}</td>
                <td>{{ $p->nama }}</td>
                <td>{{ $p->status }}</td>
                <td>
                    <form action="{{ route('admin.anggota.pending.approve', $p->id) }}" method="POST">
                        @csrf
                        <button type="submit">Approve</button>
                    </form>
                    <form action="{{ route('admin.anggota.pending.reject', $p->id) }}" method="POST">
                        @csrf
                        <button type="submit">Reject</button>
                    </form>
                </td>
            </tr>
            @endforeach
        </tbody>
    </table>
    {{ $pengajuan->links() }}
</div>
@endsection
