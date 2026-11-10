@extends('layouts.user')

@section('title', 'Profil')

@section('content')
    <section class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <div>
            <p class="store-eyebrow">Akun Anda</p>
            <h1 class="store-title mt-2 text-4xl sm:text-5xl">Profil dan keamanan</h1>
            <p class="mt-3 text-sm leading-6 text-stone-600">Perbarui informasi kontak agar pesanan dapat dikirim dengan tepat.</p>
        </div>

        <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data" class="store-card mt-8 p-5 sm:p-7">
            @csrf
            @method('PUT')
            <h2 class="font-display text-xl font-medium text-botanical-950">Informasi profil</h2>
            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="name" class="mb-2 block text-sm font-semibold text-ink">Nama lengkap</label>
                    <input id="name" name="name" value="{{ old('name', $user->nama ?: $user->name) }}" required maxlength="100" class="store-field">
                    @error('name') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="username" class="mb-2 block text-sm font-semibold text-ink">Username</label>
                    <input id="username" name="username" value="{{ old('username', $user->username) }}" required maxlength="50" autocomplete="username" class="store-field">
                    @error('username') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-ink">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="email" maxlength="100" class="store-field">
                    @error('email') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="no_hp" class="mb-2 block text-sm font-semibold text-ink">Nomor telepon</label>
                    <input id="no_hp" name="no_hp" type="tel" value="{{ old('no_hp', $user->no_hp) }}" required maxlength="20" class="store-field">
                    @error('no_hp') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="alamat" class="mb-2 block text-sm font-semibold text-ink">Alamat utama</label>
                    <textarea id="alamat" name="alamat" rows="3" required maxlength="5000" class="w-full rounded-lg border-line bg-white text-sm focus:border-botanical-500 focus:ring-botanical-500">{{ old('alamat', $user->alamat) }}</textarea>
                    @error('alamat') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="profil_user" class="mb-2 block text-sm font-semibold text-ink">Foto profil <span class="font-normal text-stone-500">(opsional)</span></label>
                    <input id="profil_user" name="profil_user" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="block w-full text-sm text-stone-600 file:mr-4 file:min-h-10 file:rounded-lg file:border-0 file:bg-botanical-50 file:px-4 file:font-semibold file:text-botanical-800">
                    <p class="mt-2 text-xs text-stone-500">JPG, PNG, atau WEBP, maksimal 5 MB.</p>
                    @error('profil_user') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="submit" class="store-action store-action-primary">Simpan perubahan</button>
            </div>
        </form>

        <form method="POST" action="{{ route('user.profile.password') }}" class="store-card mt-5 p-5 sm:p-7">
            @csrf
            @method('PUT')
            <h2 class="font-display text-xl font-medium text-botanical-950">Ubah password</h2>
            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="current_password" class="mb-2 block text-sm font-semibold text-ink">Password saat ini</label>
                    <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="store-field">
                    @error('current_password') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold text-ink">Password baru</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password" class="store-field">
                </div>
                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-ink">Konfirmasi password baru</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="store-field">
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="submit" class="store-action store-action-secondary">Perbarui password</button>
            </div>
        </form>
    </section>
@endsection
