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

        <!-- Card 1: Draft Ijazah -->
        <VCard
          flat
          class="form-card position-relative mb-6"
        >
          <VCardTitle class="text-h5 mb-4 d-flex justify-space-between align-center">
            Draft Ijazah
            <VBtn
              icon
              title="Buka di tab baru"
              style="margin-inline-start: auto;"
              @click="openInNewTab"
            >
              <VIcon>ri-external-link-line</VIcon>
            </VBtn>
          </VCardTitle>
          <VCardText style="padding: 0;">
            <div style="inline-size: 100%; overflow-x: auto;">
              <div
                ref="printableContent"
                style="margin: auto; max-inline-size: 1130px; min-inline-size: 1130px; padding-block: 2rem; padding-inline: 0;"
              >
                <div
                  ref="ijazahPreview"
                  class="draft-ijazah-preview"
                  style="box-sizing: border-box; border-radius: 0; margin: auto; background: #fff; font-size: 0.95rem; max-inline-size: 100%; min-block-size: 600px; min-inline-size: 100%; overflow-y: auto; padding-block: 0; padding-inline: 16px;"
                >
                  <!-- Nomor Ijazah Nasional di kanan atas -->
                  <div
                    class="d-flex justify-end"
                    style="inline-size: 100%;"
                  >
                    <div
                      class="text-right"
                      style="max-inline-size: 380px;"
                    >
                      <div
                        class="font-weight-bold"
                        style="font-size: 1rem;"
                      >
                        Nomor Ijazah Nasional:
                      </div>
                      <div style="font-size: 0.85rem; font-style: italic; margin-block-end: 2px;">
                        National Certificate Number
                      </div>
                      <div
                        class="font-weight-bold"
                        style="font-size: 1rem;"
                      >
                        051022592012025100002
                      </div>
                    </div>
                  </div>
                  <!-- Dua kolom utama -->
                  <div class="mt-6">
                    <div class="d-flex mb-2">
                      <div style="flex-shrink: 0; min-inline-size: 270px;">
                        <span class="font-weight-bold">Memberikan Kepada</span><br><span style="font-size: 0.85rem;font-style: italic;">Awarded to</span>
                      </div>
                      <div style="flex: 1; min-inline-size: 0;">
                        : <span class="font-weight-bold">{{ user.name }}<br><span style="font-size: 0.85rem;font-style: italic;">IRWANDA BUDI PANGESTU</span></span>
                      </div>
                    </div>
                    <div class="d-flex mb-2">
                      <div style="flex-shrink: 0; min-inline-size: 270px;">
                        <span class="font-weight-bold">Tempat dan Tanggal Lahir</span><br><span style="font-size: 0.85rem;font-style: italic;">Place and Date of Birth</span>
                      </div>
                      <div style="flex: 1; min-inline-size: 0;">
                        : <span>{{ user.birthPlace }}, {{ user.birthDate }}<br><span style="font-size: 0.85rem;font-style: italic;">ASTRA KSETRA, DECEMBER 17, 2000</span></span>
                      </div>
                    </div>
                    <div class="d-flex mb-2">
                      <div style="flex-shrink: 0; min-inline-size: 270px;">
                        <span class="font-weight-bold">Nomor Induk Kependudukan</span><br><span style="font-size: 0.85rem;font-style: italic;">Identity Number</span>
                      </div>
                      <div style="flex: 1; min-inline-size: 0;">
                        : <div style="display: inline-block; background-color: black; min-block-size: 18px; min-inline-size: 200px; vertical-align: middle;" />
                      </div>
                    </div>
                    <div class="d-flex mb-2">
                      <div style="flex-shrink: 0; min-inline-size: 270px;">
                        <span class="font-weight-bold">N I M</span><br><span style="font-size: 0.85rem;font-style: italic;">Student Number</span>
                      </div>
                      <div style="flex: 1; min-inline-size: 0;">
                        : <span>{{ user.nim }}<br><span style="font-size: 0.85rem;font-style: italic;">1911501007</span></span>
                      </div>
                    </div>
                    <div class="d-flex mb-2">
                      <div style="flex-shrink: 0; min-inline-size: 270px;">
                        <span class="font-weight-bold">Program Studi</span><br><span style="font-size: 0.85rem;font-style: italic;">Study Program</span>
                      </div>
                      <div style="flex: 1; min-inline-size: 0;">
                        : <span>{{ user.studyProgram }}<br><span style="font-size: 0.85rem;font-style: italic;">INFORMATION TECHNOLOGY</span></span>
                      </div>
                    </div>
                    <div class="d-flex mb-2">
                      <div style="flex-shrink: 0; min-inline-size: 270px;">
                        <span class="font-weight-bold">Jenjang Pendidikan</span><br><span style="font-size: 0.85rem;font-style: italic;">Degree</span>
                      </div>
                      <div style="flex: 1; min-inline-size: 0;">
                        : <span>{{ user.degree }}<br><span style="font-size: 0.85rem;font-style: italic;">BACHELOR</span></span>
                      </div>
                    </div>
                    <div class="d-flex mb-2">
                      <div style="flex-shrink: 0; min-inline-size: 270px;">
                        <span class="font-weight-bold">Tanggal Kelulusan</span><br><span style="font-size: 0.85rem;font-style: italic;">Date of Graduation</span>
                      </div>
                      <div style="flex: 1; min-inline-size: 0;">
                        : <span>{{ user.graduationDate }}<br><span style="font-size: 0.85rem;font-style: italic;">MARCH 22, 2025</span></span>
                      </div>
                    </div>
                    <div class="d-flex mb-2">
                      <div style="flex-shrink: 0; min-inline-size: 270px;">
                        <span class="font-weight-bold">Status</span><br><span style="font-size: 0.85rem;font-style: italic;">Status</span>
                      </div>
                      <div style="flex: 1; min-inline-size: 0;">
                        : <span>Akreditasi LAM INFOKOM No. 027/SK/LAM-INFOKOM/Ak/S/III/2024 tanggal 19 Maret 2024 dengan peringkat "Baik Sekali".<br><span style="font-size: 0.85rem;font-style: italic;">Accredited "Very Good" by LAM INFOKOM No. 027/SK/LAM-INFOKOM/Ak/S/III/2024 on March 19, 2024.</span></span>
                      </div>
                    </div>
                  </div>
                  <!-- Penjelasan ijazah -->
                  <div
                    class="mt-4"
                    style="inline-size: 100%;"
                  >
                    <div style="font-size: 1rem;">
                      Ijazah ini diserahkan setelah yang bersangkutan memenuhi semua persyaratan yang ditentukan, dan kepadanya dilimpahkan segala wewenang dan hak yang berhubungan dengan ijazah yang dimilikinya, serta berhak memakai gelar Sarjana Komputer (S.Kom.).<br><span style="font-size: 0.95rem;font-style: italic;">This certificate is awarded in recognition of fulfillment of the requirements for the degree. Therefore, the person has been awarded the degree of Bachelor of Computer Science with all rights and privileges associated with the degree.</span>
                    </div>
                  </div>
                  <!-- Tanda tangan -->
                  <div
                    class="d-flex mt-12 justify-space-between"
                    style="align-items: flex-end; inline-size: 100%;"
                  >
                    <!-- Left: REKTOR -->
                    <div style="display: flex; flex-direction: column;">
                      <div style="flex-grow: 1;" /> <!-- Spacer to align titles -->
                      <div class="mb-8">
                        REKTOR,<br><span style="font-size: 0.95rem;font-style: italic;">RECTOR,</span>
                      </div>
                      <div style="min-block-size: 60px;" />
                      <div class="font-weight-bold">
                        Dr. WARSITI, S.Kp., M.Kep., Sp.Mat.
                      </div>
                    </div>

                    <!-- Right Group: Photo Slot + DEKAN -->
                    <div style="display: flex; align-items: flex-end; gap: 14px;">
                      <!-- Photo Slot -->
                      <div style="border: 2px dashed #888; min-block-size: 180px; min-inline-size: 150px;" />

                      <!-- DEKAN -->
                      <div class="d-flex flex-column">
                        <div style="flex-grow: 1;" /> <!-- Spacer to align titles -->
                        <div class="mb-2">
                          YOGYAKARTA, {{ user.graduationDate }}<br><span style="font-size: 0.95rem;font-style: italic;">YOGYAKARTA, MARCH 22, 2025</span>
                        </div>
                        <div style="min-block-size: 12px;" />
                        <div class="mb-8">
                          DEKAN FAKULTAS SAINS DAN TEKNOLOGI,<br><span style="font-size: 0.95rem;font-style: italic;">DEAN OF FACULTY OF SCIENCE AND TECHNOLOGY,</span>
                        </div>
                        <div style="min-block-size: 60px;" />
                        <div class="font-weight-bold">
                          Ar. TIKA AINUNNISA FITRIA, S.T., M.T., Ph.D
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </VCardText>
        </VCard>

        <!-- Card 2: Transkrip Nilai -->
        <VCard
          flat
          class="form-card position-relative mb-6"
        >
          <VCardTitle class="text-h5 mb-4 d-flex justify-space-between align-center">
            Transkrip Nilai
            <VBtn
              icon
              title="Buka di tab baru"
              style="margin-inline-start: auto;"
              @click="openInNewTab"
            >
              <VIcon>ri-external-link-line</VIcon>
            </VBtn>
          </VCardTitle>
          <VCardText style="padding: 0;">
            <div style="inline-size: 100%; overflow-x: auto;">
              <div
                ref="printableContent"
                style="margin: auto; max-inline-size: 1130px; min-inline-size: 1130px; padding-block: 2rem; padding-inline: 0;"
              >
                <div
                  ref="transcriptPreview"
                  class="transcript-preview mt-8"
                  style="box-sizing: border-box; border-radius: 0; margin: auto; background: #fff; font-size: 0.95rem; max-inline-size: 100%; min-block-size: 600px; min-inline-size: 100%; overflow-y: auto; padding-block: 0; padding-inline: 16px;"
                >
                  <!-- Summary of Grades -->
                  <div class="mt-6">
                    <div class="d-flex mb-2">
                      <div style=" flex-shrink: 0;min-inline-size: 270px;">
                        <span class="font-weight-bold">TOTAL SKS</span><br><span style="font-size: 0.85rem;font-style: italic;">TOTAL CREDITS</span>
                      </div>
                      <div style="inline-size: 10px;">
                        :
                      </div>
                      <div style="flex: 1;">
                        <span>146</span>
                      </div>
                    </div>
                    <div class="d-flex mb-2">
                      <div style=" flex-shrink: 0;min-inline-size: 270px;">
                        <span class="font-weight-bold">INDEKS PRESTASI</span><br><span style="font-size: 0.85rem;font-style: italic;">GRADE POINT AVERAGE</span>
                      </div>
                      <div style="inline-size: 10px;">
                        :
                      </div>
                      <div style="flex: 1;">
                        <span>2.94</span>
                      </div>
                    </div>
                    <div class="d-flex mb-2">
                      <div style=" flex-shrink: 0;min-inline-size: 270px;">
                        <span class="font-weight-bold">PREDIKAT KELULUSAN</span><br><span style="font-size: 0.85rem;font-style: italic;">QUALIFICATION</span>
                      </div>
                      <div style="inline-size: 10px;">
                        :
                      </div>
                      <div style="flex: 1;">
                        <span>MEMUASKAN<br><span style="font-size: 0.85rem;font-style: italic;">SATISFACTORY</span></span>
                      </div>
                    </div>
                    <div class="d-flex mb-2">
                      <div style=" flex-shrink: 0;min-inline-size: 270px;">
                        <span class="font-weight-bold">STATUS</span><br><span style="font-size: 0.85rem;font-style: italic;">STATUS</span>
                      </div>
                      <div style="inline-size: 10px;">
                        :
                      </div>
                      <div style="flex: 1;">
                        <span>Akreditasi LAM INFOKOM No. 027/SK/LAM-INFOKOM/Ak/S/III/2024 tanggal 19 Maret 2024 dengan peringkat "Baik Sekali".<br><span style="font-size: 0.85rem;font-style: italic;">Accredited "Very Good" by LAM INFOKOM No. 027/SK/LAM-INFOKOM/Ak/S/III/2024 on March 19, 2024.</span></span>
                      </div>
                    </div>
                  </div>

                  <!-- Tables for Subjects -->
                  <div class="d-flex justify-space-between mt-8">
                    <!-- Column 1 -->
                    <div style="flex: 1; margin-inline-end: 16px;">
                      <VTable class="text-caption fixed-header">
                        <thead>
                          <tr>
                            <th class="no-col">
                              NO<br>NO
                            </th>
                            <th class="kode-col">
                              KODE<br>CODE
                            </th>
                            <th class="matkul-col">
                              MATA KULIAH<br>SUBJECTS
                            </th>
                            <th class="vertical-header sks-col">
                              SKS<br>CREDITS
                            </th>
                            <th class="vertical-header nilai-col">
                              NILAI<br>GRADE
                            </th>
                            <th class="vertical-header ekuivalen-col">
                              EKUIVALEN<br>EQUIVALENT
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>1</td>
                            <td>TIK0050</td>
                            <td>PENGUJIAN PERANGKAT LUNAK<br><span style="font-size: 0.75rem;font-style: italic;">Software Testing</span></td>
                            <td>3</td>
                            <td>A</td>
                            <td>4.0</td>
                          </tr>
                          <tr>
                            <td>2</td>
                            <td>TIK0051</td>
                            <td>REKAYASA WEB<br><span style="font-size: 0.75rem;font-style: italic;">Distributed System</span></td>
                            <td>3</td>
                            <td>C</td>
                            <td>2.0</td>
                          </tr>
                          <tr>
                            <td>3</td>
                            <td>TIK0054</td>
                            <td>INTEGRASI SISTEM<br><span style="font-size: 0.75rem;font-style: italic;">System Integration</span></td>
                            <td>3</td>
                            <td>A-</td>
                            <td>3.75</td>
                          </tr>
                          <tr>
                            <td>4</td>
                            <td>TIK1001</td>
                            <td>KALKULUS<br><span style="font-size: 0.75rem;font-style: italic;">Calculus</span></td>
                            <td>2</td>
                            <td>BC</td>
                            <td>2.5</td>
                          </tr>
                          <tr>
                            <td>5</td>
                            <td>TIK1002</td>
                            <td>LOGIKA INFORMATIKA<br><span style="font-size: 0.75rem;font-style: italic;">Informatic Logic</span></td>
                            <td>2</td>
                            <td>BC</td>
                            <td>2.5</td>
                          </tr>
                          <tr>
                            <td>6</td>
                            <td>TIK1003</td>
                            <td>PENGANTAR TEKNOLOGI INFORMASI<br><span style="font-size: 0.75rem;font-style: italic;">Introduction to Information Technology</span></td>
                            <td>3</td>
                            <td>A</td>
                            <td>4.0</td>
                          </tr>
                          <tr>
                            <td>7</td>
                            <td>TIK1004</td>
                            <td>DASAR PEMROGRAMAN<br><span style="font-size: 0.75rem;font-style: italic;">Basic Programming</span></td>
                            <td>3</td>
                            <td>B-</td>
                            <td>2.75</td>
                          </tr>
                          <tr>
                            <td>8</td>
                            <td>TIK1005</td>
                            <td>BASIS DATA<br><span style="font-size: 0.75rem;font-style: italic;">Database</span></td>
                            <td>3</td>
                            <td>B-</td>
                            <td>2.75</td>
                          </tr>
                          <tr>
                            <td>9</td>
                            <td>TIK2006</td>
                            <td>MATEMATIKA DISKRIT<br><span style="font-size: 0.75rem;font-style: italic;">Discrete Mathematics</span></td>
                            <td>2</td>
                            <td>B+</td>
                            <td>3.25</td>
                          </tr>
                          <tr>
                            <td>10</td>
                            <td>TIK2007</td>
                            <td>ALGORITMA PEMROGRAMAN<br><span style="font-size: 0.75rem;font-style: italic;">Programming Algorithm</span></td>
                            <td>3</td>
                            <td>A</td>
                            <td>4.0</td>
                          </tr>
                          <tr>
                            <td>11</td>
                            <td>TIK2008</td>
                            <td>STATISTIK DAN PROBABILITAS<br><span style="font-size: 0.75rem;font-style: italic;">Statistics and Probability</span></td>
                            <td>2</td>
                            <td>B+</td>
                            <td>3.25</td>
                          </tr>
                          <tr>
                            <td>12</td>
                            <td>TIK2009</td>
                            <td>ORGANISASI DAN ARSITEKTUR KOMPUTER<br><span style="font-size: 0.75rem;font-style: italic;">Computer Organization and Architecture</span></td>
                            <td>3</td>
                            <td>A</td>
                            <td>4.0</td>
                          </tr>
                          <tr>
                            <td>13</td>
                            <td>TIK2010</td>
                            <td>JARINGAN KOMPUTER<br><span style="font-size: 0.75rem;font-style: italic;">Computer Network</span></td>
                            <td>3</td>
                            <td>B</td>
                            <td>3.0</td>
                          </tr>
                          <tr>
                            <td>14</td>
                            <td>TIK2011</td>
                            <td>PEMROGRAMAN BERORIENTASI OBJEK<br><span style="font-size: 0.75rem;font-style: italic;">Object Oriented Programming</span></td>
                            <td>3</td>
                            <td>A</td>
                            <td>4.0</td>
                          </tr>
                          <tr>
                            <td>15</td>
                            <td>TIK3012</td>
                            <td>PEMROGRAMAN WEB<br><span style="font-size: 0.75rem;font-style: italic;">Web Programming</span></td>
                            <td>3</td>
                            <td>A</td>
                            <td>4.0</td>
                          </tr>
                          <tr>
                            <td>16</td>
                            <td>TIK3013</td>
                            <td>STRUKTUR DATA<br><span style="font-size: 0.75rem;font-style: italic;">Data Structure</span></td>
                            <td>3</td>
                            <td>A</td>
                            <td>4.0</td>
                          </tr>
                          <tr>
                            <td>17</td>
                            <td>TIK3014</td>
                            <td>SISTEM OPERASI<br><span style="font-size: 0.75rem;font-style: italic;">Operating System</span></td>
                            <td>3</td>
                            <td>A-</td>
                            <td>3.75</td>
                          </tr>
                          <tr>
                            <td>18</td>
                            <td>TIK3015</td>
                            <td>ANALISIS DAN PERANCANGAN SISTEM INFORMASI<br><span style="font-size: 0.75rem;font-style: italic;">Information System Analysis and Design</span></td>
                            <td>3</td>
                            <td>B+</td>
                            <td>3.25</td>
                          </tr>
                          <tr>
                            <td>19</td>
                            <td>TIK3016</td>
                            <td>GRAFIKA KOMPUTER<br><span style="font-size: 0.75rem;font-style: italic;">Computer Graphics</span></td>
                            <td>2</td>
                            <td>B</td>
                            <td>3.0</td>
                          </tr>
                          <tr>
                            <td>20</td>
                            <td>TIK3017</td>
                            <td>SISTEM PAKAR<br><span style="font-size: 0.75rem;font-style: italic;">Expert System</span></td>
                            <td>2</td>
                            <td>B-</td>
                            <td>2.75</td>
                          </tr>
                          <tr>
                            <td>21</td>
                            <td>TIK3018</td>
                            <td>PEMROGRAMAN PERANGKAT BERGERAK<br><span style="font-size: 0.75rem;font-style: italic;">Mobile Device Programming</span></td>
                            <td>3</td>
                            <td>A-</td>
                            <td>3.75</td>
                          </tr>
                          <tr>
                            <td>22</td>
                            <td>TIK3019</td>
                            <td>KEAMANAN INFORMASI<br><span style="font-size: 0.75rem;font-style: italic;">Information Security</span></td>
                            <td>3</td>
                            <td>B+</td>
                            <td>3.25</td>
                          </tr>
                          <tr>
                            <td>23</td>
                            <td>TIK4020</td>
                            <td>REKAYASA PERANGKAT LUNAK<br><span style="font-size: 0.75rem;font-style: italic;">Software Engineering</span></td>
                            <td>3</td>
                            <td>B</td>
                            <td>3.0</td>
                          </tr>
                          <tr>
                            <td>24</td>
                            <td>TIK4021</td>
                            <td>PEMROGRAMAN VISUAL<br><span style="font-size: 0.75rem;font-style: italic;">Visual Programming</span></td>
                            <td>3</td>
                            <td>A</td>
                            <td>4.0</td>
                          </tr>
                          <tr>
                            <td>25</td>
                            <td>TIK4022</td>
                            <td>DATA MINING<br><span style="font-size: 0.75rem;font-style: italic;">Data Mining</span></td>
                            <td>2</td>
                            <td>B</td>
                            <td>3.0</td>
                          </tr>
                          <tr>
                            <td>26</td>
                            <td>TIK4023</td>
                            <td>PEMROGRAMAN JARINGAN<br><span style="font-size: 0.75rem;font-style: italic;">Network Programming</span></td>
                            <td>3</td>
                            <td>A-</td>
                            <td>3.75</td>
                          </tr>
                          <tr>
                            <td>27</td>
                            <td>TIK4024</td>
                            <td>KECERDASAN BUATAN<br><span style="font-size: 0.75rem;font-style: italic;">Artificial Intelligence</span></td>
                            <td>3</td>
                            <td>A</td>
                            <td>4.0</td>
                          </tr>
                        </tbody>
                      </VTable>
                    </div>

                    <!-- Column 2 -->
                    <div style="flex: 1; margin-inline-end: 16px;">
                      <VTable class="text-caption fixed-header">
                        <thead>
                          <tr>
                            <th class="no-col">
                              NO<br>NO
                            </th>
                            <th class="kode-col">
                              KODE<br>CODE
                            </th>
                            <th class="matkul-col">
                              MATA KULIAH<br>SUBJECTS
                            </th>
                            <th class="vertical-header sks-col">
                              SKS<br>CREDITS
                            </th>
                            <th class="vertical-header nilai-col">
                              NILAI<br>GRADE
                            </th>
                            <th class="vertical-header ekuivalen-col">
                              EKUIVALEN<br>EQUIVALENT
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>28</td>
                            <td>TIK4025</td>
                            <td>ARSITEKTUR ENTERPRISE<br><span style="font-size: 0.75rem;font-style: italic;">Enterprise Architecture</span></td>
                            <td>2</td>
                            <td>C</td>
                            <td>2.0</td>
                          </tr>
                          <tr>
                            <td>29</td>
                            <td>TIK5026</td>
                            <td>TEORI BAHASA DAN OTOMATA<br><span style="font-size: 0.75rem;font-style: italic;">Theory of Formal Language and Automata</span></td>
                            <td>3</td>
                            <td>B-</td>
                            <td>2.75</td>
                          </tr>
                          <tr>
                            <td>30</td>
                            <td>TIK5027</td>
                            <td>MANAJEMEN PROYEK TEKNOLOGI INFORMASI<br><span style="font-size: 0.75rem;font-style: italic;">Information Technology Project Management</span></td>
                            <td>3</td>
                            <td>B-</td>
                            <td>2.75</td>
                          </tr>
                          <tr>
                            <td>31</td>
                            <td>TIK5028</td>
                            <td>SISTEM INFORMASI GEOGRAFIS<br><span style="font-size: 0.75rem;font-style: italic;">Geographic Information System</span></td>
                            <td>3</td>
                            <td>BC</td>
                            <td>2.5</td>
                          </tr>
                          <tr>
                            <td>32</td>
                            <td>TIK5029</td>
                            <td>DESAIN DAN ANALISIS ALGORITMA<br><span style="font-size: 0.75rem;font-style: italic;">Algorithm Design and Analysis</span></td>
                            <td>3</td>
                            <td>B-</td>
                            <td>2.75</td>
                          </tr>
                          <tr>
                            <td>33</td>
                            <td>TIK5030</td>
                            <td>SISTEM INFORMASI RUMAH SAKIT<br><span style="font-size: 0.75rem;font-style: italic;">Hospital Information System</span></td>
                            <td>2</td>
                            <td>BC</td>
                            <td>2.5</td>
                          </tr>
                          <tr>
                            <td>34</td>
                            <td>TIK5031</td>
                            <td>SISTEM PENDUKUNG KEPUTUSAN<br><span style="font-size: 0.75rem;font-style: italic;">Decision Support System</span></td>
                            <td>2</td>
                            <td>C</td>
                            <td>2.0</td>
                          </tr>
                          <tr>
                            <td>35</td>
                            <td>TIK5032</td>
                            <td>PEMROGRAMAN SISTEM DAN JARINGAN<br><span style="font-size: 0.75rem;font-style: italic;">System and Network Programming</span></td>
                            <td>3</td>
                            <td>BC</td>
                            <td>2.5</td>
                          </tr>
                          <tr>
                            <td>36</td>
                            <td>TIK6033</td>
                            <td>KERJA PRAKTEK<br><span style="font-size: 0.75rem;font-style: italic;">Internship</span></td>
                            <td>3</td>
                            <td>C</td>
                            <td>2.0</td>
                          </tr>
                          <tr>
                            <td>37</td>
                            <td>TIK6034</td>
                            <td>TREND DALAM TEKNOLOGI INFORMASI<br><span style="font-size: 0.75rem;font-style: italic;">Trends in Information Technology</span></td>
                            <td>2</td>
                            <td>C-</td>
                            <td>2.25</td>
                          </tr>
                          <tr>
                            <td>38</td>
                            <td>TIK6035</td>
                            <td>SISTEM PAKAR<br><span style="font-size: 0.75rem;font-style: italic;">Expert System</span></td>
                            <td>3</td>
                            <td>C+</td>
                            <td>2.25</td>
                          </tr>
                          <tr>
                            <td>39</td>
                            <td>TIK6036</td>
                            <td>TEKNOLOGI INFORMASI KESEHATAN<br><span style="font-size: 0.75rem;font-style: italic;">Health Information Technology</span></td>
                            <td>3</td>
                            <td>A</td>
                            <td>4.0</td>
                          </tr>
                          <tr>
                            <td>40</td>
                            <td>TIK6037</td>
                            <td>MANAJEMEN PROYEK SISTEM INFORMASI<br><span style="font-size: 0.75rem;font-style: italic;">Information System Project Management</span></td>
                            <td>3</td>
                            <td>B+</td>
                            <td>3.25</td>
                          </tr>
                          <tr>
                            <td>41</td>
                            <td>TIK6038</td>
                            <td>E-BUSINESS<br><span style="font-size: 0.75rem;font-style: italic;">E-Business</span></td>
                            <td>2</td>
                            <td>C</td>
                            <td>2.0</td>
                          </tr>
                          <tr>
                            <td>42</td>
                            <td>TIK6039</td>
                            <td>PERANCANGAN DAN IMPLEMENTASI DATABASE<br><span style="font-size: 0.75rem;font-style: italic;">Database Design and Implementation</span></td>
                            <td>3</td>
                            <td>B</td>
                            <td>3.0</td>
                          </tr>
                          <tr>
                            <td>43</td>
                            <td>TIK6040</td>
                            <td>PEMROGRAMAN FRAMEWORK<br><span style="font-size: 0.75rem;font-style: italic;">Framework Programming</span></td>
                            <td>3</td>
                            <td>A-</td>
                            <td>3.75</td>
                          </tr>
                          <tr>
                            <td>44</td>
                            <td>TIK6041</td>
                            <td>PEMROGRAMAN CLIENT-SERVER<br><span style="font-size: 0.75rem;font-style: italic;">Client-Server Programming</span></td>
                            <td>3</td>
                            <td>B+</td>
                            <td>3.25</td>
                          </tr>
                          <tr>
                            <td>45</td>
                            <td>TIK6042</td>
                            <td>AUDIT SISTEM INFORMASI<br><span style="font-size: 0.75rem;font-style: italic;">Information System Audit</span></td>
                            <td>2</td>
                            <td>B</td>
                            <td>3.0</td>
                          </tr>
                          <tr>
                            <td>46</td>
                            <td>TIK6043</td>
                            <td>CLOUD COMPUTING<br><span style="font-size: 0.75rem;font-style: italic;">Cloud Computing</span></td>
                            <td>2</td>
                            <td>C+</td>
                            <td>2.25</td>
                          </tr>
                          <tr>
                            <td>47</td>
                            <td>TIK6044</td>
                            <td>SISTEM INFORMASI MANAJEMEN<br><span style="font-size: 0.75rem;font-style: italic;">Management Information System</span></td>
                            <td>3</td>
                            <td>B</td>
                            <td>3.0</td>
                          </tr>
                          <tr>
                            <td>48</td>
                            <td>TIK6045</td>
                            <td>DATA WAREHOUSE DAN BUSINESS INTELLIGENCE<br><span style="font-size: 0.75rem;font-style: italic;">Data Warehouse and Business Intelligence</span></td>
                            <td>3</td>
                            <td>B+</td>
                            <td>3.25</td>
                          </tr>
                          <tr>
                            <td>49</td>
                            <td>TIK6046</td>
                            <td>BIG DATA ANALYTICS<br><span style="font-size: 0.75rem;font-style: italic;">Big Data Analytics</span></td>
                            <td>3</td>
                            <td>A-</td>
                            <td>3.75</td>
                          </tr>
                          <tr>
                            <td>50</td>
                            <td>TIK6047</td>
                            <td>PEMROGRAMAN GAME<br><span style="font-size: 0.75rem;font-style: italic;">Game Programming</span></td>
                            <td>2</td>
                            <td>B</td>
                            <td>3.0</td>
                          </tr>
                          <tr>
                            <td>51</td>
                            <td>TIK6048</td>
                            <td>ARTIFICIAL INTELLIGENCE<br><span style="font-size: 0.75rem;font-style: italic;">Artificial Intelligence</span></td>
                            <td>3</td>
                            <td>A</td>
                            <td>4.0</td>
                          </tr>
                          <tr>
                            <td>52</td>
                            <td>TIK6049</td>
                            <td>COMPUTER VISION<br><span style="font-size: 0.75rem;font-style: italic;">Computer Vision</span></td>
                            <td>3</td>
                            <td>A-</td>
                            <td>3.75</td>
                          </tr>
                          <tr>
                            <td>53</td>
                            <td>TIK6050</td>
                            <td>MACHINE LEARNING<br><span style="font-size: 0.75rem;font-style: italic;">Machine Learning</span></td>
                            <td>3</td>
                            <td>A</td>
                            <td>4.0</td>
                          </tr>
                          <tr>
                            <td>54</td>
                            <td>TIK6051</td>
                            <td>NATURAL LANGUAGE PROCESSING<br><span style="font-size: 0.75rem;font-style: italic;">Natural Language Processing</span></td>
                            <td>3</td>
                            <td>A-</td>
                            <td>3.75</td>
                          </tr>
                        </tbody>
                      </VTable>
                    </div>

                    <!-- Column 3 -->
                    <div style="flex: 1;">
                      <VTable class="text-caption fixed-header">
                        <thead>
                          <tr>
                            <th class="no-col">
                              NO<br>NO
                            </th>
                            <th class="kode-col">
                              KODE<br>CODE
                            </th>
                            <th class="matkul-col">
                              MATA KULIAH<br>SUBJECTS
                            </th>
                            <th class="vertical-header sks-col">
                              SKS<br>CREDITS
                            </th>
                            <th class="vertical-header nilai-col">
                              NILAI<br>GRADE
                            </th>
                            <th class="vertical-header ekuivalen-col">
                              EKUIVALEN<br>EQUIVALENT
                            </th>
                          </tr>
                        </thead>
                        <tbody>
                          <tr>
                            <td>55</td>
                            <td>UNI0009</td>
                            <td>ENGLISH FOR ACADEMIC READING<br><span style="font-size: 0.75rem;font-style: italic;">English for Academic Reading</span></td>
                            <td>1</td>
                            <td>AB</td>
                            <td>3.5</td>
                          </tr>
                          <tr>
                            <td>56</td>
                            <td>UNI0010</td>
                            <td>ENGLISH FOR ACADEMIC WRITING<br><span style="font-size: 0.75rem;font-style: italic;">English for Academic Writing</span></td>
                            <td>1</td>
                            <td>A</td>
                            <td>4.0</td>
                          </tr>
                          <tr>
                            <td>57</td>
                            <td>UNI0011</td>
                            <td>KEWIRAUSAHAAN<br><span style="font-size: 0.75rem;font-style: italic;">Entrepreneurship</span></td>
                            <td>3</td>
                            <td>A</td>
                            <td>4.0</td>
                          </tr>
                          <tr>
                            <td>58</td>
                            <td>UNI0012</td>
                            <td>KULIAH KERJA NYATA (KKN)<br><span style="font-size: 0.75rem;font-style: italic;">Community Service</span></td>
                            <td>3</td>
                            <td>A</td>
                            <td>4.0</td>
                          </tr>
                          <tr>
                            <td>59</td>
                            <td>UNI0013</td>
                            <td>SKRIPSI<br><span style="font-size: 0.75rem;font-style: italic;">Thesis</span></td>
                            <td>6</td>
                            <td>C</td>
                            <td>2.0</td>
                          </tr>
                        </tbody>
                      </VTable>
                    </div>
                  </div>

                  <!-- Thesis Title and Registration Information -->
                  <div class="mt-8 text-center">
                    <div class="font-weight-bold">
                      JUDUL TUGAS AKHIR<br><span style="font-size: 0.85rem;font-style: italic;">THESIS TITLE</span>
                    </div>
                    <div class="mt-2">
                      SISTEM INFORMASI PENDAFTARAN PADA KLINIK FISIOTERAPI<br>
                      UNIVERSITAS AISYIYAH YOGYAKARTA MENGGUNAKAN METODE RAD<br>
                      <span style="font-size: 0.85rem;font-style: italic;">(RAPID APPLICATION DEVELOPMENT)</span><br>
                      REGISTRATION INFORMATION SYSTEM AT AISYIYAH UNIVERSITY PHYSIOTHERAPY CLINIC<br>
                      YOGYAKARTA USING RAD METHOD (RAPID APPLICATION DEVELOPMENT)
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </VCardText>
        </VCard>

        <!-- Card 3: Komentar -->
        <VCard
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
              <template
                v-for="comment in comments"
                :key="comment.id"
              >
                <!-- Parent Comment -->
                <VCard
                  flat
                  class="mb-4 comment-card"
                >
                  <VCardText>
                    <div class="d-flex align-center mb-3 comment-header">
                      <VAvatar
                        size="36"
                        color="primary"
                        class="avatar-mr"
                      >
                        <span class="white--text">{{ comment.user.charAt(0) }}</span>
                      </VAvatar>
                      <div>
                        <div class="font-weight-bold">
                          {{ comment.user }}
                        </div>
                        <div class="text-caption grey--text">
                          {{ formatDate(comment.date) }}
                        </div>
                      </div>
                    </div>
                    <div class="comment-text mb-3">
                      {{ comment.text }}
                    </div>
                    <VBtn
                      text
                      small
                      color="primary"
                      class="reply-btn-mr"
                      @click="showReplyForm(comment.id)"
                    >
                      <VIcon
                        small
                        class="reply-icon-mr"
                      >
                        ri-reply-line
                      </VIcon>
                      Balas
                    </VBtn>
                  </VCardText>

                  <!-- Reply Form -->
                  <VCardText
                    v-if="activeReplyForm === comment.id"
                    class="pt-0 pb-0"
                  >
                    <VTextField
                      v-model="newReply"
                      label="Tulis balasan..."
                      outlined
                      dense
                      hide-details
                      class="mb-3"
                      @keyup.enter="addReply(comment.id)"
                    />
                    <div class="d-flex">
                      <VBtn
                        color="primary"
                        size="small"
                        class="reply-btn-mr"
                        @click="addReply(comment.id)"
                      >
                        Kirim Balasan
                      </VBtn>
                      <VBtn
                        text
                        size="small"
                        @click="cancelReply"
                      >
                        Batal
                      </VBtn>
                    </div>
                  </VCardText>

                  <!-- Replies -->
                  <VCardText
                    v-if="comment.replies && comment.replies.length > 0"
                    class="pt-0"
                  >
                    <div
                      v-for="reply in comment.replies"
                      :key="reply.id"
                      class="nested-reply-container mb-4"
                    >
                      <VCard
                        flat
                        class="reply-card"
                      >
                        <VCardText>
                          <div class="d-flex align-center mb-3 comment-header">
                            <VAvatar
                              size="28"
                              color="primary"
                              class="avatar-mr"
                            >
                              <span class="white--text">{{ reply.user.charAt(0) }}</span>
                            </VAvatar>
                            <div>
                              <div class="font-weight-bold">
                                {{ reply.user }}
                              </div>
                              <div class="text-caption grey--text">
                                {{ formatDate(reply.date) }}
                              </div>
                            </div>
                          </div>
                          <div class="reply-text mb-3">
                            {{ reply.text }}
                          </div>
                          
                          <!-- Reply Actions -->
                          <div class="d-flex align-center">
                            <VBtn
                              text
                              x-small
                              color="primary"
                              class="reply-btn-mr"
                              @click="showReplyForm(reply.id)"
                            >
                              <VIcon
                                x-small
                                class="reply-icon-mr"
                              >
                                ri-reply-line
                              </VIcon>
                              Balas
                            </VBtn>
                          </div>
                        </VCardText>

                        <!-- Nested Reply Form -->
                        <VCardText
                          v-if="activeReplyForm === reply.id"
                          class="pt-0 pb-0"
                        >
                          <VTextField
                            v-model="newReply"
                            label="Tulis balasan..."
                            outlined
                            dense
                            hide-details
                            class="mb-3"
                            @keyup.enter="addNestedReply(comment.id, reply.id)"
                          />
                          <div class="d-flex">
                            <VBtn
                              color="primary"
                              size="small"
                              class="reply-btn-mr"
                              @click="addNestedReply(comment.id, reply.id)"
                            >
                              Kirim Balasan
                            </VBtn>
                            <VBtn
                              text
                              size="small"
                              @click="cancelReply"
                            >
                              Batal
                            </VBtn>
                          </div>
                        </VCardText>

                        <!-- Nested Replies -->
                        <div
                          v-if="reply.replies && reply.replies.length > 0"
                          class="nested-reply-container"
                        >
                          <VCard
                            v-for="nestedReply in reply.replies"
                            :key="nestedReply.id"
                            flat
                            class="reply-card"
                          >
                            <VCardText>
                              <div class="d-flex align-center mb-3 comment-header">
                                <VAvatar
                                  size="24"
                                  color="primary"
                                  class="avatar-mr"
                                >
                                  <span class="white--text">{{ nestedReply.user.charAt(0) }}</span>
                                </VAvatar>
                                <div>
                                  <div class="font-weight-bold">
                                    {{ nestedReply.user }}
                                  </div>
                                  <div class="text-caption grey--text">
                                    {{ formatDate(nestedReply.date) }}
                                  </div>
                                </div>
                              </div>
                              <div class="reply-text mb-3">
                                {{ nestedReply.text }}
                              </div>
                              
                              <!-- Nested Reply Actions -->
                              <div class="d-flex align-center">
                                <VBtn
                                  text
                                  x-small
                                  color="primary"
                                  class="reply-btn-mr"
                                  @click="showReplyForm(nestedReply.id)"
                                >
                                  <VIcon
                                    x-small
                                    class="reply-icon-mr"
                                  >
                                    ri-reply-line
                                  </VIcon>
                                  Balas
                                </VBtn>
                              </div>
                            </VCardText>

                            <!-- Deep Nested Reply Form -->
                            <VCardText
                              v-if="activeReplyForm === nestedReply.id"
                              class="pt-0 pb-0"
                            >
                              <VTextField
                                v-model="newReply"
                                label="Tulis balasan..."
                                outlined
                                dense
                                hide-details
                                class="mb-3"
                                @keyup.enter="addNestedReply(comment.id, nestedReply.id)"
                              />
                              <div class="d-flex">
                                <VBtn
                                  color="primary"
                                  size="small"
                                  class="reply-btn-mr"
                                  @click="addNestedReply(comment.id, nestedReply.id)"
                                >
                                  Kirim Balasan
                                </VBtn>
                                <VBtn
                                  text
                                  size="small"
                                  @click="cancelReply"
                                >
                                  Batal
                                </VBtn>
                              </div>
                            </VCardText>

                            <!-- Deep Nested Replies -->
                            <div
                              v-if="nestedReply.replies && nestedReply.replies.length > 0"
                              class="nested-reply-container"
                            >
                              <VCard
                                v-for="deepReply in nestedReply.replies"
                                :key="deepReply.id"
                                flat
                                class="reply-card"
                              >
                                <VCardText>
                                  <div class="d-flex align-center mb-3 comment-header">
                                    <VAvatar
                                      size="24"
                                      color="primary"
                                      class="avatar-mr"
                                    >
                                      <span class="white--text">{{ deepReply.user.charAt(0) }}</span>
                                    </VAvatar>
                                    <div>
                                      <div class="font-weight-bold">
                                        {{ deepReply.user }}
                                      </div>
                                      <div class="text-caption grey--text">
                                        {{ formatDate(deepReply.date) }}
                                      </div>
                                    </div>
                                  </div>
                                  <div class="reply-text">
                                    {{ deepReply.text }}
                                  </div>
                                </VCardText>
                              </VCard>
                            </div>
                          </VCard>
                        </div>
                      </VCard>
                    </div>
                  </VCardText>
                </VCard>
              </template>
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
      </VContainer>
    </VMain>
  </VApp>
