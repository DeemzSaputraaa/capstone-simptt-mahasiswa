export const routes = [
  { path: '/', redirect: '/login' },
  { path: '/admin', redirect: '/admin/prayudisium' },
  {
    path: '/',
    component: () => import('@/layouts/default.vue'),
    children: [
      {
        path: 'dashboard',
        component: () => import('@/pages/dashboard.vue'),
      },
      {
        path: 'account-settings',
        component: () => import('@/pages/account-settings.vue'),
      },

      // {
      //   path: 'typography',
      //   component: () => import('@/pages/typography.vue'),
      // },
      {
        path: 'icons',
        component: () => import('@/pages/icons.vue'),
      },

      // {
      //   path: 'cards',
      //   component: () => import('@/pages/cards.vue'),
      // },
      // {
      //   path: 'tables',
      //   component: () => import('@/pages/tables.vue'),
      // },
      // {
      //   path: 'form-layouts',
      //   component: () => import('@/pages/form-layouts.vue'),
      // },
      {
        path: 'pra-yudisium',
        component: () => import('@/pages/PraYudisium.vue'),
      },
      {
        path: 'validasi-ijazah',
        component: () => import('@/pages/ValidasiIjazah.vue'),
      },
      {
        path: 'pendaftaran-legalisasi',
        component: () => import('@/pages/PendaftaranLegalisasi.vue'),
      },
      {
        path: 'edit-profile',
        component: () => import('@/pages/EditProfile.vue'),
      },
    ],
  },
  {
    path: '/admin',
    component: () => import('@/layouts/default.vue'),
    children: [
      {
        path: 'prayudisium',
        component: () => import('@/pages/admin/PraYudisium.vue'),
      },
      {
        path: 'validasiijazah',
        component: () => import('@/pages/admin/ValidasiIjazah.vue'),
      },
      {
        path: 'legalisasi',
        component: () => import('@/pages/admin/Legalisasi.vue'),
      },
    ],
  },
  {
    path: '/',
    component: () => import('@/layouts/blank.vue'),
    children: [
      {
        path: 'login',
        component: () => import('@/pages/login.vue'),
      },
      {
        path: 'register',
        component: () => import('@/pages/register.vue'),
      },
      {
        path: '/:pathMatch(.*)*',
        component: () => import('@/pages/[...error].vue'),
      },
    ],
  },
]
