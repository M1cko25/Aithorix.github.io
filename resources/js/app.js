import './bootstrap';
import { createApp, h } from 'vue'
import { createInertiaApp, Head, Link } from '@inertiajs/vue3'
import Layout from '../js/Pages/Layout.vue'
import {ZiggyVue} from '../../vendor/tightenco/ziggy'
import { MotionPlugin } from '@vueuse/motion'
import { registerLicense } from '@syncfusion/ej2-base';
import { CalendarComponent } from '@syncfusion/ej2-vue-calendars';

createInertiaApp({
  title: (title) => `Aithorix ${title}`,
  resolve: name => {
    const pages = import.meta.glob('./Pages/**/*.vue', { eager: true }) 
    let page = pages[`./Pages/${name}.vue`]
    page.default.layout = page.default.layout || Layout;
    return page;
  },
  setup({ el, App, props, plugin }) {
    createApp({ render: () => h(App, props) })
      .use(plugin)
      .use(ZiggyVue)
      .use(MotionPlugin)
      .component('Head', Head)
      .component('Link', Link)
      .component('ejs-calendar', CalendarComponent)
      .mount(el)
  },
  progress: {
    color: '#4B5563',
    includeCSS: true,
    delay: 250,
  }
})
registerLicense('Ngo9BigBOggjHTQxAR8/V1NMaF1cXmhNYVVpR2Nbek5xdF9HZ1ZQTWYuP1ZhSXxWdkZjXn5ecXNXRWJUWUY=')