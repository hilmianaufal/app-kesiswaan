<div>
    @php
        $canCreate = ! auth()->user()->isKepalaSekolah();
        $canManageTypes = auth()->user()->isAdmin();
    @endphp

    <div class="space-y-5">
        <section class="overflow-hidden rounded-[28px] bg-gradient-to-br from-red-600 via-rose-600 to-orange-500 p-5 text-white shadow-xl shadow-red-200 sm:p-7">
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
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-red-100">
                                Kedisiplinan Siswa
                            </p>

                            <h2 class="mt-1 text-2xl font-extrabold">
                                Pelanggaran dan Poin
                            </h2>
                        </div>
                    </div>

                    <p class="mt-4 max-w-xl text-sm leading-6 text-red-100">
                        Catat pelanggaran, poin kedisiplinan, kronologi,
                        tindakan, dan proses tindak lanjut siswa.
                    </p>
                </div>

                <div class="grid gap-2 sm:grid-cols-2">
                    @if ($canManageTypes)
                        <button
                            type="button"
                            wire:click="openTypeModal"
                            class="h-12 rounded-2xl border border-white/20 bg-white/10 px-5 text-sm font-bold text-white backdrop-blur"
                        >
                            Jenis Pelanggaran
                        </button>
                    @endif

                    @if ($canCreate)
                        <button
                            type="button"
                            wire:click="openCreateModal"
                            class="h-12 rounded-2xl bg-white px-5 text-sm font-bold text-red-600 shadow-lg"
                        >
                            Tambah Pelanggaran
                        </button>
                    @endif
                </div>
            </div>
        </section>

        <section class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            <article class="rounded-3xl border border-slate-100 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold text-slate-500">
                    Total kasus
                </p>

                <p class="mt-2 text-2xl font-extrabold text-slate-900">
                    {{ number_format($statistics['cases']) }}
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

            <article class="rounded-3xl border border-amber-100 bg-amber-50 p-4">
                <p class="text-xs font-semibold text-amber-600">
                    Belum selesai
                </p>

                <p class="mt-2 text-2xl font-extrabold text-amber-700">
                    {{ number_format($statistics['pending']) }}
                </p>
            </article>

            <article class="rounded-3xl border border-red-100 bg-red-50 p-4">
                <p class="text-xs font-semibold text-red-600">
                    Pelanggaran berat
                </p>

                <p class="mt-2 text-2xl font-extrabold text-red-700">
                    {{ number_format($statistics['heavy']) }}
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
                    placeholder="Cari siswa, NIS, pelanggaran, lokasi, atau kronologi..."
                    class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 pl-12 pr-12 text-sm outline-none focus:border-red-500 focus:bg-white focus:ring-4 focus:ring-red-100"
                >

                <div
                    wire:loading
                    wire:target="search"
                    class="absolute right-4 top-1/2 -translate-y-1/2"
                >
                    <svg
                        class="h-5 w-5 animate-spin text-red-600"
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

            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-3">
                <select
                    wire:model.live="filterClassId"
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-100"
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
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-100"
                >
                    <option value="">Semua kategori</option>

                    @foreach ($categories as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <select
                    wire:model.live="filterStatus"
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-100"
                >
                    <option value="">Semua status tindak lanjut</option>

                    @foreach ($followUpStatuses as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <input
                    type="date"
                    wire:model.live="filterDateFrom"
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-100"
                >

                <input
                    type="date"
                    wire:model.live="filterDateTo"
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-100"
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
                        Poin Siswa Tertinggi
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Lima siswa dengan akumulasi poin terbesar
                    </p>
                </div>

                <div class="grid gap-3 sm:grid-cols-2 lg:grid-cols-5">
                    @foreach ($topStudents as $index => $item)
                        <article class="rounded-3xl border border-slate-100 bg-white p-4 shadow-sm">
                            <div class="flex items-center justify-between">
                                <span class="flex h-9 w-9 items-center justify-center rounded-2xl bg-red-50 text-xs font-extrabold text-red-600">
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

                            <p class="mt-3 text-[11px] font-semibold text-red-600">
                                {{ $item->total_cases }} kasus
                            </p>
                        </article>
                    @endforeach
                </div>
            </section>
        @endif

        <section>
            <div class="mb-4">
                <h3 class="text-lg font-extrabold text-slate-900">
                    Riwayat Pelanggaran
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Ditemukan {{ $violations->total() }} catatan
                </p>
            </div>

            <div
                wire:loading.class="opacity-50"
                wire:target="search,filterClassId,filterCategory,filterStatus,filterDateFrom,filterDateTo"
                class="space-y-4"
            >
                @forelse ($violations as $violation)
                    @php
                        $canModify = auth()->user()->isAdmin()
                            || auth()->user()->isGuruBk()
                            || $violation->recorded_by === auth()->id();
                    @endphp

                    <article
                        wire:key="violation-{{ $violation->id }}"
                        class="rounded-[28px] border border-slate-100 bg-white p-5 shadow-sm transition hover:shadow-lg"
                    >
                        <div class="flex items-start gap-4">
                            @if ($violation->student->photo_url)
                                <img
                                    src="{{ $violation->student->photo_url }}"
                                    alt="{{ $violation->student->name }}"
                                    class="h-14 w-14 shrink-0 rounded-2xl object-cover"
                                >
                            @else
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-red-50 text-lg font-extrabold text-red-600">
                                    {{ $violation->student->initial }}
                                </div>
                            @endif

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <div>
                                        <h4 class="text-sm font-extrabold text-slate-900">
                                            {{ $violation->student->name }}
                                        </h4>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $violation->student->schoolClass?->name ?? 'Tanpa kelas' }}
                                            · NIS {{ $violation->student->nis }}
                                        </p>
                                    </div>

                                    <span
                                        @class([
                                            'rounded-full px-3 py-1 text-xs font-extrabold',
                                            'bg-yellow-50 text-yellow-700' => $violation->category === 'light',
                                            'bg-orange-50 text-orange-700' => $violation->category === 'medium',
                                            'bg-red-50 text-red-700' => $violation->category === 'heavy',
                                        ])
                                    >
                                        +{{ $violation->points }} poin
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <span
                                @class([
                                    'rounded-full px-3 py-1 text-[10px] font-bold',
                                    'bg-yellow-50 text-yellow-700' => $violation->category === 'light',
                                    'bg-orange-50 text-orange-700' => $violation->category === 'medium',
                                    'bg-red-50 text-red-700' => $violation->category === 'heavy',
                                ])
                            >
                                {{ $violation->category_label }}
                            </span>

                            <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold text-slate-500">
                                {{ $violation->violation_date->translatedFormat('d M Y') }}
                            </span>

                            <span
                                @class([
                                    'rounded-full px-3 py-1 text-[10px] font-bold',
                                    'bg-amber-50 text-amber-700' => $violation->follow_up_status === 'pending',
                                    'bg-blue-50 text-blue-700' => $violation->follow_up_status === 'in_progress',
                                    'bg-emerald-50 text-emerald-700' => $violation->follow_up_status === 'completed',
                                ])
                            >
                                {{ $violation->follow_up_status_label }}
                            </span>
                        </div>

                        <h5 class="mt-4 text-base font-extrabold text-slate-900">
                            {{ $violation->violation_name }}
                        </h5>

                        @if ($violation->location)
                            <p class="mt-2 text-xs font-semibold text-slate-500">
                                Lokasi: {{ $violation->location }}
                            </p>
                        @endif

                        <div class="mt-4 rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-extrabold text-slate-700">
                                Kronologi
                            </p>

                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">
                                {{ $violation->chronology }}
                            </p>
                        </div>

                        @if ($violation->action_taken)
                            <div class="mt-3 rounded-2xl border border-red-100 bg-red-50 p-4">
                                <p class="text-xs font-extrabold text-red-700">
                                    Tindakan atau Sanksi
                                </p>

                                <p class="mt-2 whitespace-pre-line text-xs leading-5 text-red-600">
                                    {{ $violation->action_taken }}
                                </p>
                            </div>
                        @endif

                        @if ($violation->notes)
                            <p class="mt-3 whitespace-pre-line text-xs leading-5 text-slate-500">
                                {{ $violation->notes }}
                            </p>
                        @endif

                        <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4">
                            <div>
                                <p class="text-[10px] text-slate-400">
                                    Dicatat oleh
                                </p>

                                <p class="mt-1 text-xs font-bold text-slate-600">
                                    {{ $violation->recorder?->name ?? 'Pengguna dihapus' }}
                                </p>
                            </div>

                            @if ($canModify)
                                <div class="flex gap-2">
                                    <button
                                        type="button"
                                        wire:click="edit({{ $violation->id }})"
                                        class="h-10 rounded-2xl bg-indigo-50 px-4 text-xs font-bold text-indigo-600"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        x-on:click="
                                            confirmDelete({
                                                title: 'Hapus pelanggaran?',
                                                text: @js('Catatan pelanggaran '.$violation->student->name.' akan dihapus.')
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $wire.deleteViolation({{ $violation->id }});
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
                    </article>
                @empty
                    <div class="rounded-[28px] border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-red-50 text-red-600">
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
                                    d="M12 9v2m0 4h.01M10.29 3.86L1.82 18a2 2 0 001.71 3h16.94a2 2 0 001.71-3L13.71 3.86a2 2 0 00-3.42 0z"
                                />
                            </svg>
                        </div>

                        <h4 class="mt-5 text-base font-extrabold text-slate-900">
                            Catatan pelanggaran belum ditemukan
                        </h4>

                        <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">
                            Tambahkan pelanggaran baru atau ubah pencarian
                            dan filter yang sedang digunakan.
                        </p>

                        @if ($canCreate)
                            <button
                                type="button"
                                wire:click="openCreateModal"
                                class="mt-5 rounded-2xl bg-red-600 px-5 py-3 text-sm font-bold text-white"
                            >
                                Tambah Pelanggaran
                            </button>
                        @endif
                    </div>
                @endforelse
            </div>

            @if ($violations->hasPages())
                <div class="mt-6 rounded-2xl bg-white p-4 shadow-sm">
                    {{ $violations->links() }}
                </div>
            @endif
        </section>
    </div>

    <x-smooth-modal
        name="violation-form"
        :title="$editingId ? 'Edit Pelanggaran Siswa' : 'Tambah Pelanggaran Siswa'"
        max-width="2xl"
    >
        <form wire:submit="save" class="space-y-5">
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Siswa
                </label>

                <select
                    wire:model="studentId"
                    class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-100"
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
                    Jenis pelanggaran
                </label>

                <select
                    wire:model.live="violationTypeId"
                    class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-100"
                >
                    <option value="">Pilih jenis pelanggaran</option>

                    @foreach ($violationTypes as $type)
                        <option
                            value="{{ $type->id }}"
                            @disabled(! $type->is_active && (string) $type->id !== $violationTypeId)
                        >
                            {{ $type->name }}
                            — {{ $type->category_label }}
                            — {{ $type->points }} poin
                            {{ $type->is_active ? '' : '(Tidak Aktif)' }}
                        </option>
                    @endforeach
                </select>

                @error('violationTypeId')
                    <p class="mt-2 text-xs font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Tanggal kejadian
                    </label>

                    <input
                        type="date"
                        wire:model="violationDate"
                        max="{{ now()->format('Y-m-d') }}"
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-100"
                    >

                    @error('violationDate')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Lokasi kejadian
                    </label>

                    <input
                        type="text"
                        wire:model="location"
                        placeholder="Contoh: Ruang kelas 8A"
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-100"
                    >
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Kronologi kejadian
                </label>

                <textarea
                    wire:model="chronology"
                    rows="4"
                    placeholder="Tuliskan kronologi secara jelas dan objektif..."
                    class="w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-100"
                ></textarea>

                @error('chronology')
                    <p class="mt-2 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Tindakan atau sanksi
                </label>

                <textarea
                    wire:model="actionTaken"
                    rows="3"
                    placeholder="Tindakan akan terisi otomatis dari jenis pelanggaran..."
                    class="w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-100"
                ></textarea>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Status tindak lanjut
                </label>

                <select
                    wire:model="followUpStatus"
                    class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-100"
                >
                    @foreach ($followUpStatuses as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Catatan tambahan
                </label>

                <textarea
                    wire:model="notes"
                    rows="3"
                    placeholder="Catatan tambahan bila diperlukan..."
                    class="w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-100"
                ></textarea>
            </div>

            <div class="grid grid-cols-2 gap-3 border-t border-slate-100 pt-5">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal', { modal: 'violation-form' })"
                    class="h-14 rounded-2xl border border-slate-200 bg-white text-sm font-bold text-slate-600"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="flex h-14 items-center justify-center rounded-2xl bg-gradient-to-r from-red-600 to-orange-500 text-sm font-bold text-white shadow-lg shadow-red-200 disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="save">
                        {{ $editingId ? 'Simpan Perubahan' : 'Simpan Pelanggaran' }}
                    </span>

                    <span wire:loading wire:target="save">
                        Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </x-smooth-modal>

    @if ($canManageTypes)
        <x-smooth-modal
            name="violation-type-form"
            title="Master Jenis Pelanggaran"
            max-width="2xl"
        >
            <div class="space-y-6">
                <form wire:submit="saveType" class="space-y-4">
                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            Nama jenis pelanggaran
                        </label>

                        <input
                            type="text"
                            wire:model="typeName"
                            placeholder="Contoh: Terlambat masuk sekolah"
                            class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-100"
                        >

                        @error('typeName')
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
                                wire:model="typeCategory"
                                class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-100"
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
                                Poin
                            </label>

                            <input
                                type="number"
                                wire:model="typePoints"
                                min="0"
                                max="1000"
                                class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-100"
                            >

                            @error('typePoints')
                                <p class="mt-2 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            Tindakan atau sanksi bawaan
                        </label>

                        <textarea
                            wire:model="typeDefaultAction"
                            rows="3"
                            placeholder="Tindakan otomatis ketika jenis pelanggaran dipilih..."
                            class="w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-100"
                        ></textarea>
                    </div>

                    <label class="flex cursor-pointer items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 p-4">
                        <div>
                            <p class="text-sm font-bold text-slate-700">
                                Jenis pelanggaran aktif
                            </p>

                            <p class="mt-1 text-xs text-slate-500">
                                Jenis aktif dapat dipilih saat pencatatan
                            </p>
                        </div>

                        <input
                            type="checkbox"
                            wire:model="typeIsActive"
                            class="h-5 w-5 rounded border-slate-300 text-red-600 focus:ring-red-500"
                        >
                    </label>

                    <div class="grid grid-cols-2 gap-3">
                        <button
                            type="button"
                            wire:click="openTypeModal"
                            class="h-13 rounded-2xl border border-slate-200 bg-white text-sm font-bold text-slate-600"
                        >
                            Reset Form
                        </button>

                        <button
                            type="submit"
                            wire:loading.attr="disabled"
                            wire:target="saveType"
                            class="h-13 rounded-2xl bg-red-600 text-sm font-bold text-white disabled:opacity-60"
                        >
                            {{ $typeEditingId ? 'Simpan Perubahan' : 'Tambah Jenis' }}
                        </button>
                    </div>
                </form>

                <div class="border-t border-slate-100 pt-5">
                    <h3 class="text-sm font-extrabold text-slate-900">
                        Daftar Jenis Pelanggaran
                    </h3>

                    <div class="mt-4 space-y-3">
                        @foreach ($violationTypes as $type)
                            <article
                                wire:key="violation-type-{{ $type->id }}"
                                class="rounded-2xl border border-slate-100 bg-slate-50 p-4"
                            >
                                <div class="flex items-start justify-between gap-3">
                                    <div>
                                        <h4 class="text-sm font-bold text-slate-900">
                                            {{ $type->name }}
                                        </h4>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $type->category_label }}
                                            · {{ $type->points }} poin
                                            · {{ $type->status_label }}
                                        </p>
                                    </div>

                                    <span
                                        @class([
                                            'rounded-full px-3 py-1 text-[10px] font-bold',
                                            'bg-emerald-100 text-emerald-700' => $type->is_active,
                                            'bg-slate-200 text-slate-600' => ! $type->is_active,
                                        ])
                                    >
                                        {{ $type->status_label }}
                                    </span>
                                </div>

                                @if ($type->default_action)
                                    <p class="mt-3 text-xs leading-5 text-slate-600">
                                        {{ $type->default_action }}
                                    </p>
                                @endif

                                <div class="mt-4 grid grid-cols-3 gap-2">
                                    <button
                                        type="button"
                                        wire:click="editType({{ $type->id }})"
                                        class="h-10 rounded-xl bg-indigo-50 text-xs font-bold text-indigo-600"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="toggleTypeStatus({{ $type->id }})"
                                        class="h-10 rounded-xl bg-amber-50 text-xs font-bold text-amber-600"
                                    >
                                        {{ $type->is_active ? 'Nonaktifkan' : 'Aktifkan' }}
                                    </button>

                                    <button
                                        type="button"
                                        x-on:click="
                                            confirmDelete({
                                                title: 'Hapus jenis?',
                                                text: @js('Jenis '.$type->name.' akan dihapus.')
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $wire.deleteType({{ $type->id }});
                                                }
                                            })
                                        "
                                        class="h-10 rounded-xl bg-red-50 text-xs font-bold text-red-600"
                                    >
                                        Hapus
                                    </button>
                                </div>
                            </article>
                        @endforeach
                    </div>
                </div>
            </div>
        </x-smooth-modal>
    @endif
</div>
