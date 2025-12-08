import App from '@/App.vue'
import { registerPlugins } from '@core/utils/plugins'
import { createApp } from 'vue'

// Styles
import '@core-scss/template/index.scss'
import '@layouts/styles/index.scss'
import '@mdi/font/css/materialdesignicons.min.css'

// Create vue app
const app = createApp(App)


// Register plugins
registerPlugins(app)

// Mount vue app
app.mount('#app')

// Hide initial loader once Vue is mounted
const loader = document.getElementById('loading-bg')
if (loader)
  loader.style.display = 'none'
