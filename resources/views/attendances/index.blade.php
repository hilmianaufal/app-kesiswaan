@extends('layouts.mobile')

@section('title', 'Absensi Siswa')

@section('content')
    <div class="mx-auto max-w-7xl px-4 py-5 sm:px-6 sm:py-8">
        <livewire:attendances.index />
    </div>
@endsection
