const expirationPanel = document.querySelector('[data-order-expiration-poll]');
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

if (expirationPanel instanceof HTMLElement && csrfToken) {
    let isChecking = false;
    let timeoutId;

    const updateExpiredOrder = (state) => {
        const orderStatus = expirationPanel.querySelector('[data-order-status-pill]');
        const paymentStatus = expirationPanel.querySelector('[data-payment-status-pill]');
        const notice = expirationPanel.querySelector('[data-order-expired-notice]');

        if (orderStatus) {
            orderStatus.textContent = 'Dibatalkan';
            orderStatus.classList.remove('bg-botanical-50', 'text-botanical-800');
            orderStatus.classList.add('bg-rose-50', 'text-rose-800');
        }

        if (paymentStatus && state.payment_status === 'failed') {
            paymentStatus.textContent = 'failed';
            paymentStatus.classList.remove('bg-amber-50', 'text-amber-800');
            paymentStatus.classList.add('bg-rose-50', 'text-rose-800');
        }

        expirationPanel.querySelector('[data-order-cancel-form]')?.remove();
        expirationPanel.querySelector('[data-payment-proof-form]')?.remove();

        if (notice) {
            notice.textContent = state.expired
                ? 'Batas waktu pembayaran telah habis. Pesanan dibatalkan otomatis dan stok sudah dikembalikan.'
                : 'Pesanan ini sudah dibatalkan.';
            notice.hidden = false;
        }

        expirationPanel.removeAttribute('data-order-expiration-poll');
    };

    const checkPaymentDeadline = async () => {
        if (isChecking || ! expirationPanel.isConnected) {
            return;
        }

        isChecking = true;

        try {
            const response = await fetch(expirationPanel.dataset.checkUrl, {
                method: 'POST',
                credentials: 'same-origin',
                headers: {
                    Accept: 'application/json',
                    'X-CSRF-TOKEN': csrfToken,
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });

            if (response.ok) {
                const state = await response.json();

                if (state.cancelled) {
                    updateExpiredOrder(state);

                    return;
                }
            }
        } catch {
            // Keep retrying while the order is still open in the browser.
        } finally {
            isChecking = false;
        }

        if (expirationPanel.isConnected) {
            timeoutId = window.setTimeout(checkPaymentDeadline, 5000);
        }
    };

    window.addEventListener('pagehide', () => window.clearTimeout(timeoutId), { once: true });
    void checkPaymentDeadline();
}
