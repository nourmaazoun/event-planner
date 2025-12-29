<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use App\Models\Event;
use App\Models\Registration;
class User extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'profile_image',
        'phone',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    /**
     * Les événements créés par cet utilisateur
     */
    public function createdEvents(): HasMany
    {
        return $this->hasMany(Event::class, 'created_by');
    }

    /**
     * Les inscriptions de cet utilisateur
     */
    public function registrations(): HasMany
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Les événements auxquels l'utilisateur est inscrit
     */
    public function registeredEvents(): BelongsToMany
    {
        return $this->belongsToMany(Event::class, 'registrations')
                    ->withTimestamps();
    }

    /**
     * Vérifie si l'utilisateur est un administrateur
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Vérifie si l'utilisateur est un utilisateur simple
     */
    public function isUser(): bool
    {
        return $this->role === 'user';
    }

    /**
     * Vérifie si l'utilisateur est inscrit à un événement
     */
    public function isRegisteredForEvent(Event $event): bool
    {
        return $this->registrations()->where('event_id', $event->id)->exists();
    }

    /**
     * Vérifie si l'utilisateur peut s'inscrire à un événement
     */
    public function canRegisterForEvent(Event $event): bool
    {
        return !$this->isRegisteredForEvent($event) && $event->hasAvailableSpaces();
    }

    /**
     * Inscrire l'utilisateur à un événement
     */
    public function registerForEvent(Event $event): Registration
    {
        if ($this->isRegisteredForEvent($event)) {
            throw new \Exception('Vous êtes déjà inscrit à cet événement.');
        }

        if (!$event->hasAvailableSpaces()) {
            throw new \Exception('Plus de places disponibles pour cet événement.');
        }

        $registration = $this->registrations()->create([
            'event_id' => $event->id,
        ]);

        $event->decreaseAvailableSpaces();

        return $registration;
    }

    /**
     * Désinscrire l'utilisateur d'un événement
     */
    public function unregisterFromEvent(Event $event): bool
    {
        $registration = $this->registrations()->where('event_id', $event->id)->first();
        
        if ($registration) {
            $registration->delete();
            $event->increaseAvailableSpaces();
            return true;
        }

        return false;
    }
}