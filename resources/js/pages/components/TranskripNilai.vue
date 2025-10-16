<template>
  <div class="transkrip-pdf">
    <div class="transkrip-header">
      <div class="transkrip-info">
        <div><b>NIM / NAMA LENGKAP</b> : {{ user.nim }} / {{ user.name }}</div>
        <div><b>TEMPAT/TANGGAL LAHIR</b> : {{ user.birthPlace }}, {{ user.birthDate }}</div>
        <div><b>PROGRAM STUDI / JENJANG</b> : {{ user.studyProgram }} / {{ user.degree }}</div>
        <div><b>TANGGAL KELULUSAN</b> : {{ user.graduationDate }}</div>
        <div><b>TOTAL SKS</b> : {{ totalSks }}</div>
        <div><b>INDEKS PRESTASI</b> : {{ ipk }}</div>
        <div><b>PREDIKAT KELULUSAN</b> : {{ predikat }}</div>
        <div><b>STATUS</b> : Akreditasi LAM INFOKOM No. 027/SK/LAM-INFOKOM/Ak/S/III/2024 tanggal 19 Maret 2024 dengan peringkat "Baik Sekali".<br><span class="sub-label">Accredited "Very Good" by LAM INFOKOM No. 027/SK/LAM-INFOKOM/Ak/S/III/2024 on March 19, 2024.</span></div>
      </div>
      <div class="transkrip-nomor">
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
    <div class="transkrip-tables">
      <table
        v-for="(col, idx) in columns"
        :key="idx"
        class="transkrip-table no-inner-border"
      >
        <thead>
          <tr>
            <th style="inline-size: 36px;">
              NO<br>NO
            </th>
            <th style="inline-size: 70px;">
              KODE<br>CODE
            </th>
            <th style="min-inline-size: 180px; text-align: start;">
              MATA KULIAH<br>SUBJECTS
            </th>
            <th
              class="vertical-text"
              style="inline-size: 30px;"
            >
              SKS<br>CREDITS
            </th>
            <th
              class="vertical-text"
              style="inline-size: 30px;"
            >
              NILAI<br>GRADE
            </th>
            <th
              class="vertical-text"
              style="inline-size: 30px;"
            >
              EKUIVALEN<br>EQUIVALENT
            </th>
          </tr>
        </thead>
        <tbody>
          <tr
            v-for="(mk, i) in col"
            :key="mk.no + mk.kode"
          >
            <td class="center">
              {{ mk.no }}
            </td>
            <td class="center">
              {{ mk.kode }}
            </td>
            <td class="left">
              {{ mk.nama }}<br><span class="sub-label">{{ mk.nama_en }}</span>
            </td>
            <td class="center">
              {{ mk.sks }}
            </td>
            <td class="center">
              {{ mk.nilai }}
            </td>
            <td class="center">
              {{ mk.ekuivalen }}
            </td>
          </tr>
          <!-- Judul tugas akhir & ttd dekan hanya di kolom ketiga -->
          <tr v-if="idx === 2">
            <td
              colspan="6"
              class="ta-ttd-cell"
            >
              <div class="judul-ta">
                <b>JUDUL TUGAS AKHIR</b><br><span class="sub-label">THESIS TITLE</span><br>
                {{ thesisTitle.indo }}<br>
                <span class="sub-label">(RAPID APPLICATION DEVELOPMENT)</span><br>
                {{ thesisTitle.eng }} (RAPID APPLICATION DEVELOPMENT)
              </div>
              <div class="ttd-row">
                <div class="dekan">
                  YOGYAKARTA, {{ user.graduationDate }}<br><span class="sub-label">YOGYAKARTA, MARCH 22, 2025</span><br>
                  DEKAN FAKULTAS SAINS DAN TEKNOLOGI,<br><span class="sub-label">DEAN OF FACULTY OF SCIENCE AND TECHNOLOGY,</span>
                  <div class="ttd-space" />
                  <div class="nama">
                    Ar. TIKA AINUNNISA FITRIA, S.T., M.T., Ph.D
                  </div>
                </div>
              </div>
            </td>
          </tr>
        </tbody>
      </table>
    </div>
  </div>
</template>

<script setup>
import { computed } from 'vue'

const props = defineProps({
  user: { type: Object, required: true },
  matakuliah: { type: Array, required: true },
  totalSks: { type: [Number, String], default: 146 },
  ipk: { type: [Number, String], default: 2.94 },
  predikat: { type: String, default: 'MEMUASKAN / SATISFACTORY' },
  thesisTitle: { type: Object, default: () => ({
    indo: 'SISTEM INFORMASI PENDAFTARAN PADA KLINIK FISIOTERAPI UNIVERSITAS AISYIYAH YOGYAKARTA MENGGUNAKAN METODE RAD',
    eng: 'REGISTRATION INFORMATION SYSTEM AT AISYIYAH UNIVERSITY PHYSIOTHERAPY CLINIC YOGYAKARTA USING RAD METHOD'
  }) },
})

