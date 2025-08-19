import './bootstrap'
import '../css/app.css'

import { createInertiaApp } from '@inertiajs/vue3'
import { createApp, h } from 'vue'
import Vue3Toasity from 'vue3-toastify'
import 'vue3-toastify/dist/index.css'
import { ZiggyVue } from 'ziggy-js'

createInertiaApp({
    resolve: name => {
        const pages = import.meta.glob('./pages/**/*.vue', { eager: false })
        return pages[`./pages/${name}.vue`]()
    },
    setup({ el, App, props, plugin }) {
        createApp({ render: () => h(App, props) })
            .use(plugin)
            .use(Vue3Toasity, {
                autoClose: 3000,
            })
            .use(ZiggyVue)
            .mount(el)
    },
})
