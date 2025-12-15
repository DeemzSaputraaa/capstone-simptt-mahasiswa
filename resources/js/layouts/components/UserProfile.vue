<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const user = ref({
  name: '',
})

// Fungsi untuk mengambil data user
const fetchUserData = async () => {
  try {
    const userData = JSON.parse(sessionStorage.getItem('user_data') || '{}')
    const displayName = userData.namalengkap || userData.name || userData.username
    user.value.name = displayName || 'Admin'
  } catch (error) {
    console.error('Error fetching user data:', error)
  }
}

const handleLogout = async () => {
  const token = sessionStorage.getItem('jwt_token')

  try {
    await fetch('/api/logout', {
      method: 'POST',
      headers: {
        'Content-Type': 'application/json',
        ...(token ? { Authorization: `Bearer ${token}` } : {}),
      },
    })
  } catch (error) {
    console.error('Logout error:', error)
  } finally {
    sessionStorage.removeItem('jwt_token')
    sessionStorage.removeItem('user_data')
    localStorage.removeItem('login_as')
    localStorage.removeItem('timeout')
    router.push('/login')
  }
}

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
        {{ user.name.split(' ')[0] }}
      </span>
    </template>

    <!-- Dropdown Menu -->
    <VList>
      <VListItem @click="handleLogout">
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