const columns = computed(() => {
  const total = props.matakuliah.length
  const perCol = Math.ceil(total / 3)
  
  return [
    props.matakuliah.slice(0, perCol),
    props.matakuliah.slice(perCol, perCol * 2),
    props.matakuliah.slice(perCol * 2),
  ]
})
</script>

<style scoped>
.transkrip-pdf {
  overflow: hidden;
  box-sizing: border-box;
  border: 1px solid #000;
  background: #fff;
  color: #000;
  font-family: "Times New Roman", Times, serif;
  font-size: 9pt;
  line-height: 1.2;
  margin-block: 0 32px;
  margin-inline: auto;
  max-inline-size: 297mm;
  min-block-size: 210mm;
  padding-block: 18px;
  padding-inline: 10px;
}

.transkrip-header {
  display: flex;
  align-items: flex-start;
  justify-content: space-between;
  margin-block-end: 8px;
}

.transkrip-info {
  flex: 1;
  font-size: 10pt;
}

.transkrip-nomor {
  min-inline-size: 220px;
  text-align: end;
}

.transkrip-nomor .label {
  font-weight: bold;
}

.transkrip-nomor .sub-label {
  font-size: 8pt;
  font-style: italic;
}

.transkrip-nomor .value {
  font-size: 1rem;
  font-weight: bold;
}

.transkrip-tables {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  gap: 8px;
  margin-block-end: 10px;
}

.transkrip-table {
  border: 1px solid #000;
  background: #fff;
  border-collapse: collapse;
  flex: 1;
  font-size: 9pt;
  margin-block-end: 0;
  min-inline-size: 220px;
}

.transkrip-table th {
  border: 1px solid #000;
  background: #fff;
  font-size: 9pt;
  font-weight: bold;
  padding-block: 4px;
  padding-inline: 2px;
  text-align: center;
}

.transkrip-table td {
  border: 1px solid #000;
  font-size: 9pt;
  padding-block: 2px;
  padding-inline: 2px;
}

.transkrip-table .center {
  text-align: center;
}

.transkrip-table .left {
  text-align: start;
}

.vertical-text {
  font-size: 9pt;
  font-weight: bold;
  text-align: center;
  text-orientation: mixed;
  white-space: nowrap;
  writing-mode: vertical-rl;
}

.no-inner-border td,
.no-inner-border th {
  border-block-start: none;
  border-inline-end: none;
  border-inline-start: none;
}

.no-inner-border tr:first-child th {
  border-block-start: 1px solid #000;
}

.no-inner-border tr td:first-child,
.no-inner-border tr th:first-child {
  border-inline-start: 1px solid #000;
}

.no-inner-border tr td:last-child,
.no-inner-border tr th:last-child {
  border-inline-end: 1px solid #000;
}

.no-inner-border tr:last-child td {
  border-block-end: 1px solid #000;
}

.transkrip-table th[style*="inline-size: 36px"],
.transkrip-table td:nth-child(1) {
  inline-size: 28px !important;
  max-inline-size: 28px;
}

.transkrip-table th[style*="inline-size: 70px"],
.transkrip-table td:nth-child(2) {
  inline-size: 55px !important;
  max-inline-size: 55px;
}

.transkrip-table th[style*="min-inline-size: 180px"],
.transkrip-table td:nth-child(3) {
  max-inline-size: 180px;
  min-inline-size: 120px !important;
}

.transkrip-table th.vertical-text,
.transkrip-table td:nth-child(4),
.transkrip-table td:nth-child(5),
.transkrip-table td:nth-child(6) {
  inline-size: 30px !important;
  max-inline-size: 30px;
  text-align: center;
}

.transkrip-table .sub-label {
  font-size: 8pt;
  font-style: italic;
  font-weight: normal;
}

.judul-ta {
  font-size: 10pt;
  margin-block-end: 10px;
  text-align: center;
}

.judul-ta .sub-label {
  font-size: 8pt;
  font-style: italic;
}

.ta-ttd-cell {
  background: #fff;
  border-block-end: none !important;
  padding-block: 12px;
  padding-inline: 8px;
  text-align: center;
}

.ttd-row {
  display: flex;
  justify-content: flex-end;
  margin-block-start: 24px;
}

.dekan {
  font-size: 10pt;
  min-inline-size: 220px;
  text-align: end;
}

.ttd-space {
  min-block-size: 40px;
}

.nama {
  font-weight: bold;
  margin-block-start: 6px;
}
</style> 
