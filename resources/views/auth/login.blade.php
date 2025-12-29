{{-- NE PAS utiliser @extends --}}
{{-- DIRECTEMENT le code HTML du formulaire --}}

@section('title', 'Sign In')
@section('form-title', 'Sign In')
@section('form-subtitle', 'Sign in to your account')

@section('content')
<form method="POST" action="{{ route('login') }}">
    @csrf

    <!-- Email -->
    <div class="mb-4">
        <label for="email" class="block text-sm font-medium text-gray-700">Your Email</label>
        <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus 
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('email')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Password -->
    <div class="mb-6">
        <label for="password" class="block text-sm font-medium text-gray-700">Password</label>
        <input id="password" type="password" name="password" required 
               class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-blue-500 focus:ring-blue-500">
        @error('password')
            <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
        @enderror
    </div>

    <!-- Remember Me -->
    <div class="flex items-center justify-between mb-6">
        <label for="remember_me" class="flex items-center">
            <input id="remember_me" type="checkbox" name="remember" 
                   class="rounded border-gray-300 text-blue-600 shadow-sm focus:ring-blue-500">
            <span class="ml-2 text-sm text-gray-600">Remember me</span>
        </label>

        @if (Route::has('password.request'))
            <a href="{{ route('password.request') }}" class="text-sm text-blue-600 hover:text-blue-500">
                Forgot your password?
            </a>
        @endif
    </div>

    <!-- Submit Button -->
    <button type="submit" 
            class="w-full bg-blue-600 text-white py-2 px-4 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition">
        Sign In
    </button>

    <!-- Register Link -->
    <div class="mt-6 text-center">
        <p class="text-sm text-gray-600">
            Don't have an account?
            <a href="{{ route('register') }}" class="text-blue-600 hover:text-blue-500 font-medium">
                Create one
            </a>
        </p>
    </div>
</form>
@endsection

{{-- INCLURE le layout à la fin --}}
@extends('layouts.auth-layout')