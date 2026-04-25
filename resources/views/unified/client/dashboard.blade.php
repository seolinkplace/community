<!DOCTYPE html>
<html lang="uk">
<head>
    <meta charset="UTF-8">
    <title>Client Dashboard — seolinkplace</title>
    <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100 min-h-screen">
<nav class="bg-white border-b border-gray-200 px-4 py-3 flex justify-between items-center">
    <div class="flex items-center gap-6">
        <span class="font-bold text-gray-900">seolinkplace</span>
        <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">Client</span>
    </div>
    <div class="flex items-center gap-4">
        <a href="{{ route('unified.dashboard') }}" class="text-sm text-gray-500 hover:text-gray-900">← Всі кабінети</a>
        <form method="POST" action="{{ route('unified.logout') }}">
            @csrf
            <button class="text-sm text-gray-500 hover:text-gray-900">Вийти</button>
        </form>
    </div>
</nav>
<div class="max-w-4xl mx-auto py-10 px-4">
    <h1 class="text-xl font-bold text-gray-900">Кабінет Client</h1>
    <p class="text-sm text-gray-500 mt-2">В розробці — переносимо функціонал...</p>
</div>
</body>
</html>
