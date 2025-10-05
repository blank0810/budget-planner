<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Income extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'source',
        'category',
        'amount',
        'date',
        'notes',
        'is_recurring',
        'recurring_interval',
        'next_recurring_date',
        'is_paused',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'amount' => 'decimal:2',
        'date' => 'date',
        'is_recurring' => 'boolean',
        'next_recurring_date' => 'date',
        'is_paused' => 'boolean',
    ];

    /**
     * Get the user that owns the income.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
