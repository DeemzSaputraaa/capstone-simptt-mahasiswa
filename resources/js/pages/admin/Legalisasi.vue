

<script setup>
import { onMounted, ref } from 'vue'

const itemsPerPage = ref(10)
const daftarLegalisasi = ref([])
const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')

const approvingId = ref(null)
const deletingId = ref(null)

const showApproveDialog = ref(false)
const selectedItem = ref(null)
const approveForm = ref({
  noresi: '',
  tgl_dikirim: '',
})

const showDeleteDialog = ref(false)
const deleteCandidate = ref(null)

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

const getRowId = row => row?.id ?? row?.kdlegalisasi
const isApproved = row => Boolean(row?.tgl_dikirim)

const openApprove = item => {
  selectedItem.value = item
  approveForm.value = {
    noresi: item?.noresi ?? '',
    tgl_dikirim: (item?.tgl_dikirim
      ? String(item.tgl_dikirim).slice(0, 10)
      : new Date().toISOString().slice(0, 10)),
  }
  showApproveDialog.value = true
}

const closeApprove = () => {
  showApproveDialog.value = false
  selectedItem.value = null
}

const saveApprove = async () => {
  if (!selectedItem.value) return

  errorMessage.value = ''
  successMessage.value = ''
  approvingId.value = getRowId(selectedItem.value)

  try {
    const headers = {
      Accept: 'application/json',
      'Content-Type': 'application/json',
    }
    const token = sessionStorage.getItem('jwt_token')
    if (token)
      headers.Authorization = `Bearer ${token}`

    const id = getRowId(selectedItem.value)
    const res = await fetch(`/api/form-legalisasi/${id}`, {
      method: 'PUT',
      headers,
      body: JSON.stringify({
        noresi: approveForm.value.noresi,
        tgl_dikirim: approveForm.value.tgl_dikirim,
      }),
    })

    const json = await res.json()
    if (!res.ok || json.success === false)
      throw new Error(json.message || 'Gagal approve legalisasi')

    // update lokal
    daftarLegalisasi.value = daftarLegalisasi.value.map(row => {
      if (getRowId(row) !== id) return row
      return { ...row, ...json.data }
    })

    successMessage.value = 'Legalisasi berhasil di-approve (resi & tanggal dikirim tersimpan)'
    closeApprove()
  } catch (e) {
    errorMessage.value = e.message || 'Gagal approve legalisasi'
  } finally {
    approvingId.value = null
  }
}

const openDeleteDialog = item => {
  deleteCandidate.value = item
  showDeleteDialog.value = true
}

const closeDeleteDialog = () => {
  showDeleteDialog.value = false
  deleteCandidate.value = null
}

const confirmDelete = async () => {
  if (!deleteCandidate.value) return
  await deleteItem(deleteCandidate.value)
  closeDeleteDialog()
}

