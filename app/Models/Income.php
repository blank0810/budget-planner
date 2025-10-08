<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string $source
 * @property string $category
 * @property float $amount
 * @property Carbon $date
 * @property string|null $notes
 * @property bool $is_recurring
 * @property string|null $recurring_interval
 * @property Carbon|null $next_recurring_date
 * @property bool $is_paused
 * @property Carbon $created_at
 * @property Carbon $updated_at
 */
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
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include incomes from the current month.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeThisMonth(Builder $query): Builder
    {
        return $query->whereBetween('date', [
            now()->startOfMonth(),
            now()->endOfMonth()
        ]);
    }

    /**
     * Scope a query to only include incomes from the previous month.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeLastMonth(Builder $query): Builder
    {
        return $query->whereBetween('date', [
            now()->subMonth()->startOfMonth(),
            now()->subMonth()->endOfMonth()
        ]);
    }

    /**
     * Scope a query to only include incomes within a date range.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  mixed  $start Start date (string, Carbon, or DateTime)
     * @param  mixed  $end End date (string, Carbon, or DateTime)
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeDateRange(Builder $query, $start, $end): Builder
    {
        return $query->whereBetween('date', [
            $start instanceof \DateTime ? $start : new \DateTime($start),
            $end instanceof \DateTime ? $end : new \DateTime($end)
        ]);
    }

    /**
     * Scope a query to only include incomes of a specific category.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  string  $category
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeByCategory(Builder $query, string $category): Builder
    {
        return $query->where('category', $category);
    }

    /**
     * Scope a query to only include recurring incomes.
     *
     * @param  \Illuminate\Database\Eloquent\Builder  $query
     * @param  bool  $activeOnly Whether to include only active (non-paused) recurring incomes
     * @return \Illuminate\Database\Eloquent\Builder
     */
    public function scopeRecurring(Builder $query, bool $activeOnly = true): Builder
    {
        $query = $query->where('is_recurring', true);

        if ($activeOnly) {
            $query->where('is_paused', false);
        }

        return $query;
    }
}
