const searchForms = document.querySelectorAll('form[data-ajax-search]');

searchForms.forEach((form) => {
    const results = document.getElementById(form.dataset.ajaxResults || '');

    if (!results) {
        return;
    }

    const status = document.createElement('span');
    status.className = 'sr-only';
    status.setAttribute('role', 'status');
    status.setAttribute('aria-live', 'polite');
    form.append(status);

    let debounceTimer;
    let activeRequest;

    const setFormValuesFromUrl = (url) => {
        const params = new URL(url).searchParams;

        for (const control of form.elements) {
            if (!(control instanceof HTMLInputElement || control instanceof HTMLSelectElement) || !control.name) {
                continue;
            }

            if (control.type === 'submit' || control.type === 'button' || control.type === 'hidden') {
                continue;
            }

            control.value = params.get(control.name) || '';
        }
    };

    const refreshRelatedElements = (nextDocument) => {
        document.querySelectorAll('[data-ajax-refresh][id]').forEach((currentElement) => {
            const nextElement = nextDocument.getElementById(currentElement.id);

            if (nextElement) {
                currentElement.innerHTML = nextElement.innerHTML;
            }
        });
    };

    const updateResults = async (url, historyMode = 'replace') => {
        activeRequest?.abort();
        const requestController = new AbortController();
        activeRequest = requestController;
        results.setAttribute('aria-busy', 'true');
        status.textContent = 'Sedang mencari…';

        try {
            const response = await fetch(url, {
                headers: {
                    Accept: 'text/html',
                    'X-Requested-With': 'XMLHttpRequest',
                },
                signal: requestController.signal,
            });
            const responseHtml = await response.text();

            if (!response.ok) {
                throw new Error(`Pencarian gagal (${response.status}).`);
            }

            const nextDocument = new DOMParser().parseFromString(responseHtml, 'text/html');
            const nextResults = nextDocument.getElementById(results.id);

            if (!nextResults) {
                if (response.redirected) {
                    window.location.assign(response.url);

                    return;
                }

                throw new Error('Bagian hasil pencarian tidak ditemukan.');
            }

            results.innerHTML = nextResults.innerHTML;
            results.removeAttribute('aria-busy');
            refreshRelatedElements(nextDocument);
            setFormValuesFromUrl(url);

            if (historyMode === 'push') {
                window.history.pushState({}, '', url);
            } else if (historyMode === 'replace') {
                window.history.replaceState({}, '', url);
            }

            const resultCount = nextResults.querySelector('[data-ajax-result-count]')?.textContent?.trim();
            status.textContent = resultCount || 'Hasil pencarian diperbarui.';
            results.dispatchEvent(new CustomEvent('ajax:content-updated', {
                bubbles: true,
                detail: { root: results },
            }));
        } catch (error) {
            if (error.name !== 'AbortError') {
                status.textContent = 'Pencarian gagal. Silakan coba lagi.';
                console.error('Pencarian AJAX gagal.', error);
            }
        } finally {
            if (activeRequest === requestController) {
                results.removeAttribute('aria-busy');
            }
        }
    };

    const submitSearch = () => {
        const url = new URL(form.action, window.location.origin);
        const params = new URLSearchParams(new FormData(form));

        for (const [key, value] of params.entries()) {
            if (value.trim() === '') {
                params.delete(key);
            }
        }

        url.search = params.toString();
        updateResults(url.toString());
    };

    form.addEventListener('submit', (event) => {
        event.preventDefault();
        window.clearTimeout(debounceTimer);
        submitSearch();
    });

    form.addEventListener('input', (event) => {
        if (event.target instanceof HTMLInputElement && event.target.type === 'search') {
            window.clearTimeout(debounceTimer);
            debounceTimer = window.setTimeout(submitSearch, 350);
        }
    });

    form.addEventListener('change', (event) => {
        if (event.target instanceof HTMLSelectElement || (event.target instanceof HTMLInputElement && event.target.type === 'date')) {
            window.clearTimeout(debounceTimer);
            submitSearch();
        }
    });

    results.addEventListener('click', (event) => {
        const link = event.target.closest('a[href]');

        if (!link || !results.contains(link)) {
            return;
        }

        const url = new URL(link.href, window.location.origin);

        if (
            url.origin === window.location.origin
            && url.pathname === new URL(form.action).pathname
            && (url.searchParams.has('page') || link.hasAttribute('data-ajax-search-link'))
        ) {
            event.preventDefault();
            updateResults(url.toString(), 'push');
        }
    });

    window.addEventListener('popstate', () => {
        if (window.location.pathname === new URL(form.action).pathname) {
            updateResults(window.location.href, 'none');
        }
    });
});
