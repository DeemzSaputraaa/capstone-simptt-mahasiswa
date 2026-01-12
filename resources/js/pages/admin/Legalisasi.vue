

<script setup>
import { computed, onBeforeUnmount, onMounted, ref, watch } from 'vue'

const itemsPerPage = ref(10)
const daftarLegalisasi = ref([])


const getItemsPerPageOptions = total => {
  if (total <= 75) return [5, 10, 20, 50, 75]
  if (total <= 100) return [5, 25, 50, 75, 100]
  if (total <= 1000) return [5, 50, 100, 500, 1000]

  return [5, 100, 500, 1000, total]
}

const itemsPerPageOptions = computed(() => {
  const total = Array.isArray(daftarLegalisasi.value) ? daftarLegalisasi.value.length : 0
  return getItemsPerPageOptions(total)
})

const loading = ref(false)
const errorMessage = ref('')
const successMessage = ref('')
const filterResi = ref('all')
const filterBiaya = ref('all')
const filterApprove = ref('all')
const pendingFilterResi = ref('all')
const pendingFilterBiaya = ref('all')
const pendingFilterApprove = ref('all')
let successTimer = null
let errorTimer = null

const approvingId = ref(null)
const deletingId = ref(null)

const showApproveDialog = ref(false)
const selectedItem = ref(null)

const approveForm = ref({
  noresi: '',
  tgl_dikirim: '',
  biaya_legalisasi: '',
})

const showResiDialog = ref(false)
const resiTarget = ref(null)
const resiValue = ref('')

const showBiayaDialog = ref(false)
const biayaTarget = ref(null)
const biayaValue = ref('')

const showDeleteDialog = ref(false)
const deleteCandidate = ref(null)
const showDetailDialog = ref(false)
const detailItem = ref(null)

const getCsrfToken = () => document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

