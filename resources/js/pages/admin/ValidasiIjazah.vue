
<script setup>
import axios from 'axios'
import { computed, onMounted, ref } from 'vue'

const itemsPerPage = ref(5)
const daftarValidasi = ref([])
const loading = ref(false)
const errorMessage = ref(null)
const filterApprove = ref('all')
const filterComment = ref('all')

const commentDialog = ref(false)
const selectedItem = ref(null)
const comments = ref([])
const loadingComments = ref(false)
const newComment = ref('')
const savingComment = ref(false)
const replyTo = ref(null)
const replyText = ref('')
const expandedReplies = ref({})
const seenCommentMap = ref({})

const seenStorageKey = 'admin_validasi_seen_counts'

const loadSeenMap = () => {
  try {
    const raw = localStorage.getItem(seenStorageKey)
    if (raw) {
      const parsed = JSON.parse(raw)
      if (parsed && typeof parsed === 'object') {
        seenCommentMap.value = parsed
      }
    }
  } catch (err) {
    seenCommentMap.value = {}
  }
}

const saveSeenMap = () => {
  try {
    localStorage.setItem(seenStorageKey, JSON.stringify(seenCommentMap.value))
  } catch (err) {
    // Ignore storage errors
  }
}

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

const getSortDate = item => {
  const raw = item?.last_comment_at || item?.create_at || item?.tgl_diambil_ijazah
  const parsed = raw ? new Date(raw) : null
  return parsed && !Number.isNaN(parsed.getTime()) ? parsed.getTime() : 0
}

const hasNewCommentForAdmin = item => {
  const count = item?.comment_count || 0
  if (!count) return false

  const recordId = item?.kdvalidasiijazahmahasiswa
  if (!recordId) return false

  const seenCount = Number(seenCommentMap.value[recordId] ?? 0)
  return count > seenCount
}

const filteredValidasi = computed(() => {
  const sorted = [...daftarValidasi.value].sort((a, b) => getSortDate(b) - getSortDate(a))

  return sorted.filter(item => {
    const isApproved = !!item?.is_ijazah_validate && !!item?.is_transkrip_validate
    const hasComment = (item?.comment_count || 0) > 0
    const hasUnread = hasNewCommentForAdmin(item)

    if (filterApprove.value === 'approved' && !isApproved) return false
    if (filterApprove.value === 'pending' && isApproved) return false

    if (filterComment.value === 'new' && !hasUnread) return false
    if (filterComment.value === 'none' && hasUnread) return false

    return true
  })
})

const normalizeId = value => {
  if (value === null || value === undefined) return null
  const normalized = String(value).trim()
  return normalized === '' ? null : normalized
}

const flattenComments = (items, parentId = null) => {
  if (!Array.isArray(items)) return []
  const flat = []

  items.forEach(item => {
    const replies = Array.isArray(item.replies) ? item.replies : []
    const normalizedId = normalizeId(item?.id ?? item?.kdcomment ?? item?.comment_id)
    const normalizedParentId = normalizeId(item?.parent_id ?? item?.parentId ?? parentId)
    const normalized = {
      ...item,
      id: normalizedId,
      parent_id: normalizedParentId,
      replies: [],
    }
    flat.push(normalized)
    flat.push(...flattenComments(replies, normalizedId))
  })

  return flat
}

const buildCommentTree = items => {
  if (!Array.isArray(items)) return []

  const flat = flattenComments(items)
  if (!flat.length) return []

  const byId = new Map()
  flat.forEach(item => {
    if (item?.id !== undefined && item?.id !== null)
      byId.set(item.id, { ...item, replies: [] })
  })

  const roots = []
  byId.forEach(node => {
    if (node.parent_id !== null && node.parent_id !== undefined && byId.has(node.parent_id)) {
      byId.get(node.parent_id).replies.push(node)
    } else {
      roots.push(node)
    }
  })

  return roots
}

const commentTree = computed(() => buildCommentTree(comments.value))

const countReplies = replies => {
  if (!Array.isArray(replies) || !replies.length) return 0
  return replies.reduce((total, reply) => {
    const childReplies = countReplies(reply.replies)
    return total + 1 + childReplies
  }, 0)
}

const buildVisibleComments = (items, depth = 0) => {
  if (!Array.isArray(items)) return []
  const result = []

  items.forEach(item => {
    const replies = Array.isArray(item.replies) ? item.replies : []
    const replyCount = countReplies(replies)

    result.push({ ...item, depth, replyCount })
    if (!replyCount) return
    if (depth === 0 && replyCount > 1 && !expandedReplies.value[item.id]) return

    result.push(...buildVisibleComments(replies, depth + 1))
  })

  return result
}

const visibleComments = computed(() => buildVisibleComments(commentTree.value))

const openChat = async item => {
  selectedItem.value = item
  commentDialog.value = true
  await fetchComments(item)
}

const countAllComments = items => flattenComments(items).length

