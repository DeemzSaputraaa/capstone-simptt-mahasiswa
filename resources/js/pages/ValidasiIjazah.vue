
<template>
  <VApp>
    <VMain>
      <VContainer
        fluid
        class="pa-6 page-container"
      >
        <VAlert
          v-if="showNotifAlert"
          type="success"
          variant="tonal"
          class="mb-4"
          border="start"
          prominent
        >
          <span>{{ notifMessage }}</span>
        </VAlert>

        <div class="preview-grid">
          <div class="preview-shell">
            <DraftIjazah
              ref="ijazahPreview"
              :user="user"
            />
          </div>
          <div class="preview-shell">
            <TranskripNilai
              ref="transcriptPreview"
              :user="user"
              :matakuliah="matakuliah"
            />
          </div>
        </div>

        <div class="action-bar">
          <div class="action-bar-left">
            <VBtn
              color="success"
              @click="generateCombinedPDF"
            >
              Cetak Ijazah dan Transkrip
            </VBtn>
          </div>
          <div class="action-bar-right">
            <VBtn
              color="error"
              @click="onLapor"
            >
              Lapor
            </VBtn>
            <VBtn
              color="primary"
              @click="onSimpan"
            >
              Simpan
            </VBtn>
          </div>
        </div>

        <!-- Card 3: Komentar -->
        <VCard
          v-if="showComments"
          flat
          class="mb-6 comment-form-card"
        >
          <VCardTitle class="text-h5 mb-4">
            Komentar
          </VCardTitle>
          <VCardText>
            <!-- Form komentar -->
            <div class="comment-input-row">
              <VTextField
                v-model="newComment"
                label="Tulis komentar..."
                outlined
                dense
                hide-details
                class="comment-input-field"
                @keyup.enter="addComment"
              />
              <VBtn
                color="primary"
                size="small"
                class="comment-submit-btn"
                @click="addComment"
              >
                Kirim Komentar
              </VBtn>
            </div>
            <!-- Daftar Komentar -->
            <div class="comments-list">
              <CommentItem
                v-for="comment in comments"
                :key="comment.id"
                :comment="comment"
                :depth="0"
                :max-depth="10"
                :current-user="user"
                @add-reply="handleAddReply"
                @submit-reply="handleSubmitReply"
              />
            </div>
          </VCardText>
        </VCard>

        <!-- Validation Modal -->
        <VDialog
          v-model="showValidationModal"
          max-width="400"
        >
          <VCard>
            <VCardTitle class="text-h5">
              {{ validationMessage.includes('berhasil') ? 'Sukses' : 'Peringatan' }}
            </VCardTitle>
            <VCardText>
              {{ validationMessage }}
            </VCardText>
            <VCardActions>
              <VSpacer />
              <VBtn
                color="primary"
                @click="showValidationModal = false"
              >
                OK
              </VBtn>
            </VCardActions>
          </VCard>
        </VDialog>

        <!-- Confirm Save Modal -->
        <VDialog
          v-model="showConfirmModal"
          max-width="460"
        >
          <VCard class="confirm-card">
            <VCardTitle class="confirm-title">
              Konfirmasi Simpan
            </VCardTitle>
            <VCardText class="confirm-subtitle">
              <strong>Apakah Anda yakin ingin menyimpan perubahan?</strong>
              <br>
              <span style="color: #666; font-size: 14px;">
                Pastikan status validasi dan kelengkapan dokumen sudah sesuai sebelum menyimpan.
              </span>
            </VCardText>
            <VCardActions class="confirm-actions justify-center" style="padding: 16px; gap: 12px;">
              <VBtn
                color="default"
                variant="flat"
                class="confirm-cancel"
                @click="showConfirmModal = false"
              >
                Batal
              </VBtn>
              <VBtn
                color="primary"
                class="confirm-submit"
                variant="flat"
                @click="confirmSimpan"
              >
                Ya, Simpan
              </VBtn>
            </VCardActions>
          </VCard>
        </VDialog>

        <!-- Dialog untuk PDF Viewer -->
        <VDialog
          v-model="showPdfViewer"
          fullscreen
          hide-overlay
          transition="dialog-bottom-transition"
        >
          <VCard>
            <VToolbar
              dark
              color="primary"
            >
              <VBtn
                icon
                @click="showPdfViewer = false"
              >
                <VIcon>mdi-square-edit-outline</VIcon>
              </VBtn>
              <VToolbarTitle>{{ pdfTitle }}</VToolbarTitle>
            </VToolbar>
            
            <VCardText>
              <PdfViewer
                :pdf-data="currentPdfData"
                class="pdf-viewer-container"
              />
            </VCardText>
          </VCard>
        </VDialog>
      </VContainer>
    </VMain>
  </VApp>
</template>

<script>
import html2pdf from 'html2pdf.js'
import { onBeforeUnmount, onMounted, ref } from 'vue'
import { useRoute } from 'vue-router'
import CommentItem from '../components/CommentItem.vue'
import DraftIjazah from './components/DraftIjazah.vue'
import TranskripNilai from './components/TranskripNilai.vue'

