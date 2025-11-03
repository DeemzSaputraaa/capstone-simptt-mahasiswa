<script setup>
import { onMounted, ref } from 'vue'

const user = ref({
  name: '',
})

// Fungsi untuk mengambil data user
const fetchUserData = async () => {
  try {
    // Ambil data dari session storage atau local storage
    const userData = JSON.parse(sessionStorage.getItem('user_data'))
    if (userData && userData.namalengkap) {
      user.value.name = userData.namalengkap
    }
  } catch (error) {
    console.error('Error fetching user data:', error)
  }
}

// Panggil fungsi saat komponen dimount
onMounted(() => {
  fetchUserData()
})
</script>

<template>
  <!-- Nama User sebagai dropdown -->
  <VMenu
    location="bottom end"
    offset="14px"
  >
    <!-- Activator: klik nama user -->
    <template #activator="{ props }">
      <span
        v-bind="props"
        class="cursor-pointer font-weight-semibold"
      >
        {{ user.name }}
      </span>
    </template>

    <!-- Dropdown Menu -->
    <VList>
      <!-- 👉 Logout -->
      <VListItem @click="$router.push('/login')">
        <template #prepend>
          <VIcon
            class="me-2"
            icon="ri-logout-box-r-line"
            size="22"
          />
        </template>
        <VListItemTitle>Logout</VListItemTitle>
      </VListItem>
    </VList>
  </VMenu>
</template>
