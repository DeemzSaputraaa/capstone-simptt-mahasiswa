<script setup>
import { onMounted, ref } from 'vue'

const itemsPerPage = ref(5)
const loading = ref(false)
const errorMessage = ref('')
const mahasiswaList = ref([])

const storageBase = `${window.location.origin}/storage/`
const fileUrl = path => (path ? `${storageBase}${path}` : '')
const isImage = path => /\.(png|jpe?g|gif|bmp|webp)$/i.test(path || '')
const showPreview = ref(false)
const previewUrl = ref('')
const previewTitle = ref('Pratinjau')
const formatDate = date => date ? new Date(date).toLocaleDateString('id-ID') : '-'

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

const deleteItem = item => {
  console.log('Delete:', item)
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
    <VCardTitle>Pra Yudisium</VCardTitle>
    <VCardSubtitle>Daftar pengajuan pra yudisium</VCardSubtitle>
    <VDivider />

    <div class="d-flex align-center justify-between mb-4">
      <div>Total: {{ mahasiswaList.length }}</div>
      <VBtn
        size="small"
        variant="tonal"
        :loading="loading"
        @click="fetchData"
      >
        Muat Ulang
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
                @click="deleteItem(m)"
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
  </VCard>
</template>

<style scoped>
.pra-yudisium-table {
  border: 1px solid #e0e0e0;
}

.pra-yudisium-table thead {
  background-color: #f5f5f5;
}

.pra-yudisium-table thead th {
  border-block-end: 2px solid #e0e0e0;
  color: #333;
  font-size: 14px;
  font-weight: 600;
  padding-block: 12px;
  padding-inline: 8px;
}

.pra-yudisium-table tbody td {
  background-color: #f7f8fb;
  border-block-end: 1px solid #e0e0e0;
  font-size: 13px;
  padding-block: 12px;
  padding-inline: 8px;
}

.pra-yudisium-table tbody tr:hover {
  background-color: #fafafa;
}

.foto-preview {
  border-radius: 4px;
  block-size: 75px;
  inline-size: 60px;
  object-fit: cover;
}

.dokumen-preview {
  border-radius: 4px;
  block-size: 75px;
  inline-size: 60px;
  object-fit: cover;
}

.text-center {
  text-align: center;
}

.w-auto {
  inline-size: auto !important;
}

.table-footer {
  display: flex;
  align-items: center;
  gap: 12px;
  margin-block-start: 16px;
}

.action-btn {
  margin-inline: 4px;
}
</style>
