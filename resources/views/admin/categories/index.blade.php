<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>{{ config('app.name', 'Event Planner') }} - Category Management</title>
    
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
                            <a href="{{ route('admin.events.index') }}" class="text-blue-600 hover:text-blue-800">
                                <i class="fas fa-calendar mr-1"></i>Events
                            </a>
                        @endif
                        
                        <form method="POST" action="{{ route('logout') }}">
                            @csrf
                            <button type="submit" class="text-red-600 hover:text-red-800">
                                <i class="fas fa-sign-out-alt mr-1"></i>Logout
                            </button>
                        </form>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <main class="py-8">
        <div class="max-w-4xl mx-auto px-4">
            <!-- Header with purple "List Category" title and create button -->
            <div class="flex justify-between items-center mb-8">
                <div>
                    <h1 class="text-3xl font-bold text-purple-600">List Category</h1>
                </div>
                <button id="openCategoryModal" 
                       class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition flex items-center">
                    <i class="fas fa-plus mr-2"></i>
                    Create Category
                </button>
            </div>

            @if(session('success'))
                <div id="successMessage" class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded mb-6">
                    {{ session('error') }}
                </div>
            @endif

            <!-- Categories Table -->
            <div class="bg-white rounded-lg shadow overflow-hidden">
                @if($categories->count() > 0)
                    <div class="overflow-x-auto">
                        <table class="min-w-full divide-y divide-gray-200">
                            <thead class="bg-gray-50">
                                <tr>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Category Name
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Events
                                    </th>
                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                        Actions
                                    </th>
                                </tr>
                            </thead>
                            <tbody id="categoriesTableBody" class="bg-white divide-y divide-gray-200">
                                @foreach($categories as $category)
                                    <tr id="category-{{ $category->id }}">
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <div class="text-sm font-medium text-gray-900">{{ $category->name }}</div>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap">
                                            <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                                                {{ $category->events_count ?? $category->events()->count() }}
                                            </span>
                                        </td>
                                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                            <button onclick="editCategory({{ $category->id }}, '{{ $category->name }}')"
                                               class="text-blue-600 hover:text-blue-900 mr-4">
                                                <i class="fas fa-edit mr-1"></i>Edit
                                            </button>
                                            <form action="{{ route('admin.categories.destroy', $category) }}" 
                                                  method="POST" 
                                                  class="inline"
                                                  onsubmit="return confirm('Are you sure you want to delete this category?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-600 hover:text-red-900">
                                                    <i class="fas fa-trash mr-1"></i>Delete
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    
                    <!-- Pagination -->
                    @if($categories->hasPages())
                        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
                            {{ $categories->links() }}
                        </div>
                    @endif
                @else
                    <!-- If no categories -->
                    <div class="text-center py-16">
                        <i class="fas fa-tags text-6xl text-gray-400 mb-6"></i>
                        <h3 class="text-2xl font-semibold text-gray-700 mb-3">No categories created</h3>
                        <p class="text-gray-600 max-w-md mx-auto mb-8">
                            Start by creating your first category
                        </p>
                        <button id="openCategoryModalEmpty" 
                               class="bg-purple-600 text-white px-6 py-3 rounded-lg hover:bg-purple-700 transition inline-flex items-center">
                            <i class="fas fa-plus mr-2"></i>
                            Create Category
                        </button>
                    </div>
                @endif
            </div>
        </div>
    </main>

    <!-- Modal for creating/editing category -->
    <div id="categoryModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 overflow-y-auto h-full w-full hidden z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium leading-6 text-gray-900 mb-4">Create Category</h3>
                
                <form id="categoryForm" method="POST">
                    @csrf
                    <input type="hidden" id="categoryId" name="id">
                    
                    <div class="mb-4">
                        <label for="name" class="block text-sm font-medium text-gray-700 mb-1">
                            Category Name
                        </label>
                        <input type="text" 
                               id="name" 
                               name="name" 
                               required
                               class="w-full px-3 py-2 border border-gray-300 rounded-md focus:outline-none focus:ring-2 focus:ring-purple-500">
                        <div id="nameError" class="text-red-500 text-sm mt-1 hidden"></div>
                    </div>
                    
                    <div class="flex justify-end space-x-3 mt-6">
                        <button type="button" 
                                id="cancelButton"
                                class="px-4 py-2 bg-white border border-gray-300 rounded-md text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-gray-500">
                            Cancel
                        </button>
                        <button type="submit" 
                                id="submitButton"
                                class="px-4 py-2 bg-purple-600 text-white rounded-md hover:bg-purple-700 focus:outline-none focus:ring-2 focus:ring-purple-500">
                            Create
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
    // DOM Elements
    const modal = document.getElementById('categoryModal');
    const openModalBtn = document.getElementById('openCategoryModal');
    const openModalEmptyBtn = document.getElementById('openCategoryModalEmpty');
    const cancelBtn = document.getElementById('cancelButton');
    const form = document.getElementById('categoryForm');
    const submitBtn = document.getElementById('submitButton');
    const modalTitle = document.querySelector('#categoryModal h3');
    const categoryIdInput = document.getElementById('categoryId');
    const nameInput = document.getElementById('name');
    const nameError = document.getElementById('nameError');

    // Open modal for creation
    if (openModalBtn) {
        openModalBtn.addEventListener('click', () => {
            resetForm();
            modalTitle.textContent = 'Create Category';
            submitBtn.textContent = 'Create';
            modal.classList.remove('hidden');
            nameInput.focus();
        });
    }

    // Open modal from empty section
    if (openModalEmptyBtn) {
        openModalEmptyBtn.addEventListener('click', () => {
            resetForm();
            modalTitle.textContent = 'Create Category';
            submitBtn.textContent = 'Create';
            modal.classList.remove('hidden');
            nameInput.focus();
        });
    }

    // Close modal
    cancelBtn.addEventListener('click', () => {
        modal.classList.add('hidden');
        resetForm();
    });

    // Close modal when clicking outside
    window.addEventListener('click', (event) => {
        if (event.target === modal) {
            modal.classList.add('hidden');
            resetForm();
        }
    });

    // Function to reset the form
    function resetForm() {
        form.reset();
        categoryIdInput.value = '';
        nameError.classList.add('hidden');
        nameError.textContent = '';
        submitBtn.disabled = false;
        submitBtn.innerHTML = categoryIdInput.value ? 'Update' : 'Create';
    }

    // Function to edit a category
    window.editCategory = function(id, name) {
        resetForm();
        modalTitle.textContent = 'Edit Category';
        submitBtn.textContent = 'Update';
        categoryIdInput.value = id;
        nameInput.value = name.replace(/\\'/g, "'");
        modal.classList.remove('hidden');
        nameInput.focus();
    }

    // Form submission with AJAX
    form.addEventListener('submit', async function(e) {
        e.preventDefault();
        
        // Disable button to prevent double clicks
        submitBtn.disabled = true;
        submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i>Processing...';
        
        // Hide previous errors
        nameError.classList.add('hidden');
        
        // Prepare URL and method
        const categoryId = categoryIdInput.value;
        const url = categoryId 
            ? `/admin/categories/${categoryId}` 
            : '{{ route("admin.categories.store") }}';
        
        const method = categoryId ? 'PUT' : 'POST';
        
        // Prepare FormData
        const formData = new FormData();
        formData.append('name', nameInput.value);
        formData.append('_method', method);
        
        try {
            // Send request
            const response = await fetch(url, {
                method: 'POST',
                headers: {
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content'),
                    'X-Requested-With': 'XMLHttpRequest',
                    'Accept': 'application/json'
                },
                body: formData
            });
            
            // Try to parse response as JSON
            let responseData;
            const responseText = await response.text();
            
            try {
                responseData = JSON.parse(responseText);
            } catch (e) {
                console.error('Failed to parse JSON response:', responseText);
                throw new Error('Invalid server response');
            }
            
            // Check if request was successful
            if (response.ok && responseData.success) {
                // SUCCESS - Category created/updated
                if (method === 'POST') {
                    // Add new category to table
                    addCategoryToTable(responseData.category);
                    
                    // Hide empty section if it exists
                    const emptySection = document.querySelector('.text-center.py-16');
                    if (emptySection) {
                        emptySection.style.display = 'none';
                        // Show table if it was hidden
                        const tableContainer = document.querySelector('.overflow-x-auto');
                        if (tableContainer && tableContainer.classList.contains('hidden')) {
                            tableContainer.classList.remove('hidden');
                        }
                    }
                } else {
                    // Update existing category
                    updateCategoryInTable(responseData.category);
                }
                
                // Close modal
                modal.classList.add('hidden');
                resetForm();
                
                // Show success message
                showSuccessMessage(responseData.message);
                
            } else {
                // ERROR - Show error messages
                if (responseData.errors && responseData.errors.name) {
                    nameError.textContent = responseData.errors.name[0];
                    nameError.classList.remove('hidden');
                    nameInput.focus();
                } else {
                    alert(responseData.message || 'An error occurred. Please try again.');
                }
            }
            
        } catch (error) {
            console.error('Error:', error);
            // If network or other error occurs
            // Reload page to see changes
            setTimeout(() => {
                window.location.reload();
            }, 1000);
            
        } finally {
            // Re-enable button
            submitBtn.disabled = false;
            submitBtn.textContent = categoryId ? 'Update' : 'Create';
        }
    });

    // Function to add category to table
    function addCategoryToTable(category) {
        const tbody = document.getElementById('categoriesTableBody');
        
        if (!tbody) {
            // Create table structure if it doesn't exist
            createTableStructure();
        }
        
        const newTbody = document.getElementById('categoriesTableBody');
        const newRow = createCategoryRow(category);
        
        // Add to beginning of table
        if (newTbody.children.length > 0) {
            newTbody.insertBefore(newRow, newTbody.firstChild);
        } else {
            newTbody.appendChild(newRow);
        }
    }

    // Function to create table structure if it doesn't exist
    function createTableStructure() {
        const container = document.querySelector('.bg-white.rounded-lg.shadow.overflow-hidden');
        
        // Create table container
        const tableDiv = document.createElement('div');
        tableDiv.className = 'overflow-x-auto';
        
        // Create table
        const table = document.createElement('table');
        table.className = 'min-w-full divide-y divide-gray-200';
        
        // Create header
        const thead = document.createElement('thead');
        thead.className = 'bg-gray-50';
        thead.innerHTML = `
            <tr>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Category Name
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Events
                </th>
                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                    Actions
                </th>
            </tr>
        `;
        
        // Create body
        const tbody = document.createElement('tbody');
        tbody.id = 'categoriesTableBody';
        tbody.className = 'bg-white divide-y divide-gray-200';
        
        // Assemble table
        table.appendChild(thead);
        table.appendChild(tbody);
        tableDiv.appendChild(table);
        
        // Replace empty section content
        const emptySection = document.querySelector('.text-center.py-16');
        if (emptySection) {
            emptySection.parentNode.replaceChild(tableDiv, emptySection);
        }
    }

    // Function to create a category row
    function createCategoryRow(category) {
        const row = document.createElement('tr');
        row.id = `category-${category.id}`;
        
        // Escape special characters
        const escapedName = category.name
            .replace(/&/g, '&amp;')
            .replace(/</g, '&lt;')
            .replace(/>/g, '&gt;')
            .replace(/"/g, '&quot;')
            .replace(/'/g, '&#039;');
        
        row.innerHTML = `
            <td class="px-6 py-4 whitespace-nowrap">
                <div class="text-sm font-medium text-gray-900">${escapedName}</div>
            </td>
            <td class="px-6 py-4 whitespace-nowrap">
                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full bg-blue-100 text-blue-800">
                    ${category.events_count || 0}
                </span>
            </td>
            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                <button onclick="editCategory(${category.id}, '${escapedName.replace(/'/g, "\\'")}')"
                   class="text-blue-600 hover:text-blue-900 mr-4">
                    <i class="fas fa-edit mr-1"></i>Edit
                </button>
                <form action="/admin/categories/${category.id}" 
                      method="POST" 
                      class="inline"
                      onsubmit="return confirm('Are you sure you want to delete this category?')">
                    <input type="hidden" name="_token" value="{{ csrf_token() }}">
                    <input type="hidden" name="_method" value="DELETE">
                    <button type="submit" class="text-red-600 hover:text-red-900">
                        <i class="fas fa-trash mr-1"></i>Delete
                    </button>
                </form>
            </td>
        `;
        
        return row;
    }

    // Function to update a category in the table
    function updateCategoryInTable(category) {
        const row = document.getElementById(`category-${category.id}`);
        if (row) {
            const nameCell = row.querySelector('td:nth-child(1) div');
            if (nameCell) {
                // Escape special characters
                const escapedName = category.name
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
                nameCell.textContent = escapedName;
                
                // Update onclick attribute
                const editBtn = row.querySelector('button');
                if (editBtn) {
                    editBtn.setAttribute('onclick', `editCategory(${category.id}, '${escapedName.replace(/'/g, "\\'")}')`);
                }
            }
        }
    }

    // Function to show success message
    function showSuccessMessage(message) {
        // Create message
        const messageDiv = document.createElement('div');
        messageDiv.className = 'bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6';
        messageDiv.textContent = message;
        messageDiv.id = 'successMessage';
        
        // Add before table
        const container = document.querySelector('.max-w-4xl');
        const firstChild = container.children[1]; // After header
        if (firstChild) {
            container.insertBefore(messageDiv, firstChild);
        }
        
        // Hide after 5 seconds
        setTimeout(() => {
            if (messageDiv.parentNode) {
                messageDiv.remove();
            }
        }, 5000);
    }

    // Global error handler
    window.addEventListener('unhandledrejection', function(event) {
        console.error('Unhandled promise rejection:', event.reason);
    });
</script>
</body>
</html>