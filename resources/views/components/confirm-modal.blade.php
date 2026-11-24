<div
    id="voltpay-confirm-modal"
    class="fixed inset-0 z-50 hidden items-center justify-center p-4 bg-zinc-950/50 backdrop-blur-xs transition-opacity duration-200"
    aria-modal="true"
    role="dialog"
>
    <div
        id="voltpay-confirm-card"
        class="w-full max-w-md bg-white border border-zinc-200 rounded-2xl p-6 shadow-xl transform scale-95 opacity-0 transition-all duration-200"
    >
        <div class="flex items-start gap-4">
            <div
                id="voltpay-confirm-icon-wrap"
                class="shrink-0 w-10 h-10 rounded-xl bg-red-50 text-red-600 flex items-center justify-center"
            >
                <svg class="w-5 h-5" fill="none" stroke="currentColor" stroke-width="1.75" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v3.75m-9.303 3.376c-.866 1.5.217 3.374 1.948 3.374h14.71c1.73 0 2.813-1.874 1.948-3.374L13.949 3.378c-.866-1.5-3.032-1.5-3.898 0L2.697 16.126ZM12 15.75h.007v.008H12v-.008Z" />
                </svg>
            </div>
            <div class="flex-1 min-w-0">
                <h3 id="voltpay-confirm-title" class="text-base font-bold text-zinc-900">Konfirmasi Tindakan</h3>
                <p id="voltpay-confirm-message" class="text-sm text-zinc-600 mt-1.5 leading-relaxed">Apakah Anda yakin ingin melanjutkan tindakan ini?</p>

                {{-- Input Alasan (aktif saat form memakai data-confirm-prompt) --}}
                <div id="voltpay-confirm-prompt-wrap" class="hidden mt-3">
                    <textarea
                        id="voltpay-confirm-input"
                        rows="2"
                        class="w-full rounded-xl border border-zinc-300 px-3 py-2 text-sm text-zinc-900 focus:border-zinc-900 focus:outline-none"
                        placeholder="Tuliskan alasan..."
                    ></textarea>
                    <p id="voltpay-confirm-error" class="hidden text-xs text-red-600 mt-1">Alasan wajib diisi sebelum melanjutkan.</p>
                </div>
            </div>
        </div>

        <div class="mt-6 flex items-center justify-end gap-2.5">
            <button
                type="button"
                id="voltpay-confirm-cancel"
                class="px-4 py-2 text-sm font-medium text-zinc-700 bg-zinc-100 hover:bg-zinc-200 rounded-xl transition cursor-pointer"
            >
                Batal
            </button>
            <button
                type="button"
                id="voltpay-confirm-ok"
                class="px-4 py-2 text-sm font-medium text-white bg-red-600 hover:bg-red-700 rounded-xl transition cursor-pointer"
            >
                Ya, Lanjutkan
            </button>
        </div>
    </div>
</div>
