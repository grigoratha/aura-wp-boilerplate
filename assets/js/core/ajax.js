(function () {
    'use strict';

    async function themeAjaxRequest(subAction, data = {}) {

        if (!window.themeConfig || !themeConfig.ajaxUrl) {
            console.error('[AJAX Dispatcher] Missing Configuration');
            return Promise.reject('Missing Configuration');
        }

        const body = new URLSearchParams();

        body.append('action', 'theme_ajax_dispatcher');
        body.append('sub_action', subAction);
        body.append('nonce', themeConfig.nonce);

        Object.entries(data).forEach(([key, value]) => {
            body.append(key, value);
        });

        try {
            const res = await fetch(themeConfig.ajaxUrl, {
                method: "POST",
                credentials: "same-origin",
                headers: { "Content-Type": "application/x-www-form-urlencoded; charset=UTF-8" },
                body: body.toString()
            });

            if (!res.ok) {
                const text = await res.text();
                console.error("[AJAX Dispatcher] HTTP Error Response:", text);
                throw new Error(`HTTP Error ${res.status}`);
            }

            let json;
            try {
                json = await res.json();
            } 
            catch (e) {
                const raw = await res.text();
                console.error("[AJAX Dispatcher] Invalid JSON Response:", raw);
                throw e;
            }

            if (json.success === false) {
                console.error("[AJAX Dispatcher] WP Error Response:", json);
                return Promise.reject(json);
            }

            return json;

        } 
        catch (err) {
            console.error("[AJAX Dispatcher] Request Failed:", err);
            return Promise.reject(err);
        }
    }

    // Globalize
    window.themeAjaxRequest = themeAjaxRequest;
})();