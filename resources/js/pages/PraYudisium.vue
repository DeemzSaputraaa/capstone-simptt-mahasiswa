<template>
  <VApp>
    <VMain>
      <VContainer 
        fluid 
        class="pa-6"
      >
        <!-- Notifikasi Sukses -->
        <VAlert
          v-if="showSuccess"
          type="success"
          variant="tonal"
          class="mb-4"
          border="start"
          prominent
        >
          Data berhasil disimpan!
        </VAlert>

        <VAlert
          v-if="infoMessage"
          type="info"
          variant="tonal"
          class="mb-4"
          border="start"
          prominent
        >
          {{ infoMessage }}
        </VAlert>
        
        <!-- Notifikasi Error -->
        <VAlert
          v-if="errorMessage"
          type="error"
          variant="tonal"
          class="mb-4"
          border="start"
          prominent
        >
          {{ errorMessage }}
        </VAlert>
        <VCard
          flat
          class="form-card"
        >
          <VCardTitle class="text-h5 mb-4">
            Formulir Pra Yudisium
          </VCardTitle>
          
          <VCardText>
            <!-- Section 1: Upload Foto 3x4 -->
            <div class="text-subtitle-1 font-weight-bold mb-2">
              Upload Foto Berukuran 3×4 dan Berlatar Biru
            </div>
            <div class="file-upload ">
              <VIcon class="file-upload-icon">
                ri-attachment-2
              </VIcon>
              <VFileInput
                v-model="form.photo3x4"
                accept="image/*"
                variant="outlined"
                hide-details
                placeholder="Upload Foto 3x4 berlatar biru"
                prepend-icon=""
                class="file-upload-input"
                :disabled="!canEditFoto || isLoading"
                @change="handleFileChange('photo3x4', $event)"
              />
            </div>
            <div class="text-caption grey--text">
              {{ fileStatus.photo3x4 || 'No selected file' }}
            </div>
            <div class="text-caption grey--text">
              Catatan: JPG/JPEG/PNG, maksimal 2 MB.
            </div>
            
            <!-- Section 4: Upload Foto Ijazah SMA -->
            <div class="text-subtitle-1 font-weight-bold mt-6">
              Upload Ijazah SMA
            </div>
            <div class="file-upload">
              <VIcon class="file-upload-icon">
                ri-attachment-2
              </VIcon>
              <VFileInput
                v-model="form.photoSma"
                accept="image/*,.pdf"
                variant="outlined"
                hide-details
                placeholder="Upload Ijazah Terakhir"
                prepend-icon=""
                class="file-upload-input"
                :disabled="!canEditIjazah || isLoading"
                @change="handleFileChange('photoSma', $event)"
              />
            </div>
            <div class="text-caption grey--text">
              {{ fileStatus.photoSma || 'No selected file' }}
            </div>
            <div class="text-caption grey--text">
              Catatan: JPG/JPEG/PNG/PDF, maksimal 4 MB.
            </div>
            
            <!-- Section 5: Upload Foto KTP -->
            <div class="text-subtitle-1 font-weight-bold mt-6">
              Upload Foto KTP
            </div>
            <div class="file-upload">
              <VIcon class="file-upload-icon">
                ri-attachment-2
              </VIcon>
              <VFileInput
                v-model="form.photoCtp"
                accept="image/*"
                variant="outlined"
                hide-details
                placeholder="Upload Foto KTP"
                prepend-icon=""
                class="file-upload-input"
                :disabled="!canEditKtp || isLoading"
                @change="handleFileChange('photoCtp', $event)"
              />
            </div>
            <div class="text-caption grey--text">
              {{ fileStatus.photoCtp || 'No selected file' }}
            </div>
            <div class="text-caption grey--text">
              Catatan: JPG/JPEG/PNG, maksimal 2 MB.
            </div>
          </VCardText>

          <!-- Submit Button -->
          <VCardActions>
            <VSpacer />
            <VBtn
              block
              type="submit"
              color="primary"
              :loading="isLoading"
              :disabled="isLoading || !canSubmit"
              style="border-radius: 10px; font-size: 1.1rem; font-weight: 500; min-block-size: 48px;"
              @click.prevent="submitForm"
            >
              <span v-if="!isLoading">Submit</span>
              <template #loader>
                <span>Mengirim...</span>
              </template>
            </VBtn>
          </VCardActions>
        </VCard>
      </VContainer>
    </VMain>

    <!-- Validation Modal -->
    <VDialog
      v-model="showValidationModal"
      max-width="440"
    >
      <VCard class="confirm-card">
        <VCardTitle
          class="confirm-title"
          :class="{ 'confirm-title-error': !validationMessage.includes('berhasil') }"
        >
          {{ validationMessage.includes('berhasil') ? 'Berhasil' : 'Gagal' }}
        </VCardTitle>
        <VCardText class="confirm-body">
          <div class="confirm-text">
            {{ validationMessage }}
          </div>
        </VCardText>
        <VCardActions class="confirm-actions">
          <VBtn
            variant="tonal"
            color="grey"
            class="confirm-btn"
            @click="showValidationModal = false"
          >
            OK
          </VBtn>
        </VCardActions>
      </VCard>
    </VDialog>
  </VApp>
