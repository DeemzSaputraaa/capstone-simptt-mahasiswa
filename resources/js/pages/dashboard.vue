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
  { date: '2024-06-10', item: 'Document A', status: 'Delivered', trackingNumber: 'TRK123456789', history: [
    { timestamp: '2024-06-10 10:00', location: 'Warehouse A', status: 'Delivered' },
    { timestamp: '2024-06-09 18:00', location: 'Delivery Hub', status: 'Out for Delivery' },
    { timestamp: '2024-06-09 09:00', location: 'Sorting Center B', status: 'In Transit' },
    { timestamp: '2024-06-08 15:00', location: 'Origin Facility', status: 'Shipped' },
  ] },
  { date: '2024-06-09', item: 'Document B', status: 'In Transit', trackingNumber: 'TRK987654321', history: [
    { timestamp: '2024-06-09 14:30', location: 'Sorting Center A', status: 'In Transit' },
    { timestamp: '2024-06-09 10:00', location: 'Origin Facility', status: 'Shipped' },
  ] },
  { date: '2024-06-08', item: 'Document C', status: 'Pending', trackingNumber: 'TRK112233445', history: [
    { timestamp: '2024-06-08 11:00', location: 'Waiting for pickup', status: 'Pending' },
  ] },
]

const trackingNumber = ref('')
const showTrackingModal = ref(false)
const trackingResults = ref(null)
const searchError = ref(false)

function trackShipment() {
  searchError.value = false

  const result = shipments.find(s => s.trackingNumber === trackingNumber.value)
  if (result) {
    trackingResults.value = result
    showTrackingModal.value = true
  } else {
    searchError.value = true
    trackingResults.value = null
  }
}

const notifications = ref([
  {
    id: 1,
    icon: 'ri-calendar-event-line',
    color: 'warning',
    title: 'You\'ve added new project recently, with no deadline.',
  },
  {
    id: 2,
    icon: 'ri-refund-2-line',
    color: 'error',
    title: 'Project owner Adam requested a refund',
  },
  {
    id: 3,
    icon: 'ri-user-3-line',
    color: 'info',
    title: 'Today, it\'s Tatia\'s anniversary! Wish her Happy Birthday!',
  },
  {
    id: 4,
    icon: 'ri-calendar-event-line',
    color: 'warning',
    title: 'Another project added without a deadline.',
  },
  {
    id: 5,
    icon: 'ri-refund-2-line',
    color: 'error',
    title: 'Another refund request from client Z.',
  },
  {
    id: 6,
    icon: 'ri-user-3-line',
    color: 'info',
    title: 'John Doe\'s birthday is next week.',
  },
])

