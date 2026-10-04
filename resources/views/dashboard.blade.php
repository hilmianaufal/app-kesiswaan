@extends('layouts.mobile')

@section('title', 'Dashboard')

@section('content')
@php
    $hour = now()->hour;

    $greeting = match (true) {
        $hour < 11 => 'Selamat pagi',
        $hour < 15 => 'Selamat siang',
        $hour < 18 => 'Selamat sore',
        default => 'Selamat malam',
    };
@endphp

<div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 sm:py-8">
    <section
        class="relative overflow-hidden rounded-[28px] bg-gradient-to-br from-indigo-600 via-indigo-600 to-violet-500 p-6 text-white shadow-xl shadow-indigo-200"
    >
        <div
            class="absolute -right-12 -top-12 h-44 w-44 rounded-full bg-white/10"
        ></div>

        <div
            class="absolute -bottom-16 -left-10 h-44 w-44 rounded-full bg-white/10"
        ></div>

        <div class="relative">
            <p class="text-sm font-medium text-indigo-100">
                {{ $greeting }},
            </p>

            <h2 class="mt-1 text-2xl font-extrabold">
                {{ auth()->user()->name }}
            </h2>

            <p class="mt-2 max-w-md text-sm leading-6 text-indigo-100">
                Pantau perkembangan, kedisiplinan, absensi, dan catatan
                kesiswaan dengan lebih mudah.
            </p>

            <div class="mt-5 flex items-center gap-3">
                <span
                    class="rounded-full bg-white/15 px-4 py-2 text-xs font-bold backdrop-blur"
                >
                    {{ auth()->user()->role_label }}
                </span>

                <span
                    class="rounded-full bg-emerald-400/20 px-4 py-2 text-xs font-bold text-emerald-100 backdrop-blur"
                >
                    Akun aktif
                </span>
            </div>
        </div>
    </section>

    <section class="mt-7">
        <div class="mb-4 flex items-center justify-between">
            <div>
                <h3 class="text-lg font-extrabold text-slate-900">
                    Ringkasan hari ini
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Jumlah siswa dan catatan hari ini dari database
                </p>
            </div>

            <span
                class="rounded-full bg-white px-3 py-2 text-[10px] font-bold text-slate-500 shadow-sm"
            >
                {{ now()->translatedFormat('d M Y') }}
            </span>
        </div>

        <div class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            <article
                class="rounded-3xl border border-slate-100 bg-white p-4 shadow-sm"
            >
                <div
                    class="flex h-11 w-11 items-center justify-center rounded-2xl bg-blue-50 text-blue-600"
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
                            d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H2v-2a4 4 0 014-4h3m4 6h4v-2a4 4 0 00-4-4h-2a4 4 0 00-4 4v2h6zM8 11a4 4 0 100-8 4 4 0 000 8z"
                        />
                    </svg>
                </div>

                <p class="mt-4 text-2xl font-extrabold text-slate-900">
                    {{ $statistics['students'] }}
                </p>

                <p class="mt-1 text-xs font-medium text-slate-500">
                    Total siswa
                </p>
            </article>

            <article
                class="rounded-3xl border border-slate-100 bg-white p-4 shadow-sm"
            >
                <div
                    class="flex h-11 w-11 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600"
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
                            d="M5 13l4 4L19 7"
                        />
                    </svg>
                </div>

                <p class="mt-4 text-2xl font-extrabold text-slate-900">
                    {{ $statistics['present'] }}
                </p>

                <p class="mt-1 text-xs font-medium text-slate-500">
                    Hadir
                </p>
            </article>

            <article
                class="rounded-3xl border border-slate-100 bg-white p-4 shadow-sm"
            >
                <div
                    class="flex h-11 w-11 items-center justify-center rounded-2xl bg-amber-50 text-amber-600"
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
                            d="M12 8v4l3 2m6-2a9 9 0 11-18 0 9 9 0 0118 0z"
                        />
                    </svg>
                </div>

                <p class="mt-4 text-2xl font-extrabold text-slate-900">
                    {{ $statistics['late'] }}
                </p>

                <p class="mt-1 text-xs font-medium text-slate-500">
                    Terlambat
                </p>
            </article>

            <article
                class="rounded-3xl border border-slate-100 bg-white p-4 shadow-sm"
            >
                <div
                    class="flex h-11 w-11 items-center justify-center rounded-2xl bg-red-50 text-red-600"
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
                            d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"
                        />
                    </svg>
                </div>

                <p class="mt-4 text-2xl font-extrabold text-slate-900">
                    {{ $statistics['violations'] }}
                </p>

                <p class="mt-1 text-xs font-medium text-slate-500">
                    Pelanggaran
                </p>
            </article>
        </div>
    </section>

    @if (auth()->user()->isAdmin())
    <section class="mt-7">
        <a
            href="{{ route('classes.index') }}"
            class="flex items-center justify-between rounded-[28px] border border-indigo-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-xl"
        >
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M3 21h18M6 21V7l6-4 6 4v14M9 10h.01M9 14h.01M15 10h.01M15 14h.01" />
                    </svg>
                </div>

                <div>
                    <h3 class="text-base font-extrabold text-slate-900">
                        Kelola Data Kelas
                    </h3>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Tambah, edit, cari, dan atur wali kelas
                    </p>
                </div>
            </div>

            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </section>
    @endif

    <section class="mt-7">
        <a
            href="{{ route('attendances.index') }}"
            class="flex items-center justify-between rounded-[28px] border border-emerald-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-xl"
        >
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                    <svg class="h-7 w-7" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                        <path stroke-linecap="round" stroke-linejoin="round" d="M9 11l3 3L22 4M21 12v7a2 2 0 01-2 2H5a2 2 0 01-2-2V5a2 2 0 012-2h11" />
                    </svg>
                </div>

                <div>
                    <h3 class="text-base font-extrabold text-slate-900">
                        Absensi Siswa
                    </h3>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Input kehadiran harian secara massal
                    </p>
                </div>
            </div>

            <svg class="h-5 w-5 text-slate-400" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M9 5l7 7-7 7" />
            </svg>
        </a>
    </section>

    @if (! auth()->user()->isKepalaSekolah())
    <section class="mt-7">
        <a
            href="{{ route('development-notes.index') }}"
            class="flex items-center justify-between rounded-[28px] border border-violet-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-xl"
        >
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-violet-50 text-violet-600">
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
                            d="M9 12h6m-6 4h6M9 8h2m-5 13h12a2 2 0 002-2V5a2 2 0 00-2-2H6a2 2 0 00-2 2v14a2 2 0 002 2z"
                        />
                    </svg>
                </div>

                <div>
                    <h3 class="text-base font-extrabold text-slate-900">
                        Catatan Perkembangan
                    </h3>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Catat akademik, perilaku, dan tindak lanjut siswa
                    </p>
                </div>
            </div>

            <svg
                class="h-5 w-5 text-slate-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 5l7 7-7 7"
                />
            </svg>
        </a>
    </section>
    @endif

    <section class="mt-7">
        <a
            href="{{ route('violations.index') }}"
            class="flex items-center justify-between rounded-[28px] border border-red-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-xl"
        >
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50 text-red-600">
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
                            d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"
                        />
                    </svg>
                </div>

                <div>
                    <h3 class="text-base font-extrabold text-slate-900">
                        Pelanggaran dan Poin
                    </h3>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Catat pelanggaran, poin, dan tindak lanjut siswa
                    </p>
                </div>
            </div>

            <svg
                class="h-5 w-5 text-slate-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 5l7 7-7 7"
                />
            </svg>
        </a>
    </section>

    <section class="mt-7">
        <a
            href="{{ route('achievements.index') }}"
            class="flex items-center justify-between rounded-[28px] border border-amber-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-xl"
        >
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
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
                            d="M8 21h8m-4-4v4M7 4h10v4a5 5 0 01-10 0V4zM7 6H4v2a4 4 0 004 4m9-6h3v2a4 4 0 01-4 4"
                        />
                    </svg>
                </div>

                <div>
                    <h3 class="text-base font-extrabold text-slate-900">
                        Prestasi dan Poin
                    </h3>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Catat prestasi dan poin penghargaan siswa
                    </p>
                </div>
            </div>

            <svg
                class="h-5 w-5 text-slate-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 5l7 7-7 7"
                />
            </svg>
        </a>
    </section>

    <section class="mt-7">
        <a
            href="{{ route('student-permissions.index') }}"
            class="flex items-center justify-between rounded-[28px] border border-sky-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-xl"
        >
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-sky-50 text-sky-600">
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
                            d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v14l-4-3-3 3-3-3-4 3V6a2 2 0 012-2z"
                        />
                    </svg>
                </div>

                <div>
                    <h3 class="text-base font-extrabold text-slate-900">
                        Perizinan Siswa
                    </h3>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Kelola izin tidak masuk, keluar, dan pulang awal
                    </p>
                </div>
            </div>

            <svg
                class="h-5 w-5 text-slate-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 5l7 7-7 7"
                />
            </svg>
        </a>
    </section>

    <section class="mt-7">
        <a
            href="{{ route('counseling.index') }}"
            class="flex items-center justify-between rounded-[28px] border border-indigo-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-xl"
        >
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-indigo-50 text-indigo-600">
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
                            d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H2v-2a4 4 0 014-4h3m4 6h4v-2a4 4 0 00-4-4h-2a4 4 0 00-4 4v2h6zM8 11a4 4 0 100-8 4 4 0 000 8z"
                        />
                    </svg>
                </div>

                <div>
                    <h3 class="text-base font-extrabold text-slate-900">
                        Bimbingan Konseling
                    </h3>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Kelola kasus, pertemuan, dan tindak lanjut siswa
                    </p>
                </div>
            </div>

            <svg
                class="h-5 w-5 text-slate-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 5l7 7-7 7"
                />
            </svg>
        </a>
    </section>

    <section class="mt-7">
        <a
            href="{{ route('student-profiles.index') }}"
            class="flex items-center justify-between rounded-[28px] border border-indigo-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-xl"
        >
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-indigo-600 to-violet-600 text-white">
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
                            d="M5.121 17.804A9 9 0 1118.879 17.804M15 11a3 3 0 11-6 0 3 3 0 016 0z"
                        />
                    </svg>
                </div>

                <div>
                    <h3 class="text-base font-extrabold text-slate-900">
                        Profil Siswa 360°
                    </h3>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Monitoring seluruh riwayat dan perkembangan siswa
                    </p>
                </div>
            </div>

            <svg
                class="h-5 w-5 text-slate-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 5l7 7-7 7"
                />
            </svg>
        </a>
    </section>

    <section class="mt-7">
        <a
            href="{{ route('reports.index') }}"
            class="flex items-center justify-between rounded-[28px] border border-blue-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-xl"
        >
            <div class="flex items-center gap-4">
                <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-gradient-to-br from-blue-600 to-indigo-600 text-white">
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
                            d="M9 17v-6m4 6V7m4 10v-3M5 21h14a2 2 0 002-2V5a2 2 0 00-2-2H5a2 2 0 00-2 2v14a2 2 0 002 2z"
                        />
                    </svg>
                </div>

                <div>
                    <h3 class="text-base font-extrabold text-slate-900">
                        Laporan Kesiswaan
                    </h3>

                    <p class="mt-1 text-xs leading-5 text-slate-500">
                        Export laporan dan profil siswa dalam format PDF
                    </p>
                </div>
            </div>

            <svg
                class="h-5 w-5 text-slate-400"
                fill="none"
                viewBox="0 0 24 24"
                stroke="currentColor"
                stroke-width="2"
            >
                <path
                    stroke-linecap="round"
                    stroke-linejoin="round"
                    d="M9 5l7 7-7 7"
                />
            </svg>
        </a>
    </section>

    <section class="mt-7">
        <h3 class="text-lg font-extrabold text-slate-900">
            Menu utama
        </h3>

        <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3 lg:grid-cols-4">
            @php
                $menus = [
                    [
                        'name' => 'Data Siswa',
                        'description' => 'Kelola identitas siswa',
                        'class' => 'bg-blue-50 text-blue-600',
                    ],
                    [
                        'name' => 'Data Kelas',
                        'description' => 'Kelas dan wali kelas',
                        'class' => 'bg-violet-50 text-violet-600',
                    ],
                    [
                        'name' => 'Absensi',
                        'description' => 'Kehadiran harian',
                        'class' => 'bg-emerald-50 text-emerald-600',
                    ],
                    [
                        'name' => 'Pelanggaran',
                        'description' => 'Catatan dan poin',
                        'class' => 'bg-red-50 text-red-600',
                    ],
                    [
                        'name' => 'Prestasi',
                        'description' => 'Prestasi siswa',
                        'class' => 'bg-amber-50 text-amber-600',
                    ],
                    [
                        'name' => 'Konseling',
                        'description' => 'Bimbingan siswa',
                        'class' => 'bg-cyan-50 text-cyan-600',
                    ],
                    [
                        'name' => 'Perizinan',
                        'description' => 'Izin siswa',
                        'class' => 'bg-orange-50 text-orange-600',
                    ],
                    [
                        'name' => 'Laporan',
                        'description' => 'Rekap dan cetak',
                        'class' => 'bg-slate-100 text-slate-600',
                    ],
                ];
            @endphp

            @foreach ($menus as $menu)
                <button
                    type="button"
                    x-on:click="Swal.fire({
                        icon: 'info',
                        title: '{{ $menu['name'] }}',
                        text: 'Fitur ini akan dibuat pada tahap berikutnya.',
                        confirmButtonColor: '#4f46e5'
                    })"
                    class="group rounded-3xl border border-slate-100 bg-white p-4 text-left shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-lg"
                >
                    <div
                        class="flex h-12 w-12 items-center justify-center rounded-2xl {{ $menu['class'] }}"
                    >
                        <svg
                            class="h-6 w-6"
                            fill="none"
                            viewBox="0 0 24 24"
                            stroke="currentColor"
                            stroke-width="2"
                        >
                            <path
                                stroke-linecap="round"
                                stroke-linejoin="round"
                                d="M12 4v16m8-8H4"
                            />
                        </svg>
                    </div>

                    <h4 class="mt-4 text-sm font-bold text-slate-900">
                        {{ $menu['name'] }}
                    </h4>

                    <p class="mt-1 text-[11px] leading-5 text-slate-500">
                        {{ $menu['description'] }}
                    </p>
                </button>
            @endforeach
        </div>
    </section>
</div>
@endsection
