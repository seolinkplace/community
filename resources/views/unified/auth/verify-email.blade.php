@extends('unified.layouts.app')

@section('title', __('auth.verify_email_title'))

@section('content')
<div class="min-h-screen flex items-center justify-center bg-gray-50 py-12 px-4">
    <div class="max-w-md w-full">
        <div class="bg-white rounded-2xl shadow-sm border border-gray-100 p-8 text-center">

            <div class="w-16 h-16 bg-yellow-100 rounded-full flex items-center justify-center mx-auto mb-6">
                <svg class="w-8 h-8 text-yellow-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/>
                </svg>
            </div>

            <h1 class="text-2xl font-bold text-gray-900 mb-3">{{ __('auth.verify_email_title') }}</h1>
            <p class="text-gray-600 mb-6">{{ __('auth.verify_email_body') }}</p>

            @if(session('resent'))
                <div class="bg-green-50 border border-green-200 text-green-700 rounded-lg px-4 py-3 mb-6 text-sm">
                    {{ __('auth.verify_email_resent') }}
                </div>
            @endif

            <form method="POST" action="{{ route('unified.verification.resend') }}">
                @csrf
                <button type="submit"
                        class="w-full bg-yellow-400 hover:bg-yellow-500 text-black font-semibold py-3 px-6 rounded-xl transition-colors">
                    {{ __('auth.verify_email_resend') }}
                </button>
            </form>

            <form method="POST" action="{{ route('unified.logout') }}" class="mt-4">
                @csrf
                <button type="submit" class="text-sm text-gray-500 hover:text-gray-700 underline">
                    {{ __('auth.logout') }}
                </button>
            </form>
        </div>
    </div>
</div>
@endsection