export default {
  name: 'ValidasiIjazah',
  components: { DraftIjazah, TranskripNilai, CommentItem },
  setup() {
    const form = ref({
      nik: '',
      phone: '',
      photoIjazah: null,
      photoCtp: null,
    })

    const fileStatus = ref({
      photoIjazah: '',
      photoCtp: '',
    })

    // State untuk komentar (kosong saat awal)
    const comments = ref([])
    const validasiId = ref('me')

    const newComment = ref('')

    const showValidationModal = ref(false)
    const validationMessage = ref('')
    const showConfirmModal = ref(false)

    const ijazahPreview = ref(null)
    const transcriptPreview = ref(null)

    // Default fallback; akan diganti setelah fetch profil login
    const user = ref({
      name: '',
      nim: '',
      birthPlace: '',
      birthDate: '',
      birthDateEn: '',
      studyProgram: '',
      studyProgramEn: '',
      degree: '',
      degreeEn: '',
      graduationDate: '',
      graduationDateEn: '',
      educationLevel: '',
      educationLevelEn: '',
      accreditationDetail: '',
      accreditationDetailEn: '',
      gpa: '',
      graduationPredicate: '',
      graduationPredicateEn: '',
      thesisTitle: '',
      thesisTitleEn: '',
      transcriptPlaceDate: '',
      transcriptPlaceDateEn: '',
      transcriptSigner1Title: '',
      transcriptSigner1TitleEn: '',
      transcriptSigner1Name: '',
      certificateSigner2Title: '',
      certificateSigner2TitleEn: '',
      certificateSigner2Name: '',
    })

    const matakuliah = ref([])

    const route = useRoute()
    const notifId = route.query.notif
    const showNotifAlert = ref(false)
    const notifMessage = ref('')
    const notifTimer = ref(null)
    if (notifId) {
      showNotifAlert.value = true
      notifMessage.value = 'Ada komentar baru dari admin di Validasi Ijazah Anda. Silakan cek detail di bawah ini.'
      notifTimer.value = setTimeout(() => {
        showNotifAlert.value = false
      }, 30000)
    }

    const getAuthHeaders = () => {
      const token = sessionStorage.getItem('jwt_token')

      return token
        ? { Authorization: `Bearer ${token}` }
        : {}
    }

    const fetchCurrentUser = async () => {
      try {
        const response = await fetch('/api/me', {
          headers: {
            ...getAuthHeaders(),
          },
        })

        if (!response.ok) throw new Error(`Gagal memuat data user: ${response.status}`)

        const data = await response.json()
        const profile = data.data || data // API /api/me mengembalikan { success, data }
        const formatDateId = value => {
          if (!value) return ''
          const raw = String(value).split('T')[0]
          const match = raw.match(/^(\d{4})-(\d{2})-(\d{2})$/)
          if (!match) return value
          const [, year, month, day] = match
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
          const monthName = months[Number(month) - 1] || month
          return `${Number(day)} ${monthName} ${year}`
        }
        const formatDateEn = value => {
          if (!value) return ''
          const raw = String(value).trim().split('T')[0]
          const isoMatch = raw.match(/^(\d{4})-(\d{2})-(\d{2})$/)
          const monthMap = {
            januari: 'January',
            februari: 'February',
            maret: 'March',
            april: 'April',
            mei: 'May',
            juni: 'June',
            juli: 'July',
            agustus: 'August',
            september: 'September',
            oktober: 'October',
            november: 'November',
            desember: 'December',
            january: 'January',
            february: 'February',
            march: 'March',
            april_en: 'April',
            may: 'May',
            june: 'June',
            july: 'July',
            august: 'August',
            september_en: 'September',
            october: 'October',
            november_en: 'November',
            december: 'December',
          }
          const months = [
            'January',
            'February',
            'March',
            'April',
            'May',
            'June',
            'July',
            'August',
            'September',
            'October',
            'November',
            'December',
          ]
          if (isoMatch) {
            const [, year, month, day] = isoMatch
            const monthName = months[Number(month) - 1] || month
            return `${monthName} ${Number(day)}, ${year}`
          }
          const textMatch = raw.match(/^(\d{1,2})\s+([A-Za-z]+)\s+(\d{4})$/)
          if (textMatch) {
            const [, day, monthNameRaw, year] = textMatch
            const key = monthNameRaw.toLowerCase()
            const monthName = monthMap[key] || monthNameRaw
            return `${monthName} ${Number(day)}, ${year}`
          }
          return value
        }

        user.value = {
          name: profile.name || profile.namalengkap || '',
          nim: profile.nim || profile.username || '',
          birthPlace: profile.tempatlahir || profile.birth_place || '',
          birthDate: formatDateId(profile.tanggallahir || profile.birth_date || ''),
          birthDateEn: formatDateEn(profile.birth_date_en || profile.tanggallahir || profile.birth_date || ''),
          studyProgram: profile.study_program || profile.prodi || profile.studyProgram || '',
          studyProgramEn: profile.study_program_en || profile.namaprodiinggris || '',
          degree: profile.degree || profile.gelar || '',
          degreeEn: profile.degree_en || profile.gelaringgris || '',
          graduationDate: formatDateId(profile.graduation_date || profile.tglkelulusan || profile.graduationDate || ''),
          graduationDateEn: formatDateEn(profile.graduation_date_en || profile.tglkelulusaninggris || ''),
          educationLevel: profile.education_level || profile.jenjang || '',
          educationLevelEn: profile.education_level_en || profile.jenjanginggris || '',
          accreditationDetail: profile.accreditation_detail || profile.detailakreditasi || '',
          accreditationDetailEn: profile.accreditation_detail_en || profile.detailakreditasiinggris || '',
          gpa: profile.gpa || profile.ipk || '',
          graduationPredicate: profile.graduation_predicate || profile.predikat || '',
          graduationPredicateEn: profile.graduation_predicate_en || '',
          thesisTitle: profile.thesis_title || profile.judulkaryatulis || '',
          thesisTitleEn: profile.thesis_title_en || profile.judulkaryatulisinggris || '',
          transcriptPlaceDate: profile.transcript_place_date || profile.tmptgltranskrip || '',
          transcriptPlaceDateEn: profile.transcript_place_date_en || profile.tmptgltranskripinggris || '',
          transcriptSigner1Title: profile.transcript_signer1_title || profile.jabatanttd1transkrip || '',
          transcriptSigner1TitleEn: profile.transcript_signer1_title_en || profile.jabatanttd1transkripinggris || '',
          transcriptSigner1Name: profile.transcript_signer1_name || profile.namattd1transkrip || '',
          certificateSigner2Title: profile.certificate_signer2_title || profile.jabatanttd2ijazah || '',
          certificateSigner2TitleEn: profile.certificate_signer2_title_en || profile.jabatanttd2ijazahinggris || '',
          certificateSigner2Name: profile.certificate_signer2_name || profile.namattd2ijazah || '',
        }
      } catch (error) {
        console.error('Tidak bisa memuat data user:', error)
      }
    }

    const fetchMatakuliah = async () => {
      try {
        const response = await fetch('/api/nilai-krs', {
          headers: {
            ...getAuthHeaders(),
          },
        })

        if (!response.ok) {
          const errorText = await response.text()
          throw new Error(errorText || `Gagal memuat matakuliah (${response.status})`)
        }

        const data = await response.json()
        const rows = Array.isArray(data.data) ? data.data : data

        matakuliah.value = rows.map((row, index) => ({
          no: index + 1,
          kode: row.kodematakuliah || row.kode || '',
          nama: row.matakuliah || '',
          nama_en: row.matakuliahinggris || row.matakuliah_en || '',
          sks: row.sks || 0,
          nilai: row.nilai || '',
          nilaiangka: row.nilaiangka ?? '',
        }))
      } catch (error) {
        console.error('Tidak bisa memuat matakuliah:', error)
        matakuliah.value = []
      }
    }

    const updateFileStatus = (field, file) => {
      if (file) {
        fileStatus.value[field] = `${file.name} (${formatFileSize(file.size)})`
      } else {
        fileStatus.value[field] = ''
      }
    }

    const formatFileSize = bytes => {
      if (bytes === 0) return '0 Bytes'
      const k = 1024
      const sizes = ['Bytes', 'KB', 'MB', 'GB']
      const i = Math.floor(Math.log(bytes) / Math.log(k))
      
      return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i]
    }

    // Komentar hanya tampil setelah Lapor
    const showComments = ref(false)

    const normalizeComment = comment => {
      return {
        id: comment?.id ?? comment?.kdcomment ?? Date.now(),
        user: comment?.user || user.value?.name || 'Mahasiswa',
        text: comment?.text || comment?.comment || '',
        date: comment?.date ? new Date(comment.date) : new Date(),
        replies: Array.isArray(comment?.replies) ? comment.replies.map(normalizeComment) : [],
      }
    }

    const getCommentsEndpointId = () => validasiId.value || 'me'

    // Fungsi untuk komentar
    const addComment = async () => {
      if (!newComment.value.trim()) return

      const commentText = newComment.value.trim()

      try {
        // Simpan ke database
        const savedComment = await saveCommentToDatabase(commentText)
        const normalized = normalizeComment(savedComment)
        
        // Tambahkan ke state lokal
        comments.value.unshift(normalized)
        newComment.value = ''
      } catch (error) {
        console.error('Error saving comment:', error)

        // Tetap tambahkan ke state lokal jika gagal simpan
        comments.value.unshift(normalizeComment({
          text: commentText,
        }))
        newComment.value = ''
      }
    }


    // Fungsi untuk menyimpan komentar ke database
    const saveCommentToDatabase = async commentText => {
      try {
        console.log('Saving comment to database')

        const response = await fetch('/api/comments', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            ...getAuthHeaders(),
          },
          body: JSON.stringify({
            comment: commentText,
            parent_id: null, // Komentar utama
            kdvalidasiijazahmahasiswa: typeof validasiId.value === 'number' ? validasiId.value : undefined,
          }),
        })

        console.log('Save response status:', response.status)

        if (!response.ok) {
          const errorText = await response.text()

          console.error('Save failed:', response.status, response.statusText, errorText)
          throw new Error(`Failed to save comment: ${response.status}`)
        }

        const result = await response.json()

        console.log('Comment saved successfully:', result)
        
        return result
      } catch (error) {
        console.error('Error in saveCommentToDatabase:', error)
        throw error
      }
    }

    // Fungsi untuk memuat komentar dari database
    const loadCommentsFromDatabase = async () => {
      try {
        console.log('Loading comments from database...')

        const endpointId = getCommentsEndpointId()

        const response = await fetch(`/api/comments/${endpointId}`, {
          headers: {
            ...getAuthHeaders(),
          },
        })

        console.log('Response status:', response.status)
        
        if (response.ok) {
          const data = await response.json()

          console.log('Comments loaded:', data)
          comments.value = Array.isArray(data)
            ? data.map(normalizeComment)
            : []
        } else {
          console.error('Failed to load comments:', response.status, response.statusText)

          const errorText = await response.text()

          console.error('Error response:', errorText)
        }
      } catch (error) {
        console.error('Error loading comments:', error)
      }
    }


    const findReplyAndAddNested = (replies, parentReplyId, newReplyData) => {
      for (const reply of replies) {
        if (reply.id === parentReplyId) {
          if (!reply.replies) reply.replies = []
          reply.replies.push(newReplyData)
          
          return true
        }
        if (reply.replies && reply.replies.length > 0 && findReplyAndAddNested(reply.replies, parentReplyId, newReplyData)) return true
      }
      
      return false
    }

    // Fungsi untuk menangani balasan dari komponen CommentItem
    const handleAddReply = (parentId, replyData) => {
      const comment = comments.value.find(c => c.id === parentId)
      if (comment) {
        // Jika parentId adalah komentar utama
        if (!comment.replies) comment.replies = []
        comment.replies.push(replyData)
      } else {
        // Jika parentId adalah balasan, cari dan tambahkan secara rekursif
        const found = findReplyAndAddNested(comments.value, parentId, replyData)
        if (!found) {
          console.error('Failed to add nested reply: parent reply not found')
        }
      }
    }

    // Kirim balasan ke server (mahasiswa balas admin atau komentar lain)
    const handleSubmitReply = async payload => {
      const { parentId, text } = payload || {}
      if (!parentId || !text?.trim()) return

      try {
        const response = await fetch('/api/comments', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
            ...getAuthHeaders(),
          },
          body: JSON.stringify({
            comment: text,
            parent_id: parentId,
            kdvalidasiijazahmahasiswa: typeof validasiId.value === 'number' ? validasiId.value : undefined,
          }),
        })

        if (!response.ok) {
          console.error('Failed to save reply', response.status)
          
          return
        }

        const saved = await response.json()
        const normalized = normalizeComment(saved)

        // Sisipkan balasan ke posisi yang tepat
        const parent = comments.value.find(c => c.id === parentId)
        if (parent) {
          if (!parent.replies) parent.replies = []
          parent.replies.push(normalized)
        } else {
          const inserted = findReplyAndAddNested(comments.value, parentId, normalized)
          if (!inserted) {
            console.error('Failed to place reply: parent not found')
          }
        }
      } catch (error) {
        console.error('Error submitting reply:', error)
      }
    }

    const formatDate = date => {
      return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
      }).format(date)
    }

    const handleSubmit = () => {
      // Validasi contoh, sesuaikan dengan kebutuhan
      if (!form.value.nik || !form.value.phone) {
        validationMessage.value = 'Mohon lengkapi NIK dan nomor telepon'
        showValidationModal.value = true
        
        return
      }
      if (!form.value.photoIjazah || !form.value.photoCtp) {
        validationMessage.value = 'Mohon lengkapi semua file yang diperlukan'
        showValidationModal.value = true
        
        return
      }
      validationMessage.value = 'Form berhasil dikirim'
      showValidationModal.value = true
    }

    const onLapor = async () => {
      showComments.value = true

      // Muat komentar dari database saat pertama kali menampilkan komentar
      await loadCommentsFromDatabase()
    }

    const onSimpan = () => {
      showConfirmModal.value = true
    }

    const confirmSimpan = async () => {
      showConfirmModal.value = false
      try {
        const csrf = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content')

        const response = await fetch('/api/ak-validasi-ijazah', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            Accept: 'application/json',
            ...(csrf ? { 'X-CSRF-TOKEN': csrf } : {}),
            ...getAuthHeaders(),
          },
          body: JSON.stringify({}),
        })

        if (!response.ok) {
          const errorText = await response.text()
          throw new Error(errorText || `Gagal menyimpan (${response.status})`)
        }

        validationMessage.value = 'Perubahan berhasil disimpan'
        showValidationModal.value = true
      } catch (error) {
        validationMessage.value = 'Gagal menyimpan data'
        showValidationModal.value = true
        console.error('Error saving validasi ijazah:', error)
      }
    }

    // Fungsi untuk membuka draft di tab baru
    const openInNewTab = () => {
      if (!ijazahPreview.value || !transcriptPreview.value) return

      const ijazahHTML = ijazahPreview.value.outerHTML
      const transcriptHTML = transcriptPreview.value.outerHTML

      const styles = `
        <style>
          body {
            font-family: 'Times New Roman', Times, serif;
            background-color: #f0f0f0;
            margin: 0;
            padding: 0;
            display: flex;
            justify-content: center;
            align-items: center;
            flex-direction: column;
          }
          .page {
            background: white;
            width: 210mm;
            height: 297mm;
            margin: 20mm auto;
            padding: 20mm;
            box-sizing: border-box;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
            page-break-after: always;
          }
          .page:last-child {
            page-break-after: auto;
          }

          /* Copy relevant styles from the component */
          .draft-ijazah-preview, .transcript-preview {
            border: none !important;
            box-shadow: none !important;
            margin: 0 !important;
            padding: 0 !important;
            font-size: 12pt !important;
            max-inline-size: 100% !important;
            min-block-size: initial !important;
          }
           .d-flex { display: flex; }
          .justify-end { justify-content: flex-end; }
          .justify-space-between { justify-content: space-between; }
          .align-center { align-items: center; }
          .align-items-flex-end { align-items: flex-end; }
          .mt-6 { margin-top: 1.5rem; }
          .mt-4 { margin-top: 1rem; }
          .mt-8 { margin-top: 2rem; }
          .mt-12 { margin-top: 3rem; }
          .mb-2 { margin-bottom: 0.5rem; }
          .text-right { text-align: right; }
          .font-weight-bold { font-weight: bold; }
          .text-center { text-align: center; }
          .fixed-header, .v-table {
            width: 100%;
            border-collapse: collapse;
          }
          .v-table th, .v-table td {
            border: 1px solid #000;
            padding: 4px 8px;
            text-align: left;
          }
          .v-table th {
             font-weight: bold;
          }

           .no-col { width: 45px; text-align: center; }
          .kode-col { width: 110px; }
          .matkul-col { width: 55%; }
          .vertical-header {
            writing-mode: vertical-rl;
            transform: rotate(180deg);
            text-align: center !important;
            vertical-align: middle;
            width: 40px;
          }
           .fixed-header th, .fixed-header td {
            height: auto; /* Let content decide */
          }
          
          /* Hide elements not needed for PDF */
          .v-application, .v-application__wrap, .v-main, .v-container, .v-card, .v-card-title, .v-card-text, .v-card-actions {
              all: unset !important;
          }
          body > .v-application > .v-application__wrap {
              min-height: auto !important;
          }

          @media print {
            body, .page {
              margin: 0;
              box-shadow: none;
            }
          }
        </style>
      `

      const newWindow = window.open('', '_blank')
      if (newWindow) {
        newWindow.document.write(`
          <!DOCTYPE html>
          <html>
            <head>
              <title>Preview Ijazah & Transkrip</title>
              ${styles}
            </head>
            <body>
              <div class="page">
                ${ijazahHTML}
              </div>
              <div class="page">
                ${transcriptHTML}
              </div>
            </body>
          </html>
        `)
        newWindow.document.close()
      }
    }

    // Load comments saat komponen dimount jika ada notifikasi
    onMounted(async () => {
      await fetchCurrentUser()
      await fetchMatakuliah()

      if (notifId) {
        showComments.value = true
        await loadCommentsFromDatabase()
      }
    })

    onBeforeUnmount(() => {
      if (notifTimer.value) {
        clearTimeout(notifTimer.value)
        notifTimer.value = null
      }
    })

    // Tambahkan fungsi generatePDF
    const generatePDF = () => {
      const content = document.createElement('div')
      
      // Ambil konten ijazah dan transkrip
      const ijazahContent = ijazahPreview.value.$el.cloneNode(true)
      const transcriptContent = transcriptPreview.value.$el.cloneNode(true)
      
      // Gabungkan konten
      content.appendChild(ijazahContent)
      content.appendChild(transcriptContent)
      
      // Konfigurasi PDF
      const options = {
        margin: 10,
        filename: 'ijazah-transkrip.pdf',
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { 
          scale: 2,
          useCORS: true,
          logging: true,
        },
        jsPDF: { 
          unit: 'mm', 
          format: 'a4', 
          orientation: 'portrait', 
        },
      }

      // Generate PDF
      html2pdf().from(content).set(options).save()
    }

    const container = ref(null)
    const canvas = ref(null)
    const scale = ref(1)
    const rotation = ref(0)
    let pdfDoc = null
    let pageNum = 1

    const renderPage = async () => {
      const page = await pdfDoc.getPage(pageNum)
      const viewport = page.getViewport({ scale: scale.value, rotation: rotation.value })
      
      const context = canvas.value.getContext('2d')

      canvas.value.height = viewport.height
      canvas.value.width = viewport.width

      await page.render({
        canvasContext: context,
        viewport: viewport,
      }).promise
    }

    const zoomIn = () => {
      scale.value = Math.min(scale.value + 0.1, 3)
      renderPage()
    }

    const zoomOut = () => {
      scale.value = Math.max(scale.value - 0.1, 0.5)
      renderPage()
    }

    const rotate = () => {
      rotation.value = (rotation.value + 90) % 360
      renderPage()
    }

    const download = () => {
      const link = document.createElement('a')

      link.href = props.pdfData
      link.download = 'document.pdf'
      link.click()
    }

    const print = () => {
      const iframe = document.createElement('iframe')

      iframe.style.display = 'none'
      iframe.src = props.pdfData
      document.body.appendChild(iframe)
      iframe.contentWindow.print()
    }

    onMounted(async () => {
      try {
        pdfDoc = await pdfjsLib.getDocument(props.pdfData).promise
        renderPage()
      } catch (error) {
        console.error('Error loading PDF:', error)
      }
    })

    const showPdfViewer = ref(false)
    const currentPdfData = ref('')
    const pdfTitle = ref('')

    const generatePdfData = async element => {
      const opt = {
        margin: 10,
        image: { type: 'jpeg', quality: 0.98 },
        html2canvas: { scale: 2 },
        jsPDF: { unit: 'mm', format: 'a4', orientation: 'portrait' },
      }

      return await html2pdf().from(element).set(opt).outputPdf('datauristring')
    }

    const showIjazahPdf = async () => {
      pdfTitle.value = 'Draft Ijazah'
      currentPdfData.value = await generatePdfData(ijazahPreview.value.$el)
      showPdfViewer.value = true
    }

    const showTranscriptPdf = async () => {
      pdfTitle.value = 'Transkrip Nilai'
      currentPdfData.value = await generatePdfData(transcriptPreview.value.$el)
      showPdfViewer.value = true
    }

    const generateCombinedPDF = async () => {
      try {
        const ijazahElement = document.querySelector('.draft-ijazah-pdf')
        const transkripElement = document.querySelector('.transkrip-pdf')
        
        if (!ijazahElement || !transkripElement) {
          alert('Error: Tidak dapat menemukan elemen preview')
          return
        }

        // Save original borders
        const ijazahOriginalBorder = ijazahElement.style.border
        const transkripOriginalBorder = transkripElement.style.border
        
        // Temporarily hide borders for PDF generation
        ijazahElement.style.border = 'none'
        transkripElement.style.border = 'none'

        console.log('Generating ijazah PDF...')
        
        // Generate ijazah PDF
        const ijazahOpt = {
          margin: [5, 5, 5, 5],
          image: { type: 'jpeg', quality: 0.98 },
          html2canvas: { 
            scale: 2,
            useCORS: true,
            logging: false,
          },
          jsPDF: { 
            unit: 'mm', 
            format: 'a4', 
            orientation: 'landscape',
          },
        }
        
        const ijazahPdfBytes = await html2pdf()
          .set(ijazahOpt)
          .from(ijazahElement)
          .outputPdf('arraybuffer')
        
        console.log('Generating transkrip PDF...')
        
        // Add custom CSS for vertical text rotation (same as TranskripNilai.vue)
        const transkripStyleElement = document.createElement('style')
        transkripStyleElement.id = 'transkrip-pdf-style-temp'
        transkripStyleElement.textContent = `
          .transkrip-table .vertical-header {
            display: inline-block !important;
            width: 100% !important;
            height: 100% !important;
            position: relative !important;
            transform: rotate(90deg) !important;
            transform-origin: center center !important;
            white-space: nowrap !important;
            margin-top: -35px !important;
          }
          .transkrip-table .vertical-text-eng {
            display: block !important;
            font-size: 5pt !important;
            font-style: italic !important;
            font-weight: bold !important;
            text-align: center !important;
            line-height: 1.2 !important;
            margin: 0 !important;
          }
          .transkrip-table .vertical-text-indo {
            display: block !important;
            font-size: 5pt !important;
            font-weight: bold !important;
            text-align: center !important;
            line-height: 1.2 !important;
            margin: 0 !important;
          }
        `
        transkripElement.appendChild(transkripStyleElement)
        
        // Generate transkrip PDF
        const transkripOpt = {
          margin: [5, 2.5, 0, 2.5],
          image: { type: 'jpeg', quality: 0.98 },
          html2canvas: { 
            scale: 2,
            useCORS: true,
            logging: false,
            letterRendering: true,
          },
          jsPDF: { 
            unit: 'mm', 
            format: [337.9, 278],
            orientation: 'landscape',
          },
        }
        
        const transkripPdfBytes = await html2pdf()
          .set(transkripOpt)
          .from(transkripElement)
          .outputPdf('arraybuffer')
        
        console.log('Merging PDFs using pdf-lib...')
        
        // Import pdf-lib
        const { PDFDocument } = await import('pdf-lib')
        
        // Load both PDFs
        const ijazahPdfDoc = await PDFDocument.load(ijazahPdfBytes)
        const transkripPdfDoc = await PDFDocument.load(transkripPdfBytes)
        
        // Create a new PDF document
        const mergedPdf = await PDFDocument.create()
        
        // Copy all pages from ijazah PDF
        const ijazahPages = await mergedPdf.copyPages(ijazahPdfDoc, ijazahPdfDoc.getPageIndices())
        ijazahPages.forEach((page) => {
          mergedPdf.addPage(page)
        })
        
        // Copy all pages from transkrip PDF
        const transkripPages = await mergedPdf.copyPages(transkripPdfDoc, transkripPdfDoc.getPageIndices())
        transkripPages.forEach((page) => {
          mergedPdf.addPage(page)
        })
        
        console.log('Saving merged PDF...')
        
        // Serialize the merged PDF to bytes
        const mergedPdfBytes = await mergedPdf.save()
        
        // Create blob from bytes
        const blob = new Blob([mergedPdfBytes], { type: 'application/pdf' })
        const blobUrl = URL.createObjectURL(blob)
        
        console.log('Opening merged PDF in new window...')
        
        // Open in new window
        const pdfWindow = window.open('', '_blank')
        if (!pdfWindow) {
          alert('Please allow popups for this website')
          return
        }

        pdfWindow.document.write(`
          <html dir="ltr" lang="en">
          <head>
            <meta charset="utf-8">
            <title>Ijazah dan Transkrip.pdf</title>
            <style>
              html, body {
                height: 100%;
                width: 100%;
                margin: 0;
                padding: 0;
              }
              embed {
                width: 100%;
                height: 100%;
                display: block;
              }
            </style>
          </head>
          <body>
            <embed 
              type="application/pdf" 
              src="${blobUrl}#toolbar=1&navpanes=1&scrollbar=1&view=FitH"
              width="100%"
              height="100%"
            >
          </body>
          </html>
        `)
        pdfWindow.document.close()

        pdfWindow.onbeforeunload = () => {
          URL.revokeObjectURL(blobUrl)
        }
        
        console.log('PDF merged and opened successfully!')
        
        // Restore original borders
        ijazahElement.style.border = ijazahOriginalBorder
        transkripElement.style.border = transkripOriginalBorder
        
        // Remove temporary style element
        const tempStyle = document.getElementById('transkrip-pdf-style-temp')
        if (tempStyle) tempStyle.remove()
      } catch (error) {
        console.error('Error generating combined PDF:', error)
        alert('Error generating PDF: ' + error.message)
        
        // Restore original borders even on error
        const ijazahElement = document.querySelector('.draft-ijazah-pdf')
        const transkripElement = document.querySelector('.transkrip-pdf')
        if (ijazahElement) ijazahElement.style.border = ''
        if (transkripElement) transkripElement.style.border = ''
        
        // Remove temporary style element even on error
        const tempStyle = document.getElementById('transkrip-pdf-style-temp')
        if (tempStyle) tempStyle.remove()
      }
    }

    return {
      form,
      fileStatus,
      updateFileStatus,
      comments,
      newComment,
      addComment,
      handleAddReply,
      handleSubmitReply,
      formatDate,
      showValidationModal,
      validationMessage,
      showConfirmModal,
      handleSubmit,
      openInNewTab,
      user,
      ijazahPreview,
      transcriptPreview,
      showNotifAlert,
      notifMessage,
      matakuliah,
      onLapor,
      onSimpan,
      confirmSimpan,
      showComments,
      loadCommentsFromDatabase,
      generatePDF,
      generateCombinedPDF,
      container,
      canvas, 
      scale,
      zoomIn,
      zoomOut,
      rotate,
      download,
      print,
      showPdfViewer,
      currentPdfData,
      pdfTitle,
      showIjazahPdf,
      showTranscriptPdf,
    }
  },
}
</script>

