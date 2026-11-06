<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('users_dbml');

        Schema::create('users_dbml', function (Blueprint $table): void {
            $table->string('id_user', 36)->primary();
            $table->string('nama', 100);
            $table->string('username', 50)->unique();
            $table->string('email', 100)->unique();
            $table->string('password', 255);
            $table->string('no_hp', 20);
            $table->text('alamat');
            $table->enum('role', ['admin', 'user'])->default('user');
            $table->string('profil_user', 255)->nullable();
            $table->timestamps();
        });

        DB::table('users')->orderBy('id')->chunk(100, function ($users): void {
            foreach ($users as $user) {
                if (mb_strlen($user->name) > 100 || mb_strlen($user->email) > 100) {
                    throw new RuntimeException("User {$user->id} exceeds the DBML name or email length.");
                }

                DB::table('users_dbml')->insert([
                    'id_user' => (string) Str::uuid(),
                    'nama' => $user->name,
                    'username' => 'user_'.$user->id,
                    'email' => $user->email,
                    'password' => $user->password,
                    'no_hp' => '',
                    'alamat' => '',
                    'role' => 'user',
                    'profil_user' => null,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]);
            }
        });

        Schema::drop('users');
        Schema::rename('users_dbml', 'users');

        DB::table('sessions')->whereNotNull('user_id')->update(['user_id' => null]);

        Schema::table('sessions', function (Blueprint $table): void {
            $table->string('user_id', 36)->nullable()->change();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('users_legacy');

        Schema::create('users_legacy', function (Blueprint $table): void {
            $table->id();
            $table->string('name');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->rememberToken();
            $table->timestamps();
        });

        DB::table('users')->orderBy('id_user')->chunk(100, function ($users): void {
            foreach ($users as $user) {
                DB::table('users_legacy')->insert([
                    'name' => $user->nama,
                    'email' => $user->email,
                    'password' => $user->password,
                    'created_at' => $user->created_at,
                    'updated_at' => $user->updated_at,
                ]);
            }
        });

        Schema::drop('users');
        Schema::rename('users_legacy', 'users');

        DB::table('sessions')->update(['user_id' => null]);

        Schema::table('sessions', function (Blueprint $table): void {
            $table->foreignId('user_id')->nullable()->change();
        });
    }
};
