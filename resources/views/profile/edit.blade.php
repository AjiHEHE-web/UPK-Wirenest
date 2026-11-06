@extends('layouts.user')

@section('content')
    <div class="mx-auto max-w-4xl space-y-6 px-4 py-8 sm:px-6 lg:px-8">
        <div>
            <p class="text-sm font-semibold text-botanical-700">Akun</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-ink">Pengaturan profil</h1>
        </div>

        <form method="POST" action="{{ route('profile.update') }}" class="rounded-2xl border border-line bg-white p-6 shadow-sm">
            @csrf
            @method('PATCH')
            <h2 class="text-lg font-semibold text-ink">Informasi akun</h2>
            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="name" class="mb-2 block text-sm font-semibold text-ink">Nama</label>
                    <input id="name" name="name" value="{{ old('name', $user->name) }}" required class="h-11 w-full rounded-xl border-line text-sm focus:border-botanical-500 focus:ring-botanical-500">
                    @error('name') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-ink">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required maxlength="100" class="h-11 w-full rounded-xl border-line text-sm focus:border-botanical-500 focus:ring-botanical-500">
                    @error('email') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                </div>
            </div>
            <button type="submit" class="mt-6 min-h-11 rounded-xl bg-botanical-800 px-5 text-sm font-semibold text-white hover:bg-botanical-900">Simpan perubahan</button>
        </form>

        <form method="POST" action="{{ route('profile.destroy') }}" class="rounded-2xl border border-rose-200 bg-white p-6 shadow-sm">
            @csrf
            @method('DELETE')
            <h2 class="font-semibold text-rose-800">Hapus akun</h2>
            <p class="mt-2 text-sm text-stone-600">Tindakan ini tidak dapat dibatalkan.</p>
            <label for="delete-password" class="mb-2 mt-4 block text-sm font-semibold text-ink">Konfirmasi password</label>
            <input id="delete-password" name="password" type="password" required autocomplete="current-password" class="h-11 w-full rounded-xl border-line text-sm focus:border-rose-500 focus:ring-rose-500 sm:max-w-sm">
            @error('password', 'userDeletion') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
            <button type="submit" class="mt-4 min-h-11 rounded-xl border border-rose-300 px-5 text-sm font-semibold text-rose-700 hover:bg-rose-50">Hapus akun</button>
        </form>
    </div>
@endsection