<style scoped>
.form-card {
  margin: 0;
  max-inline-size: 100%;
}

.upload-field {
  position: relative;
}

.custom-upload-box {
  position: relative;
  display: flex;
  align-items: stretch;
  border: 2px dashed #2196f3 !important;
  border-radius: 12px !important;
  background: #fff;
  box-shadow: 0 2px 8px 0 rgba(33, 150, 243, 5%);
  margin-block-end: 16px;
  min-block-size: 120px;
}

.upload-center-content {
  position: absolute;
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  block-size: 100%;
  inline-size: 100%;
  inset: 0;
  pointer-events: none;
}

.upload-label {
  color: #2196f3;
  font-size: 1rem;
  font-weight: 500;
  margin-block-start: 8px;
}

.upload-field .v-input__slot {
  min-block-size: unset !important;
}

.upload-field .v-label {
  display: none;
}

.upload-field .v-input__control {
  block-size: 100%;
}

.upload-field .v-input__prepend-inner {
  display: none;
}

.comment-form-card {
  border: 1px solid rgba(0, 0, 0, 12%);
  border-radius: 8px;
  background-color: var(--v-theme-surface);
}

.comment-input-row {
  display: flex;
  flex-wrap: wrap;
  align-items: center;
  gap: 12px;
  margin-block-end: 16px;
}

