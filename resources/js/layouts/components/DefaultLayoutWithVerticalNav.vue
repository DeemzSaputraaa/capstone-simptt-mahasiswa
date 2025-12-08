<script setup>
import NavItems from '@/layouts/components/NavItems.vue'
import NavItemsAdmin from '@/layouts/components/NavItemsAdmin.vue'
import logo from '@images/logo_unisa.png'
import VerticalNavLayout from '@layouts/components/VerticalNavLayout.vue'
import NavbarThemeSwitcher from '@/layouts/components/NavbarThemeSwitcher.vue'
import UserProfile from '@/layouts/components/UserProfile.vue'
import { computed } from 'vue'
import { useRoute } from 'vue-router'

const route = useRoute()
const isAdmin = computed(() => route.path.startsWith('/admin'))
const headerTitle = 'SIMPTT'
const homeLink = computed(() => (isAdmin.value ? '/admin/prayudisium' : '/dashboard'))
</script>

<template>
  <VerticalNavLayout>
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

        <VSpacer />

        <NavbarThemeSwitcher class="me-2" />
        <UserProfile />
      </div>
    </template>

    <template #vertical-nav-header="{ toggleIsOverlayNavActive }">
      <RouterLink
        :to="homeLink"
        class="app-logo app-title-wrapper"
      >
        <img
          :src="logo"
          alt="logo"
          class="d-flex"
          style="max-block-size: 30px;"
        >

        <h1 class="font-weight-medium leading-normal text-xl text-uppercase">
          {{ headerTitle }}
        </h1>
      </RouterLink>

      <IconBtn
        class="d-block d-lg-none"
        @click="toggleIsOverlayNavActive(false)"
      >
        <VIcon icon="ri-close-line" />
      </IconBtn>
    </template>

    <template #vertical-nav-content>
      <NavItemsAdmin v-if="isAdmin" />
      <NavItems v-else />
    </template>

    <!-- 👉 Pages -->
    <slot />
  </VerticalNavLayout>
</template>

<style lang="scss" scoped>
.meta-key {
  border: thin solid rgba(var(--v-border-color), var(--v-border-opacity));
  border-radius: 6px;
  block-size: 1.5625rem;
  line-height: 1.3125rem;
  padding-block: 0.125rem;
  padding-inline: 0.25rem;
}

.app-logo {
  display: flex;
  align-items: center;
  column-gap: 0.75rem;

  .app-logo-title {
    font-size: 1.25rem;
    font-weight: 500;
    line-height: 1.75rem;
    text-transform: uppercase;
  }
}
</style>
