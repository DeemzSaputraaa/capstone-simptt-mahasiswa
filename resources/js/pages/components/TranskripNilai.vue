<template>
  <div class="position-relative mb-6">
    <div class="transcript-wrapper">
      <div class="transcript-preview pa-6">
        <div class="transkrip-pdf">
          <!-- Tombol PDF dipindah ke sini -->
          <VBtn
            color="primary"
            icon
            class="view-pdf-btn"
            @click="openPdfViewer"
          >
            <VIcon>mdi-file-pdf-box</VIcon>
          </VBtn>
        
        
          <div class="transkrip-header">
            <div class="transkrip-info-center">
              <div class="info-row">
                <span class="info-label-center">NIM/NAMA LENGKAP</span>
                <span class="info-separator">:</span>
                <span class="info-value-center">{{ user.nim }} / {{ user.name }}</span>
              </div>
              <div class="info-row">
                <span class="info-label-center">TEMPAT/TANGGAL LAHIR</span>
                <span class="info-separator">:</span>
                <span class="info-value-center">{{ user.birthPlace }}, {{ user.birthDate }}</span>
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
                <span class="info-value-center">{{ user.studyProgram }} / {{ user.degree }}</span>
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
                <span class="info-value-center">{{ user.graduationDate }}</span>
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
                  </th>
                  <th
                    rowspan="3"
                    class="th-kode"
                  >
                    KODE
                  </th>
                  <th
                    rowspan="3"
                    class="th-matkul matkul-center"
                  >
                    MATA KULIAH
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
                  :class="[{ 'empty-row': mk.isEmpty }, { 'last-empty-row': mk.isLastEmpty }]"
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
                    <div class="judul-ta-label">
                      <b>JUDUL TUGAS AKHIR:</b>
                    </div>
                    <div class="judul-ta">
                      SISTEM INFORMASI PENDAFTARAN PADA KLINIK FISIOTERAPI<br>
                      UNIVERSITAS AISYIYAH YOGYAKARTA MENGGUNAKAN METODE RAD<br>
                      (RAPID APPLICATION DEVELOPMENT)<br>
                      <span class="italic-text">REGISTRATION INFORMATION SYSTEM AT AISYIYAH UNIVERSITY PHYSIOTHERAPY CLINIC<br>
                        YOGYAKARTA USING RAD METHOD (RAPID APPLICATION DEVELOPMENT)</span>
                    </div>
                    <div class="ttd-section">
                      <div class="dekan">
                        <div class="ttd-space" />
                        <div class="location-date">
                          Yogyakarta, 22 Maret 2025<br>
                          <span class="italic-text">Yogyakarta, March 22, 2025</span>
                        </div>
                        DEKAN FAKULTAS SAINS DAN TEKNOLOGI,<br>
                        <span class="italic-text">DEAN OF FACULTY OF SCIENCE AND TECHNOLOGY,</span>
                        <div class="nama">
                          <u>Ir. Ar. TIKA AINUNNISA FITRIA, S.T., M.T., Ph.D</u>
                        </div>
                      </div>
                    </div>
                  </td>
                </tr>
              </tbody>
              <tbody v-if="idx !== 2">
                <tr class="spacer-row">
                  <td colspan="6" />
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
      block-size: 54px !important;
      inline-size: 30px !important;
      min-inline-size: 30px !important;
      padding-block: 3px !important;
      padding-inline: 1px !important;
    }
    .transkrip-table .vertical-header {
      display: flex !important;
      overflow: visible !important;
      flex-direction: column !important;
      align-items: center !important;
      justify-content: center !important;
      block-size: 100% !important;
      font-size: 6pt !important;
      font-weight: bold !important;
      gap: 2px !important;
      line-height: 1 !important;
      margin-block: 0 !important;
      margin-inline: auto !important;
      text-orientation: mixed !important;
      transform: none !important;
      white-space: nowrap !important;
      writing-mode: vertical-lr !important;
    }
    .transkrip-table .vertical-text-indo {
      display: block !important;
      font-size: 6pt !important;
      font-weight: bold !important;
      inline-size: 100% !important;
      overflow-wrap: normal !important;
      text-align: center !important;
      word-break: keep-all !important;
    }
    .transkrip-table .vertical-text-eng {
      display: block !important;
      font-size: 6pt !important;
      font-style: italic !important;
      font-weight: bold !important;
      inline-size: 100% !important;
      overflow-wrap: normal !important;
      text-align: center !important;
      word-break: keep-all !important;
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
}

