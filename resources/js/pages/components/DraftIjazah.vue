<template>
  <div class="draft-ijazah-pdf">
    <div class="ijazah-header">
      <div class="ijazah-title" />
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
        <div>REKTOR,<br><span class="sub-label">RECTOR,</span></div>
        <div class="ttd-space" />
        <div class="nama">
          Dr. WARSITI, S.Kp., M.Kep., Sp.Mat.
        </div>
      </div>
      <div class="dekan">
        <div>YOGYAKARTA, {{ user.graduationDate }}<br><span class="sub-label">YOGYAKARTA, MARCH 22, 2025</span></div>
        <div>DEKAN FAKULTAS SAINS DAN TEKNOLOGI,<br><span class="sub-label">DEAN OF FACULTY OF SCIENCE AND TECHNOLOGY,</span></div>
        <div class="ttd-space" />
        <div class="nama">
          Ar. TIKA AINUNNISA FITRIA, S.T., M.T., Ph.D
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'DraftIjazah',
  props: { user: { type: Object, required: true } },
}
</script>

<style scoped>
.draft-ijazah-pdf {
  border: 1px solid #000;
  background: #fff;
  color: #000;
  font-family: "Times New Roman", Times, serif;
  font-size: 11pt;
  margin-block: 0 32px;
  margin-inline: auto;
  max-inline-size: 900px;
  padding-block: 32px;
  padding-inline: 40px;
}

.ijazah-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
}

.ijazah-title {
  flex: 1;
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
  font-size: 1rem;
  font-weight: bold;
}

.ijazah-body {
  margin-block-start: 32px;
}

.ijazah-table {
  border-collapse: collapse;
  inline-size: 100%;
  margin-block-end: 18px;
}

.ijazah-table td {
  border: none;
  padding-block: 2px;
  padding-inline: 6px;
  vertical-align: top;
}

.ijazah-table .label {
  font-weight: bold;
  inline-size: 260px;
}

.ijazah-table .sub-label {
  font-size: 10pt;
  font-style: italic;
  font-weight: normal;
}

.ijazah-table .colon {
  inline-size: 12px;
}

.ijazah-table .nik-block {
  display: inline-block;
  background: #000;
  min-block-size: 18px;
  min-inline-size: 200px;
  vertical-align: middle;
}

.ijazah-explanation {
  font-size: 11pt;
  margin-block-start: 18px;
}

.ijazah-explanation .sub-label {
  font-size: 10pt;
  font-style: italic;
}

.ijazah-signatures {
  display: flex;
  justify-content: space-between;
  margin-block-start: 48px;
}

.ijazah-signatures .rektor {
  min-inline-size: 260px;
  text-align: start;
}

.ijazah-signatures .dekan {
  min-inline-size: 340px;
  text-align: end;
}

.ijazah-signatures .ttd-space {
  min-block-size: 60px;
}

.ijazah-signatures .nama {
  font-weight: bold;
  margin-block-start: 8px;
}
</style> 
