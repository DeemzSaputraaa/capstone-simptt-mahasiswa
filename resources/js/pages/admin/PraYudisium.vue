<script setup>
import { onMounted, ref } from 'vue'

const itemsPerPage = ref(5)
const loading = ref(false)
const errorMessage = ref('')
const mahasiswaList = ref([])
const deletingId = ref(null)
const showDeleteDialog = ref(false)
const deleteTarget = ref(null)

const storageBase = `${window.location.origin}/storage/`
const fileUrl = path => (path ? `${storageBase}${path}` : '')
const isImage = path => /\.(png|jpe?g|gif|bmp|webp)$/i.test(path || '')
const showPreview = ref(false)
const previewUrl = ref('')
const previewTitle = ref('Pratinjau')
const formatDate = date => date ? new Date(date).toLocaleDateString('id-ID') : '-'

const showCommentDialog = ref(false)
const selectedItem = ref(null)
const newComment = ref('')
const commentText = ref('')
const savingComment = ref(false)

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

const openPreview = (path, title) => {
  const url = fileUrl(path)
  if (!url) return
  previewUrl.value = url
  previewTitle.value = title || 'Pratinjau'
  showPreview.value = true
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
          <th>Kode</th>
          <th>NIK</th>
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
        <tr
          v-for="(m, i) in mahasiswaList.slice(0, itemsPerPage)"
          :key="m.kdprayudisium"
        >
          <td>{{ i + 1 }}</td>
          <td>{{ m.kdprayudisium }}</td>
          <td>{{ m.nim || m.kdmahasiswa }}</td>
          <td>
            <template v-if="isImage(m.berkas_foto_ijazah)">
              <img
                :src="fileUrl(m.berkas_foto_ijazah)"
                alt="Pas Foto"
                class="foto-preview"
                role="button"
                @click="openPreview(m.berkas_foto_ijazah, 'Pas Foto')"
              >
            </template>
            <template v-else>
              <VBtn
                variant="text"
                size="small"
                :href="fileUrl(m.berkas_foto_ijazah)"
                target="_blank"
              >
                Lihat
              </VBtn>
            </template>
          </td>
          <td>
            <template v-if="isImage(m.berkas_ijazah_terakhir)">
              <img
                :src="fileUrl(m.berkas_ijazah_terakhir)"
                alt="Ijazah"
                class="dokumen-preview"
                role="button"
                @click="openPreview(m.berkas_ijazah_terakhir, 'Ijazah')"
              >
            </template>
            <template v-else>
              <VBtn
                variant="text"
                size="small"
                :href="fileUrl(m.berkas_ijazah_terakhir)"
                target="_blank"
              >
                Lihat
              </VBtn>
            </template>
          </td>
          <td>
            <template v-if="isImage(m.berkas_kk_ktp)">
              <img
                :src="fileUrl(m.berkas_kk_ktp)"
                alt="KTP"
                class="dokumen-preview"
                role="button"
                @click="openPreview(m.berkas_kk_ktp, 'KTP')"
              >
            </template>
            <template v-else>
              <VBtn
                variant="text"
                size="small"
                :href="fileUrl(m.berkas_kk_ktp)"
                target="_blank"
              >
                Lihat
              </VBtn>
            </template>
          </td>
          <td>{{ formatDate(m.create_at) }}</td>
          <td class="text-center">
            <div class="d-flex justify-center">
              <VBtn
                icon
                variant="text"
                color="primary"
                class="action-btn"
                @click="editItem(m)"
              >
                <VIcon
                  icon="ri-pencil-line"
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
                class="action-btn"
                :loading="deletingId === m.kdprayudisium"
                :disabled="deletingId !== null"
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
      <span>Showing per Page</span>
      <VSelect
        v-model="itemsPerPage"
        :items="[5, 10, 20]"
        density="compact"
        style="max-inline-size: 80px;"
      />
    </div>

    <VDialog
      v-model="showPreview"
      max-width="600"
    >
      <VCard>
        <VCardTitle>{{ previewTitle }}</VCardTitle>
        <VCardText class="d-flex justify-center">
          <VImg
            :src="previewUrl"
            max-width="540"
            cover
          />
        </VCardText>
        <VCardActions>
          <VSpacer />
          <VBtn
            variant="text"
            color="primary"
            @click="showPreview = false"
          >
            Tutup
          </VBtn>
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
</style>
