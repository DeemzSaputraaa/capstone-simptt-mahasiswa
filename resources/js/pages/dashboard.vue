<script setup>
import { onMounted, onUnmounted, ref } from 'vue'

const totalProfit = {
  title: 'Total Profit',
  color: 'secondary',
  icon: 'ri-pie-chart-2-line',
  stats: '$25.6k',
  change: 42,
  subtitle: 'Weekly Project',
}

const newProject = {
  title: 'New Project',
  color: 'primary',
  icon: 'ri-file-word-2-line',
  stats: '862',
  change: -18,
  subtitle: 'Yearly Project',
}

const stats = ref([
  {
    title: 'Potential Monthly Profit',
    value: '$24,042,000',
    icon: 'ri-wallet-3-line',
    color: 'error',
    bg: 'bg-error',
  },
  {
    title: 'Workers Wage This Month',
    value: '$8,402,000',
    icon: 'ri-article-line',
    color: 'info',
    bg: 'bg-info',
  },
  {
    title: 'Average Project Length',
    value: '2 weeks',
    icon: 'ri-calendar-line',
    color: 'warning',
    bg: 'bg-warning',
  },
  {
    title: 'Average Income per Project',
    value: '$12,000',
    icon: 'ri-line-chart-line',
    color: 'success',
    bg: 'bg-success',
  },
])

// Tanggal realtime
const today = ref('')
let timer = null
function updateToday() {
  const now = new Date()

  today.value = new Intl.DateTimeFormat('en-US', {
    weekday: 'long', year: 'numeric', month: 'long', day: 'numeric',
  }).format(now)
}
onMounted(() => {
  updateToday()
  timer = setInterval(updateToday, 1000)
})
onUnmounted(() => {
  clearInterval(timer)
})

// Data dummy pembayaran
const payments = [
  { date: '2024-06-10', amount: '$1,200', status: 'Success' },
  { date: '2024-06-09', amount: '$800', status: 'Pending' },
  { date: '2024-06-08', amount: '$2,000', status: 'Failed' },
]


// Data dummy pengiriman
const shipments = [
  { date: '2024-06-10', item: 'Document A', status: 'Delivered' },
  { date: '2024-06-09', item: 'Document B', status: 'In Transit' },
  { date: '2024-06-08', item: 'Document C', status: 'Pending' },
]
</script>

