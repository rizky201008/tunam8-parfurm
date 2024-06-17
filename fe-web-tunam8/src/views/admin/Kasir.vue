<!-- Catalogue.vue -->
<template>
  <Navbar>
    <div>
      <div class="container-fluid px-4 py-2">
        <Breadcrumbs class="d-flex align-items-center" :items="breadcrumbsItems" />
        <v-dialog v-model="showDialog" hide-overlay persistent width="300" lazy>
          <v-progress-circular indeterminate color="red" :size="90" class="mb-0"
            style="right: -100px;"></v-progress-circular>
        </v-dialog>
        <div class="row">
          <div class="col-md-12">
            <div class="card border-0 px-2 " style="text-align: end;">
              <div class="col text-right">
                <div class="button-set my-4">
                  <v-btn color="red" rounded="xl" @click="openKeranjang" class="mx-4 mb-2 mt-2">
                    Keranjang
                  </v-btn>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12 mt-4 mb-12">
            <Datatables ref="datatablesKasir" />
          </div>
        </div>
      </div>
    </div>
  </Navbar>
</template>

<script>
import axios from 'axios';
import Navbar from '@/components/AdminNavbar.vue';
import Datatables from '@/components/Vuetify/DataTablesKasir.vue';
import Breadcrumbs from '@/components/Vuetify/Breadcrumbs.vue';

const BASE_URL = import.meta.env.VITE_BASE_URL_API;

export default {
  name: 'DaftarBuku',
  components: {
    Navbar,
    Datatables,
    Breadcrumbs
  },
  data() {
    return {
      showDialog: false,
      parfum: {
        name: '',
        desc: '',
        price: '',
        stok: '',
        jenis_kelamin: '',
        category: '',
        tag: ''
      },
      selectedFile: [],
      categories: [],
      tags: [],
      selectedTags: [],
      breadcrumbsItems: [
        {
          title: 'Daftar Produk',
          disabled: false,
          href: '/admin/daftarproduk',
        },
      ],
    }
  },

  mounted() {
    this.fetchCategories();
    this.retrieveTags();
  },

  methods: {
    // Prompt
    closeModal() {
      document.getElementById('closeModal').click();
    },

    clearForm() {
      this.parfum.name = '';
      this.parfum.desc = '';
      this.parfum.price = '';
      this.parfum.stok = '';
      this.parfum.foto = '';
      this.parfum.jenis_kelamin = '',
      this.parfum.category = '',
        this.$refs.fileInput.value = '';
    },
    openKeranjang() {
      this.$router.push('/admin/keranjang');
    },
    openTags() {
      this.$router.push('/admin/tags');
    },

    // Method
    handleFileChange(event) {
      const fileInput = this.$refs.fileInput;
      this.selectedFiles = Array.from(fileInput.files);
    },
    async retrieveTags() {
      this.showDialog = true
      try {
        const response = await axios.get(BASE_URL + '/tags', {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('access_token')
          }
        });
        this.tags = response.data.data;
      } catch (error) {
        console.error(error);
      }
    },
    async fetchCategories() {
      try {
        const token = localStorage.getItem('access_token');
        const response = await axios.get(BASE_URL + '/categories', {
          headers: {
            'Authorization': 'Bearer ' + token,
          },
        });
        this.categories = response.data;
      } catch (error) {
        console.error('Error fetching categories:', error);
      } finally {
        this.showDialog = false
      }
    },
  }
};
</script>

<style scoped>
.dashboard-admin {
  min-height: 100vh;

  background: url("../../../src/assets/LandingPage/Background.png");
  background-position: center;
  background-size: cover;
}

.button-set {
  display: block;
  width: 100%;
  clear: both;
  margin: 15px 0;

}

button-custom,
.button {
  outline: 0 none;
  border: 0 none;
  padding: 13px 30px;
  background-color: #0771B8;
  background-image: linear-gradient(45deg, #ff0000 0%, #ff5252 50%, #ff7b7b 90%);
  background-position: 100% 0;
  background-size: 200% 200%;
  color: #FFF;
  font-size: 12px;
  text-transform: uppercase;
  border-radius: 20px;
  letter-spacing: 2px;
  transition: .3s;
  cursor: pointer;
  text-decoration: none;
  transition: all 0.5s ease;

  &:hover {
    background-position: 0 0;
  }
}
</style>