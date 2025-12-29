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

            <!-- Section principale -->
            <div class="bg-white rounded-xl shadow-lg overflow-hidden">
                <!-- Image avec titre par-dessus -->
                <div class="relative h-96">
                    @if($event->image)
                        <img src="{{ asset('storage/' . $event->image) }}" 
                             alt="{{ $event->title }}"
                             class="w-full h-full object-cover">
                    @else
                        <div class="w-full h-full bg-gray-200 flex items-center justify-center">
                            <i class="fas fa-calendar-alt text-gray-400 text-6xl"></i>
                        </div>
                    @endif
                    
                    <!-- Overlay avec titre et description courte -->
                    <div class="absolute inset-0 bg-gradient-to-t from-black/70 to-transparent flex flex-col justify-end p-8">
                        <h1 class="text-4xl font-bold text-white mb-3">{{ $event->title }}</h1>
                        <p class="text-lg text-white/90 max-w-3xl line-clamp-2">
                            {{ Str::limit($event->description, 150) }}
                        </p>
                        
                        <!-- Bouton Book Now -->
                        <div class="mt-6">
                            @if($event->available_spaces > 0)
                                @if($isRegistered)
                                    <div class="inline-flex items-center px-6 py-3 bg-green-600 text-white rounded-lg">
                                        <i class="fas fa-check-circle mr-2"></i>
                                        Already Registered
                                    </div>
                                @else
                                    <form action="{{ route('events.register', $event) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="px-8 py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-bold text-lg shadow-lg">
                                            <i class="fas fa-ticket-alt mr-2"></i>
                                            BOOK NOW
                                        </button>
                                    </form>
                                @endif
                            @else
                                <div class="inline-flex items-center px-6 py-3 bg-red-600 text-white rounded-lg">
                                    <i class="fas fa-times-circle mr-2"></i>
                                    SOLD OUT
                                </div>
                            @endif
                        </div>
                    </div>
                </div>

                <!-- Contenu principal -->
                <div class="p-8">
                    <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                        <!-- Description (2/3) -->
                        <div class="lg:col-span-2">
                            <h2 class="text-2xl font-bold text-gray-800 mb-6 pb-3 border-b">Description</h2>
                            <div class="prose max-w-none">
                                <p class="text-gray-700 whitespace-pre-line">{{ $event->description }}</p>
                            </div>
                            
                            <!-- Informations supplémentaires -->
                            <div class="mt-8 grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Location</h3>
                                    <div class="flex items-start">
                                        <i class="fas fa-map-marker-alt text-red-500 mt-1 mr-3"></i>
                                        <p class="text-gray-700">{{ $event->place }}</p>
                                    </div>
                                </div>
                                
                                <div>
                                    <h3 class="text-lg font-semibold text-gray-800 mb-3">Price</h3>
                                    <div class="flex items-center">
                                        <i class="fas fa-tag text-green-500 mr-3"></i>
                                        <span class="text-2xl font-bold {{ $event->is_free ? 'text-green-600' : 'text-gray-800' }}">
                                            {{ $event->is_free ? 'FREE' : number_format($event->price, 0) . ' TND' }}
                                        </span>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Sidebar avec date et capacité (1/3) -->
                        <div class="lg:col-span-1">
                            <div class="bg-gray-50 rounded-xl p-6 border border-gray-200">
                                <!-- Date en GRAND -->
                                <div class="mb-8">
                                    <h3 class="text-lg font-semibold text-gray-600 mb-3">Event Date</h3>
                                    <div class="text-center">
                                        <div class="text-5xl font-bold text-purple-600 mb-2">
                                            {{ $event->start_date->format('d') }}
                                        </div>
                                        <div class="text-2xl font-bold text-gray-800 mb-1">
                                            {{ $event->start_date->format('F') }}
                                        </div>
                                        <div class="text-xl text-gray-600">
                                            {{ $event->start_date->format('Y') }}
                                        </div>
                                        <div class="mt-3 text-lg text-gray-700">
                                            <i class="fas fa-clock mr-2"></i>
                                            {{ $event->start_date->format('g:i A') }}
                                        </div>
                                    </div>
                                </div>

                                <!-- Capacité en GRAND -->
                                <div class="mb-8">
                                    <h3 class="text-lg font-semibold text-gray-600 mb-3">Capacity</h3>
                                    <div class="text-center">
                                        <div class="text-5xl font-bold text-blue-600 mb-2">
                                            {{ $event->capacity }}
                                        </div>
                                        <div class="text-lg text-gray-700">
                                            Total Spots
                                        </div>
                                        <div class="mt-3">
                                            <div class="text-sm text-gray-600 mb-1">Available Spots</div>
                                            <div class="text-3xl font-bold {{ $event->available_spaces > 0 ? 'text-green-600' : 'text-red-600' }}">
                                                {{ $event->available_spaces }}
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Barre de progression -->
                                <div class="mb-6">
                                    <div class="flex justify-between text-sm text-gray-600 mb-2">
                                        <span>Registration</span>
                                        <span>{{ $event->capacity - $event->available_spaces }} / {{ $event->capacity }}</span>
                                    </div>
                                    <div class="h-2 bg-gray-200 rounded-full overflow-hidden">
                                        <div class="h-full bg-green-500" 
                                             style="width: {{ $event->capacity > 0 ? (($event->capacity - $event->available_spaces) / $event->capacity) * 100 : 0 }}%">
                                        </div>
                                    </div>
                                </div>

                                <!-- Informations rapides -->
                                <div class="space-y-4">
                                    <div class="flex items-center">
                                        <i class="fas fa-calendar-day text-purple-500 w-5 mr-3"></i>
                                        <span class="text-gray-700">
                                            Duration: {{ $event->start_date->diffInHours($event->end_date) }} hours
                                        </span>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <i class="fas fa-users text-blue-500 w-5 mr-3"></i>
                                        <span class="text-gray-700">Category: {{ $event->category->name ?? 'General' }}</span>
                                    </div>
                                    
                                    <div class="flex items-center">
                                        <i class="fas fa-user-tie text-gray-500 w-5 mr-3"></i>
                                        <span class="text-gray-700">Organizer: {{ $event->creator->name ?? 'Admin' }}</span>
                                    </div>
                                </div>

                                <!-- Bouton d'inscription (mobile) -->
                                @if($event->available_spaces > 0 && !$isRegistered)
                                <div class="mt-8 lg:hidden">
                                    <form action="{{ route('events.register', $event) }}" method="POST">
                                        @csrf
                                        <button type="submit" 
                                                class="w-full py-3 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-bold text-lg">
                                            <i class="fas fa-ticket-alt mr-2"></i>
                                            BOOK NOW
                                        </button>
                                    </form>
                                </div>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </main>

    <footer class="bg-gray-800 text-white py-8 mt-12">
        <div class="max-w-7xl mx-auto px-4 text-center">
            <p class="text-gray-400">&copy; {{ date('Y') }} Event Planner. All rights reserved.</p>
        </div>
    </footer>
</body>
</html>