<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Merchant extends Model
{
    protected $fillable = [
        'code',
        'name',
        'description',
        'location',
        'icon',
        'status',
    ];

    public function isActive(): bool
    {
        return $this->status === 'active';
    }
}
