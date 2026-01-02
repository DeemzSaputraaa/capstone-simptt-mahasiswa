<template>
  <div class="position-relative mb-6">
    <div class="transcript-wrapper">
      <div class="transcript-preview pa-6 position-relative">
        <div class="transcript-toolbar">
          <div class="transcript-title">
            Transkrip Nilai
          </div>
          <VBtn
            color="primary"
            icon
            class="view-pdf-btn"
            @click="openPdfViewer"
          >
            <VIcon>ri-external-link-line</VIcon>
          </VBtn>
        </div>
        <div class="transkrip-pdf">
        
        
          <div class="transkrip-header">
            <div></div>
            <div class="transkrip-info-center">
              <div class="info-row">
                <span class="info-label-center">NIM/NAMA LENGKAP</span>
                <span class="info-separator">:</span>
                <span class="info-value-center">{{ userDisplay.nim }} / {{ userDisplay.name }}</span>
              </div>
              <div class="info-row">
                <span
                  class="info-label-center"
                  style=" font-size: 6pt;font-style: italic;"
                >student number / name :</span>
              </div>
              <div class="info-row">
                <span class="info-label-center">TEMPAT/TANGGAL LAHIR</span>
                <span class="info-separator">:</span>
                <span class="info-value-center">{{ userDisplay.birthPlace }}, {{ userDisplay.birthDate }}</span>
              </div>
              <div class="info-row">
                <span
                  class="info-label-center"
                  style=" font-size: 6pt;font-style: italic;"
                >Place of Birth/Date</span>
              </div>
              <div class="info-row">
                <span class="info-label-center">PROGRAM STUDI / JENJANG</span>
                <span class="info-separator">:</span>
                <span class="info-value-center">{{ userDisplay.studyProgram }} / {{ userDisplay.degree }}</span>
              </div>
              <div class="info-row">
                <span
                  class="info-label-center"
                  style=" font-size: 6pt;font-style: italic;"
                >Study Program / Level</span>
              </div>
              <div class="info-row">
                <span class="info-label-center">TANGGAL KELULUSAN</span>
                <span class="info-separator">:</span>
                <span class="info-value-center">{{ userDisplay.graduationDate }}</span>
              </div>
              <div class="info-row">
                <span
                  class="info-label-center"
                  style=" font-size: 6pt;font-style: italic;"
                >Graduation Date</span>
              </div>
              <div class="info-row">
                <span class="info-label-center">TOTAL SKS</span>
                <span class="info-separator">:</span>
                <span class="info-value-center">146</span>
              </div>
              <div class="info-row">
                <span
                  class="info-label-center"
                  style=" font-size: 6pt;font-style: italic;"
                >Total Credits</span>
              </div>
              <div class="info-row">
                <span class="info-label-center">INDEKS PRESTASI</span>
                <span class="info-separator">:</span>
                <span class="info-value-center">2.94</span>
              </div>
              <div class="info-row">
                <span
                  class="info-label-center"
                  style=" font-size: 6pt;font-style: italic;"
                >Grade Point Average</span>
              </div>
              <div class="info-row">
                <span class="info-label-center">PREDIKAT KELULUSAN</span>
                <span class="info-separator">:</span>
                <span class="info-value-center">MEMUASKAN / <span class="italic-text">SATISFACTORY</span></span>
              </div>
              <div class="info-row">
                <span
                  class="info-label-center"
                  style=" font-size: 6pt;font-style: italic;"
                >Graduation Predicate</span>
              </div>
              <div class="info-row">
                <span class="info-label-center">STATUS</span>
                <span class="info-separator">:</span>
                <span class="status-text-inline">Akreditasi LAM INFOKOM No. 027/SK/LAM-INFOKOM/Ak/S/III/2024 tanggal 19 Maret 2024 dengan peringkat "Baik Sekali".</span>
              </div>
              <div class="info-row">
                <span
                  class="italic-text"
                  style="font-size: 6pt;"
                >Accredited "Very Good" by LAM INFOKOM No. 027/SK/LAM-INFOKOM/Ak/S/III/2024 on March 19, 2024.</span>
              </div>
            </div>
          </div>

          <div class="transkrip-tables">
            <table
              v-for="(col, idx) in columns"
              :key="idx"
              class="transkrip-table no-inner-border"
            >
              <thead>
                <tr>
                  <th
                    rowspan="3"
                    class="th-no"
                  >
                    NO
                    <span class="th-sub">NO</span>
                  </th>
                  <th
                    rowspan="3"
                    class="th-kode"
                  >
                    KODE
                    <span class="th-sub">CODE</span>
                  </th>
                  <th class="th-matkul">
                    MATA KULIAH
                    <div class="th-sub th-sub-block">
                      SUBJECTS
                    </div>
                  </th>
                  <th
                    rowspan="3"
                    class="th-vertical"
                  >
                    <div class="vertical-header">
                      <div class="vertical-text-eng">
                        CREDITS
                      </div>
                      <div class="vertical-text-indo">
                        SKS
                      </div>
                    </div>
                  </th>
                  <th
                    rowspan="3"
                    class="th-vertical"
                  >
                    <div class="vertical-header">
                      <div class="vertical-text-eng">
                        GRADE
                      </div>
                      <div class="vertical-text-indo">
                        NILAI
                      </div>
                    </div>
                  </th>
                  <th
                    rowspan="3"
                    class="th-vertical"
                  >
                    <div class="vertical-header">
                      <div class="vertical-text-eng">
                        EQUIVALENT
                      </div>
                      <div class="vertical-text-indo">
                        EKUIVALEN
                      </div>
                    </div>
                  </th>
                </tr>
              </thead>
              <tbody>
                <tr
                  v-for="(mk, i) in col"
                  :key="mk.no + mk.kode + i"
                  :class="[
                    { 'empty-row': mk.isEmpty },
                    { 'last-empty-row': mk.isLastEmpty },
                    { 'tight-row': mk.no && mk.no >= 55 && mk.no <= 59 }
                  ]"
                >
                  <template v-if="!mk.isEmpty">
                    <td class="td-no">
                      {{ mk.no }}
                    </td>
                    <td class="td-kode">
                      {{ mk.kode }}
                    </td>
                    <td class="td-matkul">
                      {{ mk.nama }}<br>
                      <span class="course-subtitle">{{ mk.nama_en }}</span>
                    </td>
                    <td class="td-center">
                      {{ mk.sks }}
                    </td>
                    <td class="td-center">
                      {{ mk.nilai }}
                    </td>
                    <td class="td-center">
                      {{ formatKredit(mk.kredit) }}
                    </td>
                  </template>
                  <template v-else-if="mk.isLastEmpty">
                    <td colspan="6" />
                  </template>
                </tr>
                <!-- Judul tugas akhir & ttd dekan hanya di kolom ketiga -->
                <tr v-if="idx === 2">
                  <td
                    colspan="6"
                    class="ta-ttd-cell"
                  >
                    <div class="ta-ttd-content">
                      <div>
                        <div class="judul-ta-label">
                          <div><b>JUDUL TUGAS AKHIR</b></div>
                          <div class="italic-text">
                            THESIS TITLE
                          </div>
                        </div>
                        <div class="judul-ta">
                          SISTEM INFORMASI PENDAFTARAN PADA KLINIK FISIOTERAPI<br>
                          UNIVERSITAS AISYIAH YOGYAKARTA MENGGUNAKAN METODE RAD<br>
                          (RAPID APPLICATION DEVELOPMENT)
                        </div>
                        <div class="judul-ta italic-text">
                          REGISTRATION INFORMATION SYSTEM AT AISYIYAH UNIVERSITY PHYSIOTHERAPY CLINIC<br>
                          YOGYAKARTA USING RAD METHOD (RAPID APPLICATION DEVELOPMENT)
                        </div>
                      </div>

                      <div class="ta-signature">
                        <div class="location-date">
                          YOGYAKARTA, 22 MARET 2025<br>
                          <span class="italic-text">YOGYAKARTA, MARCH 22, 2025</span>
                        </div>
                        <div class="dekan">
                          DEKAN FAKULTAS SAINS DAN TEKNOLOGI,<br>
                          <span class="italic-text">DEAN OF FACULTY OF SCIENCE AND TECHNOLOGY,</span>
                        </div>
                        <div class="nama">
                          <u>Ar. TIKA AINUNNISA FITRIA, S.T., M.T., Ph.D</u>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import html2pdf from 'html2pdf.js'
