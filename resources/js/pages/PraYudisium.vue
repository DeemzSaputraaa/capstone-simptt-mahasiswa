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
                @change="handleFileChange('photo3x4', $event)"
              />
            </div>
            <div class="text-caption grey--text">
              {{ fileStatus.photo3x4 || 'No selected file' }}
            </div>
            
            <!-- Section 4: Upload Foto Ijazah SMA -->
            <div class="text-subtitle-1 font-weight-bold mt-6">
              Upload Foto Ijazah SMA
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
                @change="handleFileChange('photoSma', $event)"
              />
            </div>
            <div class="text-caption grey--text">
              {{ fileStatus.photoSma || 'No selected file' }}
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
                @change="handleFileChange('photoCtp', $event)"
              />
            </div>
            <div class="text-caption grey--text">
              {{ fileStatus.photoCtp || 'No selected file' }}
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
              :disabled="isLoading"
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
      max-width="400"
    >
      <VCard>
        <VCardTitle class="text-h5">
          {{ validationMessage.includes('berhasil') ? 'Sukses' : 'Peringatan' }}
        </VCardTitle>
        <VCardText>
          {{ validationMessage }}
        </VCardText>
        <VCardActions>
          <VSpacer />
          <VBtn
            color="primary"
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
import { ref, onMounted } from 'vue'
import { useRouter, useRoute } from 'vue-router'

// Notifikasi menggunakan Vuetify

export default {
  name: 'PraYudisium',
  setup() {
    const form = ref({
      photo3x4: null,
      photoSma: null,
      photoCtp: null
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
    const router = useRouter()
    const route = useRoute()
    const infoMessage = ref('')

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

    const submitForm = async () => {
      // Reset pesan error sebelumnya
      errorMessage.value = ''
      
      // Validasi form
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

        // Menggunakan endpoint yang sesuai dengan route yang didefinisikan di Laravel
        const response = await axios.post('/api/pra-yudisium', formData, {
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
            photoCtp: null
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
          
          // Tampilkan pesan sukses
          showValidationModal.value = true
          validationMessage.value = 'Data berhasil disimpan!'
        }
      } catch (error) {
        console.error('Error:', error)
        
        let errorMsg = 'Terjadi kesalahan saat mengirim data'
        
        if (error.response) {
          // The request was made and the server responded with a status code
          // that falls out of the range of 2xx
          if (error.response.data.errors) {
            // Jika ada error validasi dari Laravel
            errorMsg = Object.values(error.response.data.errors).flat().join(' ')
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
        showValidationModal.value = true
        validationMessage.value = errorMsg
      } finally {
        isLoading.value = false
      }
    }

    onMounted(() => {
      if (route.query.comment) {
        infoMessage.value = route.query.comment
      }
    })

    return {
      form,
      fileStatus,
      updateFileStatus,
      handleFileChange,
      submitForm,
      showSuccess,
      errorMessage,
      isLoading,
      showValidationModal,
      validationMessage,
      infoMessage,
    }
  },
}
</script>

<style scoped>
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
