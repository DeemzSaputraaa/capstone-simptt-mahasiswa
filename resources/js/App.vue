

<script setup>
import { onBeforeUnmount, onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'

const showUnauthorizedModal = ref(false)
const router = useRouter()

const openUnauthorizedModal = () => {
  showUnauthorizedModal.value = true
}

const handleUnauthorizedConfirm = () => {
  showUnauthorizedModal.value = false
  sessionStorage.removeItem('jwt_token')
  sessionStorage.removeItem('user_data')
  window.__unauthorizedActive = false

  if (router.currentRoute.value?.path !== '/login')
    router.push('/login')
}

onMounted(() => {
  window.addEventListener('app:unauthorized', openUnauthorizedModal)
})

onBeforeUnmount(() => {
  window.removeEventListener('app:unauthorized', openUnauthorizedModal)
})
</script>

<template>
  <VApp>
    <RouterView />
    <!-- <UpgradeToPro /> -->
    <VDialog
      v-model="showUnauthorizedModal"
      max-width="420"
      persistent
    >
      <VCard>
        <VCardTitle class="text-h5">
          Sesi Berakhir
        </VCardTitle>
        <VCardText>
          Sesi Anda sudah habis. Silakan login kembali.
        </VCardText>
        <VCardActions>
          <VSpacer />
          <VBtn
            color="primary"
            @click="handleUnauthorizedConfirm"
          >
            OK
          </VBtn>
        </VCardActions>
      </VCard>
    </VDialog>
  </VApp>
</template>
