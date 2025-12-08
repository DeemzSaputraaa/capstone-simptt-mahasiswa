import { createRouter, createWebHistory } from 'vue-router'
import { routes } from './routes'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes,
})

const publicPaths = ['/', '/login', '/register', '/tendik/login']

router.beforeEach((to, from, next) => {
  const token = sessionStorage.getItem('jwt_token')

  if (!token && !publicPaths.includes(to.path)) {
    return next('/login')
  }

  return next()
})

export default function (app) {
  app.use(router)
}
export { router }
