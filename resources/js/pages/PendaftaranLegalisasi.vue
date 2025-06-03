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
            Formulir Pendaftaran Legalisasi
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
            
            <!-- Section 3: Upload Dokumen -->
            <div class="text-subtitle-1 font-weight-bold mb-2">
              Upload Dokumen yang Akan Dilegalisasi
            </div>
            <VFileInput
              v-model="form.document"
              accept=".pdf,.doc,.docx"
              outlined
              dense
              hide-details
              prepend-icon=""
              prepend-inner-icon="ri-upload-cloud-2-line"
              label="Browse Files to upload"
              class="upload-field mb-2"
              @change="updateFileStatus('document', $event)"
            />
            <div class="text-caption grey--text">
              {{ fileStatus.document || 'No selected file' }}
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
              prepend-icon=""
              prepend-inner-icon="ri-upload-cloud-2-line"
              label="Browse Files to upload"
              class="upload-field mb-2"
              @change="updateFileStatus('photoCtp', $event)"
            />
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
  name: 'PendaftaranLegalisasi',
  setup() {
    const form = ref({
      nik: '',
      phone: '',
      document: null,
      photoCtp: null,
    })

    const fileStatus = ref({
      document: '',
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

.upload-field .v-input__prepend-inner {
  position: absolute;
  z-index: 1;
  inset-block-start: 50%;
  inset-inline-start: 50%;
  transform: translate(-50%, -50%);
}

.upload-field .v-input__slot {
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
  min-block-size: 120px !important;
}

.upload-field .v-label {
  position: absolute;
  inset-block-end: 20px;
  inset-inline: 0;
  margin-block-start: 60px;
  text-align: center;
}

.upload-field .v-input__control {
  block-size: 100%;
}
</style> 
