<template>
  <VApp>
    <VMain>
      <VContainer
        fluid
        class="pa-0"
      >
        <div v-if="!showWizard">
          <div class="d-flex flex-wrap justify-space-between align-center mb-6">
          <VBtn
            color="#17a2a6"
            style="border-radius: 10px; background: rgb(var(--v-theme-primary)); color: #fff; font-size: 1.1rem; font-weight: 500; margin-block-end: 32px; min-block-size: 48px;"
            @click="showWizard = true"
          >
            Pengajuan Legalisasi
          </VBtn>
          <VBtn
            color="#17a2a6"
            style="border-radius: 10px; background: rgb(var(--v-theme-primary)); color: #fff; font-size: 1.1rem; font-weight: 500; margin-block-end: 32px; min-block-size: 48px;"
            @click="openCaraPembayaran"
          >
            Cara Pembayaran
          </VBtn>
          </div>

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

      <div class="table-wrapper">
        <table class="pengajuan-table">
              <colgroup>
                <col class="col-no" />
                <col class="col-tanggal" />
                <col class="col-tagihan" />
                <col class="col-va" />
                <col class="col-resi" />
                <col class="col-status" />
              </colgroup>
      <thead>
        <tr>
          <th>No</th>
          <th>Tanggal Pengajuan</th>
          <th>Tagihan</th>
          <th>No. VA</th>
          <th>No. Resi</th>
          <th>Status</th>
        </tr>
      </thead>
      <tbody>
        <tr v-for="(item, index) in paginatedData" :key="item.id" :class="{ 'done-row': item.status === 'Done' }" >
          <td>{{ (currentPage - 1)* itemsPerPage + index + 1}}</td>
          <td>{{ item.tanggal }}</td>
          <td>{{ item.tagihan }}</td>
          <td>{{ item.noVA || '-' }}</td>
          <td v-if="item.noResi">
            <a
            :href="`https://www.posindonesia.co.id/en/tracking/${item.noResi}`"
            target="_blank"
            rel="noopener noreferrer"
            class="text-blue-600 underline hover:text-blue-800"
            >
            {{ item.noResi }}
          </a>
        </td>
        <td v-else>-</td>
          <!-- <td>{{ item.noResi || '-' }}</td> -->
          <td :class="statusClass(item.status)">
            {{ item.status }}
          </td>
        </tr>
      </tbody>
    </table>
        <div class="d-flex justify-start align-center mb-2" style="gap: 0.3rem;  padding-block-start: 14px;">
          <span style="font-size: 1rem; font-weight: 400;">Page:</span>
<VSelect
  v-model="itemsPerPage"
  :items="[5, 10, 20, 50]"
  density="compact"
  hide-details
  style="max-inline-size: 85px;"
