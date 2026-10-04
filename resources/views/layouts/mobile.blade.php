<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta
        name="viewport"
        content="width=device-width, initial-scale=1, viewport-fit=cover"
    >

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @yield('title', 'Dashboard') - {{ config('app.name') }}
    </title>

    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @livewireStyles
</head>

<body class="min-h-screen bg-slate-50">
    <div class="mobile-page min-h-screen">
        <header
            class="sticky top-0 z-30 border-b border-slate-100 bg-white/90 backdrop-blur-xl"
        >
            <div
                class="mx-auto flex h-16 max-w-7xl items-center justify-between px-4 sm:px-6"
            >
                <a
                    href="{{ route('dashboard') }}"
                    class="flex items-center gap-3"
                >
                    <div
                        class="flex h-10 w-10 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-500 text-white shadow-lg shadow-indigo-200"
                    >
                        <svg
                            class="h-5 w-5"
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

                    <div>
                        <h1 class="text-sm font-extrabold text-slate-900">
                            Kesiswaan Mobile
                        </h1>

                        <p class="text-[11px] font-medium text-slate-500">
                            Sistem pencatatan siswa
                        </p>
                    </div>
                </a>

                <div
                    x-data="{ open: false }"
                    class="relative"
                >
                    <button
                        type="button"
                        x-on:click="open = !open"
                        class="flex h-10 w-10 items-center justify-center rounded-2xl bg-indigo-50 font-bold text-indigo-600"
                    >
                        {{ strtoupper(substr(auth()->user()->name, 0, 1)) }}
                    </button>

                    <div
                        x-show="open"
                        x-cloak
                        x-on:click.outside="open = false"
                        x-transition
                        class="absolute right-0 mt-3 w-64 overflow-hidden rounded-3xl border border-slate-100 bg-white p-2 shadow-2xl"
                    >
                        <div class="border-b border-slate-100 px-4 py-3">
                            <p class="truncate text-sm font-bold text-slate-900">
                                {{ auth()->user()->name }}
                            </p>

                            <p class="truncate text-xs text-slate-500">
                                {{ auth()->user()->email }}
                            </p>

                            <span
                                class="mt-2 inline-flex rounded-full bg-indigo-50 px-3 py-1 text-[10px] font-bold uppercase tracking-wide text-indigo-600"
                            >
                                {{ auth()->user()->role_label }}
                            </span>
                        </div>

                        <form
                            method="POST"
                            action="{{ route('logout') }}"
                            class="mt-2"
                        >
                            @csrf

                            <button
                                type="submit"
                                class="flex w-full items-center gap-3 rounded-2xl px-4 py-3 text-left text-sm font-semibold text-red-600 transition hover:bg-red-50"
                            >
                                <svg
                                    class="h-5 w-5"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h6a2 2 0 012 2v1"
                                    />
                                </svg>

                                Keluar dari aplikasi
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </header>

        <main class="mobile-content">
            @yield('content')
        </main>

        <nav
            class="safe-bottom fixed inset-x-0 bottom-0 z-40 border-t border-slate-100 bg-white/95 backdrop-blur-xl"
        >
            <div
                class="mx-auto grid h-[72px] max-w-lg grid-cols-5 items-center px-2"
            >
                <a
                    href="{{ route('dashboard') }}"
                    class="flex flex-col items-center justify-center gap-1 text-indigo-600"
                >
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M3 12l9-9 9 9M5 10v10h14V10M9 20v-6h6v6"
                        />
                    </svg>

                    <span class="text-[10px] font-bold">
                        Beranda
                    </span>
                </a>

                <a href="{{ auth()->user()->hasRole(['admin', 'wali_kelas', 'guru_bk']) ? route('students.index') : route('student-profiles.index') }}" class="flex flex-col items-center justify-center gap-1 text-slate-400">
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H2v-2a4 4 0 014-4h3m4 6h4v-2a4 4 0 00-4-4h-2a4 4 0 00-4 4v2h6zM8 11a4 4 0 100-8 4 4 0 000 8zm8 0a3 3 0 100-6 3 3 0 000 6z"
                        />
                    </svg>

                    <span class="text-[10px] font-semibold">
                        Siswa
                    </span>
                </a>

                <a href="{{ auth()->user()->isKepalaSekolah() ? route('reports.index') : route('development-notes.index') }}" aria-label="Buka catatan kesiswaan" class="-mt-8 flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-500 text-white shadow-xl shadow-indigo-200">
                    <svg
                        class="h-7 w-7"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2.5"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M12 4v16m8-8H4"
                        />
                    </svg>
                </a>

                <a href="{{ route('reports.index') }}" class="flex flex-col items-center justify-center gap-1 text-slate-400">
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M9 17v-6m4 6V7m4 10V4M5 20h14"
                        />
                    </svg>

                    <span class="text-[10px] font-semibold">
                        Laporan
                    </span>
                </a>

                <a href="{{ route('profile.edit') }}" class="flex flex-col items-center justify-center gap-1 text-slate-400">
                    <svg
                        class="h-5 w-5"
                        fill="none"
                        viewBox="0 0 24 24"
                        stroke="currentColor"
                        stroke-width="2"
                    >
                        <path
                            stroke-linecap="round"
                            stroke-linejoin="round"
                            d="M5.121 17.804A9 9 0 1118.879 17.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                        />
                    </svg>

                    <span class="text-[10px] font-semibold">
                        Profil
                    </span>
                </a>
            </div>
        </nav>
    </div>

    <x-sweet-alert />

    @livewireScripts
    @stack('scripts')
</body>
</html>
