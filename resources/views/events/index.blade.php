<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Event Planner') }} - Événements</title>
    
    <!-- Tailwind CSS via CDN (temporaire) -->
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .event-card:hover {
            transform: translateY(-5px);
            transition: transform 0.3s ease;
        }
        .pagination .page-item.active .page-link {
            background-color: #3b82f6;
            border-color: #3b82f6;
        }
    </style>
</head>
<body class="bg-gray-50">
    
    <nav class="bg-white shadow-lg">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <a href="{{ route('events.index') }}" class="text-xl font-bold text-blue-600">
                        <i class="fas fa-calendar-alt mr-2"></i>Event Planner
                    </a>
                </div>
                
                <div class="flex items-center space-x-4">
                    @auth
                        <span class="text-gray-700">{{ auth()->user()->name }}</span>
                        
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.events.index') }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-cog mr-1"></i>Admin
                            </a>
                        @endif
                        
                        <a href="{{ route('profile.registrations') }}" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-ticket-alt mr-1"></i>Mes inscriptions
                        </a>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-sign-out-alt mr-1"></i>Déconnexion
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-sign-in-alt mr-1"></i>Connexion
                        </a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            <i class="fas fa-user-plus mr-1"></i>Inscription
                        </a>
                    @endauth
                </div>
            </div>
        </div>
</nav>

    <!-- Contenu principal -->
    <main class="py-8">
        <div class="max-w-7xl mx-auto px-4">
            <!-- Messages Flash -->
            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-4">
                    <i class="fas fa-check-circle mr-2"></i>{{ session('success') }}
                </div>
            @endif
            
            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-4">
                    <i class="fas fa-exclamation-circle mr-2"></i>{{ session('error') }}
                </div>
            @endif

            <!-- En-tête -->
            <div class="flex flex-col md:flex-row justify-between items-start md:items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Événements à venir</h1>
                    <p class="text-gray-600 mt-2">Découvrez et participez aux prochains événements</p>
                </div>
                
                @auth
                    <div class="mt-4 md:mt-0">
                        <a href="{{ route('profile.registrations') }}" 
                           class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition flex items-center">
                            <i class="fas fa-ticket-alt mr-2"></i>
                            Mes inscriptions
                        </a>
                    </div>
                @endauth
            </div>
            
            <!-- Filtres et recherche -->
            <div class="bg-white rounded-lg shadow p-6 mb-8">
                <form action="{{ route('events.index') }}" method="GET" class="space-y-4 md:space-y-0 md:flex md:space-x-4">
                    <!-- Recherche -->
                    <div class="flex-1">
                        <div class="relative">
                            <i class="fas fa-search absolute left-3 top-3 text-gray-400"></i>
                            <input type="text" 
                                   name="search" 
                                   placeholder="Rechercher un événement..."
                                   value="{{ request('search') }}"
                                   class="w-full pl-10 pr-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        </div>
                    </div>
                    
                    <!-- Catégorie -->
                    <div class="w-full md:w-48">
                        <select name="category" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="">Toutes catégories</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ request('category') == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    
                    <!-- Tri -->
                    <div class="w-full md:w-48">
                        <select name="sort" 
                                class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                            <option value="start_date" {{ request('sort') == 'start_date' ? 'selected' : '' }}>Date ↑</option>
                            <option value="start_date_desc" {{ request('sort') == 'start_date_desc' ? 'selected' : '' }}>Date ↓</option>
                            <option value="price" {{ request('sort') == 'price' ? 'selected' : '' }}>Prix ↑</option>
                            <option value="price_desc" {{ request('sort') == 'price_desc' ? 'selected' : '' }}>Prix ↓</option>
                        </select>
                    </div>
                    
                    <!-- Boutons -->
                    <div class="flex space-x-2">
                        <button type="submit" 
                                class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition flex items-center">
                            <i class="fas fa-filter mr-2"></i>
                            Filtrer
                        </button>
                        <a href="{{ route('events.index') }}" 
                           class="bg-gray-200 text-gray-700 px-6 py-3 rounded-lg hover:bg-gray-300 transition flex items-center">
                            <i class="fas fa-redo mr-2"></i>
                            Réinitialiser
                        </a>
                    </div>
                </form>
            </div>
            
            <!-- Bannière image avant la liste -->
            <div class="mb-8 rounded-lg overflow-hidden shadow-lg relative">
                <img src="{{ asset('storage/evenement-tunisie-1024x683.jpg') }}" 
                     alt="Événements en Tunisie" 
                     class="w-full h-64 md:h-80 object-cover">
                <!-- Texte directement sur l'image -->
                <div class="absolute inset-0 flex flex-col justify-end p-6">
                    <h2 class="text-2xl md:text-3xl font-bold text-white mb-2 drop-shadow-lg">Découvrez les meilleurs événements</h2>
                    <p class="text-lg text-white opacity-90 drop-shadow-lg">Participez à des événements uniques partout en Tunisie</p>
                </div>
            </div>
            
            <!-- Liste des événements -->
                       <!-- Liste des événements -->
            @if($events->count() > 0)
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                    @foreach($events as $event)
                        <div class="bg-white rounded-lg shadow-md overflow-hidden event-card flex flex-col h-full">
                            <!-- Image -->
                            <div class="h-48 overflow-hidden relative flex-shrink-0">
                                @if($event->image)
                                    <img src="{{ asset('storage/' . $event->image) }}" 
                                         alt="{{ $event->title }}"
                                         class="w-full h-full object-cover">
                                @else
                                    <div class="w-full h-full bg-gray-100 flex items-center justify-center">
                                        <i class="fas fa-calendar-alt text-gray-400 text-4xl"></i>
                                    </div>
                                @endif
                                
                                <!-- Badge catégorie -->
                                <div class="absolute top-3 left-3">
                                    <span class="inline-block px-3 py-1 text-xs font-semibold text-white bg-black bg-opacity-60 rounded-full">
                                        {{ $event->category->name ?? 'Sans catégorie' }}
                                    </span>
                                </div>
                            </div>
                            
                            <!-- Contenu -->
                            <div class="p-4 flex flex-col flex-grow">
                                <!-- Titre -->
                                <h3 class="text-lg font-bold text-gray-800 mb-2">
                                    <a href="{{ route('events.show', $event) }}" class="hover:text-blue-600 transition">
                                        {{ Str::limit($event->title, 40) }}
                                    </a>
                                </h3>
                                
                                <!-- Description courte -->
                                <p class="text-sm text-gray-600 mb-4 line-clamp-2 flex-grow">
                                    {{ Str::limit($event->description, 80) }}
                                </p>
                                
                                <!-- Infos -->
                                <div class="mb-4">
    <div class="flex items-center text-gray-600 text-sm space-x-4">
        <!-- Date -->
        <div class="flex items-center">
            <i class="fas fa-calendar text-blue-500 mr-2"></i>
            <span>{{ $event->start_date->format('M d, Y') }}</span>
        </div>
        
        <!-- Temps -->
        <div class="flex items-center">
            <i class="fas fa-clock text-blue-500 mr-2"></i>
            <span>{{ $event->start_date->format('H:i') }}</span>
        </div>
    </div>
