<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class MakeVentorAdmin extends Command
{
    protected $signature = 'ventor:make-admin';

    protected $description = 'Create or promote a VENTOR administrator account';

    public function handle(): int
    {
        $name = trim((string) $this->ask('Administrator name'));

        if ($name === '') {
            $this->error('Name is required.');

            return self::FAILURE;
        }

        $email = strtolower(trim((string) $this->ask('Administrator email')));

        $validator = Validator::make(['email' => $email], [
            'email' => ['required', 'email', 'max:255'],
        ]);

        if ($validator->fails()) {
            $this->error('Enter a valid email address.');

            return self::FAILURE;
        }

        $password = (string) $this->secret('Password (at least 12 characters)');
        $confirmation = (string) $this->secret('Confirm password');

        if (strlen($password) < 12 || ! hash_equals($password, $confirmation)) {
            $this->error('Passwords must match and contain at least 12 characters.');

            return self::FAILURE;
        }

        User::updateOrCreate(
            ['email' => $email],
            [
                'name' => $name,
                'password' => Hash::make($password),
                'is_admin' => true,
            ],
        );

        $this->info("Administrator account ready: {$email}");
        $this->line('Sign in at /admin/login.');

        return self::SUCCESS;
    }
}
