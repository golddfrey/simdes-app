<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Daftar • SIMDes</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50">
    <div class="min-h-screen flex items-center justify-center px-4">
        <div class="w-full max-w-md">
            <div class="text-center mb-8">
                <h1 class="text-2xl font-semibold text-gray-800">Buat Akun Kepala Keluarga</h1>
                <p class="text-sm text-gray-500 mt-1">Isi data di bawah untuk mendaftar</p>
            </div>

            @if ($errors->any())
                <div class="mb-4 rounded-md bg-red-50 p-4 text-sm text-red-700">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            <div class="bg-white shadow rounded-lg p-6">
                <form method="POST" action="{{ route('register.post') }}" class="space-y-5">
                    @csrf

                    <div>
                        <label for="name" class="block text-sm font-medium text-gray-700">Nama Lengkap</label>
                        <input id="name" name="name" type="text" value="{{ old('name') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Nama lengkap">
                    </div>

                    <div>
                        <label for="email" class="block text-sm font-medium text-gray-700">Email</label>
                        <input id="email" name="email" type="email" value="{{ old('email') }}" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="nama@contoh.com">
                    </div>

                    <div>
                        <label for="password" class="block text-sm font-medium text-gray-700">Kata sandi</label>
                        <input id="password" name="password" type="password" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Minimal 6 karakter">
                    </div>

                    <div>
                        <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Ulangi kata sandi</label>
                        <input id="password_confirmation" name="password_confirmation" type="password" required
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Ulangi kata sandi">
                    </div>

                    {{-- Opsional: jika nanti mau menghubungkan ke KK yang sudah ada --}}
                    <div>
                        <label for="kepala_keluarga_id" class="block text-sm font-medium text-gray-700">ID Kepala Keluarga (opsional)</label>
                        <input id="kepala_keluarga_id" name="kepala_keluarga_id" type="number" value="{{ old('kepala_keluarga_id') }}"
                               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                               placeholder="Kosongkan bila belum ada">
                        <p class="mt-1 text-xs text-gray-400">
                            Kosongkan jika belum terdaftar di data Kepala Keluarga.
                        </p>
                    </div>

                    <div class="flex items-center justify-between">
                        <a href="{{ route('login') }}" class="text-sm text-indigo-600 hover:text-indigo-700">Sudah punya akun? Masuk</a>
                    </div>

                    <button type="submit"
                            class="w-full inline-flex justify-center rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                        Daftar
                    </button>
                </form>
            </div>

            <p class="mt-6 text-center text-xs text-gray-400">&copy; {{ now()->year }} SIMDes</p>
        </div>
    </div>
</body>
</html>
