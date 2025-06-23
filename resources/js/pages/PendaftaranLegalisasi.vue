<template>
  <VApp>
    <VMain>
      <VContainer
        fluid
        class="pa-6"
      >
        <!-- Stepper Wizard -->
        <div class="wizard-steps mb-8 d-flex justify-center align-center">
          <div
            class="wizard-step"
            :class="[{active: currentStep === 1, done: currentStep > 1}]"
          >
            <span class="wizard-circle">1</span>
            <span class="wizard-label">Pendaftaran Legalisasi</span>
          </div>
          <div class="wizard-line" />
          <div
            class="wizard-step"
            :class="[{active: currentStep === 2, done: currentStep > 2}]"
          >
            <span class="wizard-circle">2</span>
            <span class="wizard-label">Detail Data</span>
          </div>
          <div class="wizard-line" />
          <div
            class="wizard-step"
            :class="[{active: currentStep === 3}]"
          >
            <span class="wizard-circle">3</span>
            <span class="wizard-label">Pembayaran</span>
          </div>
        </div>
        <VCard
          flat
          class="form-card"
        >
          <VCardTitle class="text-h5 mb-4">
            Formulir Pendaftaran Legalisasi
          </VCardTitle>
          <VCardText>
            <!-- Step 1: Pendaftaran Legalisasi -->
            <div v-if="currentStep === 1">
              <div class="text-subtitle-1 font-weight-bold mb-2">
                Jumlah (Jml)
              </div>
              <VTextField
                v-model="form.jml"
                label="Jumlah"
                outlined
                dense
                hide-details
                class="mb-4"
              />
              <div class="text-subtitle-1 font-weight-bold mb-2">
                Alamat
              </div>
              <VTextField
                v-model="form.alamat"
                label="Alamat"
                outlined
                dense
                hide-details
                class="mb-4"
              />
              <div class="text-subtitle-1 font-weight-bold mb-2">
                Nama Penerima
              </div>
              <VTextField
                v-model="form.namaPenerima"
                label="Nama Penerima"
                outlined
                dense
                hide-details
                class="mb-4"
              />
              <div class="text-subtitle-1 font-weight-bold mb-2">
                No Telp Penerima
              </div>
              <VTextField
                v-model="form.noTelpPenerima"
                label="No Telp Penerima"
                outlined
                dense
                hide-details
                class="mb-4"
              />
            </div>
            <!-- Step 2: Detail Data (Summary Table) -->
            <div v-else-if="currentStep === 2">
              <div class="text-subtitle-1 font-weight-bold mb-4">
                Ringkasan Data Pendaftaran
              </div>
              <table class="summary-table styled-summary-table">
                <tr>
                  <td class="label">
                    Jumlah
                  </td>
                  <td class="value">
                    {{ form.jml || '-' }}
                  </td>
                </tr>
                <tr>
                  <td class="label">
                    Alamat
                  </td>
                  <td class="value">
                    {{ form.alamat || '-' }}
                  </td>
                </tr>
                <tr>
                  <td class="label">
                    Nama Penerima
                  </td>
                  <td class="value">
                    {{ form.namaPenerima || '-' }}
                  </td>
                </tr>
                <tr>
                  <td class="label">
                    No Telp Penerima
                  </td>
                  <td class="value">
                    {{ form.noTelpPenerima || '-' }}
                  </td>
                </tr>
              </table>
            </div>
            <!-- Step 3: Pembayaran (Dummy) -->
            <div v-else-if="currentStep === 3">
              <div class="text-subtitle-1 font-weight-bold mb-2">
                Pembayaran Legalisasi
              </div>
              <div class="mb-4">
                Silakan lakukan pembayaran ke rekening berikut:<br>
                <b>Bank UNISA</b><br>
                No. Rekening: <b>1234567890</b><br>
                Atas Nama: <b>Universitas Aisyiyah</b>
              </div>
              <VTextField
                v-model="form.buktiPembayaran"
                label="No. Referensi Pembayaran"
                outlined
                dense
                hide-details
                class="mb-4"
              />
              <div class="file-upload mb-4">
                <VIcon class="file-upload-icon">
                  ri-attachment-2
                </VIcon>
                <VFileInput
                  v-model="form.fileBuktiPembayaran"
                  accept="image/*,application/pdf"
                  variant="outlined"
                  hide-details
                  placeholder="Upload Bukti Pembayaran"
                  prepend-icon=""
                  class="file-upload-input"
                />
              </div>
              <div class="text-caption grey--text">
                {{ fileStatus.fileBuktiPembayaran || 'No selected file' }}
              </div>
            </div>
          </VCardText>
          <!-- Wizard Navigation Buttons -->
          <VCardActions>
            <VBtn
              :disabled="currentStep === 1"
              color="grey"
              style="border-radius: 10px; min-block-size: 48px; min-inline-size: 120px;"
              @click="prevStep"
            >
              Kembali
            </VBtn>
            <VSpacer />
            <VBtn
              v-if="currentStep < 3"
              color="#17a2a6"
              style="border-radius: 10px; background: rgb(var(--v-theme-primary)); color: #fff; font-size: 1.1rem; font-weight: 500; min-block-size: 48px; min-inline-size: 120px;"
              @click="nextStep"
            >
              Lanjut
            </VBtn>
            <VBtn
              v-else
              color="#17a2a6"
              style="border-radius: 10px; background: rgb(var(--v-theme-primary)); color: #fff; font-size: 1.1rem; font-weight: 500; min-block-size: 48px; min-inline-size: 120px;"
              @click="handleSubmit"
            >
              Kirim
            </VBtn>
          </VCardActions>
        </VCard>
      </VContainer>
    </VMain>
  </VApp>
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
</template>

