<script setup>
import {
  CategoryScale,
  Chart,
  Filler,
  LineController,
  LineElement,
  LinearScale,
  PointElement,
  Tooltip,
} from 'chart.js'
import { computed, nextTick, onMounted, onUnmounted, ref, watch } from 'vue'
import { useRouter } from 'vue-router'
import { useTheme } from 'vuetify'

Chart.register(
  CategoryScale,
  Filler,
  LineController,
  LineElement,
  LinearScale,
  PointElement,
  Tooltip,
)

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
  loadNilaiKrs()
})


// Tanggal realtime
const today = ref('')
let timer = null

// Daftar nama hari dalam Bahasa Indonesia
const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu']

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
  if (chartInstance.value) {
    chartInstance.value.destroy()
    chartInstance.value = null
  }
})

const defaultSummary = {
  statusLabel: 'Tidak ada',
  subtitle: 'Belum ada data pengajuan.',
}

const praSummary = ref({ ...defaultSummary })
const validasiSummary = ref({ ...defaultSummary })
const legalisasiSummary = ref({ ...defaultSummary })

const formatShortDate = value => {
  if (!value) return null
  const date = new Date(value)
  if (Number.isNaN(date.getTime())) return null
  
  return date.toLocaleDateString('id-ID')
}

const getLatestItem = items => {
  if (!items.length) return null
  
  return [...items].sort((a, b) => {
    const da = new Date(a?.update_at || a?.create_at || a?.tgl_dikirim || 0).getTime()
    const db = new Date(b?.update_at || b?.create_at || b?.tgl_dikirim || 0).getTime()
    
    return db - da
  })[0]
}

const setPraSummary = records => {
  if (!records.length) {
    praSummary.value = { ...defaultSummary }
    
    return
  }
  const latest = getLatestItem(records)
  const statuses = [latest?.status_foto, latest?.status_ijazah, latest?.status_ktp].filter(Boolean)
  const hasRevision = statuses.some(status => status === 'revision')
  const allApproved = statuses.length === 3 && statuses.every(status => status === 'approved')
  const lastDate = formatShortDate(latest?.update_at || latest?.create_at)

  if (hasRevision) {
    praSummary.value = {
      statusLabel: 'Perlu Revisi',
      subtitle: 'Ada dokumen yang perlu diperbaiki.',
    }
  } else if (latest?.is_validate || allApproved) {
    praSummary.value = {
      statusLabel: 'Disetujui',
      subtitle: 'Pengajuan Anda sudah disetujui.',
    }
  } else {
    praSummary.value = {
      statusLabel: 'Diproses',
      subtitle: lastDate ? `Update terakhir ${lastDate}.` : 'Menunggu verifikasi.',
    }
  }
}

const setValidasiSummary = records => {
  if (!records.length) {
    validasiSummary.value = { ...defaultSummary }
    
    return
  }
  const latest = getLatestItem(records)
  const isApproved = !!latest?.is_ijazah_validate && !!latest?.is_transkrip_validate
  const hasComment = !!latest?.last_admin_comment_text
  const lastDate = formatShortDate(latest?.last_comment_at || latest?.create_at)

  if (isApproved) {
    validasiSummary.value = {
      statusLabel: 'Disetujui',
      subtitle: 'Ijazah & transkrip sudah divalidasi.',
    }
  } else if (hasComment) {
    validasiSummary.value = {
      statusLabel: 'Ada Catatan',
      subtitle: 'Ada komentar admin terbaru.',
    }
  } else {
    validasiSummary.value = {
      statusLabel: 'Diproses',
      subtitle: lastDate ? `Update terakhir ${lastDate}.` : 'Menunggu verifikasi.',
    }
  }
}

const flattenCommentTree = (items, flattened = []) => {
  if (!Array.isArray(items)) return flattened

  items.forEach(item => {
    flattened.push(item)
    if (Array.isArray(item.replies) && item.replies.length)
      flattenCommentTree(item.replies, flattened)
  })

  return flattened
}

