<x-guest-layout>
    <div
        class="relative flex min-h-screen items-center justify-center overflow-hidden bg-slate-950 px-4 py-8"
    >
        <div
            class="absolute left-[-100px] top-[-100px] h-80 w-80 rounded-full bg-indigo-600/30 blur-3xl"
        ></div>

        <div
            class="absolute bottom-[-120px] right-[-80px] h-96 w-96 rounded-full bg-violet-500/20 blur-3xl"
        ></div>

        <div
            class="relative grid w-full max-w-5xl overflow-hidden rounded-[36px] bg-white shadow-2xl lg:grid-cols-2"
        >
            <section
                class="relative hidden overflow-hidden bg-gradient-to-br from-indigo-700 via-indigo-600 to-violet-500 p-12 text-white lg:flex lg:flex-col lg:justify-between"
            >
                <div
                    class="absolute -right-20 -top-20 h-72 w-72 rounded-full bg-white/10"
                ></div>

                <div
                    class="absolute -bottom-24 -left-20 h-72 w-72 rounded-full bg-white/10"
                ></div>

                <div class="relative">
                    <div
                        class="flex h-14 w-14 items-center justify-center rounded-2xl bg-white/15 backdrop-blur"
                    >
                        <svg
                            class="h-8 w-8"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 14l9-5-9-5-9 5 9 5z"
                            />
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 14l6.16-3.422A12.083 12.083 0 0118.5 16.5c-1.65 1.1-3.86 1.75-6.5 1.75s-4.85-.65-6.5-1.75a12.083 12.083 0 01.34-5.922L12 14z"
                            />
                        </svg>
                    </div>

                    <h1 class="mt-7 text-4xl font-extrabold leading-tight">
                        Pencatatan kesiswaan dalam satu aplikasi.
                    </h1>

                    <p class="mt-5 text-sm leading-7 text-indigo-100">
                        Kelola data siswa, absensi, pelanggaran, prestasi,
                        perizinan, dan bimbingan konseling dengan mudah.
                    </p>
                </div>

                <div
                    class="relative rounded-3xl border border-white/10 bg-white/10 p-5 backdrop-blur"
                >
                    <p class="text-sm leading-6 text-indigo-50">
                        Dibuat dengan desain mobile-first agar nyaman digunakan
                        oleh guru langsung melalui telepon genggam.
                    </p>
                </div>
            </section>

            <section class="px-5 py-8 sm:px-10 sm:py-12 lg:px-12">
                <div class="mx-auto max-w-md">
                    <div class="lg:hidden">
                        <div
                            class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-500 text-white shadow-xl shadow-indigo-200"
                        >
                            <svg
                                class="h-7 w-7"
                                fill="none"
                                viewBox="0 0 24 24"
                                stroke="currentColor"
                                stroke-width="2"
                            >
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 14l9-5-9-5-9 5 9 5z"
                                />
                                <path
                                    stroke-linecap="round"
                                    stroke-linejoin="round"
                                    d="M12 14l6.16-3.422A12.083 12.083 0 0118.5 16.5c-1.65 1.1-3.86 1.75-6.5 1.75s-4.85-.65-6.5-1.75a12.083 12.083 0 01.34-5.922L12 14z"
                                />
                            </svg>
                        </div>
                    </div>

                    <p
                        class="mt-7 text-xs font-bold uppercase tracking-[0.24em] text-indigo-600 lg:mt-0"
                    >
                        Kesiswaan Mobile
                    </p>

                    <h2 class="mt-3 text-3xl font-extrabold text-slate-900">
                        Selamat datang
                    </h2>

                    <p class="mt-2 text-sm leading-6 text-slate-500">
                        Masukkan email dan password untuk melanjutkan.
                    </p>

                    <form
                        method="POST"
                        action="{{ route('login') }}"
                        class="mt-8 space-y-5"
                    >
                        @csrf

                        <div>
                            <label
                                for="email"
                                class="mb-2 block text-sm font-bold text-slate-700"
                            >
                                Alamat email
                            </label>

                            <input
                                id="email"
                                type="email"
                                name="email"
                                value="{{ old('email') }}"
                                required
                                autofocus
                                autocomplete="username"
                                placeholder="nama@email.com"
                                class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                            >

                            @error('email')
                                <p class="mt-2 text-xs font-medium text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div x-data="{ show: false }">
                            <label
                                for="password"
                                class="mb-2 block text-sm font-bold text-slate-700"
                            >
                                Password
                            </label>

                            <div class="relative">
                                <input
                                    id="password"
                                    x-bind:type="show ? 'text' : 'password'"
                                    name="password"
                                    required
                                    autocomplete="current-password"
                                    placeholder="Masukkan password"
                                    class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 pr-14 text-sm text-slate-900 outline-none transition placeholder:text-slate-400 focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                                >

                                <button
                                    type="button"
                                    x-on:click="show = !show"
                                    class="absolute inset-y-0 right-0 flex w-14 items-center justify-center text-slate-400"
                                >
                                    <svg
                                        x-show="!show"
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"
                                        />
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"
                                        />
                                    </svg>

                                    <svg
                                        x-show="show"
                                        x-cloak
                                        class="h-5 w-5"
                                        fill="none"
                                        viewBox="0 0 24 24"
                                        stroke="currentColor"
                                        stroke-width="2"
                                    >
                                        <path
                                            stroke-linecap="round"
                                            stroke-linejoin="round"
                                            d="M3 3l18 18M10.58 10.58A2 2 0 0012 14a2 2 0 001.42-.58M9.88 5.09A9.769 9.769 0 0112 5c4.478 0 8.268 2.943 9.542 7a9.835 9.835 0 01-1.32 2.572M6.228 6.228C4.482 7.394 3.16 9.223 2.458 12c1.274 4.057 5.065 7 9.542 7a9.77 9.77 0 004.12-.91"
                                        />
                                    </svg>
                                </button>
                            </div>

                            @error('password')
                                <p class="mt-2 text-xs font-medium text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>

                        <div class="flex items-center justify-between gap-4">
                            <label class="flex items-center gap-3">
                                <input
                                    type="checkbox"
                                    name="remember"
                                    class="h-5 w-5 rounded border-slate-300 text-indigo-600 focus:ring-indigo-500"
                                >

                                <span class="text-xs font-medium text-slate-600">
                                    Ingat saya
                                </span>
                            </label>

                            @if (Route::has('password.request'))
                                <a
                                    href="{{ route('password.request') }}"
                                    class="text-xs font-bold text-indigo-600 hover:text-indigo-700"
                                >
                                    Lupa password?
                                </a>
                            @endif
                        </div>

                        <button
                            type="submit"
                            class="flex h-14 w-full items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-500 text-sm font-bold text-white shadow-xl shadow-indigo-200 transition duration-300 hover:-translate-y-0.5 hover:shadow-2xl"
                        >
                            Masuk ke aplikasi
                        </button>
                    </form>

                    <div
                        class="mt-8 rounded-2xl border border-indigo-100 bg-indigo-50 p-4"
                    >
                        <p class="text-xs font-bold text-indigo-700">
                            Akun administrator awal
                        </p>

                        <p class="mt-2 text-xs leading-5 text-indigo-600">
                            Email: admin@kesiswaan.test
                            <br>
                            Password: password123
                        </p>
                    </div>
                </div>
            </section>
        </div>
    </div>
</x-guest-layout>
