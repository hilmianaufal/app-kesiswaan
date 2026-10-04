import './bootstrap';
import Swal from 'sweetalert2';

// Livewire initializes Alpine for every layout.
window.Swal = Swal;

const showAlert = (detail, icon = 'success') => {
    const data = Array.isArray(detail) ? detail[0] : (detail ?? {});
    return Swal.fire({
        icon: data.icon ?? icon,
        title: data.title ?? 'Berhasil',
        text: data.text ?? '',
        confirmButtonText: 'Oke',
        confirmButtonColor: icon === 'error' ? '#dc2626' : '#4f46e5',
    });
};

for (const [event, icon] of Object.entries({
    swal: 'success',
    'swal:success': 'success',
    'swal:error': 'error',
})) {
    window.addEventListener(event, (e) => showAlert(e.detail, icon));
}

const showSessionAlerts = async () => {
    for (const alert of window.appAlerts ?? []) {
        await showAlert(alert, alert.icon);
    }
};

if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', showSessionAlerts, { once: true });
} else {
    showSessionAlerts();
}