const setLegalisasiSummary = records => {
  if (!records.length) {
    legalisasiSummary.value = { ...defaultSummary }
    
    return
  }
  const statusPriority = {
    Diterima: 6,
    Gagal: 5,
    Dikirim: 4,
    'Proses Pengiriman': 3,
    'Belum Dibayar': 2,
    Pending: 1,
  }

  const getLegalisasiStatus = item => {
    const hasBiaya = item?.biaya_legalisasi !== null && item?.biaya_legalisasi !== undefined && item?.biaya_legalisasi !== ''
    const hasResi = !!item?.noresi

    if (item?.status_penerimaan === 'received') return 'Diterima'
    if (item?.status_penerimaan === 'not_received') return 'Gagal'
    if (hasBiaya && hasResi && item?.tgl_dikirim) return 'Dikirim'
    if (hasBiaya && hasResi) return 'Proses Pengiriman'
    if (hasBiaya && !hasResi) return 'Belum Dibayar'

    return 'Pending'
  }

  const candidates = records.map(item => {
    const status = getLegalisasiStatus(item)
    const priority = statusPriority[status] || 0
    const sortTime = toTimestamp(item?.update_at, item?.tgl_dikirim, item?.create_at)

    return {
      item,
      status,
      priority,
      sortTime,
    }
  })

  const best = candidates.reduce((currentBest, entry) => {
    if (!currentBest) return entry
    if (entry.priority !== currentBest.priority)
      return entry.priority > currentBest.priority ? entry : currentBest

    return entry.sortTime > currentBest.sortTime ? entry : currentBest
  }, null)

  const status = best?.status || 'Pending'
  const lastDate = formatShortDate(best?.item?.tgl_dikirim || best?.item?.update_at || best?.item?.create_at)

  if (status === 'Diterima') {
    legalisasiSummary.value = {
      statusLabel: 'Diterima',
      subtitle: 'Dokumen legalisasi sudah diterima.',
    }
    return
  }

  if (status === 'Gagal') {
    legalisasiSummary.value = {
      statusLabel: 'Gagal',
      subtitle: 'Pengiriman legalisasi gagal.',
    }
    return
  }

  if (status === 'Dikirim') {
    legalisasiSummary.value = {
      statusLabel: 'Dikirim',
      subtitle: lastDate ? `Dikirim ${lastDate}.` : 'Dokumen sudah dikirim.',
    }
    return
  }

  if (status === 'Proses Pengiriman') {
    legalisasiSummary.value = {
      statusLabel: 'Proses Pengiriman',
      subtitle: 'Dokumen sedang diproses pengiriman.',
    }
    return
  }

  if (status === 'Belum Dibayar') {
    legalisasiSummary.value = {
      statusLabel: 'Belum Dibayar',
      subtitle: 'Menunggu pembayaran legalisasi.',
    }
    return
  }

  legalisasiSummary.value = {
    statusLabel: 'Pending',
    subtitle: 'Menunggu verifikasi admin.',
  }
}

const notifications = ref([])
const nilaiKrs = ref([])
const nilaiLoading = ref(false)
const nilaiError = ref('')

const roundToQuarter = value => Math.round(value * 4) / 4

const chartItems = computed(() => {
  const buckets = new Map()

  nilaiKrs.value.forEach(item => {
    const rawValue = Number(item.nilaiangka)
    if (Number.isNaN(rawValue)) return

    const rounded = roundToQuarter(rawValue)
    const key = rounded.toFixed(2)

    buckets.set(key, (buckets.get(key) || 0) + 1)
  })

  return Array.from(buckets.entries())
    .map(([label, count]) => ({
      label,
      count,
      value: Number(label),
    }))
    .sort((a, b) => b.value - a.value)
})

const chartCanvas = ref(null)
const chartInstance = ref(null)

const showNotificationsModal = ref(false)
const router = useRouter()
const theme = useTheme()
const isDark = computed(() => theme.global.current.value.dark)

const goTo = path => {
  if (!path) return
  router.push(path)
}

const statusPillClass = status => {
  if (status === 'Disetujui' || status === 'Dikirim' || status === 'Diterima')
    return 'status-pill status-success'
  if (status === 'Perlu Revisi' || status === 'Ada Catatan' || status === 'Belum Dibayar')
    return 'status-pill status-warning'
  if (status === 'Diproses' || status === 'Menunggu' || status === 'Proses Pengiriman')
    return 'status-pill status-info'
  if (status === 'Gagal')
    return 'status-pill status-error'
  
  return 'status-pill status-muted'
}

