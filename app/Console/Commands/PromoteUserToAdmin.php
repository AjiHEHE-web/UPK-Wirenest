<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Attributes\Description;
use Illuminate\Console\Attributes\Signature;
use Illuminate\Console\Command;

#[Signature('admin:promote {email : Email of the existing account to promote}')]
#[Description('Grant the Admin role to an existing account')]
class PromoteUserToAdmin extends Command
{
    public function handle(): int
    {
        $user = User::query()->where('email', $this->argument('email'))->first();

        if ($user === null) {
            $this->components->error('No account exists with that email address.');

            return self::FAILURE;
        }

        if ($user->role === 'admin') {
            $this->components->info('This account already has the Admin role.');

            return self::SUCCESS;
        }

        $user->forceFill(['role' => 'admin'])->save();

        $this->components->info("Admin access granted to {$user->email}.");

        return self::SUCCESS;
    }
}
