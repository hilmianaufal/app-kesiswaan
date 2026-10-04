<div>
    <div class="space-y-5">
        <section class="overflow-hidden rounded-[28px] bg-gradient-to-br from-violet-600 via-indigo-600 to-blue-500 p-5 text-white shadow-xl shadow-indigo-200 sm:p-7">
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
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-indigo-100">
                                Perkembangan Siswa
                            </p>

                            <h2 class="mt-1 text-2xl font-extrabold">
                                Catatan Perkembangan
                            </h2>
                        </div>
                    </div>

                    <p class="mt-4 max-w-xl text-sm leading-6 text-indigo-100">
                        Catat perkembangan akademik, perilaku, kedisiplinan,
                        tanggung jawab, dan hubungan sosial siswa.
                    </p>
                </div>

                <button
                    type="button"
                    wire:click="openCreateModal"
                    class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-white px-5 text-sm font-bold text-indigo-600 shadow-lg transition hover:-translate-y-0.5"
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

                    Tambah Catatan
                </button>
            </div>
        </section>

        <section class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            <article class="rounded-3xl border border-slate-100 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold text-slate-500">
                    Total catatan
                </p>

                <p class="mt-2 text-2xl font-extrabold text-slate-900">
                    {{ number_format($statistics['total']) }}
                </p>
            </article>

            <article class="rounded-3xl border border-emerald-100 bg-emerald-50 p-4">
                <p class="text-xs font-semibold text-emerald-600">
                    Catatan positif
                </p>

                <p class="mt-2 text-2xl font-extrabold text-emerald-700">
                    {{ number_format($statistics['positive']) }}
                </p>
            </article>

            <article class="rounded-3xl border border-amber-100 bg-amber-50 p-4">
                <p class="text-xs font-semibold text-amber-600">
                    Perlu perhatian
                </p>

                <p class="mt-2 text-2xl font-extrabold text-amber-700">
                    {{ number_format($statistics['attention']) }}
                </p>
            </article>

            <article class="rounded-3xl border border-red-100 bg-red-50 p-4">
                <p class="text-xs font-semibold text-red-600">
                    Belum selesai
                </p>

                <p class="mt-2 text-2xl font-extrabold text-red-700">
                    {{ number_format($statistics['pending']) }}
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
                    placeholder="Cari siswa, NIS, judul, atau isi catatan..."
                    class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 pl-12 pr-12 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100"
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

            <div class="mt-4 grid gap-3 sm:grid-cols-2 lg:grid-cols-4">
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
                    wire:model.live="filterType"
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                >
                    <option value="">Semua jenis catatan</option>

                    @foreach ($types as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <select
                    wire:model.live="filterFollowUpStatus"
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                >
                    <option value="">Semua status tindak lanjut</option>

                    @foreach ($followUpStatuses as $value => $label)
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
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                >

                <input
                    type="date"
                    wire:model.live="filterDateTo"
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                >

                <button
                    type="button"
                    wire:click="resetFilters"
                    class="h-12 rounded-2xl border border-slate-200 bg-white px-4 text-sm font-bold text-slate-600 transition hover:bg-slate-50"
                >
                    Reset Filter
                </button>
            </div>
        </section>

        <section>
            <div class="mb-4">
                <h3 class="text-lg font-extrabold text-slate-900">
                    Riwayat Perkembangan
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Ditemukan {{ $notes->total() }} catatan
                </p>
            </div>

            <div
                wire:loading.class="opacity-50"
                wire:target="search,filterClassId,filterCategory,filterType,filterFollowUpStatus,filterDateFrom,filterDateTo"
                class="space-y-4"
            >
                @forelse ($notes as $note)
                    @php
                        $canModify = auth()->user()->isAdmin()
                            || auth()->user()->isGuruBk()
                            || $note->recorded_by === auth()->id();
                    @endphp

                    <article
                        wire:key="development-note-{{ $note->id }}"
                        class="rounded-[28px] border border-slate-100 bg-white p-5 shadow-sm transition hover:shadow-lg"
                    >
                        <div class="flex items-start gap-4">
                            @if ($note->student->photo_url)
                                <img
                                    src="{{ $note->student->photo_url }}"
                                    alt="{{ $note->student->name }}"
                                    class="h-14 w-14 shrink-0 rounded-2xl object-cover"
                                >
                            @else
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 text-lg font-extrabold text-indigo-600">
                                    {{ $note->student->initial }}
                                </div>
                            @endif

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <div>
                                        <h4 class="text-sm font-extrabold text-slate-900">
                                            {{ $note->student->name }}
                                        </h4>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $note->student->schoolClass?->name ?? 'Tanpa kelas' }}
                                            · NIS {{ $note->student->nis }}
                                        </p>
                                    </div>

                                    <span
                                        @class([
                                            'rounded-full px-3 py-1 text-[10px] font-bold',
                                            'bg-emerald-50 text-emerald-600' => $note->type === 'positive',
                                            'bg-amber-50 text-amber-600' => $note->type === 'attention',
                                            'bg-red-50 text-red-600' => $note->type === 'follow_up',
                                        ])
                                    >
                                        {{ $note->type_label }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <span class="rounded-full bg-indigo-50 px-3 py-1 text-[10px] font-bold text-indigo-600">
                                {{ $note->category_label }}
                            </span>

                            <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold text-slate-500">
                                {{ $note->note_date->translatedFormat('d M Y') }}
                            </span>

                            @if ($note->is_private)
                                <span class="rounded-full bg-violet-50 px-3 py-1 text-[10px] font-bold text-violet-600">
                                    Catatan Privat
                                </span>
                            @endif
                        </div>

                        <h5 class="mt-4 text-base font-extrabold text-slate-900">
                            {{ $note->title }}
                        </h5>

                        <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">
                            {{ $note->description }}
                        </p>

                        @if ($note->follow_up)
                            <div class="mt-4 rounded-2xl border border-indigo-100 bg-indigo-50 p-4">
                                <p class="text-xs font-extrabold text-indigo-700">
                                    Tindak Lanjut
                                </p>

                                <p class="mt-2 whitespace-pre-line text-xs leading-5 text-indigo-600">
                                    {{ $note->follow_up }}
                                </p>

                                <span
                                    @class([
                                        'mt-3 inline-flex rounded-full px-3 py-1 text-[10px] font-bold',
                                        'bg-amber-100 text-amber-700' => $note->follow_up_status === 'pending',
                                        'bg-blue-100 text-blue-700' => $note->follow_up_status === 'in_progress',
                                        'bg-emerald-100 text-emerald-700' => $note->follow_up_status === 'completed',
                                    ])
                                >
                                    {{ $note->follow_up_status_label }}
                                </span>
                            </div>
                        @endif

                        <div class="mt-4 flex items-center justify-between border-t border-slate-100 pt-4">
                            <div>
                                <p class="text-[10px] text-slate-400">
                                    Dicatat oleh
                                </p>

                                <p class="mt-1 text-xs font-bold text-slate-600">
                                    {{ $note->recorder?->name ?? 'Pengguna dihapus' }}
                                </p>
                            </div>

                            @if ($canModify)
                                <div class="flex gap-2">
                                    <button
                                        type="button"
                                        wire:click="edit({{ $note->id }})"
                                        class="h-10 rounded-2xl bg-indigo-50 px-4 text-xs font-bold text-indigo-600"
                                    >
                                        Edit
                                    </button>

                                    <button
                                        type="button"
                                        x-on:click="
                                            confirmDelete({
                                                title: 'Hapus catatan?',
                                                text: @js('Catatan '.$note->student->name.' akan dihapus.')
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    $wire.delete({{ $note->id }});
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
                        <h4 class="text-base font-extrabold text-slate-900">
                            Catatan belum ditemukan
                        </h4>

                        <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">
                            Tambahkan catatan perkembangan baru atau ubah
                            filter yang sedang digunakan.
                        </p>

                        <button
                            type="button"
                            wire:click="openCreateModal"
                            class="mt-5 rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white"
                        >
                            Tambah Catatan
                        </button>
                    </div>
                @endforelse
            </div>

            @if ($notes->hasPages())
                <div class="mt-6 rounded-2xl bg-white p-4 shadow-sm">
                    {{ $notes->links() }}
                </div>
            @endif
        </section>
    </div>

    <x-smooth-modal
        name="development-note-form"
        :title="$editingId ? 'Edit Catatan Perkembangan' : 'Tambah Catatan Perkembangan'"
        max-width="2xl"
    >
        <form
            wire:submit="save"
            class="space-y-5"
        >
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
                    <p class="mt-2 text-xs font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Tanggal catatan
                    </label>

                    <input
                        type="date"
                        wire:model="noteDate"
                        max="{{ now()->format('Y-m-d') }}"
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                    >

                    @error('noteDate')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Kategori
                    </label>

                    <select
                        wire:model="category"
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                    >
                        @foreach ($categories as $value => $label)
                            <option value="{{ $value }}">
                                {{ $label }}
                            </option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Jenis catatan
                </label>

                <select
                    wire:model="type"
                    class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                >
                    @foreach ($types as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Judul catatan
                </label>

                <input
                    type="text"
                    wire:model="title"
                    placeholder="Contoh: Aktif dalam diskusi kelompok"
                    class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                >

                @error('title')
                    <p class="mt-2 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Isi catatan
                </label>

                <textarea
                    wire:model="description"
                    rows="4"
                    placeholder="Tuliskan perkembangan atau kejadian secara jelas..."
                    class="w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                ></textarea>

                @error('description')
                    <p class="mt-2 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Rencana tindak lanjut
                </label>

                <textarea
                    wire:model="followUp"
                    rows="3"
                    placeholder="Kosongkan apabila tidak memerlukan tindak lanjut"
                    class="w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                ></textarea>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Status tindak lanjut
                </label>

                <select
                    wire:model="followUpStatus"
                    class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                >
                    @foreach ($followUpStatuses as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <label class="flex cursor-pointer items-center justify-between rounded-2xl border border-violet-100 bg-violet-50 p-4">
                <div>
                    <p class="text-sm font-bold text-violet-700">
                        Jadikan catatan privat
                    </p>

                    <p class="mt-1 text-xs leading-5 text-violet-600">
                        Catatan privat hanya terlihat oleh pembuat, admin,
                        dan guru BK.
                    </p>
                </div>

                <div class="relative shrink-0">
                    <input
                        type="checkbox"
                        wire:model="isPrivate"
                        class="peer sr-only"
                    >

                    <div class="h-7 w-12 rounded-full bg-slate-300 transition peer-checked:bg-violet-600"></div>

                    <div class="absolute left-1 top-1 h-5 w-5 rounded-full bg-white shadow transition peer-checked:translate-x-5"></div>
                </div>
            </label>

            <div class="grid grid-cols-2 gap-3 border-t border-slate-100 pt-5">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal', { modal: 'development-note-form' })"
                    class="h-14 rounded-2xl border border-slate-200 bg-white text-sm font-bold text-slate-600"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="flex h-14 items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-500 text-sm font-bold text-white shadow-lg shadow-indigo-200 disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="save">
                        {{ $editingId ? 'Simpan Perubahan' : 'Tambah Catatan' }}
                    </span>

                    <span wire:loading wire:target="save">
                        Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </x-smooth-modal>
</div>
