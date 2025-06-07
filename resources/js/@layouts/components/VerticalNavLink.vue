<script setup>
const props = defineProps({
  item: {
    type: null,
    required: true,
  },
})
</script>

<template>
  <li
    class="nav-link"
    :class="{ disabled: item.disable }"
  >
    <Component
      :is="item.to ? 'RouterLink' : 'a'"
      :to="item.to"
      :href="item.href"
      :target="item.target"
    >
      <VIcon
        :icon="item.icon || 'ri-checkbox-blank-circle-line'"
        class="nav-item-icon"
      />
      <!-- 👉 Title -->
      <span class="nav-item-title">
        {{ item.title }}
      </span>
      <!--
        <span
        class="nav-item-badge"
        :class="item.badgeClass"
        >
        {{ item.badgeContent }}
        </span> 
      -->
    </Component>
  </li>
</template>

<style lang="scss">
.layout-vertical-nav {
  .nav-link a {
    display: flex;
    align-items: center;
    border-radius: 8px;
    color: rgba(var(--v-theme-on-surface), var(--v-medium-emphasis-opacity));
    cursor: pointer;
    padding-block: 10px;
    padding-inline: 16px;
    transition: all 0.3s ease-in-out;

    .nav-item-icon {
      color: rgba(var(--v-theme-on-surface), var(--v-medium-emphasis-opacity));
      margin-inline-end: 12px;
      transition: all 0.3s ease-in-out;
    }

    .nav-item-title {
      color: rgba(var(--v-theme-on-surface), var(--v-medium-emphasis-opacity));
      transition: all 0.3s ease-in-out;
    }

    &:hover {
      background-color: rgba(var(--v-theme-primary), 0.1); /* Light background for hover */
      .nav-item-icon,
      .nav-item-title {
        color: rgb(var(--v-theme-primary)); /* Primary color for text/icon on hover */
      }
    }

    &.router-link-active,
    &.router-link-exact-active {
      background-color: rgb(var(--v-theme-primary)); /* Primary color for active background */
      .nav-item-icon,
      .nav-item-title {
        color: rgb(var(--v-theme-on-primary)); /* On-primary color for active text/icon */
      }
    }

    &.disabled {
      opacity: var(--v-disabled-opacity);
      pointer-events: none;
    }
  }
}
</style>
