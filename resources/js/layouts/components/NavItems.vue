<script setup>
import VerticalNavGroup from '@layouts/components/VerticalNavGroup.vue'
import VerticalNavLink from '@layouts/components/VerticalNavLink.vue'
</script>

<template>
  <!-- 👉 Dashboards -->
  <VerticalNavLink
    :item="{
      title: 'Dashboards',
      // badgeContent: '5',
      // badgeClass: 'bg-error',
      icon: 'ri-home-smile-line',
      to: '/dashboard',
    }"
  />

  <!-- 👉 Pra Yudisium -->
  <VerticalNavLink
    :item="{
      title: 'Pra Yudisium',
      icon: 'ri-file-copy-line',
      to: '/pra-yudisium',
    }"
  />

  <!-- 👉 Validasi Ijazah -->
  <VerticalNavLink
    :item="{
      title: 'Validasi Ijazah',
      icon: 'ri-file-copy-line',
      to: '/validasi-ijazah',
    }"
  />

  <!-- 👉 Pendaftaran Legalisasi -->
  <VerticalNavLink
    :item="{
      title: 'Pendaftaran Legalisasi',
      icon: 'ri-file-copy-line',
      to: '/pendaftaran-legalisasi',
    }"
  />

  <!-- 👉 Front Pages -->
  <!--
    <VerticalNavLink
    :item="{
    title: 'Landing',
    href: 'https://demos.themeselection.com/materio-vuetify-vuejs-laravel-admin-template/demo-1/front-pages/landing-page',
    target: '_blank',
    }"
    />
    <VerticalNavLink
    :item="{
    title: 'Pricing',
    href: 'https://demos.themeselection.com/materio-vuetify-vuejs-laravel-admin-template/demo-1/front-pages/pricing',
    target: '_blank',
    }"
    />
    </VerticalNavGroup> 
  -->

  <VerticalNavGroup
    :item="{
      title: 'Validasi Ijazah',
      icon: 'ri-file-copy-line',
      // to: '/fitur-satu',
      // href: 'FiturSatu',
      // target: '_blank',
      // path: '/fitur-satu',
    }"
  >
    <VerticalNavLink 
      :item="{
        title: 'Pembayaran',
        href: 'https://demos.themeselection.com/materio-vuetify-vuejs-laravel-admin-template/demo-1/front-pages/landing-page',
        // target: '_blank',
      }"
    />
    <VerticalNavLink 
      :item="{
        title: 'Pengiriman',
        href: 'https://demos.themeselection.com/materio-vuetify-vuejs-laravel-admin-template/demo-1/front-pages/landing-page',
        // target: '_blank',
      }"
    />
  </VerticalNavGroup>
  <VerticalNavLink
    :item="{
      title: 'Pendaftaran Ijazah',
      icon: 'ri-file-copy-line',
      to: '',
    }"
  />
  <VerticalNavLink
    :item="{
      title: 'Pendaftaran Legalisir',
      icon: 'ri-file-copy-line',
      to: '',
    }"
  />
</template>