</template>

<script>
import { ref } from 'vue'
import { useRoute } from 'vue-router'

export default {
  name: 'ValidasiIjazah',
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

    // State untuk komentar
    const comments = ref([
      {
        id: 1,
        user: 'Admin',
        text: 'Mohon lengkapi data diri Anda dengan benar.',
        date: new Date('2024-03-20T10:00:00'),
        replies: [
          {
            id: 2,
            user: 'User',
            text: 'Baik, saya akan melengkapi data diri.',
            date: new Date('2024-03-20T10:30:00'),
            replies: [
              {
                id: 3,
                user: 'Admin',
                text: 'Terima kasih atas konfirmasinya.',
                date: new Date('2024-03-20T11:00:00'),
                replies: [
                  {
                    id: 4,
                    user: 'User',
                    text: 'Sama-sama, terima kasih atas bantuannya.',
                    date: new Date('2024-03-20T11:30:00'),
                  },
                ],
              },
            ],
          },
        ],
      },
    ])

    const newComment = ref('')
    const newReply = ref('')
    const activeReplyForm = ref(null)

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

    // Fungsi untuk komentar
    const addComment = () => {
      if (!newComment.value.trim()) return

      comments.value.unshift({
        id: Date.now(),
        user: 'User',
        text: newComment.value,
        date: new Date(),
        replies: [],
      })

      newComment.value = ''
    }

    const showReplyForm = commentId => {
      activeReplyForm.value = commentId
      newReply.value = ''
    }

    const cancelReply = () => {
      activeReplyForm.value = null
      newReply.value = ''
    }

    const addReply = commentId => {
      if (!newReply.value.trim()) return

      const comment = comments.value.find(c => c.id === commentId)
      if (comment) {
        if (!comment.replies) comment.replies = []
        comment.replies.push({
          id: Date.now(),
          user: 'User',
          text: newReply.value,
          date: new Date(),
        })
      }

      newReply.value = ''
      activeReplyForm.value = null
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

    const addNestedReply = (commentId, parentReplyId) => {
      if (!newReply.value.trim()) return

      const comment = comments.value.find(c => c.id === commentId)
      if (!comment) return

      const newReplyData = {
        id: Date.now(),
        user: 'User',
        text: newReply.value,
        date: new Date(),
        replies: [],
      }

      // Cek apakah parentReplyId adalah ID komentar utama
      if (commentId === parentReplyId) {
        if (!comment.replies) comment.replies = []
        comment.replies.push(newReplyData)
      } else {
        // Cari dan tambahkan ke balasan berjenjang
        if (reply.replies && findReplyAndAddNested(reply.replies, parentReplyId, newReplyData)) {
          return true
        }
      }

      newReply.value = ''
      activeReplyForm.value = null
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

    return {
      form,
      fileStatus,
      updateFileStatus,
      comments,
      newComment,
      newReply,
      activeReplyForm,
      addComment,
      showReplyForm,
      cancelReply,
      addReply,
      addNestedReply,
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
  max-inline-size: 900px;
  min-block-size: 400px;
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
</style> 
