<div>
    @php
        $canCreate = ! auth()->user()->isKepalaSekolah();
    @endphp

    <div class="space-y-5">
        <section class="overflow-hidden rounded-[28px] bg-gradient-to-br from-violet-700 via-indigo-700 to-blue-600 p-5 text-white shadow-xl shadow-indigo-200 sm:p-7">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
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
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-100">
                                Pendampingan Siswa
                            </p>

                            <h2 class="mt-1 text-2xl font-extrabold">
                                Bimbingan Konseling
                            </h2>
                        </div>
                    </div>

                    <p class="mt-4 max-w-xl text-sm leading-6 text-indigo-100">
                        Kelola kasus, jadwal pertemuan, hasil konseling,
                        tindak lanjut, dan komunikasi dengan orang tua.
                    </p>
                </div>

                @if ($canCreate)
                    <button
                        type="button"
                        wire:click="openCreateCaseModal"
                        class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-white px-5 text-sm font-bold text-indigo-700 shadow-lg"
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

                        Tambah Kasus
                    </button>
                @endif
            </div>
        </section>

        <section class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            <article class="rounded-3xl border border-slate-100 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold text-slate-500">
                    Total kasus
                </p>

                <p class="mt-2 text-2xl font-extrabold text-slate-900">
                    {{ number_format($statistics['total']) }}
                </p>
            </article>

            <article class="rounded-3xl border border-blue-100 bg-blue-50 p-4">
                <p class="text-xs font-semibold text-blue-600">
                    Kasus aktif
                </p>

                <p class="mt-2 text-2xl font-extrabold text-blue-700">
                    {{ number_format($statistics['active']) }}
                </p>
            </article>

            <article class="rounded-3xl border border-red-100 bg-red-50 p-4">
                <p class="text-xs font-semibold text-red-600">
                    Mendesak
                </p>

                <p class="mt-2 text-2xl font-extrabold text-red-700">
                    {{ number_format($statistics['urgent']) }}
                </p>
            </article>

            <article class="rounded-3xl border border-violet-100 bg-violet-50 p-4">
                <p class="text-xs font-semibold text-violet-600">
                    Jadwal mendatang
                </p>

                <p class="mt-2 text-2xl font-extrabold text-violet-700">
                    {{ number_format($statistics['upcoming']) }}
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
                    placeholder="Cari siswa, NIS, nomor kasus, judul, atau ringkasan..."
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

            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
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
                    wire:model.live="filterCategory"
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                >
                    <option value="">Semua kategori</option>

                    @foreach ($categories as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <select
                    wire:model.live="filterPriority"
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                >
                    <option value="">Semua prioritas</option>

                    @foreach ($priorities as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <select
                    wire:model.live="filterStatus"
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                >
                    <option value="">Semua status</option>

                    @foreach ($caseStatuses as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                    @endforeach
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
                    Daftar Kasus Konseling
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Ditemukan {{ $cases->total() }} kasus
                </p>
            </div>

            <div
                wire:loading.class="opacity-50"
                wire:target="search,filterClassId,filterCategory,filterPriority,filterStatus"
                class="grid gap-4 lg:grid-cols-2"
            >
                @forelse ($cases as $case)
                    @php
                        $canModify = auth()->user()->isAdmin()
                            || auth()->user()->isGuruBk()
                            || $case->assigned_to === auth()->id()
                            || $case->created_by === auth()->id();
                    @endphp

                    <article
                        wire:key="counseling-case-{{ $case->id }}"
                        class="rounded-[28px] border border-slate-100 bg-white p-5 shadow-sm transition hover:shadow-lg"
                    >
                        <div class="flex items-start gap-4">
                            @if ($case->student->photo_url)
                                <img
                                    src="{{ $case->student->photo_url }}"
                                    alt="{{ $case->student->name }}"
                                    class="h-14 w-14 shrink-0 rounded-2xl object-cover"
                                >
                            @else
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-lg font-extrabold text-indigo-600">
                                    {{ $case->student->initial }}
                                </div>
                            @endif

                            <div class="min-w-0 flex-1">
                                <div class="flex items-start justify-between gap-2">
                                    <div class="min-w-0">
                                        <h4 class="truncate text-sm font-extrabold text-slate-900">
                                            {{ $case->student->name }}
                                        </h4>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $case->student->schoolClass?->name ?? 'Tanpa kelas' }}
                                            · NIS {{ $case->student->nis }}
                                        </p>
                                    </div>

                                    <span
                                        @class([
                                            'shrink-0 rounded-full px-3 py-1 text-[10px] font-bold',
                                            'bg-slate-100 text-slate-600' => $case->priority === 'low',
                                            'bg-amber-50 text-amber-700' => $case->priority === 'medium',
                                            'bg-orange-50 text-orange-700' => $case->priority === 'high',
                                            'bg-red-50 text-red-700' => $case->priority === 'urgent',
                                        ])
                                    >
                                        {{ $case->priority_label }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <span class="rounded-full bg-indigo-50 px-3 py-1 text-[10px] font-bold text-indigo-700">
                                {{ $case->category_label }}
                            </span>

                            <span
                                @class([
                                    'rounded-full px-3 py-1 text-[10px] font-bold',
                                    'bg-blue-50 text-blue-700' => $case->status === 'open',
                                    'bg-violet-50 text-violet-700' => $case->status === 'in_progress',
                                    'bg-amber-50 text-amber-700' => $case->status === 'monitoring',
                                    'bg-emerald-50 text-emerald-700' => in_array($case->status, ['resolved', 'closed']),
                                ])
                            >
                                {{ $case->status_label }}
                            </span>

                            <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold text-slate-600">
                                {{ $case->confidentiality_label }}
                            </span>
                        </div>

                        <p class="mt-4 text-[10px] font-bold uppercase tracking-wide text-slate-400">
                            {{ $case->case_number }}
                        </p>

                        <h5 class="mt-2 text-base font-extrabold text-slate-900">
                            {{ $case->title }}
                        </h5>

                        @if ($case->summary)
                            <p class="mt-2 line-clamp-3 whitespace-pre-line text-sm leading-6 text-slate-600">
                                {{ $case->summary }}
                            </p>
                        @endif

                        <div class="mt-4 grid grid-cols-2 gap-3">
                            <div class="rounded-2xl bg-slate-50 p-3">
                                <p class="text-[10px] uppercase text-slate-400">
                                    Penanggung Jawab
                                </p>

                                <p class="mt-1 truncate text-xs font-bold text-slate-700">
                                    {{ $case->assignedTeacher?->name ?? 'Belum ditentukan' }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-3">
                                <p class="text-[10px] uppercase text-slate-400">
                                    Pertemuan
                                </p>

                                <p class="mt-1 text-xs font-bold text-slate-700">
                                    {{ $case->sessions_count }} pertemuan
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 grid grid-cols-2 gap-2 border-t border-slate-100 pt-4">
                            <button
                                type="button"
                                wire:click="showHistory({{ $case->id }})"
                                class="h-11 rounded-2xl bg-slate-100 text-xs font-bold text-slate-700"
                            >
                                Lihat Riwayat
                            </button>

                            @if ($canModify)
                                <button
                                    type="button"
                                    wire:click="openSessionModal({{ $case->id }})"
                                    class="h-11 rounded-2xl bg-indigo-600 text-xs font-bold text-white"
                                >
                                    Tambah Pertemuan
                                </button>

                                <button
                                    type="button"
                                    wire:click="editCase({{ $case->id }})"
                                    class="h-11 rounded-2xl bg-blue-50 text-xs font-bold text-blue-600"
                                >
                                    Edit Kasus
                                </button>

                                <button
                                    type="button"
                                    x-on:click="
                                        confirmDelete({
                                            title: 'Hapus kasus konseling?',
                                            text: @js('Kasus '.$case->student->name.' beserta seluruh pertemuannya akan dihapus.')
                                        }).then((result) => {
                                            if (result.isConfirmed) {
                                                $wire.deleteCase({{ $case->id }});
                                            }
                                        })
                                    "
                                    class="h-11 rounded-2xl bg-red-50 text-xs font-bold text-red-600"
                                >
                                    Hapus
                                </button>
                            @endif
                        </div>
                    </article>
                @empty
                    <div class="lg:col-span-2">
                        <div class="rounded-[28px] border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                            <h4 class="text-base font-extrabold text-slate-900">
                                Kasus konseling belum ditemukan
                            </h4>

                            <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">
                                Tambahkan kasus baru atau ubah pencarian dan
                                filter yang sedang digunakan.
                            </p>

                            @if ($canCreate)
                                <button
                                    type="button"
                                    wire:click="openCreateCaseModal"
                                    class="mt-5 rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white"
                                >
                                    Tambah Kasus
                                </button>
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($cases->hasPages())
                <div class="mt-6 rounded-2xl bg-white p-4 shadow-sm">
                    {{ $cases->links() }}
                </div>
            @endif
        </section>
    </div>

    <x-smooth-modal
        name="counseling-case-form"
        :title="$caseEditingId ? 'Edit Kasus Konseling' : 'Tambah Kasus Konseling'"
        max-width="2xl"
    >
        <form wire:submit="saveCase" class="space-y-5">
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Siswa
                </label>

                <select
                    wire:model="studentId"
                    class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
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
                    <p class="mt-2 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Judul kasus
                </label>

                <input
                    type="text"
                    wire:model="caseTitle"
                    placeholder="Contoh: Kesulitan mengikuti pembelajaran"
                    class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                >

                @error('caseTitle')
                    <p class="mt-2 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Kategori masalah
                    </label>

                    <select
                        wire:model="problemCategory"
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
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
                        Prioritas
                    </label>

                    <select
                        wire:model="priority"
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                    >
                        @foreach ($priorities as $value => $label)
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
                        Tingkat kerahasiaan
                    </label>

                    <select
                        wire:model="confidentialityLevel"
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                    >
                        @foreach ($confidentialityLevels as $value => $label)
                            <option value="{{ $value }}">
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Guru BK
                    </label>

                    <select
                        wire:model="assignedTo"
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                    >
                        <option value="">Belum ditentukan</option>

                        @foreach ($counselors as $counselor)
                            <option value="{{ $counselor->id }}">
                                {{ $counselor->name }}
                            </option>
                        @endforeach
                    </select>

                    @error('assignedTo')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Ringkasan masalah
                </label>

                <textarea
                    wire:model="caseSummary"
                    rows="4"
                    placeholder="Tuliskan ringkasan masalah secara jelas dan objektif..."
                    class="w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                ></textarea>

                @error('caseSummary')
                    <p class="mt-2 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Tanggal pembukaan
                    </label>

                    <input
                        type="date"
                        wire:model="openedAt"
                        max="{{ now()->format('Y-m-d') }}"
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                    >
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Status kasus
                    </label>

                    <select
                        wire:model="caseStatus"
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                    >
                        @foreach ($caseStatuses as $value => $label)
                            <option value="{{ $value }}">
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-3 border-t border-slate-100 pt-5">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal', { modal: 'counseling-case-form' })"
                    class="h-14 rounded-2xl border border-slate-200 bg-white text-sm font-bold text-slate-600"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="saveCase"
                    class="h-14 rounded-2xl bg-indigo-600 text-sm font-bold text-white disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="saveCase">
                        {{ $caseEditingId ? 'Simpan Perubahan' : 'Tambah Kasus' }}
                    </span>

                    <span wire:loading wire:target="saveCase">
                        Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </x-smooth-modal>

    <x-smooth-modal
        name="counseling-session-form"
        :title="$sessionEditingId ? 'Edit Pertemuan Konseling' : 'Tambah Pertemuan Konseling'"
        max-width="2xl"
    >
        <form wire:submit="saveSession" class="space-y-5">
            <div class="grid gap-4 sm:grid-cols-3">
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Tanggal
                    </label>

                    <input
                        type="date"
                        wire:model="sessionDate"
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                    >
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Jam mulai
                    </label>

                    <input
                        type="time"
                        wire:model="startTime"
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                    >
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Jam selesai
                    </label>

                    <input
                        type="time"
                        wire:model="endTime"
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                    >

                    @error('endTime')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Metode konseling
                    </label>

                    <select
                        wire:model="sessionMethod"
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                    >
                        @foreach ($sessionMethods as $value => $label)
                            <option value="{{ $value }}">
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Status pertemuan
                    </label>

                    <select
                        wire:model.live="attendanceStatus"
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                    >
                        @foreach ($attendanceStatuses as $value => $label)
                            <option value="{{ $value }}">
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Lokasi
                </label>

                <input
                    type="text"
                    wire:model="sessionLocation"
                    placeholder="Contoh: Ruang BK"
                    class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                >
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Agenda pertemuan
                </label>

                <textarea
                    wire:model="agenda"
                    rows="3"
                    placeholder="Tuliskan tujuan atau agenda konseling..."
                    class="w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                ></textarea>

                @error('agenda')
                    <p class="mt-2 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Hasil konseling
                </label>

                <textarea
                    wire:model="sessionResult"
                    rows="4"
                    placeholder="Tuliskan hasil pertemuan konseling..."
                    class="w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                ></textarea>

                @error('sessionResult')
                    <p class="mt-2 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Tindak lanjut
                </label>

                <textarea
                    wire:model="followUp"
                    rows="3"
                    placeholder="Tuliskan tindak lanjut yang akan dilakukan..."
                    class="w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                ></textarea>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Jadwal pertemuan berikutnya
                </label>

                <input
                    type="date"
                    wire:model="nextSessionDate"
                    class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                >

                @error('nextSessionDate')
                    <p class="mt-2 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <label class="flex cursor-pointer items-center justify-between rounded-2xl border border-violet-100 bg-violet-50 p-4">
                <div>
                    <p class="text-sm font-bold text-violet-700">
                        Orang tua sudah dihubungi
                    </p>

                    <p class="mt-1 text-xs text-violet-600">
                        Aktifkan apabila sudah berkomunikasi dengan orang tua.
                    </p>
                </div>

                <input
                    type="checkbox"
                    wire:model.live="isParentContacted"
                    class="h-5 w-5 rounded border-violet-300 text-violet-600 focus:ring-violet-500"
                >
            </label>

            @if ($isParentContacted)
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Tanggapan orang tua
                    </label>

                    <textarea
                        wire:model="parentResponse"
                        rows="3"
                        placeholder="Tuliskan tanggapan atau kesepakatan dengan orang tua..."
                        class="w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                    ></textarea>
                </div>
            @endif

            <div class="grid grid-cols-2 gap-3 border-t border-slate-100 pt-5">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal', { modal: 'counseling-session-form' })"
                    class="h-14 rounded-2xl border border-slate-200 bg-white text-sm font-bold text-slate-600"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="saveSession"
                    class="h-14 rounded-2xl bg-indigo-600 text-sm font-bold text-white disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="saveSession">
                        {{ $sessionEditingId ? 'Simpan Perubahan' : 'Tambah Pertemuan' }}
                    </span>

                    <span wire:loading wire:target="saveSession">
                        Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </x-smooth-modal>

    <x-smooth-modal
        name="counseling-history"
        title="Riwayat Bimbingan Konseling"
        max-width="2xl"
    >
        @if ($selectedCase)
            <div class="space-y-5">
                <section class="rounded-3xl bg-indigo-50 p-5">
                    <p class="text-xs font-bold uppercase tracking-wide text-indigo-500">
                        {{ $selectedCase->case_number }}
                    </p>

                    <h3 class="mt-2 text-lg font-extrabold text-slate-900">
                        {{ $selectedCase->title }}
                    </h3>

                    <p class="mt-2 text-sm font-bold text-indigo-700">
                        {{ $selectedCase->student->name }}
                        · {{ $selectedCase->student->schoolClass?->name ?? 'Tanpa kelas' }}
                    </p>

                    <div class="mt-4 flex flex-wrap gap-2">
                        <span class="rounded-full bg-white px-3 py-1 text-[10px] font-bold text-indigo-700">
                            {{ $selectedCase->category_label }}
                        </span>

                        <span class="rounded-full bg-white px-3 py-1 text-[10px] font-bold text-slate-700">
                            {{ $selectedCase->status_label }}
                        </span>

                        <span class="rounded-full bg-white px-3 py-1 text-[10px] font-bold text-violet-700">
                            {{ $selectedCase->confidentiality_label }}
                        </span>
                    </div>
                </section>

                <section>
                    <h4 class="text-sm font-extrabold text-slate-900">
                        Riwayat Pertemuan
                    </h4>

                    <div class="mt-4 space-y-4">
                        @forelse ($selectedCase->sessions as $session)
                            @php
                                $canModifySession =
                                    auth()->user()->isAdmin()
                                    || auth()->user()->isGuruBk()
                                    || $selectedCase->assigned_to === auth()->id()
                                    || $selectedCase->created_by === auth()->id();
                            @endphp

                            <article class="rounded-3xl border border-slate-100 bg-slate-50 p-4">
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <p class="text-sm font-extrabold text-slate-900">
                                            {{ $session->session_date->translatedFormat('d M Y') }}
                                        </p>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $session->method_label }}
                                            @if ($session->start_time)
                                                · {{ substr((string) $session->start_time, 0, 5) }}
                                            @endif
                                        </p>
                                    </div>

                                    <span class="rounded-full bg-white px-3 py-1 text-[10px] font-bold text-indigo-600">
                                        {{ $session->attendance_status_label }}
                                    </span>
                                </div>

                                <div class="mt-4">
                                    <p class="text-xs font-extrabold text-slate-700">
                                        Agenda
                                    </p>

                                    <p class="mt-1 whitespace-pre-line text-xs leading-5 text-slate-600">
                                        {{ $session->agenda }}
                                    </p>
                                </div>

                                @if ($session->result)
                                    <div class="mt-3 rounded-2xl bg-white p-3">
                                        <p class="text-xs font-extrabold text-indigo-700">
                                            Hasil Konseling
                                        </p>

                                        <p class="mt-1 whitespace-pre-line text-xs leading-5 text-slate-600">
                                            {{ $session->result }}
                                        </p>
                                    </div>
                                @endif

                                @if ($session->follow_up)
                                    <div class="mt-3 rounded-2xl bg-amber-50 p-3">
                                        <p class="text-xs font-extrabold text-amber-700">
                                            Tindak Lanjut
                                        </p>

                                        <p class="mt-1 whitespace-pre-line text-xs leading-5 text-amber-700">
                                            {{ $session->follow_up }}
                                        </p>
                                    </div>
                                @endif

                                @if ($session->is_parent_contacted)
                                    <div class="mt-3 rounded-2xl bg-emerald-50 p-3">
                                        <p class="text-xs font-extrabold text-emerald-700">
                                            Orang tua sudah dihubungi
                                        </p>

                                        @if ($session->parent_response)
                                            <p class="mt-1 whitespace-pre-line text-xs leading-5 text-emerald-700">
                                                {{ $session->parent_response }}
                                            </p>
                                        @endif
                                    </div>
                                @endif

                                @if ($canModifySession)
                                    <div class="mt-4 grid grid-cols-2 gap-2 border-t border-slate-200 pt-4">
                                        <button
                                            type="button"
                                            wire:click="editSession({{ $session->id }})"
                                            class="h-10 rounded-2xl bg-indigo-50 text-xs font-bold text-indigo-600"
                                        >
                                            Edit
                                        </button>

                                        <button
                                            type="button"
                                            x-on:click="
                                                confirmDelete({
                                                    title: 'Hapus pertemuan?',
                                                    text: 'Riwayat pertemuan konseling ini akan dihapus.'
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        $wire.deleteSession({{ $session->id }});
                                                    }
                                                })
                                            "
                                            class="h-10 rounded-2xl bg-red-50 text-xs font-bold text-red-600"
                                        >
                                            Hapus
                                        </button>
                                    </div>
                                @endif
                            </article>
                        @empty
                            <div class="rounded-3xl border border-dashed border-slate-300 p-8 text-center">
                                <p class="text-sm font-bold text-slate-600">
                                    Belum ada pertemuan konseling
                                </p>
                            </div>
                        @endforelse
                    </div>
                </section>
            </div>
        @endif
    </x-smooth-modal>
</div>
