<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateAdminUser extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'user:create-admin 
                            {--name= : The name of the admin user}
                            {--email= : The email of the admin user}
                            {--password= : The password for the admin user (min: 8 characters)}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new admin user';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $name = $this->option('name') ?: $this->ask('Enter the admin name');
        $email = $this->option('email') ?: $this->ask('Enter the admin email');
        $password = $this->option('password') ?: $this->secret('Enter the admin password (min: 8 characters)');

        // Validate input
        $validator = Validator::make(
            [
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ],
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8'],
            ]
        );

        if ($validator->fails()) {
            $this->error('Validation failed:');
            foreach ($validator->errors()->all() as $error) {
                $this->error('- ' . $error);
            }
            return 1;
        }

        // Create the admin user
        $user = User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
            'is_admin' => true,
            'email_verified_at' => now(),
        ]);

        $this->info('Admin user created successfully!');
        $this->line('Name: ' . $user->name);
        $this->line('Email: ' . $user->email);

        return 0;
    }
}
