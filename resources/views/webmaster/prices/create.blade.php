@extends('webmaster.layouts.app')
@section('title', 'Нове правило ціни')
@section('content')
<div class="max-w-2xl mx-auto py-8 px-4">
    <div class="flex items-center gap-3 mb-6">
        <a href="{{ route('webmaster.prices.index') }}" class="text-gray-400 hover:text-gray-600">←</a>
        <h1 class="text-2xl font-bold text-gray-800">Нове правило ціни</h1>
    </div>

    @if($errors->any())
        <div class="mb-4 bg-red-50 border border-red-200 text-red-800 rounded-lg px-4 py-3 text-sm">
            <ul class="list-disc list-inside space-y-1">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <form method="POST" action="{{ route('webmaster.prices.store') }}" class="bg-white rounded-xl shadow p-6 space-y-5">
        @csrf
        @include('webmaster.prices._form', ['price' => null])
        <div class="flex gap-3 pt-2">
            <button type="submit"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-medium px-5 py-2 rounded-lg text-sm">
                Зберегти
            </button>
            <a href="{{ route('webmaster.prices.index') }}"
               class="border border-gray-300 text-gray-600 hover:bg-gray-50 font-medium px-5 py-2 rounded-lg text-sm">
                Скасувати
            </a>
        </div>
    </form>
</div>
@endsection
