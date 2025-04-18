@extends('layouts.app')
@section('content')
<div class="flex min-h-screen items-center justify-center bg-gray-50">
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8">
        <div class="flex flex-col items-center mb-6">
            <div class="w-12 h-12 rounded-full bg-violet-100 flex items-center justify-center mb-2">
                <svg width="28" height="28" fill="none" viewBox="0 0 24 24"><rect width="24" height="24" rx="6" fill="#6C3EF4"/><text x="50%" y="55%" text-anchor="middle" fill="#fff" font-size="14" font-weight="bold" dy=".3em">F4U</text></svg>
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Fundings4u</h1>
        </div>
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Sign in to your account</h2>
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('login') }}" class="space-y-4">
            @csrf
            <input type="email" name="email" placeholder="Your email" value="{{ old('email') }}" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400">
            <input type="password" name="password" placeholder="Password" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400">
            <div class="flex items-center justify-between">
                <div></div>
                <a href="#" class="text-xs text-violet-500 hover:underline">Forgot password?</a>
            </div>
            <button type="submit" class="w-full py-2 bg-violet-600 hover:bg-violet-700 text-white font-semibold rounded-lg transition">Sign in</button>
        </form>
        <button onclick="window.location.href='{{ route('register') }}'" class="w-full mt-3 py-2 bg-gray-100 text-gray-700 rounded-lg border border-gray-200 hover:bg-gray-200 transition">Create Account</button>
        <div class="mt-4 text-xs text-gray-400 text-center">
            <a href="#" class="hover:underline">Privacy policy</a>
        </div>
    </div>
</div>
@endsection
