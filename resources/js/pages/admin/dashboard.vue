

<template>
  <VContainer
    fluid
    class="pa-4 dashboard-bg"
  >
    <VRow>
      <VCol
        cols="12"
        md="8"
      >
        <VCard
          class="pa-6 welcome-card mb-4"
          elevation="2"
          style="min-block-size: 230px;"
        >
          <div class="text-h5 font-weight-bold mb-2">
            Selamat datang, Admin
          </div>
          <div class="text-body-2">
            Ini adalah Dashboard Admin. Konten fitur akan ditambahkan kemudian.
          </div>
        </VCard>

        <VCard class="pa-4 mb-4">
          <div class="text-h6 font-weight-bold mb-3">
            Statistik
          </div>
          <div class="text-caption">
            Belum ada data untuk ditampilkan.
          </div>
        </VCard>
      </VCol>

      <VCol
        cols="12"
        md="4"
      >
        <VCard
          class="pa-4 notification-card"
          elevation="2"
          style="block-size: 625px;"
        >
          <div class="d-flex align-center justify-space-between mb-2">
            <div class="text-h6 font-weight-bold">
              Notifications
            </div>
          </div>
          <div class="text-caption">
            Belum ada notifikasi.
          </div>
        </VCard>
      </VCol>
    </VRow>
  </VContainer>
</template>

<style scoped>
.dashboard-bg {
  background: var(--v-theme-background);
  transition: background 0.3s;
}
.welcome-card,
.notification-card,
.stat-card {
  padding: 24px;
  border-radius: 8px;
}
</style>
