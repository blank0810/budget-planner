<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Expense extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'description',
        'category',
        'amount',
        'date',
        'notes',
        'is_recurring',
        'recurring_interval',
        'next_recurring_date',
        'is_paused',
        'payment_method',
        'receipt_path',
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
     * Get the user that owns the expense.
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    /**
     * Scope a query to only include recurring expenses that are due.
     */
    public function scopeDueForProcessing($query)
    {
        return $query->where('is_recurring', true)
            ->where('next_recurring_date', '<=', now()->toDateString())
            ->where(function ($query) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', now()->toDateString());
            });
    }

    /**
     * Create the next instance of a recurring expense.
     */
    public function createNextRecurringInstance()
    {
        if (!$this->is_recurring || !$this->next_recurring_date) {
            return null;
        }

        $newExpense = $this->replicate([
            'created_at',
            'updated_at',
            'receipt_path',
        ]);

        // Set the date to the next occurrence
        $newExpense->date = $this->next_recurring_date;
        
        // Calculate the next occurrence
        $newExpense->next_recurring_date = $this->calculateNextOccurrence();
        
        // If this is the last occurrence, mark it as such
        if ($this->ends_at && $newExpense->next_recurring_date > $this->ends_at) {
            $newExpense->is_recurring = false;
            $newExpense->next_recurring_date = null;
        }

        return $newExpense->save() ? $newExpense : null;
    }

    /**
     * Calculate the next occurrence date based on the recurring interval.
     */
    protected function calculateNextOccurrence()
    {
        if (!$this->next_recurring_date) {
            return null;
        }
        
        $date = \Carbon\Carbon::parse($this->next_recurring_date);
        
        return match ($this->recurring_interval) {
            'daily' => $date->copy()->addDay(),
            'weekly' => $date->copy()->addWeek(),
            'monthly' => $date->copy()->addMonth(),
            'yearly' => $date->copy()->addYear(),
            default => null,
        };
    }

    /**
     * Process all recurring expenses that are due.
     */
    public static function processRecurringExpenses()
    {
        $processed = 0;
        $now = now();
        
        // Get all recurring expenses that are due for processing
        $recurringExpenses = self::with('user')
            ->where('is_recurring', true)
            ->where('next_recurring_date', '<=', $now->toDateString())
            ->where(function ($query) use ($now) {
                $query->whereNull('ends_at')
                    ->orWhere('ends_at', '>=', $now->toDateString());
            })
            ->get();

        foreach ($recurringExpenses as $expense) {
            // Create the next instance
            $newExpense = $expense->replicate([
                'created_at',
                'updated_at',
                'receipt_path',
            ]);

            // Set the date to the next occurrence
            $newExpense->date = $expense->next_recurring_date;
            
            // Calculate the next occurrence
            $newExpense->next_recurring_date = $expense->calculateNextOccurrence();
            
            // If this is the last occurrence, mark it as such
            if ($expense->ends_at && $newExpense->next_recurring_date > $expense->ends_at) {
                $expense->is_recurring = false;
                $expense->next_recurring_date = null;
                $expense->save();
            }

            // Save the new expense
            if ($newExpense->save()) {
                $processed++;
                
                // Update the original expense's next recurring date
                if ($expense->is_recurring) {
                    $expense->next_recurring_date = $newExpense->next_recurring_date;
                    $expense->save();
                }
            }
        }

        return $processed;
    }
}