@media screen and (max-width: 768px) {
  .transkrip-pdf {
    transform: scale(0.7);
    transform-origin: top center;
  }

  .transkrip-info-center {
    margin-inline-start: 30px;
  }

  .transkrip-nomor {
    margin-inline-start: 30px;
  }
}

@media screen and (max-width: 576px) {
  .transkrip-pdf {
    transform: scale(0.6);
    transform-origin: top center;
  }

  .transkrip-info-center {
    margin-inline-start: 10px;
  }

  .transkrip-nomor {
    margin-inline-start: 10px;
  }
}

/* Scroll horizontal untuk layar kecil */
.transcript-wrapper {
  -webkit-overflow-scrolling: touch;
  scrollbar-width: thin;
}

/* Memastikan container utama tidak melebihi viewport */
.transcript-preview {
  overflow: hidden;
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
  background: #f5f5f5;
  margin-inline: calc(-1 * var(--v-container-padding-x, 24px));
  -webkit-overflow-scrolling: touch;
  padding-block: 20px;
  scrollbar-width: thin;
}

/* Preview container dengan background putih landscape */
.transcript-preview {
  display: inline-block;
  background: #fff;
  box-shadow: 0 2px 8px rgba(0, 0, 0, 10%);
  max-inline-size: 100%;
  min-inline-size: fit-content;
  overflow-x: auto;
  padding-block: 24px;
  padding-inline: 24px;
}

.transkrip-pdf {
  position: relative;
  overflow: hidden;
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
  padding-block: 9.5mm 0;
  padding-inline: 2.5mm;
  transform-origin: top center;
  transition: transform 0.3s ease;
}

.transkrip-header {
  display: flex;
  align-items: flex-start;
  justify-content: center;
  gap: 40px;
  margin-block-end: 6px;
}

.transkrip-info-center {
  flex: 0 0 auto;
  font-size: 6.5pt;
  line-height: 1.2;
  margin-inline-start: 50px;
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
  align-items: center;
  font-size: 7pt;
  line-height: 1.2;
  margin-inline-start: 70px;
  min-inline-size: 190px;
}

.nomor-label {
  flex: 1;
  font-size: 7pt;
  font-weight: bold;
  text-align: start;
}

.nomor-header {
  display: flex;
  align-items: center;
  justify-content: space-between;
  inline-size: 100%;
  margin-block-end: 2px;
}

.nomor-sublabel {
  display: block;
  font-size: 6.5 pt;
  inline-size: 100%;
  margin-block-start: 1px;
}

.nomor-value-box {
  display: inline-block;
  font-size: 8pt;
  font-weight: normal;
  padding-block: 3px;
  padding-inline: 6px;
  text-align: center;
}

.transkrip-tables {
  display: grid;
  grid-template-columns: repeat(3, minmax(280px, 1fr));
  gap: 8px;
  margin-block-end: 0;
  width: 100%;
  align-items: stretch;
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
  vertical-align: top;
  table-layout: fixed;
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
  vertical-align: top;
  text-align: center;
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
  text-align: start;
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

.judul-ta-label {
  font-size: 6.5pt;
  font-weight: bold;
  margin-block-end: 2px;
  text-align: center;
}

.judul-ta {
  font-size: 6.5pt;
  line-height: 1.15;
  margin-block-end: 6px;
  text-align: center;
}

.location-date {
  font-size: 6.5pt;
  line-height: 1.2;
  margin-block-end: 4px;
  text-align: center;
}

.ttd-section {
  display: flex;
  justify-content: center;
}

.ta-ttd-cell {
  background: #fff;
  border-block-end: 0.5px solid #000 !important;
  border-inline-end: 0.5px solid #000;
  border-inline-start: 0.5px solid #000;
  padding-block: 4px;
  padding-block-end: 18px;
  padding-inline: 4px;
  text-align: center;
  vertical-align: top;
}

.dekan {
  font-size: 6.5pt;
  line-height: 1.15;
  min-inline-size: 170px;
  text-align: center;
}

.ttd-space {
  min-block-size: 380px;
}

.nama {
  font-size: 6.5pt;
  font-weight: bold;
  margin-block-start: 2px;
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

.spacer-row td {
  block-size: 220px;
  border: none !important;
  background: transparent !important;
  padding: 0 !important;
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
  position: absolute;
  z-index: 1;
  inset-block-start: 20px;
  inset-inline-end: 20px;
}

/* Tambahkan style untuk tooltip */
.v-tooltip {
  font-size: 12px;
}
</style>
