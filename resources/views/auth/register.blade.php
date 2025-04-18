@extends('layouts.app')
@section('content')
<div class="auth-container">
    <h2>Create Account</h2>
    <form method="POST" action="{{ route('register') }}">
        @csrf
        <input type="text" name="name" placeholder="Your name" value="{{ old('name') }}" required>
        <input type="email" name="email" placeholder="Your email" value="{{ old('email') }}" required>
        <input type="password" name="password" placeholder="Password" required>
        <input type="password" name="password_confirmation" placeholder="Confirm Password" required>
        <input type="text" name="account_login" placeholder="MetaTrader Account Login" value="{{ old('account_login') }}" required>
        <button type="submit">Create Account</button>
    </form>
    <p>Already have an account? <a href="{{ route('login') }}">Sign in</a></p>
</div>
@endsection
