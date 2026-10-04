@php
    $alerts = [];
    if (session('success')) {
        $alerts[] = ['icon' => 'success', 'title' => 'Berhasil', 'text' => session('success')];
    }
    if (session('error')) {
        $alerts[] = ['icon' => 'error', 'title' => 'Gagal', 'text' => session('error')];
    }
    if ($errors->any()) {
        $alerts[] = ['icon' => 'warning', 'title' => 'Form belum lengkap', 'text' => implode("\n", $errors->all())];
    }
@endphp
<script>
    window.appAlerts = {{ Illuminate\Support\Js::from($alerts) }};
</script>
