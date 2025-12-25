<script setup>
import { computed, onMounted, ref } from 'vue'

const itemsPerPage = ref(5)
const filterStatus = ref('all')
const loading = ref(false)
const errorMessage = ref('')
const mahasiswaList = ref([])
const deletingId = ref(null)
const showDeleteDialog = ref(false)
const deleteTarget = ref(null)
const approvingId = ref(null)
const showApproveDialog = ref(false)
const approveTarget = ref(null)

const storageBase = `${window.location.origin}/storage/`
const fileUrl = path => (path ? `${storageBase}${path}` : '')
const isImage = path => /\.(png|jpe?g|gif|bmp|webp)$/i.test(path || '')
const showPreview = ref(false)
const previewUrl = ref('')
const previewTitle = ref('Pratinjau')
const previewDocKey = ref('')
const previewIsImage = ref(true)
const previewItem = ref(null)
const formatDate = date => date ? new Date(date).toLocaleDateString('id-ID') : '-'
const updatingDocStatus = ref(false)

const showCommentDialog = ref(false)
const selectedItem = ref(null)
const newComment = ref('')
const commentText = ref('')
const savingComment = ref(false)
const filteredList = computed(() => {
  const list = mahasiswaList.value
  if (filterStatus.value === 'all') return list

  return list.filter(item => {
    const statuses = [item.status_foto, item.status_ijazah, item.status_ktp]
    const allApproved = statuses.length && statuses.every(status => status === 'approved')
    const hasRevision = statuses.some(status => status === 'revision')

    if (filterStatus.value === 'approved')
      return item.is_validate || allApproved
    if (filterStatus.value === 'revision')
      return hasRevision

    return !item.is_validate && !allApproved && !hasRevision
  })
})

const fetchData = async () => {
  loading.value = true
  errorMessage.value = ''
  try {
    const headers = {
      Accept: 'application/json',
    }

    const token = sessionStorage.getItem('jwt_token')
    if (token)
      headers.Authorization = `Bearer ${token}`

    const res = await fetch('/api/pra-yudisium', { headers })
    if (!res.ok) {
      throw new Error(`Gagal memuat data (${res.status})`)
    }
    const json = await res.json()
    if (json.success && Array.isArray(json.data)) {
      mahasiswaList.value = json.data
    } else {
      throw new Error(json.message || 'Response tidak valid')
    }
  } catch (err) {
    errorMessage.value = err.message || 'Gagal memuat data'
  } finally {
    loading.value = false
  }
}

const editItem = item => {
  console.log('Edit:', item)
}

const openApproveDialog = item => {
  if (!item) return
  approveTarget.value = item
  showApproveDialog.value = true
}

const closeApproveDialog = () => {
  showApproveDialog.value = false
  approveTarget.value = null
}

const approveItem = async () => {
  if (!approveTarget.value || approvingId.value) return

  approvingId.value = approveTarget.value.kdprayudisium
  errorMessage.value = ''

  try {
    const headers = {
      Accept: 'application/json',
    }

    const token = sessionStorage.getItem('jwt_token')
    if (token)
      headers.Authorization = `Bearer ${token}`

    const res = await fetch(`/api/pra-yudisium/${approveTarget.value.kdprayudisium}/approve`, {
      method: 'PATCH',
      headers,
    })

    const json = await res.json().catch(() => ({}))
    if (!res.ok || json.success === false)
      throw new Error(json.message || 'Gagal menyetujui data')

    if (json.data) {
      const normalized = {
        ...json.data,
        status_foto: json.data.status_foto || 'approved',
        status_ijazah: json.data.status_ijazah || 'approved',
        status_ktp: json.data.status_ktp || 'approved',
      }
      mahasiswaList.value = mahasiswaList.value.map(row => row.kdprayudisium === json.data.kdprayudisium
        ? { ...row, ...normalized }
        : row)
    }

    closeApproveDialog()
  } catch (err) {
    errorMessage.value = err.message || 'Gagal menyetujui data'
  } finally {
    approvingId.value = null
  }
}

const openDeleteDialog = item => {
  if (!item) return
  deleteTarget.value = item
  showDeleteDialog.value = true
}

const closeDeleteDialog = () => {
  showDeleteDialog.value = false
  deleteTarget.value = null
}

