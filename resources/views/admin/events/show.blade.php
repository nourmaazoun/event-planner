@extends('layouts.app')

@section('title', $event->title)

@section('content')
<div class="container mx-auto px-4 py-8">
    <!-- Bouton retour -->
    <a href="{{ route('events.index') }}" class="inline-flex items-center text-blue-600 hover:text-blue-800 mb-6">
        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
        </svg>
        Retour aux événements
    </a>
    
    <div class="bg-white rounded-lg shadow-lg overflow-hidden">
        <!-- Image -->
        <div class="h-96 overflow-hidden">
            @if($event->image)
                <img src="{{ asset('storage/' . $event->image) }}" 
                     alt="{{ $event->title }}"
                     class="w-full h-full object-cover">
            @else
                <div class="w-full h-full bg-gradient-to-r from-blue-500 to-purple-600 flex items-center justify-center">
                    <span class="text-white text-3xl font-bold">{{ $event->category->name }}</span>
                </div>
            @endif
        </div>
        
        <div class="p-8">
            <!-- En-tête -->
            <div class="flex justify-between items-start mb-6">
                <div>
                    <span class="inline-block px-4 py-1 text-sm font-semibold text-blue-600 bg-blue-100 rounded-full mb-3">
                        {{ $event->category->name }}
                    </span>
                    <h1 class="text-3xl font-bold text-gray-900">{{ $event->title }}</h1>
                    <p class="text-gray-600 mt-2">Organisé par {{ $event->creator->name }}</p>
                </div>
                
                <!-- Prix -->
                <div class="text-right">
                    <div class="text-2xl font-bold {{ $event->is_free ? 'text-green-600' : 'text-gray-900' }}">
                        {{ $event->is_free ? 'Gratuit' : number_format($event->price, 2) . ' €' }}
                    </div>
                    <div class="text-sm text-gray-600 mt-1">
                        {{ $event->available_spaces }} places disponibles
                    </div>
                </div>
            </div>
            
            <!-- Infos principales -->
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-8">
                <!-- Date et heure -->
                <div class="flex items-start">
                    <div class="bg-blue-100 p-3 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Date et heure</h3>
                        <p class="text-gray-600">{{ $event->start_date->format('d/m/Y à H:i') }}</p>
                        <p class="text-sm text-gray-500">Fin : {{ $event->end_date->format('d/m/Y à H:i') }}</p>
                    </div>
                </div>
                
                <!-- Lieu -->
                <div class="flex items-start">
                    <div class="bg-green-100 p-3 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/>
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/>
                        </svg>
                    </div>
                    <div>
                        <h3 class="font-semibold text-gray-900">Lieu</h3>
                        <p class="text-gray-600">{{ $event->place }}</p>
                    </div>
                </div>
            </div>
            
            <!-- Description -->
            <div class="mb-8">
                <h2 class="text-xl font-bold text-gray-900 mb-4">Description</h2>
                <div class="prose max-w-none">
                    {!! nl2br(e($event->description)) !!}
                </div>
            </div>
            
            <!-- Inscription -->
            @auth
                <div class="border-t pt-8">
                    @if($isRegistered)
                        <div class="bg-green-50 border border-green-200 rounded-lg p-6">
                            <div class="flex items-center justify-between">
                                <div class="flex items-center">
                                    <svg class="w-8 h-8 text-green-600 mr-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                    </svg>
                                    <div>
                                        <h3 class="font-bold text-green-800">Vous êtes inscrit à cet événement</h3>
                                        <p class="text-green-600">Rendez-vous le {{ $event->start_date->format('d/m/Y') }} !</p>
                                    </div>
                                </div>
                                <form action="{{ route('events.unregister', $event) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="bg-red-100 text-red-700 px-6 py-2 rounded-lg hover:bg-red-200 transition"
                                            onclick="return confirm('Êtes-vous sûr de vouloir vous désinscrire ?')">
                                        Se désinscrire
                                    </button>
                                </form>
                            </div>
                        </div>
                    @else
                        @if($event->hasAvailableSpaces())
                            <form action="{{ route('events.register', $event) }}" method="POST">
                                @csrf
                                <button type="submit" 
                                        class="w-full md:w-auto bg-blue-600 text-white px-8 py-4 rounded-lg hover:bg-blue-700 transition text-lg font-semibold flex items-center justify-center">
                                    <svg class="w-6 h-6 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                    S'inscrire à cet événement
                                </button>
                                <p class="text-gray-600 text-sm mt-2 text-center">
                                    {{ $event->available_spaces }} places restantes
                                </p>
                            </form>
                        @else
                            <div class="bg-red-50 border border-red-200 rounded-lg p-6 text-center">
                                <svg class="w-12 h-12 text-red-600 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/>
                                </svg>
                                <h3 class="font-bold text-red-800 text-lg mb-2">Complet !</h3>
                                <p class="text-red-600">Toutes les places ont été réservées pour cet événement.</p>
                            </div>
                        @endif
                    @endif
                </div>
            @else
                <div class="border-t pt-8">
                    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
                        <h3 class="font-bold text-yellow-800 text-lg mb-2">Connectez-vous pour vous inscrire</h3>
                        <p class="text-yellow-600 mb-4">Vous devez être connecté pour vous inscrire à cet événement.</p>
                        <div class="flex space-x-4 justify-center">
                            <a href="{{ route('login') }}" 
                               class="bg-blue-600 text-white px-6 py-2 rounded-lg hover:bg-blue-700 transition">
                                Se connecter
                            </a>
                            <a href="{{ route('register') }}" 
                               class="bg-gray-200 text-gray-700 px-6 py-2 rounded-lg hover:bg-gray-300 transition">
                                Créer un compte
                            </a>
                        </div>
                    </div>
                </div>
            @endauth
        </div>
    </div>
</div>
@endsection