<template>
  <VApp>
    <VMain>
      <VContainer
        fluid
        class="pa-6"
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

        <DraftIjazah
          ref="ijazahPreview"
          :user="user"
        />
        <TranskripNilai
          ref="transcriptPreview"
          :user="user"
          :matakuliah="matakuliah"
        />

        <div class="d-flex justify-end mb-6">
          <VBtn
            color="error"
            class="mr-3"
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
            <VTextField
              v-model="newComment"
              label="Tulis komentar..."
              outlined
              dense
              hide-details
              class="mb-3"
              @keyup.enter="addComment"
            />
            <VBtn
              color="primary"
              size="small"
              class="mb-4"
              @click="addComment"
            >
              Kirim Komentar
            </VBtn>
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
                <VIcon>mdi-close</VIcon>
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
import { onMounted, ref } from 'vue'
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

    const newComment = ref('')

    const showValidationModal = ref(false)
    const validationMessage = ref('')

    const ijazahPreview = ref(null)
    const transcriptPreview = ref(null)

    // Dummy user login data (ganti dengan data user login sebenarnya jika sudah ada mekanisme login)
    const user = ref({
      name: 'IRWANDA BUDI PANGESTU',
      nim: '1911501007',
      birthPlace: 'ASTRA KSETRA',
      birthDate: '17 DESEMBER 2000',
      studyProgram: 'TEKNOLOGI INFORMASI',
      degree: 'SARJANA',
      graduationDate: '22 MARET 2025',
    })

    // Daftar matakuliah (array of object)
    const matakuliah = ref([
      { no: 1, kode: 'TIK0050', nama: 'PENGUJIAN PERANGKAT LUNAK', nama_en: 'Software Testing', sks: 3, nilai: 'A' },
      { no: 2, kode: 'TIK0051', nama: 'REKAYASA WEB', nama_en: 'Web Engineering', sks: 3, nilai: 'C' },
      { no: 3, kode: 'TIK0054', nama: 'INTEGRASI SISTEM', nama_en: 'System Integration', sks: 3, nilai: 'A' },
      { no: 4, kode: 'TIK1001', nama: 'KALKULUS', nama_en: 'Calculus', sks: 2, nilai: 'BC' },
      { no: 5, kode: 'TIK1002', nama: 'LOGIKA INFORMATIKA', nama_en: 'Informatics Logic', sks: 2, nilai: 'BC' },
      { no: 6, kode: 'TIK1003', nama: 'PENGANTAR TEKNOLOGI INFORMASI', nama_en: 'Introduction to Information Technology', sks: 3, nilai: 'A' },
      { no: 7, kode: 'TIK1004', nama: 'DASAR PEMROGRAMAN', nama_en: 'Basic Programming', sks: 3, nilai: 'B' },
      { no: 8, kode: 'TIK1005', nama: 'BASIS DATA', nama_en: 'Database', sks: 3, nilai: 'B' },
      { no: 9, kode: 'TIK2006', nama: 'MATEMATIKA DISKRIT', nama_en: 'Discrete Mathematics', sks: 2, nilai: 'B' },
      { no: 10, kode: 'TIK2007', nama: 'ALGORITMA PEMROGRAMAN', nama_en: 'Programming Algorithm', sks: 3, nilai: 'A' },
      { no: 11, kode: 'TIK2008', nama: 'STATISTIK DAN PROBABILITAS', nama_en: 'Statistics and Probability', sks: 2, nilai: 'B' },
      { no: 12, kode: 'TIK2009', nama: 'ORGANISASI DAN ARSITEKTUR KOMPUTER', nama_en: 'Computer Organization and Architecture', sks: 3, nilai: 'A' },
      { no: 13, kode: 'TIK2010', nama: 'JARINGAN KOMPUTER', nama_en: 'Computer Network', sks: 3, nilai: 'B' },
      { no: 14, kode: 'TIK2011', nama: 'PEMROGRAMAN BERORIENTASI OBJEK', nama_en: 'Object Oriented Programming', sks: 3, nilai: 'A' },
      { no: 15, kode: 'TIK3012', nama: 'PEMROGRAMAN WEB', nama_en: 'Web Programming', sks: 3, nilai: 'A' },
      { no: 16, kode: 'TIK3013', nama: 'STRUKTUR DATA', nama_en: 'Data Structure', sks: 3, nilai: 'A' },
      { no: 17, kode: 'TIK3014', nama: 'SISTEM OPERASI', nama_en: 'Operating System', sks: 3, nilai: 'A' },
      { no: 18, kode: 'TIK3015', nama: 'ANALISIS DAN PERANCANGAN SISTEM INFORMASI', nama_en: 'Information System Analysis and Design', sks: 3, nilai: 'B' },
      { no: 19, kode: 'TIK3016', nama: 'GRAFIKA KOMPUTER', nama_en: 'Computer Graphics', sks: 2, nilai: 'B' },
      { no: 20, kode: 'TIK3017', nama: 'SISTEM PAKAR', nama_en: 'Expert System', sks: 2, nilai: 'B' },
      { no: 21, kode: 'TIK3018', nama: 'PEMROGRAMAN PERANGKAT BERGERAK', nama_en: 'Mobile Device Programming', sks: 3, nilai: 'A' },
      { no: 22, kode: 'TIK3019', nama: 'KEAMANAN INFORMASI', nama_en: 'Information Security', sks: 3, nilai: 'B' },
      { no: 23, kode: 'TIK4020', nama: 'REKAYASA PERANGKAT LUNAK', nama_en: 'Software Engineering', sks: 3, nilai: 'B' },
      { no: 24, kode: 'TIK4021', nama: 'PEMROGRAMAN VISUAL', nama_en: 'Visual Programming', sks: 3, nilai: 'A' },
      { no: 25, kode: 'TIK4022', nama: 'DATA MINING', nama_en: 'Data Mining', sks: 2, nilai: 'B' },
      { no: 26, kode: 'TIK4023', nama: 'PEMROGRAMAN JARINGAN', nama_en: 'Network Programming', sks: 3, nilai: 'A' },
      { no: 27, kode: 'TIK4024', nama: 'KECERDASAN BUATAN', nama_en: 'Artificial Intelligence', sks: 3, nilai: 'A' },
      { no: 28, kode: 'TIK4025', nama: 'ARSITEKTUR ENTERPRISE', nama_en: 'Enterprise Architecture', sks: 2, nilai: 'C' },
      { no: 29, kode: 'TIK5026', nama: 'TEORI BAHASA DAN OTOMATA', nama_en: 'Theory of Formal Language and Automata', sks: 3, nilai: 'B' },
      { no: 30, kode: 'TIK5027', nama: 'MANAJEMEN PROYEK TEKNOLOGI INFORMASI', nama_en: 'Information Technology Project Management', sks: 3, nilai: 'B' },
      { no: 31, kode: 'TIK5028', nama: 'SISTEM INFORMASI GEOGRAFIS', nama_en: 'Geographic Information System', sks: 3, nilai: 'BC' },
      { no: 32, kode: 'TIK5029', nama: 'DESAIN DAN ANALISIS ALGORITMA', nama_en: 'Algorithm Design and Analysis', sks: 3, nilai: 'B' },
      { no: 33, kode: 'TIK5030', nama: 'SISTEM INFORMASI RUMAH SAKIT', nama_en: 'Hospital Information System', sks: 2, nilai: 'BC' },
      { no: 34, kode: 'TIK5031', nama: 'SISTEM PENDUKUNG KEPUTUSAN', nama_en: 'Decision Support System', sks: 2, nilai: 'C' },
      { no: 35, kode: 'TIK5032', nama: 'PEMROGRAMAN SISTEM DAN JARINGAN', nama_en: 'System and Network Programming', sks: 3, nilai: 'BC' },
      { no: 36, kode: 'TIK6033', nama: 'INTERNSHIP', nama_en: 'Internship', sks: 3, nilai: 'C' },
      { no: 37, kode: 'TIK6034', nama: 'TRENDS IN INFORMATION TECHNOLOGY', nama_en: 'Trends in Information Technology', sks: 2, nilai: 'C' },
      { no: 38, kode: 'TIK6035', nama: 'EXPERT SYSTEM', nama_en: 'Expert System', sks: 3, nilai: 'C' },
      { no: 39, kode: 'TIK6036', nama: 'TEKNOLOGI INFORMASI KESEHATAN', nama_en: 'Health Information Technology', sks: 3, nilai: 'A' },
      { no: 40, kode: 'TIK6037', nama: 'INFORMATION SYSTEM PROJECT MANAGEMENT', nama_en: 'Information System Project Management', sks: 3, nilai: 'B' },
      { no: 41, kode: 'TIK6038', nama: 'E-BUSINESS', nama_en: 'E-Business', sks: 2, nilai: 'C' },
      { no: 42, kode: 'TIK6039', nama: 'PERANCANGAN DAN IMPLEMENTASI DATABASE', nama_en: 'Database Design and Implementation', sks: 3, nilai: 'B' },
      { no: 43, kode: 'TIK6040', nama: 'FRAMEWORK PROGRAMMING', nama_en: 'Framework Programming', sks: 3, nilai: 'A' },
      { no: 44, kode: 'TIK6041', nama: 'CLIENT-SERVER PROGRAMMING', nama_en: 'Client-Server Programming', sks: 3, nilai: 'B' },
      { no: 45, kode: 'TIK6042', nama: 'AUDIT SISTEM INFORMASI', nama_en: 'Information System Audit', sks: 2, nilai: 'B' },
      { no: 46, kode: 'TIK6043', nama: 'CLOUD COMPUTING', nama_en: 'Cloud Computing', sks: 2, nilai: 'C' },
      { no: 47, kode: 'TIK6044', nama: 'SISTEM INFORMASI MANAJEMEN', nama_en: 'Management Information System', sks: 3, nilai: 'B' },
      { no: 48, kode: 'TIK6045', nama: 'DATA WAREHOUSE DAN BUSINESS INTELLIGENCE', nama_en: 'Data Warehouse and Business Intelligence', sks: 3, nilai: 'B' },
      { no: 49, kode: 'TIK6046', nama: 'BIG DATA ANALYTICS', nama_en: 'Big Data Analytics', sks: 3, nilai: 'A' },
      { no: 50, kode: 'TIK6047', nama: 'PEMROGRAMAN GAME', nama_en: 'Game Programming', sks: 2, nilai: 'B' },
      { no: 51, kode: 'TIK6048', nama: 'ARTIFICIAL INTELLIGENCE', nama_en: 'Artificial Intelligence', sks: 3, nilai: 'A' },
      { no: 52, kode: 'TIK6049', nama: 'COMPUTER VISION', nama_en: 'Computer Vision', sks: 3, nilai: 'A' },
      { no: 53, kode: 'TIK6050', nama: 'MACHINE LEARNING', nama_en: 'Machine Learning', sks: 3, nilai: 'A' },
      { no: 54, kode: 'TIK6051', nama: 'NATURAL LANGUAGE PROCESSING', nama_en: 'Natural Language Processing', sks: 3, nilai: 'A' },
      { no: 55, kode: 'UNI0009', nama: 'ENGLISH FOR ACADEMIC READING', nama_en: 'English for Academic Reading', sks: 1, nilai: 'AB' },
      { no: 56, kode: 'UNI0010', nama: 'ENGLISH FOR ACADEMIC WRITING', nama_en: 'English for Academic Writing', sks: 1, nilai: 'A' },
      { no: 57, kode: 'UNI0011', nama: 'KEWIRAUSAHAAN', nama_en: 'Entrepreneurship', sks: 3, nilai: 'A' },
      { no: 58, kode: 'UNI0012', nama: 'KULIAH KERJA NYATA (KKN)', nama_en: 'Community Service', sks: 3, nilai: 'A' },
      { no: 59, kode: 'UNI0013', nama: 'SKRIPSI', nama_en: 'Thesis', sks: 6, nilai: 'C' },
    ])

    const route = useRoute()
    const notifId = route.query.notif
    const showNotifAlert = ref(false)
    const notifMessage = ref('')
    if (notifId) {
      showNotifAlert.value = true
      notifMessage.value = 'Ada komentar baru dari admin di Validasi Ijazah Anda. Silakan cek detail di bawah ini.'
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

    // Fungsi untuk komentar
    const addComment = async () => {
      if (!newComment.value.trim()) return

      const commentData = {
        id: Date.now(),
        user: user.value?.name || 'Mahasiswa',
        text: newComment.value,
        date: new Date(),
        replies: [],
      }

      try {
        // Simpan ke database
        await saveCommentToDatabase(commentData)
        
        // Tambahkan ke state lokal
        comments.value.unshift(commentData)
        newComment.value = ''
      } catch (error) {
        console.error('Error saving comment:', error)

        // Tetap tambahkan ke state lokal jika gagal simpan
        comments.value.unshift(commentData)
        newComment.value = ''
      }
    }

    // Fungsi untuk menyimpan komentar ke database
    const saveCommentToDatabase = async commentData => {
      try {
        console.log('Saving comment to database:', commentData)

        const response = await fetch('/api/comments', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]')?.getAttribute('content'),
          },
          body: JSON.stringify({
            user_id: 1, // ID user, sesuaikan dengan data sebenarnya
            content: commentData.text,
            parent_id: null, // Komentar utama
            validasi_ijazah_id: 1, // ID validasi ijazah, sesuaikan dengan data sebenarnya
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

        const response = await fetch('/api/comments/1') // ID validasi ijazah

        console.log('Response status:', response.status)
        
        if (response.ok) {
          const data = await response.json()

          console.log('Comments loaded:', data)
          comments.value = data
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
      validationMessage.value = 'Perubahan berhasil disimpan'
      showValidationModal.value = true
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
      if (notifId) {
        showComments.value = true
        await loadCommentsFromDatabase()
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

    return {
      form,
      fileStatus,
      updateFileStatus,
      comments,
      newComment,
      addComment,
      handleAddReply,
      formatDate,
      showValidationModal,
      validationMessage,
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
      showComments,
      loadCommentsFromDatabase,
      generatePDF,
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
  margin-block-end: 12px;
  white-space: pre-wrap;
  word-break: break-word;
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
  inline-size: fit-content;
  max-inline-size: 1200px;
  min-block-size: auto;
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