<template>
  <VContainer
    fluid
    class="pa-4 dashboard-bg"
  >
    <VRow>
      <!-- Welcome Card -->
      <VCol
        cols="12"
        md="8"
      >
        <VCard
          class="pa-6 welcome-card"
          elevation="2"
        >
          <VRow align="center">
            <VCol
              cols="12"
              md="7"
              class="d-flex flex-column justify-center"
            >
              <div class="welcome-title mb-2">
                Hi, Sadr!
              </div>
              <div class="welcome-subtitle mb-2">
                What are we doing today?
              </div>
              <div
                class="welcome-date mb-4"
                style="min-inline-size: 200px;"
              >
                {{ today }}
              </div>
            </VCol>
            <VCol
              cols="12"
              md="5"
              class="d-flex justify-center align-center"
            >
              <VImg
                src="https://cdn.pixabay.com/photo/2017/01/31/13/14/animal-2023924_1280.png"
                alt="Bear"
                width="180"
                height="180"
                style="object-fit: contain;"
              />
            </VCol>
          </VRow>
        </VCard>
      </VCol>
      <!-- Notifications -->
      <VCol
        cols="12"
        md="4"
      >
        <VCard
          class="pa-4 notification-card"
          elevation="2"
        >
          <div class="d-flex align-center justify-space-between mb-2">
            <div class="text-h6 font-weight-bold">
              Notifications
            </div>
            <VBtn
              variant="text"
              color="primary"
              class="text-none px-2"
              style="min-inline-size: 0;"
            >
              See all
            </VBtn>
          </div>
          <div class="notification-scroll">
            <VList class="notification-list">
              <VListItem
                class="notification-item mb-2"
                rounded
              >
                <template #prepend>
                  <VAvatar
                    rounded
                    size="40"
                    class="bg-warning"
                  >
                    <VIcon color="white">
                      ri-calendar-event-line
                    </VIcon>
                  </VAvatar>
                </template>
                <VListItemTitle class="text-body-2">
                  You've added new project recently, with no deadline.
                </VListItemTitle>
              </VListItem>
              <VListItem
                class="notification-item mb-2"
                rounded
              >
                <template #prepend>
                  <VAvatar
                    rounded
                    size="40"
                    class="bg-error"
                  >
                    <VIcon color="white">
                      ri-refund-2-line
                    </VIcon>
                  </VAvatar>
                </template>
                <VListItemTitle class="text-body-2">
                  Project owner Adam requested a refund
                </VListItemTitle>
              </VListItem>
              <VListItem
                class="notification-item"
                rounded
              >
                <template #prepend>
                  <VAvatar
                    rounded
                    size="40"
                    class="bg-info"
                  >
                    <VIcon color="white">
                      ri-user-3-line
                    </VIcon>
                  </VAvatar>
                </template>
                <VListItemTitle class="text-body-2">
                  Today, it's Tatia's anniversary! <span class="text-caption text-grey">Wish her Happy Birthday!</span>
                </VListItemTitle>
              </VListItem>
              <VListItem
                class="notification-item mb-2"
                rounded
              >
                <template #prepend>
                  <VAvatar
                    rounded
                    size="40"
                    class="bg-warning"
                  >
                    <VIcon color="white">
                      ri-calendar-event-line
                    </VIcon>
                  </VAvatar>
                </template>
                <VListItemTitle class="text-body-2">
                  You've added new project recently, with no deadline.
                </VListItemTitle>
              </VListItem>
              <VListItem
                class="notification-item mb-2"
                rounded
              >
                <template #prepend>
                  <VAvatar
                    rounded
                    size="40"
                    class="bg-error"
                  >
                    <VIcon color="white">
                      ri-refund-2-line
                    </VIcon>
                  </VAvatar>
                </template>
                <VListItemTitle class="text-body-2">
                  Project owner Adam requested a refund
                </VListItemTitle>
              </VListItem>
              <VListItem
                class="notification-item"
                rounded
              >
                <template #prepend>
                  <VAvatar
                    rounded
                    size="40"
                    class="bg-info"
                  >
                    <VIcon color="white">
                      ri-user-3-line
                    </VIcon>
                  </VAvatar>
                </template>
                <VListItemTitle class="text-body-2">
                  Today, it's Tatia's anniversary! <span class="text-caption text-grey">Wish her Happy Birthday!</span>
                </VListItemTitle>
              </VListItem>
            </VList>
          </div>
        </VCard>
      </VCol>
    </VRow>
    <!-- Statistik Cards -->
    <!--
      <VRow class="mt-2">
      <VCol
      v-for="stat in stats"
      :key="stat.title"
      cols="12"
      md="3"
      >
      <VCard
      class="pa-4 stat-card"
      elevation="1"
      >
      <div class="d-flex align-center mb-2">
      <div :style="`width:36px;height:36px;border-radius:8px;background:${stat.color === 'error' ? '#ff5252' : stat.color === 'info' ? '#40a9ff' : stat.color === 'warning' ? '#ffc107' : '#4caf50'};`" />
      <VSpacer />
      <VIcon color="grey">
      ri-information-line
      </VIcon>
      </div>
      <div class="text-body-2 text-grey mb-1">
      {{ stat.title }}
      </div>
      <div class="text-h6 font-weight-bold">
      {{ stat.value }}
      </div>
      </VCard>
      </VCol>
      </VRow> 
    -->
    <!-- 2 Kolom: Pembayaran & Pengiriman -->
    <VRow class="mt-4">
      <VCol
        cols="12"
        md="6"
      >
        <VCard class="pa-4 mb-4">
          <div class="text-h6 font-weight-bold mb-3">
            Riwayat Pembayaran
          </div>
          <VList>
            <VListItem
              v-for="(pay, i) in payments"
              :key="i"
              class="mb-2"
              rounded
            >
              <VListItemTitle>
                <span class="font-weight-bold">{{ pay.amount }}</span> - {{ pay.date }}
              </VListItemTitle>
              <VListItemSubtitle>
                <span :class="pay.status === 'Success' ? 'text-success' : pay.status === 'Pending' ? 'text-warning' : 'text-error'">{{ pay.status }}</span>
              </VListItemSubtitle>
            </VListItem>
          </VList>
        </VCard>
      </VCol>
      <VCol
        cols="12"
        md="6"
      >
        <VCard class="pa-4 mb-4">
          <div class="text-h6 font-weight-bold mb-3">
            Data Pengiriman Barang
          </div>
          <VList>
            <VListItem
              v-for="(ship, i) in shipments"
              :key="i"
              class="mb-2"
              rounded
            >
              <VListItemTitle>
                <span class="font-weight-bold">{{ ship.item }}</span> - {{ ship.date }}
              </VListItemTitle>
              <VListItemSubtitle>
                <span :class="ship.status === 'Delivered' ? 'text-success' : ship.status === 'In Transit' ? 'text-warning' : 'text-error'">{{ ship.status }}</span>
              </VListItemSubtitle>
            </VListItem>
          </VList>
        </VCard>
      </VCol>
    </VRow>
    <!--
      <VRow class="match-height">
      <VCol
      cols="12"
      md="4"
      >
      <AnalyticsAward />
      </VCol>

      <VCol
      cols="12"
      md="8"
      >
      <AnalyticsTransactions />
      </VCol>

      <VCol
      cols="12"
      md="4"
      >
      <AnalyticsWeeklyOverview />
      </VCol>

      <VCol
      cols="12"
      md="4"
      >
      <AnalyticsTotalEarning />
      </VCol>

      <VCol
      cols="12"
      md="4"
      >
      <VRow class="match-height">
      <VCol
      cols="12"
      sm="6"
      >
      <AnalyticsTotalProfitLineCharts />
      </VCol>

      <VCol
      cols="12"
      sm="6"
      >
      <CardStatisticsVertical v-bind="totalProfit" />
      </VCol>

      <VCol
      cols="12"
      sm="6"
      >
      <CardStatisticsVertical v-bind="newProject" />
      </VCol>

      <VCol
      cols="12"
      sm="6"
      >
      <AnalyticsBarCharts />
      </VCol>
      </VRow>
      </VCol>

      <VCol
      cols="12"
      md="4"
      >
      <AnalyticsSalesByCountries />
      </VCol>

      <VCol
      cols="12"
      md="8"
      >
      <AnalyticsDepositWithdraw />
      </VCol>

      <VCol cols="12">
      <AnalyticsUserTable />
      </VCol>
      </VRow> 
    -->
  </VContainer>
