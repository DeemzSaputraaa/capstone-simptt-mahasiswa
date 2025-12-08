<template>
  <VCard
    flat
    class="comment-card"
    :class="{ 'nested-comment': isNested }"
  >
    <VCardText>
      <div class="d-flex align-center mb-3 comment-header">
        <VAvatar
          :size="avatarSize"
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
      
      <!-- Reply Button -->
      <VBtn
        text
        :size="buttonSize"
        color="primary"
        class="reply-btn-mr"
        @click="showReplyForm"
      >
        <VIcon
          :size="iconSize"
          class="reply-icon-mr"
        >
          ri-reply-line
        </VIcon>
        Balas
      </VBtn>
    </VCardText>

    <!-- Reply Form -->
    <VCardText
      v-if="showReply"
      class="pt-0 pb-0"
    >
      <VTextField
        v-model="newReplyText"
        label="Tulis balasan..."
        outlined
        dense
        hide-details
        class="mb-3"
        @keyup.enter="submitReply"
      />
      <div class="d-flex">
        <VBtn
          color="primary"
          size="small"
          class="reply-btn-mr"
          @click="submitReply"
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
      v-if="comment.replies && comment.replies.length > 0"
      class="nested-reply-container"
    >
      <CommentItem
        v-for="reply in comment.replies"
        :key="reply.id"
        :comment="reply"
        :depth="depth + 1"
        :max-depth="maxDepth"
        :current-user="currentUser"
        @add-reply="handleAddReply"
      />
    </div>
  </VCard>
</template>

<script>
import { computed, ref } from 'vue'

export default {
  name: 'CommentItem',
  props: {
    comment: {
      type: Object,
      required: true,
    },
    depth: {
      type: Number,
      default: 0,
    },
    maxDepth: {
      type: Number,
      default: 10, // Batas maksimal depth, bisa disesuaikan
    },
    currentUser: {
      type: Object,
      default: () => ({ name: 'Mahasiswa' }),
    },
  },
  emits: ['add-reply'],
  setup(props, { emit }) {
    const showReply = ref(false)
    const newReplyText = ref('')

    // Hitung ukuran komponen berdasarkan depth
    const avatarSize = computed(() => {
      const baseSize = 36
      const sizeReduction = Math.min(props.depth * 4, 20) // Max reduction 20px
      
      return Math.max(baseSize - sizeReduction, 16) // Min size 16px
    })

    const buttonSize = computed(() => {
      return props.depth > 2 ? 'x-small' : 'small'
    })

    const iconSize = computed(() => {
      return props.depth > 2 ? 'x-small' : 'small'
    })

    const isNested = computed(() => props.depth > 0)

    const formatDate = date => {
      return new Intl.DateTimeFormat('id-ID', {
        dateStyle: 'medium',
        timeStyle: 'short',
      }).format(new Date(date))
    }

    const showReplyForm = () => {
      if (props.depth < props.maxDepth) {
        showReply.value = true
        newReplyText.value = ''
      }
    }

    const cancelReply = () => {
      showReply.value = false
      newReplyText.value = ''
    }

    const submitReply = () => {
      if (!newReplyText.value.trim()) return

      const replyData = {
        id: Date.now(),
        user: props.currentUser?.name || 'Mahasiswa',
        text: newReplyText.value,
        date: new Date(),
        replies: [],
      }

      emit('add-reply', props.comment.id, replyData)
      
      newReplyText.value = ''
      showReply.value = false
    }

    const handleAddReply = (parentId, replyData) => {
      emit('add-reply', parentId, replyData)
    }

    return {
      showReply,
      newReplyText,
      avatarSize,
      buttonSize,
      iconSize,
      isNested,
      formatDate,
      showReplyForm,
      cancelReply,
      submitReply,
      handleAddReply,
    }
  },
}
</script>

<style scoped>
.comment-card {
  border: 1px solid rgba(0, 0, 0, 12%);
  border-radius: 8px;
  background-color: var(--v-theme-surface);
  margin-block-end: 16px;
}

.nested-comment {
  border-inline-start: 2px solid rgba(var(--v-theme-primary), 0.2);
  margin-inline-start: 24px;
  padding-inline-start: 16px;
}

.nested-reply-container {
  position: relative;
  margin-block-start: 16px;
}

.comment-text {
  line-height: 1.5;
  white-space: pre-wrap;
  word-break: break-word;
}

.comment-header > .avatar-mr {
  margin-inline-end: 12px;
}

.reply-btn-mr {
  margin-inline-end: 8px;
}

.reply-icon-mr {
  margin-inline-end: 4px;
}
</style>
