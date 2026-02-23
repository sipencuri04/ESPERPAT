import { ref, reactive } from 'vue';

const state = reactive({
    show: false,
    type: 'success', // success, error, warning, confirm
    title: '',
    message: '',
    confirmText: 'OK',
    cancelText: 'Batal',
    onConfirm: null,
    onCancel: null,
});

const show = (opts) => {
    state.show = true;
    state.type = opts.type || 'success';
    state.title = opts.title || '';
    state.message = opts.message || '';
    state.confirmText = opts.confirmText || 'OK';
    state.cancelText = opts.cancelText || 'Batal';
    state.onConfirm = opts.onConfirm || null;
    state.onCancel = opts.onCancel || null;
};

const close = () => {
    state.show = false;
};

const confirm = (cb) => {
    if (state.onConfirm) state.onConfirm();
    if (cb) cb();
    close();
};

const cancel = () => {
    if (state.onCancel) state.onCancel();
    close();
};

export function useToast() {
    return {
        state,
        success: (message, title) => show({ type: 'success', message, title: title || 'Berhasil! 🎉' }),
        error: (message, title) => show({ type: 'error', message, title: title || 'Gagal!' }),
        warning: (message, title) => show({ type: 'warning', message, title: title || 'Perhatian!' }),
        ask: (message, onYes, title) => show({
            type: 'confirm',
            message,
            title: title || 'Konfirmasi',
            confirmText: 'Ya, Lanjutkan',
            cancelText: 'Batal',
            onConfirm: onYes,
        }),
        close,
        confirm,
        cancel,
    };
}
