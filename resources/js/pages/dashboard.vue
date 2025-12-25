<script setup>
import { onMounted, onUnmounted, ref } from 'vue'
import { useRouter } from 'vue-router'

const userName = ref('')
const userNim = ref('')

// Get user data from session storage
const fetchUserData = () => {
  try {
    const userData = JSON.parse(sessionStorage.getItem('user_data'))
    if (userData && userData.namalengkap) {
      userName.value = userData.namalengkap
    }
    if (userData && (userData.nim || userData.username)) {
      userNim.value = userData.nim || userData.username
    }
  } catch (error) {
    console.error('Error fetching user data:', error)
  }
}

// Fetch user data when component mounts
onMounted(() => {
  fetchUserData()
  updateToday()
  timer = setInterval(updateToday, 1000)
  loadNotifications()
})

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

// Daftar nama hari dalam Bahasa Indonesia
const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabat']

// Daftar nama bulan dalam Bahasa Indonesia
const months = [
  'Januari',
  'Februari',
  'Maret',
  'April',
  'Mei',
  'Juni',
  'Juli',
  'Agustus',
  'September',
  'Oktober',
  'November',
  'Desember',
]

function updateToday() {
  const now = new Date()
  const dayName = days[now.getDay()]
  const date = now.getDate()
  const monthName = months[now.getMonth()]
  const year = now.getFullYear()
  
  today.value = `${dayName}, ${date} ${monthName} ${year}`
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

  // Jika tracking number format Pos Indonesia (misal: P2303300137522)
  if (/^P\d+$/i.test(trackingNumber.value)) {
    window.open(`https://www.posindonesia.co.id/en/tracking/${trackingNumber.value}`, '_blank')
    
    return
  }

  const result = shipments.find(s => s.trackingNumber === trackingNumber.value)
  if (result) {
    trackingResults.value = result
    showTrackingModal.value = true
  } else {
    searchError.value = true
    trackingResults.value = null
  }
}

const notifications = ref([])

const showNotificationsModal = ref(false)
const router = useRouter()
function goToNotification(notification) {
  const targetPath = notification.type === 'pra-yudisium'
    ? '/pra-yudisium'
    : notification.route

  if (!targetPath) return

  // Untuk admin route, sertakan dataId agar bisa dipakai memuat entri terkait
  router.push({
    path: targetPath,
    query: {
      notif: notification.id,
      dataId: notification.dataId,
      comment: notification.comment,
    },
  })
}

