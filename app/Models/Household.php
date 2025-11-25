<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Household extends Model
{
    use HasFactory;

    protected $fillable = [
        'household_number',
        'address_line',
        'purok',
        'zone',
        'head_name',
        'members_count',
        'notes',
    ];

    public function residents(): HasMany
    {
        return $this->hasMany(Resident::class);
    }
}
