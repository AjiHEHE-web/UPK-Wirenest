const cartNotice = document.querySelector('[data-cart-notice]');
const cartNoticeMessage = cartNotice?.querySelector('[data-cart-notice-message]');
let cartNoticeTimer;

const showCartNotice = (message, isError = false) => {
    if (!(cartNotice instanceof HTMLElement) || !(cartNoticeMessage instanceof HTMLElement)) {
        return;
    }

    window.clearTimeout(cartNoticeTimer);
    cartNoticeMessage.textContent = message;
    cartNotice.classList.toggle('border-rose-200', isError);
    cartNotice.classList.toggle('text-rose-800', isError);
    cartNotice.classList.toggle('border-botanical-200', !isError);
    cartNotice.classList.toggle('text-botanical-900', !isError);
    cartNotice.hidden = false;
    cartNoticeTimer = window.setTimeout(() => {
        cartNotice.hidden = true;
    }, 3600);
};

document.querySelectorAll('form[data-ajax-cart]').forEach((form) => {
    form.addEventListener('submit', async (event) => {
        event.preventDefault();

        const submitButton = form.querySelector('button[type="submit"]');

        if (!(submitButton instanceof HTMLButtonElement) || submitButton.disabled) {
            return;
        }

        submitButton.disabled = true;
        submitButton.setAttribute('aria-busy', 'true');

        try {
            const response = await fetch(form.action, {
                method: form.method || 'POST',
                body: new FormData(form),
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            const result = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(result.message || 'Produk gagal ditambahkan ke keranjang.');
            }

            showCartNotice(result.message || 'Produk ditambahkan ke keranjang.');
            document.dispatchEvent(new CustomEvent('cart:updated', { detail: result }));
        } catch (error) {
            showCartNotice(error.message || 'Tidak dapat terhubung. Periksa koneksi lalu coba lagi.', true);
        } finally {
            submitButton.disabled = false;
            submitButton.removeAttribute('aria-busy');
        }
    });
});
