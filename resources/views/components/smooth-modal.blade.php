@props([
    'name',
    'title' => null,
    'maxWidth' => 'md',
])

@php
    $maxWidthClass = match ($maxWidth) {
        'sm' => 'max-w-sm',
        'md' => 'max-w-md',
        'lg' => 'max-w-lg',
        'xl' => 'max-w-xl',
        '2xl' => 'max-w-2xl',
        default => 'max-w-md',
    };
@endphp

<div
    x-data="{
        show: false,

        matchesModal(detail) {
            if (typeof detail === 'string') {
                return detail === '{{ $name }}';
            }

            return detail?.modal === '{{ $name }}'
                || detail?.name === '{{ $name }}';
        },

        openModal() {
            this.show = true;
            document.body.classList.add('overflow-hidden');
        },

        closeModal() {
            this.show = false;
            document.body.classList.remove('overflow-hidden');
        }
    }"
    x-on:open-modal.window="
        if (matchesModal($event.detail)) {
            openModal();
        }
    "
    x-on:close-modal.window="
        if (matchesModal($event.detail)) {
            closeModal();
        }
    "
    x-on:keydown.escape.window="
        if (show) {
            closeModal();
        }
    "
    x-show="show"
    x-cloak
    class="fixed inset-0 z-50 overflow-y-auto"
    role="dialog"
    aria-modal="true"
>
    <div
        x-show="show"
        x-transition:enter="ease-out duration-300"
        x-transition:enter-start="opacity-0"
        x-transition:enter-end="opacity-100"
        x-transition:leave="ease-in duration-200"
        x-transition:leave-start="opacity-100"
        x-transition:leave-end="opacity-0"
        class="fixed inset-0 bg-slate-950/60 backdrop-blur-sm"
        x-on:click="closeModal()"
    ></div>

    <div class="flex min-h-full items-end justify-center sm:items-center sm:p-5">
        <div
            x-show="show"
            x-transition:enter="transform ease-out duration-300"
            x-transition:enter-start="translate-y-full opacity-0 sm:translate-y-8 sm:scale-95"
            x-transition:enter-end="translate-y-0 opacity-100 sm:scale-100"
            x-transition:leave="transform ease-in duration-200"
            x-transition:leave-start="translate-y-0 opacity-100 sm:scale-100"
            x-transition:leave-end="translate-y-full opacity-0 sm:translate-y-8 sm:scale-95"
            class="relative w-full {{ $maxWidthClass }} overflow-hidden rounded-t-[28px] bg-white shadow-2xl sm:rounded-[28px]"
            x-on:click.stop
        >
            <div class="flex justify-center pb-1 pt-3 sm:hidden">
                <span class="h-1.5 w-12 rounded-full bg-slate-200"></span>
            </div>

            @if ($title)
                <div class="flex items-center justify-between border-b border-slate-100 px-5 py-4">
                    <div>
                        <h2 class="text-base font-extrabold text-slate-900">
                            {{ $title }}
                        </h2>

                        <p class="mt-1 text-xs text-slate-500">
                            Lengkapi data yang diperlukan
                        </p>
                    </div>

                    <button
                        type="button"
                        x-on:click="closeModal()"
                        class="flex h-10 w-10 items-center justify-center rounded-2xl bg-slate-100 text-xl text-slate-500 transition hover:bg-slate-200"
                    >
                        &times;
                    </button>
                </div>
            @endif

            <div class="max-h-[82vh] overflow-y-auto p-5 smooth-scrollbar">
                {{ $slot }}
            </div>
        </div>
    </div>
</div>
