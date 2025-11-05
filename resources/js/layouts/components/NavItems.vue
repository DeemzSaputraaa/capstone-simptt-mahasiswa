<script setup>
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
      icon: 'ri-graduation-cap-line',
      to: '/pra-yudisium',
    }"
  />

  <!-- 👉 Validasi Ijazah -->
  <VerticalNavLink
    :item="{
      title: 'Validasi Ijazah',
      icon: 'ri-file-check-line',
      to: '/validasi-ijazah',
    }"
  />

  <!-- 👉 Pendaftaran Legalisasi -->
  <VerticalNavLink
    :item="{
      title: 'Pendaftaran Legalisasi',
      icon: 'ri-settings-line',
      to: '/pendaftaran-legalisasi',
    }"
  />
</template>
