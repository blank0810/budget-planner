<?php

namespace Tests\Unit;

use App\Models\User;
use App\Models\Income;
use App\Services\IncomeService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class IncomeServiceTest extends TestCase
{
    use RefreshDatabase;

    protected IncomeService $incomeService;
    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear any existing cache
        Cache::flush();
        
        // Create a test user
        $this->user = User::factory()->create();
        $this->incomeService = app(IncomeService::class);
    }
    
    protected function tearDown(): void
    {
        // Clean up after each test
        Cache::flush();
        parent::tearDown();
    }

    /** @test */
    public function it_calculates_monthly_summary_correctly()
    {
        // Create test data
        $date = now()->startOfMonth();
        
        Income::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 1000,
            'date' => $date,
        ]);
        
        Income::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 2000,
            'date' => $date->copy()->addDay(),
        ]);

        $summary = $this->incomeService->getMonthlySummary(
            $this->user,
            $date->year,
            $date->month
        );

        $this->assertEquals(3000, $summary['total_amount']);
        $this->assertEquals(1500, $summary['average_amount']);
        $this->assertEquals(2, $summary['transaction_count']);
    }

    /** @test */
    public function it_calculates_category_breakdown_correctly()
    {
        $date = now()->startOfMonth();
        
        Income::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 1000,
            'category' => 'Salary',
            'date' => $date,
        ]);
        
        Income::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 500,
            'category' => 'Freelance',
            'date' => $date->copy()->addDay(),
        ]);
        
        Income::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 750,
            'category' => 'Salary',
            'date' => $date->copy()->addDays(2),
        ]);

        $breakdown = $this->incomeService->getCategoryBreakdown(
            $this->user,
            $date->year,
            $date->month
        );

        $this->assertCount(2, $breakdown);
        $this->assertEquals(1750, $breakdown->firstWhere('category', 'Salary')['total_amount']);
        $this->assertEquals(500, $breakdown->firstWhere('category', 'Freelance')['total_amount']);
    }

    /** @test */
    public function it_caches_monthly_summary()
    {
        $date = now()->startOfMonth();
        
        $income = Income::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 1000,
            'date' => $date,
        ]);

        // First call - should hit database
        $summary1 = $this->incomeService->getMonthlySummary(
            $this->user,
            $date->year,
            $date->month
        );

        // Delete the income to prove we're getting cached data
        $income->delete();

        // Second call - should come from cache
        $summary2 = $this->incomeService->getMonthlySummary(
            $this->user,
            $date->year,
            $date->month
        );

        $this->assertEquals($summary1, $summary2);
    }

    /** @test */
    public function it_clears_cache_when_clearing_cache()
    {
        $date = now()->startOfMonth();
        
        // Create and cache some data
        $this->incomeService->getMonthlySummary($this->user, $date->year, $date->month);
        $this->incomeService->getCategoryBreakdown($this->user, $date->year, $date->month);

        // Clear cache
        $this->incomeService->clearCache($this->user, $date->year, $date->month);

        // Check if cache was cleared
        $cacheKey = "user_{$this->user->id}_income_summary_{$date->year}_{$date->month}";
        $this->assertFalse(Cache::has($cacheKey));
    }
}