const findLatestCommentDate = items => {
  const flat = flattenComments(items)
  if (!flat.length) return null

  return flat.reduce((latest, current) => {
    const currentDate = current?.date ? new Date(current.date) : null
    if (!currentDate || Number.isNaN(currentDate.getTime())) return latest
    if (!latest || currentDate > latest) return currentDate
    return latest
  }, null)
}

const formatCommentDate = value => {
  if (!value) return '-'
  const parsed = value instanceof Date ? value : new Date(value)
  if (Number.isNaN(parsed.getTime())) return '-'

  return new Intl.DateTimeFormat('id-ID', {
    dateStyle: 'medium',
    timeStyle: 'short',
  }).format(parsed)
}

const normalizeCommentDates = items => {
  if (!Array.isArray(items)) return []

  return items.map(item => ({
    ...item,
    date: item?.date ? new Date(item.date) : new Date(),
    replies: normalizeCommentDates(item?.replies),
  }))
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
    comments.value = normalizeCommentDates(data || [])
    const totalCount = countAllComments(comments.value)
    const latestDate = findLatestCommentDate(comments.value)
    const recordId = item?.kdvalidasiijazahmahasiswa
    if (recordId) {
      daftarValidasi.value = daftarValidasi.value.map(row => {
        if (row.kdvalidasiijazahmahasiswa !== recordId) return row
        return {
          ...row,
          comment_count: totalCount,
          last_comment_at: latestDate ? latestDate.toISOString() : row.last_comment_at,
        }
      })
      seenCommentMap.value = {
        ...seenCommentMap.value,
        [recordId]: totalCount,
      }
      saveSeenMap()
    }
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
      parent_id: null,
    }, {
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        ...authHeaders(),
      },
    })
    newComment.value = ''
    replyTo.value = null
    replyText.value = ''
    await fetchComments(selectedItem.value)
  } catch (err) {
    errorMessage.value = 'Gagal mengirim komentar'
  } finally {
    savingComment.value = false
  }
}

const startReply = comment => {
  replyTo.value = comment
  replyText.value = ''
  commentDialog.value = true
}

const submitReply = async () => {
  if (!replyTo.value || !replyText.value || !selectedItem.value) return
  savingComment.value = true
  errorMessage.value = null
  try {
    await axios.post('/api/comments', {
      comment: replyText.value,
      kdvalidasiijazahmahasiswa: selectedItem.value.kdvalidasiijazahmahasiswa,
      parent_id: replyTo.value.id,
    }, {
      headers: {
        Accept: 'application/json',
        'Content-Type': 'application/json',
        ...authHeaders(),
      },
    })
    replyText.value = ''
    replyTo.value = null
    await fetchComments(selectedItem.value)
  } catch (err) {
    errorMessage.value = 'Gagal mengirim balasan'
  } finally {
    savingComment.value = false
  }
}

const isReplyTarget = comment => replyTo.value?.id === comment?.id

const toggleReplies = comment => {
  if (!comment?.id) return
  expandedReplies.value = {
    ...expandedReplies.value,
    [comment.id]: !expandedReplies.value[comment.id],
  }
}

const isRepliesExpanded = comment => {
  if (!comment?.id) return false
  if (expandedReplies.value[comment.id] !== undefined)
    return expandedReplies.value[comment.id]

  return false
}

const getInitial = name => {
  const value = (name || '').trim()
  return value ? value.charAt(0).toUpperCase() : 'A'
}

onMounted(() => {
  loadSeenMap()
  loadValidasi()
})
</script>

