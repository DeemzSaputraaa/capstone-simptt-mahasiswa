<template>
  <VApp>
    <VMain>
      <VContainer
        fluid
        class="pa-6"
      >
        <VCard
          flat
          class="form-card"
        >
          <VCardTitle class="text-h5 mb-4">
            Formulir Validasi Ijazah
          </VCardTitle>
          
          <!-- Section 1: NIK -->
          <VCardText>
            <div class="text-subtitle-1 font-weight-bold mb-2">
              Masukkan NIK
            </div>
            <VTextField
              v-model="form.nik"
              label="NIK"
              outlined
              dense
              hide-details
              class="mb-4"
            />
            
            <!-- Section 2: Nomor Telepon -->
            <div class="text-subtitle-1 font-weight-bold mb-2">
              Masukkan No Telepon
            </div>
            <VTextField
              v-model="form.phone"
              label="No Telepon"
              outlined
              dense
              hide-details
              class="mb-4"
            />
            
            <!-- Section 3: Upload Foto Ijazah -->
            <div class="text-subtitle-1 font-weight-bold mb-2 mt-6">
              Upload Foto Ijazah
            </div>
            <div class="file-upload mb-4 mt-6">
              <VIcon class="file-upload-icon">
                ri-attachment-2
              </VIcon>
              <VFileInput
                v-model="form.photoIjazah"
                accept="image/*"
                variant="outlined"
                hide-details
                placeholder="Upload Foto Ijazah"
                prepend-icon=""
                class="file-upload-input"
              />
            </div>
            <div class="text-caption grey--text">
              {{ fileStatus.photoIjazah || 'No selected file' }}
            </div>
            
            <!-- Section 4: Upload Foto KTP -->
            <div class="text-subtitle-1 font-weight-bold mb-2 mt-6">
              Upload Foto KTP
            </div>
            <div class="file-upload mb-4 mt-6">
              <VIcon class="file-upload-icon">
                ri-attachment-2
              </VIcon>
              <VFileInput
                v-model="form.photoCtp"
                accept="image/*"
                variant="outlined"
                hide-details
                placeholder="Upload Foto KTP"
                prepend-icon=""
                class="file-upload-input"
              />
            </div>
            <div class="text-caption grey--text">
              {{ fileStatus.photoCtp || 'No selected file' }}
            </div>

            <!-- Submit Button -->
            <VCardActions>
              <VSpacer />
              <VBtn
                block
                type="submit"
                color="#17a2a6"
                style="border-radius: 10px; background: rgb(var(--v-theme-primary)); color: #fff; font-size: 1.1rem; font-weight: 500; min-block-size: 48px;"
                @click="handleSubmit"
              >
                Submit
              </VBtn>
            </VCardActions>

            <!-- Section 5: Komentar -->
            <div class="text-subtitle-1 font-weight-bold mb-4 mt-8">
              Komentar
            </div>
            
            <!-- Form Komentar Baru -->
            <VCard
              flat
              class="mb-6 comment-form-card"
            >
              <VCardText>
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
                  @click="addComment"
                >
                  Kirim Komentar
                </VBtn>
              </VCardText>
            </VCard>

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
      </VContainer>
    </VMain>
  </VApp>

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
</template>

<script>
import { ref } from 'vue'

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
</style> 
