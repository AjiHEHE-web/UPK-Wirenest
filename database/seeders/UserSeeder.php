<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use InvalidArgumentException;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $user = config('user.seed_user');
        $requiredFields = ['name', 'username', 'email'];

        if (
            ! is_array($user)
            || collect($requiredFields)->contains(fn (string $field): bool => ! is_string($user[$field] ?? null) || trim($user[$field]) === '')
        ) {
            throw new InvalidArgumentException('User seed configuration must include name, username, and email.');
        }

        if ($user['email'] === config('admin.seed_admin.email')) {
            throw new InvalidArgumentException('The seeded user and admin accounts must use different email addresses.');
        }

        $password = $user['password'] ?? null;
        $passwordConfigured = is_string($password) && trim($password) !== '';
        $userRecord = User::query()->firstOrNew(['email' => $user['email']]);
        $generatedPassword = ! $passwordConfigured && ! $userRecord->exists;

        if ($generatedPassword) {
            if (! app()->isLocal() && ! app()->runningUnitTests()) {
                throw new InvalidArgumentException('Set USER_PASSWORD before seeding outside local or test environments.');
            }

            $password = Str::password(24);
        }

        $userRecord->fill([
            'name' => $user['name'],
            'username' => $user['username'],
            'no_hp' => $user['no_hp'] ?? '',
            'alamat' => $user['alamat'] ?? '',
            'role' => 'user',
        ]);

        if ($passwordConfigured || $generatedPassword) {
            $userRecord->password = Hash::make($password);
        }

        $userRecord->save();

        if ($generatedPassword) {
            $this->command?->warn('Generated a temporary user password. Save it now; later runs preserve this password.');
            $this->command?->line('User username: '.$user['username']);
            $this->command?->line('User email: '.$user['email']);
            $this->command?->line('User password: '.$password);
        }
    }
}
