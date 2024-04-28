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
                  <v-btn color="red" rounded="xl" data-bs-toggle="modal" data-bs-target="#addTags">
                    Add Tags
                  </v-btn>
                  <!-- <v-btn color="red" rounded="xl" @click="openDaftarParfum">
                    Add Parfum
                  </v-btn> -->
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
                    <th scope="col">Tag</th>
                    <th scope="col">Action</th>
                  </tr>
                </thead>
                <tbody>
                  <tr v-for="(item, index) in tags" :key="item.id">
                    <th scope="row">{{ index + 1 }}</th>
                    <td>{{ item.name }}</td>
                    <td>
                      <button class="btn btn-infos mr-2" @click="showModal(item.id)">Edit</button>
                      <button class="btn btn-delete" @click="confirmDelete(index)">Hapus</button>
                    </td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal Nich -->

      <div class="modal fade text-black" id="addTags" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <share-modal ref="share-modal-ref" />
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">Tambah Tags</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeModal"></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="addTags">
                <div class="mb-3">
                  <label for="exampleInputEmail1" class="form-label">Nama Tags</label>
                  <input type="name" class="form-control" v-model="tags.name" aria-describedby="namaCustomer">
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button-custom type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button-custom>
              <button-custom class="btn btn-info" type="submit" @click="addTags">Tambah Tags</button-custom>
            </div>
          </div>
        </div>
      </div>

      <div class="modal fade text-black" id="editModal" tabindex="-1" aria-labelledby="editModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h5 class="modal-title" id="editModalLabel">Edit Tags</h5>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="editTags">
                <div class="mb-3">
                  <label for="kategoriName" class="form-label">Nama Tags</label>
                  <input type="text" class="form-control" id="tagsName" v-model="selectedTags.name">
                </div>
                <button-custom type="submit" class="btn btn-primary" @click="editTags">Simpan Tags</button-custom>
              </form>
            </div>
          </div>
        </div>
      </div>
      <v-dialog v-model="deleteDialog" max-width="500px">
        <v-card title="Confirm Delete">
          <v-card-text>
            Are you sure you want to remove this tags?
          </v-card-text>
          <v-card-actions>
            <v-btn text @click="deleteDialog = false">Cancel</v-btn>
            <v-btn color="red" @click="deleteTags()">Delete</v-btn>
          </v-card-actions>
        </v-card>
      </v-dialog>
    </div>
  </Navbar>
</template>
  
<script>
import axios from 'axios';
import Navbar from '@/components/AdminNavbar.vue';
import Breadcrumbs from '@/components/Vuetify/Breadcrumbs.vue';


const BASE_URL = import.meta.env.VITE_BASE_URL_API;

export default {
  name: 'DaftarBuku',
  components: {
    Navbar,
    Breadcrumbs
  },
  data() {
    return {
      tags: [],
      selectedTags: {
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
          title: 'Tags',
          disabled: true,
          href: '/admin/tags',
        }
      ],
      deleteDialog: false,
      deleteIndex: null
    }
  },
  mounted() {
    this.retrieveTags();
  },
  methods: {
    // Prompt
    closeModal() {
      document.getElementById('closeModal').click();
    },

    clearForm() {
      this.tags.name = '';
    },

    openDaftarParfum() {
      this.$router.push('/admin/daftarproduk');
    },
    showModal(id) {
      this.selectedTags = this.tags.find(item => item.id === id);
      $('#editModal').modal('show');
    },
    confirmDelete(index) {
      this.deleteIndex = index;
      this.deleteDialog = true;
    },

    // Method
    handleFileChange(event) {
      // Handle file change event
      const fileInput = this.$refs.fileInput;
      // Store all selected files in an array
      this.selectedFiles = Array.from(fileInput.files);
    },
    async retrieveTags() {
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
    async deleteTags(id) {
      try {
        const item_id = this.tags[this.deleteIndex].id;
        await axios.delete(BASE_URL + '/tags', {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('access_token')
          },
          data: { id: item_id },

        });
        this.tags.splice(this.deleteIndex, 1);
        this.deleteDialog = false;
        this.retrieveTags();
        this.$notify({
          type: 'success',
          title: 'Success',
          text: 'Tags berhasil dihapus',
          color: 'green'
        });
      } catch (error) {
        console.error(error);
        // Optionally, show an error message or perform other actions if deletion fails
      }
    },
    async addTags() {
      try {
        // this.parfum.price = parseInt(this.parfum.price);
        const formData = new FormData();

        formData.append('name', this.tags.name);

        const token = localStorage.getItem('access_token')
        const response = await axios.post(BASE_URL + '/tags', formData, {
          headers: {
            'Content-Type': 'multipart/form-data', // Set content type for file upload
            'Authorization': 'Bearer ' + token, // Include Bearer token in the Authorization header
          },
        });

        this.closeModal();
        this.clearForm();
        this.retrieveTags();

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
    editTags() {
      const data = {
        id: this.selectedTags.id,
        name: this.selectedTags.name,
      };
      axios.put(BASE_URL + '/tags', data, {
        headers: {
          Authorization: 'Bearer ' + localStorage.getItem('access_token')
        }
      })
        .then(response => {
          console.log('Tags updated successfully:', response.data);

          $('#editModal').modal('hide');
          this.retrieveTags();
          this.$notify({
            type: 'success',
            title: 'Success',
            text: 'Tags telah ter-update!',
            color: 'green',
          });
        })
        .catch(error => {
          console.error('Error updating tags:', error);
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
  