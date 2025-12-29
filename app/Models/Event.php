<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Event extends Model
{
    use HasFactory;

    // Dans app/Models/Event.php

protected $fillable = [
    'title',
    'description',
    'start_date',
    'end_date',
    'place',
    'price',
    'is_free',
    'capacity',
    'available_spaces',
    'image',
    'category_id',
    'created_by',
    'is_active', // ⬅️ REMPLACER status par is_active
];

protected $casts = [
    'start_date' => 'datetime',
    'end_date' => 'datetime',
    'price' => 'decimal:2',
    'is_free' => 'boolean',
    'is_active' => 'boolean', // ⬅️ AJOUTER
];

    // CONSTANTES POUR LE STATUT
    public const STATUS_ACTIVE = 'active';
    public const STATUS_ARCHIVED = 'archived';

    // RELATIONS
    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    public function registeredUsers()
    {
        return $this->belongsToMany(User::class, 'registrations')
                    ->withTimestamps();
    }

    // SCOPES
    public function scopeActive($query)
    {
        return $query->where('status', self::STATUS_ACTIVE);
    }

    public function scopeArchived($query)
    {
        return $query->where('status', self::STATUS_ARCHIVED);
    }

    public function scopeUpcoming($query)
    {
        return $query->where('start_date', '>', now());
    }

    public function scopePast($query)
    {
        return $query->where('end_date', '<', now());
    }

    public function scopeSearch($query, $search)
    {
        if (!$search) {
            return $query;
        }
        
        return $query->where(function($q) use ($search) {
            $q->where('title', 'like', "%{$search}%")
              ->orWhere('description', 'like', "%{$search}%")
              ->orWhere('place', 'like', "%{$search}%");
        });
    }

    public function scopeFree($query)
    {
        return $query->where('is_free', true);
    }

    public function scopePaid($query)
    {
        return $query->where('is_free', false);
    }

    public function scopeByCategory($query, $categoryId)
    {
        if (!$categoryId) {
            return $query;
        }
        
        return $query->where('category_id', $categoryId);
    }

    // MÉTHODES UTILES
    public function hasAvailableSpaces(): bool
    {
        return $this->available_spaces > 0;
    }

    public function isFull(): bool
    {
        return $this->available_spaces <= 0;
    }

    public function isRegisteredBy(?User $user): bool
    {
        if (!$user) {
            return false;
        }
        
        return $this->registrations()->where('user_id', $user->id)->exists();
    }

    public function decreaseAvailableSpaces(): void
    {
        if ($this->available_spaces > 0) {
            $this->available_spaces--;
            $this->save();
        }
    }

    public function increaseAvailableSpaces(): void
    {
        if ($this->available_spaces < $this->capacity) {
            $this->available_spaces++;
            $this->save();
        }
    }

    public function isActive(): bool
    {
        return $this->status === self::STATUS_ACTIVE;
    }

    public function isArchived(): bool
    {
        return $this->status === self::STATUS_ARCHIVED;
    }

    public function activate(): void
    {
        $this->status = self::STATUS_ACTIVE;
        $this->save();
    }

    public function archive(): void
    {
        $this->status = self::STATUS_ARCHIVED;
        $this->save();
    }

    // MÉTHODES POUR L'ADMIN
    public function getFormattedPriceAttribute(): string
    {
        if ($this->is_free) {
            return 'Gratuit';
        }
        
        return number_format($this->price, 2) . ' €';
    }

    public function getDurationAttribute(): string
    {
        $start = $this->start_date;
        $end = $this->end_date;
        
        if ($start->format('Y-m-d') === $end->format('Y-m-d')) {
            // Même jour
            return $start->format('d/m/Y') . ' de ' . $start->format('H:i') . ' à ' . $end->format('H:i');
        } else {
            // Sur plusieurs jours
            return 'Du ' . $start->format('d/m/Y H:i') . ' au ' . $end->format('d/m/Y H:i');
        }
    }

    public function getRegistrationPercentageAttribute(): float
    {
        if ($this->capacity === 0) {
            return 0;
        }
        
        $registered = $this->capacity - $this->available_spaces;
        return ($registered / $this->capacity) * 100;
    }

    // MUTATORS
    public function setIsFreeAttribute($value): void
    {
        $this->attributes['is_free'] = (bool) $value;
        if ($this->attributes['is_free']) {
            $this->attributes['price'] = 0;
        }
    }

    public function setCapacityAttribute($value): void
    {
        $this->attributes['capacity'] = (int) $value;
        
        // Si c'est une création, initialiser available_spaces
        if (!isset($this->attributes['available_spaces'])) {
            $this->attributes['available_spaces'] = $value;
        }
    }

    // ACCESSORS
    public function getImageUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }
        
        return asset('storage/' . $this->image);
    }

    public function getThumbnailUrlAttribute(): ?string
    {
        if (!$this->image) {
            return null;
        }
        
        // Vous pourriez ajouter une logique pour les miniatures ici
        return asset('storage/' . $this->image);
    }
}