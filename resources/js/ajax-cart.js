const cartNotice = document.querySelector('[data-cart-notice]');
const cartNoticeMessage = cartNotice?.querySelector('[data-cart-notice-message]');
let cartNoticeTimer;

document.addEventListener('cart:updated', (event) => {
    const cartLink = document.querySelector('[data-cart-link]');
    const cartCountBadge = document.querySelector('[data-cart-count]');
    const cartStatus = document.querySelector('[data-cart-status]');
    const cartCount = Number(event.detail?.cartCount ?? 0);

    if (!(cartLink instanceof HTMLAnchorElement) || !(cartCountBadge instanceof HTMLElement)) {
        return;
    }

    cartCountBadge.textContent = String(cartCount);
    cartCountBadge.hidden = cartCount < 1;
    cartCountBadge.classList.toggle('hidden', cartCount < 1);
    cartLink.setAttribute(
        'aria-label',
        cartCount > 0 ? `Keranjang belanja, ${cartCount} item` : 'Keranjang belanja',
    );

    if (cartStatus instanceof HTMLElement) {
        cartStatus.textContent = `${cartCount} item di keranjang.`;
    }
});

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

document.querySelectorAll('form[data-ajax-cart-update]').forEach((form) => {
    const quantityInput = form.querySelector('[data-cart-quantity]');
    const submitButton = form.querySelector('button[type="submit"]');
    let savedQuantity = quantityInput?.value ?? '';
    let isUpdating = false;

    if (!(quantityInput instanceof HTMLInputElement)) {
        return;
    }

    const updateQuantity = async () => {
        if (isUpdating || quantityInput.value === savedQuantity) {
            return;
        }

        if (!form.reportValidity()) {
            quantityInput.value = savedQuantity;

            return;
        }

        isUpdating = true;
        const formData = new FormData(form);

        if (submitButton instanceof HTMLButtonElement) {
            submitButton.disabled = true;
            submitButton.setAttribute('aria-busy', 'true');
        }

        quantityInput.disabled = true;

        try {
            const response = await fetch(form.action, {
                method: form.method || 'POST',
                body: formData,
                headers: {
                    Accept: 'application/json',
                    'X-Requested-With': 'XMLHttpRequest',
                },
            });
            const result = await response.json().catch(() => ({}));

            if (!response.ok) {
                throw new Error(result.errors?.quantity?.[0] || result.message || 'Jumlah produk gagal diperbarui.');
            }

            savedQuantity = String(result.quantity);
            quantityInput.value = savedQuantity;

            const cartItem = form.closest('[data-cart-item]');
            const itemSubtotal = cartItem?.querySelector('[data-cart-line-subtotal]');
            const formatter = new Intl.NumberFormat('id-ID', { maximumFractionDigits: 0 });

            if (itemSubtotal instanceof HTMLElement) {
                itemSubtotal.textContent = `Rp ${formatter.format(Number(result.itemSubtotal))}`;
            }

            document.querySelectorAll('[data-cart-total]').forEach((totalElement) => {
                totalElement.textContent = `Rp ${formatter.format(Number(result.cartTotal))}`;
            });

            const cartQuantityTotal = document.querySelector('[data-cart-quantity-total]');

            if (cartQuantityTotal instanceof HTMLElement) {
                cartQuantityTotal.textContent = `${result.cartCount} buket`;
            }

            document.dispatchEvent(new CustomEvent('cart:updated', { detail: result }));
            showCartNotice(result.message || 'Jumlah produk diperbarui.');
        } catch (error) {
            quantityInput.value = savedQuantity;
            showCartNotice(error.message || 'Tidak dapat terhubung. Periksa koneksi lalu coba lagi.', true);
        } finally {
            quantityInput.disabled = false;
            isUpdating = false;

            if (submitButton instanceof HTMLButtonElement) {
                submitButton.disabled = false;
                submitButton.removeAttribute('aria-busy');
            }
        }
    };

    quantityInput.addEventListener('change', updateQuantity);
    form.addEventListener('submit', (event) => {
        event.preventDefault();
        updateQuantity();
    });
});