/>
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
                  <tbody>
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
                  </tbody>
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
          @click="closeValidationModal"
        >
          OK
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
  <VDialog
    v-model="showCaraBayarDialog"
    max-width="700"
  >
    <VCard>
      <VCardTitle class="text-h5">
        Cara Melakukan Pembayaran
      </VCardTitle>
      <VCardText>
        <div style="border-radius: 8px; background: #fff; color: #000; font-family: Georgia, serif; padding-block: 1rem; padding-inline: 1rem;">
          <div style="font-size: 2rem; font-style: normal; font-weight: 600; line-height: 3rem; text-align: center;">
            Tagihan Pembayaran 2211501033
          </div>
          <div style="font-size: 1.2rem; font-style: italic; margin-block-end: 1rem; text-align: center;">
            Timestamp: 30 Juni 2025 13:21:51
          </div>
          <div style="border-radius: 16px 16px 0 0; background: #129990; color: #fff; font-size: 1.5rem; font-weight: 500; padding-block: 0.5rem; padding-inline: 1rem; text-align: center;">
            Melalui ATM BPD DIY
          </div>
          <div style="padding-block: 0.4rem; padding-inline: 1.5rem;">
            <div style="font-size: 1.1rem; margin-block-end: 0.2rem;">
              Catatan:
            </div>
            <ol style="font-size: 1.1rem;">
              <li>Siapkan Kartu ATM Bank BPD DIY</li>
              <li>Masukkan PIN dan pilih bahasa</li>
              <li>Pilih menu Pembayaran</li>
              <li>Pilih layanan Pendidikan</li>
              <li>Pilih Universitas</li>
              <li>Masukan ID Peserta <b>2211501033</b></li>
              <li>Masukkan nominal <b>400.000</b></li>
              <li>Lakukan pembayaran</li>
            </ol>
          </div>
          <div style="border-radius: 16px 16px 0 0; background: #129990; color: #fff; font-size: 1.5rem; font-weight: 500; padding-block: 0.5rem; padding-inline: 1rem; text-align: center;">
            Melalui Internet Banking Bank BSI
          </div>
          <div style="padding-block: 0.4rem; padding-inline: 1.5rem;">
            <div style="font-size: 1.1rem; margin-block-end: 0.2rem;">
              Catatan: Nominal pembayaran otomatis muncul di Ibanking.
            </div>
            <ol style="font-size: 1.1rem;">
              <li>Pilih menu Pembayaran</li>
              <li>
                Pada menu pembayaran pilih:
                </li>
                <ul>
                  <li>Jenis Pembayaran: Institusi</li>
                  <li>Nama Lembaga: UNISA Yogya</li>
                  <li>Nomor Pembayaran: <b>2211501033</b></li>
                </ul>
            </ol>
          </div>
          <div style="border-radius: 16px 16px 0 0; background: #129990; color: #fff; font-size: 1.5rem; font-weight: 500; padding-block: 0.5rem; padding-inline: 1rem; text-align: center;">
            Melalui Mobile Banking Bank BSI
          </div>
          <div style="padding-block: 0.4rem; padding-inline: 1.5rem;">
            <div style="font-size: 1.1rem; margin-block-end: 0.2rem;">
              Catatan: Nominal pembayaran otomatis muncul di Mbanking.
            </div>
            <ol style="font-size: 1.1rem;">
              <li>Pilih menu Pembayaran</li>
              <li>
                Pada menu pembayaran pilih:
                <ul>
                  <li>Jenis Pembayaran: Akademik</li>
                  <li>Nama Akademik: 9032 - UNISA Yogya</li>
                  <li>Kode Bayar: <b>2211501033</b></li>
                </ul>
              </li>
            </ol>
          </div>
          <div style="border-radius: 16px 16px 0 0; background: #129990; color: #fff; font-size: 1.5rem; font-weight: 500; padding-block: 0.5rem; padding-inline: 1rem; text-align: center;">
            Melalui ATM Bank BSI
          </div>
          <div style="padding-block: 0.4rem; padding-inline: 1.5rem;">
            <div style="font-size: 1.1rem; margin-block-end: 0.5rem;">
              Catatan: Nominal pembayaran otomatis muncul di ATM.
            </div>
            <ol style="font-size: 1.2rem;">
              <li>Pilih menu Pembayaran/Pembelian</li>
              <li>Pilih menu Akademik</li>
              <li>Masukkan kode 9032-<b>2211501033</b></li>
            </ol>
          </div>
        </div>
      </VCardText>
      <VCardActions>
        <VSpacer />
        <VBtn
          color="primary"
          @click="showCaraBayarDialog = false"
        >
          Tutup
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>

<script>
import { computed, ref } from 'vue';
import { useRoute } from 'vue-router';