const showNotificationsModal = ref(false)
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
              @click="showNotificationsModal = true"
            >
              See all
            </VBtn>
          </div>
          <div class="notification-scroll">
            <VList class="notification-list">
              <VListItem
                v-for="notification in notifications.slice(0, 3)"
                :key="notification.id"
                class="notification-item mb-2"
                rounded
              >
                <template #prepend>
                  <VAvatar
                    rounded
                    size="40"
                    :class="`bg-${notification.color}`"
                  >
                    <VIcon color="white">
                      {{ notification.icon }}
                    </VIcon>
                  </VAvatar>
                </template>
                <VListItemTitle class="text-body-2">
                  {{ notification.title }}
                </VListItemTitle>
              </VListItem>
            </VList>
          </div>
        </VCard>
      </VCol>
    </VRow>
    <!-- All Notifications Modal -->
    <VDialog
      v-model="showNotificationsModal"
      max-width="600"
    >
      <VCard>
        <VCardTitle class="d-flex align-center justify-space-between pe-4">
          Notifications
          <VBtn
            icon="ri-close-line"
            variant="text"
            size="small"
            @click="showNotificationsModal = false"
          />
        </VCardTitle>
        <VDivider />
        <VCardText>
          <VList class="notification-list">
            <VListItem
              v-for="notification in notifications"
              :key="notification.id"
              class="notification-item mb-2"
              rounded
            >
              <template #prepend>
                <VAvatar
                  rounded
                  size="40"
                  :class="`bg-${notification.color}`"
                >
                  <VIcon color="white">
                    {{ notification.icon }}
                  </VIcon>
                </VAvatar>
              </template>
              <VListItemTitle class="text-body-2">
                {{ notification.title }}
              </VListItemTitle>
            </VListItem>
          </VList>
        </VCardText>
      </VCard>
    </VDialog>
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
          <VTextField
            v-model="trackingNumber"
            label="Enter Tracking Number"
            placeholder="TRK123456789"
            class="mb-3"
            append-inner-icon="ri-search-line"
            @click:append-inner="trackShipment"
            @keyup.enter="trackShipment"
          />
          <VAlert
            v-if="searchError"
            type="error"
            class="mb-3"
            density="compact"
          >
            Tracking number not found.
          </VAlert>
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
              <template #append>
                <VBtn
                  variant="text"
                  color="primary"
                  size="small"
                  @click="trackingNumber = ship.trackingNumber; trackShipment()"
                >
                  Track
                </VBtn>
              </template>
            </VListItem>
          </VList>
        </VCard>
      </VCol>
    </VRow>
    <!-- Tracking Modal -->
    <VDialog
      v-model="showTrackingModal"
      max-width="600"
    >
      <VCard class="pa-6 rounded-lg">
        <VCardTitle class="d-flex align-center justify-space-between pe-4">
          Tracking Details: {{ trackingResults ? trackingResults.trackingNumber : '' }}
          <VBtn
            icon="ri-close-line"
            variant="text"
            size="small"
            @click="showTrackingModal = false"
          />
        </VCardTitle>
        <VDivider />
        <VCardText v-if="trackingResults">
          <VTimeline
            density="compact"
            align="start"
            line-inset="8"
          >
            <VTimelineItem
              v-for="(event, i) in trackingResults.history"
              :key="i"
              :dot-color="event.status === 'Delivered' ? 'success' : event.status === 'In Transit' ? 'warning' : 'info'"
              size="small"
            >
              <div class="d-flex justify-space-between flex-wrap text-no-wrap mb-2">
                <div class="text-body-2 font-weight-bold">
                  {{ event.status }}
                </div>
                <small class="text-caption text-no-wrap text-grey">
                  {{ event.timestamp }}
                </small>
              </div>
              <p class="text-caption mb-0">
                {{ event.location }}
              </p>
            </VTimelineItem>
          </VTimeline>
        </VCardText>
      </VCard>
    </VDialog>
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

.welcome-card,
.notification-card,
.stat-card {
  padding: 24px;
  border-radius: 8px;
  background-color: var(--v-theme-surface) !important;
  transition: background 0.3s;
}

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
  max-block-size: 250px;
  overflow-y: auto;
}

.notification-list {
  background-color: transparent !important;
}

.notification-item {
  background-color: var(--v-theme-surface);
  transition: all 0.2s ease-in-out;

  &:hover {
    background-color: rgba(var(--v-theme-on-surface), 0.05);
  }
}

.v-dialog {
  .v-card {
    color: var(--v-theme-on-surface);
    transition: background 0.3s;
  }

  .v-card-title,
  .v-card-text {
    color: var(--v-theme-on-surface);
  }

  .v-btn__content .v-icon {
    color: var(--v-theme-on-surface);
  }
}

.v-timeline-item__body {
  color: var(--v-theme-on-surface);
}

.v-timeline-item__opposite {
  color: var(--v-theme-on-surface);
}

.v-timeline-item__subtitle {
  color: var(--v-theme-on-surface);
}

.v-input__control {
  color: var(--v-theme-on-surface) !important;
}

.v-label.v-field-label {
  color: var(--v-theme-on-surface) !important;
}

.v-field__input {
  color: var(--v-theme-on-surface) !important;
}

.v-input__details {
  color: var(--v-theme-on-surface) !important;
}

.v-overlay__scrim {
  backdrop-filter: blur(4px);
  background-color: rgba(var(--v-theme-on-surface), 40%) !important;
}
</style>
