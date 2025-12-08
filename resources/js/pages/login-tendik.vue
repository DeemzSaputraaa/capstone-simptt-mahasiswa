<script setup>
import authV1MaskDark from '@images/pages/auth-v1-mask-dark.png'
import authV1MaskLight from '@images/pages/auth-v1-mask-light.png'
import logoUnisa from '@images/unisa_tulisan.png'
import logoUnisaDark from '@images/unisa_tulisan_dark.png'
import { useRouter } from 'vue-router'
import { useTheme } from 'vuetify'

const form = ref({
  nip: '',
  password: '',
  remember: false,
})

const showLoginErrorModal = ref(false)
const loginErrorMessage = ref('')
const isLoggingIn = ref(false)
const isPasswordVisible = ref(false)

const vuetifyTheme = useTheme()
const router = useRouter()

const authThemeMask = computed(() => vuetifyTheme.global.name.value === 'light' ? authV1MaskLight : authV1MaskDark)
const logoTheme = computed(() => vuetifyTheme.global.name.value === 'light' ? logoUnisa : logoUnisaDark)

const handleLogin = async () => {
  if (!form.value.nip || !form.value.password) {
    loginErrorMessage.value = 'Mohon lengkapi NIP dan Password'
    showLoginErrorModal.value = true

    return
  }

  isLoggingIn.value = true

  try {
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

    const headers = {
      'Content-Type': 'application/json',
      Accept: 'application/json',
      'X-Requested-With': 'XMLHttpRequest',
    }

    if (csrfToken) {
      headers['X-CSRF-TOKEN'] = csrfToken
    }

    const response = await fetch('/api/login', {
      method: 'POST',
      headers,
      credentials: 'include',
      body: JSON.stringify({
        nip: form.value.nip,
        password: form.value.password,
        remember_me: form.value.remember,
      }),
    })

    const data = await response.json()

    if (response.ok && data.isallowed === true) {
      if (data.loginas && data.loginas !== 'tendik') {
        loginErrorMessage.value = 'Akun ini bukan akun tendik.'
        showLoginErrorModal.value = true
        return
      }

      const userData = {
        username: data.username || form.value.nip,
        name: data.name || '',
        role: data.role || 'admin_akademik',
        loginAs: data.loginas || 'tendik',
      }

      sessionStorage.setItem('user_data', JSON.stringify(userData))
      if (data.token) {
        sessionStorage.setItem('jwt_token', data.token)
      }

      localStorage.setItem('login_as', data.loginas || 'tendik')
      if (data.timeout) {
        localStorage.setItem('timeout', data.timeout)
      }

      await router.push('/admin/prayudisium')
    } else {
      const errorMessage = data?.message || 'NIP atau Password yang Anda masukkan salah. Silakan coba lagi.'
      loginErrorMessage.value = errorMessage
      showLoginErrorModal.value = true
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
  <div class="auth-wrapper d-flex align-center justify-center pa-4">
    <VCard
      class="auth-card pa-4 pt-7"
      max-width="448"
    >
      <VCardItem class="justify-center">
        <RouterLink
          to="/tendik/login"
          class="d-flex align-center gap-3"
        >
          <div class="d-flex">
            <img
              :src="logoTheme"
              alt="Logo Unisa"
              width="195"
              height="70"
            >
          </div>
        </RouterLink>
      </VCardItem>

      <VCardText class="pt-2">
        <h4 class="text-h4 mb-1 text-center">
          Login Tendik SIMPTT
        </h4>
        <p
          class="mb-0 text-center"
          style="font-size: 0.8rem;"
        >
          Masuk menggunakan NIP dan password Anda.
        </p>
      </VCardText>

      <VCardText>
        <VForm @submit.prevent="handleLogin">
          <VRow>
            <VCol cols="12">
              <VTextField
                v-model="form.nip"
                label="NIP"
                placeholder="Masukkan NIP"
              />
            </VCol>

            <VCol cols="12">
              <VTextField
                v-model="form.password"
                label="Password"
                placeholder="********"
                :type="isPasswordVisible ? 'text' : 'password'"
                :append-inner-icon="isPasswordVisible ? 'ri-eye-off-line' : 'ri-eye-line'"
                @click:append-inner="isPasswordVisible = !isPasswordVisible"
              />
            </VCol>

            <VCol cols="12">
              <div class="d-flex align-center justify-space-between flex-wrap mb-4">
                <VCheckbox
                  v-model="form.remember"
                  label="Remember Me"
                />
              </div>

              <VBtn
                block
                type="submit"
                color="#17a2a6"
                class="login-btn"
                :loading="isLoggingIn"
                :disabled="isLoggingIn"
              >
                {{ isLoggingIn ? 'Memproses...' : 'Login Tendik' }}
              </VBtn>
            </VCol>

            <VCol
              cols="12"
              class="text-center text-body-2"
            >
              <RouterLink
                class="text-primary"
                to="/login"
              >
                Login Mahasiswa?
              </RouterLink>
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
            {{ loginErrorMessage || 'NIP atau Password yang Anda masukkan salah. Silakan coba lagi.' }}
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
  </div>
</template>

<style lang="scss">
@use "@core-scss/template/pages/page-auth";

.auth-wrapper {
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
