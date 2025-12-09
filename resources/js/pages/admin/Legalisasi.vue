

<script setup>
import { onMounted, ref } from 'vue'

const itemsPerPage = ref(10)
const daftarLegalisasi = ref([])
const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const form = ref({
  kdmahasiswa: '',
  jumlah_legalisasi: 1,
  biaya_legalisasi: '',
  alamat_kirim: '',
  nama_penerima_legalisasi: '',
  noresi: '',
  idtagihan: '',
  tgl_dikirim: '',
  kdlegalisasi_sebelum: '',
  telp_penerima: '',
  comment: '',
})

const resetForm = () => {
  form.value = {
    kdmahasiswa: '',
    jumlah_legalisasi: 1,
    biaya_legalisasi: '',
    alamat_kirim: '',
    nama_penerima_legalisasi: '',
    noresi: '',
    idtagihan: '',
    tgl_dikirim: '',
    kdlegalisasi_sebelum: '',
    telp_penerima: '',
    comment: '',
  }
}

const fetchData = async () => {
  loading.value = true
  errorMessage.value = ''
  try {
    const headers = { Accept: 'application/json' }
    const token = sessionStorage.getItem('jwt_token')
    if (token)
      headers.Authorization = `Bearer ${token}`

    const res = await fetch('/api/form-legalisasi', { headers })
    if (!res.ok)
      throw new Error(`Gagal memuat data (${res.status})`)

    const json = await res.json()
    if (json.success ?? Array.isArray(json.data)) {
      daftarLegalisasi.value = json.data ?? json
    } else {
      throw new Error(json.message || 'Response tidak valid')
    }
  } catch (e) {
    errorMessage.value = e.message || 'Gagal memuat data'
  } finally {
    loading.value = false
  }
}

const submitForm = async () => {
  errorMessage.value = ''
  successMessage.value = ''
  try {
    const headers = {
      Accept: 'application/json',
      'Content-Type': 'application/json',
    }
    const token = sessionStorage.getItem('jwt_token')
    if (token)
      headers.Authorization = `Bearer ${token}`

    const res = await fetch('/api/form-legalisasi', {
      method: 'POST',
      headers,
      body: JSON.stringify(form.value),
    })

    const json = await res.json()
    if (!res.ok || json.success === false)
      throw new Error(json.message || 'Gagal menyimpan pengajuan')

    // prepend data baru
    if (json.data)
      daftarLegalisasi.value.unshift(json.data)

    successMessage.value = 'Pengajuan legalisasi berhasil disimpan'
    resetForm()
  } catch (e) {
    errorMessage.value = e.message || 'Gagal menyimpan pengajuan'
  }
}

onMounted(fetchData)
</script>

