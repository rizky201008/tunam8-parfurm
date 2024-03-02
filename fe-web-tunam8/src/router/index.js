import { createRouter, createWebHistory } from 'vue-router'
import HomeView from '../views/HomeView.vue'
import Login from '../views/Login.vue'
// User
import UserDashboard from '../views/user/UserDashboard.vue'
import UserProfile from '../views/user/EditProfil.vue'
import Keranjang from '../views/user/Keranjang.vue'
import Checkout from '../views/user/Checkout.vue'
import Pesanan from '../views/user/Pesanan.vue'
import DetailPesanan from '../views/user/DetailPesanan.vue'
// import UserProfile from '../views/user/Profile.vue'
import ViewProduk from '../views/user/ViewProduk.vue'

// Admin
import AdminDashboard from '../views/admin/AdminDashboard.vue'
import DaftarProduk from '../views/admin/DaftarProduk.vue'
import EditProduk from '../views/admin/EditProduk.vue'
import DaftarKategori from '../views/admin/DaftarKategori.vue'
import DaftarTags from '../views/admin/DaftarTags.vue'
import KelolaPelanggan from '../views/admin/KelolaPelanggan.vue'
import ManagePesanan from '../views/admin/ManagePesanan.vue'
import DetailPesananAdmin from '../views/admin/DetailPesananAdmin.vue'

import { getUserRole } from '@/utils/auth'
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
      path: '/dashboard',
      name: 'User Dashboard',
      component: UserDashboard,
    },
    {
      path: '/profile',
      name: 'User Profile',
      component: UserProfile,
    },
    {
      path: '/product/:slug',
      name: 'View Produk',
      component: ViewProduk,
    },
    {
      path: '/keranjang',
      name: 'User Keranjang',
      component: Keranjang,
    },
    {
      path: '/checkout',
      name: 'User Checkout',
      component: Checkout,
    },
    {
      path: '/pesanan',
      name: 'User Pesanan',
      component: Pesanan,
    },
    {
      path: '/pesanan/:id',
      name: 'User Detail Pesanan',
      component: DetailPesanan,
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
      meta: { requiresAdmin: true } // Add meta field to indicate admin access required
    },
    {
      path: '/admin/daftarproduk',
      name: 'Daftar Produk',
      component: DaftarProduk,
      meta: { requiresAdmin: true } // Add meta field to indicate admin access required
    },
    {
      path: '/admin/daftarproduk/editproduk/:slug',
      name: 'Edit Produk',
      component: EditProduk,
      meta: { requiresAdmin: true } // Add meta field to indicate admin access required
    },
    {
      path: '/admin/kategori',
      name: 'Daftar Kategori',
      component: DaftarKategori,
      meta: { requiresAdmin: true } // Add meta field to indicate admin access required
    },
    {
      path: '/admin/tags',
      name: 'Daftar Tags',
      component: DaftarTags,
      meta: { requiresAdmin: true } // Add meta field to indicate admin access required
    },
    {
      path: '/admin/kelolapelanggan',
      name: 'Kelola Pelanggan',
      component: KelolaPelanggan,
      meta: { requiresAdmin: true } 
    },
    {
      path: '/admin/pesanan',
      name: 'Manage Pesanan',
      component: ManagePesanan,
      meta: { requiresAdmin: true } 
    },
    {
      path: '/admin/detailpesanan/:id',
      name: 'Kelola Pesanan',
      component: DetailPesananAdmin,
      meta: { requiresAdmin: true } 
    },
    {
      path: '/admin/detailpesanan',
      name: 'Detail Pesanan',
      component: DetailPesananAdmin,
      meta: { requiresAdmin: true } 
    },
  ]
})

router.beforeEach(async (to, from, next) => {
  const isAdminRoute = to.matched.some(record => record.meta.requiresAdmin);
  if (isAdminRoute) {
    try {
      const userRole = await getUserRole(); 
      if (userRole !== 'admin') {
        next('/');
      } else {
        next(); 
      }
    } catch (error) {
      console.error('Error fetching user role:', error);
      next('/'); // Redirect to home page if an error occurs
    }
  } else {
    next();
  }
});
export default router