<script>
import { ref } from 'vue'

export default {
  name: 'PendaftaranLegalisasi',
  setup() {
    const currentStep = ref(1)

    const form = ref({
      // Step 1
      jml: '',
      alamat: '',
      namaPenerima: '',
      noTelpPenerima: '',

      // Step 2
      nama: '',
      prodi: '',
      tahunLulus: '',

      // Step 3
      buktiPembayaran: '',
      fileBuktiPembayaran: null,
    })

    const fileStatus = ref({
      fileBuktiPembayaran: '',
    })

    const showValidationModal = ref(false)
    const validationMessage = ref('')

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

    // Wizard navigation
    const nextStep = () => {
      if (currentStep.value < 3) currentStep.value++
    }

    const prevStep = () => {
      if (currentStep.value > 1) currentStep.value--
    }

    const handleSubmit = () => {
      // Validasi contoh, sesuaikan dengan kebutuhan

      if (!form.value.jml || !form.value.alamat || !form.value.namaPenerima || !form.value.noTelpPenerima) {
        validationMessage.value = 'Mohon lengkapi semua data pendaftaran legalisasi'
        showValidationModal.value = true

        return
      }

      // Validasi step 3

      if (!form.value.buktiPembayaran || !form.value.fileBuktiPembayaran) {
        validationMessage.value = 'Mohon lengkapi data pembayaran'
        showValidationModal.value = true

        return
      }
      validationMessage.value = 'Form berhasil dikirim'
      showValidationModal.value = true
    }

    return {
      currentStep,
      form,
      fileStatus,
      updateFileStatus,
      showValidationModal,
      validationMessage,
      handleSubmit,
      nextStep,
      prevStep,
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

/* File upload style (theme aware) */
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

.wizard-steps {
  gap: 0.5rem;
}

.wizard-step {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: center;
  min-inline-size: 120px;
}

.wizard-circle {
  display: flex;
  align-items: center;
  justify-content: center;
  border: 2px solid #e0e0e0;
  border-radius: 50%;
  background: #e0e0e0;
  block-size: 36px;
  color: #888;
  font-size: 1.2rem;
  font-weight: bold;
  inline-size: 36px;
  margin-block-end: 4px;
  transition: all 0.2s;
}

.wizard-step.active .wizard-circle {
  border-color: #17a2a6;
  background: #17a2a6;
  color: #fff;
}

.wizard-step.done .wizard-circle {
  border-color: #17a2a6;
  background: #17a2a6;
  color: #fff;
  opacity: 0.7;
}

.wizard-label {
  color: #888;
  font-size: 0.95rem;
  text-align: center;
}

.wizard-step.active .wizard-label {
  color: #17a2a6;
  font-weight: 600;
}

.wizard-step.done .wizard-label {
  color: #17a2a6;
  opacity: 0.7;
}

.wizard-line {
  flex: 1;
  align-self: center;
  background: #e0e0e0;
  block-size: 2px;
  margin-inline: 8px;
}

.wizard-step.done + .wizard-line {
  background: #17a2a6;
  opacity: 0.7;
}

.styled-summary-table {
  border-radius: 8px;
  background: #fff;
  border-collapse: separate;
  border-spacing: 0 8px;
  box-shadow: 0 1px 4px rgba(0, 0, 0, 3%);
  inline-size: 100%;
  margin-block-end: 24px;
}

.styled-summary-table tr {
  background: #fff;
  border-block-end: 1px solid #f0f0f0;
}

.styled-summary-table td {
  font-size: 1rem;
  padding-block: 14px;
  padding-inline: 18px;
  vertical-align: top;
}

.styled-summary-table .label {
  background: #fafbfc;
  border-inline-end: 1px solid #f0f0f0;
  color: #444;
  font-weight: 600;
  inline-size: 200px;
}

.styled-summary-table .value {
  background: #fff;
  color: #222;
  text-align: start;
}
</style> 
