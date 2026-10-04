<div>
    <div class="space-y-5">
        <section class="overflow-hidden rounded-[28px] bg-gradient-to-br from-slate-900 via-indigo-900 to-violet-700 p-5 text-white shadow-xl shadow-indigo-200 sm:p-7">
            <div class="flex items-center gap-3">
                <a
                    href="{{ route('dashboard') }}"
                    class="flex h-10 w-10 items-center justify-center rounded-2xl bg-white/15 transition hover:bg-white/25"
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
                    <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-200">
                        Monitoring Terpadu
                    </p>

                    <h2 class="mt-1 text-2xl font-extrabold">
                        Profil Siswa 360°
                    </h2>
                </div>
            </div>

            <p class="mt-4 max-w-2xl text-sm leading-6 text-indigo-100">
                Lihat seluruh riwayat absensi, perkembangan, pelanggaran,
                prestasi, perizinan, dan bimbingan konseling siswa.
            </p>
        </section>

        <section class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            <article class="rounded-3xl border border-slate-100 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold text-slate-500">
                    Siswa aktif
                </p>

                <p class="mt-2 text-2xl font-extrabold text-slate-900">
                    {{ number_format($statistics['students']) }}
                </p>
            </article>

            <article class="rounded-3xl border border-red-100 bg-red-50 p-4">
                <p class="text-xs font-semibold text-red-600">
                    Perlu perhatian
                </p>

                <p class="mt-2 text-2xl font-extrabold text-red-700">
                    {{ number_format($statistics['needs_attention']) }}
                </p>
            </article>

            <article class="rounded-3xl border border-amber-100 bg-amber-50 p-4">
                <p class="text-xs font-semibold text-amber-600">
                    Prestasi terverifikasi
                </p>

                <p class="mt-2 text-2xl font-extrabold text-amber-700">
                    {{ number_format($statistics['achievements']) }}
                </p>
            </article>

            <article class="rounded-3xl border border-sky-100 bg-sky-50 p-4">
                <p class="text-xs font-semibold text-sky-600">
                    Izin menunggu
                </p>

                <p class="mt-2 text-2xl font-extrabold text-sky-700">
                    {{ number_format($statistics['pending_permissions']) }}
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
                    placeholder="Cari nama siswa, NIS, NISN, atau kelas..."
                    class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 pl-12 pr-12 text-sm outline-none focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                >

                <div
                    wire:loading
                    wire:target="search"
                    class="absolute right-4 top-1/2 -translate-y-1/2"
                >
                    <svg
                        class="h-5 w-5 animate-spin text-indigo-600"
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

            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                <select
                    wire:model.live="filterClassId"
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                >
                    <option value="">Semua kelas</option>

                    @foreach ($schoolClasses as $class)
                        <option value="{{ $class->id }}">
                            {{ $class->name }} — {{ $class->academic_year }}
                        </option>
                    @endforeach
                </select>

                <select
                    wire:model.live="filterCondition"
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                >
                    <option value="">Semua kondisi</option>
                    <option value="needs_attention">Perlu perhatian</option>
                    <option value="has_achievement">Memiliki prestasi</option>
                    <option value="pending_permission">Izin menunggu</option>
                    <option value="clear">Tidak ada catatan perhatian</option>
                </select>

                <button
                    type="button"
                    wire:click="resetFilters"
                    class="h-12 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-600"
                >
                    Reset Filter
                </button>
            </div>
        </section>

        <section>
            <div class="mb-4">
                <h3 class="text-lg font-extrabold text-slate-900">
                    Daftar Profil Siswa
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Ditemukan {{ $students->total() }} siswa
                </p>
            </div>

            <div
                wire:loading.class="opacity-50"
                wire:target="search,filterClassId,filterCondition"
                class="grid gap-4 md:grid-cols-2 xl:grid-cols-3"
            >
                @forelse ($students as $student)
                    @php
                        $violationPoints =
                            (int) ($student->violation_points ?? 0);

                        $achievementPoints =
                            (int) ($student->achievement_points ?? 0);

                        $pointBalance =
                            $achievementPoints - $violationPoints;

                        $needsAttention =
                            $student->violations_count > 0
                            || $student->pending_permissions_count > 0;
                    @endphp

                    <article
                        wire:key="profile-student-{{ $student->id }}"
                        class="rounded-[28px] border border-slate-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-xl"
                    >
                        <div class="flex items-start gap-4">
                            @if ($student->photo_url)
                                <img
                                    src="{{ $student->photo_url }}"
                                    alt="{{ $student->name }}"
                                    class="h-16 w-16 shrink-0 rounded-2xl object-cover"
                                >
                            @else
                                <div class="flex h-16 w-16 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-xl font-extrabold text-indigo-600">
                                    {{ $student->initial }}
                                </div>
                            @endif

                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <h4 class="truncate text-base font-extrabold text-slate-900">
                                            {{ $student->name }}
                                        </h4>

                                        <p class="mt-1 text-xs text-slate-500">
                                            NIS {{ $student->nis }}
                                        </p>

                                        <p class="mt-2 text-xs font-bold text-indigo-600">
                                            {{ $student->schoolClass?->name ?? 'Belum memiliki kelas' }}
                                        </p>
                                    </div>

                                    @if ($needsAttention)
                                        <span class="shrink-0 rounded-full bg-red-50 px-3 py-1 text-[10px] font-bold text-red-600">
                                            Perhatian
                                        </span>
                                    @else
                                        <span class="shrink-0 rounded-full bg-emerald-50 px-3 py-1 text-[10px] font-bold text-emerald-600">
                                            Terpantau
                                        </span>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-3 gap-2 text-center">
                            <div class="rounded-2xl bg-emerald-50 p-3">
                                <p class="text-lg font-extrabold text-emerald-700">
                                    {{ $student->verified_achievements_count }}
                                </p>

                                <p class="mt-1 text-[9px] font-semibold text-emerald-600">
                                    Prestasi
                                </p>
                            </div>

                            <div class="rounded-2xl bg-red-50 p-3">
                                <p class="text-lg font-extrabold text-red-700">
                                    {{ $student->violations_count }}
                                </p>

                                <p class="mt-1 text-[9px] font-semibold text-red-600">
                                    Pelanggaran
                                </p>
                            </div>

                            <div class="rounded-2xl bg-violet-50 p-3">
                                <p class="text-lg font-extrabold text-violet-700">
                                    {{ $student->counseling_cases_count }}
                                </p>

                                <p class="mt-1 text-[9px] font-semibold text-violet-600">
                                    Konseling
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 flex items-center justify-between rounded-2xl bg-slate-50 p-4">
                            <div>
                                <p class="text-[10px] font-semibold uppercase text-slate-400">
                                    Saldo Poin
                                </p>

                                <p
                                    @class([
                                        'mt-1 text-lg font-extrabold',
                                        'text-emerald-600' => $pointBalance >= 0,
                                        'text-red-600' => $pointBalance < 0,
                                    ])
                                >
                                    {{ $pointBalance > 0 ? '+' : '' }}{{ $pointBalance }}
                                </p>
                            </div>

                            <div class="text-right">
                                <p class="text-[10px] text-slate-400">
                                    Absensi tercatat
                                </p>

                                <p class="mt-1 text-sm font-bold text-slate-700">
                                    {{ $student->attendances_count }} hari
                                </p>
                            </div>
                        </div>

                        <button
                            type="button"
                            wire:click="openProfile({{ $student->id }})"
                            class="mt-4 h-12 w-full rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 text-sm font-bold text-white shadow-lg shadow-indigo-100"
                        >
                            Lihat Profil Lengkap
                        </button>
                    </article>
                @empty
                    <div class="md:col-span-2 xl:col-span-3">
                        <div class="rounded-[28px] border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                            <h4 class="text-base font-extrabold text-slate-900">
                                Profil siswa tidak ditemukan
                            </h4>

                            <p class="mt-2 text-sm text-slate-500">
                                Ubah pencarian atau filter yang sedang digunakan.
                            </p>
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($students->hasPages())
                <div class="mt-6 rounded-2xl bg-white p-4 shadow-sm">
                    {{ $students->links() }}
                </div>
            @endif
        </section>
    </div>

    <x-smooth-modal
        name="student-profile-detail"
        title="Profil Lengkap Siswa"
        max-width="2xl"
    >
        @if ($selectedStudent)
            @php
                $pointBalance =
                    (int) $selectedStudent->achievement_points
                    - (int) $selectedStudent->violation_points;

                $attendancePercentage =
                    $selectedStudent->attendances_count > 0
                        ? round(
                            (
                                $selectedStudent->present_count
                                / $selectedStudent->attendances_count
                            ) * 100
                        )
                        : 0;
            @endphp

            <div class="space-y-5">
                <section class="rounded-3xl bg-gradient-to-br from-indigo-600 to-violet-600 p-5 text-white">
                    <div class="flex items-center gap-4">
                        @if ($selectedStudent->photo_url)
                            <img
                                src="{{ $selectedStudent->photo_url }}"
                                alt="{{ $selectedStudent->name }}"
                                class="h-20 w-20 shrink-0 rounded-3xl object-cover ring-4 ring-white/20"
                            >
                        @else
                            <div class="flex h-20 w-20 shrink-0 items-center justify-center rounded-3xl bg-white/15 text-2xl font-extrabold">
                                {{ $selectedStudent->initial }}
                            </div>
                        @endif

                        <div class="min-w-0">
                            <h3 class="truncate text-xl font-extrabold">
                                {{ $selectedStudent->name }}
                            </h3>

                            <p class="mt-1 text-sm text-indigo-100">
                                NIS {{ $selectedStudent->nis }}
                                @if ($selectedStudent->nisn)
                                    · NISN {{ $selectedStudent->nisn }}
                                @endif
                            </p>

                            <p class="mt-2 text-sm font-bold">
                                {{ $selectedStudent->schoolClass?->full_name ?? 'Belum memiliki kelas' }}
                            </p>
                        </div>
                    </div>
                </section>

                <div class="overflow-x-auto pb-1">
                    <div class="flex min-w-max gap-2">
                        @foreach ([
                            'overview' => 'Ringkasan',
                            'attendance' => 'Absensi',
                            'development' => 'Perkembangan',
                            'violations' => 'Pelanggaran',
                            'achievements' => 'Prestasi',
                            'permissions' => 'Perizinan',
                            'counseling' => 'Konseling',
                        ] as $tab => $label)
                            <button
                                type="button"
                                wire:click="setActiveTab('{{ $tab }}')"
                                @class([
                                    'h-10 rounded-2xl px-4 text-xs font-bold transition',
                                    'bg-indigo-600 text-white' => $activeTab === $tab,
                                    'bg-slate-100 text-slate-600' => $activeTab !== $tab,
                                ])
                            >
                                {{ $label }}
                            </button>
                        @endforeach
                    </div>
                </div>

                @if ($activeTab === 'overview')
                    <section class="space-y-4">
                        <div class="grid grid-cols-2 gap-3">
                            <article class="rounded-3xl bg-emerald-50 p-4">
                                <p class="text-xs font-semibold text-emerald-600">
                                    Kehadiran
                                </p>

                                <p class="mt-2 text-2xl font-extrabold text-emerald-700">
                                    {{ $attendancePercentage }}%
                                </p>
                            </article>

                            <article class="rounded-3xl bg-indigo-50 p-4">
                                <p class="text-xs font-semibold text-indigo-600">
                                    Saldo poin
                                </p>

                                <p
                                    @class([
                                        'mt-2 text-2xl font-extrabold',
                                        'text-emerald-700' => $pointBalance >= 0,
                                        'text-red-700' => $pointBalance < 0,
                                    ])
                                >
                                    {{ $pointBalance > 0 ? '+' : '' }}{{ $pointBalance }}
                                </p>
                            </article>

                            <article class="rounded-3xl bg-red-50 p-4">
                                <p class="text-xs font-semibold text-red-600">
                                    Pelanggaran
                                </p>

                                <p class="mt-2 text-2xl font-extrabold text-red-700">
                                    {{ $selectedStudent->violations_count }}
                                </p>
                            </article>

                            <article class="rounded-3xl bg-amber-50 p-4">
                                <p class="text-xs font-semibold text-amber-600">
                                    Prestasi
                                </p>

                                <p class="mt-2 text-2xl font-extrabold text-amber-700">
                                    {{ $selectedStudent->verified_achievements_count }}
                                </p>
                            </article>
                        </div>

                        <article class="rounded-3xl border border-slate-100 bg-white p-5">
                            <h4 class="text-sm font-extrabold text-slate-900">
                                Identitas Siswa
                            </h4>

                            <div class="mt-4 space-y-3">
                                @foreach ([
                                    'Jenis kelamin' => $selectedStudent->gender_label,
                                    'Tempat lahir' => $selectedStudent->birth_place ?: '-',
                                    'Tanggal lahir' => $selectedStudent->birth_date?->translatedFormat('d M Y') ?? '-',
                                    'Tahun masuk' => $selectedStudent->enrollment_year ?: '-',
                                    'Nomor HP' => $selectedStudent->phone ?: '-',
                                    'Nomor orang tua' => $selectedStudent->parent_phone ?: '-',
                                    'Wali kelas' => $selectedStudent->schoolClass?->homeroomTeacher?->name ?? '-',
                                ] as $label => $value)
                                    <div class="flex items-start justify-between gap-4 border-b border-slate-100 pb-3 last:border-0 last:pb-0">
                                        <span class="text-xs text-slate-500">
                                            {{ $label }}
                                        </span>

                                        <span class="text-right text-xs font-bold text-slate-700">
                                            {{ $value }}
                                        </span>
                                    </div>
                                @endforeach
                            </div>
                        </article>

                        <article class="rounded-3xl bg-slate-50 p-5">
                            <h4 class="text-sm font-extrabold text-slate-900">
                                Data Orang Tua
                            </h4>

                            <div class="mt-4 grid gap-3 sm:grid-cols-3">
                                <div>
                                    <p class="text-[10px] text-slate-400">
                                        Nama ayah
                                    </p>

                                    <p class="mt-1 text-xs font-bold text-slate-700">
                                        {{ $selectedStudent->father_name ?: '-' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-[10px] text-slate-400">
                                        Nama ibu
                                    </p>

                                    <p class="mt-1 text-xs font-bold text-slate-700">
                                        {{ $selectedStudent->mother_name ?: '-' }}
                                    </p>
                                </div>

                                <div>
                                    <p class="text-[10px] text-slate-400">
                                        Nama wali
                                    </p>

                                    <p class="mt-1 text-xs font-bold text-slate-700">
                                        {{ $selectedStudent->guardian_name ?: '-' }}
                                    </p>
                                </div>
                            </div>
                        </article>
                    </section>
                @endif

                @if ($activeTab === 'attendance')
                    <section>
                        <div class="grid grid-cols-3 gap-2 sm:grid-cols-5">
                            @foreach ([
                                ['Hadir', $selectedStudent->present_count, 'emerald'],
                                ['Sakit', $selectedStudent->sick_count, 'blue'],
                                ['Izin', $selectedStudent->permit_count, 'amber'],
                                ['Alfa', $selectedStudent->absent_count, 'red'],
                                ['Terlambat', $selectedStudent->late_count, 'orange'],
                            ] as [$label, $value, $color])
                                <div class="rounded-2xl bg-slate-50 p-3 text-center">
                                    <p class="text-lg font-extrabold text-slate-900">
                                        {{ $value }}
                                    </p>

                                    <p class="mt-1 text-[9px] font-semibold text-slate-500">
                                        {{ $label }}
                                    </p>
                                </div>
                            @endforeach
                        </div>

                        <div class="mt-4 space-y-3">
                            @forelse ($selectedStudent->attendances as $attendance)
                                <article
                                    wire:key="profile-attendance-{{ $attendance->id }}"
                                    class="rounded-3xl border border-slate-100 bg-white p-4"
                                >
                                    <div class="flex items-center justify-between gap-3">
                                        <div>
                                            <p class="text-sm font-extrabold text-slate-900">
                                                {{ $attendance->attendance_date->translatedFormat('d M Y') }}
                                            </p>

                                            <p class="mt-1 text-xs text-slate-500">
                                                Masuk:
                                                {{ $attendance->arrival_time ? substr((string) $attendance->arrival_time, 0, 5) : '-' }}
                                                · Pulang:
                                                {{ $attendance->departure_time ? substr((string) $attendance->departure_time, 0, 5) : '-' }}
                                            </p>
                                        </div>

                                        <span
                                            @class([
                                                'rounded-full px-3 py-1 text-[10px] font-bold',
                                                'bg-emerald-50 text-emerald-700' => $attendance->status === 'present',
                                                'bg-blue-50 text-blue-700' => $attendance->status === 'sick',
                                                'bg-amber-50 text-amber-700' => $attendance->status === 'permit',
                                                'bg-red-50 text-red-700' => $attendance->status === 'absent',
                                            ])
                                        >
                                            {{ $attendance->status_label }}
                                        </span>
                                    </div>

                                    @if ($attendance->note)
                                        <p class="mt-3 text-xs leading-5 text-slate-600">
                                            {{ $attendance->note }}
                                        </p>
                                    @endif
                                </article>
                            @empty
                                <p class="rounded-3xl bg-slate-50 p-8 text-center text-sm text-slate-500">
                                    Belum ada riwayat absensi.
                                </p>
                            @endforelse
                        </div>
                    </section>
                @endif

                @if ($activeTab === 'development')
                    <section class="space-y-3">
                        @forelse ($selectedStudent->developmentNotes as $note)
                            <article
                                wire:key="profile-note-{{ $note->id }}"
                                class="rounded-3xl border border-slate-100 bg-white p-4"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-extrabold text-slate-900">
                                            {{ $note->title }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $note->note_date->translatedFormat('d M Y') }}
                                            · {{ $note->category_label }}
                                        </p>
                                    </div>

                                    <span
                                        @class([
                                            'rounded-full px-3 py-1 text-[10px] font-bold',
                                            'bg-emerald-50 text-emerald-700' => $note->type === 'positive',
                                            'bg-amber-50 text-amber-700' => $note->type === 'attention',
                                            'bg-red-50 text-red-700' => $note->type === 'follow_up',
                                        ])
                                    >
                                        {{ $note->type_label }}
                                    </span>
                                </div>

                                <p class="mt-3 whitespace-pre-line text-xs leading-5 text-slate-600">
                                    {{ $note->description }}
                                </p>

                                @if ($note->follow_up)
                                    <div class="mt-3 rounded-2xl bg-indigo-50 p-3">
                                        <p class="text-xs font-bold text-indigo-700">
                                            Tindak lanjut
                                        </p>

                                        <p class="mt-1 text-xs leading-5 text-indigo-600">
                                            {{ $note->follow_up }}
                                        </p>
                                    </div>
                                @endif
                            </article>
                        @empty
                            <p class="rounded-3xl bg-slate-50 p-8 text-center text-sm text-slate-500">
                                Belum ada catatan perkembangan.
                            </p>
                        @endforelse
                    </section>
                @endif

                @if ($activeTab === 'violations')
                    <section class="space-y-3">
                        @forelse ($selectedStudent->violations as $violation)
                            <article
                                wire:key="profile-violation-{{ $violation->id }}"
                                class="rounded-3xl border border-red-100 bg-red-50 p-4"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-extrabold text-slate-900">
                                            {{ $violation->violation_name }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $violation->violation_date->translatedFormat('d M Y') }}
                                            · {{ $violation->category_label }}
                                        </p>
                                    </div>

                                    <span class="rounded-full bg-white px-3 py-1 text-xs font-extrabold text-red-600">
                                        +{{ $violation->points }} poin
                                    </span>
                                </div>

                                <p class="mt-3 whitespace-pre-line text-xs leading-5 text-slate-600">
                                    {{ $violation->chronology }}
                                </p>

                                @if ($violation->action_taken)
                                    <div class="mt-3 rounded-2xl bg-white p-3">
                                        <p class="text-xs font-bold text-red-700">
                                            Tindakan
                                        </p>

                                        <p class="mt-1 text-xs leading-5 text-slate-600">
                                            {{ $violation->action_taken }}
                                        </p>
                                    </div>
                                @endif
                            </article>
                        @empty
                            <p class="rounded-3xl bg-emerald-50 p-8 text-center text-sm font-semibold text-emerald-700">
                                Siswa belum memiliki catatan pelanggaran.
                            </p>
                        @endforelse
                    </section>
                @endif

                @if ($activeTab === 'achievements')
                    <section class="space-y-3">
                        @forelse ($selectedStudent->achievements as $achievement)
                            <article
                                wire:key="profile-achievement-{{ $achievement->id }}"
                                class="rounded-3xl border border-amber-100 bg-amber-50 p-4"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-extrabold text-slate-900">
                                            {{ $achievement->title }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $achievement->achievement_date->translatedFormat('d M Y') }}
                                            · {{ $achievement->level_label }}
                                            · {{ $achievement->rank_label }}
                                        </p>
                                    </div>

                                    <span class="rounded-full bg-white px-3 py-1 text-xs font-extrabold text-amber-600">
                                        +{{ $achievement->points }} poin
                                    </span>
                                </div>

                                <div class="mt-3 flex gap-2">
                                    <span
                                        @class([
                                            'rounded-full px-3 py-1 text-[10px] font-bold',
                                            'bg-emerald-100 text-emerald-700' => $achievement->status === 'verified',
                                            'bg-amber-100 text-amber-700' => $achievement->status === 'pending',
                                            'bg-red-100 text-red-700' => $achievement->status === 'rejected',
                                        ])
                                    >
                                        {{ $achievement->status_label }}
                                    </span>

                                    <span class="rounded-full bg-white px-3 py-1 text-[10px] font-bold text-slate-600">
                                        {{ $achievement->category_label }}
                                    </span>
                                </div>
                            </article>
                        @empty
                            <p class="rounded-3xl bg-slate-50 p-8 text-center text-sm text-slate-500">
                                Belum ada prestasi yang dicatat.
                            </p>
                        @endforelse
                    </section>
                @endif

                @if ($activeTab === 'permissions')
                    <section class="space-y-3">
                        @forelse ($selectedStudent->permissions as $permission)
                            <article
                                wire:key="profile-permission-{{ $permission->id }}"
                                class="rounded-3xl border border-sky-100 bg-sky-50 p-4"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-extrabold text-slate-900">
                                            {{ $permission->type_label }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $permission->permission_date->translatedFormat('d M Y') }}
                                        </p>
                                    </div>

                                    <span
                                        @class([
                                            'rounded-full px-3 py-1 text-[10px] font-bold',
                                            'bg-amber-100 text-amber-700' => $permission->status === 'pending',
                                            'bg-emerald-100 text-emerald-700' => $permission->status === 'approved',
                                            'bg-red-100 text-red-700' => $permission->status === 'rejected',
                                            'bg-blue-100 text-blue-700' => $permission->status === 'completed',
                                        ])
                                    >
                                        {{ $permission->status_label }}
                                    </span>
                                </div>

                                <p class="mt-3 whitespace-pre-line text-xs leading-5 text-slate-600">
                                    {{ $permission->reason }}
                                </p>

                                @if ($permission->rejection_note)
                                    <div class="mt-3 rounded-2xl bg-red-100 p-3">
                                        <p class="text-xs font-bold text-red-700">
                                            Alasan penolakan
                                        </p>

                                        <p class="mt-1 text-xs text-red-600">
                                            {{ $permission->rejection_note }}
                                        </p>
                                    </div>
                                @endif
                            </article>
                        @empty
                            <p class="rounded-3xl bg-slate-50 p-8 text-center text-sm text-slate-500">
                                Belum ada riwayat perizinan.
                            </p>
                        @endforelse
                    </section>
                @endif

                @if ($activeTab === 'counseling')
                    <section class="space-y-4">
                        @forelse ($visibleCounselingCases as $case)
                            <article
                                wire:key="profile-counseling-{{ $case->id }}"
                                class="rounded-3xl border border-violet-100 bg-violet-50 p-4"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-[10px] font-bold uppercase text-violet-500">
                                            {{ $case->case_number }}
                                        </p>

                                        <p class="mt-1 text-sm font-extrabold text-slate-900">
                                            {{ $case->title }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $case->category_label }}
                                            · {{ $case->priority_label }}
                                        </p>
                                    </div>

                                    <span class="rounded-full bg-white px-3 py-1 text-[10px] font-bold text-violet-700">
                                        {{ $case->status_label }}
                                    </span>
                                </div>

                                @if ($case->summary)
                                    <p class="mt-3 whitespace-pre-line text-xs leading-5 text-slate-600">
                                        {{ $case->summary }}
                                    </p>
                                @endif

                                <div class="mt-4 space-y-3">
                                    @foreach ($case->sessions as $session)
                                        <div class="rounded-2xl bg-white p-3">
                                            <div class="flex items-center justify-between gap-3">
                                                <p class="text-xs font-bold text-slate-700">
                                                    {{ $session->session_date->translatedFormat('d M Y') }}
                                                </p>

                                                <span class="text-[10px] font-semibold text-violet-600">
                                                    {{ $session->attendance_status_label }}
                                                </span>
                                            </div>

                                            <p class="mt-2 text-xs leading-5 text-slate-600">
                                                {{ $session->agenda }}
                                            </p>

                                            @if ($session->follow_up)
                                                <p class="mt-2 text-xs font-semibold text-amber-700">
                                                    Tindak lanjut: {{ $session->follow_up }}
                                                </p>
                                            @endif
                                        </div>
                                    @endforeach
                                </div>
                            </article>
                        @empty
                            <p class="rounded-3xl bg-slate-50 p-8 text-center text-sm text-slate-500">
                                Tidak ada kasus konseling yang dapat ditampilkan.
                            </p>
                        @endforelse
                    </section>
                @endif
            </div>
        @endif
    </x-smooth-modal>
</div>