.comment-input-field {
  flex: 1 1 240px;
}

.comment-submit-btn {
  min-inline-size: 140px;
}

.confirm-card {
  border-radius: 12px;

  /* width: fit-content; */
}

.confirm-title {
  color: #1f9d8a;
  font-weight: 700;
  padding-block-start: 20px;
  text-align: center;
}

.confirm-subtitle {
  color: rgba(var(--v-theme-on-surface), 0.7);
  padding-block-start: 0;
  text-align: center;
}

.confirm-actions {
  justify-content: flex-end;
  padding-block: 16px 24px;
  padding-inline: 24px;
}

.confirm-cancel {
  background: #e5e7eb;
  color: #6b7280;
}

.confirm-submit {
  background: #1f9d8a;
  color: #fff !important;
}

.comment-card {
  border: 1px solid rgba(0, 0, 0, 12%);
  border-radius: 8px;
  background-color: var(--v-theme-surface);
}

.nested-reply-container {
  position: relative;
  margin-block-start: 16px; /* Add space above nested replies block */
  margin-inline-start: 24px; /* Adjust indentation for nested replies */
  padding-inline-start: 24px; /* Add padding for the vertical line */
}

.nested-reply-container::before {
  position: absolute;
  background-color: var(--v-primary-base);
  block-size: 100%;
  content: "";
  inline-size: 2px;
  inset-block-start: 0;
  inset-inline-start: 0;
  opacity: 0.2;
}

