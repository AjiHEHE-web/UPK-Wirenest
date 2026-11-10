<x-guest-layout :wide="true">
    <div class="mb-8">
        <p class="text-xs font-bold uppercase tracking-[0.18em] text-botanical-700">Akun pengguna</p>
        <h1 class="mt-2 text-3xl font-semibold tracking-tight text-ink">Buat akun baru</h1>
        <p class="mt-2 text-sm leading-6 text-gray-600">
            Lengkapi data diri Anda untuk mulai menggunakan WireNest.
        </p>
    </div>

    <form method="POST" action="{{ route('register') }}" class="space-y-5">
        @csrf

        <div class="grid gap-5 sm:grid-cols-2">
            <div>
                <x-input-label for="name" value="Nama lengkap" class="text-sm font-semibold text-ink" />
                <x-text-input
                    id="name"
                    class="mt-1.5 block min-h-12 w-full rounded-xl border-line bg-[#f8f8f5] px-4 py-3 text-base shadow-sm focus:border-botanical-500 focus:ring-botanical-500"
                    type="text"
                    name="name"
                    :value="old('name')"
                    required
                    autofocus
                    autocomplete="name"
                    maxlength="100"
                    placeholder="Nama sesuai identitas"
                />
                <x-input-error :messages="$errors->get('name')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="username" value="Username" class="text-sm font-semibold text-ink" />
                <x-text-input
                    id="username"
                    class="mt-1.5 block min-h-12 w-full rounded-xl border-line bg-[#f8f8f5] px-4 py-3 text-base shadow-sm focus:border-botanical-500 focus:ring-botanical-500"
                    type="text"
                    name="username"
                    :value="old('username')"
                    required
                    maxlength="50"
                    autocomplete="username"
                    placeholder="Pilih username"
                />
                <x-input-error :messages="$errors->get('username')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="email" value="Email" class="text-sm font-semibold text-ink" />
                <x-text-input
                    id="email"
                    class="mt-1.5 block min-h-12 w-full rounded-xl border-line bg-[#f8f8f5] px-4 py-3 text-base shadow-sm focus:border-botanical-500 focus:ring-botanical-500"
                    type="email"
                    name="email"
                    :value="old('email')"
                    required
                    maxlength="100"
                    autocomplete="email"
                    placeholder="nama@email.com"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="no_hp" value="Nomor HP" class="text-sm font-semibold text-ink" />
                <x-text-input
                    id="no_hp"
                    class="mt-1.5 block min-h-12 w-full rounded-xl border-line bg-[#f8f8f5] px-4 py-3 text-base shadow-sm focus:border-botanical-500 focus:ring-botanical-500"
                    type="tel"
                    name="no_hp"
                    :value="old('no_hp')"
                    required
                    maxlength="20"
                    autocomplete="tel"
                    placeholder="08xxxxxxxxxx"
                />
                <x-input-error :messages="$errors->get('no_hp')" class="mt-1.5" />
            </div>

            <div class="sm:col-span-2">
                <x-input-label for="alamat" value="Alamat" class="text-sm font-semibold text-ink" />
                <textarea
                    id="alamat"
                    name="alamat"
                    rows="3"
                    required
                    maxlength="5000"
                    autocomplete="street-address"
                    placeholder="Masukkan alamat lengkap"
                    class="mt-1.5 block w-full rounded-xl border-line bg-[#f8f8f5] px-4 py-3 text-base shadow-sm focus:border-botanical-500 focus:ring-botanical-500"
                >{{ old('alamat') }}</textarea>
                <x-input-error :messages="$errors->get('alamat')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="password" value="Password" class="text-sm font-semibold text-ink" />
                <x-text-input
                    id="password"
                    class="mt-1.5 block min-h-12 w-full rounded-xl border-line bg-[#f8f8f5] px-4 py-3 text-base shadow-sm focus:border-botanical-500 focus:ring-botanical-500"
                    type="password"
                    name="password"
                    required
                    autocomplete="new-password"
                    placeholder="Buat password"
                />
                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
            </div>

            <div>
                <x-input-label for="password_confirmation" value="Konfirmasi password" class="text-sm font-semibold text-ink" />
                <x-text-input
                    id="password_confirmation"
                    class="mt-1.5 block min-h-12 w-full rounded-xl border-line bg-[#f8f8f5] px-4 py-3 text-base shadow-sm focus:border-botanical-500 focus:ring-botanical-500"
                    type="password"
                    name="password_confirmation"
                    required
                    autocomplete="new-password"
                    placeholder="Ulangi password"
                />
                <x-input-error :messages="$errors->get('password_confirmation')" class="mt-1.5" />
            </div>
        </div>

        <div class="flex flex-col-reverse items-stretch justify-between gap-4 border-t border-line pt-5 sm:flex-row sm:items-center">
            <a
                href="{{ route('login') }}"
                class="inline-flex min-h-11 items-center justify-center rounded-lg px-3 text-sm font-medium text-botanical-700 transition hover:bg-botanical-50 hover:text-botanical-900"
            >
                Sudah punya akun? Masuk
            </a>

            <button
                type="submit"
                class="inline-flex min-h-11 items-center justify-center rounded-lg bg-botanical-700 px-6 py-3 text-sm font-semibold text-white shadow-sm transition hover:bg-botanical-800 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-botanical-700"
            >
                Daftar sekarang
            </button>
        </div>
    </form>
</x-guest-layout>
