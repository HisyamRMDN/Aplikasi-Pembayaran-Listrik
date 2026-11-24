export function initConfirmModal() {
    const modal = document.getElementById('voltpay-confirm-modal');
    if (!modal) return;

    const card = document.getElementById('voltpay-confirm-card');
    const titleEl = document.getElementById('voltpay-confirm-title');
    const messageEl = document.getElementById('voltpay-confirm-message');
    const okBtn = document.getElementById('voltpay-confirm-ok');
    const cancelBtn = document.getElementById('voltpay-confirm-cancel');
    const iconWrap = document.getElementById('voltpay-confirm-icon-wrap');

    const promptWrap = document.getElementById('voltpay-confirm-prompt-wrap');
    const promptInput = document.getElementById('voltpay-confirm-input');
    const promptError = document.getElementById('voltpay-confirm-error');

    let targetForm = null;
    let promptTargetName = null;

    function openConfirmModal({ title, message, confirmText, variant, promptField, promptPlaceholder, form }) {
        targetForm = form;
        promptTargetName = promptField || null;

        titleEl.textContent = title || 'Konfirmasi Tindakan';
        messageEl.textContent = message || 'Apakah Anda yakin ingin melanjutkan?';
        okBtn.textContent = confirmText || 'Ya, Lanjutkan';

        promptError.classList.add('hidden');
        promptInput.value = '';

        if (promptTargetName) {
            promptWrap.classList.remove('hidden');
            promptInput.placeholder = promptPlaceholder || 'Tuliskan alasan...';
        } else {
            promptWrap.classList.add('hidden');
        }

        if (variant === 'dark') {
            iconWrap.className =
                'shrink-0 w-10 h-10 rounded-xl bg-zinc-100 text-zinc-800 flex items-center justify-center';
            okBtn.className =
                'px-4 py-2 text-sm font-medium text-white bg-zinc-900 hover:bg-zinc-800 rounded-xl transition cursor-pointer';
        } else {
            iconWrap.className =
                'shrink-0 w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center';
            okBtn.className =
                'px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-xl transition cursor-pointer';
        }

        modal.classList.remove('hidden');
        modal.classList.add('flex');
        requestAnimationFrame(() => {
            card.classList.remove('scale-95', 'opacity-0');
            card.classList.add('scale-100', 'opacity-100');
            if (promptTargetName) promptInput.focus();
        });
    }

    function closeConfirmModal() {
        card.classList.remove('scale-100', 'opacity-100');
        card.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            targetForm = null;
            promptTargetName = null;
        }, 150);
    }

    document.addEventListener('submit', (e) => {
        const form = e.target;
        if (form.matches('form[data-confirm]')) {
            e.preventDefault();
            openConfirmModal({
                title: form.dataset.confirmTitle || 'Konfirmasi Hapus Data',
                message: form.dataset.confirm,
                confirmText: form.dataset.confirmText || 'Ya, Hapus',
                variant: form.dataset.confirmVariant || 'danger',
                promptField: form.dataset.confirmPrompt || null,
                promptPlaceholder: form.dataset.confirmPlaceholder || null,
                form,
            });
        }
    });

    okBtn.addEventListener('click', () => {
        if (!targetForm) return;

        if (promptTargetName) {
            const val = promptInput.value.trim();
            if (!val) {
                promptError.classList.remove('hidden');
                promptInput.focus();
                return;
            }
            const hiddenInput = targetForm.querySelector(`[name="${promptTargetName}"]`);
            if (hiddenInput) hiddenInput.value = val;
        }

        const formToSubmit = targetForm;
        formToSubmit.removeAttribute('data-confirm');
        closeConfirmModal();
        formToSubmit.submit();
    });

    cancelBtn.addEventListener('click', closeConfirmModal);
    modal.addEventListener('click', (e) => {
        if (e.target === modal) closeConfirmModal();
    });
    document.addEventListener('keydown', (e) => {
        if (e.key === 'Escape' && !modal.classList.contains('hidden')) {
            closeConfirmModal();
        }
    });
}