.reply-card {
  border: 1px solid rgba(0, 0, 0, 12%);
  border-radius: 8px;
  background-color: var(--v-theme-surface);
  margin-block-end: 16px;
  padding-block: 12px;
  padding-inline: 16px;
}

.comment-text,
.reply-text {
  line-height: 1.5;
  margin-block-end: 0;
  white-space: pre-wrap;
  word-break: break-word;
}

.print-button-container {
  display: flex;
  align-items: center;
  justify-content: center;
}

.reply-text {
  margin-block-end: 0; /* Remove bottom margin for the last reply text */
}

.comment-header > .avatar-mr {
  margin-inline-end: 12px;
}

/* Custom spacing utilities for comments */
.reply-btn-mr {
  margin-inline-end: 8px;
}

.reply-icon-mr {
  margin-inline-end: 4px;
}

/* Generic Spacing utilities - keeping original if used elsewhere */
.mt-8 {
  margin-block-start: 32px;
}

.mb-3 {
  margin-block-end: 12px;
}

.mb-4 {
  margin-block-end: 16px;
}

.mb-6 {
  margin-block-end: 24px;
}

.mr-3 {
  margin-inline-end: 12px;
}

.ml-4 {
  margin-inline-start: 16px;
}

.pb-0 {
  padding-block-end: 0;
}