import { computed } from 'vue'

const props = defineProps({
  user: { type: Object, required: true },
  matakuliah: { type: Array, required: true },
})

// Ambil data user dari localStorage (hasil login) bila props belum lengkap
const authUser = computed(() => {
  if (typeof window === 'undefined') return {}

  try {
    return JSON.parse(localStorage.getItem('user') || '{}')
  } catch (error) {
    console.warn('Failed to parse stored user', error)

    return {}
  }
})

const baseUser = computed(() => {
  const hasProps = props.user && Object.keys(props.user).length > 0

  return hasProps ? props.user : authUser.value
})

const userDisplay = computed(() => ({
  nim: baseUser.value?.nim ?? '',
  name: baseUser.value?.namalengkap ?? baseUser.value?.name ?? '',
  birthPlace: baseUser.value?.tempatlahir ?? baseUser.value?.birthPlace ?? '',
  birthDate: baseUser.value?.tanggallahir ?? baseUser.value?.birthDate ?? '',
  studyProgram: baseUser.value?.prodi ?? baseUser.value?.studyProgram ?? '',
  degree: baseUser.value?.degree ?? baseUser.value?.jenjang ?? '',
  graduationDate: baseUser.value?.tanggallulus ?? baseUser.value?.graduationDate ?? '',
}))

