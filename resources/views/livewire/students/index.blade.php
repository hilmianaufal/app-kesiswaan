<div>
    <div class="space-y-5">
        <section class="overflow-hidden rounded-[28px] bg-gradient-to-br from-blue-600 via-indigo-600 to-violet-500 p-5 text-white shadow-xl shadow-indigo-200 sm:p-7">
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
                                Master Data
                            </p>

                            <h2 class="mt-1 text-2xl font-extrabold">
                                Data Siswa
                            </h2>
                        </div>
                    </div>

                    <p class="mt-4 max-w-xl text-sm leading-6 text-indigo-100">
                        Kelola identitas, kelas, orang tua, foto, dan status
                        seluruh siswa.
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

                    Tambah Siswa
                </button>
            </div>
        </section>

        <section class="grid grid-cols-2 gap-3 lg:grid-cols-4">
            <article class="rounded-3xl border border-slate-100 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold text-slate-500">
                    Total siswa
                </p>

                <p class="mt-2 text-2xl font-extrabold text-slate-900">
                    {{ number_format($statistics['total']) }}
                </p>
            </article>

            <article class="rounded-3xl border border-slate-100 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold text-emerald-600">
                    Siswa aktif
                </p>

                <p class="mt-2 text-2xl font-extrabold text-slate-900">
                    {{ number_format($statistics['active']) }}
                </p>
            </article>

            <article class="rounded-3xl border border-slate-100 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold text-blue-600">
                    Laki-laki
                </p>

                <p class="mt-2 text-2xl font-extrabold text-slate-900">
                    {{ number_format($statistics['male']) }}
                </p>
            </article>

            <article class="rounded-3xl border border-slate-100 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold text-pink-600">
                    Perempuan
                </p>

                <p class="mt-2 text-2xl font-extrabold text-slate-900">
                    {{ number_format($statistics['female']) }}
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
                    placeholder="Cari nama, NIS, NISN, kelas, atau nomor orang tua..."
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
                    wire:model.live="filterGender"
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                >
                    <option value="">Semua jenis kelamin</option>

                    @foreach ($genders as $value => $label)
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

                    @foreach ($statuses as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <button
                    type="button"
                    wire:click="resetFilters"
                    class="h-12 rounded-2xl border border-slate-200 bg-white text-sm font-bold text-slate-600 transition hover:bg-slate-50"
                >
                    Reset Filter
                </button>
            </div>
        </section>

        <section>
            <div class="mb-4">
                <h3 class="text-lg font-extrabold text-slate-900">
                    Daftar Siswa
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Ditemukan {{ $students->total() }} data siswa
                </p>
            </div>

            <div
                wire:loading.class="opacity-50"
                wire:target="search,filterClassId,filterGender,filterStatus"
                class="grid gap-4 md:grid-cols-2 xl:grid-cols-3"
            >
                @forelse ($students as $student)
                    <article
                        wire:key="student-{{ $student->id }}"
                        class="rounded-[28px] border border-slate-100 bg-white p-5 shadow-sm transition duration-300 hover:-translate-y-1 hover:shadow-xl"
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
                                            NIS: {{ $student->nis }}
                                        </p>
                                    </div>

                                    <span
                                        @class([
                                            'shrink-0 rounded-full px-3 py-1 text-[10px] font-bold',
                                            'bg-emerald-50 text-emerald-600' => $student->status === 'active',
                                            'bg-blue-50 text-blue-600' => $student->status === 'graduated',
                                            'bg-amber-50 text-amber-600' => $student->status === 'moved',
                                            'bg-slate-100 text-slate-500' => in_array($student->status, ['inactive', 'dropped']),
                                        ])
                                    >
                                        {{ $student->status_label }}
                                    </span>
                                </div>

                                <p class="mt-2 text-xs font-semibold text-indigo-600">
                                    {{ $student->schoolClass?->name ?? 'Belum memiliki kelas' }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-3">
                            <div class="rounded-2xl bg-slate-50 p-3">
                                <p class="text-[10px] uppercase tracking-wide text-slate-400">
                                    Jenis Kelamin
                                </p>

                                <p class="mt-1 text-xs font-bold text-slate-700">
                                    {{ $student->gender_label }}
                                </p>
                            </div>

                            <div class="rounded-2xl bg-slate-50 p-3">
                                <p class="text-[10px] uppercase tracking-wide text-slate-400">
                                    Tahun Masuk
                                </p>

                                <p class="mt-1 text-xs font-bold text-slate-700">
                                    {{ $student->enrollment_year ?? '-' }}
                                </p>
                            </div>
                        </div>

                        <div class="mt-4 space-y-2">
                            <div class="flex items-center justify-between gap-4">
                                <span class="text-xs text-slate-500">
                                    NISN
                                </span>

                                <span class="truncate text-xs font-bold text-slate-700">
                                    {{ $student->nisn ?: '-' }}
                                </span>
                            </div>

                            <div class="flex items-center justify-between gap-4">
                                <span class="text-xs text-slate-500">
                                    Nomor orang tua
                                </span>

                                <span class="truncate text-xs font-bold text-slate-700">
                                    {{ $student->parent_phone ?: '-' }}
                                </span>
                            </div>
                        </div>

                        <div class="mt-5 grid grid-cols-2 gap-3 border-t border-slate-100 pt-4">
                            <button
                                type="button"
                                wire:click="edit({{ $student->id }})"
                                class="h-11 rounded-2xl bg-indigo-50 text-xs font-bold text-indigo-600 transition hover:bg-indigo-100"
                            >
                                Edit
                            </button>

                            <button
                                type="button"
                                x-on:click="
                                    confirmDelete({
                                        title: 'Hapus siswa?',
                                        text: @js('Data siswa '.$student->name.' akan dihapus.')
                                    }).then((result) => {
                                        if (result.isConfirmed) {
                                            $wire.delete({{ $student->id }});
                                        }
                                    })
                                "
                                class="h-11 rounded-2xl bg-red-50 text-xs font-bold text-red-600 transition hover:bg-red-100"
                            >
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
                                        d="M17 20h5v-2a4 4 0 00-4-4h-1M9 20H2v-2a4 4 0 014-4h3m4 6h4v-2a4 4 0 00-4-4h-2a4 4 0 00-4 4v2h6zM8 11a4 4 0 100-8 4 4 0 000 8z"
                                    />
                                </svg>
                            </div>

                            <h4 class="mt-5 text-base font-extrabold text-slate-900">
                                Data siswa belum ditemukan
                            </h4>

                            <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">
                                Tambahkan siswa baru atau ubah pencarian dan
                                filter yang digunakan.
                            </p>

                            <button
                                type="button"
                                wire:click="openCreateModal"
                                class="mt-5 rounded-2xl bg-indigo-600 px-5 py-3 text-sm font-bold text-white"
                            >
                                Tambah Siswa
                            </button>
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
        name="student-form"
        :title="$editingId ? 'Edit Data Siswa' : 'Tambah Data Siswa'"
        max-width="2xl"
    >
        <form
            wire:submit="save"
            class="space-y-6"
        >
            <section>
                <h3 class="text-sm font-extrabold text-slate-900">
                    Foto Siswa
                </h3>

                <div class="mt-3 flex items-center gap-4">
                    @if ($photo)
                        <img
                            src="{{ $photo->temporaryUrl() }}"
                            class="h-20 w-20 rounded-2xl object-cover"
                            alt="Preview foto"
                        >
                    @elseif ($existingPhoto)
                        <img
                            src="{{ Storage::disk('public')->url($existingPhoto) }}"
                            class="h-20 w-20 rounded-2xl object-cover"
                            alt="Foto siswa"
                        >
                    @else
                        <div class="flex h-20 w-20 items-center justify-center rounded-2xl bg-slate-100 text-xs font-bold text-slate-400">
                            FOTO
                        </div>
                    @endif

                    <div class="flex-1">
                        <label class="flex cursor-pointer items-center justify-center rounded-2xl border border-dashed border-indigo-300 bg-indigo-50 px-4 py-3 text-xs font-bold text-indigo-600">
                            Pilih Foto

                            <input
                                type="file"
                                wire:model="photo"
                                accept="image/*"
                                class="hidden"
                            >
                        </label>

                        @if ($photo)
                            <button
                                type="button"
                                wire:click="cancelNewPhoto"
                                class="mt-2 text-xs font-bold text-red-600"
                            >
                                Batalkan foto baru
                            </button>
                        @elseif ($existingPhoto)
                            <button
                                type="button"
                                wire:click="removeCurrentPhoto"
                                class="mt-2 text-xs font-bold text-red-600"
                            >
                                Hapus foto
                            </button>
                        @endif

                        <p class="mt-2 text-[10px] text-slate-500">
                            JPG, PNG atau WEBP. Maksimal 2 MB.
                        </p>
                    </div>
                </div>

                @error('photo')
                    <p class="mt-2 text-xs font-medium text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </section>

            <section>
                <h3 class="text-sm font-extrabold text-slate-900">
                    Identitas Siswa
                </h3>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            NIS
                        </label>

                        <input
                            type="text"
                            wire:model="nis"
                            placeholder="Masukkan NIS"
                            class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        >

                        @error('nis')
                            <p class="mt-2 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            NISN
                        </label>

                        <input
                            type="text"
                            wire:model="nisn"
                            placeholder="Masukkan NISN"
                            class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        >

                        @error('nisn')
                            <p class="mt-2 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Nama lengkap
                    </label>

                    <input
                        type="text"
                        wire:model="name"
                        placeholder="Masukkan nama lengkap"
                        class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                    >

                    @error('name')
                        <p class="mt-2 text-xs text-red-600">
                            {{ $message }}
                        </p>
                    @enderror
                </div>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            Jenis kelamin
                        </label>

                        <select
                            wire:model="gender"
                            class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        >
                            <option value="">Pilih jenis kelamin</option>

                            @foreach ($genders as $value => $label)
                                <option value="{{ $value }}">
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>

                        @error('gender')
                            <p class="mt-2 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            Kelas
                        </label>

                        <select
                            wire:model="schoolClassId"
                            class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        >
                            <option value="">Belum memiliki kelas</option>

                            @foreach ($schoolClasses as $class)
                                <option value="{{ $class->id }}">
                                    {{ $class->name }} — {{ $class->academic_year }}
                                </option>
                            @endforeach
                        </select>

                        @error('schoolClassId')
                            <p class="mt-2 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            Tempat lahir
                        </label>

                        <input
                            type="text"
                            wire:model="birthPlace"
                            placeholder="Contoh: Bandung"
                            class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            Tanggal lahir
                        </label>

                        <input
                            type="date"
                            wire:model="birthDate"
                            class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        >

                        @error('birthDate')
                            <p class="mt-2 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>
                </div>

                <div class="mt-4">
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Alamat
                    </label>

                    <textarea
                        wire:model="address"
                        rows="3"
                        placeholder="Masukkan alamat siswa"
                        class="w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                    ></textarea>
                </div>
            </section>

            <section>
                <h3 class="text-sm font-extrabold text-slate-900">
                    Data Orang Tua
                </h3>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            Nama ayah
                        </label>

                        <input
                            type="text"
                            wire:model="fatherName"
                            class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            Nama ibu
                        </label>

                        <input
                            type="text"
                            wire:model="motherName"
                            class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            Nama wali
                        </label>

                        <input
                            type="text"
                            wire:model="guardianName"
                            class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            Nomor WhatsApp orang tua
                        </label>

                        <input
                            type="text"
                            wire:model="parentPhone"
                            placeholder="08xxxxxxxxxx"
                            class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        >
                    </div>
                </div>
            </section>

            <section>
                <h3 class="text-sm font-extrabold text-slate-900">
                    Status Siswa
                </h3>

                <div class="mt-4 grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            Tahun masuk
                        </label>

                        <input
                            type="number"
                            wire:model="enrollmentYear"
                            min="2000"
                            max="{{ now()->year + 1 }}"
                            class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        >
                    </div>

                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            Status siswa
                        </label>

                        <select
                            wire:model="status"
                            class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                        >
                            @foreach ($statuses as $value => $label)
                                <option value="{{ $value }}">
                                    {{ $label }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                </div>

                <div class="mt-4">
                    <label class="mb-2 block text-sm font-bold text-slate-700">
                        Catatan
                    </label>

                    <textarea
                        wire:model="notes"
                        rows="3"
                        placeholder="Catatan tambahan mengenai siswa"
                        class="w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-indigo-500 focus:ring-4 focus:ring-indigo-100"
                    ></textarea>
                </div>
            </section>

            <div class="grid grid-cols-2 gap-3 border-t border-slate-100 pt-5">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal', { modal: 'student-form' })"
                    class="h-14 rounded-2xl border border-slate-200 bg-white text-sm font-bold text-slate-600"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="save,photo"
                    class="flex h-14 items-center justify-center rounded-2xl bg-gradient-to-r from-indigo-600 to-violet-500 text-sm font-bold text-white shadow-lg shadow-indigo-200 disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="save">
                        {{ $editingId ? 'Simpan Perubahan' : 'Tambah Siswa' }}
                    </span>

                    <span wire:loading wire:target="save">
                        Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </x-smooth-modal>
</div>
