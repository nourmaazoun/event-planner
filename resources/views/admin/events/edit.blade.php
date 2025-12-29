<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Event Planner') }} - Créer un événement</title>
    
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body class="bg-gray-50">
    <!-- Navbar -->
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-6">
            <div class="flex justify-between items-center h-16">
                <!-- Logo à gauche - "Event" en noir, "Planner" en mauve -->
                <div class="flex items-center">
                    <span class="text-xl font-bold text-black">Event</span>
                    <span class="text-xl font-bold text-purple-600 ml-1">Planner</span>
                </div>
                
                <!-- Menu à droite -->
                <div class="flex items-center space-x-6">
                    <!-- Page actuelle -->
                    <span class="text-gray-700 font-medium">
                        Edit event
                    </span>
                    
                    <!-- Lien vers catégories -->
                    <a href="{{ route('admin.categories.index') }}" class="text-gray-600 hover:text-blue-600 transition">
                        Categories
                    </a>
                    
                    <!-- Déconnexion -->
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button type="submit" class="text-gray-600 hover:text-red-600 transition">
                            Logout
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="py-8">
        <div class="max-w-4xl mx-auto px-6">
            <!-- Titre principal au centre -->
            <div class="text-center mb-10">
                <h1 class="text-3xl font-bold text-gray-800 mb-2">Edit event</h1>
            </div>

            <!-- Formulaire -->
            <div class="bg-white rounded-xl shadow-sm border border-gray-200 p-8">
                <form action="{{ route('admin.events.store') }}" method="POST" enctype="multipart/form-data" class="space-y-8">
                    @csrf
                    
                    <!-- SECTION 1: Event Title & Category -->
                    <div class="space-y-6">
                        <!-- Event Title -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Event Title
                            </label>
                            <input type="text" 
                                   name="title" 
                                   value="{{ old('title') }}"
                                   placeholder="Title"
                                   class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition"
                                   required>
                            @error('title')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                        
                        <!-- Category -->
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-2">
                                Category
                            </label>
                            <select name="category_id" 
                                    class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                    required>
                                <option value="">Select category</option>
                                @foreach($categories as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                @endforeach
                            </select>
                            @error('category_id')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                    
                    <!-- SECTION 2: Dates -->
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Start Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                   Start Date
                                </label>
                                <input type="datetime-local" 
                                       name="start_date" 
                                       value="{{ old('start_date') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       required>
                                @error('start_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- End Date -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    End Date
                                </label>
                                <input type="datetime-local" 
                                       name="end_date" 
                                       value="{{ old('end_date') }}"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       required>
                                @error('end_date')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- SECTION 3: Place & Capacity -->
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <!-- Place -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Place
                                </label>
                                <input type="text" 
                                       name="place" 
                                       value="{{ old('place') }}"
                                       placeholder="Location"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       required>
                                @error('place')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Capacity -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Capacity
                                </label>
                                <input type="number" 
                                       name="capacity" 
                                       value="{{ old('capacity', 50) }}"
                                       min="1"
                                       placeholder="Capacity"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500"
                                       required>
                                @error('capacity')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- SECTION 4: Price & Amount -->
                    <div class="space-y-6">
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <!-- Price -->
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    Price
                                </label>
                                <input type="number" 
                                       name="price" 
                                       value="{{ old('price', 0) }}"
                                       min="0"
                                       step="0.01"
                                       placeholder="Free Access"
                                       class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                @error('price')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                            
                            <!-- Amount -->
                           <!-- Capacity (affiché comme "Amount") -->

                        </div>
                    </div>
                    
                    <!-- SECTION 5: Event Description (Titre principal) -->
                    <div class="space-y-6">
                        <!-- Titre principal "Event Description" -->
                       <h2 class="text-lg font-bold text-gray-800 text-center mb-4">Event Description</h2>
                        <!-- Sous-section 1: Event Image -->
                        <div class="space-y-4">
                            <h3 class="text-md font-medium text-gray-700">Event Image</h3>
                            <div class="border-2 border-dashed border-gray-300 rounded-xl p-8 text-center hover:border-blue-400 transition">
                                <div id="image-preview" class="mb-4">
                                  
                                </div>
                                
                                <img id="preview" class="hidden max-w-full mx-auto rounded-lg max-h-64 object-cover mb-4">
                                
                                <input type="file" 
                                       id="image" 
                                       name="image" 
                                       accept="image/*"
                                       class="hidden"
                                       onchange="previewImage(event)">
                                
                                <button type="button" 
                                        onclick="document.getElementById('image').click()"
                                        class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition font-medium">
                                    Choose an image
                                </button>
                                
                                @error('image')
                                    <p class="mt-3 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                        
                        <!-- Sous-section 2: Event Description (textarea) -->
                        <div class="space-y-4">
                            <h3 class="text-md font-medium text-gray-700">Event Description</h3>
                            <div>
                                <textarea name="description" 
                                          rows="6"
                                          placeholder="Describe your event in detail"
                                          class="w-full px-4 py-3 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500 resize-none"
                                          required>{{ old('description') }}</textarea>
                                @error('description')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>
                        </div>
                    </div>
                    
                    <!-- Bouton Submit - PLEINE LARGEUR en MAUVE -->
                    <div class="pt-6">
                        <button type="submit" 
                                class="w-full py-4 bg-purple-600 text-white rounded-lg hover:bg-purple-700 transition font-medium text-lg">
                            Create Event
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <script>
        // Prévisualisation de l'image
        function previewImage(event) {
            const preview = document.getElementById('preview');
            const imagePreview = document.getElementById('image-preview');
            const file = event.target.files[0];
            
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    preview.src = e.target.result;
                    preview.classList.remove('hidden');
                    imagePreview.classList.add('hidden');
                }
                reader.readAsDataURL(file);
            }
        }
        
        // Zone de drop pour l'image
        document.addEventListener('DOMContentLoaded', function() {
            const dropZone = document.querySelector('.border-dashed');
            const fileInput = document.getElementById('image');
            
            if (dropZone) {
                dropZone.addEventListener('dragover', (e) => {
                    e.preventDefault();
                    dropZone.classList.add('border-blue-400', 'bg-blue-50');
                });
                
                dropZone.addEventListener('dragleave', () => {
                    dropZone.classList.remove('border-blue-400', 'bg-blue-50');
                });
                
                dropZone.addEventListener('drop', (e) => {
                    e.preventDefault();
                    dropZone.classList.remove('border-blue-400', 'bg-blue-50');
                    
                    if (e.dataTransfer.files.length) {
                        fileInput.files = e.dataTransfer.files;
                        previewImage({ target: fileInput });
                    }
                });
                
                dropZone.addEventListener('click', () => {
                    fileInput.click();
                });
            }
        });
    </script>
</body>
</html>