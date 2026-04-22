import "../css/app.css";
import "./bootstrap";

import { createInertiaApp } from "@inertiajs/inertia-vue3";
import { resolvePageComponent } from "laravel-vite-plugin/inertia-helpers";
import { createApp, h } from "vue";
import { ZiggyVue } from "ziggy-js";
import AppLayout from "./Layouts/AppLayout.vue"; // Explicitly import AppLayout from Layouts folder
import { formatCurrency, formatNumber } from "./Utils/helpers"; // Import helper functions
import i18n from "./i18n"; // Import i18n instance

const appName = import.meta.env.VITE_APP_NAME || "Laravel";

createInertiaApp({
    title: (title) => `${title} - ${appName}`,
    resolve: (name) =>
        resolvePageComponent(
            `./Pages/${name}.vue`,
            import.meta.glob("./Pages/**/*.vue"),
        ),
    setup({ el, App, props, plugin }) {
        const app = createApp({ render: () => h(App, props) });
        app.config.globalProperties.$formatCurrency = formatCurrency;
        app.config.globalProperties.$formatNumber = formatNumber;
        return app
            .use(plugin)
            .use(ZiggyVue)
            .use(i18n)
            .component("Layout", AppLayout) // Register AppLayout globally
            .mount(el);
    },
    progress: {
        color: "#4B5563",
    },
});