// Helper function untuk konversi nilai ke bobot
const getNilaiBobot = nilai => {
  const bobotMap = {
    'A': 4.0,
    'AB': 3.5,
    'B': 3.0,
    'BC': 2.5,
    'C': 2.0,
    'D': 1.0,
    'E': 0.0,
  }
  
  return bobotMap[nilai] || 0.0
}

// Helper function untuk format kredit dengan desimal yang tepat
const formatKredit = kredit => {
  const num = parseFloat(kredit)

  // Jika bilangan bulat (desimal .0), tampilkan 1 angka desimal
  // Jika ada pecahan, tampilkan 2 angka desimal
  return num % 1 === 0 ? num.toFixed(1) : num.toFixed(2)
}

const columns = computed(() => {
  // Add bobot and kredit to each matakuliah
  const enrichedData = props.matakuliah.map(mk => ({
    ...mk,
    bobot: getNilaiBobot(mk.nilai),
    kredit: formatKredit(mk.sks * getNilaiBobot(mk.nilai)),
  }))

  // Set fixed size untuk setiap kolom
  const itemsPerColumn = 27 // Jumlah item tetap per kolom

  // Slice data untuk setiap kolom
  const col1 = enrichedData.slice(0, itemsPerColumn).map((mk, i) => ({
    ...mk,
    no: i + 1,
  }))

  const col2 = enrichedData.slice(itemsPerColumn, itemsPerColumn * 2).map((mk, i) => ({
    ...mk,
    no: i + itemsPerColumn + 1,
  }))

  // Prepare col3 with actual data and fixed length
  const col3Start = itemsPerColumn * 2
  const col3Data = enrichedData.slice(col3Start)

  const col3 = col3Data.map((mk, i) => ({
    ...mk,
    no: i + col3Start + 1,
  }))

  return [col1, col2, col3]
})

