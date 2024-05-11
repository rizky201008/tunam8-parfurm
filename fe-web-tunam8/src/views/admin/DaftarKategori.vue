<!-- Catalogue.vue -->
<template>
  <Navbar>
    <div>
      <div class="container-fluid px-4 py-2">
        <Breadcrumbs class="d-flex align-items-center" :items="breadcrumbsItems" />
        <div class="row justify-content-center">
          <div class="col-md-12">
            <div class="card border-0 px-2 " style="text-align: end;">
              <div class="col text-right">
                <div class="button-set my-4">
                  <v-btn color="red" rounded="xl" data-bs-toggle="modal" data-bs-target="#addKategori" >
                    Add Category
                  </v-btn>
              
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-6 mt-4">
            <div class="card rounded border-0 p-2">
              <table class="table table-sm">
                <thead class="table-light">
                  <tr>
                    <th scope="col">No</th>
                    <th scope="col">Kategori</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in kategori" :key="item.id">
                    <th scope="row">{{ index + 1 }}</th>
                    <td>{{ item.name }}</td>
                    <td>
                      <button class="btn btn-infos mr-2" @click="showModal(item.id)">Edit</button>
                      <button class="btn btn-delete" @click="deleteKategori(item.id)">Hapus</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal Nich -->

      <div class="modal fade text-black" id="addKategori" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <share-modal ref="share-modal-ref" />
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Kategori</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeModal"></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="addKategori">
                <div class="mb-3">
                  <label for="exampleInputEmail1" class="form-label">Nama Kategori</label>
                  <input type="name" class="form-control" v-model="kategori.name" aria-describedby="namaCustomer">
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button-custom type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button-custom>
              <button-custom class="btn btn-info" type="submit" @click="addKategori">Tambah Kategori</button-custom>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade text-black" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editModalLabel">Edit Category</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="editKategori">
                <div class="mb-3">
                  <label for="kategoriName" class="form-label">Nama Kategori</label>
                  <input type="text" class="form-control" id="kategoriName" v-model="selectedCategory.name">
                </div>
                <button-custom type="submit" class="btn btn-primary" @click="editKategori">Simpan Kategori</button-custom>
              </form>
            </div>
          </div>
        </div>
      </div>
    </div>
  </Navbar>
</template>
  
<script>
import axios from 'axios';
import Navbar from '@/components/AdminNavbar.vue';
import Datatables from '@/components/Vuetify/DataTablesProduk.vue';
import Breadcrumbs from '@/components/Vuetify/Breadcrumbs.vue';
import { Modal } from 'bootstrap'

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
      kategori: [],
      kategori: {
      },
      selectedCategory: {
        id: null,
        name: ''
      },
      breadcrumbsItems: [
        {
          title: 'Daftar Produk',
          disabled: false,
          href: '/admin/daftarproduk',
        },
        {
          title: 'Kategori',
          disabled: true,
          href: '/admin/kategori',
        }
      ],
    }
  },
  mounted() {
    this.retrieveKategori();
  },
  methods: {
    // Prompt
    closeModal() {
      document.getElementById('closeModal').click();
    },

    clearForm() {
      this.kategori.name = '';
    },
    openCategory() {
      this.$router.push('/admin/category');
    },

    openDaftarParfum() {
      this.$router.push('/admin/daftarproduk');
    },
    showModal(id) {
      this.selectedCategory = this.kategori.find(item => item.id === id);
      $('#editModal').modal('show');
    },
    
    // Method
    handleFileChange(event) {
      // Handle file change event
      const fileInput = this.$refs.fileInput;
      // Store all selected files in an array
      this.selectedFiles = Array.from(fileInput.files);
    },
    async retrieveKategori() {
      try {
        const response = await axios.get(BASE_URL + '/categories', {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('access_token')
          }
        });
        this.kategori = response.data;
      } catch (error) {
        console.error(error);
      }
    },
    async deleteKategori(id) {
      try {
        await axios.delete(BASE_URL + '/categories', {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('access_token')
          },
          data: { id: id },

        });

        this.retrieveKategori();
        this.$notify({
          type: 'success',
          title: 'Success',
          text: 'Kategori berhasil dihapus',
          color: 'green'
        });
      } catch (error) {
        console.error(error);
        // Optionally, show an error message or perform other actions if deletion fails
      }
    },
    async addKategori() {
      try {
        // this.parfum.price = parseInt(this.parfum.price);
        const formData = new FormData();

        formData.append('name', this.kategori.name);

        const token = localStorage.getItem('access_token')
        const response = await axios.post(BASE_URL + '/categories', formData, {
          headers: {
            'Content-Type': 'multipart/form-data', // Set content type for file upload
            'Authorization': 'Bearer ' + token, // Include Bearer token in the Authorization header
          },
        });

        this.closeModal();
        this.clearForm();
        this.retrieveKategori();

        this.$notify({
          type: 'success',
          title: 'Success',
          text: response.data.message,
          color: 'green',
        });
      } catch (error) {
        console.error(error);
        if (error.response && error.response.data.message) {
          const errorMessage = error.response.data.message;
          this.$notify({
            type: 'error',
            title: 'Error',
            text: errorMessage,
            color: 'red',
          });
        }
      }
    },
    editKategori() {
      const data = {
        id: this.selectedCategory.id,
        name: this.selectedCategory.name,
      };
      axios.put(BASE_URL + '/categories', data, {
        headers: {
          Authorization: 'Bearer ' + localStorage.getItem('access_token')
        }
      })
        .then(response => {
          console.log('Category updated successfully:', response.data);

          $('#editModal').modal('hide');
          this.retrieveKategori();
          this.$notify({
            type: 'success',
            title: 'Success',
            text: 'Kategori telah ter-update!',
            color: 'green',
          });
        })
        .catch(error => {
          console.error('Error updating category:', error);
        });
    }
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

.btn-delete {
  background-color: #dc3545;
  /* Red background color */
  border-color: #dc3545;
  /* Red border color */
  color: #fff;
  /* White text color */
  font-weight: 400;
  /* Lighter font weight */
  padding: 0.2rem 0.4rem;
  /* Reduced padding */
  font-size: 0.85rem;
  /* Smaller font size */
  transition: background-color 0.3s, color 0.3s;
  /* Smooth transition */
}

.btn-delete:hover {
  background-color: #fff;
  /* White background color on hover */
  border-color: #dc3545;
  /* Red border color on hover */
  color: #dc3545;
  /* Red text color on hover */
}

.btn-infos {
  background-color: blue;
  /* Red background color */
  border-color: blue;
  /* Red border color */
  color: #fff;
  /* White text color */
  font-weight: 400;
  /* Lighter font weight */
  padding: 0.2rem 0.4rem;
  /* Reduced padding */
  font-size: 0.85rem;
  /* Smaller font size */
  transition: background-color 0.3s, color 0.3s;
  /* Smooth transition */
}

.btn-infos:hover {
  background-color: #fff;
  /* White background color on hover */
  border-color: blue;
  /* Red border color on hover */
  color: blue;
  /* Red text color on hover */
}
</style>
  