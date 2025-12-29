<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Event Planner') }} - Gestion des événements</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
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
                        @if(auth()->user()->role === 'admin')
                            <a href="{{ route('admin.dashboard') }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-cog mr-1"></i>Dashboard
                            </a>
                            <a href="{{ route('admin.events.create') }}" class="bg-blue-600 text-white px-4 py-2 rounded hover:bg-blue-700">
                                <i class="fas fa-plus mr-1"></i>Nouvel événement
                            </a>
                            <a href="{{ route('admin.categories.index') }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-tags mr-1"></i>Catégories
                            </a>
                        @endif
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-sign-out-alt mr-1"></i>Déconnexion
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="py-8">
        <div class="max-w-7xl mx-auto px-4">
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Gestion des événements</h1>
                    <p class="text-gray-600 mt-2">Créez, modifiez et gérez vos événements</p>
                </div>
                <a href="{{ route('admin.events.create') }}" 
                   class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Nouvel événement
                </a>
            </div>

            @if(session('success'))
                <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Tableau des événements -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Événement
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Date
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Lieu
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Places
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Statut
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @foreach($events as $event)
                                <tr>
                                    <td class="px-6 py-4">
                                        <div class="flex items-center">
                                            @if($event->image)
                                                <img class="h-10 w-10 rounded-lg object-cover mr-3" 
                                                     src="{{ asset('storage/' . $event->image) }}" 
                                                     alt="{{ $event->title }}">
                                            @else
                                                <div class="h-10 w-10 rounded-lg bg-blue-100 flex items-center justify-center mr-3">
                                                    <i class="fas fa-calendar text-blue-600"></i>
                                                </div>
                                            @endif
                                            <div>
                                                <div class="font-medium text-gray-900">{{ $event->title }}</div>
                                                <div class="text-sm text-gray-500">{{ $event->category->name }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ date('d/m/Y', strtotime($event->start_date)) }}</div>
                                        <div class="text-sm text-gray-500">{{ date('H:i', strtotime($event->start_date)) }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">{{ $event->place }}</div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            <span class="font-medium">{{ $event->available_spaces }}</span>
                                            <span class="text-gray-500">/{{ $event->capacity }}</span>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        @if($event->status == 'active')
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-green-100 text-green-800">
                                                Actif
                                            </span>
                                        @else
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-gray-100 text-gray-800">
                                                Archivé
                                            </span>
                                        @endif
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                        <a href="{{ route('admin.events.edit', $event) }}" 
                                           class="text-blue-600 hover:text-blue-900 mr-4">
                                            <i class="fas fa-edit mr-1"></i>Modifier
                                        </a>
                                        <form action="{{ route('admin.events.destroy', $event) }}" 
                                              method="POST" 
                                              class="inline"
                                              onsubmit="return confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" class="text-red-600 hover:text-red-900">
                                                <i class="fas fa-trash mr-1"></i>Supprimer
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                @if($events->hasPages())
                    <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                        {{ $events->links() }}
                    </div>
                @endif
            </div>
            
            <!-- Si aucun événement -->
            @if($events->isEmpty())
                <div class="text-center py-16 bg-white rounded-lg shadow">
                    <i class="fas fa-calendar-times text-6xl text-gray-400 mb-6"></i>
                    <h3 class="text-2xl font-semibold text-gray-700 mb-3">Aucun événement créé</h3>
                    <p class="text-gray-600 max-w-md mx-auto mb-8">
                        Commencez par créer votre premier événement
                    </p>
                    <a href="{{ route('admin.events.create') }}" 
                       class="bg-blue-600 text-white px-6 py-3 rounded-lg hover:bg-blue-700 transition inline-flex items-center">
                        <i class="fas fa-plus mr-2"></i>
                        Créer un événement
                    </a>
                </div>
            @endif
        </div>
    </main>

    <script>
        // Script pour confirmer la suppression
        function confirmDelete(eventId) {
            if (confirm('Êtes-vous sûr de vouloir supprimer cet événement ?')) {
                document.getElementById('delete-form-' + eventId).submit();
            }
        }
    </script>
</body>
</html>