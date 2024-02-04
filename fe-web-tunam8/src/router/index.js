import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import Login from '../views/Login.vue'
// User
import UserDashboard from '../views/user/UserDashboard.vue'
import UserProfile from '../views/user/Profile.vue'

// Admin
import AdminDashboard from '../views/admin/AdminDashboard.vue'
import DaftarBuku from '../views/admin/DaftarBuku.vue'
import KelolaPelanggan from '../views/admin/KelolaPelanggan.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'home',
      component: HomeView
    },
    {
      path: '/about',
      name: 'about',
      // route level code-splitting
      // this generates a separate chunk (About.[hash].js) for this route
      // which is lazy-loaded when the route is visited.
      component: () => import('../views/AboutView.vue')
    },
    // User
    {
      path: '/profile/:id',
      name: 'User Profile',
      component: UserProfile,
    },
    {
      path: '/dashboard',
      name: 'User Dashboard',
      component: UserDashboard,
    },

    // Admin
    {
      path: '/login',
      name: 'login',
      component: Login
    },
    {
      path: '/admin/dashboard',
      name: 'Admin Dashboard',
      component: AdminDashboard,
    },
    {
      path: '/admin/daftarbuku',
      name: 'Daftar Buku',
      component: DaftarBuku,
    },
    {
      path: '/admin/kelolapelanggan',
      name: 'Kelola Pelanggan',
      component: KelolaPelanggan,
    },


  ]
})

export default router
