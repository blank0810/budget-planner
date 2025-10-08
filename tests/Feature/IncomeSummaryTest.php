<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Income;
use Carbon\Carbon;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Cache;
use Tests\TestCase;

class IncomeSummaryTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Clear any existing cache
        Cache::flush();
        
        // Create a single user for all tests
        static $user = null;
        if (!$user) {
            $user = User::factory()->create();
        }
        
        $this->user = $user;
        $this->actingAs($this->user);
    }
    
    protected function tearDown(): void
    {
        // Clean up after each test
        Cache::flush();
        parent::tearDown();
    }

    /** @test */
    public function user_can_view_monthly_income_summary()
    {
        $date = now()->startOfMonth();
        
        // Create test income
        $income = Income::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 2000,
            'date' => $date,
            'category' => 'Salary'
        ]);
        
        // Ensure user is authenticated
        $this->actingAs($this->user);
        
        // Debug: Check if income was created
        $this->assertDatabaseHas('incomes', [
            'id' => $income->id,
            'user_id' => $this->user->id,
            'amount' => 2000
        ]);
        
        // Build the URL with query parameters
        $url = route('incomes.monthly', [
            'year' => $date->year,
            'month' => $date->month
        ]);
        
        // Debug: Output the URL being tested
        fwrite(STDERR, "\nTesting URL: " . $url . "\n");
        
        // Make the request
        $response = $this->get($url);
        
        // Debug: Output response status and content
        fwrite(STDERR, "Response status: " . $response->status() . "\n");
        fwrite(STDERR, "Response content: " . $response->getContent() . "\n");
        
        $response->assertStatus(200)
            ->assertSee('Income Summary')
            ->assertSee('2,000.00');
    }

    /** @test */
    public function monthly_summary_shows_correct_data()
    {
        $date = now()->startOfMonth();
        
        // Create test data
        Income::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 1000,
            'date' => $date,
            'category' => 'Salary',
            'is_recurring' => true
        ]);
        
        Income::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 500,
            'date' => $date->copy()->addDay(),
            'category' => 'Freelance'
        ]);

        $response = $this->get(route('incomes.monthly', [
            'year' => $date->year,
            'month' => $date->month
        ]));

        $response->assertStatus(200)
            ->assertSee('1,500.00')  // Total amount
            ->assertSee('750.00')    // Average amount
            ->assertSee('2')         // Transaction count
            ->assertSee('1,000.00'); // Recurring amount
    }

    /** @test */
    public function it_shows_category_breakdown()
    {
        $date = now()->startOfMonth();
        
        Income::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 1000,
            'date' => $date,
            'category' => 'Salary'
        ]);
        
        Income::factory()->create([
            'user_id' => $this->user->id,
            'amount' => 500,
            'date' => $date->copy()->addDay(),
            'category' => 'Freelance'
        ]);

        $response = $this->get(route('incomes.monthly', [
            'year' => $date->year,
            'month' => $date->month
        ]));

        $response->assertStatus(200)
            ->assertSee('Salary')
            ->assertSee('1,000.00')
            ->assertSee('Freelance')
            ->assertSee('500.00');
    }

    /** @test */
    public function it_handles_empty_data_gracefully()
    {
        $date = now()->startOfMonth();
        
        $response = $this->get(route('incomes.monthly', [
            'year' => $date->year,
            'month' => $date->month
        ]));

        $response->assertStatus(200)
            ->assertSee('No recent transactions found');
    }

    /** @test */
    public function it_uses_caching_for_performance()
    {
        $date = now()->startOfMonth();
        
        // First request - should hit the database
        $response1 = $this->get(route('incomes.monthly', [
            'year' => $date->year,
            'month' => $date->month
        ]));
        
        // Clear the query log
        $this->getConnection()->getQueryLog();
        
        // Second request - should come from cache
        $response2 = $this->get(route('incomes.monthly', [
            'year' => $date->year,
            'month' => $date->month
        ]));
        
        // Verify no database queries were made on the second request
        $this->assertCount(0, $this->getConnection()->getQueryLog());
        
        $response2->assertStatus(200);
    }
}
