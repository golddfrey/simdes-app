<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Beranda • SIMDes</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50">
    <header class="bg-white shadow">
        <div class="max-w-5xl mx-auto px-4 py-4 flex items-center justify-between">
            <h1 class="text-lg font-semibold text-gray-800">SIMDes</h1>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <button class="px-3 py-1.5 text-sm rounded bg-gray-800 text-white hover:bg-black">
                    Keluar
                </button>
            </form>
        </div>
    </header>

    <main class="max-w-5xl mx-auto px-4 py-10">
        @if (session('success'))
            <div class="mb-4 rounded-md bg-green-50 p-4 text-sm text-green-700">
                {{ session('success') }}
            </div>
        @endif

        <h2 class="text-xl font-semibold text-gray-800">Halo, {{ $user->name ?? 'Pengguna' }}</h2>

        <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Total Desa</div>
                <div class="mt-1 text-2xl font-semibold text-gray-800">
                    {{ $stats['total_desa'] ?? 0 }}
                </div>
            </div>

            @isset($stats['anggota_count'])
            <div class="bg-white rounded-lg shadow p-4">
                <div class="text-sm text-gray-500">Anggota Keluarga Anda</div>
                <div class="mt-1 text-2xl font-semibold text-gray-800">
                    {{ $stats['anggota_count'] }}
                </div>
            </div>
            @endisset
        </div>

        <div class="mt-8">
            <p class="text-sm text-gray-600">
                Ini adalah beranda sederhana. Menu lengkap untuk kepala keluarga & admin akan kita lengkapi setelah autentikasi beres.
            </p>
        </div>
    </main>
</body>
</html>
