import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import Login from '../views/Login.vue'
// User
import UserDashboard from '../views/user/UserDashboard.vue'
import UserProfile from '../views/user/Profile.vue'
import ViewProduk from '../views/user/ViewProduk.vue'

// Admin
import AdminDashboard from '../views/admin/AdminDashboard.vue'
import DaftarProduk from '../views/admin/DaftarProduk.vue'
import EditProduk from '../views/admin/EditProduk.vue'
import DaftarKategori from '../views/admin/DaftarKategori.vue'
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

    {
      path: '/dashboard',
      name: 'User Dashboard',
      component: UserDashboard,
    },

    {
      path: '/product/:slug',
      name: 'View Produk',
      component: ViewProduk,
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
      path: '/admin/daftarproduk',
      name: 'Daftar Produk',
      component: DaftarProduk,
    },
    {
      path: '/admin/daftarproduk/editproduk/:slug',
      name: 'Edit Produk',
      component: EditProduk,
    },
    {
      path: '/admin/kategori',
      name: 'Daftar Kategori',
      component: DaftarKategori,
    },
    {
      path: '/admin/kelolapelanggan',
      name: 'Kelola Pelanggan',
      component: KelolaPelanggan,
    },


  ]
})

export default router
