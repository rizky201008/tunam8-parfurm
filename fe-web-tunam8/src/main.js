import './assets/main.css'

import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import Notifications from '@kyvg/vue3-notification'

// Vuetify
import 'vuetify/styles'
import { createVuetify } from 'vuetify'
import * as components from 'vuetify/components'
import * as directives from 'vuetify/directives'
import '@mdi/font/css/materialdesignicons.css'
import CanvasJSChart from '@canvasjs/vue-charts';


const app = createApp(App)


const vuetify = createVuetify({
    components,
    directives,
  })

app.use(router);
app.use(CanvasJSChart);
app.use(Notifications);
app.use(vuetify);
app.mount('#app')
