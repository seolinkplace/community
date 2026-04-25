<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Вхід — seolinkplace</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-6xl">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">seolinkplace</h1>
            <p class="text-gray-500 mt-1 text-sm">SEO-студія / спеціаліст</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Вхід</h2>

            @if(session('prefill_email'))
                <div class="mb-4 p-3 bg-blue-50 border border-blue-200 text-blue-700 rounded-lg text-sm">
                    Переключення з кабінету вебмастера. Введіть пароль для клієнта.
                </div>
            @endif

            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">{{ $errors->first() }}</div>
            @endif

            <form method="POST" action="{{ route('unified.login') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ session('prefill_email', old('email')) }}" required autofocus
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Пароль</label>
                        <input type="password" name="password" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                    </div>
                    <div class="flex items-center">
                        <input type="checkbox" name="remember" id="remember" class="rounded border-gray-300">
                        <label for="remember" class="ml-2 text-sm text-gray-600">Запам'ятати мене</label>
                    </div>
                </div>
                <button type="submit" class="w-full mt-6 bg-gray-900 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-gray-700 transition">
                    Увійти
                </button>
            </form>

            <p class="mt-6 text-center text-sm text-gray-500">
                Немає акаунту? <a href="{{ route('client.register') }}" class="text-gray-900 font-medium hover:underline">Зареєструватись</a>
            </p>
        </div>
    </div>
</body>
</html>
