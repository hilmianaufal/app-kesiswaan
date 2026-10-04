<div>
    @php
        $canCreate = ! auth()->user()->isKepalaSekolah();

        $canVerify = auth()->user()->isAdmin()
            || auth()->user()->isKepalaSekolah();
    @endphp

    <div class="space-y-5">
        <section class="overflow-hidden rounded-[28px] bg-gradient-to-br from-amber-500 via-orange-500 to-rose-500 p-5 text-white shadow-xl shadow-orange-200 sm:p-7">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <a
                            href="{{ route('dashboard') }}"
                            class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white/15 backdrop-blur transition hover:bg-white/25"
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
                                    d="M15 19l-7-7 7-7"
                                />
                            </svg>
                        </a>

                        <div>
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-orange-100">
                                Penghargaan Siswa
                            </p>

                            <h2 class="mt-1 text-2xl font-extrabold">
                                Prestasi dan Poin
                            </h2>
                        </div>
                    </div>

                    <p class="mt-4 max-w-xl text-sm leading-6 text-orange-100">
                        Catat prestasi akademik dan nonakademik serta
                        hitung poin penghargaan secara otomatis.
                    </p>
                </div>

                @if ($canCreate)
                    <button
                        type="button"
                        wire:click="openCreateModal"
                        class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-white px-5 text-sm font-bold text-orange-600 shadow-lg"
                    >
                        <svg
                            class="h-5 w-5"
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

                        Tambah Prestasi
                    </button>
                @endif
            </div>
        </section>

        <section class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            <article class="rounded-3xl border border-slate-100 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold text-slate-500">
                    Total prestasi
                </p>

                <p class="mt-2 text-2xl font-extrabold text-slate-900">
                    {{ number_format($statistics['total']) }}
                </p>
            </article>

            <article class="rounded-3xl border border-emerald-100 bg-emerald-50 p-4">
                <p class="text-xs font-semibold text-emerald-600">
                    Terverifikasi
                </p>

                <p class="mt-2 text-2xl font-extrabold text-emerald-700">
                    {{ number_format($statistics['verified']) }}
                </p>
            </article>

            <article class="rounded-3xl border border-amber-100 bg-amber-50 p-4">
                <p class="text-xs font-semibold text-amber-600">
                    Menunggu verifikasi
                </p>

                <p class="mt-2 text-2xl font-extrabold text-amber-700">
                    {{ number_format($statistics['pending']) }}
                </p>
            </article>

            <article class="rounded-3xl border border-orange-100 bg-orange-50 p-4">
                <p class="text-xs font-semibold text-orange-600">
                    Total poin
                </p>

                <p class="mt-2 text-2xl font-extrabold text-orange-700">
                    {{ number_format($statistics['points']) }}
                </p>
            </article>
        </section>

        <section class="rounded-[28px] border border-slate-100 bg-white p-4 shadow-sm sm:p-5">
            <div class="relative">
                <svg
                    class="pointer-events-none absolute left-4 top-1/2 h-5 w-5 -translate-y-1/2 text-slate-400"
                    fill="none"
                    viewBox="0 0 24 24"
                    stroke="currentColor"
                    stroke-width="2"
                >
                    <path
                        stroke-linecap="round"
                        stroke-linejoin="round"
                        d="M21 21l-4.35-4.35m1.35-5.65a7 7 0 11-14 0 7 7 0 0114 0z"
                    />
                </svg>

                <input
                    type="search"
                    wire:model.live.debounce.300ms="search"
                    placeholder="Cari siswa, NIS, nama prestasi, kegiatan, atau pembimbing..."
                    class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 pl-12 pr-12 text-sm outline-none focus:border-orange-500 focus:bg-white focus:ring-4 focus:ring-orange-100"
                >

                <div
                    wire:loading
                    wire:target="search"
                    class="absolute right-4 top-1/2 -translate-y-1/2"
                >
                    <svg
                        class="h-5 w-5 animate-spin text-orange-600"
                        viewBox="0 0 24 24"
                        fill="none"
                    >
                        <circle
                            class="opacity-25"
                            cx="12"
                            cy="12"
                            r="10"
                            stroke="currentColor"
                            stroke-width="4"
                        ></circle>

                        <path
                            class="opacity-75"
                            fill="currentColor"
                            d="M4 12a8 8 0 018-8v4a4 4 0 00-4 4H4z"
                        ></path>
                    </svg>
                </div>
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
                <select
                    wire:model.live="filterClassId"
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-100"
                >
                    <option value="">Semua kelas</option>

                    @foreach ($schoolClasses as $class)
                        <option value="{{ $class->id }}">
                            {{ $class->name }} — {{ $class->academic_year }}
                        </option>
                    @endforeach
                </select>

                <select
                    wire:model.live="filterCategory"
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-100"
                >
                    <option value="">Semua kategori</option>

                    @foreach ($categories as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <select
                    wire:model.live="filterLevel"
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-100"
                >
                    <option value="">Semua tingkat</option>

                    @foreach ($levels as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <select
                    wire:model.live="filterStatus"
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-100"
                >
                    <option value="">Semua status</option>

                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="mt-3 grid gap-3 sm:grid-cols-3">
                <input
                    type="date"
                    wire:model.live="filterDateFrom"
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-100"
                >

                <input
                    type="date"
                    wire:model.live="filterDateTo"
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-100"
                >

                <button
                    type="button"
                    wire:click="resetFilters"
                    class="h-12 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-600"
                >
                    Reset Filter
                </button>
            </div>
        </section>

        @if ($topStudents->isNotEmpty())
            <section>
                <div class="mb-4">
                    <h3 class="text-lg font-extrabold text-slate-900">
                        Peringkat Prestasi Siswa
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Berdasarkan poin prestasi yang telah diverifikasi
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                    @foreach ($topStudents as $index => $item)
                        <article class="rounded-3xl border border-amber-100 bg-white p-4 shadow-sm">
                            <div class="flex items-center justify-between">
                                <span class="flex h-9 w-9 items-center justify-center rounded-2xl bg-amber-50 text-xs font-extrabold text-amber-600">
                                    {{ $index + 1 }}
                                </span>

                                <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-extrabold text-orange-600">
                                    {{ $item->total_points }} poin
                                </span>
                            </div>

                            <h4 class="mt-4 truncate text-sm font-extrabold text-slate-900">
                                {{ $item->student?->name ?? 'Siswa dihapus' }}
                            </h4>

                            <p class="mt-1 text-xs text-slate-500">
                                {{ $item->student?->schoolClass?->name ?? 'Tanpa kelas' }}
                            </p>

                            <p class="mt-3 text-[11px] font-semibold text-emerald-600">
                                {{ $item->total_achievements }} prestasi
                            </p>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        <section>
            <div class="mb-4">
                <h3 class="text-lg font-extrabold text-slate-900">
                    Riwayat Prestasi
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Ditemukan {{ $achievements->total() }} prestasi
                </p>
            </div>

            <div
                wire:loading.class="opacity-50"
                wire:target="search,filterClassId,filterCategory,filterLevel,filterStatus,filterDateFrom,filterDateTo"
                class="space-y-4"
            >
                @forelse ($achievements as $achievement)
                    @php
                        $canModify = auth()->user()->isAdmin()
                            || auth()->user()->isGuruBk()
                            || $achievement->recorded_by === auth()->id();
                    @endphp

                    <article
                        wire:key="achievement-{{ $achievement->id }}"
                        class="rounded-[28px] border border-slate-100 bg-white p-5 shadow-sm transition hover:shadow-lg"
                    >
                        <div class="flex items-start gap-4">
                            @if ($achievement->student->photo_url)
                                <img
                                    src="{{ $achievement->student->photo_url }}"
                                    alt="{{ $achievement->student->name }}"
                                    class="h-14 w-14 shrink-0 rounded-2xl object-cover"
                                >
                            @else
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-amber-50 text-lg font-extrabold text-amber-600">
                                    {{ $achievement->student->initial }}
                                </div>
                            @endif

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <div>
                                        <h4 class="text-sm font-extrabold text-slate-900">
                                            {{ $achievement->student->name }}
                                        </h4>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $achievement->student->schoolClass?->name ?? 'Tanpa kelas' }}
                                            · NIS {{ $achievement->student->nis }}
                                        </p>
                                    </div>

                                    <span class="rounded-full bg-orange-50 px-3 py-1 text-xs font-extrabold text-orange-600">
                                        +{{ $achievement->points }} poin
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <span class="rounded-full bg-indigo-50 px-3 py-1 text-[10px] font-bold text-indigo-600">
                                {{ $achievement->category_label }}
                            </span>

                            <span class="rounded-full bg-violet-50 px-3 py-1 text-[10px] font-bold text-violet-600">
                                {{ $achievement->level_label }}
                            </span>

                            <span class="rounded-full bg-amber-50 px-3 py-1 text-[10px] font-bold text-amber-700">
                                {{ $achievement->rank_label }}
                            </span>

                            <span
                                @class([
                                    'rounded-full px-3 py-1 text-[10px] font-bold',
                                    'bg-amber-50 text-amber-700' => $achievement->status === 'pending',
                                    'bg-emerald-50 text-emerald-700' => $achievement->status === 'verified',
                                    'bg-red-50 text-red-700' => $achievement->status === 'rejected',
                                ])
                            >
                                {{ $achievement->status_label }}
                            </span>
                        </div>

                        <h5 class="mt-4 text-base font-extrabold text-slate-900">
                            {{ $achievement->title }}
                        </h5>

                        <p class="mt-2 text-xs font-semibold text-slate-500">
                            {{ $achievement->achievement_date->translatedFormat('d M Y') }}
                        </p>

                        @if ($achievement->event_name)
                            <p class="mt-3 text-sm font-semibold text-slate-700">
                                Kegiatan: {{ $achievement->event_name }}
                            </p>
                        @endif

                        @if ($achievement->organizer)
                            <p class="mt-1 text-xs text-slate-500">
                                Penyelenggara: {{ $achievement->organizer }}
                            </p>
                        @endif

                        @if ($achievement->mentor)
                            <p class="mt-1 text-xs text-slate-500">
                                Pembimbing: {{ $achievement->mentor }}
                            </p>
                        @endif

                        @if ($achievement->description)
                            <p class="mt-4 whitespace-pre-line rounded-2xl bg-slate-50 p-4 text-sm leading-6 text-slate-600">
                                {{ $achievement->description }}
                            </p>
                        @endif

                        @if ($achievement->certificate_url)
                            <a
                                href="{{ $achievement->certificate_url }}"
                                target="_blank"
                                class="mt-4 flex h-11 items-center justify-center rounded-2xl border border-orange-100 bg-orange-50 text-xs font-bold text-orange-600"
                            >
                                Lihat Sertifikat
                            </a>
                        @endif

                        @if ($achievement->status === 'verified')
                            <div class="mt-4 rounded-2xl bg-emerald-50 p-3">
                                <p class="text-xs font-bold text-emerald-700">
                                    Diverifikasi oleh
                                    {{ $achievement->verifier?->name ?? 'Administrator' }}
                                </p>

                                @if ($achievement->verified_at)
                                    <p class="mt-1 text-[10px] text-emerald-600">
                                        {{ $achievement->verified_at->translatedFormat('d M Y H:i') }}
                                    </p>
                                @endif
                            </div>
                        @endif

                        <div class="mt-4 border-t border-slate-100 pt-4">
                            @if (
                                $canVerify
                                && $achievement->status === 'pending'
                            )
                                <div class="mb-3 grid grid-cols-2 gap-2">
                                    <button
                                        type="button"
                                        wire:click="verifyAchievement({{ $achievement->id }})"
                                        class="h-11 rounded-2xl bg-emerald-600 text-xs font-bold text-white"
                                    >
                                        Verifikasi
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="rejectAchievement({{ $achievement->id }})"
                                        class="h-11 rounded-2xl bg-red-50 text-xs font-bold text-red-600"
                                    >
                                        Tolak
                                    </button>
                                </div>
                            @endif

                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-[10px] text-slate-400">
                                        Dicatat oleh
                                    </p>

                                    <p class="mt-1 text-xs font-bold text-slate-600">
                                        {{ $achievement->recorder?->name ?? 'Pengguna dihapus' }}
                                    </p>
                                </div>

                                @if ($canModify)
                                    <div class="flex gap-2">
                                        <button
                                            type="button"
                                            wire:click="edit({{ $achievement->id }})"
                                            class="h-10 rounded-2xl bg-indigo-50 px-4 text-xs font-bold text-indigo-600"
                                        >
                                            Edit
                                        </button>

                                        <button
                                            type="button"
                                            x-on:click="
                                                confirmDelete({
                                                    title: 'Hapus prestasi?',
                                                    text: @js('Prestasi '.$achievement->student->name.' akan dihapus.')
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        $wire.deleteAchievement({{ $achievement->id }});
                                                    }
                                                })
                                            "
                                            class="h-10 rounded-2xl bg-red-50 px-4 text-xs font-bold text-red-600"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                @endif
                            </div>
                        </div>
                    </article>
                @empty
                    <div class="rounded-[28px] border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-amber-50 text-amber-600">
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
                                    d="M8 21h8m-4-4v4M7 4h10v4a5 5 0 01-10 0V4zM7 6H4v2a4 4 0 004 4m9-6h3v2a4 4 0 01-4 4"
                                />
                            </svg>
                        </div>

                        <h4 class="mt-5 text-base font-extrabold text-slate-900">
                            Prestasi belum ditemukan
                        </h4>

                        <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">
                            Tambahkan prestasi baru atau ubah pencarian
                            dan filter yang sedang digunakan.
                        </p>

                        @if ($canCreate)
                            <button
                                type="button"
                                wire:click="openCreateModal"
                                class="mt-5 rounded-2xl bg-orange-500 px-5 py-3 text-sm font-bold text-white"
                            >
                                Tambah Prestasi
                            </button>
                        @endif
                    </div>
                @endforelse
            </div>

            @if ($achievements->hasPages())
                <div class="mt-6 rounded-2xl bg-white p-4 shadow-sm">
                    {{ $achievements->links() }}
                </div>
            @endif
        </section>
    </div>

    <x-smooth-modal
        name="achievement-form"
        :title="$editingId ? 'Edit Prestasi Siswa' : 'Tambah Prestasi Siswa'"
        max-width="2xl"
    >
        <form wire:submit="save" class="space-y-5">
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Siswa
                </label>

                <select
                    wire:model="studentId"
                    class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-100"
                >
                    <option value="">Pilih siswa</option>

                    @foreach ($students as $student)
                        <option value="{{ $student->id }}">
                            {{ $student->name }}
                            — {{ $student->schoolClass?->name ?? 'Tanpa kelas' }}
                            — {{ $student->nis }}
                        </option>
                    @endforeach
                </select>

                @error('studentId')
                    <p class="mt-2 text-xs font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Nama prestasi
                </label>

                <input
                    type="text"
                    wire:model="title"
                    placeholder="Contoh: Juara Olimpiade Matematika"
                    class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-100"
                >

                @error('title')
                    <p class="mt-2 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Kategori
                    </label>

                    <select
                        wire:model="category"
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-100"
                    >
                        @foreach ($categories as $value => $label)
                            <option value="{{ $value }}">
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Tingkat prestasi
                    </label>

                    <select
                        wire:model.live="level"
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-100"
                    >
                        @foreach ($levels as $value => $label)
                            <option value="{{ $value }}">
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Peringkat
                    </label>

                    <select
                        wire:model.live="rank"
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-100"
                    >
                        @foreach ($ranks as $value => $label)
                            <option value="{{ $value }}">
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Poin penghargaan
                    </label>

                    <div class="flex h-14 items-center rounded-2xl border border-orange-100 bg-orange-50 px-4">
                        <span class="text-lg font-extrabold text-orange-600">
                            {{ $points }} poin
                        </span>
                    </div>

                    <p class="mt-2 text-[10px] text-slate-500">
                        Poin dihitung otomatis dari tingkat dan peringkat.
                    </p>
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Nama kegiatan
                    </label>

                    <input
                        type="text"
                        wire:model="eventName"
                        placeholder="Nama lomba atau kegiatan"
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-100"
                    >
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Penyelenggara
                    </label>

                    <input
                        type="text"
                        wire:model="organizer"
                        placeholder="Nama instansi penyelenggara"
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-100"
                    >
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Pembimbing
                    </label>

                    <input
                        type="text"
                        wire:model="mentor"
                        placeholder="Nama guru pembimbing"
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-100"
                    >
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Tanggal prestasi
                    </label>

                    <input
                        type="date"
                        wire:model="achievementDate"
                        max="{{ now()->format('Y-m-d') }}"
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-100"
                    >

                    @error('achievementDate')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Deskripsi
                </label>

                <textarea
                    wire:model="description"
                    rows="3"
                    placeholder="Keterangan tambahan mengenai prestasi..."
                    class="w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-orange-500 focus:ring-4 focus:ring-orange-100"
                ></textarea>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Sertifikat
                </label>

                <label class="flex cursor-pointer items-center justify-center rounded-2xl border border-dashed border-orange-300 bg-orange-50 px-4 py-4 text-xs font-bold text-orange-600">
                    Pilih Sertifikat

                    <input
                        type="file"
                        wire:model="certificate"
                        accept=".pdf,.jpg,.jpeg,.png,.webp"
                        class="hidden"
                    >
                </label>

                <div wire:loading wire:target="certificate">
                    <p class="mt-2 text-xs font-semibold text-orange-600">
                        Mengunggah sertifikat...
                    </p>
                </div>

                @if ($certificate)
                    <div class="mt-3 rounded-2xl bg-emerald-50 p-3">
                        <p class="truncate text-xs font-bold text-emerald-700">
                            {{ $certificate->getClientOriginalName() }}
                        </p>

                        <button
                            type="button"
                            wire:click="cancelNewCertificate"
                            class="mt-2 text-xs font-bold text-red-600"
                        >
                            Batalkan file baru
                        </button>
                    </div>
                @elseif ($existingCertificate)
                    <div class="mt-3 rounded-2xl bg-indigo-50 p-3">
                        <p class="text-xs font-bold text-indigo-700">
                            Sertifikat lama masih tersimpan
                        </p>

                        <button
                            type="button"
                            wire:click="removeCurrentCertificate"
                            class="mt-2 text-xs font-bold text-red-600"
                        >
                            Hapus sertifikat
                        </button>
                    </div>
                @endif

                <p class="mt-2 text-[10px] text-slate-500">
                    PDF, JPG, PNG, atau WEBP. Maksimal 5 MB.
                </p>

                @error('certificate')
                    <p class="mt-2 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-3 border-t border-slate-100 pt-5">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal', { modal: 'achievement-form' })"
                    class="h-14 rounded-2xl border border-slate-200 bg-white text-sm font-bold text-slate-600"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="save,certificate"
                    class="flex h-14 items-center justify-center rounded-2xl bg-gradient-to-r from-amber-500 to-orange-500 text-sm font-bold text-white shadow-lg shadow-orange-200 disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="save">
                        {{ $editingId ? 'Simpan Perubahan' : 'Tambah Prestasi' }}
                    </span>

                    <span wire:loading wire:target="save">
                        Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </x-smooth-modal>
</div>
