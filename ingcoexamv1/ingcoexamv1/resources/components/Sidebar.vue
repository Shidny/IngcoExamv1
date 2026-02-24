<script setup lang="ts">
import { computed } from 'vue'
import { Link, usePage, router } from '@inertiajs/vue3'

const page = usePage()
const props = defineProps({
  user: Object
})

const userAvatar = computed(() => props.user?.profile_image || '/dist/img/user2-160x160.jpg')
const userName = computed(() => props.user?.name || 'User')

const handleLogout = () => {
  router.post('/logout')
}
</script>

<template>
  <aside class="sidebar">
    <!-- Sidebar Header -->
    <div class="sidebar-header">
      <div class="logo">
        <i class="fas fa-feather"></i>
        <span>IngcoExam</span>
      </div>
    </div>

    <!-- User Profile Section -->
    <div class="sidebar-user">
      <img :src="userAvatar" :alt="userName" class="user-avatar">
      <div class="user-info">
        <p class="user-name">{{ userName }}</p>
        <p class="user-role">Member</p>
      </div>
    </div>

    <!-- Sidebar Menu -->
    <nav class="sidebar-menu">
      <Link href="/dashboard" class="menu-link" :class="{ active: page.component === 'Dashboard' }">
        <i class="fas fa-home"></i>
        <span class="menu-text">Home</span>
      </Link>

      <Link href="/profile" class="menu-link" :class="{ active: page.component === 'Profile' }">
        <i class="fas fa-user"></i>
        <span class="menu-text">Profile</span>
      </Link>
    </nav>

    <!-- Sidebar Footer -->
    <div class="sidebar-footer">
      <button class="logout-btn" @click="handleLogout">
        <i class="fas fa-sign-out-alt"></i>
        <span>Logout</span>
      </button>
    </div>
  </aside>
</template>

<style scoped>
.sidebar {
  position: fixed;
  left: 0;
  top: 0;
  width: 280px;
  height: 100vh;
  background: linear-gradient(135deg, #FF8C00 0%, #FF6F00 100%);
  color: white;
  display: flex;
  flex-direction: column;
  overflow-y: auto;
  z-index: 1000;
  box-shadow: 2px 0 10px rgba(0, 0, 0, 0.1);
}

.sidebar-header {
  padding: 25px 20px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.logo {
  display: flex;
  align-items: center;
  gap: 10px;
  font-size: 20px;
  font-weight: bold;
  color: white;
}

.logo i {
  font-size: 24px;
}

.sidebar-user {
  padding: 20px 15px;
  display: flex;
  align-items: center;
  gap: 15px;
  border-bottom: 1px solid rgba(255, 255, 255, 0.1);
}

.user-avatar {
  width: 50px;
  height: 50px;
  border-radius: 50%;
  object-fit: cover;
  border: 2px solid white;
}

.user-info {
  flex: 1;
  min-width: 0;
}

.user-name {
  margin: 0;
  font-size: 14px;
  font-weight: 600;
  color: white;
  white-space: nowrap;
  overflow: hidden;
  text-overflow: ellipsis;
}

.user-role {
  margin: 3px 0 0 0;
  font-size: 12px;
  color: rgba(255, 255, 255, 0.7);
}

.sidebar-menu {
  flex: 1;
  padding: 15px 0;
  overflow-y: auto;
}

.menu-link {
  display: flex;
  align-items: center;
  gap: 12px;
  padding: 12px 20px;
  color: rgba(255, 255, 255, 0.8);
  text-decoration: none;
  transition: all 0.3s ease;
  border-left: 3px solid transparent;
  font-size: 15px;
}

.menu-link:hover {
  background: rgba(0, 0, 0, 0.1);
  color: white;
}

.menu-link.active {
  background: rgba(0, 0, 0, 0.2);
  color: white;
  border-left-color: white;
}

.menu-link i {
  width: 18px;
  text-align: center;
  font-size: 16px;
}

.menu-text {
  white-space: nowrap;
}

.sidebar-footer {
  padding: 15px;
  border-top: 1px solid rgba(255, 255, 255, 0.1);
}

.logout-btn {
  width: 100%;
  display: flex;
  align-items: center;
  gap: 10px;
  padding: 10px 15px;
  background: rgba(0, 0, 0, 0.2);
  border: none;
  border-radius: 6px;
  color: white;
  cursor: pointer;
  font-size: 14px;
  font-weight: 500;
  transition: all 0.3s ease;
}

.logout-btn:hover {
  background: rgba(0, 0, 0, 0.3);
}

.logout-btn i {
  width: 16px;
  text-align: center;
}

/* Scrollbar styling */
.sidebar-menu::-webkit-scrollbar {
  width: 6px;
}

.sidebar-menu::-webkit-scrollbar-track {
  background: transparent;
}

.sidebar-menu::-webkit-scrollbar-thumb {
  background: rgba(255, 255, 255, 0.3);
  border-radius: 3px;
}

.sidebar-menu::-webkit-scrollbar-thumb:hover {
  background: rgba(255, 255, 255, 0.5);
}

/* Responsive */
@media (max-width: 768px) {
  .sidebar {
    width: 280px;
    transform: translateX(-100%);
    transition: transform 0.3s ease;
    box-shadow: 2px 0 10px rgba(0, 0, 0, 0.3);
  }

  .sidebar.mobile-open {
    transform: translateX(0);
  }
}
</style>