</div>
                                
                                <!-- Bouton -->
                                <div class="mt-auto pt-3 border-t">
                                    <a href="{{ route('events.show', $event) }}" 
                                       class="block w-full text-center bg-purple-600 text-white px-4 py-2 rounded hover:bg-purple-700 transition text-sm font-medium">
                                        View Details
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
                
                <!-- Pagination -->
                <div class="mt-8">
                    {{ $events->links() }}
                </div>
            @else
                <!-- Aucun événement -->
                <div class="text-center py-16 bg-white rounded-lg shadow">
                    <i class="fas fa-calendar-times text-6xl text-gray-400 mb-6"></i>
                    <h3 class="text-2xl font-semibold text-gray-700 mb-3">Aucun événement trouvé</h3>
                    <p class="text-gray-600 max-w-md mx-auto mb-8">
                        @if(request()->has('search') || request()->has('category'))
                            Aucun événement ne correspond à vos critères de recherche.
                        @else
                            Aucun événement n'est actuellement programmé.
                        @endif
                    </p>
                    @if(request()->has('search') || request()->has('category'))
                        <a href="{{ route('events.index') }}" 
                           class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition inline-flex items-center">
                            <i class="fas fa-redo mr-2"></i>
                            Voir tous les événements
                        </a>
                    @endif
                    
                    @if(auth()->check() && auth()->user()->role === 'admin')
                        <div class="mt-6">
                            <p class="text-gray-600 mb-4">Vous êtes administrateur, vous pouvez créer des événements.</p>
                            <a href="{{ route('admin.events.create') }}" 
                               class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition inline-flex items-center">
                                <i class="fas fa-plus mr-2"></i>
                                Créer un événement
                            </a>
                        </div>
                    @endif
                </div>
            @endif
        </div> <!-- Fermeture de la div max-w-7xl -->
    </main>

    <!-- Footer -->
    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex flex-col md:flex-row justify-between items-center">
                <div class="mb-4 md:mb-0">
                    <h3 class="text-xl font-bold">
                        <i class="fas fa-calendar-alt mr-2"></i>Event Planner
                    </h3>
                    <p class="text-gray-400 mt-2">Gestion d'événements simplifiée</p>
                </div>
                <div class="text-gray-400">
                    <p>&copy; {{ date('Y') }} Event Planner. Tous droits réservés.</p>
                </div>
            </div>
        </div>
    </footer>

    <!-- Scripts -->
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const cards = document.querySelectorAll('.event-card');
            cards.forEach(card => {
                card.addEventListener('mouseenter', () => {
                    card.style.transform = 'translateY(-5px)';
                    card.style.boxShadow = '0 10px 25px rgba(0, 0, 0, 0.1)';
                });
                card.addEventListener('mouseleave', () => {
                    card.style.transform = '';
                    card.style.boxShadow = '';
                });
            });
        });
    </script>
</body>
</html>