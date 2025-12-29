<template>
  <div class="position-relative mb-6">
    <div class="draft-wrapper">
      <div class="draft-ijazah-preview position-relative">
        <div class="draft-toolbar">
          <div class="draft-title">
            Draft Ijazah
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
        <div class="draft-ijazah-pdf">
          <!-- Existing draft ijazah content -->

          <!-- Nomor Ijazah Nasional (aligned label + value, with English sublabel) -->
          <div class="ijazah-nomor-top" aria-hidden="false">
            <div class="nomor-container">
              <div class="nomor-row">
                <div class="nomor-labels">Nomor Ijazah Nasional :</div>
                <div class="nomor-value">05102259012021/500002</div>
              </div>
              <div class="nomor-sublabel italic-text">National Certificate Number</div>
            </div>
          </div>

          <div class="ijazah-header">
            <div class="ijazah-title">
            <!-- Space for university logo/seal -->
            </div>
          </div>
          <div class="ijazah-body">
            <table class="ijazah-table">
              <tbody>
                <tr>
                  <td class="label">
                    Memberikan Kepada<br><span class="sub-label">Awarded to</span>
                  </td>
                  <td class="colon">
                    :
                  </td>
                  <td class="value">
                    <span class="value-main">{{ user.name?.toUpperCase() }}</span>
                  </td>
                </tr>
                <tr>
                  <td class="label">
                    Tempat dan Tanggal Lahir<br><span class="sub-label">Place and Date of Birth</span>
                  </td>
                  <td class="colon">
                    :
                  </td>
                  <td class="value">
                    <span class="value-main">{{ user.birthPlace?.toUpperCase() }}, {{ user.birthDate?.toUpperCase() }}</span><br>
                    <span class="value-sub">{{ user.birthPlace }}, {{ user.birthDate }}</span>
                  </td>
                </tr>
                <tr>
                  <td class="label">
                    Nomor Induk Kependudukan<br><span class="sub-label">Identity Number</span>
                  </td>
                  <td class="colon">
                    :
                  </td>
                  <td class="value">
                    <span class="nik-block" />
                  </td>
                </tr>
                <tr>
                  <td class="label">
                    N I M<br><span class="sub-label">Student Number</span>
                  </td>
                  <td class="colon">
                    :
                  </td>
                  <td class="value">
                    {{ user.nim }}
                  </td>
                </tr>
                <tr>
                  <td class="label">
                    Program Studi<br><span class="sub-label">Study Program</span>
                  </td>
                  <td class="colon">
                    :
                  </td>
                  <td class="value">
                    <span class="value-main">{{ user.studyProgram?.toUpperCase() }}</span><br>
                    <span class="value-sub">{{ user.studyProgram }}</span>
                  </td>
                </tr>
                <tr>
                  <td class="label">
                    Jenjang Pendidikan<br><span class="sub-label">Degree</span>
                  </td>
                  <td class="colon">
                    :
                  </td>
                  <td class="value">
                    <span class="value-main">{{ user.degree?.toUpperCase() }}</span><br>
                    <span class="value-sub">{{ user.degree }}</span>
                  </td>
                </tr>
                <tr>
                  <td class="label">
                    Tanggal Kelulusan<br><span class="sub-label">Date of Graduation</span>
                  </td>
                  <td class="colon">
                    :
                  </td>
                  <td class="value">
                    <span class="value-main">{{ user.graduationDate?.toUpperCase() }}</span><br>
                    <span class="value-sub">{{ user.graduationDate }}</span>
                  </td>
                </tr>
                <tr>
                  <td class="label">
                    Status<br><span class="sub-label">Status</span>
                  </td>
                  <td class="colon">
                    :
                  </td>
                  <td class="value">
                    Akreditasi LAM INFOKOM No. 027/SK/LAM-INFOKOM/Ak/S/III/2024 tanggal 19 Maret 2024 dengan peringkat "Baik Sekali".<br><span class="sub-label">Accredited "Very Good" by LAM INFOKOM No. 027/SK/LAM-INFOKOM/Ak/S/III/2024 on March 19, 2024.</span>
                  </td>
                </tr>
              </tbody>
            </table>
            <div class="ijazah-explanation">
              Ijazah ini diserahkan setelah yang bersangkutan memenuhi semua persyaratan yang ditentukan, dan kepadanya dilimpahkan segala wewenang dan hak yang berhubungan dengan ijazah yang dimilikinya, serta berhak memakai gelar Sarjana Komputer (S.Kom.).<br>
              <span class="sub-label">This certificate is awarded in recognition of fulfillment of the requirements for the degree. Therefore, the person has been awarded the degree of Bachelor of Computer Science with all rights and privileges associated with the degree.</span>
            </div>
          </div>
          <div class="ijazah-signatures">
            <div class="rektor">
              <div class="signature-label">
                REKTOR,<br><span class="sub-label">RECTOR,</span>
              </div>
              <div class="ttd-space" />
              <div class="nama">
                Dr. WARSITI, S.Kp., M.Kep., Sp.Mat.
              </div>
            </div>
            <div class="stamp-area">
              <div class="stamp-box" />
            </div>
            <div class="dekan">
              <div class="date-location">
                YOGYAKARTA, 22 MARET 2025<br><span class="sub-label">YOGYAKARTA, MARCH 22, 2025</span>
              </div>
              <div class="signature-label">
                DEKAN FAKULTAS SAINS DAN TEKNOLOGI,<br><span class="sub-label">DEAN OF FACULTY OF SCIENCE AND TECHNOLOGY,</span>
              </div>
              <div class="ttd-space" />
              <div class="nama">
                Ar. TIKA AINUNNISA FITRIA, S.T., M.T., Ph.D
              </div>
            </div>
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