const deleteItem = async () => {
  if (!deleteTarget.value || deletingId.value) return

  deletingId.value = deleteTarget.value.kdprayudisium
  errorMessage.value = ''

  try {
    const headers = {
      Accept: 'application/json',
    }

    const token = sessionStorage.getItem('jwt_token')
    if (token)
      headers.Authorization = `Bearer ${token}`

    const res = await fetch(`/api/pra-yudisium/${deleteTarget.value.kdprayudisium}`, {
      method: 'DELETE',
      headers,
    })

    const json = await res.json().catch(() => ({}))
    if (!res.ok || json.success === false)
      throw new Error(json.message || 'Gagal menghapus data')

    mahasiswaList.value = mahasiswaList.value.filter(row => row.kdprayudisium !== deleteTarget.value.kdprayudisium)
    closeDeleteDialog()
  } catch (err) {
    errorMessage.value = err.message || 'Gagal menghapus data'
  } finally {
    deletingId.value = null
  }
}

const openComments = item => {
  selectedItem.value = item
  commentText.value = item.comment || ''
  newComment.value = item.comment || ''
  showCommentDialog.value = true
}

const saveComment = async () => {
  if (!selectedItem.value) return
  savingComment.value = true
  try {
    const headers = {
      Accept: 'application/json',
      'Content-Type': 'application/json',
    }
    const token = sessionStorage.getItem('jwt_token')
    if (token)
      headers.Authorization = `Bearer ${token}`

    const res = await fetch(`/api/pra-yudisium/${selectedItem.value.kdprayudisium}/comment`, {
      method: 'PATCH',
      headers,
      body: JSON.stringify({ comment: newComment.value }),
    })

    const json = await res.json()
    if (!res.ok || json.success === false)
      throw new Error(json.message || 'Gagal menyimpan komentar')

    commentText.value = newComment.value
    selectedItem.value.comment = newComment.value
    // perbarui di list utama
    mahasiswaList.value = mahasiswaList.value.map(row => row.kdprayudisium === selectedItem.value.kdprayudisium
      ? { ...row, comment: newComment.value }
      : row)
    showCommentDialog.value = false
  } catch (err) {
    errorMessage.value = err.message || 'Gagal menyimpan komentar'
  } finally {
    savingComment.value = false
  }
}

const openPreview = (item, path, title, docKey) => {
  const url = fileUrl(path)
  if (!url) return
  previewUrl.value = url
  previewTitle.value = title || 'Pratinjau'
  previewDocKey.value = docKey || ''
  previewIsImage.value = isImage(path)
  previewItem.value = item || null
  showPreview.value = true
}

const isFullyApproved = item => {
  if (!item) return false
  if (item.is_validate) return true

  return item.status_foto === 'approved'
    && item.status_ijazah === 'approved'
    && item.status_ktp === 'approved'
}

const updateDocumentStatus = async (status) => {
  if (!previewItem.value || !previewDocKey.value || updatingDocStatus.value) return

  updatingDocStatus.value = true
  errorMessage.value = ''

  const statusPayload = {}
  if (previewDocKey.value === 'foto')
    statusPayload.status_foto = status
  if (previewDocKey.value === 'ijazah')
    statusPayload.status_ijazah = status
  if (previewDocKey.value === 'ktp')
    statusPayload.status_ktp = status

  try {
    const headers = {
      Accept: 'application/json',
      'Content-Type': 'application/json',
    }

    const token = sessionStorage.getItem('jwt_token')
    if (token)
      headers.Authorization = `Bearer ${token}`

    const res = await fetch(`/api/pra-yudisium/${previewItem.value.kdprayudisium}/document-status`, {
      method: 'PATCH',
      headers,
      body: JSON.stringify(statusPayload),
    })

    const json = await res.json().catch(() => ({}))
    if (!res.ok || json.success === false)
      throw new Error(json.message || 'Gagal memperbarui status dokumen')

    if (json.data) {
      mahasiswaList.value = mahasiswaList.value.map(row => row.kdprayudisium === json.data.kdprayudisium
        ? { ...row, ...json.data }
        : row)
      previewItem.value = { ...previewItem.value, ...json.data }
    }
  } catch (err) {
    errorMessage.value = err.message || 'Gagal memperbarui status dokumen'
  } finally {
    updatingDocStatus.value = false
  }
}

