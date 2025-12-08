

<script setup>
import { ref } from 'vue'

const itemsPerPage = ref(5)

const daftarValidasi = ref([
  { nama: 'Syahrur Ramadhan(presiden)', nim: '2211501047', prodi: 'Ti', tanggal: '17-09-2025', unread: 1 },
  { nama: 'ibu cilok perempatan', nim: '2211501060', prodi: 'TLM', tanggal: '10-09-2025', unread: 3 },
  { nama: 'heni safitri', nim: '2211501021', prodi: 'Manajemen', tanggal: '03-09-2025', unread: 2 },
  { nama: 'Firdaus Washington', nim: '2111501029', prodi: 'Fisioterapi', tanggal: '04-08-2025', unread: 1 },
  { nama: 'safrina aisyah', nim: '2211501053', prodi: 'Akuntansi', tanggal: '11-09-2025', unread: 0 },
])

const editItem = item => { console.log('Edit:', item) }
const deleteItem = item => { console.log('Delete:', item) }
const openChat = item => { console.log('Chat:', item) }
</script>

<template>
  <VCard>
    <div class="mb-4">
      <VBtn
        color="success"
        variant="tonal"
      >
        Validasi ijazah dan Transkrip
      </VBtn>
    </div>

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
        <tr
          v-for="(m, i) in daftarValidasi.slice(0, itemsPerPage)"
          :key="m.nim"
        >
          <td>{{ i + 1 }}</td>
          <td>{{ m.nama }}</td>
          <td>{{ m.nim }}</td>
          <td>{{ m.prodi }}</td>
          <td>{{ m.tanggal }}</td>
          <td class="text-center">
            <div class="d-flex justify-center">
              <VBadge
                v-if="m.unread > 0"
                :content="m.unread"
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
  </VCard>
</template>

<style scoped>
.validasi-table {
  border: 1px solid #e0e0e0;
}

.validasi-table thead {
  background-color: #f5f5f5;
}

.validasi-table thead th {
  border-block-end: 2px solid #e0e0e0;
  color: #333;
  font-size: 14px;
  font-weight: 600;
  padding-block: 12px;
  padding-inline: 8px;
}

.validasi-table tbody td {
  background-color: #f7f8fb;
  border-block-end: 1px solid #e0e0e0;
  font-size: 13px;
  padding-block: 12px;
  padding-inline: 8px;
}

.validasi-table tbody tr:hover {
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

.action-btn { margin-inline: 4px; }
</style>
