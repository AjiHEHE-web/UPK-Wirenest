const expirationPanels = document.querySelectorAll('[data-order-expiration-poll]');
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

if (csrfToken) {
    expirationPanels.forEach((expirationPanel) => {
        if (!(expirationPanel instanceof HTMLElement)) {
            return;
        }

    let isChecking = false;
    let timeoutId;
    let countdownInterval;
    const deadlineTimestamp = Number(expirationPanel.dataset.deadlineTimestamp);
    const countdownDisplay = expirationPanel.querySelector('[data-order-deadline-countdown]');

    const updateCountdown = () => {
        if (!countdownDisplay || ! Number.isFinite(deadlineTimestamp)) {
            return;
        }

        const remainingSeconds = Math.max(0, Math.ceil((deadlineTimestamp - Date.now()) / 1000));
        const hours = String(Math.floor(remainingSeconds / 3600)).padStart(2, '0');
        const minutes = String(Math.floor((remainingSeconds % 3600) / 60)).padStart(2, '0');
        const seconds = String(remainingSeconds % 60).padStart(2, '0');

        countdownDisplay.textContent = `${hours}:${minutes}:${seconds}`;
    };

    const updateExpiredOrder = (state) => {
        const orderStatus = expirationPanel.querySelector('[data-order-status-pill]');
        const paymentStatus = expirationPanel.querySelector('[data-payment-status-pill]');
        const notice = expirationPanel.querySelector('[data-order-expired-notice]');

        if (orderStatus) {
            orderStatus.textContent = 'Dibatalkan';
            orderStatus.classList.remove(
                'bg-botanical-50',
                'text-botanical-800',
                'bg-[#fff3c9]',
                'text-[#785e00]',
                'bg-[#e6f3e2]',
                'bg-[#f9e5ec]',
            );
            orderStatus.classList.add('bg-rose-50', 'text-rose-800');
        }

        if (paymentStatus && state.payment_status === 'failed') {
            paymentStatus.textContent = 'Failed';
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

        window.clearInterval(countdownInterval);
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

    updateCountdown();
    countdownInterval = window.setInterval(updateCountdown, 1000);
    window.addEventListener('pagehide', () => {
        window.clearTimeout(timeoutId);
        window.clearInterval(countdownInterval);
    }, { once: true });
    void checkPaymentDeadline();
    });
}
