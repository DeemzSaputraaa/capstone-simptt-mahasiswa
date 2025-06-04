<template>
  <VApp>
    <VMain>
      <VContainer
        fluid
        class="pa-6"
      >
        <VCard
          flat
          class="form-card"
        >
          <VCardTitle class="text-h5 mb-4">
            Formulir Validasi Ijazah
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
            
            <!-- Section 3: Upload Foto Ijazah -->
            <div class="text-subtitle-1 font-weight-bold mb-2">
              Upload Foto Ijazah
            </div>
            <VFileInput
              v-model="form.photoIjazah"
              accept="image/*"
              outlined
              dense
              hide-details
              class="upload-field mb-2 custom-upload-box"
              prepend-icon=""
              :show-size="false"
            >
              <template #default>
                <div class="upload-center-content">
                  <VIcon
                    size="40"
                    color="success"
                  >
                    ri-upload-cloud-2-line
                  </VIcon>
                  <div class="upload-label">
                    Browse Files to upload
                  </div>
                </div>
              </template>
            </VFileInput>
            <div class="text-caption grey--text">
              {{ fileStatus.photoIjazah || 'No selected file' }}
            </div>
            
            <!-- Section 4: Upload Foto KTP -->
            <div class="text-subtitle-1 font-weight-bold mb-2 mt-6">
              Upload Foto KTP
            </div>
            <VFileInput
              v-model="form.photoCtp"
              accept="image/*"
              outlined
              dense
              hide-details
              class="upload-field mb-2 custom-upload-box"
              prepend-icon=""
              :show-size="false"
            >
              <template #default>
                <div class="upload-center-content">
                  <VIcon
                    size="40"
                    color="success"
                  >
                    ri-upload-cloud-2-line
                  </VIcon>
                  <div class="upload-label">
                    Browse Files to upload
                  </div>
                </div>
              </template>
            </VFileInput>
            <div class="text-caption grey--text">
              {{ fileStatus.photoCtp || 'No selected file' }}
            </div>
          </VCardText>
        </VCard>
      </VContainer>
    </VMain>
  </VApp>
</template>

<script>
import { ref } from 'vue'

export default {
  name: 'ValidasiIjazah',
  setup() {
    const form = ref({
      nik: '',
      phone: '',
      photoIjazah: null,
      photoCtp: null,
    })

    const fileStatus = ref({
      photoIjazah: '',
      photoCtp: '',
    })

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

    return {
      form,
      fileStatus,
      updateFileStatus,
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
  border: 2px dashed #2196f3 !important;
  border-radius: 12px !important;
  background: #fff;
  box-shadow: 0 2px 8px 0 rgba(33, 150, 243, 5%);
  margin-block-end: 16px;
  min-block-size: 120px;
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
  color: #2196f3;
  font-size: 1rem;
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
</style> 