const getPreviewStatus = () => {
  if (!previewItem.value || !previewDocKey.value) return ''
  if (previewDocKey.value === 'foto') return previewItem.value.status_foto || ''
  if (previewDocKey.value === 'ijazah') return previewItem.value.status_ijazah || ''
  if (previewDocKey.value === 'ktp') return previewItem.value.status_ktp || ''
  return ''
}

const statusLabel = status => {
  if (status === 'approved') return 'Approved'
  if (status === 'revision') return 'Revision'
  return 'Submitted'
}

const statusClass = status => {
  if (status === 'approved') return 'status-chip status-approved'
  if (status === 'revision') return 'status-chip status-revision'
  return 'status-chip status-submitted'
}

onMounted(fetchData)
</script>

<template>
  <VCard>
    <VCardTitle class="admin-card-title">
      Pra Yudisium
    </VCardTitle>


    <VAlert
      v-if="errorMessage"
      type="error"
      variant="tonal"
      class="mb-4"
    >
      {{ errorMessage }}
    </VAlert>

    <VTable class="pra-yudisium-table w-auto">
      <thead>
        <tr>
          <th>No</th>
          <th>Nama Mahasiswa</th>
          <th>Pas Foto</th>
          <th>Ijazah</th>
          <th>KTP</th>
          <th>Tanggal Pengajuan</th>
          <th class="text-center">
            Aksi
          </th>
        </tr>
      </thead>
      <tbody>
        <tr v-if="loading">
          <td
            colspan="7"
            class="text-center py-6"
          >
            Memuat data...
          </td>
        </tr>
        <tr v-else-if="!filteredList.length">
          <td
            colspan="7"
            class="text-center py-6"
          >
            Tidak ada data pra yudisium.
          </td>
        </tr>
        <tr
          v-else
          v-for="(m, i) in filteredList.slice(0, itemsPerPage)"
          :key="m.kdprayudisium"
        >
          <td>{{ i + 1 }}</td>
          <td>{{ m.namalengkap || '-' }}</td>
          <td>
            <div class="doc-cell">
              <template v-if="isImage(m.berkas_foto_ijazah)">
                <img
                  :src="fileUrl(m.berkas_foto_ijazah)"
                  alt="Pas Foto"
                  class="foto-preview"
                  role="button"
                  @click="openPreview(m, m.berkas_foto_ijazah, 'Pas Foto', 'foto')"
                >
              </template>
              <template v-else>
                <VBtn
                  variant="text"
                  size="small"
                  @click="openPreview(m, m.berkas_foto_ijazah, 'Pas Foto', 'foto')"
                >
                  Lihat
                </VBtn>
              </template>
              <div :class="statusClass(m.status_foto)">
                {{ statusLabel(m.status_foto) }}
              </div>
            </div>
          </td>
          <td>
            <div class="doc-cell">
              <template v-if="isImage(m.berkas_ijazah_terakhir)">
                <img
                  :src="fileUrl(m.berkas_ijazah_terakhir)"
                  alt="Ijazah"
                  class="dokumen-preview"
                  role="button"
                  @click="openPreview(m, m.berkas_ijazah_terakhir, 'Ijazah', 'ijazah')"
                >
              </template>
              <template v-else>
                <VBtn
                  variant="text"
                  size="small"
                  @click="openPreview(m, m.berkas_ijazah_terakhir, 'Ijazah', 'ijazah')"
                >
                  Lihat
                </VBtn>
              </template>
              <div :class="statusClass(m.status_ijazah)">
                {{ statusLabel(m.status_ijazah) }}
              </div>
            </div>
          </td>
          <td>
            <div class="doc-cell">
              <template v-if="isImage(m.berkas_kk_ktp)">
                <img
                  :src="fileUrl(m.berkas_kk_ktp)"
                  alt="KTP"
                  class="dokumen-preview"
                  role="button"
                  @click="openPreview(m, m.berkas_kk_ktp, 'KTP', 'ktp')"
                >
              </template>
              <template v-else>
                <VBtn
                  variant="text"
                  size="small"
                  @click="openPreview(m, m.berkas_kk_ktp, 'KTP', 'ktp')"
                >
                  Lihat
                </VBtn>
              </template>
              <div :class="statusClass(m.status_ktp)">
                {{ statusLabel(m.status_ktp) }}
              </div>
            </div>
          </td>
          <td>{{ formatDate(m.create_at) }}</td>
          <td class="text-center">
            <div class="d-flex justify-center">
              <VBtn
                icon
                variant="text"
                color="success"
                :class="['action-btn', { 'action-btn-disabled': isFullyApproved(m) }]"
                :disabled="isFullyApproved(m)"
                :loading="approvingId === m.kdprayudisium"
                @click="openApproveDialog(m)"
              >
                <VIcon
                  icon="ri-check-line"
                  size="20"
                />
              </VBtn>
              <VBtn
                icon
                variant="text"
                color="secondary"
                class="action-btn"
                @click="openComments(m)"
              >
                <VIcon
                  icon="ri-chat-3-line"
                  size="20"
                />
              </VBtn>
              <VBtn
                icon
                variant="text"
                color="error"
                :class="['action-btn', { 'action-btn-disabled': isFullyApproved(m) }]"
                :loading="deletingId === m.kdprayudisium"
                :disabled="deletingId !== null || isFullyApproved(m)"
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
    <div class="table-footer">
      <div class="table-footer-left">
        <span>Status</span>
        <VSelect
          v-model="filterStatus"
          :items="[
            { title: 'Semua', value: 'all' },
            { title: 'Approved', value: 'approved' },
            { title: 'Revisi', value: 'revision' },
            { title: 'Submitted', value: 'submitted' },
          ]"
          density="compact"
          style="max-inline-size: 160px;"
        />
      </div>
      <div class="table-footer-right">
        <span>Showing per Page</span>
        <VSelect
          v-model="itemsPerPage"
          :items="[5, 10, 20]"
          density="compact"
          style="max-inline-size: 80px;"
        />
      </div>
    </div>

    <VDialog
      v-model="showPreview"
      max-width="600"
    >
      <VCard>
        <VCardTitle>{{ previewTitle }}</VCardTitle>
        <VCardText class="d-flex justify-center">
          <template v-if="previewIsImage">
            <VImg
              :src="previewUrl"
              max-width="540"
              cover
            />
          </template>
          <template v-else>
            <iframe
              :src="previewUrl"
              title="Dokumen"
              class="preview-frame"
            />
          </template>
        </VCardText>
        <VCardActions class="preview-actions">
          <div class="preview-actions-center">
            <VBtn
              icon
              variant="text"
              color="error"
              :loading="updatingDocStatus"
              :disabled="updatingDocStatus || getPreviewStatus() === 'approved'"
              @click="updateDocumentStatus('revision')"
            >
              <VIcon icon="ri-close-line" />
            </VBtn>
            <VBtn
              icon
              variant="text"
              color="success"
              :loading="updatingDocStatus"
              :disabled="updatingDocStatus || getPreviewStatus() === 'approved'"
              @click="updateDocumentStatus('approved')"
            >
              <VIcon icon="ri-check-line" />
            </VBtn>
          </div>
          <div class="preview-actions-right">
            <VBtn
              variant="text"
              color="primary"
              @click="showPreview = false"
            >
              Tutup
            </VBtn>
          </div>
        </VCardActions>
      </VCard>
    </VDialog>

    <VDialog
      v-model="showCommentDialog"
      max-width="800"
    >
      <VCard class="comment-card">
        <VCardTitle class="text-h6 font-weight-bold text-center">
          Komentar
        </VCardTitle>
        <VCardText>
          <div class="comment-input d-flex align-center mb-6">
            <div class="avatar-chip">A</div>
            <VTextarea
              v-model="newComment"
              placeholder="Tulis komentar..."
              auto-grow
              rows="1"
              max-rows="3"
              class="flex-grow-1 ms-4"
              variant="solo-filled"
            />
            <VBtn
              color="primary"
              class="ms-4"
              :loading="savingComment"
              @click="saveComment"
            >
              Kirim
            </VBtn>
          </div>

          <div
            v-if="commentText"
            class="comment-item d-flex mb-5"
          >
            <div class="avatar-chip">A</div>
            <div class="ms-4">
              <div class="d-flex align-center gap-2">
                <span class="author">Admin</span>
              </div>
              <div class="comment-text">
                {{ commentText }}
              </div>
            </div>
          </div>
        </VCardText>
        <VCardActions class="justify-end">
          <VBtn
            variant="text"
            color="primary"
            @click="showCommentDialog = false"
          >
            Tutup
          </VBtn>
        </VCardActions>
      </VCard>
    </VDialog>

    <VDialog
      v-model="showDeleteDialog"
      max-width="480"
    >
      <VCard>
        <VCardTitle class="text-h6">
          Hapus Data?
        </VCardTitle>
        <VCardText>
          <p class="mb-4">
            Data pra yudisium dengan kode <strong>{{ deleteTarget?.kdprayudisium }}</strong> akan dihapus.
            Tindakan ini tidak dapat dibatalkan.
          </p>
          <p class="text-medium-emphasis">
            Pastikan data sudah benar sebelum melanjutkan.
          </p>
        </VCardText>
        <VCardActions class="justify-end">
          <VBtn
            variant="text"
            color="default"
            @click="closeDeleteDialog"
          >
            Batal
          </VBtn>
          <VBtn
            color="error"
            :loading="!!deletingId"
            :disabled="!!deletingId"
            @click="deleteItem"
          >
            Hapus
          </VBtn>
        </VCardActions>
      </VCard>
    </VDialog>

    <VDialog
      v-model="showApproveDialog"
      max-width="480"
    >
      <VCard>
        <VCardTitle class="text-h6">
          Setujui Pengajuan?
        </VCardTitle>
        <VCardText>
          <p class="mb-4">
            Pengajuan pra yudisium dengan kode <strong>{{ approveTarget?.kdprayudisium }}</strong> akan disetujui.
          </p>
          <p class="text-medium-emphasis">
            Mahasiswa akan menerima notifikasi bahwa pengajuan sudah disetujui.
          </p>
        </VCardText>
        <VCardActions class="justify-end">
          <VBtn
            variant="text"
            color="default"
            @click="closeApproveDialog"
          >
            Tidak
          </VBtn>
          <VBtn
            color="success"
            :loading="!!approvingId"
            :disabled="!!approvingId"
            @click="approveItem"
          >
            Ya, Setujui
          </VBtn>
        </VCardActions>
      </VCard>
    </VDialog>
  </VCard>
