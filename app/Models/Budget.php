<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Carbon\Carbon;

class Budget extends Model
{
    use HasFactory;

    /**
     * Get the expenses for the budget.
     */
    public function expenses()
    {
        return $this->hasMany(Expense::class, 'category', 'category')
            ->where('user_id', $this->user_id);
    }

    protected $fillable = [
        'user_id',
        'category',
        'amount',
        'year',
        'month',
        'notes',
        'is_active',
    ];

    protected $casts = [
        'amount' => 'decimal:2',
        'year' => 'integer',
        'month' => 'integer',
        'is_active' => 'boolean',
    ];

    /**
     * Get the user that owns the budget.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include budgets for the current month.
     */
    public function scopeCurrentMonth($query)
    {
        return $query->where([
            'year' => now()->year,
            'month' => now()->month,
        ]);
    }

    /**
     * Scope to get budgets for a specific month and year.
     */
    public function scopeForPeriod($query, int $year, int $month)
    {
        return $query->where('year', $year)->where('month', $month);
    }

    /**
     * Scope to get active budgets.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Get the period as a Carbon instance.
     */
    public function getPeriodAttribute()
    {
        return Carbon::create($this->year, $this->month, 1);
    }

    /**
     * Get the formatted period (e.g., "October 2023").
     */
    public function getFormattedPeriodAttribute()
    {
        return $this->period->format('F Y');
    }

    /**
     * Get the formatted month name.
     */
    public function getMonthNameAttribute(): string
    {
        return $this->period->format('F');
    }

    /**
     * Copy budgets from a specific month to a new month.
     *
     * @param int $fromYear
     * @param int $fromMonth
     * @param int $toYear
     * @param int $toMonth
     * @param int $userId
     * @param array $categoryIds Specific category IDs to copy (empty for all)
     * @param float $adjustmentFactor Percentage to adjust the budget amounts (1.0 for same, 1.1 for 10% increase, etc.)
     * @return array Result of the operation
     */
    public static function copyBudgets(
        int $fromYear,
        int $fromMonth,
        int $toYear,
        int $toMonth,
        int $userId,
        array $categoryIds = [],
        float $adjustmentFactor = 1.0
    ): array {
        try {
            $query = self::where('user_id', $userId)
                ->where('year', $fromYear)
                ->where('month', $fromMonth);

            if (!empty($categoryIds)) {
                $query->whereIn('category', $categoryIds);
            }

            $budgets = $query->get();
            
            if ($budgets->isEmpty()) {
                return [
                    'success' => false,
                    'message' => 'No budgets found for the selected period.'
                ];
            }

            $copiedCount = 0;
            $skippedCount = 0;
            $errors = [];

            foreach ($budgets as $budget) {
                // Check if budget already exists for the target month
                $exists = self::where('user_id', $userId)
                    ->where('year', $toYear)
                    ->where('month', $toMonth)
                    ->where('category', $budget->category)
                    ->exists();

                if ($exists) {
                    $skippedCount++;
                    continue;
                }

                // Create new budget for the target month
                $newBudget = $budget->replicate();
                $newBudget->year = $toYear;
                $newBudget->month = $toMonth;
                $newBudget->amount = round($budget->amount * $adjustmentFactor, 2);
                $newBudget->created_at = now();
                $newBudget->updated_at = now();
                
                if ($newBudget->save()) {
                    $copiedCount++;
                } else {
                    $errors[] = "Failed to copy budget for category: {$budget->category}";
                }
            }

            $result = [
                'success' => true,
                'copied_count' => $copiedCount,
                'skipped_count' => $skippedCount,
                'total' => $budgets->count(),
                'errors' => $errors
            ];

            if ($copiedCount === 0 && $skippedCount > 0) {
                $result['message'] = 'All selected budgets already exist for the target month.';
            } elseif ($copiedCount > 0) {
                $result['message'] = "Successfully copied {$copiedCount} " . 
                    Str::plural('budget', $copiedCount) . 
                    " to " . Carbon::create($toYear, $toMonth, 1)->format('F Y');
                
                if ($skippedCount > 0) {
                    $result['message'] .= " ({$skippedCount} skipped - already exist)";
                }
            } else {
                $result['success'] = false;
                $result['message'] = 'No budgets were copied. ' . 
                    ($errors ? implode(' ', $errors) : 'Please check if you have budgets to copy.');
            }

            return $result;

        } catch (\Exception $e) {
            \Log::error('Error copying budgets: ' . $e->getMessage());
            return [
                'success' => false,
                'message' => 'An error occurred while copying budgets: ' . $e->getMessage()
            ];
        }
    }

    /**
     * Get the amount spent in this budget's category for the budget period.
     */
    public function getSpentAmountAttribute()
    {
        return Expense::where('user_id', $this->user_id)
            ->where('category', $this->category)
            ->whereYear('date', $this->year)
            ->whereMonth('date', $this->month)
            ->sum('amount');
    }

    /**
     * Get the remaining budget amount.
     */
    public function getRemainingAmountAttribute()
    {
        return max(0, $this->amount - $this->spent_amount);
    }

    /**
     * Get the budget utilization percentage.
     */
    public function getUtilizationPercentageAttribute()
    {
        if ($this->amount <= 0) {
            return 100;
        }
        
        return min(100, ($this->spent_amount / $this->amount) * 100);
    }

    /**
     * Get the budget status (on_track, warning, over_budget).
     */
    public function getStatusAttribute()
    {
        $percentage = $this->utilization_percentage;
        
        if ($percentage >= 100) {
            return 'over_budget';
        } elseif ($percentage >= 80) {
            return 'warning';
        } else {
            return 'on_track';
        }
    }
}
