<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable implements HasName
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'familia_id',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function familia(): BelongsTo
    {
        return $this->belongsTo(\App\Domain\Models\Familia::class, 'familia_id');
    }

    public function getFilamentName(): string
    {
        $name = $this->name ?? (string) $this->email;
        // Evitar "Felipe (Felipe)" cuando el texto entre paréntesis repite el nombre
        if (preg_match('/^(.+?)\s*\(\s*\1\s*\)\s*$/u', trim($name), $m)) {
            return trim($m[1]);
        }
        return $name;
    }
}
