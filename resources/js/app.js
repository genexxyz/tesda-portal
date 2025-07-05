import './bootstrap';
import Swal from 'sweetalert2'
window.Swal = Swal;

window.addEventListener('swal:alert', (event) => {
    let data = event.detail;

    Swal.fire({
        position: 'top-end',
        toast: true,
        icon: data.type,
        text: data.text,
        timerProgressBar: true,
        timer: 3000,
        showConfirmButton: false,
        showCloseButton: true,
    })
});

window.addEventListener('swal:confirm', (event) => {
    let data = event.detail;

    Swal.fire({
        title: data.title || 'Are you sure?',
        text: data.text || 'You won\'t be able to revert this!',
        icon: data.icon || 'warning',
        showCancelButton: true,
        confirmButtonText: data.confirmButtonText || 'Yes, delete it!',
        cancelButtonText: data.cancelButtonText || 'No, cancel!',
        reverseButtons: data.reverseButtons || false,
    }).then((result) => {
        if (result.isConfirmed) {
            Livewire.emit('confirmed');
        }
    });
});

window.addEventListener('swal:success', (event) => {
    let data = event.detail;

    Swal.fire({
        icon: 'success',
        title: data.title || 'Success!',
        text: data.text || 'Operation completed successfully!',
        confirmButtonText: data.confirmButtonText || 'OK!',
        timer: data.timer || 3000,
        timerProgressBar: true,
        showCancelButton: false,
    });
});