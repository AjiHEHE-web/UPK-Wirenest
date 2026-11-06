@extends('layouts.user')

@section('title', 'Profil')

@section('content')
    <section class="mx-auto max-w-4xl px-4 py-10 sm:px-6 lg:px-8">
        <div>
            <p class="text-sm font-semibold text-botanical-700">Akun Anda</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-ink">Profil dan keamanan</h1>
            <p class="mt-2 text-sm text-stone-600">Perbarui informasi kontak agar pesanan dapat dikirim dengan tepat.</p>
        </div>

        <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data" class="mt-7 rounded-2xl border border-line bg-white p-5 shadow-sm sm:p-7">
            @csrf
            @method('PUT')
            <h2 class="text-lg font-semibold text-ink">Informasi profil</h2>
            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <div>
                    <label for="name" class="mb-2 block text-sm font-semibold text-ink">Nama lengkap</label>
                    <input id="name" name="name" value="{{ old('name', $user->nama ?: $user->name) }}" required maxlength="100" class="h-11 w-full rounded-xl border-line text-sm focus:border-botanical-500 focus:ring-botanical-500">
                    @error('name') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="username" class="mb-2 block text-sm font-semibold text-ink">Username</label>
                    <input id="username" name="username" value="{{ old('username', $user->username) }}" required maxlength="50" autocomplete="username" class="h-11 w-full rounded-xl border-line text-sm focus:border-botanical-500 focus:ring-botanical-500">
                    @error('username') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="email" class="mb-2 block text-sm font-semibold text-ink">Email</label>
                    <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="email" maxlength="100" class="h-11 w-full rounded-xl border-line text-sm focus:border-botanical-500 focus:ring-botanical-500">
                    @error('email') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="no_hp" class="mb-2 block text-sm font-semibold text-ink">Nomor telepon</label>
                    <input id="no_hp" name="no_hp" type="tel" value="{{ old('no_hp', $user->no_hp) }}" required maxlength="20" class="h-11 w-full rounded-xl border-line text-sm focus:border-botanical-500 focus:ring-botanical-500">
                    @error('no_hp') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                </div>
                <div class="sm:col-span-2">
                    <label for="alamat" class="mb-2 block text-sm font-semibold text-ink">Alamat utama</label>
                    <textarea id="alamat" name="alamat" rows="3" required maxlength="5000" class="w-full rounded-xl border-line text-sm focus:border-botanical-500 focus:ring-botanical-500">{{ old('alamat', $user->alamat) }}</textarea>
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
                <button type="submit" class="min-h-11 rounded-xl bg-botanical-800 px-5 text-sm font-semibold text-white hover:bg-botanical-900">Simpan perubahan</button>
            </div>
        </form>

        <form method="POST" action="{{ route('user.profile.password') }}" class="mt-6 rounded-2xl border border-line bg-white p-5 shadow-sm sm:p-7">
            @csrf
            @method('PUT')
            <h2 class="text-lg font-semibold text-ink">Ubah password</h2>
            <div class="mt-5 grid gap-5 sm:grid-cols-2">
                <div class="sm:col-span-2">
                    <label for="current_password" class="mb-2 block text-sm font-semibold text-ink">Password saat ini</label>
                    <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="h-11 w-full rounded-xl border-line text-sm focus:border-botanical-500 focus:ring-botanical-500">
                    @error('current_password') <p class="mt-1 text-sm text-rose-700">{{ $message }}</p> @enderror
                </div>
                <div>
                    <label for="password" class="mb-2 block text-sm font-semibold text-ink">Password baru</label>
                    <input id="password" name="password" type="password" required autocomplete="new-password" class="h-11 w-full rounded-xl border-line text-sm focus:border-botanical-500 focus:ring-botanical-500">
                </div>
                <div>
                    <label for="password_confirmation" class="mb-2 block text-sm font-semibold text-ink">Konfirmasi password baru</label>
                    <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="h-11 w-full rounded-xl border-line text-sm focus:border-botanical-500 focus:ring-botanical-500">
                </div>
            </div>
            <div class="mt-6 flex justify-end">
                <button type="submit" class="min-h-11 rounded-xl border border-botanical-300 px-5 text-sm font-semibold text-botanical-800 hover:bg-botanical-50">Perbarui password</button>
            </div>
        </form>
    </section>
@endsection
