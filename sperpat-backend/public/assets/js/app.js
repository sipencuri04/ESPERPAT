// ESPERPAT App JS

document.addEventListener('DOMContentLoaded', function () {
    // Format currency
    window.formatRupiah = function (number) {
        return 'Rp ' + number.toString().replace(/\B(?=(\d{3})+(?!\d))/g, '.');
    };

    // Confirm delete
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            if (!confirm(this.dataset.confirm || 'Yakin ingin menghapus?')) {
                e.preventDefault();
            }
        });
    });

    // Quantity controls
    document.querySelectorAll('.qty-control').forEach(function (control) {
        const input = control.querySelector('input');
        const btnMinus = control.querySelector('.qty-minus');
        const btnPlus = control.querySelector('.qty-plus');

        if (btnMinus) btnMinus.addEventListener('click', () => {
            let val = parseInt(input.value) || 1;
            if (val > 1) input.value = val - 1;
            input.dispatchEvent(new Event('change'));
        });

        if (btnPlus) btnPlus.addEventListener('click', () => {
            let val = parseInt(input.value) || 1;
            let max = parseInt(input.max) || 999;
            if (val < max) input.value = val + 1;
            input.dispatchEvent(new Event('change'));
        });
    });

    // Image preview
    document.querySelectorAll('.image-input').forEach(function (input) {
        input.addEventListener('change', function () {
            const preview = document.querySelector(this.dataset.preview);
            if (preview && this.files[0]) {
                const reader = new FileReader();
                reader.onload = (e) => preview.src = e.target.result;
                reader.readAsDataURL(this.files[0]);
            }
        });
    });

    // Loading buttons
    document.querySelectorAll('form').forEach(function (form) {
        form.addEventListener('submit', function () {
            const btn = form.querySelector('[type="submit"]');
            if (btn) {
                btn.disabled = true;
                btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span> Memproses...';
            }
        });
    });

    // Smooth scroll
    document.querySelectorAll('a[href^="#"]').forEach(function (anchor) {
        anchor.addEventListener('click', function (e) {
            e.preventDefault();
            const target = document.querySelector(this.getAttribute('href'));
            if (target) target.scrollIntoView({ behavior: 'smooth' });
        });
    });
});
