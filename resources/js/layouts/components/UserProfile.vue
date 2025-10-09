<script setup>
// contoh ambil nama user, nanti bisa diganti dari store / API login
const user = {
  name: "Deemz"
}
</script>

<template>
  <!-- Nama User sebagai dropdown -->
  <VMenu
    location="bottom end"
    offset="14px"
  >
    <!-- Activator: klik nama user -->
    <template #activator="{ props }">
      <span v-bind="props" class="cursor-pointer font-weight-semibold">
        {{ user.name }}
      </span>
    </template>

    <!-- Dropdown Menu -->
    <VList>
      <!-- 👉 Profile
      <VListItem
        link
        @click="$router.push('/edit-profile')"
      >
        <template #prepend>
          <VIcon
            class="me-2"
            icon="ri-user-line"
            size="22"
          />
        </template>
        <VListItemTitle>Profile</VListItemTitle>
      </VListItem> -->

      <!-- 👉 Logout -->
      <VListItem @click="$router.push('/login')">
        <template #prepend>
          <VIcon
            class="me-2"
            icon="ri-logout-box-r-line"
            size="22"
          />
        </template>
        <VListItemTitle>Logout</VListItemTitle>
      </VListItem>
    </VList>
  </VMenu>
</template>
