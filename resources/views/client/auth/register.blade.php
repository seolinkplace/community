<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Реєстрація — seolinkplace</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen flex items-center justify-center">
    <div class="w-full max-w-6xl">
        <div class="text-center mb-8">
            <h1 class="text-2xl font-bold text-gray-900">seolinkplace</h1>
            <p class="text-gray-500 mt-1 text-sm">SEO-студія / спеціаліст</p>
        </div>
        <div class="bg-white rounded-2xl border border-gray-200 p-8">
            <h2 class="text-xl font-semibold text-gray-900 mb-6">Реєстрація</h2>
            @if($errors->any())
                <div class="mb-4 p-3 bg-red-50 border border-red-200 text-red-700 rounded-lg text-sm">
                    <ul class="list-disc list-inside space-y-1">
                        @foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach
                    </ul>
                </div>
            @endif
            <form method="POST" action="{{ route('client.register') }}">
                @csrf
                <div class="space-y-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Ім'я</label>
                        <input type="text" name="name" value="{{ old('name') }}" required autofocus
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Компанія</label>
                        <input type="text" name="company_name" value="{{ old('company_name') }}"
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                        <input type="email" name="email" value="{{ old('email') }}" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Пароль</label>
                        <input type="password" name="password" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Підтвердити пароль</label>
                        <input type="password" name="password_confirmation" required
                            class="w-full border border-gray-300 rounded-lg px-3 py-2.5 text-sm focus:outline-none focus:ring-2 focus:ring-gray-900">
                    </div>
                </div>
                <button type="submit" class="w-full mt-6 bg-gray-900 text-white py-2.5 rounded-lg text-sm font-medium hover:bg-gray-700 transition">
                    Зареєструватись
                </button>
            </form>
            <p class="mt-6 text-center text-sm text-gray-500">
                Вже є акаунт? <a href="{{ route('unified.login') }}" class="text-gray-900 font-medium hover:underline">Увійти</a>
            </p>
        </div>
    </div>
</body>
</html>
