<?php

namespace App\Console\Commands;

use App\Models\Expense;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

class ProcessRecurringExpenses extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'expenses:process-recurring';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Process all recurring expenses that are due';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting to process recurring expenses...');
        
        try {
            $processed = Expense::processRecurringExpenses();
            
            if ($processed > 0) {
                $this->info("Successfully processed {$processed} recurring expenses.");
                Log::info("Processed {$processed} recurring expenses");
            } else {
                $this->info('No recurring expenses to process.');
            }
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $error = 'Error processing recurring expenses: ' . $e->getMessage();
            $this->error($error);
            Log::error($error);
            
            return Command::FAILURE;
        }
    }
}
