<div>
    @php
        $queryParameters = array_filter([
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'class_id' => $classId,
        ], fn ($value) => filled($value));
    @endphp

    <div class="space-y-5">
        <section class="rounded-[28px] bg-gradient-to-br from-slate-900 via-blue-900 to-indigo-700 p-5 text-white shadow-xl shadow-indigo-200 sm:p-7">
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
                        Dokumen Kesiswaan
                    </p>

                    <h2 class="mt-1 text-2xl font-extrabold">
                        Laporan dan Export PDF
                    </h2>
                </div>
            </div>

            <p class="mt-4 max-w-2xl text-sm leading-6 text-indigo-100">
                Tentukan periode dan kelas, kemudian download laporan
                absensi, pelanggaran, prestasi, perizinan, konseling,
                atau profil lengkap siswa.
            </p>
        </section>

        <section class="rounded-[28px] border border-slate-100 bg-white p-5 shadow-sm">
            <h3 class="text-base font-extrabold text-slate-900">
                Filter Laporan
            </h3>

            <div class="mt-4 grid gap-4 sm:grid-cols-2 lg:grid-cols-4">
                <div>
                    <label class="mb-2 block text-xs font-bold text-slate-600">
                        Tanggal awal
                    </label>

                    <input
                        type="date"
                        wire:model.live="dateFrom"
                        class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                    >
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold text-slate-600">
                        Tanggal akhir
                    </label>

                    <input
                        type="date"
                        wire:model.live="dateTo"
                        min="{{ $dateFrom }}"
                        class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                    >
                </div>

                <div>
                    <label class="mb-2 block text-xs font-bold text-slate-600">
                        Kelas
                    </label>

                    <select
                        wire:model.live="classId"
                        class="h-12 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                    >
                        <option value="">Semua kelas</option>

                        @foreach ($schoolClasses as $class)
                            <option value="{{ $class->id }}">
                                {{ $class->name }}
                                — {{ $class->academic_year }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="flex items-end">
                    <button
                        type="button"
                        wire:click="resetFilters"
                        class="h-12 w-full rounded-2xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-600 transition hover:bg-slate-50"
                    >
                        Reset Filter
                    </button>
                </div>
            </div>
        </section>

        <section class="grid grid-cols-2 gap-3 lg:grid-cols-5">
            <article class="rounded-3xl border border-emerald-100 bg-emerald-50 p-4">
                <p class="text-xs font-semibold text-emerald-600">
                    Absensi
                </p>

                <p class="mt-2 text-2xl font-extrabold text-emerald-700">
                    {{ number_format($statistics['attendance']) }}
                </p>
            </article>

            <article class="rounded-3xl border border-red-100 bg-red-50 p-4">
                <p class="text-xs font-semibold text-red-600">
                    Pelanggaran
                </p>

                <p class="mt-2 text-2xl font-extrabold text-red-700">
                    {{ number_format($statistics['violations']) }}
                </p>
            </article>

            <article class="rounded-3xl border border-amber-100 bg-amber-50 p-4">
                <p class="text-xs font-semibold text-amber-600">
                    Prestasi
                </p>

                <p class="mt-2 text-2xl font-extrabold text-amber-700">
                    {{ number_format($statistics['achievements']) }}
                </p>
            </article>

            <article class="rounded-3xl border border-sky-100 bg-sky-50 p-4">
                <p class="text-xs font-semibold text-sky-600">
                    Perizinan
                </p>

                <p class="mt-2 text-2xl font-extrabold text-sky-700">
                    {{ number_format($statistics['permissions']) }}
                </p>
            </article>

            <article class="col-span-2 rounded-3xl border border-violet-100 bg-violet-50 p-4 lg:col-span-1">
                <p class="text-xs font-semibold text-violet-600">
                    Konseling
                </p>

                <p class="mt-2 text-2xl font-extrabold text-violet-700">
                    {{ number_format($statistics['counseling']) }}
                </p>
            </article>
        </section>

        <section>
            <h3 class="mb-4 text-lg font-extrabold text-slate-900">
                Pilih Laporan
            </h3>

            <div class="grid gap-4 md:grid-cols-2 xl:grid-cols-3">
                <a
                    href="{{ route('reports.attendance.pdf', $queryParameters) }}"
                    target="_blank"
                    class="rounded-[28px] border border-emerald-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-xl"
                >
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-emerald-50 text-emerald-600">
                        <span class="text-xl font-extrabold">A</span>
                    </div>

                    <h4 class="mt-4 text-base font-extrabold text-slate-900">
                        Laporan Absensi
                    </h4>

                    <p class="mt-2 text-xs leading-5 text-slate-500">
                        Rekap hadir, sakit, izin, alfa, jam masuk,
                        jam pulang, dan keterlambatan.
                    </p>

                    <p class="mt-4 text-xs font-bold text-emerald-600">
                        Download PDF
                    </p>
                </a>

                <a
                    href="{{ route('reports.violations.pdf', $queryParameters) }}"
                    target="_blank"
                    class="rounded-[28px] border border-red-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-xl"
                >
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-red-50 text-red-600">
                        <span class="text-xl font-extrabold">P</span>
                    </div>

                    <h4 class="mt-4 text-base font-extrabold text-slate-900">
                        Laporan Pelanggaran
                    </h4>

                    <p class="mt-2 text-xs leading-5 text-slate-500">
                        Rekap kasus, kategori, poin, status penanganan,
                        dan pencatat pelanggaran.
                    </p>

                    <p class="mt-4 text-xs font-bold text-red-600">
                        Download PDF
                    </p>
                </a>

                <a
                    href="{{ route('reports.achievements.pdf', $queryParameters) }}"
                    target="_blank"
                    class="rounded-[28px] border border-amber-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-xl"
                >
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-amber-50 text-amber-600">
                        <span class="text-xl font-extrabold">J</span>
                    </div>

                    <h4 class="mt-4 text-base font-extrabold text-slate-900">
                        Laporan Prestasi
                    </h4>

                    <p class="mt-2 text-xs leading-5 text-slate-500">
                        Prestasi akademik, nonakademik, tingkat,
                        peringkat, poin, dan verifikasi.
                    </p>

                    <p class="mt-4 text-xs font-bold text-amber-600">
                        Download PDF
                    </p>
                </a>

                <a
                    href="{{ route('reports.permissions.pdf', $queryParameters) }}"
                    target="_blank"
                    class="rounded-[28px] border border-sky-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-xl"
                >
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-sky-50 text-sky-600">
                        <span class="text-xl font-extrabold">I</span>
                    </div>

                    <h4 class="mt-4 text-base font-extrabold text-slate-900">
                        Laporan Perizinan
                    </h4>

                    <p class="mt-2 text-xs leading-5 text-slate-500">
                        Pengajuan izin, jam keluar, waktu kembali,
                        alasan, persetujuan, dan penolakan.
                    </p>

                    <p class="mt-4 text-xs font-bold text-sky-600">
                        Download PDF
                    </p>
                </a>

                <a
                    href="{{ route('reports.counseling.pdf', $queryParameters) }}"
                    target="_blank"
                    class="rounded-[28px] border border-violet-100 bg-white p-5 shadow-sm transition hover:-translate-y-1 hover:shadow-xl"
                >
                    <div class="flex h-14 w-14 items-center justify-center rounded-2xl bg-violet-50 text-violet-600">
                        <span class="text-xl font-extrabold">BK</span>
                    </div>

                    <h4 class="mt-4 text-base font-extrabold text-slate-900">
                        Laporan Konseling
                    </h4>

                    <p class="mt-2 text-xs leading-5 text-slate-500">
                        Kasus konseling, prioritas, status, guru BK,
                        dan jumlah pertemuan.
                    </p>

                    <p class="mt-4 text-xs font-bold text-violet-600">
                        Download PDF
                    </p>
                </a>
            </div>
        </section>

        <section class="rounded-[28px] border border-indigo-100 bg-white p-5 shadow-sm">
            <h3 class="text-lg font-extrabold text-slate-900">
                Profil Lengkap Per Siswa
            </h3>

            <p class="mt-2 text-sm leading-6 text-slate-500">
                Pilih siswa untuk mencetak identitas, absensi,
                perkembangan, pelanggaran, prestasi, perizinan,
                dan konseling.
            </p>

            <div class="mt-5 grid gap-3 sm:grid-cols-[1fr_auto]">
                <select
                    wire:model.live="studentId"
                    class="h-14 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                >
                    <option value="">Pilih siswa</option>

                    @foreach ($students as $student)
                        <option value="{{ $student->id }}">
                            {{ $student->name }}
                            — {{ $student->nis }}
                            — {{ $student->schoolClass?->name ?? 'Tanpa kelas' }}
                        </option>
                    @endforeach
                </select>

                @if ($studentId)
                    <a
                        href="{{ route('reports.student-profile.pdf', $studentId) }}"
                        target="_blank"
                        class="flex h-14 items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-600 px-6 text-sm font-bold text-white shadow-lg shadow-indigo-200"
                    >
                        Download Profil PDF
                    </a>
                @else
                    <button
                        type="button"
                        disabled
                        class="h-14 rounded-2xl bg-slate-200 px-6 text-sm font-bold text-slate-400"
                    >
                        Pilih Siswa
                    </button>
                @endif
            </div>
        </section>
    </div>
</div>