<template>
  <VCard>
    <VCardTitle class="admin-card-title">
      Validasi Ijazah & Transkrip
    </VCardTitle>
    <div class="validasi-filters">
      <div class="filter-item">
        <span>Status Approve</span>
        <VSelect
          v-model="filterApprove"
          :items="[
            { title: 'Semua', value: 'all' },
            { title: 'Approved', value: 'approved' },
            { title: 'Belum', value: 'pending' },
          ]"
          density="compact"
          style="max-inline-size: 160px;"
        />
      </div>
      <div class="filter-item">
        <span>Komentar Baru</span>
        <VSelect
          v-model="filterComment"
          :items="[
            { title: 'Semua', value: 'all' },
            { title: 'Ada', value: 'new' },
            { title: 'Tidak Ada', value: 'none' },
          ]"
          density="compact"
          style="max-inline-size: 160px;"
        />
      </div>
    </div>

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
          <tr v-else-if="!filteredValidasi.length">
            <td
              colspan="6"
              class="text-center py-6"
            >
              Tidak ada data validasi.
            </td>
          </tr>
          <tr
            v-else
            v-for="(m, i) in filteredValidasi.slice(0, itemsPerPage)"
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
                  v-if="hasNewCommentForAdmin(m)"
                  dot
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
        style="max-inline-size: 100px;"
      />
    </div>
  </VCard>

  <VDialog
    v-model="commentDialog"
    max-width="980"
  >
    <VCard>
      <VCardTitle class="comment-title">
        Komentar Validasi
      </VCardTitle>
      <VCardSubtitle>
        {{ selectedItem?.namalengkap || selectedItem?.nim || 'Mahasiswa' }}
      </VCardSubtitle>

      <VCardText>
        <div class="comment-input">
          <div class="comment-avatar admin-avatar">{{ getInitial('Admin') }}</div>
          <VTextarea
            v-model="newComment"
            class="comment-input-field"
            placeholder="Tulis komentar..."
            auto-grow
            rows="1"
            hide-details
          />
          <VBtn
            class="comment-send-btn"
            color="success"
            :loading="savingComment"
            @click="submitComment"
          >
            Kirim
          </VBtn>
        </div>

        <div v-if="loadingComments">
          Memuat komentar...
        </div>
        <div v-else>
          <div
            v-if="visibleComments.length === 0"
            class="comment-empty"
          >
            Belum ada komentar
          </div>
          <div
            v-else
            class="comment-list"
          >
            <div
              v-for="c in visibleComments"
              :key="c.id"
              class="comment-row"
              :class="{ reply: c.depth > 0 }"
              :style="{ marginLeft: `${c.depth * 24}px` }"
            >
              <div class="comment-avatar">{{ getInitial(c.user) }}</div>
              <div
                class="comment-body"
              >
                <div class="comment-user">{{ c.user }}</div>
                <div class="comment-date">{{ formatCommentDate(c.date) }}</div>
                <div class="comment-text">
                  {{ c.text }}
                </div>
                <div class="comment-actions">
                  <VBtn
                    size="x-small"
                    variant="text"
                    color="secondary"
                    @click="startReply(c)"
                  >
                    Balas
                  </VBtn>
                  <VBtn
                    v-if="c.depth === 0 && (c.replyCount || 0) > 1"
                    size="x-small"
                    variant="text"
                    color="secondary"
                    @click="toggleReplies(c)"
                  >
                    {{ isRepliesExpanded(c) ? 'Sembunyikan' : 'Tampilkan semua' }}
                  </VBtn>
                </div>
                <div
                  v-if="isReplyTarget(c)"
                  class="reply-input"
                >
                  <VTextarea
                    v-model="replyText"
                    auto-grow
                    rows="1"
                    hide-details
                    placeholder="Tulis balasan..."
                    class="reply-input-field"
                  />
                  <div class="reply-actions">
                    <VBtn
                      size="x-small"
                      variant="text"
                      color="secondary"
                      @click="replyTo = null"
                    >
                      Batal
                    </VBtn>
                    <VBtn
                      size="x-small"
                      color="success"
                      :loading="savingComment"
                      @click="submitReply"
                    >
                      Kirim
                    </VBtn>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
      </VCardText>

      <VCardActions class="comment-actions-footer">
        <VSpacer />
        <VBtn
          variant="text"
          color="secondary"
          @click="commentDialog = false"
        >
          Tutup
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

.validasi-filters {
  display: flex;
  flex-wrap: wrap;
  gap: 16px;
  align-items: center;
  padding-block: 12px 16px;
  padding-inline: 20px;
}

.filter-item {
  display: flex;
  align-items: center;
  gap: 8px;
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

.comment-input {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-top: 8px;
  margin-bottom: 16px;
}

.comment-avatar {
  display: inline-flex;
  align-items: center;
  justify-content: center;
  inline-size: 36px;
  block-size: 36px;
  border-radius: 999px;
  background: rgb(var(--v-theme-primary));
  color: rgb(var(--v-theme-on-primary));
  font-weight: 700;
  flex: 0 0 36px;
}

.comment-input-field {
  flex: 1 1 auto;
}

.comment-send-btn {
  min-inline-size: 90px;
  text-transform: none;
}

.comment-empty {
  color: rgba(var(--v-theme-on-surface), 0.6);
  margin-bottom: 12px;
}

.comment-list {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.comment-row {
  display: flex;
  align-items: flex-start;
  gap: 12px;
}

.comment-row.reply {
  margin-inline-start: 48px;
}

.comment-body {
  flex: 1 1 auto;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
  border-radius: 10px;
  padding: 8px 12px;
  background: rgba(var(--v-theme-surface), 0.9);
}

.comment-user {
  font-weight: 700;
  color: rgba(var(--v-theme-on-surface), 0.9);
  margin-bottom: 4px;
}

.comment-date {
  color: rgba(var(--v-theme-on-surface), 0.7);
  font-size: 0.8rem;
  margin-bottom: 4px;
}

.comment-text {
  color: rgba(var(--v-theme-on-surface), 0.88);
  white-space: pre-line;
}

.comment-actions {
  margin-top: 4px;
}

.reply-input {
  margin-top: 8px;
  padding: 10px;
  border-radius: 10px;
  background: rgba(var(--v-theme-on-surface), 0.04);
}

.reply-input-field {
  margin-bottom: 8px;
}

.reply-actions {
  display: flex;
  justify-content: flex-end;
  gap: 8px;
}

.comment-replies {
  display: flex;
  flex-direction: column;
  gap: 8px;
  margin-top: 8px;
}

.comment-actions-footer {
  padding-top: 0;
}
</style>

