<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use InvalidArgumentException;

class AdminUserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = config('admin.seed_admin');
        $requiredFields = ['name', 'username', 'email'];

        if (
            ! is_array($admin)
            || collect($requiredFields)->contains(fn (string $field): bool => ! is_string($admin[$field] ?? null) || trim($admin[$field]) === '')
        ) {
            throw new InvalidArgumentException('Admin seed configuration must include name, username, and email.');
        }

        $password = $admin['password'] ?? null;
        $passwordConfigured = is_string($password) && trim($password) !== '';
        $user = User::query()->firstOrNew(['email' => $admin['email']]);
        $generatedPassword = ! $passwordConfigured && ! $user->exists;

        if ($generatedPassword) {
            if (! app()->isLocal() && ! app()->runningUnitTests()) {
                throw new InvalidArgumentException('Set ADMIN_PASSWORD before seeding outside local or test environments.');
            }

            $password = Str::password(24);
        }

        $user->fill([
            'name' => $admin['name'],
            'username' => $admin['username'],
            'no_hp' => $admin['no_hp'] ?? '',
            'alamat' => $admin['alamat'] ?? '',
            'role' => 'admin',
        ]);

        if ($passwordConfigured || $generatedPassword) {
            $user->password = Hash::make($password);
        }

        $user->save();

        if ($generatedPassword) {
            $this->command?->warn('Generated a temporary admin password. Save it now; later runs preserve this password.');
            $this->command?->line('Admin username: '.$admin['username']);
            $this->command?->line('Admin email: '.$admin['email']);
            $this->command?->line('Admin password: '.$password);
        }
    }
}