export default {
  name: 'PendaftaranLegalisasi',
  setup() {
    const showWizard = ref(false)
    const currentStep = ref(1)

    // Dummy data pengajuan legalisasi
    const pengajuanList = ref([
      { id: 1, tanggal: '2025-06-01', tagihan: 'Menunggu Tagihan', noVA: '', noResi: '', status: 'Pending' },
      { id: 2, tanggal: '2025-05-17', tagihan: 'Rp 20.000,00', noVA: '2211501047', noResi: 'P2303300137564', status: 'Done' },
      { id: 3, tanggal: '2025-04-20', tagihan: 'Rp 65.000,00', noVA: '2211501047', noResi: '', status: 'Proses' },
      { id: 4, tanggal: '2025-04-17', tagihan: 'Menunggu Tagihan', noVA: '', noResi: '', status: 'Pending' },
      { id: 5, tanggal: '2025-04-08', tagihan: 'Menunggu Tagihan', noVA: '', noResi: '', status: 'Pending' }, 
      { id: 6, tanggal: '2025-10-06', tagihan: 'Rp 80.000,00', noVA: '2211505666', noResi: 'P2345678901234', status: 'Done' },
      { id: 7, tanggal: '2025-10-07', tagihan: 'Menunggu Tagihan', noVA: '', noResi: '', status: 'Pending' },
      { id: 8,  tanggal: '2025-10-08', tagihan: 'Rp 40.000,00', noVA: '2211233313', noResi: '', status: 'Proses' },
    ])
    const currentPage = ref(1)
    const itemsPerPage = ref(5)
    
    const totalPages = computed(() => Math.ceil(pengajuanList.value.length / itemsPerPage.value))
    const paginatedData = computed(() => {
      const start = (currentPage.value - 1) * itemsPerPage.value
      const end = start + itemsPerPage.value
      return pengajuanList.value.slice(start, end)
    })
    
    const statusClass = status => {
      switch(status.toLowerCase()) {
        case 'done': return 'bg-green'
        default: return 'bg-white'
      }
    }

    // State pengajuan yang dipilih
    const selectedPengajuan = ref(pengajuanList.value[0])

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

    const showValidationModal = ref(false)
    const validationMessage = ref('')

    const showNotifAlert = ref(false)
    const notifMessage = ref('')

    const showCaraBayarDialog = ref(false)

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

    // Fungsi untuk membuka cara pembayaran dalam dialog/modal
    const openCaraPembayaran = item => {
      showCaraBayarDialog.value = true
    }

    // Warna status step
    const tagihanColor = pengajuan => pengajuan.status === 'pending' ? 'dot-white' : 'dot-white'
    const caraBayarColor = pengajuan => pengajuan.status === 'pending' ? 'dot-white' : 'dot-white'

    const statusColor = pengajuan => {
      if (pengajuan.status === 'pending') return 'dot-white'
      if (pengajuan.status === 'proses') return 'dot-white'
      
      return 'dot-green'
    }

    const route = useRoute()
    const notifId = route.query.notif
    if (notifId) {
      showNotifAlert.value = true
      notifMessage.value = 'Ada update status legalisasi atau pengiriman legalisasi Anda. Silakan cek detail di bawah ini.'
    }

    // Nomor resi dummy, bisa diganti sesuai data pengajuan
    const defaultResi = 'P2303300137522'


    // Fungsi untuk tracking
    const trackingResi = item => {
      // Ganti dengan item.resi jika ada field resi di data pengajuan
      const resi = item.noResi || defaultResi

      window.open(`https://www.posindonesia.co.id/en/tracking/${resi}`, '_blank')
    }

    const closeValidationModal = () => {
      showValidationModal.value = false
      
      if (validationMessage.value.includes('berhasil')) {
        // Balik ke list pengajuan
         showWizard.value = false
         currentStep.value = 1
        }
      }

    return {
      showWizard,
      currentStep,
      pengajuanList,
      statusClass,
      selectedPengajuan,
      selectPengajuan,
      form,
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
      showNotifAlert,
      notifMessage,
      showCaraBayarDialog,
      trackingResi,
      paginatedData,
      currentPage,
      itemsPerPage,
      totalPages,
    }
  },
}
</script>

<style scoped>
.table-wrapper {
  border-radius: 8px;
  background: var(--v-theme-surface);
  box-shadow: 0 2px 6px rgba(0, 0, 0, 10%);
  inline-size: 100%;
  overflow-x: auto;
  padding-block: 16px;
  padding-inline: 20px;
}

.table-wrapper .pengajuan-table {
  background: var(--v-theme-surface);
  border-collapse: collapse; /* 🟢 Biar garis antar sel nyatu */
  inline-size: 100%;
  table-layout: fixed; /* 🟢 Kolom proporsional, nggak goyah */
}

.pengajuan-table col.col-no { inline-size: 6%; }
.pengajuan-table col.col-tanggal { inline-size: 18%; }
.pengajuan-table col.col-tagihan { inline-size: 28%; }
.pengajuan-table col.col-va { inline-size: 14%; }
.pengajuan-table col.col-resi { inline-size: 22%; }
.pengajuan-table col.col-status { inline-size: 12%; }

.pengajuan-table th,
.pengajuan-table td {
  box-sizing: border-box;
  border: 1px solid #ccc;
  line-height: 1.2;
  padding-block: 12px;
  padding-inline: 14px;
  text-align: center;
  vertical-align: middle;
  word-wrap: break-word;
}

.pengajuan-table th {
  background-color: var(--v-theme-surface);
  font-weight: 600;
  white-space: normal;
}

.styled-summary-table td {
  font-size: 1rem;
  padding-block: 14px;
  padding-inline: 18px;
  vertical-align: top;
}

.pengajuan-table tr.done-row td {
  background-color: #7cfc00 !important;
  color: black;
  font-weight: normal;
}

.styled-summary-table tr {
  background: #fff;
  border-block-end: 1px solid #f0f0f0;
}

.pengajuan-table tr:nth-child(even) {
  background-color: var(--v-theme-surface);
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
