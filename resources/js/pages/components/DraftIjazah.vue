<template>
  <div class="position-relative mb-6">
    <div class="draft-ijazah-preview">
      <!-- Pindahkan tombol ke dalam draft-ijazah-pdf -->
      <div class="draft-ijazah-pdf">
        <!-- Tombol PDF dipindah ke sini -->
        <VBtn
          color="primary"
          icon
          class="view-pdf-btn"
          @click="openPdfViewer"
        >
          <VIcon>mdi-file-pdf-box</VIcon>
        </VBtn>
        
        <!-- Existing draft ijazah content -->
        <div class="ijazah-header">
          <div class="ijazah-title">
            <!-- Space for university logo/seal -->
          </div>
          <div class="ijazah-nomor">
            <div class="label">
              Nomor Ijazah Nasional:
            </div>
            <div class="sub-label">
              National Certificate Number
            </div>
            <div class="value">
              051022592012025100002
            </div>
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
                  <b>{{ user.name }}</b>
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
                  {{ user.birthPlace }}, {{ user.birthDate }}
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
                  {{ user.studyProgram }}
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
                  {{ user.degree }}
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
                  {{ user.graduationDate }}
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
            <div class="signature-label">REKTOR,<br><span class="sub-label">RECTOR,</span></div>
            <div class="ttd-space" />
            <div class="nama">
              Dr. WARSITI, S.Kp., M.Kep., Sp.Mat.
            </div>
          </div>
          <div class="stamp-area">
            <div class="stamp-box" />
          </div>
          <div class="dekan">
            <div class="date-location">YOGYAKARTA, {{ user.graduationDate }}<br><span class="sub-label">YOGYAKARTA, MARCH 22, 2025</span></div>
            <div class="signature-label">DEKAN FAKULTAS SAINS DAN TEKNOLOGI,<br><span class="sub-label">DEAN OF FACULTY OF SCIENCE AND TECHNOLOGY,</span></div>
            <div class="ttd-space" />
            <div class="nama">
              Ar. TIKA AINUNNISA FITRIA, S.T., M.T., Ph.D
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script setup>
import html2pdf from 'html2pdf.js'

const props = defineProps({
  user: { type: Object, required: true }
})

const openPdfViewer = async () => {
  const element = document.querySelector('.draft-ijazah-pdf').cloneNode(true)
  
  // Hapus tombol dari clone
  const button = element.querySelector('.view-pdf-btn')
  if (button) {
    button.remove()
  }

  const opt = {
    margin: 10,
    filename: 'ijazah.pdf',
    image: { type: 'jpeg', quality: 0.98 },
    html2canvas: { scale: 2 },
    jsPDF: { unit: 'mm', format: 'a4', orientation: 'landscape' }
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
</script>

<style scoped>
.draft-ijazah-preview {
  border: 1px solid #e0e0e0;
  border-radius: 12px;
  margin: auto;
  background: #fff;
  box-shadow: 0 2px 8px 0 rgba(33, 150, 243, 0.05);
  padding: 24px;
  inline-size: fit-content;
  display: flex;
  justify-content: center;
  align-items: center;
}

.draft-ijazah-pdf {
  position: relative;
  border: 1px solid #000;
  background: #fff;
  color: #000;
  font-family: "Times New Roman", Times, serif;
  font-size: 10pt;
  margin-block: 0 32px;
  margin-inline: auto;
  /* A4 Landscape: 29.7cm x 21cm = 1122px x 793px (at 96 DPI) */
  inline-size: 1122px;
  block-size: 793px;
  /* Padding disesuaikan untuk tampilan layar yang proporsional */
  padding-block-start: 30px;
  padding-block-end: 40px;
  padding-inline-start: 100px;
  padding-inline-end: 100px;
  box-sizing: border-box;
  overflow: hidden;
}

.ijazah-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-block-end: 80px;
}

.ijazah-title {
  flex: 1;
  min-block-size: 100px;
}

.ijazah-nomor {
  min-inline-size: 320px;
  text-align: end;
}

.ijazah-nomor .label {
  font-weight: bold;
}

.ijazah-nomor .sub-label {
  font-size: 10pt;
  font-style: italic;
}

.ijazah-nomor .value {
  font-size: 11pt;
  font-weight: bold;
  margin-block-start: 4px;
}

.ijazah-body {
  margin-block-start: 20px;
}

.ijazah-table {
  border-collapse: collapse;
  inline-size: 100%;
  margin-block-end: 18px;
}

.ijazah-table td {
  border: none;
  padding-block: 4px;
  padding-inline: 6px;
  vertical-align: top;
}

.ijazah-table .label {
  font-weight: normal;
  inline-size: 240px;
  font-size: 11pt;
}

.ijazah-table .sub-label {
  font-size: 9pt;
  font-style: italic;
  font-weight: normal;
}

.ijazah-table .colon {
  inline-size: 20px;
  font-weight: normal;
}

.ijazah-table .value {
  font-size: 11pt;
}

.ijazah-table .nik-block {
  display: inline-block;
  background: #000;
  min-block-size: 16px;
  min-inline-size: 180px;
  vertical-align: middle;
}

.ijazah-explanation {
  font-size: 11pt;
  margin-block-start: 20px;
  line-height: 1.5;
  text-align: justify;
}

.ijazah-explanation .sub-label {
  font-size: 9pt;
  font-style: italic;
}

.ijazah-signatures {
  display: flex;
  justify-content: space-between;
  align-items: flex-end;
  margin-block-start: 60px;
  position: relative;
}

.ijazah-signatures .rektor {
  flex: 1;
  text-align: start;
}

.ijazah-signatures .stamp-area {
  flex: 0 0 auto;
  display: flex;
  align-items: center;
  justify-content: center;
  margin-inline: 20px;
}

.ijazah-signatures .stamp-box {
  inline-size: 120px;
  block-size: 120px;
  border: 2px dashed #666;
}

.ijazah-signatures .dekan {
  flex: 1;
  text-align: end;
}

.ijazah-signatures .signature-label {
  font-size: 11pt;
  margin-block-end: 8px;
}

.ijazah-signatures .date-location {
  font-size: 11pt;
  margin-block-end: 8px;
}

.ijazah-signatures .ttd-space {
  min-block-size: 70px;
}

.ijazah-signatures .nama {
  font-weight: normal;
  margin-block-start: 8px;
  font-size: 11pt;
}

.view-pdf-btn {
  position: absolute;
  z-index: 1;
  inset-block-start: 20px;
  inset-inline-end: 20px;
}
</style>
