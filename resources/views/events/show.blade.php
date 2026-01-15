<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ $event->title }} - Event Planner</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .line-clamp-2 {
            display: -webkit-box;
            -webkit-line-clamp: 2;
            -webkit-box-orient: vertical;
            overflow: hidden;
        }
        .popup-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            z-index: 1000;
        }
        .popup-content {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 2rem;
            border-radius: 1rem;
            width: 90%;
            max-width: 400px;
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
                            <i class="fas fa-ticket-alt mr-1"></i>My bookings
                        </a>
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-sign-out-alt mr-1"></i>Logout
                            </button>
                        </form>
                    @else
                        <a href="{{ route('login') }}" class="text-blue-600 hover:text-blue-800">
                            <i class="fas fa-sign-in-alt mr-1"></i>Login
                        </a>
                        <a href="{{ route('register') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                            <i class="fas fa-user-plus mr-1"></i>Register
                        </a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Popup de confirmation -->
    <div id="bookPopup" class="popup-overlay">
        <div class="popup-content">
            <h3 class="text-xl font-bold text-gray-800 mb-4">Confirm Booking</h3>
            <p class="text-gray-600 mb-6">Are you sure you want to book "<strong>{{ $event->title }}</strong>"?</p>
            
            <div class="flex justify-between space-x-4">
                <button type="button" onclick="closePopup()" 
                        class="flex-1 py-2 border border-gray-300 text-gray-700 rounded-lg hover:bg-gray-50 transition">
                    Cancel
                </button>
                
                <form id="bookForm" action="{{ route('events.register', $event) }}" method="POST" class="flex-1">
                    @csrf
                    <button type="submit" 
                            class="w-full py-2 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium">
                        Book Now
                    </button>
                </form>
            </div>
        </div>
    </div>

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

            <!-- Retour à la liste -->
            <div class="mb-6">
                <a href="{{ route('events.index') }}" class="text-blue-600 hover:text-blue-800 flex items-center">
                    <i class="fas fa-arrow-left mr-2"></i> Back to events
                </a>
            </div>

            <!-- Image avec titre par-dessus -->
            <div class="relative h-96 mb-8 rounded-xl overflow-hidden">
                @if($event->image)
                    <img src="{{ asset('storage/' . $event->image) }}" 
                         alt="{{ $event->title }}"
                         class="w-full h-full object-cover">
                @else
                    <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                        <i class="fas fa-calendar-alt text-gray-400 text-6xl"></i>
                    </div>
                @endif
                
                <!-- Overlay avec titre -->
                <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex flex-col justify-end p-8">
                    <h1 class="text-4xl font-bold text-white mb-2">{{ $event->title }}</h1>
                    <div class="flex items-center space-x-4 text-white/90">
                        <span class="flex items-center">
                            <i class="fas fa-tag mr-2"></i>
                            {{ $event->category->name ?? 'General' }}
                        </span>
                        <span>•</span>
                        <span class="flex items-center">
                            <i class="fas fa-users mr-2"></i>
                            {{ $event->capacity }} spots
                        </span>
                    </div>
                </div>
            </div>

            <!-- Contenu principal sans grille -->
            <div class="flex flex-col lg:flex-row gap-8">
                <!-- Description tout à gauche -->
                <div class="lg:w-2/3">
                    <h2 class="text-2xl font-bold text-gray-800 mb-4">DESCRIPTION</h2>
                    <div class="text-gray-700 whitespace-pre-line mb-8">{{ $event->description }}</div>
                    
                    <!-- Location & Price -->
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="p-3">
                          
                        </div>
                        
                        <div class="p-3">
                            <div class="flex items-center">
                              
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sidebar droite -->
                <div class="lg:w-1/3 space-y-6">
                    <!-- HOURS -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 mb-3">Hours</h3>
                        <div class="text-gray-700">
                            <div class="mb-2">
                                <strong>Start-Day:</strong> {{ $event->start_date->format('g:i A') }}  
                            </div>
                            <div>
                                <strong>End-Day:</strong> {{ $event->start_date->format('g:i A') }}  
                            </div>
                        </div>
                    </div>

                    <!-- CAPACITY -->
                    <div>
                        <h3 class="text-lg font-bold text-gray-800 mb-3">Capacity</h3>
                        <div class="text-gray-700">
                            Seats number: <strong>{{ $event->capacity }} persons</strong>
                        </div>
                    </div>

                    <!-- Ligne de séparation -->
                    <hr class="my-4">

                    <!-- Bouton Book Now -->
                    @if($event->available_spaces > 0)
                        @if($isRegistered)
                            <div class="text-center p-3">
                                <div class="inline-flex items-center px-4 py-2 bg-green-100 text-green-700 rounded">
                                    <i class="fas fa-check-circle mr-2"></i>
                                    Already Registered
                                </div>
                            </div>
                        @else
                            <div class="text-center">
                                <button type="button" onclick="openPopup()"
                                        class="w-full py-3 bg-black text-white rounded hover:bg-gray-800 transition font-bold">
                                    BOOK NOW
                                </button>
                            </div>
                        @endif
                    @else
                        <div class="text-center p-3">
                            <div class="inline-flex items-center px-4 py-2 bg-red-100 text-red-700 rounded">
                                <i class="fas fa-times-circle mr-2"></i>
                                SOLD OUT
                            </div>
                        </div>
                    @endif
                </div>
            </div>

            <!-- Other Events You Might Like -->
            @isset($otherEvents)
                @if($otherEvents->count() > 0)
                    <div class="mt-12 ml-0 lg:ml-8">
                        <h2 class="text-2xl font-bold text-gray-800 mb-4">Other events you may like</h2>
                        <hr class="mb-6">
                        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                            @foreach($otherEvents as $otherEvent)
                                <div class="bg-white rounded-lg shadow-md overflow-hidden hover:shadow-lg transition">
                                    <!-- Image -->
                                    <div class="h-48 overflow-hidden relative">
                                        @if($otherEvent->image)
                                            <img src="{{ asset('storage/' . $otherEvent->image) }}" 
                                                 alt="{{ $otherEvent->title }}"
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                                                <i class="fas fa-calendar-alt text-gray-400 text-3xl"></i>
                                            </div>
                                        @endif
                                        
                                        <!-- Badge catégorie -->
                                        <div class="absolute top-3 left-3">
                                            <span class="inline-block px-2 py-1 text-xs font-semibold text-white bg-black bg-opacity-50 rounded-full">
                                                {{ $otherEvent->category->name ?? 'General' }}
                                            </span>
                                        </div>
                                    </div>
                                    
                                    <!-- Contenu -->
                                    <div class="p-4">
                                        <h3 class="font-bold text-gray-800 mb-2">
                                            <a href="{{ route('events.show', $otherEvent) }}" class="hover:text-blue-600">
                                                {{ Str::limit($otherEvent->title, 40) }}
                                            </a>
                                        </h3>
                                        
                                        <!-- Date -->
                                        <div class="flex items-center text-gray-600 text-sm mb-3">
                                            <i class="fas fa-calendar-day text-blue-500 mr-2"></i>
                                            <span>{{ $otherEvent->start_date->format('M d, Y') }}</span>
                                        </div>
                                        
                                        <!-- Prix -->
                                        <div class="flex justify-between items-center">
                                            <span class="font-medium {{ $otherEvent->is_free ? 'text-green-600' : 'text-gray-900' }}">
                                                {{ $otherEvent->is_free ? 'Free' : number_format($otherEvent->price, 0) . ' TND' }}
                                            </span>
                                            <a href="{{ route('events.show', $otherEvent) }}" 
                                               class="text-sm bg-gray-100 text-gray-700 px-3 py-1 rounded hover:bg-gray-200">
                                                View
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            @endforeach
                        </div>
                    </div>
                @endif
            @endisset
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-gray-400">&copy; {{ date('Y') }} Event Planner. All rights reserved.</p>
        </div>
    </footer>

    <script>
        // Gestion de la popup
        function openPopup() {
            document.getElementById('bookPopup').style.display = 'block';
        }
        
        function closePopup() {
            document.getElementById('bookPopup').style.display = 'none';
        }
        
        // Fermer la popup en cliquant en dehors
        document.getElementById('bookPopup').addEventListener('click', function(e) {
            if (e.target === this) {
                closePopup();
            }
        });
        
        // Fermer avec Échap
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closePopup();
            }
        });
    </script>
</body>
</html>