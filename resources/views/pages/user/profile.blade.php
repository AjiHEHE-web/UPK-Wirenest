@extends('layouts.user')

@section('title', 'Profil dan pengaturan')

@section('content')
    <section class="bg-[radial-gradient(ellipse_at_35%_35%,rgba(255,231,235,.55),transparent_30%),#fbfbf6] px-4 pb-16 pt-8 sm:px-6 sm:pb-20 lg:px-8">
        <div class="mx-auto max-w-7xl">
            <div>
                <p class="inline-flex items-center gap-2 text-xs font-semibold uppercase tracking-[0.14em] text-[#a8942f]"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 3v18M3 12h18"/><circle cx="12" cy="12" r="8"/></svg>Anggota kolektif WireNest</p>
                <h1 class="mt-2 font-display text-4xl font-semibold text-[#34873a] sm:text-5xl">Profil &amp; pengaturan</h1>
                <p class="mt-2 max-w-lg text-sm leading-6 text-stone-600">Kelola informasi akun, kontak, dan alamat pengiriman Anda.</p>
            </div>

            <div class="mt-6 grid items-start gap-6 lg:grid-cols-[minmax(0,0.8fr)_minmax(0,1.2fr)] lg:gap-7">
                <div class="space-y-5" x-data="{ editing: @js($errors->has('name') || $errors->has('username') || $errors->has('email') || $errors->has('no_hp') || $errors->has('alamat') || $errors->has('profil_user')) }">
                    <section class="rounded-[30px] bg-matcha p-6 text-center shadow-[0_12px_34px_rgba(47,75,55,.08)] sm:p-8">
                        <div class="relative mx-auto h-28 w-28 rounded-full border-[4px] border-botanical-600 bg-botanical-100 p-1 shadow-md">
                            @if ($user->profil_user)
                                <img src="{{ Storage::disk('public')->url($user->profil_user) }}" alt="Foto profil {{ $user->nama ?: $user->name }}" class="h-full w-full rounded-full object-cover">
                            @else
                                <div class="flex h-full w-full items-center justify-center rounded-full bg-[#e2f1df] text-botanical-700"><x-application-logo class="h-12 w-12 fill-current" /></div>
                            @endif
                            <span class="absolute -bottom-1 -right-1 flex h-9 w-9 items-center justify-center rounded-full border-2 border-white bg-[#34873a] text-white"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M4 8h3l2-3h6l2 3h3v11H4z"/><circle cx="12" cy="13" r="3"/></svg></span>
                        </div>
                        <h2 class="mt-4 font-display text-3xl font-semibold text-[#34873a]">{{ $user->nama ?: $user->name }}</h2>
                        <p class="mt-1 text-sm text-stone-500">{{ '@'.$user->username }} · Anggota WireNest</p>

                        <div class="mt-6 space-y-4 rounded-[24px] bg-[#e7f4e3] p-5 text-left">
                            <div class="flex items-start gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-matcha text-botanical-700"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="2"/><path d="m4 7 8 6 8-6"/></svg></span>
                                <div class="min-w-0"><p class="text-[10px] font-semibold uppercase tracking-wide text-stone-500">Alamat email</p><p class="mt-0.5 break-all text-sm font-medium text-ink">{{ $user->email }}</p></div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-matcha text-botanical-700"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M6.6 3.5h3l1.5 4-2 1.5a15 15 0 0 0 5.9 5.9l1.5-2 4 1.5v3a2 2 0 0 1-2.2 2A17 17 0 0 1 4.6 5.7a2 2 0 0 1 2-2.2Z"/></svg></span>
                                <div class="min-w-0"><p class="text-[10px] font-semibold uppercase tracking-wide text-stone-500">Nomor telepon</p><p class="mt-0.5 text-sm font-medium text-ink">{{ $user->no_hp ?: 'Belum ditambahkan' }}</p></div>
                            </div>
                            <div class="flex items-start gap-3">
                                <span class="flex h-9 w-9 shrink-0 items-center justify-center rounded-full bg-matcha text-botanical-700"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M20 10c0 5-8 11-8 11S4 15 4 10a8 8 0 1 1 16 0Z"/><circle cx="12" cy="10" r="2.5"/></svg></span>
                                <div class="min-w-0"><p class="text-[10px] font-semibold uppercase tracking-wide text-stone-500">Alamat pengiriman</p><p class="mt-0.5 whitespace-pre-line text-sm leading-5 text-ink">{{ $user->alamat ?: 'Belum ditambahkan' }}</p></div>
                            </div>
                        </div>
                        <button type="button" x-on:click="editing = !editing" x-bind:aria-expanded="editing.toString()" class="mt-5 inline-flex min-h-11 w-full items-center justify-center gap-2 rounded-full bg-[#34873a] px-5 text-sm font-semibold text-white shadow-[0_6px_16px_rgba(65,95,72,.16)] transition hover:bg-botanical-800"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="m15 5 4 4M4 20l4-.8L19 8a2.8 2.8 0 0 0-4-4L4 15l-1 5Z"/></svg><span x-text="editing ? 'Tutup edit profil' : 'Edit profil'"></span></button>
                    </section>

                    <div x-cloak x-show="editing" x-transition class="rounded-[28px] bg-matcha p-5 shadow-[0_10px_26px_rgba(47,75,55,.07)] sm:p-6">
                        <h2 class="font-display text-xl font-semibold text-[#34873a]">Perbarui informasi profil</h2>
                        <form method="POST" action="{{ route('user.profile.update') }}" enctype="multipart/form-data" class="mt-5 space-y-4">
                            @csrf
                            @method('PUT')
                            <div><label for="name" class="mb-1.5 block text-sm font-semibold text-ink">Nama lengkap</label><input id="name" name="name" value="{{ old('name', $user->nama ?: $user->name) }}" required maxlength="100" class="store-field">@error('name')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror</div>
                            <div><label for="username" class="mb-1.5 block text-sm font-semibold text-ink">Username</label><input id="username" name="username" value="{{ old('username', $user->username) }}" required maxlength="50" autocomplete="username" class="store-field">@error('username')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror</div>
                            <div><label for="email" class="mb-1.5 block text-sm font-semibold text-ink">Email</label><input id="email" name="email" type="email" value="{{ old('email', $user->email) }}" required autocomplete="email" maxlength="100" class="store-field">@error('email')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror</div>
                            <div><label for="no_hp" class="mb-1.5 block text-sm font-semibold text-ink">Nomor telepon</label><input id="no_hp" name="no_hp" type="tel" value="{{ old('no_hp', $user->no_hp) }}" required maxlength="20" class="store-field">@error('no_hp')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror</div>
                            <div><label for="alamat" class="mb-1.5 block text-sm font-semibold text-ink">Alamat utama</label><textarea id="alamat" name="alamat" rows="3" required maxlength="5000" class="w-full rounded-2xl border-line bg-matcha text-sm focus:border-botanical-500 focus:ring-botanical-500">{{ old('alamat', $user->alamat) }}</textarea>@error('alamat')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror</div>
                            <div><label for="profil_user" class="mb-1.5 block text-sm font-semibold text-ink">Foto profil <span class="font-normal text-stone-500">(opsional)</span></label><input id="profil_user" name="profil_user" type="file" accept=".jpg,.jpeg,.png,.webp,image/jpeg,image/png,image/webp" class="block w-full text-sm text-stone-600 file:mr-3 file:min-h-10 file:rounded-full file:border-0 file:bg-botanical-50 file:px-4 file:font-semibold file:text-botanical-800">@error('profil_user')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror</div>
                            <button type="submit" class="inline-flex min-h-11 w-full items-center justify-center rounded-full bg-[#34873a] px-5 text-sm font-semibold text-white transition hover:bg-botanical-800">Simpan profil</button>
                        </form>
                    </div>

                    <div class="flex items-center justify-between gap-4 rounded-[26px] bg-[#84a587] p-5 text-white sm:px-6">
                        <div><p class="font-semibold text-[#34873a]">Keluar dari WireNest</p><p class="mt-1 text-xs text-white">Akhiri sesi akun dengan aman.</p></div>
                        <form method="POST" action="{{ route('logout') }}">@csrf<button type="submit" class="inline-flex min-h-10 items-center justify-center rounded-full bg-matcha px-5 text-sm font-semibold text-rose-700 transition hover:bg-[#fff2f5]">Keluar</button></form>
                    </div>
                </div>

                <section class="rounded-[30px] bg-matcha p-6 shadow-[0_12px_34px_rgba(47,75,55,.08)] sm:p-8">
                    <div class="flex flex-wrap items-start justify-between gap-4">
                        <div class="flex items-start gap-3">
                            <span class="flex h-10 w-10 shrink-0 items-center justify-center rounded-full bg-[#34873a] text-white"><svg class="h-5 w-5" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.7" aria-hidden="true"><path d="M12 3 5 6v5c0 4.6 2.8 8 7 10 4.2-2 7-5.4 7-10V6l-7-3Z"/><path d="M9 12h6m-3-3v6"/></svg></span>
                            <div><h2 class="font-display text-2xl font-semibold text-[#34873a]">Ubah kata sandi</h2><p class="mt-1 max-w-sm text-sm leading-5 text-stone-500">Perbarui kredensial akun untuk menjaga keamanan.</p></div>
                        </div>
                        <span class="rounded-full bg-[#fff0bd] px-3 py-1.5 text-xs font-semibold text-[#745900]">Akun WireNest</span>
                    </div>

                    <form method="POST" action="{{ route('user.profile.password') }}" class="mt-7 space-y-5">
                        @csrf
                        @method('PUT')
                        <div><label for="current_password" class="mb-2 block text-sm font-semibold text-stone-700">Kata sandi saat ini</label><input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="h-12 w-full rounded-full border-0 bg-[#e4f2df] px-5 text-sm focus:ring-2 focus:ring-botanical-500">@error('current_password')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror</div>
                        <div class="grid gap-4 sm:grid-cols-2">
                            <div><label for="password" class="mb-2 block text-sm font-semibold text-stone-700">Kata sandi baru</label><input id="password" name="password" type="password" required autocomplete="new-password" class="h-12 w-full rounded-full border-0 bg-[#e4f2df] px-5 text-sm focus:ring-2 focus:ring-botanical-500">@error('password')<p class="mt-1 text-sm text-rose-700">{{ $message }}</p>@enderror</div>
                            <div><label for="password_confirmation" class="mb-2 block text-sm font-semibold text-stone-700">Konfirmasi kata sandi</label><input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="h-12 w-full rounded-full border-0 bg-[#e4f2df] px-5 text-sm focus:ring-2 focus:ring-botanical-500"></div>
                        </div>
                        <div class="flex justify-end pt-1"><button type="submit" class="inline-flex min-h-11 items-center justify-center gap-2 rounded-full bg-[#34873a] px-6 text-sm font-semibold text-white shadow-[0_6px_16px_rgba(65,95,72,.16)] transition hover:bg-botanical-800"><svg class="h-4 w-4" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.8" aria-hidden="true"><path d="M12 3 5 6v5c0 4.6 2.8 8 7 10 4.2-2 7-5.4 7-10V6l-7-3Z"/><path d="m9 12 2 2 4-4"/></svg>Perbarui kata sandi</button></div>
                    </form>
                </section>
            </div>
        </div>
    </section>
@endsection
