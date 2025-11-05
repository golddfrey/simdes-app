<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Kepala Keluarga • SIMDes</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="min-h-screen bg-gray-50">
<div class="min-h-screen flex items-center justify-center px-4">
    <div class="w-full max-w-md">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-semibold text-gray-800">SIMDes — Login Kepala Keluarga</h1>
            <p class="text-sm text-gray-500 mt-1">Masuk menggunakan NIK</p>
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
            <form method="POST" action="{{ route('kk.login.post') }}" class="space-y-5">
                @csrf

                <div>
                    <label for="nik" class="block text-sm font-medium text-gray-700">NIK</label>
                    <input id="nik" name="nik" type="text" value="{{ old('nik') }}" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500"
                           placeholder="16 digit NIK">
                </div>

                <div>
                    <label for="password" class="block text-sm font-medium text-gray-700">Kata sandi</label>
                    <input id="password" name="password" type="password" required
                           class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                </div>

                <div class="flex items-center justify-between">
                    <label class="inline-flex items-center">
                        <input type="checkbox" name="remember" class="rounded border-gray-300 text-indigo-600 focus:ring-indigo-500">
                        <span class="ml-2 text-sm text-gray-600">Ingat saya</span>
                    </label>
                    <a href="{{ route('admin.login') }}" class="text-sm text-indigo-600 hover:text-indigo-700">Login Admin</a>
                </div>

                <button type="submit"
                        class="w-full inline-flex justify-center rounded-md bg-indigo-600 px-4 py-2.5 text-sm font-medium text-white shadow hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500">
                    Masuk
                </button>
            </form>
        </div>

        <p class="mt-6 text-center text-xs text-gray-400">&copy; {{ now()->year }} SIMDes</p>
    </div>
</div>
</body>
</html>
