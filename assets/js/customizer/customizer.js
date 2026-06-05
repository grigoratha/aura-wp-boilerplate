(function (window, wp) {
    if (!wp || !wp.customize) {
        return;
    }

    const root = document.documentElement;
    const loadedFonts = new Set();

    /* =====================================
     * FONT LOADER
     * ===================================== */
    function fetchGoogleFont(font) {
        if (!font || loadedFonts.has(font)) return;

        const family = font.trim().replace(/\s+/g, '+');

        const link = document.createElement('link');
        link.rel = 'stylesheet';
        link.href =
            'https://fonts.googleapis.com/css2?family=' +
            family +
            ':wght@300;400;500;600;700&display=swap';

        document.head.appendChild(link);

        loadedFonts.add(font);
    }

    function setFont(property, fontName) {
        if (!fontName || typeof fontName !== 'string') {
            root.style.setProperty(property, 'inherit');
            return;
        }

        const normalized = fontName.trim();
        fetchGoogleFont(normalized);
        root.style.setProperty(property, normalized);
    }

    function setFontSize(property, size) {
        const val = parseFloat(size);
        root.style.setProperty(
            property,
            (isNaN(val) ? 1 : val) + 'em'
        );
    }

    function setColor(property, color) {
        if (!color) return;
        root.style.setProperty(property, color);
    }

    /* =====================================
     * COLOR PALETTE
     * ===================================== */
    function calcPalette(color) {
        if (!color || !color.startsWith('#')) return null;

        let hex = color.replace('#', '').trim();

        if (hex.length === 3) {
            hex = hex.split('').map(c => c + c).join('');
        }

        const r = parseInt(hex.slice(0, 2), 16);
        const g = parseInt(hex.slice(2, 4), 16);
        const b = parseInt(hex.slice(4, 6), 16);

        const luminance =
            (0.2126 * r + 0.7152 * g + 0.0722 * b) / 255;

        const adjust = (percent) => {
            const calc = (c) =>
                Math.max(0, Math.min(255, Math.round(c + 255 * percent)));

            return (
                '#' +
                calc(r).toString(16).padStart(2, '0') +
                calc(g).toString(16).padStart(2, '0') +
                calc(b).toString(16).padStart(2, '0')
            );
        };

        if (luminance < 0.2) {
            return {
                'contrast-1': adjust(0.05),
                'contrast-2': adjust(0.09),
                'contrast-3': adjust(0.15),
                'contrast-4': adjust(0.23),
                'contrast-5': adjust(0.31),
                'contrast-6': adjust(0.39),
                reverse: '#f2f2f2'
            };
        }

        if (luminance > 0.8) {
            return {
                'contrast-1': adjust(-0.04),
                'contrast-2': adjust(-0.08),
                'contrast-3': adjust(-0.14),
                'contrast-4': adjust(-0.22),
                'contrast-5': adjust(-0.30),
                'contrast-6': adjust(-0.38),
                reverse: '#1c1c1c'
            };
        }

        return {
            'contrast-1': adjust(-0.04),
            'contrast-2': adjust(-0.08),
            'contrast-3': adjust(-0.14),
            'contrast-4': adjust(0.14),
            'contrast-5': adjust(0.22),
            'contrast-6': adjust(0.30),
            reverse: '#a68585'
        };
    }

    function setPalette(property, color) {
        if (!color) return;

        const palette = calcPalette(color);
        if (!palette) return;

        root.style.setProperty(property, color);

        for (const key in palette) {
            root.style.setProperty(
                `${property}-${key}`,
                palette[key]
            );
        }
    }

    /* =====================================
     * ANIMATIONS
     * ===================================== */
    function setAnimation(selector, animation) {
        const el = document.querySelector(selector);
        if (!el) return;

        el.className = '';
        if (!animation) return;

        el.classList.add(animation);
    }

    /* =====================================
     * CUSTOMIZER BINDINGS
     * ===================================== */
    function bind(setting, callback) {
        wp.customize(setting, function (value) {
            callback(value.get());
            value.bind(callback);
        });
    }

    wp.customize.bind('ready', function () {

        /* MENU */
        bind('menu_font', v =>
            setFont('--menu-font-family', v)
        );

        bind('menu_background_color', v =>
            setColor('--menu-background-color', v)
        );

        bind('menu_item_color', v =>
            setColor('--menu-item-color', v)
        );

        bind('menu_item_hover_color', v =>
            setColor('--menu-item-hover-color', v)
        );

        bind('menu_item_font_size', v =>
            setFontSize('--menu-item-font-size', v)
        );

        bind('submenu_background_color', v =>
            setPalette('--menu-dropdown-background-color', v)
        );

        bind('submenu_item_color', v =>
            setColor('--menu-dropdown-item-color', v)
        );

        bind('submenu_item_hover_color', v =>
            setColor('--menu-dropdown-item-hover-color', v)
        );

        bind('submenu_item_font_size', v =>
            setFontSize('--menu-dropdown-item-font-size', v)
        );

        console.log('Customizer preview loaded ✔');
    });

})(window, wp);