</template>

<style scoped>
.dashboard-bg {
  background: var(--v-theme-background);
  min-block-size: 100vh;
  transition: background 0.3s;
}

.welcome-card {
  padding: 1.5rem;
  border-radius: 24px;
  background: var(--v-theme-surface);
  transition: background 0.3s;
}

/* .welcome-card,
.notification-card,
.stat-card {
  border-radius: 24px;
  background: var(--v-theme-surface);
  transition: background 0.3s;
} */

.card-title,
.text-dark,
.text-h4,
.text-h6,
.font-weight-bold {
  color: var(--v-theme-on-surface) !important;
  transition: color 0.3s;
}

.text-grey {
  color: var(--v-theme-on-background);
  transition: color 0.3s;
}

.gap-4 {
  gap: 1rem;
}

.action-btn-group {
  gap: 2.5rem;
}

.action-btn {
  font-size: 1.25rem;
  font-weight: bold;
  letter-spacing: 0.01em;
  min-inline-size: 0;
}

.action-btn-label {
  color: var(--v-theme-on-surface);
  font-size: 1.18rem;
  font-weight: bold;
  margin-inline-start: 0.5rem;
  transition: color 0.3s;
}

.welcome-title {
  color: var(--v-theme-on-surface);
  font-size: 2.2rem;
  font-weight: bold;
  transition: color 0.3s;
}

.welcome-subtitle {
  color: var(--v-theme-on-surface);
  font-size: 1.1rem;
  transition: color 0.3s;
}

.welcome-date {
  color: var(--v-theme-on-surface);
  font-size: 1rem;
  transition: color 0.3s;
}

.notification-scroll {
  max-block-size: 190px;
  overflow-y: auto;
}
</style>
