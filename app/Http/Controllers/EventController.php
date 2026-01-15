<?php

namespace App\Http\Controllers;

use App\Models\Event;
use App\Models\Category;
use App\Models\Registration;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class EventController extends Controller
{
    public function index(Request $request)
    {
        // UNIFORMISER : Utiliser 'is_active' partout
        $events = Event::with('category')
                      ->where('is_active', true) // ⬅️ CORRIGÉ (uniquement is_active)
                      ->where('start_date', '>', now())
                      ->orderBy('start_date', 'asc')
                      ->paginate(12);
        
        $categories = Category::all();
        
        return view('events.index', compact('events', 'categories'));
    }
    
   public function show(Event $event)
{
    // Vérifier si l'événement est actif
    if (!$event->is_active) {
        abort(404, 'Cet événement n\'est pas disponible');
    }
    
    $event->load('category', 'creator', 'registrations');
    
    // Vérifier si l'utilisateur connecté est inscrit
    $isRegistered = auth()->check() 
        ? $event->registrations()->where('user_id', auth()->id())->exists()
        : false;

    // Autres événements similaires (même catégorie ou futurs) - CORRIGÉ
    $otherEvents = Event::where('id', '!=', $event->id)
        ->where('is_active', true)
        ->where('start_date', '>', now())
        ->where(function($query) use ($event) {
            // Événements de la même catégorie OU avec peu de places disponibles
            $query->where('category_id', $event->category_id)
                  ->orWhere('available_spaces', '<=', 5); // Événements avec 5 places ou moins
        })
        ->orderBy('start_date', 'asc')
        ->limit(4)
        ->get();

    return view('events.show', compact('event', 'isRegistered', 'otherEvents'));
}
    
    public function register(Request $request, $id)
    {
        $this->middleware('auth');
        
        $user = Auth::user();
        $event = Event::where('is_active', true)->findOrFail($id); // ⬅️ CORRIGÉ (uniquement is_active)
        
        // Vérifier si déjà inscrit
        if ($event->isRegisteredBy($user)) {
            return back()->with('error', 'Vous êtes déjà inscrit à cet événement.');
        }
        
        // Vérifier places disponibles
        if (!$event->hasAvailableSpaces()) {
            return back()->with('error', 'Désolé, cet événement est complet.');
        }
        
        // Vérifier que l'événement n'a pas encore commencé
        if ($event->start_date <= now()) {
            return back()->with('error', 'Les inscriptions sont closes pour cet événement.');
        }
        
        // Créer l'inscription
        Registration::create([
            'user_id' => $user->id,
            'event_id' => $event->id,
        ]);
        
        // Décrémenter places disponibles
        $event->decreaseAvailableSpaces();
        
        return back()->with('success', 'Inscription réussie !');
    }
    
    public function unregister(Request $request, $id)
    {
        $this->middleware('auth');
        
        $user = Auth::user();
        $event = Event::where('is_active', true)->findOrFail($id); // ⬅️ CORRIGÉ (uniquement is_active)
        
        $registration = Registration::where('user_id', $user->id)
                                    ->where('event_id', $event->id)
                                    ->first();
        
        if (!$registration) {
            return back()->with('error', 'Vous n\'êtes pas inscrit à cet événement.');
        }
        
        // Vérifier qu'on peut encore se désinscrire (avant le début)
        if ($event->start_date <= now()) {
            return back()->with('error', 'La période de désinscription est terminée.');
        }
        
        // Supprimer l'inscription
        $registration->delete();
        
        // Incrémenter places disponibles
        $event->increaseAvailableSpaces();
        
        return back()->with('success', 'Désinscription réussie.');
    }
    
    /**
     * Afficher les inscriptions de l'utilisateur
     */
   public function registrations()
{
    if (!auth()->check()) {
        return redirect()->route('login');
    }

    // Méthode alternative sans utiliser User::registrations()
    $registrations = Registration::where('user_id', auth()->id())
        ->with(['event' => function($query) {
            $query->where('is_active', true)
                  ->with('category');
        }])
        ->whereHas('event', function($query) {
            $query->where('is_active', true);
        })
        ->latest()
        ->paginate(10);

    return view('profile.registrations', compact('registrations'));
}
}