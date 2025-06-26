<template>
  <VApp>
    <VMain>
      <VContainer
        fluid
        class="pa-6"
      >
        <div v-if="!showWizard">
          <VBtn
            color="#17a2a6"
            style="border-radius: 10px; background: rgb(var(--v-theme-primary)); color: #fff; font-size: 1.1rem; font-weight: 500; margin-block-end: 32px; min-block-size: 48px; min-inline-size: 220px;"
            @click="showWizard = true"
          >
            Pengajuan Legalisasi
          </VBtn>
          <div class="d-flex flex-row align-start">
            <!-- Sidebar tanggal pengajuan -->
            <div class="wizard-date-sidebar">
              <div
                v-for="(group, year) in groupedPengajuan"
                :key="year"
                class="mb-4"
              >
                <div class="wizard-date-year">
                  {{ year }}
                </div>
                <div
                  v-for="item in group"
                  :key="item.id"
                  class="wizard-date-item"
                >
                  <button
                    class="wizard-date-btn"
                    :class="{active: selectedPengajuan && selectedPengajuan.id === item.id}"
                    @click="selectPengajuan(item)"
                  >
                    {{ item.tanggal.split('-').slice(1).join('/') }}
                  </button>
                </div>
              </div>
            </div>
            <!-- Vertical wizard detail -->
            <div class="wizard-vertical-detail">
              <div v-if="selectedPengajuan">
                <!-- Step 1: Tagihan -->
                <div class="wizard-vertical-step">
                  <span
                    class="wizard-vertical-dot"
                    :class="tagihanColor(selectedPengajuan)"
                  />
                  <div class="wizard-vertical-line" />
                  <div class="wizard-vertical-content">
                    <div class="wizard-vertical-title">
                      Tagihan
                    </div>
                    <div class="wizard-vertical-desc">
                      <template v-if="selectedPengajuan.status === 'pending'">
                        Menunggu Tagihan
                      </template>
                      <template v-else>
                        Rp. {{ selectedPengajuan.tagihan }}
                      </template>
                    </div>
                  </div>
                </div>
                <!-- Step 2: Cara Pembayaran -->
                <div class="wizard-vertical-step">
                  <span
                    class="wizard-vertical-dot"
                    :class="caraBayarColor(selectedPengajuan)"
                  />
                  <div class="wizard-vertical-line" />
                  <div class="wizard-vertical-content">
                    <div class="wizard-vertical-title">
                      Cara Pembayaran
                    </div>
                    <div class="wizard-vertical-desc">
                      <template v-if="selectedPengajuan.status === 'pending'">
                        -
                      </template>
                      <template v-else>
                        Cara Melakukan Pembayaran
                        <div>
                          <button
                            class="wizard-bayar-btn"
                            @click="openCaraPembayaran(selectedPengajuan)"
                          >
                            CARA MELAKUKAN PEMBAYARAN
                          </button>
                        </div>
                      </template>
                    </div>
                  </div>
                </div>
                <!-- Step 3: Status -->
                <div class="wizard-vertical-step">
                  <span
                    class="wizard-vertical-dot"
                    :class="statusColor(selectedPengajuan)"
                  />
                  <div class="wizard-vertical-content">
                    <div class="wizard-vertical-title">
                      Status
                    </div>
                    <div class="wizard-vertical-desc">
                      <template v-if="selectedPengajuan.status === 'pending'">
                        Pending
                      </template>
                      <template v-else-if="selectedPengajuan.status === 'proses'">
                        Sedang di proses
                      </template>
                      <template v-else>
                        Selesai
                      </template>
                    </div>
                  </div>
                </div>
              </div>
              <div
                v-else
                class="wizard-vertical-empty"
              >
                Pilih tanggal pengajuan untuk melihat detail.
              </div>
            </div>
          </div>
        </div>
        <div v-else>
          <!-- Stepper Wizard -->
          <div class="wizard-steps mb-8 d-flex justify-center align-center">
            <div
              class="wizard-step"
              :class="[{active: currentStep === 1, done: currentStep > 1}]"
            >
              <span class="wizard-circle">1</span>
              <span class="wizard-label">Pengajuan Legalisasi</span>
            </div>
            <div class="wizard-line" />
            <div
              class="wizard-step"
              :class="[{active: currentStep === 2}]"
            >
              <span class="wizard-circle">2</span>
              <span class="wizard-label">Detail Data</span>
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
              <!-- Step 1: Pengajuan Legalisasi -->
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
            </VCardText>
            <!-- Wizard Navigation Buttons -->
            <VCardActions>
              <VBtn
                color="grey"
                style="border-radius: 10px; min-block-size: 48px; min-inline-size: 120px;"
                :disabled="false"
                @click="handleBack"
              >
                Kembali
              </VBtn>
              <VSpacer />
              <VBtn
                v-if="currentStep < 2"
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
        </div>
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
import { computed, ref } from 'vue'

