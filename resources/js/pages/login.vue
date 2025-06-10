<script setup>
import AuthProvider from '@/views/pages/authentication/AuthProvider.vue'
import authV1MaskDark from '@images/pages/auth-v1-mask-dark.png'
import authV1MaskLight from '@images/pages/auth-v1-mask-light.png'
import logoUnisa from '@images/unisa_tulisan.png'
import { useRouter } from 'vue-router'
import { useTheme } from 'vuetify'

const form = ref({
  nim: '',
  password: '',
  tahunAkademik: null,
  remember: false,
})

const tahunAkademikOptions = [
  '2023/2024',
  '2022/2023',
  '2021/2022',
  '2020/2021',
]

const showLoginErrorModal = ref(false)

const vuetifyTheme = useTheme()
const router = useRouter()

const authThemeMask = computed(() => {
  return vuetifyTheme.global.name.value === 'light' ? authV1MaskLight : authV1MaskDark
})

const isPasswordVisible = ref(false)

const handleLogin = () => {
  console.log('Attempting login...')

  // Contoh validasi sederhana
  if (form.value.nim === '12345678' && form.value.password === 'password123') {
    console.log('Login successful! Redirecting to dashboard.')
    router.push('/dashboard')
  } else {
    console.log('Login failed: Incorrect credentials.')
    showLoginErrorModal.value = true
  }
}
</script>

<template>
  <!-- eslint-disable vue/no-v-html -->

  <div class="auth-wrapper d-flex align-center justify-center pa-4">
    <VCard
      class="auth-card pa-4 pt-7"
      max-width="448"
    >
      <VCardItem class="justify-center">
        <RouterLink
          to="/"
          class="d-flex align-center gap-3"
        >
          <!-- eslint-disable vue/no-v-html -->
          <div class="d-flex">
            <img
              :src="logoUnisa"
              alt="Logo Unisa"
              width="140"
              height="50"
            >
          </div>
          <!--
            <h2 class="font-weight-medium text-2xl text-uppercase">
            Materio
            </h2> 
          -->
        </RouterLink>
      </VCardItem>

      <VCardText class="pt-2">
        <h4 class="text-h4 mb-1">
          Welcome to SIMPTT Mahasiswa
        </h4>
        <p class="mb-0">
          Please sign-in to your account and start the adventure
        </p>
      </VCardText>

      <VCardText>
        <VForm @submit.prevent="handleLogin">
          <VRow>
            <!-- email -->
            <VCol cols="12">
              <VTextField
                v-model="form.nim"
                label="NIM"
                placeholder="Masukkan NIM"
              />
            </VCol>

            <!-- password -->
            <VCol cols="12">
              <VTextField
                v-model="form.password"
                label="Password"
                placeholder="············"
                :type="isPasswordVisible ? 'text' : 'password'"
                :append-inner-icon="isPasswordVisible ? 'ri-eye-off-line' : 'ri-eye-line'"
                @click:append-inner="isPasswordVisible = !isPasswordVisible"
              />

              <VSelect
                v-model="form.tahunAkademik"
                :items="tahunAkademikOptions"
                label="Tahun Akademik"
                placeholder="Pilih Tahun Akademik"
                class="mt-4"
              />

              <!-- remember me checkbox -->
              <div class="d-flex align-center justify-space-between flex-wrap mt-1 mb-4">
                <VCheckbox
                  v-model="form.remember"
                  label="Remember Me"
                />

                <!--
                  <RouterLink
                  class="text-primary text-body-1"
                  to="/forgot-password"
                  >
                  Forgot Password?
                  </RouterLink> 
                -->
              </div>

              <!-- login button -->
              <VBtn
                block
                type="submit"
              >
                Login
              </VBtn>
            </VCol>

            <!-- create account -->
            <!--
              <VCol
              cols="12"
              class="text-center text-base"
              >
              <span>New on our platform?</span>
              <RouterLink
              class="text-primary ms-2"
              to="/register"
              >
              Create an account
              </RouterLink>
              </VCol> 
            -->

            <!--
              <VCol
              cols="12"
              class="d-flex align-center"
              >
              <VDivider />
              <span class="mx-4 text-high-emphasis">or</span>
              <VDivider />
              </VCol> 
            -->

            <!-- auth providers -->
            <VCol
              cols="12"
              class="text-center"
            >
              <AuthProvider />
            </VCol>
          </VRow>
        </VForm>
      </VCardText>

      <VDialog
        v-model="showLoginErrorModal"
        max-width="500"
      >
        <VCard>
          <VCardTitle class="headline">
            Login Gagal
          </VCardTitle>
          <VCardText>
            NIM atau Password yang Anda masukkan salah. Silakan coba lagi.
          </VCardText>
          <VCardActions>
            <VSpacer />
            <VBtn
              color="primary"
              text
              @click="showLoginErrorModal = false"
            >
              Tutup
            </VBtn>
          </VCardActions>
        </VCard>
      </VDialog>
    </VCard>

    <!--
      <VImg
      class="auth-footer-start-tree d-none d-md-block"
      :src="authV1Tree"
      :width="250"
      />

      <VImg
      :src="authV1Tree2"
      class="auth-footer-end-tree d-none d-md-block"
      :width="350"
      /> 
    -->

    <!-- bg img -->
    <VImg
      class="auth-footer-mask d-none d-md-block"
      :src="authThemeMask"
    />
  </div>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth";
</style>
