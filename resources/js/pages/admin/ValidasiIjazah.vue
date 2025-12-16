
<script setup>
import axios from 'axios'
import { onMounted, ref } from 'vue'

const itemsPerPage = ref(5)
const daftarValidasi = ref([])
const loading = ref(false)
const errorMessage = ref(null)

const commentDialog = ref(false)
const selectedItem = ref(null)
const comments = ref([])
const loadingComments = ref(false)
const newComment = ref('')
const savingComment = ref(false)
const replyTo = ref(null)

const authHeaders = () => {
  const token = sessionStorage.getItem('jwt_token')
  return token ? { Authorization: `Bearer ${token}` } : {}
}

const loadValidasi = async () => {
  loading.value = true
  errorMessage.value = null
  try {
    const { data } = await axios.get('/api/ak-validasi-ijazah', {
      headers: {
        Accept: 'application/json',
        ...authHeaders(),
      },
    })
    daftarValidasi.value = data || []
  } catch (err) {
    errorMessage.value = 'Gagal memuat daftar validasi'
  } finally {
    loading.value = false
  }
}

const formatDate = date => {
  if (!date) return '-'
  const parsed = new Date(date)
  return Number.isNaN(parsed.getTime()) ? '-' : parsed.toLocaleDateString('id-ID')
}

const openChat = async item => {
  selectedItem.value = item
  commentDialog.value = true
  await fetchComments(item)
}

const fetchComments = async item => {
  loadingComments.value = true
  errorMessage.value = null
  try {
    const { data } = await axios.get(`/api/comments/${item.kdvalidasiijazahmahasiswa}`, {
      headers: {
        Accept: 'application/json',
        ...authHeaders(),
      },
    })
    comments.value = data || []
  } catch (err) {
    comments.value = []
    errorMessage.value = 'Gagal memuat komentar'
  } finally {
    loadingComments.value = false
  }
}

const submitComment = async () => {
  if (!newComment.value || !selectedItem.value) return
  savingComment.value = true
  errorMessage.value = null
  try {
    await axios.post('/api/comments', {
      comment: newComment.value,
      kdvalidasiijazahmahasiswa: selectedItem.value.kdvalidasiijazahmahasiswa,
      parent_id: replyTo.value?.id ?? null,
    }, {
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        ...authHeaders(),
      },
    })
    newComment.value = ''
    replyTo.value = null
    await fetchComments(selectedItem.value)
  } catch (err) {
    errorMessage.value = 'Gagal mengirim komentar'
  } finally {
    savingComment.value = false
  }
}

const startReply = comment => {
  replyTo.value = comment
  commentDialog.value = true
}

onMounted(() => {
  loadValidasi()
})
</script>

<template>
  <VCard>
    <VCardTitle class="admin-card-title">
      Validasi Ijazah & Transkrip
    </VCardTitle>
    <VCardSubtitle>Daftar pengajuan validasi</VCardSubtitle>

    <div
      v-if="errorMessage"
      class="error-box"
    >
      {{ errorMessage }}
    </div>

    <div class="table-wrapper">
      <VTable class="validasi-table w-auto">
        <thead>
          <tr>
            <th>No</th>
            <th>Nama</th>
            <th>NIM</th>
            <th>Prodi</th>
            <th>Tanggal</th>
            <th class="text-center">
              Detail
            </th>
          </tr>
        </thead>
        <tbody>
          <tr v-if="loading">
            <td
              colspan="6"
              class="text-center py-6"
            >
              Memuat data...
            </td>
          </tr>
          <tr
            v-else
            v-for="(m, i) in daftarValidasi.slice(0, itemsPerPage)"
            :key="m.kdvalidasiijazahmahasiswa ?? m.nim ?? i"
          >
            <td data-label="No">{{ i + 1 }}</td>
            <td data-label="Nama">{{ m.namalengkap || '-' }}</td>
            <td data-label="NIM">{{ m.nim || '-' }}</td>
            <td data-label="Prodi">{{ m.prodi || '-' }}</td>
            <td data-label="Tanggal">{{ formatDate(m.create_at || m.last_comment_at || m.tgl_diambil_ijazah) }}</td>
            <td
              data-label="Detail"
              class="text-center"
            >
              <div class="d-flex justify-center">
                <VBadge
                  v-if="(m.comment_count || 0) > 0"
                  :content="m.comment_count"
                  color="error"
                  class="me-2"
                >
                  <VBtn
                    icon
                    variant="text"
                    color="secondary"
                    class="action-btn"
                    @click="openChat(m)"
                  >
                    <VIcon
                      icon="ri-chat-3-line"
                      size="20"
                    />
                  </VBtn>
                </VBadge>
                <VBtn
                  v-else
                  icon
                  variant="text"
                  color="secondary"
                  class="action-btn"
                  @click="openChat(m)"
                >
                  <VIcon
                    icon="ri-chat-3-line"
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
    v-model="commentDialog"
    max-width="640"
  >
    <VCard>
      <VCardTitle class="comment-title">
        Komentar Validasi
      </VCardTitle>
      <VCardSubtitle>
        {{ selectedItem?.namalengkap || selectedItem?.nim || 'Mahasiswa' }}
      </VCardSubtitle>

      <VCardText>
        <div v-if="loadingComments">
          Memuat komentar...
        </div>
          <div v-else>
            <div v-if="comments.length === 0">
              Belum ada komentar
            </div>
            <div v-else>
              <div
                v-for="c in comments"
                :key="c.id"
                class="comment-item"
              >
                <div class="comment-meta">
                  <span class="comment-user">{{ c.user }}</span>
                  <span class="comment-date">{{ formatDate(c.date) }}</span>
                </div>
                <div class="comment-text">
                  {{ c.text }}
                </div>
                <div class="mt-2">
                  <VBtn
                    size="small"
                    variant="text"
                    color="secondary"
                    @click="startReply(c)"
                  >
                    Balas
                  </VBtn>
                </div>

                <div
                  v-if="c.replies && c.replies.length"
                  class="comment-replies"
                >
                  <div
                    v-for="r in c.replies"
                    :key="r.id"
                    class="comment-item reply"
                  >
                    <div class="comment-meta">
                      <span class="comment-user">{{ r.user }}</span>
                      <span class="comment-date">{{ formatDate(r.date) }}</span>
                    </div>
                    <div class="comment-text">
                      {{ r.text }}
                    </div>
                    <div class="mt-2">
                      <VBtn
                        size="small"
                        variant="text"
                        color="secondary"
                        @click="startReply(r)"
                      >
                        Balas
                      </VBtn>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>

        <VTextarea
          v-model="newComment"
          class="mt-4"
          label="Kirim komentar"
          auto-grow
          rows="2"
        />
        <div
          v-if="replyTo"
          class="reply-hint"
        >
          Balas komentar: <strong>{{ replyTo.user }}</strong> — "{{ replyTo.text }}"
          <VBtn
            size="x-small"
            variant="text"
            color="secondary"
            class="ms-2"
            @click="replyTo = null"
          >
            batal
          </VBtn>
        </div>
      </VCardText>

      <VCardActions>
        <VBtn
          variant="text"
          color="secondary"
          @click="commentDialog = false"
        >
          Tutup
        </VBtn>
        <VBtn
          color="primary"
          :loading="savingComment"
          @click="submitComment"
        >
          Kirim
        </VBtn>
      </VCardActions>
    </VCard>
  </VDialog>
