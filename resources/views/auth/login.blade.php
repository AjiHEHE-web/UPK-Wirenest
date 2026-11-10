<x-guest-layout>
    <div class="mx-auto w-full max-w-md">
        <div class="mb-8">
            <p class="text-xs font-bold uppercase tracking-[0.18em] text-botanical-700">Akun pengguna</p>
            <h1 class="mt-2 text-3xl font-semibold tracking-tight text-ink">Masuk ke akun</h1>
            <p class="mt-2 text-sm leading-6 text-gray-600">Masukkan detail akun Anda untuk melanjutkan.</p>
        </div>

        <x-auth-session-status
            class="mb-5 rounded-xl border border-botanical-100 bg-botanical-50 px-4 py-3 text-sm text-botanical-800"
            :status="session('status')"
        />

        <form method="POST" action="{{ route('login') }}" class="space-y-5">
            @csrf

            <div>
                <x-input-label for="email" :value="__('Username atau email')" class="text-sm font-semibold text-ink" />
                <x-text-input
                    id="email"
                    class="mt-1.5 block min-h-12 w-full rounded-xl border-line bg-[#f8f8f5] px-4 py-3 text-base shadow-sm focus:border-botanical-500 focus:ring-botanical-500"
                    type="text"
                    name="email"
                    :value="old('email')"
                    required
                    autofocus
                    autocomplete="username"
                    placeholder="Username atau email"
                />
                <x-input-error :messages="$errors->get('email')" class="mt-1.5" />
            </div>

            <div>
                <div class="flex items-center justify-between gap-3">
                    <x-input-label for="password" :value="__('Password')" class="text-sm font-semibold text-ink" />
                    @if (Route::has('password.request'))
                        <a
                            href="{{ route('password.request') }}"
                            class="rounded-md text-sm font-medium text-botanical-700 underline decoration-botanical-300 underline-offset-4 transition hover:text-botanical-900"
                        >
                            Lupa password?
                        </a>
                    @endif
                </div>

                <x-text-input
                    id="password"
                    class="mt-1.5 block min-h-12 w-full rounded-xl border-line bg-[#f8f8f5] px-4 py-3 text-base shadow-sm focus:border-botanical-500 focus:ring-botanical-500"
                    type="password"
                    name="password"
                    required
                    autocomplete="current-password"
                    placeholder="Masukkan password"
                />
                <x-input-error :messages="$errors->get('password')" class="mt-1.5" />
            </div>

            <button
                type="submit"
                class="inline-flex min-h-12 w-full items-center justify-center rounded-xl bg-botanical-800 px-5 py-3 text-sm font-semibold text-white transition hover:bg-botanical-900 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-botanical-700"
            >
                Masuk
            </button>
        </form>

        <div class="mt-7 border-t border-line pt-5 text-center text-sm text-gray-600">
            Belum punya akun?
            <a href="{{ route('register') }}" class="font-semibold text-botanical-800 underline decoration-botanical-300 underline-offset-4 transition hover:text-botanical-900">
                Buat akun
            </a>
        </div>
    </div>
</x-guest-layout>
