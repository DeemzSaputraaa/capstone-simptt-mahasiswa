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
        meta: { title: 'Dashboard' },
      },
      {
        path: 'account-settings',
        component: () => import('@/pages/account-settings.vue'),
        meta: { title: 'Account Settings' },
      },

      // {
      //   path: 'typography',
      //   component: () => import('@/pages/typography.vue'),
      // },
      {
        path: 'icons',
        component: () => import('@/pages/icons.vue'),
        meta: { title: 'Icons' },
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
        meta: { title: 'Pra Yudisium' },
      },
      {
        path: 'validasi-ijazah',
        component: () => import('@/pages/ValidasiIjazah.vue'),
        meta: { title: 'Validasi Ijazah' },
      },
      {
        path: 'pendaftaran-legalisasi',
        component: () => import('@/pages/PendaftaranLegalisasi.vue'),
        meta: { title: 'Pendaftaran Legalisasi' },
      },
      {
        path: 'edit-profile',
        component: () => import('@/pages/EditProfile.vue'),
        meta: { title: 'Edit Profile' },
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
        meta: { title: 'Admin - Pra Yudisium' },
      },
      {
        path: 'validasiijazah',
        component: () => import('@/pages/admin/ValidasiIjazah.vue'),
        meta: { title: 'Admin - Validasi Ijazah' },
      },
      {
        path: 'legalisasi',
        component: () => import('@/pages/admin/Legalisasi.vue'),
        meta: { title: 'Admin - Legalisasi' },
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
        path: 'tendik/login',
        component: () => import('@/pages/login-tendik.vue'),
      },

      // {
      //   path: 'register',
      //   component: () => import('@/pages/register.vue'),
      // },
      {
        path: '/:pathMatch(.*)*',
        component: () => import('@/pages/[...error].vue'),
      },
    ],
  },
]