const openPdfViewer = async () => {
  const element = document.querySelector('.transkrip-pdf').cloneNode(true)
  
  // Hapus tombol dari clone
  const button = element.querySelector('.view-pdf-btn')
  if (button) {
    button.remove()
  }
  
  // Hapus border untuk PDF
  element.style.border = 'none'

  // Pastikan semua style di-inject
  const style = document.createElement('style')

  style.innerHTML = `
    .transkrip-table .th-vertical {
      height: 54px !important;
      width: 30px !important;
      min-width: 30px !important;
      padding: 3px 1px !important;
      vertical-align: middle !important;
      text-align: center !important;
      overflow: hidden !important;
    }
    .transkrip-table .vertical-header {
      display: inline-block !important;
      width: 100% !important;
      height: 100% !important;
      position: relative !important;
      transform: rotate(90deg) !important;
      transform-origin: center center !important;
      white-space: nowrap !important;
      /* UBAH NILAI margin-top UNTUK MENGGESER POSISI TEKS KE ATAS/BAWAH */
      /* Nilai negatif = geser ke atas, Nilai positif = geser ke bawah */
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
  element.appendChild(style)

  const opt = {
    margin: [5, 2.5, 0, 2.5],
    filename: 'transkrip-nilai.pdf',
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: { 
      scale: 2,
      useCORS: true,
      logging: false,
      letterRendering: true,
    },
    jsPDF: { 
      unit: 'mm', 
      format: [278, 337.9],
      orientation: 'landscape',
    },
    pagebreak: { mode: ['avoid-all', 'css', 'legacy'] },
  }

  try {
    // Generate PDF as blob
    const pdf = await html2pdf().from(element).set(opt).outputPdf('blob')
    
    // Create blob URL
    const blobUrl = URL.createObjectURL(pdf)
    
    // Open PDF in new window
    const pdfWindow = window.open('', '_blank')
    if (!pdfWindow) {
      alert('Please allow popups for this website')
      
      return
    }

    pdfWindow.document.write(`
      <html dir="ltr" lang="en">
      <head>
        <meta charset="utf-8">
        <title>transkrip-nilai.pdf</title>
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

    // Clean up blob URL when window closes
    pdfWindow.onbeforeunload = () => {
      URL.revokeObjectURL(blobUrl)
    }
  } catch (error) {
    console.error('Error generating PDF:', error)
    alert('Error generating PDF. Please try again.')
  }
}
</script>

<style scoped>
/* Responsive styles */
@media screen and (max-width: 1200px) {
  .transkrip-pdf {
    transform: scale(0.9);
    transform-origin: top center;
  }
}

@media screen and (max-width: 992px) {
  .transkrip-pdf {
    transform: scale(0.8);
    transform-origin: top center;
  }

  .transkrip-info-center {
    justify-self: center;
    margin-inline-start: 0;
  }

  .transkrip-nomor {
    margin-inline-start: 0;
  }
}

@media screen and (max-width: 576px) {
  .transkrip-pdf {
    transform: scale(0.6);
    transform-origin: top center;
  }

  .transkrip-info-center {
    margin-inline-start: 0;
  }

  .transkrip-nomor {
    margin-inline-start: 0;
  }
}

/* Scroll horizontal untuk layar kecil */
.transcript-wrapper {
  -webkit-overflow-scrolling: touch;
  scrollbar-width: thin;
}

/* Memastikan container utama tidak melebihi viewport */
.transcript-preview {
  overflow: visible;
  max-inline-size: 100vw;
}

/* Menambahkan padding untuk touch devices */
@media (hover: none) {
  .transkrip-pdf {
    padding-inline: 1rem;
  }
}

/* Style asli */

/* Wrapper utama dengan background dan scroll horizontal */
.transcript-wrapper {
  display: flex;
  overflow: auto hidden;
  justify-content: center;
  background: #fff;
  margin-inline: calc(-1 * var(--v-container-padding-x, 24px));
  -webkit-overflow-scrolling: touch;
  padding-block: 8px;
  scrollbar-width: thin;
}

/* Preview container dengan background putih landscape */
.transcript-preview {
  display: inline-block;
  overflow: visible; /* allow absolutely positioned buttons to extend outside */
  background: #fff;
  box-shadow: none;
  max-inline-size: 100%;
  min-inline-size: fit-content;
  padding-block: 8px;
  padding-inline: 20px;
}

.transcript-toolbar {
  position: absolute;
  display: flex;
  align-items: center;
  justify-content: space-between;
  inset-block-start: -52px;
  inset-inline: 0;
  padding-inline: 24px;
}

.transcript-title {
  color: #0f172a;
  font-size: 0.95rem;
  font-weight: 700;
  letter-spacing: 0.2px;
}

.transkrip-pdf {
  position: relative;
  overflow: visible;
  box-sizing: border-box;
  border: 1px solid #000;
  background: #fff;
  color: #000;
  font-family: "Times New Roman", Times, serif;
  font-size: 7.5pt;
  inline-size: 100%;
  line-height: 1.25;
  margin-block: 0;
  margin-inline: auto;
  max-inline-size: 337.9mm;
  min-block-size: 278mm;
  padding-block: 8mm 2mm;
  padding-inline: 4mm;
  transform-origin: top center;
  transition: transform 0.3s ease;
}