/* File upload style (theme aware) */
.file-upload {
  display: flex;
  align-items: center;
  padding: 12px;
  border-radius: 8px;
  background: rgb(var(--v-theme-surface));
  transition: background 0.2s;
}

.file-upload-icon {
  color: rgb(var(--v-theme-primary));
  font-size: 22px;
  margin-inline-end: 10px;
  transition: color 0.2s;
}

.file-upload-input {
  flex: 1;
  background: transparent !important;
  color: rgb(var(--v-theme-on-surface)) !important;

  --v-field-border-color: rgb(var(--v-theme-primary));
  --v-field-label-color: rgb(var(--v-theme-on-surface));
  --v-field-placeholder-color: rgb(var(--v-theme-on-surface));
  --v-field-bg-color: rgb(var(--v-theme-surface));
}

.v-file-input__text {
  display: flex;
  align-items: center;
  border-radius: 0 0 10px 10px;
  background: rgb(var(--v-theme-surface)) !important;
  color: rgb(var(--v-theme-on-surface));
  font-size: 1rem;
  padding-block: 8px;
  padding-inline: 12px;
}

.v-file-input__text .v-icon {
  color: rgb(var(--v-theme-primary)) !important;
  margin-inline-end: 8px;
}

.v-file-input__text .v-btn {
  color: rgb(var(--v-theme-on-surface)) !important;
  margin-inline-start: 8px;
}

