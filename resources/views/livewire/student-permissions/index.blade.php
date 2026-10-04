<div>
    @php
        $canCreate = ! auth()->user()->isKepalaSekolah();

        $canApprove = auth()->user()->isAdmin()
            || auth()->user()->isKepalaSekolah()
            || auth()->user()->isGuruBk();
    @endphp

    <div class="space-y-5">
        <section class="overflow-hidden rounded-[28px] bg-gradient-to-br from-cyan-600 via-sky-600 to-blue-600 p-5 text-white shadow-xl shadow-sky-200 sm:p-7">
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
                            <p class="text-xs font-semibold uppercase tracking-[0.18em] text-sky-100">
                                Administrasi Siswa
                            </p>

                            <h2 class="mt-1 text-2xl font-extrabold">
                                Perizinan Siswa
                            </h2>
                        </div>
                    </div>

                    <p class="mt-4 max-w-xl text-sm leading-6 text-sky-100">
                        Kelola izin tidak masuk, izin keluar sekolah,
                        dan izin pulang lebih awal.
                    </p>
                </div>

                @if ($canCreate)
                    <button
                        type="button"
                        wire:click="openCreateModal"
                        class="inline-flex h-12 items-center justify-center gap-2 rounded-2xl bg-white px-5 text-sm font-bold text-sky-600 shadow-lg"
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

                        Tambah Perizinan
                    </button>
                @endif
            </div>
        </section>

        <section class="grid grid-cols-2 gap-3 lg:grid-cols-5">
            <article class="rounded-3xl border border-slate-100 bg-white p-4 shadow-sm">
                <p class="text-xs font-semibold text-slate-500">
                    Total izin
                </p>

                <p class="mt-2 text-2xl font-extrabold text-slate-900">
                    {{ number_format($statistics['total']) }}
                </p>
            </article>

            <article class="rounded-3xl border border-amber-100 bg-amber-50 p-4">
                <p class="text-xs font-semibold text-amber-600">
                    Menunggu
                </p>

                <p class="mt-2 text-2xl font-extrabold text-amber-700">
                    {{ number_format($statistics['pending']) }}
                </p>
            </article>

            <article class="rounded-3xl border border-emerald-100 bg-emerald-50 p-4">
                <p class="text-xs font-semibold text-emerald-600">
                    Disetujui
                </p>

                <p class="mt-2 text-2xl font-extrabold text-emerald-700">
                    {{ number_format($statistics['approved']) }}
                </p>
            </article>

            <article class="rounded-3xl border border-red-100 bg-red-50 p-4">
                <p class="text-xs font-semibold text-red-600">
                    Ditolak
                </p>

                <p class="mt-2 text-2xl font-extrabold text-red-700">
                    {{ number_format($statistics['rejected']) }}
                </p>
            </article>

            <article class="col-span-2 rounded-3xl border border-sky-100 bg-sky-50 p-4 lg:col-span-1">
                <p class="text-xs font-semibold text-sky-600">
                    Sedang di luar
                </p>

                <p class="mt-2 text-2xl font-extrabold text-sky-700">
                    {{ number_format($statistics['outside']) }}
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
                    placeholder="Cari siswa, NIS, alasan, atau catatan penolakan..."
                    class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 pl-12 pr-12 text-sm outline-none focus:border-sky-500 focus:bg-white focus:ring-4 focus:ring-sky-100"
                >

                <div
                    wire:loading
                    wire:target="search"
                    class="absolute right-4 top-1/2 -translate-y-1/2"
                >
                    <svg
                        class="h-5 w-5 animate-spin text-sky-600"
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
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
                >
                    <option value="">Semua kelas</option>

                    @foreach ($schoolClasses as $class)
                        <option value="{{ $class->id }}">
                            {{ $class->name }} — {{ $class->academic_year }}
                        </option>
                    @endforeach
                </select>

                <select
                    wire:model.live="filterType"
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
                >
                    <option value="">Semua jenis izin</option>

                    @foreach ($permissionTypes as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                    @endforeach
                </select>

                <select
                    wire:model.live="filterStatus"
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
                >
                    <option value="">Semua status</option>

                    @foreach ($permissionStatuses as $value => $label)
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

            <div class="mt-3 grid gap-3 sm:grid-cols-2">
                <input
                    type="date"
                    wire:model.live="filterDateFrom"
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
                >

                <input
                    type="date"
                    wire:model.live="filterDateTo"
                    class="h-12 rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
                >
            </div>
        </section>

        <section>
            <div class="mb-4">
                <h3 class="text-lg font-extrabold text-slate-900">
                    Riwayat Perizinan
                </h3>

                <p class="mt-1 text-xs text-slate-500">
                    Ditemukan {{ $permissions->total() }} pengajuan
                </p>
            </div>

            <div
                wire:loading.class="opacity-50"
                wire:target="search,filterClassId,filterType,filterStatus,filterDateFrom,filterDateTo"
                class="space-y-4"
            >
                @forelse ($permissions as $permission)
                    @php
                        $canModify = auth()->user()->isAdmin()
                            || auth()->user()->isGuruBk()
                            || (
                                $permission->recorded_by === auth()->id()
                                && $permission->status === 'pending'
                            );
                    @endphp

                    <article
                        wire:key="permission-{{ $permission->id }}"
                        class="rounded-[28px] border border-slate-100 bg-white p-5 shadow-sm transition hover:shadow-lg"
                    >
                        <div class="flex items-start gap-4">
                            @if ($permission->student->photo_url)
                                <img
                                    src="{{ $permission->student->photo_url }}"
                                    alt="{{ $permission->student->name }}"
                                    class="h-14 w-14 shrink-0 rounded-2xl object-cover"
                                >
                            @else
                                <div class="flex h-14 w-14 shrink-0 items-center justify-center rounded-2xl bg-sky-50 text-lg font-extrabold text-sky-600">
                                    {{ $permission->student->initial }}
                                </div>
                            @endif

                            <div class="min-w-0 flex-1">
                                <div class="flex flex-wrap items-start justify-between gap-2">
                                    <div>
                                        <h4 class="text-sm font-extrabold text-slate-900">
                                            {{ $permission->student->name }}
                                        </h4>

                                        <p class="mt-1 text-xs text-slate-500">
                                            {{ $permission->student->schoolClass?->name ?? 'Tanpa kelas' }}
                                            · NIS {{ $permission->student->nis }}
                                        </p>
                                    </div>

                                    <span
                                        @class([
                                            'rounded-full px-3 py-1 text-[10px] font-bold',
                                            'bg-amber-50 text-amber-700' => $permission->status === 'pending',
                                            'bg-emerald-50 text-emerald-700' => $permission->status === 'approved',
                                            'bg-red-50 text-red-700' => $permission->status === 'rejected',
                                            'bg-blue-50 text-blue-700' => $permission->status === 'completed',
                                        ])
                                    >
                                        {{ $permission->status_label }}
                                    </span>
                                </div>
                            </div>
                        </div>

                        <div class="mt-4 flex flex-wrap gap-2">
                            <span class="rounded-full bg-sky-50 px-3 py-1 text-[10px] font-bold text-sky-700">
                                {{ $permission->type_label }}
                            </span>

                            <span class="rounded-full bg-slate-100 px-3 py-1 text-[10px] font-bold text-slate-500">
                                {{ $permission->permission_date->translatedFormat('d M Y') }}
                            </span>

                            @if ($permission->is_currently_outside)
                                <span class="rounded-full bg-orange-50 px-3 py-1 text-[10px] font-bold text-orange-700">
                                    Sedang di luar sekolah
                                </span>
                            @endif
                        </div>

                        @if ($permission->start_time)
                            <div class="mt-4 grid grid-cols-2 gap-3 sm:grid-cols-3">
                                <div class="rounded-2xl bg-slate-50 p-3">
                                    <p class="text-[10px] font-semibold uppercase text-slate-400">
                                        Jam Keluar
                                    </p>

                                    <p class="mt-1 text-xs font-bold text-slate-700">
                                        {{ substr((string) $permission->start_time, 0, 5) }}
                                    </p>
                                </div>

                                @if ($permission->expected_return_time)
                                    <div class="rounded-2xl bg-slate-50 p-3">
                                        <p class="text-[10px] font-semibold uppercase text-slate-400">
                                            Perkiraan Kembali
                                        </p>

                                        <p class="mt-1 text-xs font-bold text-slate-700">
                                            {{ substr((string) $permission->expected_return_time, 0, 5) }}
                                        </p>
                                    </div>
                                @endif

                                @if ($permission->actual_return_time)
                                    <div class="rounded-2xl bg-emerald-50 p-3">
                                        <p class="text-[10px] font-semibold uppercase text-emerald-500">
                                            Kembali
                                        </p>

                                        <p class="mt-1 text-xs font-bold text-emerald-700">
                                            {{ substr((string) $permission->actual_return_time, 0, 5) }}
                                        </p>
                                    </div>
                                @endif
                            </div>
                        @endif

                        <div class="mt-4 rounded-2xl bg-slate-50 p-4">
                            <p class="text-xs font-extrabold text-slate-700">
                                Alasan Izin
                            </p>

                            <p class="mt-2 whitespace-pre-line text-sm leading-6 text-slate-600">
                                {{ $permission->reason }}
                            </p>
                        </div>

                        @if ($permission->evidence_url)
                            <a
                                href="{{ $permission->evidence_url }}"
                                target="_blank"
                                class="mt-3 flex h-11 items-center justify-center rounded-2xl border border-sky-100 bg-sky-50 text-xs font-bold text-sky-600"
                            >
                                Lihat Bukti Perizinan
                            </a>
                        @endif

                        @if ($permission->status === 'rejected')
                            <div class="mt-3 rounded-2xl border border-red-100 bg-red-50 p-4">
                                <p class="text-xs font-extrabold text-red-700">
                                    Alasan Penolakan
                                </p>

                                <p class="mt-2 whitespace-pre-line text-xs leading-5 text-red-600">
                                    {{ $permission->rejection_note }}
                                </p>
                            </div>
                        @endif

                        @if ($permission->approved_by)
                            <div class="mt-3 rounded-2xl bg-indigo-50 p-3">
                                <p class="text-xs font-bold text-indigo-700">
                                    Diproses oleh
                                    {{ $permission->approver?->name ?? 'Pengguna dihapus' }}
                                </p>

                                @if ($permission->approved_at)
                                    <p class="mt-1 text-[10px] text-indigo-600">
                                        {{ $permission->approved_at->translatedFormat('d M Y H:i') }}
                                    </p>
                                @endif
                            </div>
                        @endif

                        <div class="mt-4 border-t border-slate-100 pt-4">
                            @if (
                                $canApprove
                                && $permission->status === 'pending'
                            )
                                <div class="mb-3 grid grid-cols-2 gap-2">
                                    <button
                                        type="button"
                                        wire:click="approvePermission({{ $permission->id }})"
                                        class="h-11 rounded-2xl bg-emerald-600 text-xs font-bold text-white"
                                    >
                                        Setujui
                                    </button>

                                    <button
                                        type="button"
                                        wire:click="openRejectModal({{ $permission->id }})"
                                        class="h-11 rounded-2xl bg-red-50 text-xs font-bold text-red-600"
                                    >
                                        Tolak
                                    </button>
                                </div>
                            @endif

                            @if ($permission->is_currently_outside)
                                <button
                                    type="button"
                                    wire:click="markReturned({{ $permission->id }})"
                                    class="mb-3 h-11 w-full rounded-2xl bg-sky-600 text-xs font-bold text-white"
                                >
                                    Tandai Siswa Sudah Kembali
                                </button>
                            @endif

                            <div class="flex items-center justify-between gap-3">
                                <div>
                                    <p class="text-[10px] text-slate-400">
                                        Dicatat oleh
                                    </p>

                                    <p class="mt-1 text-xs font-bold text-slate-600">
                                        {{ $permission->recorder?->name ?? 'Pengguna dihapus' }}
                                    </p>
                                </div>

                                @if ($canModify)
                                    <div class="flex gap-2">
                                        <button
                                            type="button"
                                            wire:click="edit({{ $permission->id }})"
                                            class="h-10 rounded-2xl bg-indigo-50 px-4 text-xs font-bold text-indigo-600"
                                        >
                                            Edit
                                        </button>

                                        <button
                                            type="button"
                                            x-on:click="
                                                confirmDelete({
                                                    title: 'Hapus perizinan?',
                                                    text: @js('Perizinan '.$permission->student->name.' akan dihapus.')
                                                }).then((result) => {
                                                    if (result.isConfirmed) {
                                                        $wire.deletePermission({{ $permission->id }});
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
                        <div class="mx-auto flex h-16 w-16 items-center justify-center rounded-3xl bg-sky-50 text-sky-600">
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
                                    d="M9 12h6m-6 4h6M7 4h10a2 2 0 012 2v14l-4-3-3 3-3-3-4 3V6a2 2 0 012-2z"
                                />
                            </svg>
                        </div>

                        <h4 class="mt-5 text-base font-extrabold text-slate-900">
                            Perizinan belum ditemukan
                        </h4>

                        <p class="mx-auto mt-2 max-w-sm text-sm leading-6 text-slate-500">
                            Tambahkan pengajuan izin baru atau ubah
                            pencarian dan filter yang digunakan.
                        </p>

                        @if ($canCreate)
                            <button
                                type="button"
                                wire:click="openCreateModal"
                                class="mt-5 rounded-2xl bg-sky-600 px-5 py-3 text-sm font-bold text-white"
                            >
                                Tambah Perizinan
                            </button>
                        @endif
                    </div>
                @endforelse
            </div>

            @if ($permissions->hasPages())
                <div class="mt-6 rounded-2xl bg-white p-4 shadow-sm">
                    {{ $permissions->links() }}
                </div>
            @endif
        </section>
    </div>

    <x-smooth-modal
        name="student-permission-form"
        :title="$editingId ? 'Edit Perizinan Siswa' : 'Tambah Perizinan Siswa'"
        max-width="2xl"
    >
        <form wire:submit="save" class="space-y-5">
            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Siswa
                </label>

                <select
                    wire:model="studentId"
                    class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
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
                    Jenis perizinan
                </label>

                <select
                    wire:model.live="permissionType"
                    class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
                >
                    @foreach ($permissionTypes as $value => $label)
                        <option value="{{ $value }}">
                            {{ $label }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Tanggal izin
                </label>

                <input
                    type="date"
                    wire:model="permissionDate"
                    class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
                >

                @error('permissionDate')
                    <p class="mt-2 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            @if (
                in_array(
                    $permissionType,
                    ['out', 'early_leave'],
                    true
                )
            )
                <div class="grid gap-4 sm:grid-cols-2">
                    <div>
                        <label class="mb-2 block text-sm font-bold text-slate-700">
                            {{ $permissionType === 'out' ? 'Jam keluar' : 'Jam pulang' }}
                        </label>

                        <input
                            type="time"
                            wire:model="startTime"
                            class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
                        >

                        @error('startTime')
                            <p class="mt-2 text-xs text-red-600">
                                {{ $message }}
                            </p>
                        @enderror
                    </div>

                    @if ($permissionType === 'out')
                        <div>
                            <label class="mb-2 block text-sm font-bold text-slate-700">
                                Perkiraan jam kembali
                            </label>

                            <input
                                type="time"
                                wire:model="expectedReturnTime"
                                class="h-14 w-full rounded-2xl border border-slate-200 bg-slate-50 px-4 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
                            >

                            @error('expectedReturnTime')
                                <p class="mt-2 text-xs text-red-600">
                                    {{ $message }}
                                </p>
                            @enderror
                        </div>
                    @endif
                </div>
            @endif

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Alasan izin
                </label>

                <textarea
                    wire:model="reason"
                    rows="4"
                    placeholder="Tuliskan alasan izin secara jelas..."
                    class="w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-sky-500 focus:ring-4 focus:ring-sky-100"
                ></textarea>

                @error('reason')
                    <p class="mt-2 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Bukti surat atau foto
                </label>

                <label class="flex cursor-pointer items-center justify-center rounded-2xl border border-dashed border-sky-300 bg-sky-50 px-4 py-4 text-xs font-bold text-sky-600">
                    Pilih File Bukti

                    <input
                        type="file"
                        wire:model="evidence"
                        accept=".pdf,.jpg,.jpeg,.png,.webp"
                        class="hidden"
                    >
                </label>

                <div wire:loading wire:target="evidence">
                    <p class="mt-2 text-xs font-semibold text-sky-600">
                        Mengunggah file...
                    </p>
                </div>

                @if ($evidence)
                    <div class="mt-3 rounded-2xl bg-emerald-50 p-3">
                        <p class="truncate text-xs font-bold text-emerald-700">
                            {{ $evidence->getClientOriginalName() }}
                        </p>

                        <button
                            type="button"
                            wire:click="cancelNewEvidence"
                            class="mt-2 text-xs font-bold text-red-600"
                        >
                            Batalkan file baru
                        </button>
                    </div>
                @elseif ($existingEvidence)
                    <div class="mt-3 rounded-2xl bg-indigo-50 p-3">
                        <p class="text-xs font-bold text-indigo-700">
                            Bukti lama masih tersimpan
                        </p>

                        <button
                            type="button"
                            wire:click="removeCurrentEvidence"
                            class="mt-2 text-xs font-bold text-red-600"
                        >
                            Hapus bukti
                        </button>
                    </div>
                @endif

                <p class="mt-2 text-[10px] text-slate-500">
                    PDF, JPG, PNG, atau WEBP. Maksimal 5 MB.
                </p>

                @error('evidence')
                    <p class="mt-2 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-3 border-t border-slate-100 pt-5">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal', { modal: 'student-permission-form' })"
                    class="h-14 rounded-2xl border border-slate-200 bg-white text-sm font-bold text-slate-600"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="save,evidence"
                    class="flex h-14 items-center justify-center rounded-2xl bg-gradient-to-r from-sky-600 to-blue-600 text-sm font-bold text-white shadow-lg shadow-sky-200 disabled:opacity-60"
                >
                    <span wire:loading.remove wire:target="save">
                        {{ $editingId ? 'Simpan Perubahan' : 'Ajukan Perizinan' }}
                    </span>

                    <span wire:loading wire:target="save">
                        Menyimpan...
                    </span>
                </button>
            </div>
        </form>
    </x-smooth-modal>

    <x-smooth-modal
        name="reject-permission-form"
        title="Tolak Perizinan"
        max-width="md"
    >
        <form wire:submit="submitRejection" class="space-y-5">
            <div class="rounded-2xl border border-red-100 bg-red-50 p-4">
                <p class="text-sm font-bold text-red-700">
                    Perizinan akan ditolak
                </p>

                <p class="mt-1 text-xs leading-5 text-red-600">
                    Tuliskan alasan penolakan agar dapat diketahui oleh
                    pencatat pengajuan.
                </p>
            </div>

            <div>
                <label class="mb-2 block text-sm font-bold text-slate-700">
                    Alasan penolakan
                </label>

                <textarea
                    wire:model="rejectionNote"
                    rows="4"
                    placeholder="Masukkan alasan penolakan..."
                    class="w-full resize-none rounded-2xl border border-slate-200 bg-slate-50 px-4 py-3 text-sm outline-none focus:border-red-500 focus:ring-4 focus:ring-red-100"
                ></textarea>

                @error('rejectionNote')
                    <p class="mt-2 text-xs text-red-600">
                        {{ $message }}
                    </p>
                @enderror
            </div>

            <div class="grid grid-cols-2 gap-3">
                <button
                    type="button"
                    x-on:click="$dispatch('close-modal', { modal: 'reject-permission-form' })"
                    class="h-14 rounded-2xl border border-slate-200 bg-white text-sm font-bold text-slate-600"
                >
                    Batal
                </button>

                <button
                    type="submit"
                    wire:loading.attr="disabled"
                    wire:target="submitRejection"
                    class="h-14 rounded-2xl bg-red-600 text-sm font-bold text-white disabled:opacity-60"
                >
                    Tolak Perizinan
                </button>
            </div>
        </form>
    </x-smooth-modal>
</div>
