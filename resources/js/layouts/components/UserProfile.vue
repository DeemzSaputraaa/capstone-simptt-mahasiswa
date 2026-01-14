<script setup>
import { computed, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const user = ref({
  name: '',
})

const userInitial = computed(() => {
  const value = (user.value.name || '').trim()

  return value ? value.charAt(0).toUpperCase() : 'A'
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
      <div
        v-bind="props"
        class="user-profile-trigger"
      >
        <span class="font-weight-semibold">
          {{ user.name.split(' ')[0] }}
        </span>
        <VAvatar
          size="28"
          class="user-avatar"
        >
          {{ userInitial }}
        </VAvatar>
      </div>
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

<style scoped>
.user-profile-trigger {
  display: inline-flex;
  align-items: center;
  gap: 8px;
  cursor: pointer;
}

.user-avatar {
  background-color: rgba(var(--v-theme-primary), 0.15);
  color: rgb(var(--v-theme-primary));
  font-weight: 700;
}
</style>
