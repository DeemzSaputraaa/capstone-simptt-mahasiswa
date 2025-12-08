

<script setup>
import { ref } from 'vue'

const itemsPerPage = ref(10)

const daftarLegalisasi = ref([
  { nama: 'Syahrur Ramadhan', jumlah: 2, dokumen: 'Ijazah dan transkrip', tanggal: '2025 - 06 - 01', telp: '087678342122', alamat: 'Sleman, D.I Yogyakarta', tagihan: 'Lunas' },
  { nama: 'Dimas Edwin Saputra', jumlah: 8, dokumen: 'Ijazah dan transkrip', tanggal: '2025 - 06 - 01', telp: '081234567890', alamat: 'Bantul, D.I Yogyakarta', tagihan: 'Lunas' },
  { nama: 'Pandanunda Primadifani Kafah', jumlah: 5, dokumen: 'Ijazah', tanggal: '2025 - 06 - 01', telp: '082345678901', alamat: 'Gunungkidul, D.I Yogyakarta', tagihan: 'Belum Lunas' },
])

const showHargaDialog = ref(false)
const selectedItem = ref(null)
const nomorResi = ref('')

const openHarga = item => {
  selectedItem.value = item
  nomorResi.value = ''
  showHargaDialog.value = true
}

const openResi = item => { console.log('Resi:', item) }

const saveHarga = () => {
  console.log('Simpan harga/resi untuk:', selectedItem.value, 'resi:', nomorResi.value)
  showHargaDialog.value = false
}

const closeHargaDialog = () => {
  showHargaDialog.value = false
}
</script>

<template>
  <VCard>
    <VCardTitle>Legalisasi</VCardTitle>
    <VDivider />

    <VTable class="legalisasi-table w-auto">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Mahasiswa</th>
          <th>Jumlah</th>
          <th>Dokumen</th>
          <th>Tanggal Pengajuan</th>
          <th class="text-center">
            Action
          </th>
        </tr>
      </thead>
      <tbody>
        <tr
          v-for="(m, i) in daftarLegalisasi.slice(0, itemsPerPage)"
          :key="m.nama"
        >
          <td>{{ i + 1 }}</td>
          <td>{{ m.nama }}</td>
          <td>{{ m.jumlah }}</td>
          <td>{{ m.dokumen }}</td>
          <td>{{ m.tanggal }}</td>
          <td class="text-center">
            <div class="d-flex justify-center">
              <VBtn
                icon
                variant="text"
                class="me-2"
                title="Harga"
                @click="openHarga(m)"
              >
                <VIcon
                  icon="ri-money-dollar-circle-line"
                  size="22"
                  color="success"
                />
              </VBtn>
              <VBtn
                icon
                variant="text"
                title="Resi"
                @click="openResi(m)"
              >
                <VIcon
                  icon="ri-file-text-line"
                  size="22"
                  color="primary"
                />
              </VBtn>
            </div>
          </td>
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
