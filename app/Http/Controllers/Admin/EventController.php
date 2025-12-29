<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Event;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class EventController extends Controller
{
    /**
     * Afficher la liste des événements (admin)
     */
    public function index()
    {
        $events = Event::with('category', 'creator')
                      ->latest()
                      ->paginate(10);
        
        return view('admin.events.index', compact('events'));
    }

    /**
     * Afficher le formulaire de création
     */
    public function create()
    {
        $categories = Category::all();
        return view('admin.events.create', compact('categories'));
    }

    /**
     * Enregistrer un nouvel événement
     */
 public function store(Request $request)
{
    // Validation
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'place' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'price' => 'required|numeric|min:0',
        'capacity' => 'required|integer|min:1',
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'is_free' => 'boolean',
        'status' => 'required|in:active,archived'
    ]);

    // Gérer l'image
    if ($request->hasFile('image')) {
        $validated['image'] = $request->file('image')->store('events', 'public');
    }

    // 🔥 CORRECTION IMPORTANTE : Si is_free est coché, prix = 0
    if ($request->has('is_free') && $request->is_free == '1') {
        $validated['price'] = 0;
        $validated['is_free'] = true;
    } else {
        $validated['is_free'] = false;
    }

    // Définir les valeurs automatiques
    $validated['created_by'] = auth()->id();
    $validated['available_spaces'] = $validated['capacity'];
    
    // Assurer que le prix est un nombre décimal valide
    $validated['price'] = (float) $validated['price'];

    // Créer l'événement
    Event::create($validated);

    return redirect()->route('admin.events.index')
                    ->with('success', 'Événement créé avec succès !');
}
    /**
     * Afficher un événement (détail admin)
     */
    public function show(Event $event)
    {
        $event->load('category', 'creator', 'registrations.user');
        return view('admin.events.show', compact('event'));
    }

    /**
     * Afficher le formulaire d'édition
     */
    public function edit(Event $event)
    {
        $categories = Category::all();
        return view('admin.events.edit', compact('event', 'categories'));
    }

    /**
     * Mettre à jour un événement
     */
    public function update(Request $request, Event $event)
{
    // Validation
    $validated = $request->validate([
        'title' => 'required|string|max:255',
        'description' => 'required|string',
        'start_date' => 'required|date',
        'end_date' => 'required|date|after:start_date',
        'place' => 'required|string|max:255',
        'category_id' => 'required|exists:categories,id',
        'price' => 'required|numeric|min:0',
        'capacity' => 'required|integer|min:1', // ⬅️ REMPLACÉ amount par capacity
        'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,webp|max:2048',
        'is_free' => 'boolean',
        'status' => 'required|in:active,archived'
    ]);

    // Gérer l'image
    if ($request->hasFile('image')) {
        // Supprimer l'ancienne image
        if ($event->image && Storage::disk('public')->exists($event->image)) {
            Storage::disk('public')->delete($event->image);
        }
        $validated['image'] = $request->file('image')->store('events', 'public');
    } elseif ($request->has('remove_image')) {
        // Supprimer l'image existante
        if ($event->image && Storage::disk('public')->exists($event->image)) {
            Storage::disk('public')->delete($event->image);
        }
        $validated['image'] = null;
    } else {
        // Garder l'image actuelle
        $validated['image'] = $event->image;
    }

    // Si l'événement est gratuit, forcer le prix à 0
    if ($request->has('is_free') && $request->is_free == '1') {
        $validated['price'] = 0;
        $validated['is_free'] = true;
    } else {
        $validated['is_free'] = false;
    }

    // Gérer les places disponibles si la capacité change
    if ($validated['capacity'] != $event->capacity) {
        $difference = $validated['capacity'] - $event->capacity;
        $validated['available_spaces'] = max(0, $event->available_spaces + $difference);
    }

    // Mettre à jour l'événement
    $event->update($validated);

    return redirect()->route('admin.events.index')
                    ->with('success', 'Événement mis à jour avec succès !');
}
    /**
     * Supprimer un événement
     */
    public function destroy(Event $event)
    {
        // Supprimer l'image si elle existe
        if ($event->image && Storage::disk('public')->exists($event->image)) {
            Storage::disk('public')->delete($event->image);
        }

        $event->delete();

        return redirect()->route('admin.events.index')
                        ->with('success', 'Événement supprimé avec succès !');
    }
}