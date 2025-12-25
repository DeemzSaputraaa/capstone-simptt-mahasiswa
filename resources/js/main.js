import App from '@/App.vue'
import { registerPlugins } from '@core/utils/plugins'
import { createApp } from 'vue'
import axios from 'axios'
import { router } from '@/plugins/router'

// Styles
import '@core-scss/template/index.scss'
import '@layouts/styles/index.scss'
import '@mdi/font/css/materialdesignicons.min.css'

// Create vue app
const app = createApp(App)


// Register plugins
registerPlugins(app)

const handleUnauthorized = () => {
  if (window.__unauthorizedActive)
    return

  window.__unauthorizedActive = true
  window.dispatchEvent(new CustomEvent('app:unauthorized'))
}

const originalFetch = window.fetch?.bind(window)
if (originalFetch) {
  window.fetch = async (...args) => {
    const response = await originalFetch(...args)
    if (response.status === 401)
      handleUnauthorized()
    return response
  }
}

axios.interceptors.response.use(
  response => response,
  error => {
    if (error?.response?.status === 401)
      handleUnauthorized()

    return Promise.reject(error)
  },
)

// Mount vue app
app.mount('#app')

// Hide initial loader once Vue is mounted
const loader = document.getElementById('loading-bg')
if (loader)
  loader.style.display = 'none'