<template>
  <VCard>
    <VCardTitle>Legalisasi</VCardTitle>
    <VDivider />

    <VAlert
      v-if="errorMessage"
      type="error"
      variant="tonal"
      class="mb-4"
    >
      {{ errorMessage }}
    </VAlert>
    <VAlert
      v-if="successMessage"
      type="success"
      variant="tonal"
      class="mb-4"
    >
      {{ successMessage }}
    </VAlert>

    <VCard
      variant="outlined"
      class="pa-4 mb-6"
    >
      <div class="text-subtitle-1 font-weight-bold mb-3">
        Form Pengajuan Legalisasi
      </div>
      <VRow>
        <VCol cols="12" md="6">
          <VTextField
            v-model="form.kdmahasiswa"
            label="KDMahasiswa"
            placeholder="Isi kdmahasiswa"
            type="number"
          />
        </VCol>
        <VCol cols="12" md="3">
          <VTextField
            v-model="form.jumlah_legalisasi"
            label="Jumlah Legalisasi"
            type="number"
            min="1"
          />
        </VCol>
        <VCol cols="12" md="3">
          <VTextField
            v-model="form.biaya_legalisasi"
            label="Biaya Legalisasi"
            type="number"
          />
        </VCol>
        <VCol cols="12" md="6">
          <VTextField
            v-model="form.alamat_kirim"
            label="Alamat Kirim"
            placeholder="Alamat lengkap"
          />
        </VCol>
        <VCol cols="12" md="6">
          <VTextField
            v-model="form.nama_penerima_legalisasi"
            label="Nama Penerima"
          />
        </VCol>
        <VCol cols="12" md="4">
          <VTextField
            v-model="form.telp_penerima"
            label="Telepon Penerima"
          />
        </VCol>
        <VCol cols="12" md="4">
          <VTextField
            v-model="form.idtagihan"
            label="ID Tagihan"
          />
        </VCol>
        <VCol cols="12" md="4">
          <VTextField
            v-model="form.noresi"
            label="No. Resi"
          />
        </VCol>
        <VCol cols="12" md="4">
          <VTextField
            v-model="form.tgl_dikirim"
            label="Tanggal Dikirim"
            type="date"
          />
        </VCol>
        <VCol cols="12" md="4">
          <VTextField
            v-model="form.kdlegalisasi_sebelum"
            label="KD Legalisasi Sebelumnya"
            type="number"
          />
        </VCol>
        <VCol cols="12">
          <VTextarea
            v-model="form.comment"
            label="Catatan"
            rows="2"
          />
        </VCol>
        <VCol cols="12" class="d-flex justify-end">
          <VBtn
            color="primary"
            :loading="loading"
            @click="submitForm"
          >
            Simpan Pengajuan
          </VBtn>
        </VCol>
      </VRow>
    </VCard>

    <VTable class="legalisasi-table w-auto">
      <thead>
        <tr>
          <th>No</th>
          <th>KDMahasiswa</th>
          <th>Jumlah</th>
          <th>Biaya</th>
          <th>Penerima</th>
          <th>Tgl Dikirim</th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(m, i) in daftarLegalisasi.slice(0, itemsPerPage)"
          :key="m.kdlegalisasi || i"
        >
          <td>{{ i + 1 }}</td>
          <td>{{ m.kdmahasiswa }}</td>
          <td>{{ m.jumlah_legalisasi }}</td>
          <td>{{ m.biaya_legalisasi }}</td>
          <td>{{ m.nama_penerima_legalisasi }}</td>
          <td>{{ m.tgl_dikirim }}</td>
        </tr>
      </tbody>
    </VTable>

    <div class="table-footer">
      <span>Showing per Page</span>
      <VSelect
        v-model="itemsPerPage"
        :items="[5, 10, 20]"
        density="compact"
        style="max-inline-size: 80px;"
      />
    </div>
  </VCard>
  <VDialog
    v-model="showHargaDialog"
    max-width="800"
  >
    <VCard>
      <VCardTitle>Formulir Pendaftaran Legalisasi</VCardTitle>
      <VCardText>
        <div class="text-subtitle-1 font-weight-bold mb-2">
          Ringkasan Data Pendaftaran
        </div>
        <div
          v-if="selectedItem"
          class="summary"
        >
          <div class="summary-row">
            <span class="summary-label">Jenis Dokumen</span><span class="summary-value">{{ selectedItem.dokumen }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Jumlah</span><span class="summary-value">{{ selectedItem.jumlah }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Nama Penerima</span><span class="summary-value">{{ selectedItem.nama }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">No Telp Penerima</span><span class="summary-value">{{ selectedItem.telp }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Alamat</span><span class="summary-value">{{ selectedItem.alamat }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Tagihan</span><span class="summary-value">{{ selectedItem.tagihan }}</span>
          </div>
        </div>
        <VTextField
          v-model="nomorResi"
          label="Masukkan Nomor Resi"
          variant="outlined"
          class="mt-6"
        />
      </VCardText>
      <VCardActions class="justify-space-between">
        <VBtn
          variant="tonal"
          color="grey"
          @click="closeHargaDialog"
        >
          Kembali
        </VBtn>
        <VBtn
          color="teal"
          variant="tonal"
          @click="saveHarga"
        >
          Simpan
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>

<style scoped>
.legalisasi-table {
  border: 1px solid #e0e0e0;
}

.legalisasi-table thead {
  background-color: #f5f5f5;
}

.legalisasi-table thead th {
  border-block-end: 2px solid #e0e0e0;
  color: #333;
  font-size: 14px;
  font-weight: 600;
  padding-block: 12px;
  padding-inline: 8px;
}

.legalisasi-table tbody td {
  background-color: #f7f8fb;
  border-block-end: 1px solid #e0e0e0;
  font-size: 13px;
  padding-block: 12px;
  padding-inline: 8px;
}

.legalisasi-table tbody tr:hover {
  background-color: #fafafa;
}

.text-center { text-align: center; }
.w-auto { inline-size: auto !important; }

.table-footer {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-block-start: 16px;
}

.summary {
  display: grid;
  row-gap: 12px;
}

.summary-row {
  display: grid;
  align-items: center;
  grid-template-columns: 220px 1fr;
}

.summary-label {
  font-weight: 600;
}
</style>