const loadNilaiKrs = async () => {
  nilaiLoading.value = true
  nilaiError.value = ''
  try {
    const headers = { Accept: 'application/json' }
    const token = sessionStorage.getItem('jwt_token')
    if (token)
      headers.Authorization = `Bearer ${token}`

    const res = await fetch('/api/nilai-krs', { headers })
    const json = await res.json()
    if (!res.ok || json.success === false)
      throw new Error(json.message || 'Gagal memuat data nilai')

    nilaiKrs.value = Array.isArray(json.data) ? json.data : []
  } catch (err) {
    nilaiError.value = err.message || 'Gagal memuat data nilai'
  } finally {
    nilaiLoading.value = false
  }
}

watch(chartItems, async () => {
  if (nilaiLoading.value) return
  await nextTick()
  buildNilaiChart()
})

watch(isDark, () => {
  if (!chartInstance.value) return

  const tickColor = isDark.value ? '#fff' : 'rgba(0, 0, 0, 0.6)'
  const gridColor = isDark.value ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)'

  chartInstance.value.options.scales.x.ticks.color = tickColor
  chartInstance.value.options.scales.y.ticks.color = tickColor
  chartInstance.value.options.scales.y.grid.color = gridColor
  chartInstance.value.update()
})

const buildNilaiChart = () => {
  if (!chartCanvas.value) return

  const items = [...chartItems.value].sort((a, b) => a.value - b.value)
  const labels = items.map(item => item.label)
  const data = items.map(item => item.count)

  if (chartInstance.value) {
    chartInstance.value.data.labels = labels
    chartInstance.value.data.datasets[0].data = data
    chartInstance.value.update()
    
    return
  }

  chartInstance.value = new Chart(chartCanvas.value, {
    type: 'line',
    data: {
      labels,
      datasets: [
        {
          data,
          borderColor: '#17a2a6',
          backgroundColor: 'rgba(23, 162, 166, 0.25)',
          fill: true,
          tension: 0.35,
          borderWidth: 2,
          pointRadius: 4,
          pointHoverRadius: 6,
          pointBackgroundColor: '#17a2a6',
          pointBorderWidth: 0,
        },
      ],
    },
    options: {
      responsive: true,
      maintainAspectRatio: false,
      plugins: {
        legend: { display: false },
        tooltip: {
          callbacks: {
            title: context => `Nilai ${context[0].label}`,
            label: context => `${context.raw} mata kuliah`,
          },
        },
      },
      scales: {
        x: {
          grid: { display: false },
          ticks: { color: isDark.value ? '#fff' : 'rgba(0, 0, 0, 0.6)' },
        },
        y: {
          beginAtZero: true,
          ticks: { precision: 0, color: isDark.value ? '#fff' : 'rgba(0, 0, 0, 0.6)' },
          grid: { color: isDark.value ? 'rgba(255, 255, 255, 0.08)' : 'rgba(0, 0, 0, 0.06)' },
        },
      },
    },
  })
}

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

