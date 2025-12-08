<script setup>
import NavItems from '@/layouts/components/NavItems.vue'
import NavItemsAdmin from '@/layouts/components/NavItemsAdmin.vue'
import logo from '@images/logo_unisa.png'
import VerticalNavLayout from '@layouts/components/VerticalNavLayout.vue'
import { useRoute } from 'vue-router'
import { computed } from 'vue'

const route = useRoute()
const isAdmin = computed(() => route.path.startsWith('/admin'))
const headerTitle = computed(() => (isAdmin.value ? 'Pra Yudisium' : 'Dashboard'))
</script>

<template>
  <VerticalNavLayout>
<<<<<<< Updated upstream
    <!-- 👉 navbar -->
    <template #navbar="{ toggleVerticalOverlayNavActive }">
      <div class="d-flex h-100 align-center">
        <!-- 👉 Vertical nav toggle in overlay mode -->
        <IconBtn
          class="ms-n3 d-lg-none"
          @click="toggleVerticalOverlayNavActive(true)"
        >
          <VIcon icon="ri-menu-line" />
        </IconBtn>

        <!-- 👉 Search -->
        <!--
          <div
          class="d-flex align-center cursor-pointer"
          style="user-select: none;"
          >
          👉 Search Trigger button
          <IconBtn>
          <VIcon icon="ri-search-line" />
          </IconBtn>

          <span class="d-none d-md-flex align-center text-disabled">
          <span class="me-3">Mencari</span>
          <span class="meta-key">&#8984;K</span>
          </span>
          </div> 
        -->

        <VSpacer />

        <!--
          <IconBtn
          href="https://github.com/themeselection/materio-vuetify-vuejs-laravel-admin-template-free"
          target="_blank"
          rel="noopener noreferrer"
          >
          <VIcon icon="ri-github-fill" />
          </IconBtn> 
        -->

        <!--
          <IconBtn>
          <VIcon icon="ri-notification-line" />
          </IconBtn> 
        -->

        <NavbarThemeSwitcher class="me-2" />

        <!-- Language Selector -->
        <!--
          <IconBtn class="me-2">
          <VIcon icon="ri-translate-2" />
          </IconBtn> 
        -->

        <UserProfile />
      </div>
    </template>

=======
>>>>>>> Stashed changes
    <template #vertical-nav-header="{ toggleIsOverlayNavActive }">
      <RouterLink
        :to="isAdmin ? '/admin/prayudisium' : '/dashboard'"
        class="app-logo app-title-wrapper"
      >
        <img
          :src="logo"
          alt="UNISA logo"
          class="d-flex"
          style="max-block-size: 30px;"
        >
        <h1 class="font-weight-medium leading-normal text-xl text-uppercase">
          {{ headerTitle }}
        </h1>
      </RouterLink>
    </template>

    <template #vertical-nav-content>
      <NavItemsAdmin v-if="isAdmin" />
      <NavItems v-else />
    </template>

    <slot />
  </VerticalNavLayout>
</template>