</template>

<script>
import axios from 'axios'
import { computed, onBeforeUnmount, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'

// Notifikasi menggunakan Vuetify

export default {
  name: 'PraYudisium',
  setup() {
    const form = ref({
      photo3x4: null,
      photoSma: null,
      photoCtp: null,
    })

    const fileStatus = ref({
      photo3x4: '',
      photoSma: '',
      photoCtp: '',
    })

    const showSuccess = ref(false)
    const errorMessage = ref('')
    const isLoading = ref(false)
    const showValidationModal = ref(false)
    const validationMessage = ref('')
    const route = useRoute()
    const infoMessage = ref('')
    const infoTimer = ref(null)
    const existingRecord = ref(null)
    const userNim = ref('')

    const fileFieldLabels = {
      'berkas ijazah terakhir': 'Berkas ijazah terakhir',
      'berkas foto ijazah': 'Foto 3x4',
      'berkas kk ktp': 'Foto KTP',
    }

    const formatFileSizeMb = kilobytes => {
      const mb = Math.round((kilobytes / 1024) * 10) / 10
      return Number.isInteger(mb) ? `${mb}` : `${mb}`.replace('.', ',')
    }

    const localizeValidationMessage = message => {
      if (!message) return message

      const sizeMatch = message.match(/The (.+) field must not be greater than (\\d+) kilobytes\\./i)
      if (sizeMatch) {
        const rawField = (sizeMatch[1] || '').toLowerCase()
        const label = fileFieldLabels[rawField] || sizeMatch[1]
        const sizeMb = formatFileSizeMb(Number(sizeMatch[2]))

        return `Ukuran ${label} maksimal ${sizeMb} MB.`
      }

      return message
    }

    const canSubmit = computed(() => {
      if (!existingRecord.value) return true

      const statuses = [
        existingRecord.value.status_foto,
        existingRecord.value.status_ijazah,
        existingRecord.value.status_ktp,
      ]

      if (statuses.some(status => status === 'revision'))
        return true

      const allEmpty = statuses.every(status => !status)
      if (allEmpty && existingRecord.value.comment)
        return true

      return false
    })

    const canEditFoto = computed(() => !existingRecord.value || existingRecord.value.status_foto === 'revision')
    const canEditIjazah = computed(() => !existingRecord.value || existingRecord.value.status_ijazah === 'revision')
    const canEditKtp = computed(() => !existingRecord.value || existingRecord.value.status_ktp === 'revision')

    // Handle file change
    const handleFileChange = (field, event) => {
      form.value[field] = event.target.files[0]
      updateFileStatus(field, event.target.files[0])
    }

    const updateFileStatus = (field, file) => {
      if (file) {
        fileStatus.value[field] = `${file.name} (${formatFileSize(file.size)})`
      } else {
        fileStatus.value[field] = ''
      }
    }

    const formatFileSize = bytes => {
      if (bytes === 0) return '0 Bytes'
      const k = 1024
      const sizes = ['Bytes', 'KB', 'MB', 'GB']
      const i = Math.floor(Math.log(bytes) / Math.log(k))
      
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
    }

    const loadUserIdentity = async () => {
      try {
        const stored = JSON.parse(sessionStorage.getItem('user_data') || '{}')
        if (stored?.nim || stored?.username) {
          userNim.value = stored.nim || stored.username
          
          return
        }
      } catch (error) {
        console.error('Error reading user data:', error)
      }

      try {
        const headers = { Accept: 'application/json' }
        const token = sessionStorage.getItem('jwt_token')
        if (token)
          headers.Authorization = `Bearer ${token}`

        const res = await fetch('/api/me', { headers })
        if (!res.ok) return
        const json = await res.json()
        const data = json.data ?? json

        userNim.value = data.nim || data.username || ''
      } catch (error) {
        console.error('Error fetching user identity:', error)
      }
    }

    const loadCurrentSubmission = async () => {
      try {
        const headers = { Accept: 'application/json' }
        const token = sessionStorage.getItem('jwt_token')
        if (token)
          headers.Authorization = `Bearer ${token}`

        const res = await fetch('/api/pra-yudisium', { headers })
        if (!res.ok) return

        const json = await res.json()
        const data = Array.isArray(json.data) ? json.data : []
        const match = data.find(item => item.nim === userNim.value || String(item.kdmahasiswa) === String(userNim.value))
        if (match) {
          existingRecord.value = {
            ...match,
            status_foto: match.status_foto || 'submitted',
            status_ijazah: match.status_ijazah || 'submitted',
            status_ktp: match.status_ktp || 'submitted',
          }
          if (!route.query.comment) {
            const hasRevision = [existingRecord.value.status_foto, existingRecord.value.status_ijazah, existingRecord.value.status_ktp]
              .some(status => status === 'revision')

            if (hasRevision) {
              infoMessage.value = 'Ada dokumen pra yudisium yang perlu diperbaiki.'
            } else if (existingRecord.value.is_validate) {
              infoMessage.value = 'Pengajuan pra yudisium Anda sudah disetujui.'
            } else {
              infoMessage.value = 'Pengajuan pra yudisium Anda sedang diproses.'
            }
          }
        }
      } catch (error) {
        console.error('Error fetching pra yudisium:', error)
      }
    }

    const submitForm = async () => {
      // Reset pesan error sebelumnya
      errorMessage.value = ''

      if (!canSubmit.value) {
        errorMessage.value = 'Pengajuan Anda sedang diproses dan belum bisa diubah'
        showValidationModal.value = true
        validationMessage.value = errorMessage.value
        
        return
      }
      
      const isResubmission = !!existingRecord.value?.kdprayudisium

      // Validasi form
      if (!isResubmission) {
        if (!form.value.photo3x4) {
          errorMessage.value = 'Mohon unggah foto 3x4'
          
          return
        }

        if (!form.value.photoSma) {
          errorMessage.value = 'Mohon unggah ijazah terakhir'
          
          return
        }

        if (!form.value.photoCtp) {
          errorMessage.value = 'Mohon unggah foto KTP'
          
          return
        }
      } else if (!form.value.photo3x4 && !form.value.photoSma && !form.value.photoCtp) {
        errorMessage.value = 'Mohon unggah minimal satu berkas untuk revisi'
        
        return
      }

      // Membuat FormData untuk mengirim file
      const formData = new FormData()

      // Validasi tipe file sebelum mengirim
      const validImageTypes = ['image/jpeg', 'image/png', 'image/jpg']
      const validPdfType = 'application/pdf'

      if (form.value.photo3x4) {
        if (!validImageTypes.includes(form.value.photo3x4.type)) {
          errorMessage.value = 'Foto 3x4 harus dalam format JPEG, JPG, atau PNG'
          
          return
        }
        formData.append('berkas_foto_ijazah', form.value.photo3x4)
      }

      if (form.value.photoSma) {
        if (!validImageTypes.includes(form.value.photoSma.type) && form.value.photoSma.type !== validPdfType) {
          errorMessage.value = 'Ijazah SMA harus dalam format JPEG, JPG, PNG, atau PDF'
          
          return
        }
        formData.append('berkas_ijazah_terakhir', form.value.photoSma)
      }

      if (form.value.photoCtp) {
        if (!validImageTypes.includes(form.value.photoCtp.type)) {
          errorMessage.value = 'Foto KTP harus dalam format JPEG, JPG, atau PNG'
          
          return
        }
        formData.append('berkas_kk_ktp', form.value.photoCtp)
      }

      try {
        isLoading.value = true
        errorMessage.value = ''
        
        const token = sessionStorage.getItem('jwt_token')

        const endpoint = existingRecord.value?.kdprayudisium
          ? `/api/pra-yudisium/${existingRecord.value.kdprayudisium}`
          : '/api/pra-yudisium'

        const isUpdate = !!existingRecord.value?.kdprayudisium
        const method = isUpdate ? 'post' : 'post'

        if (isUpdate)
          formData.append('_method', 'PATCH')

        // Menggunakan endpoint yang sesuai dengan route yang didefinisikan di Laravel
        const response = await axios({
          url: endpoint,
          method,
          data: formData,
          headers: {
            'Content-Type': 'multipart/form-data',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
            'X-Requested-With': 'XMLHttpRequest',
            ...(token ? { Authorization: `Bearer ${token}` } : {}),
          },
          withCredentials: true,
        })

        if (response.data.success) {
          showSuccess.value = true
          
          // Reset form
          form.value = {
            photo3x4: null,
            photoSma: null,
            photoCtp: null,
          }
          
          // Tampilkan notifikasi sukses
          showValidationModal.value = true
          validationMessage.value = 'Data berhasil disimpan!'
          
          // Reset file inputs
          const fileInputs = document.querySelectorAll('input[type="file"]')

          fileInputs.forEach(input => {
            input.value = ''
          })
          
          // Reset file status
          Object.keys(fileStatus.value).forEach(key => {
            fileStatus.value[key] = ''
          })
          
          existingRecord.value = response.data.data ?? existingRecord.value
          if (existingRecord.value)
            existingRecord.value.status = existingRecord.value.status || 'submitted'
        }
      } catch (error) {
        console.error('Error:', error)
        
        let errorMsg = 'Terjadi kesalahan saat mengirim data'
        
        if (error.response) {
          // The request was made and the server responded with a status code
          // that falls out of the range of 2xx
          if (error.response.data.errors) {
            // Jika ada error validasi dari Laravel
            errorMsg = Object.values(error.response.data.errors)
              .flat()
              .map(localizeValidationMessage)
              .join(' ')
          } else if (error.response.data.message) {
            errorMsg = error.response.data.message
          }
        } else if (error.request) {
          // The request was made but no response was received
          errorMsg = 'Tidak ada respon dari server. Silakan coba lagi nanti.'
        }
        
        errorMessage.value = errorMsg
        showValidationModal.value = true
        validationMessage.value = errorMsg
      } finally {
        isLoading.value = false
      }
    }

    onMounted(() => {
      if (route.query.comment) {
        infoMessage.value = route.query.comment
        infoTimer.value = setTimeout(() => {
          infoMessage.value = ''
        }, 30000)
      }

      loadUserIdentity().then(loadCurrentSubmission)
    })

    onBeforeUnmount(() => {
      if (infoTimer.value) {
        clearTimeout(infoTimer.value)
        infoTimer.value = null
      }
    })

    return {
      form,
      fileStatus,
      updateFileStatus,
      handleFileChange,
      submitForm,
      canSubmit,
      canEditFoto,
      canEditIjazah,
      canEditKtp,
      showSuccess,
      errorMessage,
      isLoading,
      showValidationModal,
      validationMessage,
      infoMessage,
      existingRecord,
    }
  },
}
</script>

<style scoped>
.confirm-card {
  border-radius: 18px;
  padding-block: 8px 4px;
  padding-inline: 8px;
}

.confirm-title {
  color: #17a2a6;
  font-size: 1.35rem;
  font-weight: 700;
  padding-block: 12px 4px;
  text-align: center;
}

.confirm-title-error {
  color: #e63946;
}

.confirm-body {
  display: flex;
  flex-direction: column;
  gap: 8px;
  padding-block: 6px 14px;
  text-align: center;
}

.confirm-text {
  color: rgba(var(--v-theme-on-surface), 0.8);
  font-size: 1rem;
  font-weight: 600;
}

.confirm-subtext {
  color: rgba(var(--v-theme-on-surface), 0.6);
  font-size: 0.95rem;
}

.confirm-actions {
  display: flex;
  justify-content: center;
  gap: 12px;
  padding-block-end: 16px;
}

.confirm-btn {
  border-radius: 8px;
  font-weight: 600;
  min-inline-size: 120px;
  text-transform: none;
}

.confirm-btn-primary {
  color: #fff !important;
}
.form-card {
  margin: 0;
  max-inline-size: 100%;
}

.upload-field {
  position: relative;
}

.custom-upload-box {
  position: relative;
  display: flex;
  align-items: stretch;
  border: 3px dashed #17a2a6 !important;
  border-radius: 10px !important;
  background: #fff;
  box-shadow: none;
  margin-block-end: 16px;
  min-block-size: 140px;
}

.upload-center-content {
  position: absolute;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  block-size: 100%;
  inline-size: 100%;
  inset: 0;
  pointer-events: none;
}

.upload-label {
  color: #17a2a6;
  font-size: 1.05rem;
  font-weight: 500;
  margin-block-start: 8px;
}

.upload-field .v-input__slot {
  min-block-size: unset !important;
}

.upload-field .v-label {
  display: none;
}

.upload-field .v-input__control {
  block-size: 100%;
}

.upload-field .v-input__prepend-inner {
  display: none;
}

/* File list style */
.v-file-input__text {
  display: flex;
  align-items: center;
  border-radius: 0 0 10px 10px;
  background: rgb(var(--v-theme-surface)) !important;
  color: rgb(var(--v-theme-on-surface));
  font-size: 1rem;
  padding-block: 8px;
  padding-inline: 12px;
}

.v-file-input__text .v-icon {
  color: rgb(var(--v-theme-primary)) !important;
  margin-inline-end: 8px;
}

.v-file-input__text .v-btn {
  color: rgb(var(--v-theme-on-surface)) !important;
  margin-inline-start: 8px;
}

/* Responsive file upload style (theme aware, not OS aware) */
.file-upload {
  display: flex;
  align-items: center;
  padding: 12px;
  border-radius: 8px;
  background: rgb(var(--v-theme-surface));
  transition: background 0.2s;
}

.file-upload-icon {
  color: rgb(var(--v-theme-primary));
  font-size: 22px;
  margin-inline-end: 10px;
  transition: color 0.2s;
}

.file-upload-input {
  flex: 1;
  background: transparent !important;
  color: rgb(var(--v-theme-on-surface)) !important;

  --v-field-border-color: rgb(var(--v-theme-primary));
  --v-field-label-color: rgb(var(--v-theme-on-surface));
  --v-field-placeholder-color: rgb(var(--v-theme-on-surface));
  --v-field-bg-color: rgb(var(--v-theme-surface));
}
</style> 