.transkrip-header {
  display: grid;
  gap: 16px;
  grid-template-columns: 1fr auto 1fr;
  margin-block-end: 8px;
  place-items: flex-start center;
}

.transkrip-info-center {
  font-size: 6.5pt;
  grid-column: 2;
  line-height: 1.2;
  margin-inline-start: 60px;
  text-align: start;
}

.info-row {
  display: block;
  margin-block-end: 1px;
  text-align: start;
}

.info-label-center {
  display: inline;
  font-weight: normal;
}

.info-separator {
  display: inline;
  padding-inline: 3px;
}

.info-value-center {
  display: inline;
  font-weight: normal;
}

.status-text-inline {
  display: inline;
  font-size: 6pt;
  line-height: 1.3;
}

.italic-text {
  font-style: italic;
}

.transkrip-nomor {
  position: relative;
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  font-size: 7pt;
  inline-size: 240px;
  line-height: 1.2;
  margin-inline-start: 70px;
  min-inline-size: 240px;
}

.nomor-row {
  display: flex;
  align-items: baseline;
  justify-content: flex-start;
  gap: 8px;
  inline-size: 100%;
}

.nomor-label {
  font-size: 7pt;
  font-weight: bold;
  text-align: start;
  white-space: nowrap;
}

.nomor-value-box {
  display: inline-block;
  font-size: 8pt;
  font-weight: normal;
  padding-block: 0;
  padding-inline: 0;
  text-align: start;
}

.nomor-sublabel {
  font-size: 6pt;
  font-style: italic;
  inline-size: 100%;
  margin-block-start: 4px;
  text-align: start;
}

.transkrip-tables {
  display: grid;
  align-items: flex-start;
  gap: 4px;
  grid-template-columns: repeat(3, minmax(260px, 1fr));
  inline-size: 100%;
  margin-block-end: 0;
}

.transkrip-table {
  flex: 1;
  align-self: stretch;
  border: 0.5px solid #000;
  background: #fff;
  border-collapse: collapse;
  font-size: 6.5pt;
  inline-size: 100%;
  margin-block-end: 0;
  table-layout: fixed;
  vertical-align: top;
}

.transkrip-table th {
  border: none;
  background: #fff;
  block-size: 18px;
  border-block-end: 0.5px solid #000;
  font-size: 6pt;
  font-weight: bold;
  line-height: 1.1;
  padding-block: 3px;
  padding-inline: 2px;
  text-align: center;
  vertical-align: middle;
}

.th-no {
  inline-size: 24px !important;
  min-inline-size: 24px;
  text-align: center;
  vertical-align: middle;
}

.th-kode {
  inline-size: 50px !important;
  min-inline-size: 50px;
  text-align: center;
  vertical-align: middle;
}

.matkul-center {
  text-align: center !important;
  vertical-align: middle;
}

.th-stack {
  display: flex;
  flex-direction: column;
  align-items: center;
  font-size: 7pt;
  font-weight: bold;
  line-height: 1.2;
}

.th-sub {
  font-size: 6pt;
  font-style: italic;
  font-weight: normal;
  line-height: 1.1;
}

.th-matkul {
  padding-inline-start: 4px;
  text-align: center;
}

.th-sub-block {
  display: block;
}

.th-vertical {
  block-size: 54px !important;
  inline-size: 30px !important;
  min-inline-size: 30px;
  padding-block: 3px !important;
  padding-inline: 1px !important;
}

.vertical-header {
  display: flex;
  overflow: visible;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  block-size: 100%;
  font-size: 6pt;
  font-weight: bold;
  gap: 2px;
  line-height: 1;
  margin-block: 0;
  margin-inline: auto;
  text-orientation: mixed;
  transform: none;
  white-space: nowrap;
  writing-mode: vertical-lr;
}

.vertical-text-indo {
  display: block;
  font-size: 6pt;
  font-weight: bold;
  inline-size: 100%;
  overflow-wrap: normal;
  text-align: center;
  word-break: keep-all;
}

.vertical-text-eng {
  display: block;
  font-size: 6pt;
  font-style: italic;
  font-weight: bold;
  inline-size: 100%;
  overflow-wrap: normal;
  text-align: center;
  word-break: keep-all;
}