export default {
  name: 'PendaftaranLegalisasi',
  setup() {
    const showWizard = ref(false)
    const currentStep = ref(1)

    // Dummy data pengajuan legalisasi
    const daftarPengajuan = ref([
      { id: 1, tanggal: '2025-06-05', status: 'pending', tagihan: null },
      { id: 2, tanggal: '2025-06-01', status: 'proses', tagihan: '20.000,00' },
      { id: 3, tanggal: '2025-05-26', status: 'selesai', tagihan: '30.000,00' },
      { id: 4, tanggal: '2024-11-27', status: 'pending', tagihan: null },
      { id: 5, tanggal: '2024-09-19', status: 'pending', tagihan: null },
      { id: 6, tanggal: '2024-08-26', status: 'pending', tagihan: null },
    ])

    // Group pengajuan by year
    const groupedPengajuan = computed(() => {
      const groups = {}

      daftarPengajuan.value.forEach(item => {
        const year = item.tanggal.split('-')[0]
        if (!groups[year]) groups[year] = []
        groups[year].push(item)
      })

      // Urutkan tahun dari terbesar ke terkecil
      const sortedYears = Object.keys(groups).sort((a, b) => parseInt(b) - parseInt(a))
      const sortedGroups = {}

      sortedYears.forEach(year => {
        // Urutkan tanggal dalam tahun dari terbaru ke terlama
        groups[year].sort((a, b) => new Date(b.tanggal) - new Date(a.tanggal))
        sortedGroups[year] = groups[year]
      })
      
      return sortedGroups
    })

    // State pengajuan yang dipilih
    const selectedPengajuan = ref(daftarPengajuan.value[0])

    const selectPengajuan = pengajuan => {
      selectedPengajuan.value = pengajuan
    }

    const form = ref({
      // Step 1
      jml: '',
      alamat: '',
      namaPenerima: '',
      noTelpPenerima: '',
    })

    const fileStatus = ref({})

    const showValidationModal = ref(false)
    const validationMessage = ref('')

    // Wizard navigation
    const nextStep = () => {
      if (currentStep.value < 2) currentStep.value++
    }

    const prevStep = () => {
      if (currentStep.value > 1) currentStep.value--
    }

    // Handler tombol kembali
    const handleBack = () => {
      if (currentStep.value === 1) {
        showWizard.value = false
        currentStep.value = 1
      } else {
        prevStep()
      }
    }

    const handleSubmit = () => {
      // Validasi contoh, sesuaikan dengan kebutuhan
      if (!form.value.jml || !form.value.alamat || !form.value.namaPenerima || !form.value.noTelpPenerima) {
        validationMessage.value = 'Mohon lengkapi semua data pendaftaran legalisasi'
        showValidationModal.value = true
        
        return
      }
      validationMessage.value = 'Form berhasil dikirim'
      showValidationModal.value = true
    }

    // Fungsi untuk membuka cara pembayaran di tab baru
    const openCaraPembayaran = item => {
      window.open('/cara-pembayaran', '_blank')
    }

    // Warna status step
    const tagihanColor = pengajuan => pengajuan.status === 'pending' ? 'dot-yellow' : 'dot-green'
    const caraBayarColor = pengajuan => pengajuan.status === 'pending' ? 'dot-grey' : 'dot-blue'

    const statusColor = pengajuan => {
      if (pengajuan.status === 'pending') return 'dot-grey'
      if (pengajuan.status === 'proses') return 'dot-blue'
      
      return 'dot-green'
    }

    return {
      showWizard,
      currentStep,
      daftarPengajuan,
      groupedPengajuan,
      selectedPengajuan,
      selectPengajuan,
      form,
      fileStatus,
      showValidationModal,
      validationMessage,
      handleSubmit,
      nextStep,
      prevStep,
      handleBack,
      openCaraPembayaran,
      tagihanColor,
      caraBayarColor,
      statusColor,
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
  margin: 0;
  background: #e0e0e0;
  block-size: 2px;
  inline-size: 2px;
  inset-block-start: 18px;
  inset-inline-start: 9px;
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

.wizard-date-sidebar {
  margin-inline-end: 32px;
  min-inline-size: 80px;
}

.wizard-date-year {
  color: #222;
  font-weight: bold;
  margin-block-end: 8px;
}

.wizard-date-item {
  margin-block-end: 8px;
}

.wizard-date-btn {
  display: block;
  border: none;
  border-radius: 8px;
  margin: 0;
  background: #bdbdbd;
  color: #fff;
  cursor: pointer;
  font-size: 1rem;
  font-weight: 600;
  inline-size: 100%;
  padding-block: 6px;
  padding-inline: 0;
  transition: background 0.2s;
}

.wizard-date-btn.active {
  background: #17a2a6;
  color: #fff;
}

.wizard-vertical-detail {
  flex: 1;
  min-inline-size: 320px;
  padding-block: 34px;
  padding-inline-start: 68px;
}

.wizard-vertical-step {
  position: relative;
  display: flex;
  align-items: start;
  margin-block-end: 70px;
}

.wizard-vertical-dot {
  flex-shrink: 0;
  border: 2px solid #fff;
  border-radius: 50%;
  block-size: 18px;
  box-shadow: 0 0 0 2px #e0e0e0;
  inline-size: 18px;
  margin-inline-end: 16px;
}

.dot-yellow {
  background: #ffe066;
}

.dot-green {
  background: #1bc47d;
}

.dot-grey {
  background: #bdbdbd;
}

.dot-blue {
  background: #4fc3f7;
}

.wizard-vertical-content {
  flex: 1;
}

.wizard-vertical-title {
  font-size: 17.6px;
  font-weight: 700;
  margin-block-end: 2px;
}

.wizard-vertical-desc {
  font-size: 16px;
  margin-block-end: 2px;
}

.wizard-bayar-btn {
  border: none;
  border-radius: 6px;
  background: #17a2a6;
  color: #fff;
  cursor: pointer;
  font-size: 0.95rem;
  font-weight: 600;
  margin-block-start: 6px;
  padding-block: 4px;
  padding-inline: 12px;
  transition: background 0.2s;
}

.wizard-bayar-btn:hover {
  background: #13918f;
}

.wizard-vertical-line {
  position: absolute;
  z-index: 0;
  margin: 0;
  background: #e0e0e0;
  block-size: 150px;
  inline-size: 2px;
  inset-block-start: 18px;
  inset-inline-start: 9px;
}

.wizard-vertical-empty {
  color: #888;
  font-style: italic;
  margin-block-start: 32px;
}
</style> 
