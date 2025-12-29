<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Event Planner') }} - @yield('title', 'Authentication')</title>

    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.bunny.net">
    <link href="https://fonts.bunny.net/css?family=figtree:400,500,600&display=swap" rel="stylesheet" />

    <!-- Scripts -->
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    
    <style>
        .auth-container {
            min-height: 100vh;
            display: flex;
        }
        .auth-image {
            flex: 1;
            background: linear-gradient(rgba(0, 0, 0, 0.7), rgba(0, 0, 0, 0.7)), 
                        url('{{ asset("storage/image.png") }}');
            background-size: cover;
            background-position: center;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            padding: 2rem;
        }
        .auth-form {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 2rem;
            background: #f8fafc;
        }
        .form-wrapper {
            width: 100%;
            max-width: 400px;
        }
        @media (max-width: 768px) {
            .auth-container {
                flex-direction: column;
            }
            .auth-image {
                min-height: 300px;
            }
        }
    </style>
</head>
<body>
    <div class="auth-container">
        <!-- Partie image -->
        <div class="auth-image">
            <div class="text-center">
                <h1 class="text-4xl font-bold mb-4">{{ config('app.name', 'Event Planner') }}</h1>
                <p class="text-xl opacity-90">Manage your events with ease</p>
                <p class="mt-4 opacity-80">
                    @if(request()->routeIs('login'))
                    Don't have an account? 
                    <a href="{{ route('register') }}" class="text-blue-300 hover:text-blue-200 underline">Sign up</a>
                    @else
                    Already have an account?
                    <a href="{{ route('login') }}" class="text-blue-300 hover:text-blue-200 underline">Sign in</a>
                    @endif
                </p>
            </div>
        </div>
        
        <!-- Partie formulaire -->
        <div class="auth-form">
            <div class="form-wrapper">
                <div class="mb-8 text-center">
                    <h2 class="text-2xl font-bold text-gray-800">
                        @yield('form-title', 'Welcome')
                    </h2>
                    <p class="text-gray-600 mt-2">@yield('form-subtitle', '')</p>
                </div>
                
                <!-- Messages de session -->
                @if(session('status'))
                    <div class="mb-4 font-medium text-sm text-green-600">
                        {{ session('status') }}
                    </div>
                @endif
                
                <!-- Erreurs de validation -->
                @if ($errors->any())
                    <div class="mb-4">
                        <div class="font-medium text-red-600">
                            {{ __('Whoops! Something went wrong.') }}
                        </div>
                        <ul class="mt-3 list-disc list-inside text-sm text-red-600">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif
                
                <!-- CONTENU DU FORMULAIRE -->
                @yield('content')
                
            </div>
        </div>
    </div>
</body>
</html>