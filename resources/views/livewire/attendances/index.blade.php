<div>
    <div class="space-y-5">
        <section class="overflow-hidden rounded-[28px] bg-gradient-to-br from-emerald-600 via-teal-600 to-cyan-500 p-5 text-white shadow-xl shadow-emerald-200 sm:p-7">
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
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-emerald-100">
                                Pencatatan Harian
                            </p>

                            <h2 class="mt-1 text-2xl font-extrabold">
                                Absensi Siswa
                            </h2>
                        </div>
                    </div>

                    <p class="mt-4 max-w-xl text-sm leading-6 text-emerald-100">
                        Catat kehadiran siswa secara massal berdasarkan
                        kelas dan tanggal.
                    </p>
                </div>

                <button
                    type="button"
                    wire:click="saveAll"
                    wire:loading.attr="disabled"
                    wire:target="saveAll"
                    class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-white px-5 text-sm font-bold text-emerald-700 shadow-lg transition hover:-translate-y-0.5 disabled:opacity-60"
                >
                    <svg
                        wire:loading
                        wire:target="saveAll"
                        class="h-5 w-5 animate-spin"
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

                    <span wire:loading.remove wire:target="saveAll">
                        Simpan Absensi
                    </span>

                    <span wire:loading wire:target="saveAll">
                        Menyimpan...
                    </span>
                </button>
            </div>
        </section>

        <section class="rounded-[28px] border border-slate-100 bg-white p-4 shadow-sm sm:p-5">
            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Tanggal absensi
                    </label>

                    <input
                        type="date"
                        wire:model.live="selectedDate"
                        max="{{ now()->format('Y-m-d') }}"
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                    >

                    @error('selectedDate')
                        <p class="mt-2 text-xs font-medium text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Pilih kelas
                    </label>

                    <select
                        wire:model.live="selectedClassId"
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                    >
                        <option value="">Pilih kelas</option>

                        @foreach ($schoolClasses as $class)
                            <option value="{{ $class->id }}">
                                {{ $class->name }}
                                — {{ $class->academic_year }}
                                — {{ ucfirst($class->semester) }}
                            </option>
                        @endforeach
                    </select>

                    @error('selectedClassId')
                        <p class="mt-2 text-xs font-medium text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            @if ($selectedClass)
                <div class="mt-4 rounded-2xl bg-emerald-50 p-4">
                    <p class="text-xs font-bold text-emerald-700">
                        {{ $selectedClass->full_name }}
                    </p>

                    <p class="mt-1 text-[11px] text-emerald-600">
                        Tahun ajaran {{ $selectedClass->academic_year }}
                        · Semester {{ $selectedClass->semester_label }}
                        · Wali kelas
                        {{ $selectedClass->homeroomTeacher?->name ?? 'belum ditentukan' }}
                    </p>
                </div>
            @endif
        </section>

        <section class="grid grid-cols-2 gap-3 lg:grid-cols-6">
            <article class="rounded-3xl border border-slate-100 bg-white p-4 shadow-sm">
                <p class="text-[11px] font-semibold text-slate-500">
                    Tercatat
                </p>

                <p class="mt-2 text-2xl font-extrabold text-slate-900">
                    {{ $summary['total'] }}
                </p>
            </article>

            <article class="rounded-3xl border border-emerald-100 bg-emerald-50 p-4">
                <p class="text-[11px] font-semibold text-emerald-600">
                    Hadir
                </p>

                <p class="mt-2 text-2xl font-extrabold text-emerald-700">
                    {{ $summary['present'] }}
                </p>
            </article>

            <article class="rounded-3xl border border-blue-100 bg-blue-50 p-4">
                <p class="text-[11px] font-semibold text-blue-600">
                    Sakit
                </p>

                <p class="mt-2 text-2xl font-extrabold text-blue-700">
                    {{ $summary['sick'] }}
                </p>
            </article>

            <article class="rounded-3xl border border-amber-100 bg-amber-50 p-4">
                <p class="text-[11px] font-semibold text-amber-600">
                    Izin
                </p>

                <p class="mt-2 text-2xl font-extrabold text-amber-700">
                    {{ $summary['permit'] }}
                </p>
            </article>

            <article class="rounded-3xl border border-red-100 bg-red-50 p-4">
                <p class="text-[11px] font-semibold text-red-600">
                    Alfa
                </p>

                <p class="mt-2 text-2xl font-extrabold text-red-700">
                    {{ $summary['absent'] }}
                </p>
            </article>

            <article class="rounded-3xl border border-orange-100 bg-orange-50 p-4">
                <p class="text-[11px] font-semibold text-orange-600">
                    Terlambat
                </p>

                <p class="mt-2 text-2xl font-extrabold text-orange-700">
                    {{ $summary['late'] }}
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
                    placeholder="Cari nama siswa, NIS, atau NISN..."
                    class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 pl-12 pr-4 text-sm outline-none transition focus:border-emerald-500 focus:bg-white focus:ring-4 focus:ring-emerald-100"
                >
            </div>

            <div class="mt-4 grid gap-3 sm:grid-cols-2">
                <button
                    type="button"
                    wire:click="markAllPresent"
                    class="h-12 rounded-2xl bg-emerald-600 px-4 text-sm font-bold text-white shadow-lg shadow-emerald-100 transition hover:bg-emerald-700"
                >
                    Tandai Semua Hadir
                </button>

                <button
                    type="button"
                    wire:click="reloadAttendance"
                    class="h-12 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-600 transition hover:bg-slate-50"
                >
                    Muat Ulang Data
                </button>
            </div>
        </section>

        <section>
            <div class="mb-4">
                <h3 class="text-lg font-extrabold text-slate-900">
                    Daftar Absensi Siswa
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    {{ $students->count() }} siswa ditemukan
                </p>
            </div>

            <div
                wire:loading.class="opacity-50"
                wire:target="selectedDate,selectedClassId,search"
                class="space-y-4"
            >
                @if (! $selectedClassId)
                    <div class="rounded-[28px] border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                        <h4 class="text-base font-extrabold text-slate-900">
                            Pilih kelas terlebih dahulu
                        </h4>

                        <p class="mt-2 text-sm text-slate-500">
                            Daftar siswa akan muncul setelah kelas dipilih.
                        </p>
                    </div>
                @else
                    @forelse ($students as $student)
                        @php
                            $key = (string) $student->id;
                            $currentStatus = $statuses[$key] ?? '';
                            $isPersisted = $persisted[$key] ?? false;
                        @endphp

                        <article
                            wire:key="attendance-student-{{ $student->id }}"
                            class="rounded-[28px] border border-slate-100 bg-white p-4 shadow-sm sm:p-5"
                        >
                            <div class="flex items-start gap-4">
                                @if ($student->photo_url)
                                    <img
                                        src="{{ $student->photo_url }}"
                                        alt="{{ $student->name }}"
                                        class="h-14 w-14 shrink-0 rounded-2xl object-cover"
                                    >
                                @else
                                    <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-emerald-50 text-lg font-extrabold text-emerald-600">
                                        {{ $student->initial }}
                                    </div>
                                @endif

                                <div class="min-w-0 flex-1">
                                    <div class="flex items-start justify-between gap-3">
                                        <div class="min-w-0">
                                            <h4 class="truncate text-sm font-extrabold text-slate-900">
                                                {{ $student->name }}
                                            </h4>

                                            <p class="mt-1 text-xs text-slate-500">
                                                NIS {{ $student->nis }}
                                                @if ($student->nisn)
                                                    · NISN {{ $student->nisn }}
                                                @endif
                                            </p>
                                        </div>

                                        @if ($isPersisted)
                                            <span class="shrink-0 rounded-full bg-emerald-50 px-3 py-1 text-[10px] font-bold text-emerald-600">
                                                Tersimpan
                                            </span>
                                        @else
                                            <span class="shrink-0 rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold text-slate-500">
                                                Belum disimpan
                                            </span>
                                        @endif
                                    </div>
                                </div>
                            </div>

                            <div class="mt-5 grid grid-cols-2 gap-2 sm:grid-cols-4">
                                <button
                                    type="button"
                                    wire:click="setStatus({{ $student->id }}, 'present')"
                                    @class([
                                        'h-11 rounded-2xl text-xs font-bold transition',
                                        'bg-emerald-600 text-white shadow-lg shadow-emerald-100' => $currentStatus === 'present',
                                        'bg-emerald-50 text-emerald-600 hover:bg-emerald-100' => $currentStatus !== 'present',
                                    ])
                                >
                                    Hadir
                                </button>

                                <button
                                    type="button"
                                    wire:click="setStatus({{ $student->id }}, 'sick')"
                                    @class([
                                        'h-11 rounded-2xl text-xs font-bold transition',
                                        'bg-blue-600 text-white shadow-lg shadow-blue-100' => $currentStatus === 'sick',
                                        'bg-blue-50 text-blue-600 hover:bg-blue-100' => $currentStatus !== 'sick',
                                    ])
                                >
                                    Sakit
                                </button>

                                <button
                                    type="button"
                                    wire:click="setStatus({{ $student->id }}, 'permit')"
                                    @class([
                                        'h-11 rounded-2xl text-xs font-bold transition',
                                        'bg-amber-500 text-white shadow-lg shadow-amber-100' => $currentStatus === 'permit',
                                        'bg-amber-50 text-amber-600 hover:bg-amber-100' => $currentStatus !== 'permit',
                                    ])
                                >
                                    Izin
                                </button>

                                <button
                                    type="button"
                                    wire:click="setStatus({{ $student->id }}, 'absent')"
                                    @class([
                                        'h-11 rounded-2xl text-xs font-bold transition',
                                        'bg-red-600 text-white shadow-lg shadow-red-100' => $currentStatus === 'absent',
                                        'bg-red-50 text-red-600 hover:bg-red-100' => $currentStatus !== 'absent',
                                    ])
                                >
                                    Alfa
                                </button>
                            </div>

                            @error('statuses.'.$key)
                                <p class="mt-2 text-xs font-medium text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror

                            @if ($currentStatus === 'present')
                                <div class="mt-4 grid gap-3 sm:grid-cols-2">
                                    <div>
                                        <label class="mb-2 block text-xs font-bold text-slate-600">
                                            Jam masuk
                                        </label>

                                        <input
                                            type="time"
                                            wire:model="arrivalTimes.{{ $key }}"
                                            class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                                        >

                                        @error('arrivalTimes.'.$key)
                                            <p class="mt-2 text-xs text-red-600">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>

                                    <div>
                                        <label class="mb-2 block text-xs font-bold text-slate-600">
                                            Jam pulang
                                        </label>

                                        <input
                                            type="time"
                                            wire:model="departureTimes.{{ $key }}"
                                            class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                                        >

                                        @error('departureTimes.'.$key)
                                            <p class="mt-2 text-xs text-red-600">
                                                {{ $message }}
                                            </p>
                                        @enderror
                                    </div>
                                </div>

                                <div class="mt-3 grid gap-3 sm:grid-cols-2">
                                    <label class="flex cursor-pointer items-center justify-between rounded-2xl border border-orange-100 bg-orange-50 p-3">
                                        <span class="text-xs font-bold text-orange-700">
                                            Terlambat
                                        </span>

                                        <input
                                            type="checkbox"
                                            wire:model="lateFlags.{{ $key }}"
                                            class="h-5 w-5 rounded border-orange-300 text-orange-600 focus:ring-orange-500"
                                        >
                                    </label>

                                    <label class="flex cursor-pointer items-center justify-between rounded-2xl border border-violet-100 bg-violet-50 p-3">
                                        <span class="text-xs font-bold text-violet-700">
                                            Pulang lebih awal
                                        </span>

                                        <input
                                            type="checkbox"
                                            wire:model="earlyLeaveFlags.{{ $key }}"
                                            class="h-5 w-5 rounded border-violet-300 text-violet-600 focus:ring-violet-500"
                                        >
                                    </label>
                                </div>
                            @endif

                            <div class="mt-4">
                                <label class="mb-2 block text-xs font-bold text-slate-600">
                                    Catatan
                                </label>

                                <textarea
                                    wire:model="notes.{{ $key }}"
                                    rows="2"
                                    placeholder="Alasan sakit, izin, keterlambatan, atau catatan lain..."
                                    class="w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-emerald-500 focus:ring-4 focus:ring-emerald-100"
                                ></textarea>

                                @error('notes.'.$key)
                                    <p class="mt-2 text-xs text-red-600">
                                        {{ $message }}
                                    </p>
                                @enderror
                            </div>

                            @if ($isPersisted)
                                <div class="mt-4 border-t border-slate-100 pt-4">
                                    <button
                                        type="button"
                                        x-on:click="
                                            confirmDelete({
                                                title: 'Hapus absensi?',
                                                text: @js('Absensi '.$student->name.' pada tanggal '.\Carbon\Carbon::parse($selectedDate)->format('d-m-Y').' akan dihapus.')
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $wire.deleteAttendance({{ $student->id }});
                                                }
                                            })
                                        "
                                        class="h-10 w-full rounded-2xl bg-red-50 text-xs font-bold text-red-600 transition hover:bg-red-100"
                                    >
                                        Hapus Catatan Absensi
                                    </button>
                                </div>
                            @endif
                        </article>
                    @empty
                        <div class="rounded-[28px] border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                            <h4 class="text-base font-extrabold text-slate-900">
                                Siswa tidak ditemukan
                            </h4>

                            <p class="mt-2 text-sm leading-6 text-slate-500">
                                Kelas belum memiliki siswa aktif atau kata
                                pencarian tidak sesuai.
                            </p>
                        </div>
                    @endforelse
                @endif
            </div>

            @if ($students->isNotEmpty())
                <button
                    type="button"
                    wire:click="saveAll"
                    wire:loading.attr="disabled"
                    wire:target="saveAll"
                    class="mt-5 flex h-14 w-full items-center justify-center rounded-2xl bg-gradient-to-r from-emerald-600 to-teal-500 text-sm font-bold text-white shadow-xl shadow-emerald-100 disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="saveAll">
                        Simpan Semua Absensi
                    </span>

                    <span wire:loading wire:target="saveAll">
                        Menyimpan absensi...
                    </span>
                </button>
            @endif
        </section>

        @if ($recentSummaries->isNotEmpty())
            <section>
                <div class="mb-4">
                    <h3 class="text-lg font-extrabold text-slate-900">
                        Rekap 7 Hari Terakhir
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Ringkasan berdasarkan kelas yang dipilih
                    </p>
                </div>

                <div class="space-y-3">
                    @foreach ($recentSummaries as $recent)
                        <article class="rounded-3xl border border-slate-100 bg-white p-4 shadow-sm">
                            <div class="flex items-center justify-between gap-4">
                                <div>
                                    <p class="text-sm font-extrabold text-slate-900">
                                        {{ \Carbon\Carbon::parse($recent->attendance_date)->translatedFormat('l, d M Y') }}
                                    </p>

                                    <p class="mt-1 text-xs text-slate-500">
                                        {{ $recent->total }} siswa tercatat
                                    </p>
                                </div>

                                <span class="rounded-full bg-emerald-50 px-3 py-1 text-xs font-bold text-emerald-600">
                                    {{ $recent->present }} hadir
                                </span>
                            </div>

                            <div class="mt-4 grid grid-cols-4 gap-2 text-center">
                                <div class="rounded-2xl bg-blue-50 p-2">
                                    <p class="text-sm font-extrabold text-blue-700">
                                        {{ $recent->sick }}
                                    </p>

                                    <p class="text-[9px] font-semibold text-blue-600">
                                        Sakit
                                    </p>
                                </div>

                                <div class="rounded-2xl bg-amber-50 p-2">
                                    <p class="text-sm font-extrabold text-amber-700">
                                        {{ $recent->permit }}
                                    </p>

                                    <p class="text-[9px] font-semibold text-amber-600">
                                        Izin
                                    </p>
                                </div>

                                <div class="rounded-2xl bg-red-50 p-2">
                                    <p class="text-sm font-extrabold text-red-700">
                                        {{ $recent->absent }}
                                    </p>

                                    <p class="text-[9px] font-semibold text-red-600">
                                        Alfa
                                    </p>
                                </div>

                                <div class="rounded-2xl bg-orange-50 p-2">
                                    <p class="text-sm font-extrabold text-orange-700">
                                        {{ $recent->late }}
                                    </p>

                                    <p class="text-[9px] font-semibold text-orange-600">
                                        Terlambat
                                    </p>
                                </div>
                            </div>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif
    </div>
</div>