const toTimestamp = (...values) => {
  for (const value of values) {
    if (!value) continue
    if (typeof value === 'number') return value

    let parsed = Date.parse(value)
    if (!Number.isNaN(parsed)) return parsed

    if (typeof value === 'string') {
      const normalized = value
        .replace(/\.\d+$/, '')
        .replace(' ', 'T')

      parsed = Date.parse(normalized)
      if (!Number.isNaN(parsed)) return parsed
    }
  }

  return 0
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
        sortTime: toTimestamp(item.update_at, item.create_at),
      }))

    const praRecords = data.filter(item => item.nim === userNim.value || String(item.kdmahasiswa) === String(userNim.value))

    setPraSummary(praRecords)

    let legalisasiNotifications = []
    try {
      const resLegalisasi = await fetch('/api/form-legalisasi', { headers })
      if (resLegalisasi.ok) {
        const legalisasiJson = await resLegalisasi.json()
        const legalisasiData = Array.isArray(legalisasiJson.data) ? legalisasiJson.data : []
        const legalisasiRecords = legalisasiData.filter(item => item.nim === userNim.value || String(item.kdmahasiswa) === String(userNim.value))

        setLegalisasiSummary(legalisasiRecords)
        legalisasiNotifications = legalisasiData
          .filter(item => !!item.tgl_dikirim || !!item.noresi || (item.biaya_legalisasi !== null && item.biaya_legalisasi !== undefined && item.biaya_legalisasi !== ''))
          .map(item => {
            const isApproved = !!item.tgl_dikirim
            const hasResi = !!item.noresi
            const hasBiaya = item.biaya_legalisasi !== null && item.biaya_legalisasi !== undefined && item.biaya_legalisasi !== ''

            let title = 'Ada pembaruan pada pengajuan legalisasi Anda.'
            if (isApproved) title = 'Pengajuan legalisasi Anda disetujui.'
            else if (hasResi && hasBiaya) title = 'Resi dan biaya legalisasi Anda sudah diinput.'
            else if (hasResi) title = 'Nomor resi legalisasi Anda sudah diinput.'
            else if (hasBiaya) title = 'Biaya legalisasi Anda sudah diinput.'

            return {
              id: `legalisasi-${item.kdlegalisasi || item.id}`,
              icon: isApproved ? 'ri-check-line' : 'ri-information-line',
              color: isApproved ? 'success' : 'info',
              title,
              type: 'legalisasi',
              route: '/pendaftaran-legalisasi',
              dataId: item.kdlegalisasi || item.id,
              comment: title,
              sortTime: toTimestamp(item.update_at, item.tgl_dikirim, item.create_at),
            }
          })
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
        sortTime: toTimestamp(item.update_at, item.tgl_validate, item.create_at),
      }))

    // Notifikasi untuk balasan admin di validasi ijazah & transkrip
    let validasiNotifications = []
    try {
      const resValidasi = await fetch('/api/ak-validasi-ijazah', { headers })
      if (resValidasi.ok) {
        const validasiJson = await resValidasi.json()
        const validasiData = Array.isArray(validasiJson) ? validasiJson : []
        const validasiRecords = validasiData.filter(item => item.nim === userNim.value || String(item.kdmahasiswa) === String(userNim.value))

        setValidasiSummary(validasiRecords)

        const notifItems = []

        await Promise.all(validasiRecords.map(async record => {
          const recordId = record?.kdvalidasiijazahmahasiswa
          if (!recordId) return
          try {
            const resComments = await fetch(`/api/comments/${recordId}`, { headers })
            if (!resComments.ok) return
            const commentJson = await resComments.json()
            const flat = flattenCommentTree(Array.isArray(commentJson) ? commentJson : [])

            flat
              .filter(comment => comment?.user_type === 'tendik')
              .forEach(comment => {
                const title = comment?.text || comment?.comment || 'Komentar admin'

                notifItems.push({
                  id: `validasi-${recordId}-${comment?.id ?? Date.now()}`,
                  icon: 'ri-chat-3-line',
                  color: 'info',
                  title,
                  type: 'validasi-ijazah',
                  route: '/validasi-ijazah',
                  dataId: recordId,
                  comment: title,
                  sortTime: toTimestamp(comment?.create_at, comment?.date, comment?.created_at),
                })
              })
          } catch (err) {
            console.error('Gagal memuat komentar validasi ijazah', err)
          }
        }))

        validasiNotifications = notifItems
          .sort((a, b) => (b.sortTime || 0) - (a.sortTime || 0))
      }
    } catch (err) {
      console.error('Gagal memuat notifikasi validasi ijazah', err)
    }

    // Sort all notifications by newest first (reverse chronological)
    const allNotifications = [...studentNotifications, ...approvedNotifications, ...legalisasiNotifications, ...validasiNotifications]

    notifications.value = allNotifications
      .sort((a, b) => (b.sortTime || 0) - (a.sortTime || 0))
      .map(({ sortTime, ...item }) => item)
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
    <VRow class="mb-0 row-tight">
      <!-- Welcome Card, Status Summary -->
      <VCol
        cols="12"
        md="8"
      >
        <VCard
          class="pa-6 welcome-card mb-6"
          elevation="2"
          style="min-block-size: 160px;"
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
        <VCard class="pa-4 mb-3 status-card">
          <div class="text-h6 font-weight-bold mb-3">
            Status Pengajuan
          </div>
          <div class="status-grid">
            <div class="status-item">
              <div class="status-title">
                Pra Yudisium
              </div>
              <div :class="statusPillClass(praSummary.statusLabel)">
                {{ praSummary.statusLabel }}
              </div>
              <div class="status-subtitle">
                {{ praSummary.subtitle }}
              </div>
              <VBtn
                size="small"
                variant="flat"
                class="status-action"
                @click="goTo('/pra-yudisium')"
              >
                Lihat Detail
              </VBtn>
            </div>
            <div class="status-item">
              <div class="status-title">
                Validasi Ijazah
              </div>
              <div :class="statusPillClass(validasiSummary.statusLabel)">
                {{ validasiSummary.statusLabel }}
              </div>
              <div class="status-subtitle">
                {{ validasiSummary.subtitle }}
              </div>
              <VBtn
                size="small"
                variant="flat"
                class="status-action"
                @click="goTo('/validasi-ijazah')"
              >
                Lihat Detail
              </VBtn>
            </div>
            <div class="status-item">
              <div class="status-title">
                Legalisasi
              </div>
              <div :class="statusPillClass(legalisasiSummary.statusLabel)">
                {{ legalisasiSummary.statusLabel }}
              </div>
              <div class="status-subtitle">
                {{ legalisasiSummary.subtitle }}
              </div>
              <VBtn
                size="small"
                variant="flat"
                class="status-action"
                @click="goTo('/pendaftaran-legalisasi')"
              >
                Lihat Detail
              </VBtn>
            </div>
          </div>
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
          style="block-size: 455px;"
        >
          <div class="d-flex align-center justify-space-between mb-1">
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
          </div>
        </VCard>
      </VCol>
    </VRow>
    <VRow class="mb-0 row-tight">
      <VCol cols="12">
        <VCard class="pa-4 mb-3 nilai-card">
          <div class="text-h6 font-weight-bold mb-3">
            Distribusi Nilai
          </div>
          <div
            v-if="nilaiLoading"
            class="nilai-empty"
          >
            Memuat data nilai...
          </div>
          <div
            v-else-if="nilaiError"
            class="nilai-empty"
          >
            {{ nilaiError }}
          </div>
          <div
            v-else-if="!chartItems.length"
            class="nilai-empty"
          >
            Belum ada data nilai.
          </div>
          <div
            v-else
            class="nilai-chart"
          >
            <div class="nilai-canvas-wrap">
              <canvas ref="chartCanvas" />
            </div>
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

