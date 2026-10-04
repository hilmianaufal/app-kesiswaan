<div>
    <div class="space-y-5">
        <section class="overflow-hidden rounded-[28px] bg-gradient-to-br from-indigo-600 via-indigo-600 to-violet-500 p-5 text-white shadow-xl shadow-indigo-200 sm:p-7">
            <div class="flex flex-col gap-5 sm:flex-row sm:items-center sm:justify-between">
                <div>
                    <div class="flex items-center gap-3">
                        <a
                            href="{{ route('dashboard') }}"
                            wire:navigate
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
                                Master Data
                            </p>

                            <h2 class="mt-1 text-2xl font-extrabold">
                                Data Kelas
                            </h2>
                        </div>
                    </div>

                    <p class="mt-4 max-w-xl text-sm leading-6 text-indigo-100">
                        Kelola kelas, tingkat, jurusan, wali kelas, semester,
                        dan tahun ajaran dalam satu halaman.
                    </p>
                </div>

                <button
                    type="button"
                    wire:click="openCreateModal"
                    class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-white px-5 text-sm font-bold text-indigo-600 shadow-lg transition hover:-translate-y-0.5 hover:shadow-xl"
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

                    Tambah Kelas
                </button>
            </div>
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
                    placeholder="Cari nama kelas, jurusan, tahun ajaran, atau wali kelas..."
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
                    wire:model.live="filterAcademicYear"
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                >
                    <option value="">Semua tahun ajaran</option>

                    @foreach ($academicYears as $year)
                        <option value="{{ $year }}">
                            {{ $year }}
                        </option>
                    @endforeach
                </select>

                <select
                    wire:model.live="filterSemester"
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                >
                    <option value="">Semua semester</option>
                    <option value="ganjil">Semester Ganjil</option>
                    <option value="genap">Semester Genap</option>
                </select>

                <select
                    wire:model.live="filterStatus"
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                >
                    <option value="">Semua status</option>
                    <option value="1">Aktif</option>
                    <option value="0">Tidak Aktif</option>
                </select>

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
            <div class="mb-4 flex items-center justify-between">
                <div>
                    <h3 class="text-lg font-extrabold text-slate-900">
                        Daftar Kelas
                    </h3>

                    <p class="mt-1 text-xs text-slate-500">
                        Ditemukan {{ $schoolClasses->total() }} data kelas
                    </p>
                </div>
            </div>

            <div
                wire:loading.class="opacity-50"
                wire:target="search,filterAcademicYear,filterSemester,filterStatus,resetFilters"
                class="grid gap-4 md:grid-cols-2 xl:grid-cols-3"
            >
                @forelse ($schoolClasses as $schoolClass)
                    <article
                        wire:key="class-{{ $schoolClass->id }}"
                        class="group rounded-[28px] border border-slate-100 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl"
                    >
                        <div class="flex items-start justify-between gap-4">
                            <div class="flex min-w-0 items-center gap-3">
                                <div class="flex h-12 w-12 shrink-0 items-center justify-center rounded-2xl bg-indigo-50 font-extrabold text-indigo-600">
                                    {{ $schoolClass->level }}
                                </div>

                                <div class="min-w-0">
                                    <h4 class="truncate text-base font-extrabold text-slate-900">
                                        {{ $schoolClass->name }}
                                    </h4>

                                    <p class="mt-1 truncate text-xs text-slate-500">
                                        {{ $schoolClass->major ?: 'Tanpa jurusan' }}
                                    </p>
                                </div>
                            </div>

                            <span
                                @class([
                                    'shrink-0 rounded-full px-3 py-1 text-[10px] font-bold',
                                    'bg-emerald-50 text-emerald-600' => $schoolClass->is_active,
                                    'bg-slate-100 text-slate-500' => ! $schoolClass->is_active,
                                ])
                            >
                                {{ $schoolClass->status_label }}
                            </span>
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-3">
                            <div class="rounded-2xl bg-slate-50 p-3">
                                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                    Tahun Ajaran
                                </p>

                                <p class="mt-1 text-xs font-bold text-slate-700">
                                    {{ $schoolClass->academic_year }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-3">
                                <p class="text-[10px] font-semibold uppercase tracking-wide text-slate-400">
                                    Semester
                                </p>

                                <p class="mt-1 text-xs font-bold text-slate-700">
                                    {{ $schoolClass->semester_label }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 space-y-3">
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-xs text-slate-500">
                                    Wali kelas
                                </span>

                                <span class="truncate text-right text-xs font-bold text-slate-700">
                                    {{ $schoolClass->homeroomTeacher?->name ?? 'Belum ditentukan' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between gap-4">
                                <span class="text-xs text-slate-500">
                                    Kapasitas
                                </span>

                                <span class="text-xs font-bold text-slate-700">
                                    {{ $schoolClass->capacity }} siswa
                                </span>
                            </div>
                        </div>

                        @if ($schoolClass->description)
                            <p class="mt-4 line-clamp-2 rounded-2xl bg-indigo-50/60 p-3 text-xs leading-5 text-indigo-700">
                                {{ $schoolClass->description }}
                            </p>
                        @endif

                        <div class="mt-5 grid grid-cols-2 gap-3 border-t border-slate-100 pt-4">
                            <button
                                type="button"
                                wire:click="edit({{ $schoolClass->id }})"
                                class="flex h-11 items-center justify-center gap-2 rounded-2xl bg-indigo-50 text-xs font-bold text-indigo-600 transition hover:bg-indigo-100"
                            >
                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5M18.5 2.5a2.121 2.121 0 013 3L12 15l-4 1 1-4 9.5-9.5z"
                                    />
                                </svg>

                                Edit
                            </button>

                            <button
                                type="button"
                                x-on:click="
                                    confirmDelete({
                                        title: 'Hapus kelas?',
                                        text: 'Kelas {{ addslashes($schoolClass->name) }} akan dihapus.'
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $wire.delete({{ $schoolClass->id }});
                                        }
                                    })
                                "
                                class="flex h-11 items-center justify-center gap-2 rounded-2xl bg-red-50 text-xs font-bold text-red-600 transition hover:bg-red-100"
                            >
                                <svg
                                    class="h-4 w-4"
                                    fill="none"
                                    viewBox="0 0 24 24"
                                    stroke="currentColor"
                                    stroke-width="2"
                                >
                                    <path
                                        stroke-linecap="round"
                                        stroke-linejoin="round"
                                        d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7V4a1 1 0 011-1h4a1 1 0 011 1v3M4 7h16"
                                    />
                                </svg>

                                Hapus
                            </button>
                        </div>
                    </article>
                @empty
                    <div class="md:col-span-2 xl:col-span-3">
                        <div class="rounded-[28px] border border-dashed border-slate-300 bg-white px-6 py-14 text-center">
                            <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-indigo-50 text-indigo-600">
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
                                        d="M3 21h18M6 21V7l6-4 6 4v14M9 10h.01M9 14h.01M15 10h.01M15 14h.01"
                                    />
                                </svg>
                            </div>

                            <h4 class="mt-5 text-base font-extrabold text-slate-900">
                                Data kelas belum ditemukan
                            </h4>

                            <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">
                                Tambahkan kelas baru atau ubah kata pencarian
                                dan filter yang sedang digunakan.
                            </p>

                            <button
                                type="button"
                                wire:click="openCreateModal"
                                class="mt-5 rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white"
                            >
                                Tambah Kelas
                            </button>
                        </div>
                    </div>
                @endforelse
            </div>

            @if ($schoolClasses->hasPages())
                <div class="mt-6 rounded-2xl bg-white p-4 shadow-sm">
                    {{ $schoolClasses->links() }}
                </div>
            @endif
        </section>
    </div>

    <x-smooth-modal
        name="class-form"
        :title="$editingId ? 'Edit Data Kelas' : 'Tambah Data Kelas'"
        max-width="lg"
    >
        <form
            wire:submit="save"
            class="space-y-5"
        >
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Nama kelas
                </label>

                <input
                    type="text"
                    wire:model="name"
                    placeholder="Contoh: Kelas 7A"
                    class="h-13 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                >

                @error('name')
                    <p class="mt-2 text-xs font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Tingkat kelas
                    </label>

                    <select
                        wire:model="level"
                        class="h-13 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                    >
                        <option value="">Pilih tingkat</option>
                        <option value="7">Kelas 7</option>
                        <option value="8">Kelas 8</option>
                        <option value="9">Kelas 9</option>
                        <option value="10">Kelas 10</option>
                        <option value="11">Kelas 11</option>
                        <option value="12">Kelas 12</option>
                    </select>

                    @error('level')
                        <p class="mt-2 text-xs font-medium text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Jurusan
                    </label>

                    <input
                        type="text"
                        wire:model="major"
                        placeholder="Contoh: IPA"
                        class="h-13 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                    >

                    @error('major')
                        <p class="mt-2 text-xs font-medium text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Wali kelas
                </label>

                <select
                    wire:model="homeroomTeacherId"
                    class="h-13 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                >
                    <option value="">Belum ditentukan</option>

                    @foreach ($teachers as $teacher)
                        <option value="{{ $teacher->id }}">
                            {{ $teacher->name }} — {{ $teacher->role_label }}
                        </option>
                    @endforeach
                </select>

                @error('homeroomTeacherId')
                    <p class="mt-2 text-xs font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="grid gap-4 sm:grid-cols-2">
                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Tahun ajaran
                    </label>

                    <input
                        type="text"
                        wire:model="academicYear"
                        placeholder="2026/2027"
                        maxlength="9"
                        class="h-13 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                    >

                    @error('academicYear')
                        <p class="mt-2 text-xs font-medium text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div>
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Semester
                    </label>

                    <select
                        wire:model="semester"
                        class="h-13 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                    >
                        <option value="ganjil">Ganjil</option>
                        <option value="genap">Genap</option>
                    </select>

                    @error('semester')
                        <p class="mt-2 text-xs font-medium text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Kapasitas kelas
                </label>

                <input
                    type="number"
                    wire:model="capacity"
                    min="1"
                    max="100"
                    placeholder="36"
                    class="h-13 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                >

                @error('capacity')
                    <p class="mt-2 text-xs font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Catatan
                </label>

                <textarea
                    wire:model="description"
                    rows="3"
                    placeholder="Catatan tambahan mengenai kelas..."
                    class="w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none transition focus:border-indigo-500 focus:bg-white focus:ring-4 focus:ring-indigo-100"
                ></textarea>

                @error('description')
                    <p class="mt-2 text-xs font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <label class="flex cursor-pointer items-center justify-between rounded-2xl border border-slate-200 bg-slate-50 p-4">
                <div>
                    <p class="text-sm font-bold text-slate-700">
                        Status kelas aktif
                    </p>

                    <p class="mt-1 text-xs text-slate-500">
                        Kelas aktif dapat digunakan pada data siswa
                    </p>
                </div>

                <div class="relative">
                    <input
                        type="checkbox"
                        wire:model="isActive"
                        class="peer sr-only"
                    >

                    <div class="h-7 w-12 rounded-full bg-slate-300 transition peer-checked:bg-indigo-600"></div>

                    <div class="absolute left-1 top-1 h-5 w-5 rounded-full bg-white shadow transition peer-checked:translate-x-5"></div>
                </div>
            </label>

            <div class="grid grid-cols-2 gap-3 border-t border-slate-100 pt-5">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal', { modal: 'class-form' })"
                    class="h-13 rounded-2xl border border-slate-200 bg-white text-sm font-bold text-slate-600 transition hover:bg-slate-50"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="save"
                    class="flex h-13 items-center justify-center gap-2 rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-500 text-sm font-bold text-white shadow-lg shadow-indigo-200 transition disabled:cursor-not-allowed disabled:opacity-60"
                >
                    <svg
                        wire:loading
                        wire:target="save"
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

                    <span wire:loading.remove wire:target="save">
                        {{ $editingId ? 'Simpan Perubahan' : 'Tambah Kelas' }}
                    </span>

                    <span wire:loading wire:target="save">
                        Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </x-smooth-modal>
</div>