const buildHeaders = (withJson = false) => {
  const headers = { Accept: 'application/json' }
  if (withJson)
    headers['Content-Type'] = 'application/json'

  const token = sessionStorage.getItem('jwt_token')
  if (token)
    headers.Authorization = `Bearer ${token}`

  const csrf = getCsrfToken()
  if (csrf)
    headers['X-CSRF-TOKEN'] = csrf

  headers['X-Requested-With'] = 'XMLHttpRequest'
  return headers
}

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
  if (!item) return
  if (item.biaya_legalisasi === null || item.biaya_legalisasi === undefined || item.biaya_legalisasi === '') {
    openBiayaDialog(item)
    
    return
  }
  if (!item.noresi) {
    openResiDialog(item)
    
    return
  }

  selectedItem.value = item
  approveForm.value = {
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

const openResiDialog = item => {
  resiTarget.value = item
  resiValue.value = item?.noresi ?? ''
  showResiDialog.value = true
}

const closeResiDialog = () => {
  showResiDialog.value = false
  resiTarget.value = null
  resiValue.value = ''
}

const saveResi = async () => {
  if (!resiTarget.value) return
  if (!/^[A-Z0-9]+$/i.test(resiValue.value || '')) {
    errorMessage.value = 'Nomor resi hanya boleh berisi huruf dan angka'
    
    return
  }

  errorMessage.value = ''
  successMessage.value = ''
  approvingId.value = getRowId(resiTarget.value)

  try {
    const headers = buildHeaders(true)

    const id = getRowId(resiTarget.value)

    const res = await fetch(`/api/form-legalisasi/${id}`, {
      method: 'PUT',
      headers,
      body: JSON.stringify({ noresi: resiValue.value }),
    })

    const json = await res.json()
    if (!res.ok || json.success === false)
      throw new Error(json.message || 'Gagal menyimpan nomor resi')

    daftarLegalisasi.value = daftarLegalisasi.value.map(row => {
      if (getRowId(row) !== id) return row
      
      return { ...row, ...json.data }
    })

    successMessage.value = 'Nomor resi tersimpan'
    closeResiDialog()
  } catch (e) {
    errorMessage.value = e.message || 'Gagal menyimpan nomor resi'
  } finally {
    approvingId.value = null
  }
}

const formatRupiahInput = value => {
  const digits = String(value ?? '').replace(/\D/g, '')
  if (!digits) return ''

  return digits.replace(/\B(?=(\d{3})+(?!\d))/g, '.')
}

const parseRupiahInput = value => String(value ?? '').replace(/\D/g, '')

const onBiayaInput = event => {
  biayaValue.value = formatRupiahInput(event?.target?.value)
}

const openBiayaDialog = item => {
  biayaTarget.value = item
  biayaValue.value = formatRupiahInput(item?.biaya_legalisasi ?? '')
  showBiayaDialog.value = true
}

const closeBiayaDialog = () => {
  showBiayaDialog.value = false
  biayaTarget.value = null
  biayaValue.value = ''
}

const saveBiaya = async () => {
  if (!biayaTarget.value) return

  errorMessage.value = ''
  successMessage.value = ''
  approvingId.value = getRowId(biayaTarget.value)

  try {
    const headers = buildHeaders(true)

    const id = getRowId(biayaTarget.value)
    const amount = parseRupiahInput(biayaValue.value)
    if (!amount) {
      errorMessage.value = 'Biaya tidak valid'

      return
    }

    const res = await fetch(`/api/form-legalisasi/${id}`, {
      method: 'PUT',
      headers,
      body: JSON.stringify({ biaya_legalisasi: Number(amount) }),
    })

    const json = await res.json()
    if (!res.ok || json.success === false)
      throw new Error(json.message || 'Gagal menyimpan biaya')

    daftarLegalisasi.value = daftarLegalisasi.value.map(row => {
      if (getRowId(row) !== id) return row
      
      return { ...row, ...json.data }
    })

    successMessage.value = 'Biaya tersimpan'
    closeBiayaDialog()
  } catch (e) {
    errorMessage.value = e.message || 'Gagal menyimpan biaya'
  } finally {
    approvingId.value = null
  }
}

const saveApprove = async () => {
  if (!selectedItem.value) return

  errorMessage.value = ''
  successMessage.value = ''
  approvingId.value = getRowId(selectedItem.value)

  try {
    const headers = buildHeaders(true)

    const id = getRowId(selectedItem.value)

    const res = await fetch(`/api/form-legalisasi/${id}`, {
      method: 'PUT',
      headers,
      body: JSON.stringify({
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

const openDetailDialog = item => {
  detailItem.value = item
  showDetailDialog.value = true
}

const closeDetailDialog = () => {
  showDetailDialog.value = false
  detailItem.value = null
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
    const headers = buildHeaders()

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
    const headers = buildHeaders(true)

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

const filteredLegalisasi = computed(() => {
  return daftarLegalisasi.value.filter(item => {
    const hasResi = !!item?.noresi
    const hasBiaya = item?.biaya_legalisasi !== null && item?.biaya_legalisasi !== undefined && item?.biaya_legalisasi !== ''
    const isApprovedRow = !!item?.tgl_dikirim

    if (filterResi.value === 'yes' && !hasResi) return false
    if (filterResi.value === 'no' && hasResi) return false

    if (filterBiaya.value === 'yes' && !hasBiaya) return false
    if (filterBiaya.value === 'no' && hasBiaya) return false

    if (filterApprove.value === 'yes' && !isApprovedRow) return false
    if (filterApprove.value === 'no' && isApprovedRow) return false

    return true
  })
})

const applyFilters = () => {
  filterResi.value = pendingFilterResi.value
  filterBiaya.value = pendingFilterBiaya.value
  filterApprove.value = pendingFilterApprove.value
}

onMounted(fetchData)

watch(successMessage, value => {
  if (!value) return
  if (successTimer) clearTimeout(successTimer)
  successTimer = setTimeout(() => {
    successMessage.value = ''
  }, 30000)
})

watch(itemsPerPageOptions, options => {
  if (!Array.isArray(options) || options.length === 0) return
  if (!options.includes(itemsPerPage.value))
    itemsPerPage.value = options[0]
})

watch(errorMessage, value => {
  if (!value) return
  if (errorTimer) clearTimeout(errorTimer)
  errorTimer = setTimeout(() => {
    errorMessage.value = ''
  }, 30000)
})

onBeforeUnmount(() => {
  if (successTimer) clearTimeout(successTimer)
  if (errorTimer) clearTimeout(errorTimer)
})
</script>

<template>
  <VCard>
    <VCardTitle class="admin-card-title">
      Legalisasi
    </VCardTitle>
    <div class="legalisasi-filters">
      <div class="filter-item">
        <span>Status Biaya</span>
        <VSelect
          v-model="pendingFilterBiaya"
          :items="[
            { title: 'Semua', value: 'all' },
            { title: 'Sudah', value: 'yes' },
            { title: 'Belum', value: 'no' },
          ]"
          density="compact"
          style="max-inline-size: 140px;"
        />
      </div>
      <div class="filter-item">
        <span>Status Resi</span>
        <VSelect
          v-model="pendingFilterResi"
          :items="[
            { title: 'Semua', value: 'all' },
            { title: 'Sudah', value: 'yes' },
            { title: 'Belum', value: 'no' },
          ]"
          density="compact"
          style="max-inline-size: 140px;"
        />
      </div>
      <div class="filter-item">
        <span>Status Approve</span>
        <VSelect
          v-model="pendingFilterApprove"
          :items="[
            { title: 'Semua', value: 'all' },
            { title: 'Sudah', value: 'yes' },
            { title: 'Belum', value: 'no' },
          ]"
          density="compact"
          style="max-inline-size: 160px;"
        />
      </div>
      <VBtn
        class="filter-apply-btn"
        @click="applyFilters"
      >
        Terapkan
      </VBtn>
    </div>

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
            <th>Dokumen</th>
            <th>Jumlah</th>
            <th>Biaya</th>
            <th>No. Resi</th>
            <th>Tgl Dikirim</th>
            <th class="text-center">
              Aksi
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td
              colspan="8"
              class="text-center py-6"
            >
              Memuat data...
            </td>
          </tr>
          <tr
            v-else-if="!filteredLegalisasi.length"
            class="text-center"
          >
            <td colspan="8">
              Tidak ada data pengajuan legalisasi.
            </td>
          </tr>
          <tr
            v-for="(m, i) in filteredLegalisasi.slice(0, itemsPerPage)"
            v-else
            :key="m.kdlegalisasi || i"
            :class="{ 'row-received': m.status_penerimaan === 'received' }"
          >
            <td data-label="No">
              {{ i + 1 }}
            </td>
            <td data-label="Nama Mahasiswa">
              {{ m.nama_mahasiswa ?? '-' }}
            </td>
            <td data-label="Dokumen">
              {{ m.dokumen ?? '-' }}
            </td>
            <td data-label="Jumlah">
              {{ m.jumlah_legalisasi ?? '-' }}
            </td>
            <td data-label="Biaya">
              <VBtn
                size="small"
                class="resi-btn"
                :loading="approvingId === getRowId(m)"
                :disabled="m.biaya_legalisasi !== null && m.biaya_legalisasi !== undefined && m.biaya_legalisasi !== '' || approvingId !== null"
                @click="openBiayaDialog(m)"
              >
                Biaya
              </VBtn>
            </td>
            <td data-label="No. Resi">
              <VBtn
                size="small"
                class="resi-btn"
                :loading="approvingId === getRowId(m)"
                :disabled="!m.biaya_legalisasi || !!m.noresi || approvingId !== null"
                @click="openResiDialog(m)"
              >
                Resi
              </VBtn>
            </td>
            <td data-label="Tgl Dikirim">
              {{ m.tgl_dikirim ?? '-' }}
            </td>
            <td
              data-label="Aksi"
              class="text-center"
            >
              <div class="d-flex justify-center">
                <VBtn
                  icon
                  variant="text"
                  color="info"
                  class="action-btn"
                  @click="openDetailDialog(m)"
                >
                  <VIcon
                    icon="ri-information-line"
                    size="20"
                  />
                </VBtn>
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
                  :disabled="approvingId !== null || isApproved(m)"
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
        :items="itemsPerPageOptions"
        density="compact"
        style="max-inline-size: 100px;"
      />
    </div>
  </VCard>
  <VDialog
    v-model="showApproveDialog"
    max-width="800"
    class="approve-dialog"
  >
    <VCard class="approve-card">
      <VCardTitle class="approve-title">
        Approve Legalisasi
      </VCardTitle>
      <VCardText>
        <div class="approve-subtitle">
          Ringkasan Pengajuan
        </div>
        <div
          v-if="selectedItem"
          class="summary approve-summary"
        >
          <div class="summary-row">
            <span class="summary-label">NIM Mahasiswa</span><span class="summary-value">{{ selectedItem.nim ?? '-' }}</span>
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
        </div>
        <VTextField
          v-model="approveForm.tgl_dikirim"
          label="Tanggal Dikirim"
          type="date"
          variant="outlined"
          density="compact"
          class="mt-4 approve-date"
        />
      </VCardText>
      <VCardActions class="approve-actions">
        <VBtn
          variant="tonal"
          color="grey"
          class="approve-btn"
          @click="closeApprove"
        >
          Kembali
        </VBtn>
        <VBtn
          color="teal"
          variant="tonal"
          class="approve-btn approve-btn-primary"
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
    max-width="500"
    class="delete-dialog"
  >
    <VCard class="delete-card">
      <VCardTitle class="delete-title">
        Hapus Pengajuan Legalisasi
      </VCardTitle>
      <VCardText>
        <div class="delete-subtitle">
          Yakin ingin menghapus pengajuan ini?
        </div>
        <div
          v-if="deleteCandidate"
          class="summary delete-summary"
        >
          <div class="summary-row">
            <span class="summary-label">NIM Mahasiswa</span><span class="summary-value">{{ deleteCandidate.nim ?? '-' }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Nama Mahasiswa</span><span class="summary-value">{{ deleteCandidate.nama_mahasiswa ?? '-' }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Jumlah</span><span class="summary-value">{{ deleteCandidate.jumlah_legalisasi }}</span>
          </div>
        </div>
      </VCardText>
      <VCardActions class="delete-actions">
        <VBtn
          variant="tonal"
          color="grey"
          class="delete-btn"
          :disabled="deletingId !== null"
          @click="closeDeleteDialog"
        >
          Batal
        </VBtn>
        <VBtn
          color="error"
          variant="tonal"
          class="delete-btn delete-btn-primary"
          :loading="deletingId !== null"
          @click="confirmDelete"
        >
          Hapus
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>

  <VDialog
    v-model="showDetailDialog"
    max-width="560"
    class="detail-dialog"
  >
    <VCard class="detail-card">
      <VCardTitle class="detail-title">
        Detail Legalisasi
      </VCardTitle>
      <VCardText>
        <div
          v-if="detailItem"
          class="summary detail-summary"
        >
          <div class="summary-row">
            <span class="summary-label">NIM Mahasiswa</span><span class="summary-value">{{ detailItem.nim ?? '-' }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Nama Mahasiswa</span><span class="summary-value">{{ detailItem.nama_mahasiswa ?? '-' }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Dokumen</span><span class="summary-value">{{ detailItem.dokumen ?? '-' }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Jumlah</span><span class="summary-value">{{ detailItem.jumlah_legalisasi ?? '-' }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Biaya</span><span class="summary-value">{{ detailItem.biaya_legalisasi ?? '-' }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Alamat</span><span class="summary-value">{{ detailItem.alamat_kirim ?? '-' }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Nama Penerima</span><span class="summary-value">{{ detailItem.nama_penerima_legalisasi ?? '-' }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">No Telp Penerima</span><span class="summary-value">{{ detailItem.telp_penerima ?? '-' }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">No. Resi</span><span class="summary-value">{{ detailItem.noresi ?? '-' }}</span>
          </div>
          <div class="summary-row">
            <span class="summary-label">Tgl Dikirim</span><span class="summary-value">{{ detailItem.tgl_dikirim ?? '-' }}</span>
          </div>
        </div>
      </VCardText>
      <VCardActions class="detail-actions">
        <VSpacer />
        <VBtn
          variant="tonal"
          color="grey"
          class="detail-btn"
          @click="closeDetailDialog"
        >
          Tutup
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>

  <VDialog
    v-model="showResiDialog"
    max-width="420"
  >
    <VCard>
      <VCardTitle>Input Nomor Resi</VCardTitle>
      <VCardText>
        <VTextField
          v-model="resiValue"
          label="Nomor Resi"
          inputmode="text"
          pattern="[A-Za-z0-9]+"
          variant="outlined"
          autofocus
        />
      </VCardText>
      <VCardActions class="justify-end">
        <VBtn
          variant="tonal"
          color="grey"
          @click="closeResiDialog"
        >
          Batal
        </VBtn>
        <VBtn
          color="primary"
          :disabled="!resiValue"
          :loading="approvingId !== null"
          @click="saveResi"
        >
          Simpan
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>

  <VDialog
    v-model="showBiayaDialog"
    max-width="420"
  >
    <VCard>
      <VCardTitle>Input Biaya</VCardTitle>
      <VCardText>
        <VTextField
          v-model="biayaValue"
          label="Biaya (Rp)"
          type="text"
          inputmode="numeric"
          pattern="\d*"
          variant="outlined"
          autofocus
          @input="onBiayaInput"
        />
      </VCardText>
      <VCardActions class="justify-end">
        <VBtn
          variant="tonal"
          color="grey"
          @click="closeBiayaDialog"
        >
          Batal
        </VBtn>
        <VBtn
          color="primary"
          :disabled="!biayaValue"
          :loading="approvingId !== null"
          @click="saveBiaya"
        >
          Simpan
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>

<style scoped>
.table-wrapper {
  overflow-x: auto;
}

.table-wrapper .text-center {
  text-align: center;
}

.admin-card-title {
  border-radius: 4px 4px 0 0;
  background:
    linear-gradient(
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
  box-shadow: 0 1px 3px rgba(0, 0, 0, 5%);
  color: rgb(var(--v-theme-on-surface));
}

.legalisasi-table thead {
  background:
    linear-gradient(
      to right,
      rgba(var(--v-theme-on-surface), 0.04),
      rgba(var(--v-theme-on-surface), 0.06)
    );
  box-shadow: 0 2px 4px rgba(0, 0, 0, 5%);
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
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 5%);
  transform: translateY(-1px);
}

.legalisasi-table tbody tr.row-received td {
  background-color: rgba(27, 196, 125, 18%);
  color: #0f5132;
  font-weight: 600;
}

.v-theme--dark .legalisasi-table tbody tr.row-received td {
  color: #fff;
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

.legalisasi-filters {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 16px;
  margin-block: 12px 20px;
  padding-inline: 24px;
}

.filter-item {
  display: flex;
  align-items: center;
  gap: 8px;
}

.filter-apply-btn {
  border-radius: 8px;
  background: #17a2a6 !important;
  color: #fff !important;
  font-weight: 600;
  margin-inline-start: auto;
  min-inline-size: 120px;
  text-transform: none;
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

.resi-btn {
  background: #17a2a6 !important;
  color: #fff !important;
  font-weight: 600;
  min-inline-size: 72px;
  text-transform: none;
}

.resi-btn:disabled,
.filter-apply-btn:disabled {
  background: rgba(23, 162, 166, 35%) !important;
  color: rgba(255, 255, 255, 85%) !important;
}

.approve-card {
  border-radius: 22px;
  padding-block: 12px 8px;
  padding-inline: 8px;
}

.approve-title {
  color: #1bc47d;
  font-size: 1.4rem;
  font-weight: 700;
  padding-block: 12px 4px;
  text-align: center;
}

.approve-subtitle {
  color: rgba(var(--v-theme-on-surface), 0.6);
  font-size: 0.95rem;
  font-weight: 600;
  margin-block-end: 16px;
  text-align: center;
}

.approve-summary {
  margin-inline: auto;
  max-inline-size: 520px;
}

.approve-actions {
  display: flex;
  justify-content: center;
  gap: 12px;
  padding-block-end: 16px;
}

.approve-btn {
  border-radius: 8px;
  font-weight: 600;
  min-inline-size: 140px;
  text-transform: none;
}

.approve-btn-primary {
  background: #1bc47d !important;
  color: #fff !important;
}

.approve-date {
  margin-inline: auto;
  max-inline-size: 520px;
}

.approve-date :deep(.v-field__append-inner) {
  margin-inline-start: auto;
}

.approve-date :deep(.v-field__input) {
  position: relative;
}

.approve-date :deep(input[type="date"]) {
  padding-inline-end: 40px;
}

.approve-date :deep(input[type="date"]::-webkit-calendar-picker-indicator) {
  position: absolute;
  margin: 0;
  inset-inline-end: 12px;
}

.delete-card {
  border-radius: 22px;
  padding-block: 12px 8px;
  padding-inline: 8px;
}

.delete-title {
  color: #e63946;
  font-size: 1.35rem;
  font-weight: 700;
  padding-block: 12px 4px;
  text-align: center;
}

.delete-subtitle {
  color: rgba(var(--v-theme-on-surface), 0.6);
  font-size: 0.95rem;
  font-weight: 600;
  margin-block-end: 16px;
  text-align: center;
}

.delete-summary {
  margin-inline: auto;
  max-inline-size: 520px;
}

.delete-actions {
  display: flex;
  justify-content: center;
  gap: 12px;
  padding-block-end: 16px;
}

.delete-btn {
  border-radius: 8px;
  font-weight: 600;
  min-inline-size: 140px;
  text-transform: none;
}

.delete-btn-primary {
  background: #e63946 !important;
  color: #fff !important;
}

.detail-card {
  border-radius: 22px;
  padding-block: 12px 8px;
  padding-inline: 8px;
}

.detail-title {
  color: #17a2a6;
  font-size: 1.35rem;
  font-weight: 700;
  padding-block: 12px 4px;
  text-align: center;
}

.detail-summary {
  margin-inline: auto;
  max-inline-size: 520px;
}

.detail-actions {
  display: flex;
  justify-content: center;
  gap: 12px;
  padding-block-end: 16px;
}

.detail-btn {
  border-radius: 8px;
  font-weight: 600;
  min-inline-size: 140px;
  text-transform: none;
}

@media (max-width: 960px) {
  .legalisasi-table thead {
    display: none;
  }

  .legalisasi-table tbody tr {
    display: grid;
    padding: 12px;
    gap: 8px;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
  }

  .legalisasi-table tbody td {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    background-color: rgba(var(--v-theme-surface), 0.9);
    gap: 4px;
  }

  .legalisasi-table tbody td::before {
    color: rgba(var(--v-theme-on-surface), 0.7);
    content: attr(data-label);
    font-size: 12px;
    font-weight: 600;
    text-transform: uppercase;
  }
}

@media (prefers-color-scheme: dark) {
  .legalisasi-table {
    box-shadow: 0 1px 3px rgba(0, 0, 0, 25%);
  }

  .legalisasi-table thead {
    background: linear-gradient(to right, #0f0f0f, #141414);
  }

  .table-footer {
    background-color: rgba(var(--v-theme-surface), 0.92);
  }
}
</style>