.notification-card {
  display: flex;
  flex-direction: column;
}

.notification-scroll {
  flex: 1;
  min-block-size: 0;
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

.notification-item :deep(.v-list-item-title) {
  overflow: visible;
  text-overflow: clip;
  white-space: normal;
}

.status-card {
  border-radius: 12px;
}

.nilai-card {
  border-radius: 12px;
}

.nilai-chart {
  display: flex;
  flex-direction: column;
  gap: 12px;
}

.nilai-canvas-wrap {
  position: relative;
  block-size: 170px;
  inline-size: 100%;
}

.nilai-empty {
  color: rgba(var(--v-theme-on-surface), 0.6);
  font-style: italic;
}

.status-grid {
  display: grid;
  gap: 16px;
  grid-template-columns: repeat(3, minmax(0, 1fr));
}

.row-tight {
  margin-block-start: 0 !important;
}

.row-tight > .v-col {
  padding-block-start: 0 !important;
}

.status-item {
  display: flex;
  flex-direction: column;
  align-items: center;
  border: 1px solid rgba(var(--v-theme-on-surface), 0.08);
  border-radius: 10px;
  background: rgba(var(--v-theme-surface), 0.9);
  gap: 10px;
  min-block-size: 160px;
  padding-block: 16px;
  padding-inline: 16px;
  text-align: center;
}

.status-title {
  font-size: 1rem;
  font-weight: 700;
}

.status-subtitle {
  color: rgba(var(--v-theme-on-surface), 0.7);
  font-size: 0.9rem;
  min-block-size: 40px;
}

.status-action {
  align-self: center;
  border-radius: 10px;
  background: #17a2a6 !important;
  box-shadow: 0 6px 12px rgba(23, 162, 166, 35%);
  color: #fff !important;
  font-weight: 600;
  padding-inline: 18px;
  text-transform: none;
}

.status-pill {
  display: inline-flex;
  align-items: center;
  border-radius: 999px;
  font-size: 0.75rem;
  font-weight: 700;
  letter-spacing: 0.04em;
  padding-block: 4px;
  padding-inline: 10px;
  text-transform: uppercase;
}

.status-success {
  background: rgba(27, 196, 125, 15%);
  color: #1bc47d;
}

.status-warning {
  background: rgba(255, 193, 7, 18%);
  color: #c58b00;
}

.status-info {
  background: rgba(33, 150, 243, 12%);
  color: #1a73c9;
}

.status-error {
  background: rgba(230, 57, 70, 15%);
  color: #e63946;
}

.status-muted {
  background: rgba(var(--v-theme-on-surface), 0.08);
  color: rgba(var(--v-theme-on-surface), 0.6);
}

@media (max-width: 960px) {
  .status-grid {
    grid-template-columns: minmax(0, 1fr);
    inline-size: 100%;
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