const loadNotifications = async () => {
  try {
    const headers = { Accept: 'application/json' }
    const token = sessionStorage.getItem('jwt_token')
    if (token)
      headers.Authorization = `Bearer ${token}`

    const res = await fetch('/api/pra-yudisium', { headers })
    if (!res.ok)
      throw new Error('Gagal memuat notifikasi')

    const json = await res.json()
    const data = Array.isArray(json.data) ? json.data : []

    const studentNotifications = data
      .filter(item => item.nim === userNim.value || String(item.kdmahasiswa) === String(userNim.value))
      .filter(item => {
        const hasRevision = [item.status_foto, item.status_ijazah, item.status_ktp]
          .some(status => status === 'revision')
        if (hasRevision)
          return true

        return !!item.comment
      })
      .map(item => ({
        id: `pra-${item.kdprayudisium}`,
        icon: 'ri-chat-3-line',
        color: 'warning',
        title: item.comment || 'Dokumen pra yudisium perlu diperbaiki.',
        type: 'pra-yudisium',
        route: '/pra-yudisium',
        dataId: item.kdprayudisium,
        comment: item.comment,
      }))

    let legalisasiNotifications = []
    try {
      const resLegalisasi = await fetch('/api/form-legalisasi', { headers })
      if (resLegalisasi.ok) {
        const legalisasiJson = await resLegalisasi.json()
        const legalisasiData = Array.isArray(legalisasiJson.data) ? legalisasiJson.data : []
        legalisasiNotifications = legalisasiData
          .filter(item => !!item.tgl_dikirim)
          .map(item => ({
            id: `legalisasi-${item.kdlegalisasi || item.id}`,
            icon: 'ri-check-line',
            color: 'success',
            title: 'Pengajuan legalisasi Anda disetujui.',
            type: 'legalisasi',
            route: '/pendaftaran-legalisasi',
            dataId: item.kdlegalisasi || item.id,
            comment: 'Pengajuan legalisasi Anda disetujui.',
          }))
      }
    } catch (err) {
      console.error('Gagal memuat notifikasi legalisasi', err)
    }

    const approvedNotifications = data
      .filter(item => item.nim === userNim.value || String(item.kdmahasiswa) === String(userNim.value))
      .filter(item => {
        if (item.is_validate)
          return true

        const statuses = [item.status_foto, item.status_ijazah, item.status_ktp].filter(Boolean)
        return statuses.length === 3 && statuses.every(status => status === 'approved')
      })
      .map(item => ({
        id: `pra-approve-${item.kdprayudisium}`,
        icon: 'ri-check-line',
        color: 'success',
        title: 'Pengajuan Pra Yudisium Anda disetujui.',
        type: 'pra-yudisium',
        route: '/pra-yudisium',
        dataId: item.kdprayudisium,
        comment: 'Pengajuan Pra Yudisium Anda disetujui.',
      }))

    // Notifikasi untuk balasan admin di validasi ijazah & transkrip
    let validasiNotifications = []
    try {
      const resValidasi = await fetch('/api/ak-validasi-ijazah', { headers })
      if (resValidasi.ok) {
        const validasiJson = await resValidasi.json()
        const validasiData = Array.isArray(validasiJson) ? validasiJson : []
        validasiNotifications = validasiData
          .filter(item => item.nim === userNim.value || String(item.kdmahasiswa) === String(userNim.value))
          .filter(item => !!item.last_admin_comment_text)
          .map(item => ({
            id: `validasi-${item.kdvalidasiijazahmahasiswa}`,
            icon: 'ri-chat-3-line',
            color: 'info',
            title: item.last_admin_comment_text,
            type: 'validasi-ijazah',
            route: '/validasi-ijazah',
            dataId: item.kdvalidasiijazahmahasiswa,
            comment: item.last_admin_comment_text,
          }))
      }
    } catch (err) {
      console.error('Gagal memuat notifikasi validasi ijazah', err)
    }

    notifications.value = [...studentNotifications, ...approvedNotifications, ...legalisasiNotifications, ...validasiNotifications]
  } catch (err) {
    console.error(err)
  }
}
</script>

<template>
  <VContainer
    fluid
    class="pa-4 dashboard-bg"
  >
    <VRow>
      <!-- Welcome Card, Search, Riwayat Pembayaran -->
      <VCol
        cols="12"
        md="8"
      >
        <VCard
          class="pa-6 welcome-card mb-4"
          elevation="2"
          style="min-block-size: 230px;"
        >
          <VRow align="center">
            <VCol
              cols="12"
              md="12"
              class="d-flex flex-column justify-center"
            >
              <div class="welcome-title mb-2">
                Hi, {{ userName || 'Mahasiswa' }}!
              </div>
              <div class="welcome-subtitle mb-2">
                Selamat atas gelar barunya...
              </div>
              <div
                class="welcome-date mb-4"
                style="min-inline-size: 200px;"
              >
                {{ today }}
              </div>
            </VCol>
            <!--
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
            -->
          </VRow>
        </VCard>
        <VCard
          class="pa-4 mb-4 mx-auto"
          :class="$vuetify.display.smAndDown ? 'w-100' : 'w-100'"
          style="margin-block: 18px 16px;"
        >
          <VTextField
            v-model="trackingNumber"
            label="Search"
            placeholder="Input Tracking Number"
            append-inner-icon="ri-search-line"
            density="comfortable"
            hide-details
            @click:append-inner="trackShipment"
            @keyup.enter="trackShipment"
          />
        </VCard>
        <VCard
          class="pa-4 mb-4"
          style="margin-block-start: 0;"
        >
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
      <!-- Notifications -->
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
                style="cursor: pointer;"
                @click="goToNotification(notification)"
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
              style="cursor: pointer;"
              @click="goToNotification(notification)"
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
  </VContainer>
</template>

<style scoped>
.dashboard-bg {
  background: var(--v-theme-background);

  /* min-block-size: 100vh; */
  transition: background 0.3s;
}

.welcome-card,
.notification-card,
.stat-card {
  padding: 24px;
  border-radius: 8px;
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
  font-size: 1.8rem;
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
