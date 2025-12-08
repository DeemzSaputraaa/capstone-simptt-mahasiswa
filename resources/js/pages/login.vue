<script setup>
import authV1MaskDark from '@images/pages/auth-v1-mask-dark.png'
import authV1MaskLight from '@images/pages/auth-v1-mask-light.png'
import logoUnisa from '@images/unisa_tulisan.png'
import logoUnisaDark from '@images/unisa_tulisan_dark.png'
import { useRouter } from 'vue-router'
import { useTheme } from 'vuetify'

const form = ref({
  nim: '',
  password: '',

  // tahunAkademik: null,
  remember: false,
})

// const tahunAkademikOptions = [
//   '2023/2024',
//   '2022/2023',
//   '2021/2022',
//   '2020/2021',
// ]

const showLoginErrorModal = ref(false)
const loginErrorMessage = ref('')
const isLoggingIn = ref(false)

const vuetifyTheme = useTheme()
const router = useRouter()

const authThemeMask = computed(() => {
  return vuetifyTheme.global.name.value === 'light' ? authV1MaskLight : authV1MaskDark
})

const logoTheme = computed(() => {
  return vuetifyTheme.global.name.value === 'light' ? logoUnisa : logoUnisaDark
})

const isPasswordVisible = ref(false)

const handleLogin = async () => {
  // Validasi form
  if (!form.value.nim || !form.value.password) {
    loginErrorMessage.value = 'Mohon lengkapi NIM dan Password'
    showLoginErrorModal.value = true
    
    return
  }

  // Set loading state
  isLoggingIn.value = true

  try {
    console.log('Attempting login...', { nim: form.value.nim })

    // Ambil CSRF token dari meta tag (jika ada)
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')
    
    // Kirim request ke API
    const headers = {
      'Content-Type': 'application/json',
      'Accept': 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    }
    
    // Tambahkan CSRF token jika tersedia
    if (csrfToken) {
      headers['X-CSRF-TOKEN'] = csrfToken
    }
    
    const response = await fetch('/api/login', {
      method: 'POST',
      headers,
      credentials: 'include', // Penting: kirim cookies untuk session
      body: JSON.stringify({
        nim: form.value.nim,
        password: form.value.password,
        remember_me: form.value.remember, // eslint-disable-line camelcase
      }),
    })

    const data = await response.json()

    console.log('Response from server:', data) 

    // ✅ PERBAIKAN: Cek response dengan format baru
    if (response.ok && data.isallowed === true) {
      console.log('Login successful!', data)
      
      // Simpan data user ke session storage
      // Simpan data tambahan yang mungkin dibutuhkan
      const userData = {
        namalengkap: data.namalengkap || data.name || '',
        username: data.username || form.value.nim,
        role: data.role || data.loginas,
        loginAs: data.loginas,
      }

      // Simpan ke session storage
      sessionStorage.setItem('user_data', JSON.stringify(userData))
      if (data.token) {
        sessionStorage.setItem('jwt_token', data.token)
      }
      
      // Simpan data ke localStorage (opsional)
      localStorage.setItem('login_as', data.loginas)
      localStorage.setItem('timeout', data.timeout)
      
      // Redirect berdasarkan role
      switch(data.loginas) {
      case 'mahasiswa':
        await router.push('/dashboard')
        break
      case 'tendik':
        await router.push('/admin/prayudisium')
        break
      case 'dosen':
        await router.push('/dosen/dashboard')
        break
      case 'preceptor':
        await router.push('/preceptor/dashboard')
        break
      default:
        await router.push('/dashboard')
      }
    } else {
      // Handle error response
      let errorMessage = 'Login gagal. Silakan coba lagi.'
      
      if (data.message) {
        errorMessage = data.message
      } else if (!data.isallowed) {
        errorMessage = 'NIM atau Password yang Anda masukkan salah. Silakan coba lagi.'
      }

      // Handle specific error messages
      if (errorMessage.includes('unavailable')) {
        errorMessage = 'Layanan autentikasi sedang tidak tersedia. Silakan coba lagi nanti.'
      } else if (errorMessage.includes('Validation failed')) {
        errorMessage = 'Mohon lengkapi semua field yang diperlukan.'
      }

      loginErrorMessage.value = errorMessage
      showLoginErrorModal.value = true
      console.error('Login failed:', data)
    }
  } catch (error) {
    console.error('Login error:', error)
    loginErrorMessage.value = 'Terjadi kesalahan saat melakukan login. Silakan coba lagi.'
    showLoginErrorModal.value = true
  } finally {
    isLoggingIn.value = false
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
              :src="logoTheme"
              alt="Logo Unisa"
              width="195"
              height="70"
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
        <h4 class="text-h4 mb-1 text-center">
          Welcome to SIMPTT Mahasiswa
        </h4>
        <p
          class="mb-0 text-center"
          style="font-size: 0.8rem;"
        >
          Please sign-in to your account and start the adventure
        </p>
      </VCardText>

      <VCardText>
        <VForm @submit.prevent="handleLogin">
          <VRow>
            <!-- NIM -->
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
            </VCol>

            <!-- Tahun Akademik -->
            <!--
              <VCol cols="12">
              <VSelect
              v-model="form.tahunAkademik"
              :items="tahunAkademikOptions"
              label="Tahun Akademik"
              placeholder="Pilih Tahun Akademik"
              />
              </VCol> 
            -->

            <!-- remember me checkbox and login button -->
            <VCol cols="12">
              <div class="d-flex align-center justify-space-between flex-wrap mb-4">
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
                color="#17a2a6"
                class="login-btn"
                :loading="isLoggingIn"
                :disabled="isLoggingIn"
              >
                {{ isLoggingIn ? 'Memproses...' : 'Login' }}
              </VBtn>
            </VCol>

            <VCol
              cols="12"
              class="text-center text-body-2"
            >
              <RouterLink
                class="text-primary"
                to="/tendik/login"
              >
                Login untuk Tendik?
              </RouterLink>
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
            <!--
              <VCol
              cols="12"
              class="text-center"
              >
              <AuthProvider />
              </VCol> 
            -->
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
            {{ loginErrorMessage || 'NIM atau Password yang Anda masukkan salah. Silakan coba lagi.' }}
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
    <!--
      <VImg
      class="auth-footer-mask d-none d-md-block"
      :src="authThemeMask"
      /> 
    -->
  </div>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth";

.auth-wrapper {
  // .auth-footer-mask {
  //   // Mengubah warna ungu menjadi cyan/tosca (#17a2a6)
  //   filter: hue-rotate(160deg) saturate(0.8) brightness(0.9);
  // }

  // Styling untuk form dengan jarak konsisten
  .v-row .v-col-12 {
    margin-block-end: 16px;
    padding-block: 0 !important;

    &:last-child {
      margin-block-end: 0;
    }
  }
}

.login-btn {
  border-radius: 8px;
  background: #17a2a6 !important;
  box-shadow: 0 2px 8px rgba(23, 162, 166, 25%);
  color: #fff !important;
  font-size: 1rem;
  font-weight: 500;
  min-block-size: 48px;
  text-transform: none;

  &:hover {
    background: #129990 !important;
    box-shadow: 0 4px 12px rgba(23, 162, 166, 35%);
  }
}
</style>