</template>

<style scoped>
/* Card Header */
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

/* Table Styling */
.pra-yudisium-table {
  overflow: hidden;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.12);
  border-radius: 4px;
  background-color: rgb(var(--v-theme-surface));
  color: rgb(var(--v-theme-on-surface));
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.pra-yudisium-table thead {
  background: linear-gradient(
    to right,
    rgba(var(--v-theme-on-surface), 0.04),
    rgba(var(--v-theme-on-surface), 0.06)
  );
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.pra-yudisium-table thead th {
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

.pra-yudisium-table tbody td {
  background-color: rgba(var(--v-theme-surface), 0.9);
  border-block-end: 1px solid rgba(var(--v-theme-on-surface), 0.08);
  color: rgba(var(--v-theme-on-surface), 0.8);
  font-size: 0.875rem;
  padding-block: 1rem;
  padding-inline: 0.75rem;
  transition: all 0.2s ease;
  vertical-align: middle;
}

.pra-yudisium-table tbody tr:not(:last-child) td {
  border-block-end: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.pra-yudisium-table tbody tr:hover td {
  background-color: rgba(var(--v-theme-on-surface), 0.05);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
  transform: translateY(-1px);
}

/* Document Previews */
.foto-preview,
.dokumen-preview {
  border: 1px solid rgba(var(--v-theme-on-surface), 0.12);
  border-radius: 6px;
  block-size: 75px;
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
  cursor: pointer;
  inline-size: 60px;
  object-fit: cover;
  transition: all 0.2s ease;
}

.foto-preview:hover,
.dokumen-preview:hover {
  box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
  transform: scale(1.05);
}

/* Action Buttons */
.action-btn {
  margin-block: 0;
  margin-inline: 2px;
  opacity: 0.8;
  transition: all 0.2s ease;
}

.action-btn:hover {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 10%);
  opacity: 1;
  transform: translateY(-2px);
}

.action-btn-disabled {
  color: rgba(var(--v-theme-on-surface), 0.35) !important;
  pointer-events: none;
}

/* Table Footer & Pagination */
.table-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem;
  border-radius: 0 0 4px 4px;
  background-color: rgba(var(--v-theme-surface), 0.9);
  border-block-start: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.table-footer-left,
.table-footer-right {
  display: flex;
  align-items: center;
  gap: 12px;
}

/* Empty State */
.empty-state {
  color: rgba(var(--v-theme-on-surface), 0.6);
  padding-block: 3rem;
  padding-inline: 1rem;
  text-align: center;
}

.empty-state-icon {
  color: rgba(var(--v-theme-on-surface), 0.2);
  font-size: 4rem;
  margin-block-end: 1rem;
}

/* Loading State */
.loading-overlay {
  position: absolute;
  z-index: 10;
  display: flex;
  align-items: center;
  justify-content: center;
  border-radius: 4px;
  background: rgba(var(--v-theme-surface), 0.85);
  inset: 0;
}

/* Responsive Adjustments */
@media (max-width: 960px) {
  .pra-yudisium-table {
    display: block;
    -webkit-overflow-scrolling: touch;
    overflow-x: auto;
  }

  .v-card-title,
  .v-card-subtitle {
    padding-block: 0.75rem !important;
    padding-inline: 1rem !important;
  }
}

/* Animation */
@keyframes fadeIn {
  from { opacity: 0; transform: translateY(10px); }
  to { opacity: 1; transform: translateY(0); }
}

.v-table {
  animation: fadeIn 0.3s ease-out;
}

@media (prefers-color-scheme: dark) {
  .pra-yudisium-table {
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
  }

  .pra-yudisium-table thead {
    background: linear-gradient(to right, #0f0f0f, #141414);
  }

  .table-footer {
    background-color: rgba(var(--v-theme-surface), 0.92);
  }

  .loading-overlay {
    background: rgba(0, 0, 0, 0.4);
  }
}

/* Comment Modal */
.comment-card {
  background-color: rgb(var(--v-theme-surface));
  color: rgb(var(--v-theme-on-surface));
}

.avatar-chip {
  inline-size: 44px;
  block-size: 44px;
  border-radius: 50%;
  background-color: rgb(var(--v-theme-primary));
  color: rgb(var(--v-theme-on-primary));
  display: inline-flex;
  align-items: center;
  justify-content: center;
  font-weight: 700;
}

.comment-input .v-textarea {
  background-color: rgba(var(--v-theme-on-surface), 0.03);
  border-radius: 8px;
}

.comment-item .author {
  font-weight: 700;
}

.comment-item .time {
  font-size: 12px;
  color: rgba(var(--v-theme-on-surface), 0.7);
}

.comment-text {
  margin-top: 6px;
  line-height: 1.5;
}

.preview-frame {
  border: 1px solid rgba(var(--v-theme-on-surface), 0.12);
  border-radius: 6px;
  block-size: 520px;
  inline-size: 100%;
}

.doc-status {
  color: rgba(var(--v-theme-on-surface), 0.7);
  font-size: 0.85rem;
}

.status-chip {
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  font-size: 0.72rem;
  font-weight: 600;
  padding-block: 2px;
  padding-inline: 8px;
  text-transform: uppercase;
}

.status-approved {
  background: rgba(27, 196, 125, 0.15);
  color: #1bc47d;
}

.status-revision {
  background: rgba(230, 57, 70, 0.15);
  color: #e63946;
}

.status-submitted {
  background: rgba(var(--v-theme-on-surface), 0.08);
  color: rgba(var(--v-theme-on-surface), 0.7);
}

.doc-cell {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
}

.preview-actions {
  display: flex;
  align-items: center;
  justify-content: center;
  position: relative;
  min-height: 48px;
}

.preview-actions-center {
  position: absolute;
  left: 50%;
  transform: translateX(-50%);
  display: flex;
  gap: 12px;
}

.preview-actions-right {
  margin-left: auto;
}
</style>
