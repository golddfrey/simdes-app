<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard • SIMDes</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50">
    <header class="bg-white shadow">
        <div class="max-w-6xl mx-auto px-4 py-4 flex items-center justify-between">
            <h1 class="text-lg font-semibold text-gray-800">SIMDes — Admin</h1>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="px-3 py-1.5 text-sm rounded bg-gray-800 text-white hover:bg-black">
                    Keluar
                </button>
            </form>
        </div>
    </header>

    <main class="max-w-6xl mx-auto px-4 py-8">
        <h2 class="text-xl font-semibold text-gray-800 mb-4">Ringkasan</h2>

        @if (session('success'))
            <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Kepala Keluarga</div>
                <div class="mt-1 text-2xl font-semibold text-gray-800">
                    {{ $counts['kepala_keluarga'] ?? 0 }}
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Anggota</div>
                <div class="mt-1 text-2xl font-semibold text-gray-800">
                    {{ $counts['anggota'] ?? 0 }}
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Penduduk</div>
                <div class="mt-1 text-2xl font-semibold text-gray-800">
                    {{ $counts['penduduk'] ?? 0 }}
                </div>
            </div>
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Desa</div>
                <div class="mt-1 text-2xl font-semibold text-gray-800">
                    {{ $counts['desa'] ?? 0 }}
                </div>
            </div>
        </div>

        <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="bg-white rounded-lg shadow">
                <div class="px-4 py-3 border-b">
                    <h3 class="font-medium text-gray-700">Kepala Keluarga Terbaru</h3>
                </div>
                <div class="p-4">
                    @forelse ($latestKk ?? [] as $kk)
                        <div class="py-2 border-b last:border-0">
                            <div class="text-sm font-medium text-gray-800">{{ $kk->nama ?? '—' }}</div>
                            <div class="text-xs text-gray-500">Dibuat: {{ optional($kk->created_at)->format('d M Y H:i') }}</div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500">Belum ada data.</div>
                    @endforelse
                </div>
            </div>

            <div class="bg-white rounded-lg shadow">
                <div class="px-4 py-3 border-b">
                    <h3 class="font-medium text-gray-700">Anggota Terbaru</h3>
                </div>
                <div class="p-4">
                    @forelse ($latestAnggota ?? [] as $a)
                        <div class="py-2 border-b last:border-0">
                            <div class="text-sm font-medium text-gray-800">{{ $a->nama ?? '—' }}</div>
                            <div class="text-xs text-gray-500">Dibuat: {{ optional($a->created_at)->format('d M Y H:i') }}</div>
                        </div>
                    @empty
                        <div class="text-sm text-gray-500">Belum ada data.</div>
                    @endforelse
                </div>
            </div>
        </div>
    </main>
</body>
</html>