.draft-ijazah-preview {
  border: 1px solid #e0e0e0;
  border-radius: 12px;
  margin: auto;
  background: #fff;
  box-shadow: 0 2px 8px 0 rgba(33, 150, 243, 5%);

  /* A4 Landscape container */
  inline-size: 100%;
  max-inline-size: 1200px;
  min-block-size: auto;
  overflow-x: auto;
}

.transcript-preview {
  margin: auto;
  inline-size: 100%;
  max-inline-size: 1200px;
  overflow-x: auto;
}

.position-relative {
  position: relative;
}

.fixed-header th,
.fixed-header td {
  block-size: 75px;
}

.fixed-header thead th {
  position: sticky;
  z-index: 1;
  background-color: white;
  inset-block-start: 0;
}

.no-col {
  inline-size: 45px;
  text-align: center;
  writing-mode: vertical-rl;
}

.kode-col {
  inline-size: 110px;
}

.matkul-col {
  inline-size: 55%;
}

.vertical-header {
  inline-size: 40px;
  text-align: center !important;
  transform: rotate(180deg);
  vertical-align: middle;
  writing-mode: vertical-rl;
}

.pdf-viewer {
  display: flex;
  flex-direction: column;
  align-items: center;
  block-size: 100%;
  inline-size: 100%;
}