</template>

<style scoped>
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

.error-box {
  margin: 1rem 1.5rem;
  padding: 0.75rem 1rem;
  border: 1px solid rgba(var(--v-theme-error), 0.4);
  color: rgb(var(--v-theme-error));
  border-radius: 6px;
  background-color: rgba(var(--v-theme-error), 0.08);
}

.table-wrapper {
  overflow-x: auto;
}

.validasi-table {
  overflow: hidden;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.12);
  border-radius: 4px;
  background-color: rgb(var(--v-theme-surface));
  color: rgb(var(--v-theme-on-surface));
  box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
}

.validasi-table thead {
  background: linear-gradient(
    to right,
    rgba(var(--v-theme-on-surface), 0.04),
    rgba(var(--v-theme-on-surface), 0.06)
  );
  box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
}

.validasi-table thead th {
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

.validasi-table tbody td {
  background-color: rgba(var(--v-theme-surface), 0.9);
  border-block-end: 1px solid rgba(var(--v-theme-on-surface), 0.08);
  color: rgba(var(--v-theme-on-surface), 0.8);
  font-size: 0.875rem;
  padding-block: 1rem;
  padding-inline: 0.75rem;
  transition: all 0.2s ease;
  vertical-align: middle;
}

.validasi-table tbody tr:not(:last-child) td {
  border-block-end: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.validasi-table tbody tr:hover td {
  background-color: rgba(var(--v-theme-on-surface), 0.05);
  box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05);
  transform: translateY(-1px);
}

.action-btn {
  margin-block: 0;
  margin-inline: 2px;
  opacity: 0.8;
  transition: all 0.2s ease;
}

.action-btn:hover {
  box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
  opacity: 1;
  transform: translateY(-2px);
}

.table-footer {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 1rem;
  border-radius: 0 0 4px 4px;
  background-color: rgba(var(--v-theme-surface), 0.9);
  border-block-start: 1px solid rgba(var(--v-theme-on-surface), 0.08);
}

.text-center { text-align: center; }
.w-auto { inline-size: auto !important; }

@media (max-width: 960px) {
  .validasi-table thead {
    display: none;
  }

  .validasi-table tbody tr {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
    gap: 8px;
    padding: 12px;
  }

  .validasi-table tbody td {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    gap: 4px;
    background-color: rgba(var(--v-theme-surface), 0.9);
  }

  .validasi-table tbody td::before {
    content: attr(data-label);
    font-size: 12px;
    font-weight: 600;
    color: rgba(var(--v-theme-on-surface), 0.7);
    text-transform: uppercase;
  }

  .validasi-table tbody td.text-center {
    align-items: center;
  }
}

@media (prefers-color-scheme: dark) {
  .validasi-table {
    box-shadow: 0 1px 3px rgba(0, 0, 0, 0.25);
  }

  .validasi-table thead {
    background: linear-gradient(to right, #0f0f0f, #141414);
  }
}

.comment-item {
  padding: 10px 12px;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
  border-radius: 8px;
  margin-block: 8px;
  background: rgba(var(--v-theme-surface), 0.8);
}

.comment-item.reply {
  margin-inline-start: 12px;
  border-style: dashed;
}

.comment-meta {
  display: flex;
  align-items: center;
  gap: 8px;
  font-size: 0.85rem;
  color: rgba(var(--v-theme-on-surface), 0.7);
  margin-bottom: 4px;
}

.comment-user {
  font-weight: 700;
  color: rgba(var(--v-theme-on-surface), 0.9);
}

.comment-text {
  color: rgba(var(--v-theme-on-surface), 0.88);
  white-space: pre-line;
}

.comment-replies {
  margin-top: 6px;
  padding-left: 8px;
}

.reply-hint {
  margin-top: 8px;
  padding: 8px 10px;
  border-radius: 6px;
  background: rgba(var(--v-theme-primary), 0.08);
  color: rgba(var(--v-theme-on-surface), 0.8);
  font-size: 0.9rem;
}
</style>