const deleteItem = async item => {
  const id = getRowId(item)
  if (!id) return

  errorMessage.value = ''
  successMessage.value = ''
  deletingId.value = id

  try {
    const headers = { Accept: 'application/json' }
    const token = sessionStorage.getItem('jwt_token')
    if (token)
      headers.Authorization = `Bearer ${token}`

    const res = await fetch(`/api/form-legalisasi/${id}`, {
      method: 'DELETE',
      headers,
    })
    const json = await res.json().catch(() => ({}))
    if (!res.ok)
      throw new Error(json.message || `Gagal menghapus (${res.status})`)

    daftarLegalisasi.value = daftarLegalisasi.value.filter(row => getRowId(row) !== id)
    successMessage.value = 'Pengajuan legalisasi berhasil dihapus'
  } catch (e) {
    errorMessage.value = e.message || 'Gagal menghapus'
  } finally {
    deletingId.value = null
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
    <VCardTitle class="admin-card-title">
      Legalisasi
    </VCardTitle>
    <VCardSubtitle>Daftar pengajuan legalisasi</VCardSubtitle>

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

    <!--
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
    -->

    <div class="table-wrapper">
      <VTable class="legalisasi-table w-auto">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama Mahasiswa</th>
            <th>Jumlah</th>
            <th>Biaya</th>
            <th>No. VA</th>
            <th>Penerima</th>
            <th>Tgl Dikirim</th>
            <th class="text-center">Aksi</th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(m, i) in daftarLegalisasi.slice(0, itemsPerPage)"
            :key="m.kdlegalisasi || i"
          >
            <td data-label="No">{{ i + 1 }}</td>
            <td data-label="Nama Mahasiswa">{{ m.nama_mahasiswa ?? '-' }}</td>
            <td data-label="Jumlah">{{ m.jumlah_legalisasi }}</td>
            <td data-label="Biaya">{{ m.biaya_legalisasi }}</td>
            <td data-label="No. VA">{{ m.noresi ?? '-' }}</td>
            <td data-label="Penerima">{{ m.nama_penerima_legalisasi }}</td>
            <td data-label="Tgl Dikirim">{{ m.tgl_dikirim }}</td>
            <td data-label="Aksi" class="text-center">
              <div class="d-flex justify-center">
                <VBtn
                  icon
                  variant="text"
                  color="success"
                  class="action-btn"
                  :loading="approvingId === getRowId(m)"
                  :disabled="deletingId !== null || isApproved(m)"
                  @click="openApprove(m)"
                >
                  <VIcon
                    icon="ri-check-line"
                    size="20"
                  />
                </VBtn>
                <VBtn
                  icon
                  variant="text"
                  color="error"
                  class="action-btn"
                  :loading="deletingId === getRowId(m)"
                  :disabled="approvingId !== null"
                  @click="openDeleteDialog(m)"
                >
                  <VIcon
                    icon="ri-delete-bin-line"
                    size="20"
                  />
                </VBtn>
              </div>
            </td>
          </tr>
        </tbody>
      </VTable>
    </div>

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
    v-model="showApproveDialog"
    max-width="800"
  >
    <VCard>
      <VCardTitle>Approve Legalisasi</VCardTitle>
      <VCardText>
        <div class="text-subtitle-1 font-weight-bold mb-2">
          Ringkasan Pengajuan
        </div>
        <div
          v-if="selectedItem"
          class="summary"
        >
          <div class="summary-row">
            <span class="summary-label">KDMahasiswa</span><span class="summary-value">{{ selectedItem.kdmahasiswa }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Nama Mahasiswa</span><span class="summary-value">{{ selectedItem.nama_mahasiswa ?? '-' }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Jenis Dokumen</span><span class="summary-value">{{ selectedItem.comment ?? '-' }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Jumlah</span><span class="summary-value">{{ selectedItem.jumlah_legalisasi }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Nama Penerima</span><span class="summary-value">{{ selectedItem.nama_penerima_legalisasi }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">No Telp Penerima</span><span class="summary-value">{{ selectedItem.telp_penerima ?? '-' }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Alamat</span><span class="summary-value">{{ selectedItem.alamat_kirim }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">No. VA</span><span class="summary-value">{{ selectedItem.noresi ?? '-' }}</span>
          </div>
        </div>
        <VTextField
          v-model="approveForm.noresi"
          label="Masukkan Nomor Resi"
          variant="outlined"
          class="mt-6"
        />
        <VTextField
          v-model="approveForm.tgl_dikirim"
          label="Tanggal Dikirim"
          type="date"
          variant="outlined"
          class="mt-4"
        />
      </VCardText>
      <VCardActions class="justify-space-between">
        <VBtn
          variant="tonal"
          color="grey"
          @click="closeApprove"
        >
          Kembali
        </VBtn>
        <VBtn
          color="teal"
          variant="tonal"
          :loading="approvingId !== null"
          @click="saveApprove"
        >
          Simpan
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
  <VDialog
    v-model="showDeleteDialog"
    max-width="420"
  >
    <VCard>
      <VCardTitle>Hapus Pengajuan Legalisasi</VCardTitle>
      <VCardText>
        <div>Yakin ingin menghapus pengajuan ini?</div>
        <div
          v-if="deleteCandidate"
          class="summary mt-4"
        >
          <div class="summary-row">
            <span class="summary-label">KDMahasiswa</span><span class="summary-value">{{ deleteCandidate.kdmahasiswa }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Nama Mahasiswa</span><span class="summary-value">{{ deleteCandidate.nama_mahasiswa ?? '-' }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">No. VA</span><span class="summary-value">{{ deleteCandidate.noresi ?? '-' }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Jumlah</span><span class="summary-value">{{ deleteCandidate.jumlah_legalisasi }}</span>
          </div>
        </div>
      </VCardText>
      <VCardActions class="justify-end">
        <VBtn
          variant="tonal"
          color="grey"
          :disabled="deletingId !== null"
          @click="closeDeleteDialog"
        >
          Batal
        </VBtn>
        <VBtn
          color="error"
          variant="tonal"
          :loading="deletingId !== null"
          @click="confirmDelete"
        >
          Hapus
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>

<style scoped>
.table-wrapper {
  overflow-x: auto;
}

.admin-card-title {
  border-radius: 4px 4px 0 0;
  background: linear-gradient(
    135deg,
    rgb(var(--v-theme-primary)) 0%,
    rgba(var(--v-theme-primary), 0.85) 100%
  );
  color: rgb(var(--v-theme-on-primary)) !important;
  font-weight: 700;
  letter-spacing: 0.5px;
  padding-block: 1rem !important;
  padding-inline: 1.5rem !important;
}

.v-card-subtitle {
  background-color: rgb(var(--v-theme-surface));
  border-block-end: 1px solid rgba(var(--v-theme-on-surface), 0.1);
  color: rgba(var(--v-theme-on-surface), 0.7) !important;
  padding-block: 0.5rem !important;
  padding-inline: 1.5rem !important;
}

.legalisasi-table {
  overflow: hidden;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.12);
  border-radius: 4px;
  background-color: rgb(var(--v-theme-surface));
  color: rgb(var(--v-theme-on-surface));
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.legalisasi-table thead {
  background: linear-gradient(
    to right,
    rgba(var(--v-theme-on-surface), 0.04),
    rgba(var(--v-theme-on-surface), 0.06)
  );
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.legalisasi-table thead th {
  border: none;
  color: rgba(var(--v-theme-on-surface), 0.85);
  font-size: 0.8rem;
  font-weight: 600;
  letter-spacing: 0.5px;
  padding-block: 1rem;
  padding-inline: 0.75rem;
  text-transform: uppercase;
  white-space: nowrap;
}

.legalisasi-table tbody td {
  background-color: rgba(var(--v-theme-surface), 0.9);
  border-block-end: 1px solid rgba(var(--v-theme-on-surface), 0.08);
  color: rgba(var(--v-theme-on-surface), 0.8);
  font-size: 0.875rem;
  padding-block: 1rem;
  padding-inline: 0.75rem;
  transition: all 0.2s ease;
  vertical-align: middle;
}

.legalisasi-table tbody tr:not(:last-child) td {
  border-block-end: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.legalisasi-table tbody tr:hover td {
  background-color: rgba(var(--v-theme-on-surface), 0.05);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
  transform: translateY(-1px);
}

.text-center { text-align: center; }
.w-auto { inline-size: auto !important; }

.table-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem;
  border-radius: 0 0 4px 4px;
  background-color: rgba(var(--v-theme-surface), 0.9);
  border-block-start: 1px solid rgba(var(--v-theme-on-surface), 0.08);
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

.action-btn {
  margin-inline: 2px;
}

@media (max-width: 960px) {
  .legalisasi-table thead {
    display: none;
  }

  .legalisasi-table tbody tr {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 8px;
    padding: 12px;
  }

  .legalisasi-table tbody td {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 4px;
    background-color: rgba(var(--v-theme-surface), 0.9);
  }

  .legalisasi-table tbody td::before {
    content: attr(data-label);
    font-size: 12px;
    font-weight: 600;
    color: rgba(var(--v-theme-on-surface), 0.7);
    text-transform: uppercase;
  }
}

@media (prefers-color-scheme: dark) {
  .legalisasi-table {
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
  }

  .legalisasi-table thead {
    background: linear-gradient(to right, #0f0f0f, #141414);
  }

  .table-footer {
    background-color: rgba(var(--v-theme-surface), 0.92);
  }
}
</style>
