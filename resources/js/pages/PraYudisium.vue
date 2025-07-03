<template>
  <VApp>
    <VMain>
      <VContainer
        fluid
        class="pa-6"
      >
        <VAlert
          v-if="showNotifAlert"
          type="success"
          variant="tonal"
          class="mb-4"
          border="start"
          prominent
        >
          <span>{{ notifMessage }}</span>
        </VAlert>
        <VCard
          flat
          class="form-card"
        >
          <VCardTitle class="text-h5 mb-4">
            Formulir Pra Yudisium
          </VCardTitle>
          
          <!-- Section 1: NIK -->
          <VCardText>
            <div class="text-subtitle-1 font-weight-bold mb-2">
              Masukkan NIK
            </div>
            <VTextField
              v-model="form.nik"
              label="NIK"
              outlined
              dense
              hide-details
              class="mb-4"
            />
            
            <!-- Section 2: Nomor Telepon -->
            <div class="text-subtitle-1 font-weight-bold mb-2">
              Masukkan No Telepon
            </div>
            <VTextField
              v-model="form.phone"
              label="No Telepon"
              outlined
              dense
              hide-details
              class="mb-4"
            />
            
            <!-- Section 3: Upload Foto 3x4 -->
            <div class="text-subtitle-1 font-weight-bold mb-2">
              Upload Foto Berukuran 3×4 dan Berlatar Biru
            </div>
            <div class="file-upload mb-4">
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
              />
            </div>
            <div class="text-caption grey--text">
              {{ fileStatus.photo3x4 || 'No selected file' }}
            </div>
            
            <!-- Section 4: Upload Foto Ijazah SMA -->
            <div class="text-subtitle-1 font-weight-bold mb-2 mt-6">
              Upload Foto Ijazah SMA
            </div>
            <div class="file-upload mb-4 mt-6">
              <VIcon class="file-upload-icon">
                ri-attachment-2
              </VIcon>
              <VFileInput
                v-model="form.photoSma"
                accept="image/*"
                variant="outlined"
                hide-details
                placeholder="Upload Foto Ijazah SMA"
                prepend-icon=""
                class="file-upload-input"
              />
            </div>
            <div class="text-caption grey--text">
              {{ fileStatus.photoSma || 'No selected file' }}
            </div>
            
            <!-- Section 5: Upload Foto KTP -->
            <div class="text-subtitle-1 font-weight-bold mb-2 mt-6">
              Upload Foto KTP
            </div>
            <div class="file-upload mb-4 mt-6">
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
              />
            </div>
            <div class="text-caption grey--text">
              {{ fileStatus.photoCtp || 'No selected file' }}
            </div>
          </VCardText>

          <!-- Submit Button -->
          <VCardActions>
            <VSpacer />
            <!--
              <VBtn
              block
              type="submit"
              color="#17a2a6"
              style="border-radius: 10px; background: #17a2a6; color: #fff; font-size: 1.1rem; font-weight: 500; min-block-size: 48px;"
              @click="handleSubmit"
              >
              Submit
              </VBtn> 
            -->
            <VBtn
              block
              type="submit"
              color="#17a2a6"
              style="border-radius: 10px; background: rgb(var(--v-theme-primary)); color: #fff; font-size: 1.1rem; font-weight: 500; min-block-size: 48px;"
              @click="handleSubmit"
            >
              Submit
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
import { ref } from 'vue'
import { useRoute } from 'vue-router'

export default {
  name: 'PraYudisium',
  setup() {
    const form = ref({
      nik: '',
      phone: '',
      photo3x4: null,
      photoSma: null,
      photoCtp: null,
    })

    const fileStatus = ref({
      photo3x4: '',
      photoSma: '',
      photoCtp: '',
    })

    const showValidationModal = ref(false)
    const validationMessage = ref('')

    const route = useRoute()
    const notifId = route.query.notif
    const showNotifAlert = ref(false)
    const notifMessage = ref('')
    if (notifId) {
      showNotifAlert.value = true
      notifMessage.value = 'Ada notifikasi baru terkait data Pra Yudisium Anda. Silakan cek detail di bawah ini.'
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

    const handleSubmit = () => {
      // Validate form
      if (!form.value.nik || !form.value.phone) {
        validationMessage.value = 'Mohon lengkapi NIK dan nomor telepon'
        showValidationModal.value = true
        
        return
      }

      if (!form.value.photo3x4 || !form.value.photoSma || !form.value.photoCtp) {
        validationMessage.value = 'Mohon lengkapi semua file yang diperlukan'
        showValidationModal.value = true
        
        return
      }

      // TODO: Implement API call to submit form
      console.log('Form submitted:', form.value)
      validationMessage.value = 'Form berhasil dikirim'
      showValidationModal.value = true
    }

    return {
      form,
      fileStatus,
      updateFileStatus,
      handleSubmit,
      showValidationModal,
      validationMessage,
      showNotifAlert,
      notifMessage,
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