.pdf-toolbar {
  display: flex;
  padding: 10px;
  border-radius: 4px;
  background: #f5f5f5;
  gap: 10px;
  margin-block-end: 10px;
}

.pdf-toolbar button {
  border: none;
  border-radius: 4px;
  background: #fff;
  cursor: pointer;
  padding-block: 5px;
  padding-inline: 10px;
}

.pdf-toolbar button:hover {
  background: #e0e0e0;
}

.pdf-container {
  overflow: auto;
  border: 1px solid #e0e0e0;
  border-radius: 4px;
  max-inline-size: 100%;
}

canvas {
  display: block;
  margin-block: 0;
  margin-inline: auto;
}

/* ... style yang sudah ada ... */

.pdf-viewer-container {
  block-size: calc(100vh - 64px);
  inline-size: 100%;
}

.dialog-bottom-transition-enter-active,
.dialog-bottom-transition-leave-active {
  transition: transform 0.3s ease-in-out;
}

.dialog-bottom-transition-enter,
.dialog-bottom-transition-leave-to {
  transform: translateY(100%);
}

/* Layout helpers */
.page-container {
  margin-inline: auto;
  max-inline-size: 1280px;
}

.preview-grid {
  display: flex;
  flex-direction: column;
  gap: 24px;
}

.preview-shell {
  overflow: hidden;
  padding: 16px;
  border: 1px solid rgba(0, 0, 0, 8%);
  border-radius: 12px;
  background: rgb(var(--v-theme-surface));
}

.action-bar {
  display: flex;
  flex-wrap: wrap;
  justify-content: space-between;
  gap: 12px;
  margin-block: 20px 24px;
}

.action-bar-left,
.action-bar-right {
  display: flex;
  flex-wrap: wrap;
  gap: 12px;
}

@media (max-width: 960px) {
  .page-container {
    padding-inline: 16px !important;
  }
}

@media (max-width: 640px) {
  .preview-shell {
    padding: 12px;
  }

  .action-bar {
    justify-content: stretch;
  }

  .action-bar .v-btn {
    flex: 1 1 100%;
  }
}
</style>

<style>
/* Tambahkan di bagian style yang ada */
@media print {
  .draft-ijazah-preview,
  .transcript-preview {
    padding: 20mm;
    margin: 0;
    background: white;
    page-break-after: always;
  }

  .draft-ijazah-preview:last-child,
  .transcript-preview:last-child {
    page-break-after: auto;
  }

  /* Sembunyikan elemen yang tidak perlu di PDF */
  .v-btn,
  .comment-form-card,
  .v-alert {
    display: none !important;
  }
}

/* Style untuk preview sebelum di-print */
.draft-ijazah-preview,
.transcript-preview {
  padding: 20mm;
  background: white;
  box-shadow: 0 0 10px rgba(0, 0, 0, 10%);
  inline-size: 210mm;
  margin-block: 20mm;
  margin-inline: auto;
}
</style>
