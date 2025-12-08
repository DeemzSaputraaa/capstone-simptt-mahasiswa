<script setup>
import { ref } from 'vue'

const itemsPerPage = ref(5)

// Data dummy mahasiswa
const mahasiswaList = ref([
  {
    kode: 'API234',
    nik: '2232920009220019I',
    pasFoto: 'https://via.placeholder.com/80x100?text=Foto+1',
    ijazah: 'https://via.placeholder.com/80x100?text=Ijazah+1',
    ktp: 'https://via.placeholder.com/80x100?text=KTP+1',
    tanggalPengajuan: '2025-06-01',
  },
  {
    kode: 'AP2345',
    nik: '2232920009220019I',
    pasFoto: 'https://via.placeholder.com/80x100?text=Foto+2',
    ijazah: 'https://via.placeholder.com/80x100?text=Ijazah+2',
    ktp: 'https://via.placeholder.com/80x100?text=KTP+2',
    tanggalPengajuan: '2025-06-01',
  },
  {
    kode: 'AP3456',
    nik: '2232920009220019I',
    pasFoto: 'https://via.placeholder.com/80x100?text=Foto+3',
    ijazah: 'https://via.placeholder.com/80x100?text=Ijazah+3',
    ktp: 'https://via.placeholder.com/80x100?text=KTP+3',
    tanggalPengajuan: '2025-06-01',
  },
])

const editItem = item => {
  console.log('Edit:', item)

  // TODO: Implement edit logic
}

const deleteItem = item => {
  console.log('Delete:', item)

  // TODO: Implement delete logic
}
</script>

<template>
  <VCard>
    <VCardTitle>Pra Yudisium</VCardTitle>
    <VCardSubtitle>Daftar pengajuan pra yudisium</VCardSubtitle>
    <VDivider />

    <div class="d-flex align-center justify-end mb-4">
      <div>Total: {{ mahasiswaList.length }}</div>
    </div>

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
          :key="m.kode"
        >
          <td>{{ i + 1 }}</td>
          <td>{{ m.kode }}</td>
          <td>{{ m.nik }}</td>
          <td>
            <img
              :src="m.pasFoto"
              alt="Pas Foto"
              class="foto-preview"
            >
          </td>
          <td>
            <img
              :src="m.ijazah"
              alt="Ijazah"
              class="dokumen-preview"
            >
          </td>
          <td>
            <img
              :src="m.ktp"
              alt="KTP"
              class="dokumen-preview"
            >
          </td>
          <td>{{ m.tanggalPengajuan }}</td>
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
