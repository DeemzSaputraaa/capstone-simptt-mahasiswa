<script setup>
import VerticalNavLink from '@layouts/components/VerticalNavLink.vue'
</script>

<template>
  <VerticalNavLink :item="{ title: 'Pra Yudisium', icon: 'ri-graduation-cap-line', to: '/admin/prayudisium' }" />
  <VerticalNavLink :item="{ title: 'Validasi Ijazah', icon: 'ri-file-check-line', to: '/admin/validasiijazah' }" />
  <VerticalNavLink :item="{ title: 'Legalisasi', icon: 'ri-settings-line', to: '/admin/legalisasi' }" />
</template>