const openPdfViewer = async () => {
  const element = document.querySelector('.draft-ijazah-pdf').cloneNode(true)
  
  // Hapus tombol dari clone
  const button = element.querySelector('.view-pdf-btn')
  if (button) {
    button.remove()
  }
  
  // Hapus border untuk PDF
  element.style.border = 'none'

  const opt = {
    margin: [5, 5, 5, 5],
    filename: 'ijazah.pdf',
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
        <title>ijazah.pdf</title>
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

const columns = computed(() => {
  // Add bobot and kredit to each matakuliah
  const enrichedData = props.matakuliah.map(mk => ({
    ...mk,
    bobot: getNilaiBobot(mk.nilai),
    kredit: (mk.sks * getNilaiBobot(mk.nilai)).toFixed(2),
  }))

  // Distribute data into 3 columns with proper numbering
  const total = enrichedData.length
  const col1Count = 27 // Fixed size for column 1
  const col2Count = 27 // Fixed size for column 2

  const col1 = enrichedData.slice(0, col1Count).map((mk, i) => ({
    ...mk,
    no: i + 1,
  }))

  const col2 = enrichedData.slice(col1Count, col1Count + col2Count).map((mk, i) => ({
    ...mk,
    no: i + 28,
  }))

  const col3Data = enrichedData.slice(col1Count + col2Count)
  const col3 = []

  // Fill col3 with actual data
  col3Data.forEach((mk, i) => {
    col3.push({
      ...mk,
      no: i + 55,
    })
  })

  // Add empty rows to match height of other columns
  const targetRowCount = 27 // Same as col1 and col2
  const emptyRowsNeeded = targetRowCount - col3.length

  for (let i = 0; i < emptyRowsNeeded; i++) {
    col3.push({
      no: '',
      kode: '',
      nama: '',
      nama_en: '',
      sks: '',
      nilai: '',
      bobot: '',
      kredit: '',
      isEmpty: true,
    })
  }

  return [col1, col2, col3]
})
</script>

<style scoped>
/* Wrapper utama untuk centering saat zoom */
.draft-wrapper {
  display: flex;
  overflow: auto hidden;
  box-sizing: border-box;
  align-items: center;
  justify-content: center;

  /* make outermost layer white and align with container */
  background: #fff;
  inline-size: 100%;
  margin-inline: 0; /* align with parent page container */

  /* adjust spacing so preview matches top layer */
  padding-block: 60px 1px;    /* PADDING BAWAH */  /* PADDING ATAS */
  padding-inline: var(--v-container-padding-x, 24px);
}

.draft-ijazah-preview {
  display: inline-block;
  background: #fff;
  box-shadow: none;
  inline-size: 100%;
  margin-block: 0; /* remove extra top/bottom gap */
  margin-inline: auto;
  max-inline-size: 980px; /* limit width to match top layer/card */
  padding-block: 20px; /* keep internal spacing small */
  padding-inline: 20px;
}

.draft-toolbar {
  position: absolute;
  display: flex;
  align-items: center;
  justify-content: space-between;
  inset-block-start: -52px;
  inset-inline: 0;
  padding-inline: 24px;
}

.draft-title {
  color: #0f172a;
  font-size: 0.95rem;
  font-weight: 700;
  letter-spacing: 0.2px;
}

.draft-ijazah-pdf {
  position: relative;
  overflow: visible; /* allow button to extend outside the paper border */
  box-sizing: border-box;
  border: 2px solid #000;
  background: #fff;
  block-size: auto;
  color: #000;
  font-family: "Times New Roman", Times, serif;
  font-size: 10pt;
  inline-size: 100%;

  /* keep paper area proportional but not extend outer background */
  max-block-size: 793px;
  max-inline-size: 1122px;

  /* Padding disesuaikan untuk tampilan layar yang proporsional */
  padding-block: 12px; /* smaller vertical padding */
  padding-inline: clamp(20px, 4vw, 60px);
}

.ijazah-nomor-top {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  margin-block-end: 15px;
  margin-inline-start: 480px;
}

.ijazah-nomor-top .nomor-container {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

.ijazah-nomor-top .nomor-row {
  display: flex;
  align-items: baseline;
  justify-content: flex-start;
  gap: 18px;
  inline-size: 100%;
}

.ijazah-nomor-top .nomor-labels {
  line-height: 1.3;
  min-inline-size: 220px;
  text-align: end;
}

.ijazah-nomor-top .label {
  font-size: 9pt;
  font-weight: normal;
}

.ijazah-nomor-top .sub-label {
  font-size: 8pt;
  font-style: italic;
  font-weight: normal;
}

.ijazah-nomor-top .nomor-value {
  font-size: 9pt;
  font-weight: normal;
  min-inline-size: 180px;
  padding-block-start: 0;
  text-align: start;
}

.ijazah-nomor-top .nomor-sublabel {
  display: block;
  font-size: 7pt;
  font-style: italic;
  margin-block-start: 4px;

  /* Geser sedikit ke kanan agar sejajar dengan value di atasnya */
  margin-inline-start: 90px;
  text-align: start;
}

.ijazah-header {
  margin-block-end: 10px;
}

.ijazah-title {
  min-block-size: 40px;
}

.ijazah-body {
  margin-block-start: 10px;
}

.ijazah-table {
  border-collapse: collapse;
  inline-size: 100%;
  margin-block-end: 10px;
}

.ijazah-table td {
  border: none;
  line-height: 1.4;
  padding-block: 2px;
  padding-inline: 4px;
  vertical-align: top;
}

.ijazah-table .label {
  font-size: 10pt;
  font-weight: normal;
  inline-size: 190px;
}

.ijazah-table .sub-label {
  font-size: 8pt;
  font-style: italic;
  font-weight: normal;
}

.ijazah-table .colon {
  font-size: 10pt;
  font-weight: normal;
  inline-size: 15px;
}

.ijazah-table .value {
  font-size: 10pt;
  line-height: 1.5;
}

.ijazah-table .value-main {
  font-size: 10pt;
  font-weight: bold;
}

.ijazah-table .value-sub {
  font-size: 8pt;
  font-style: italic;
  font-weight: normal;
}

.ijazah-table .nik-block {
  display: inline-block;
  background: #000;
  min-block-size: 14px;
  min-inline-size: 200px;
  vertical-align: middle;
}

.ijazah-explanation {
  font-size: 9pt;
  line-height: 1.3;
  margin-block: 5px 8px;
  text-align: justify;
}

.ijazah-explanation .sub-label {
  display: block;
  font-size: 8pt;
  font-style: italic;
  font-weight: normal;
  margin-block-start: 2px;
}

.ijazah-signatures {
  position: relative;
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-block-start: 0;
}

.ijazah-signatures .rektor {
  flex: 1;
  padding-block-start: 30px;
  text-align: start;
}

.ijazah-signatures .stamp-area {
  display: flex;
  flex: 0 0 auto;
  align-items: center;
  justify-content: center;
  margin-inline: 30px;
}

.ijazah-signatures .stamp-box {
  border: 2px dashed #999;
  border-radius: 2px;
  block-size: 180px;
  inline-size: 150px;
}

.ijazah-signatures .dekan {
  flex: 1;
  padding-block-start: 0;
  text-align: start;
}

.ijazah-signatures .signature-label {
  font-size: 9pt;
  line-height: 1.3;
  margin-block-end: 6px;
}

.ijazah-signatures .signature-label .sub-label {
  font-size: 8pt;
  font-style: italic;
  font-weight: normal;
}

.ijazah-signatures .date-location {
  font-size: 9pt;
  line-height: 1.3;
  margin-block-end: 6px;
}

.ijazah-signatures .date-location .sub-label {
  font-size: 8pt;
  font-style: italic;
  font-weight: normal;
}

.ijazah-signatures .ttd-space {
  min-block-size: 40px;
}

.ijazah-signatures .nama {
  font-size: 9pt;
  font-weight: normal;
  margin-block-start: 6px;
}

.view-pdf-btn {
  z-index: 999;
}

.empty-row td {
  padding: 2px !important; /* Tambahkan padding yang sama dengan row berisi */
  border: 1px solid #000 !important; /* Tampilkan border */
  background: #fff !important;
  block-size: 12px;
  min-block-size: 12px;
}

/* Ubah display none menjadi table-row */
.empty-row {
  display: table-row !important;
  visibility: visible !important;
}

@media (max-width: 1024px) {
  .draft-wrapper {
    justify-content: flex-start;
    margin-inline: 0;
    padding-inline: 16px;
  }

  .draft-ijazah-preview {
    padding: 16px;
  }

  .draft-ijazah-pdf {
    font-size: 9pt;
  }

  .ijazah-table .label {
    inline-size: 150px;
  }
}

@media (max-width: 640px) {
  .draft-wrapper {
    padding-inline: 12px;
  }

  .draft-ijazah-preview {
    padding: 12px;
  }

  .draft-ijazah-pdf {
    padding-inline: clamp(16px, 5vw, 32px);
  }

  .ijazah-table .label {
    inline-size: 130px;
  }

  .ijazah-table .value {
    line-height: 1.4;
  }
}
</style>
