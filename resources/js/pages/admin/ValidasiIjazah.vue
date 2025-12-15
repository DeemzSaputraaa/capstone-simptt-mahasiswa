

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
    <VCardTitle class="admin-card-title">
      Validasi Ijazah & Transkrip
    </VCardTitle>
    <VCardSubtitle>Daftar pengajuan validasi</VCardSubtitle>

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
          <tr
            v-for="(m, i) in daftarValidasi.slice(0, itemsPerPage)"
            :key="m.nim"
          >
            <td data-label="No">{{ i + 1 }}</td>
            <td data-label="Nama">{{ m.nama }}</td>
            <td data-label="NIM">{{ m.nim }}</td>
            <td data-label="Prodi">{{ m.prodi }}</td>
            <td data-label="Tanggal">{{ m.tanggal }}</td>
            <td
              data-label="Detail"
              class="text-center"
            >
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
</style>
