<template>
  <div
    class="edit-profile"
    style="padding: 2rem;margin: auto;max-inline-size: 400px;"
  >
    <h2>Edit Profil</h2>
    <form @submit.prevent="saveProfile">
      <!-- Avatar Selection -->
      <div style="margin-block-end: 1rem;">
        <label>Pilih Avatar</label>
        <div style="display: flex;flex-wrap: wrap;gap: 10px;margin-block-start: 8px;">
          <img
            v-for="avatar in avatars"
            :key="avatar"
            :src="`/images/avatars/${avatar}`"
            :class="{ selected: selectedAvatar === avatar }"
            width="48"
            height="48"
            style="border: 2px solid #eee;border-radius: 50%;cursor: pointer;"
            :style="selectedAvatar === avatar ? 'border:2px solid #1976d2;' : ''"
            @click="selectedAvatar = avatar"
          >
        </div>
      </div>
      <!-- Nickname -->
      <div style="margin-block-end: 1rem;">
        <label>Nama Panggilan</label>
        <input
          v-model="nickname"
          type="text"
          required
          style="padding: 6px;border: 1px solid #ccc;border-radius: 4px;inline-size: 100%;"
        >
      </div>
      <!-- Password -->
      <div style="margin-block-end: 1rem;">
        <label>Password Baru</label>
        <input
          v-model="password"
          type="password"
          style="padding: 6px;border: 1px solid #ccc;border-radius: 4px;inline-size: 100%;"
        >
      </div>
      <div style="margin-block-end: 1rem;">
        <label>Konfirmasi Password</label>
        <input
          v-model="confirmPassword"
          type="password"
          style="padding: 6px;border: 1px solid #ccc;border-radius: 4px;inline-size: 100%;"
        >
      </div>
      <button
        type="submit"
        style="border: none;border-radius: 4px;background: #1976d2;color: #fff;inline-size: 100%;padding-block: 8px;padding-inline: 0;"
      >
        Simpan
      </button>
    </form>
  </div>
</template>

<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'

const router = useRouter()

const avatars = [
  'avatar-1.png',
  'avatar-2.png',
  'avatar-3.png',
  'avatar-4.png',
  'avatar-5.png',
  'avatar-6.png',
  'avatar-7.png',
  'avatar-8.png',
  'avatar-9.png',
  'avatar-10.png',
  'avatar-11.png',
]

const selectedAvatar = ref(avatars[0])
const nickname = ref('')
const password = ref('')
const confirmPassword = ref('')

function saveProfile() {
  if (password.value && password.value !== confirmPassword.value) {
    alert('Password tidak cocok')
    
    return
  }

  // Simpan data ke backend atau localStorage sesuai kebutuhan
  alert('Profil berhasil disimpan!')
  router.push('/') // Kembali ke halaman utama/dashboard
}
</script>

<style scoped>
.selected {
  border: 2px solid #1976d2 !important;
}
</style> 
