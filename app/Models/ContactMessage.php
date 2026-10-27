<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

class ContactMessage extends Model
{
    public const TYPES = [
        'contact' => 'Əlaqə',
        'partner' => 'Tərəfdaşlıq',
    ];

    protected $fillable = [
        'type', 'name', 'company', 'country', 'email', 'phone', 'message', 'locale', 'ip_address', 'read_at',
    ];

    protected $casts = [
        'read_at' => 'datetime',
    ];

    public function scopeUnread(Builder $query): Builder
    {
        return $query->whereNull('read_at');
    }

    public function isRead(): bool
    {
        return $this->read_at !== null;
    }

    public function typeLabel(): string
    {
        return self::TYPES[$this->type] ?? $this->type;
    }
}