.transkrip-table td {
  border: none;
  block-size: 12px;
  font-size: 6.5pt;
  line-height: 1.1;
  padding-block: 2px;
  padding-inline: 2px;
  text-align: center;
  vertical-align: top;
}

.transkrip-table tr.tight-row td {
  block-size: auto;
  line-height: 1;
  min-block-size: 0;
  padding-block: 0;
}

.td-no {
  block-size: 12px;
  inline-size: 24px;
  line-height: 1.1;
  min-inline-size: 24px;
  text-align: center;
  vertical-align: top;
}

.td-kode {
  block-size: 12px;
  font-size: 6pt;
  inline-size: 50px;
  line-height: 1.1;
  min-inline-size: 50px;
  text-align: center;
  vertical-align: top;
}

.td-matkul {
  block-size: 12px;
  line-height: 1.1;
  padding-block: 2px !important;
  padding-inline: 3px !important;
  text-align: start !important;
  vertical-align: top;
}

.td-center {
  block-size: 12px;
  font-size: 6.5pt;
  font-variant-numeric: tabular-nums;
  inline-size: 30px;
  line-height: 1.1;
  min-inline-size: 30px;
  padding-block: 2px;
  padding-inline: 2px;
  text-align: center;
  vertical-align: top;
}

.course-subtitle {
  display: inline;
  color: #000;
  font-size: 5.5pt;
  font-style: italic;
  line-height: 1.1;
  margin-block-start: 1px;
}

.tight-row .course-subtitle {
  display: block;
  margin: 0;
  line-height: 1;
}

.judul-ta-label {
  font-size: 6.5pt;
  font-weight: bold;
  margin-block-end: 2px;
  text-align: center;
}

.judul-ta {
  font-size: 6.5pt;
  line-height: 1.15;
  margin-block-end: 10px;
  text-align: center;
}

.ta-ttd-cell {
  min-block-size: 120px;
  padding-block: 12px;
  padding-inline: 140px;
}

.ta-ttd-content {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
  gap: -20px;
  inline-size: 100%;
  min-block-size: 100%;
}

.ta-signature {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  margin-block-start: auto;
  padding-block-start: 195px;
}

.ta-section {
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 8px;
  margin-block-start: 26px;
  margin-inline: auto;
  max-inline-size: 70%;
  padding-block: 16px 36px;
  text-align: center;
}

.ta-section .judul-ta {
  margin: 0;
  max-inline-size: 100%;
  text-align: center;
  white-space: normal;
  word-break: normal;
}

.location-date {
  font-size: 6.5pt;
  font-weight: bold;
  line-height: 1.2;
  margin-block-start: -8px;
  text-align: center;
}

.dekan {
  font-size: 6.5pt;
  font-weight: bold;
  line-height: 1.25;
  margin-block-start: 9px;
  text-align: center;
}

.nama {
  font-size: pt;
  font-weight: bold;
  line-height: 1.25;
  margin-block-start: 50px;
  text-align: center;
}

.nama u {
  display: block;
  text-align: center;
  text-decoration: underline;
}

/* Hide borders and content for empty rows */
.empty-row {
  background: transparent !important;
}

.empty-row td {
  padding: 0 !important;
  border: none !important;
  background: transparent !important;
  block-size: 12px !important;
  font-size: 6.5pt;
  line-height: 1.1;
  min-block-size: 12px !important;
}

/* Tambahkan style untuk baris kosong terakhir */
.last-empty-row td {
  padding: 0 !important;
  border: none !important;
  background: transparent !important;
  block-size: 12px !important;
}

/* Style untuk empty rows */
.transkrip-table tr.empty-row td {
  padding: 0 !important;
  border: none;
  background: transparent !important;
  block-size: 12px !important;
}

.transkrip-table tr.empty-row {
  background: transparent !important;
}

/* Remove any extra spacing in table cells */
.td-matkul,
.td-no,
.td-kode,
.td-center {
  block-size: 12px !important;
  min-block-size: 12px !important;
}

.view-pdf-btn {
  z-index: 999;
}

/* Tambahkan style untuk tooltip */
.v-tooltip {
  font-size: 12px;
}
</style>
