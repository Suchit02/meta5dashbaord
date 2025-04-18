@extends('layouts.app')
@section('content')
<div class="flex min-h-screen items-center justify-center bg-gray-50">
    <div class="w-full max-w-md bg-white rounded-xl shadow-lg p-8">
        <div class="flex flex-col items-center mb-6">
            <div class="w-12 h-12 rounded-full bg-violet-100 flex items-center justify-center mb-2 overflow-hidden">
                <img src="/asset/logo/logo.png" alt="Logo" class="object-contain w-10 h-10">
            </div>
            <h1 class="text-2xl font-bold text-gray-900">Fundings4u</h1>
        </div>
        <h2 class="text-lg font-semibold text-gray-700 mb-4">Create Account</h2>
        @if($errors->any())
            <div class="mb-4 p-3 bg-red-100 border border-red-300 text-red-700 rounded">
                @foreach($errors->all() as $error)
                    <div>{{ $error }}</div>
                @endforeach
            </div>
        @endif
        <form method="POST" action="{{ route('register') }}" class="space-y-4">
            @csrf
            <input type="text" name="name" placeholder="Your name" value="{{ old('name') }}" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400">
            <input type="email" name="email" placeholder="Your email" value="{{ old('email') }}" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400">
            <input type="password" name="password" placeholder="Password" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400">
            <input type="password" name="password_confirmation" placeholder="Confirm Password" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400">
            <input type="text" name="account_login" placeholder="MetaTrader Account Login" value="{{ old('account_login') }}" required class="w-full px-4 py-2 border rounded-lg focus:outline-none focus:ring-2 focus:ring-violet-400">
            <button type="submit" class="w-full py-2 bg-violet-600 hover:bg-violet-700 text-white font-semibold rounded-lg transition">Create Account</button>
        </form>
        <button onclick="window.location.href='{{ route('login') }}'" class="w-full mt-3 py-2 bg-gray-100 text-gray-700 rounded-lg border border-gray-200 hover:bg-gray-200 transition">Sign in</button>
    </div>
</div>
@endsection
