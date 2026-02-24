import '../css/app.css';
import 'bootstrap/dist/css/bootstrap.min.css';
import 'bootstrap/dist/js/bootstrap.bundle.min.js';  // Optional, for JS components
import $ from 'jquery';
window.$ = window.jQuery = $;
import 'admin-lte/dist/css/adminlte.min.css';
import 'admin-lte/dist/js/adminlte.min.js';
import NProgress from 'nprogress';
import 'nprogress/nprogress.css';
import { createApp, h } from 'vue'
import { createInertiaApp } from '@inertiajs/vue3'

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./Pages/**/*.vue', { eager: true })
        return pages[`./Pages/${name}.vue`]
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .mount(el)
    },
})

NProgress.configure({ showSpinner: false });

document.addEventListener('inertia:start', () => {
    NProgress.start();
});

document.addEventListener('inertia:progress', (e) => {
    const progress = e?.detail?.progress;
    if (typeof progress === 'number') {
        NProgress.set(progress / 100);
    }
});

document.addEventListener('inertia:finish', () => {
    NProgress.done();
});

document.addEventListener('inertia:error', () => {
    NProgress.done();
});