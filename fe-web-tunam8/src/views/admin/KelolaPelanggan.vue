<template>
  <Navbar>
    <div>
      <div class="container-fluid px-4 py-2">
        <div class="row">
          <div class="col-md-12">
            <div class="card border-0 px-2 " style="text-align: end;">
              <div class="col text-right">
                <div class="button-set my-4">
                  <button-custom data-bs-toggle="modal" data-bs-target="#exampleModal">Add New Customer</button-custom>
                </div>
              </div>
            </div>
          </div>
          <!-- <div class="col-md-12 mt-4">
            <div class="card border-0 px-2 " style="text-align: end;">
              <div class="col text-right">
                <div class="table-responsive mt-2 mb-2">
                  <table id="datatable-programs" class="table" style="width: 100%">
                    <thead>
                      <tr>
                        <th>No</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Action</th>
                      </tr>
                    </thead>
                    <tbody style="text-align: left;">
                      <tr v-for="(item, index) in users" :key="item.id">
                        <td>{{ index + 1 }}</td>
                        <td>{{ item.name }}</td>
                        <td>{{ item.email }}</td>
                        <td>
                          <v-btn class="ma-2" color="purple" icon="mdi-pencil"></v-btn>
                          <ion-icon name="pencil-outline" type="button"></ion-icon>
                          &nbsp;
                          <ion-icon name="trash" type="button" @click="deleteUser(item.id)"></ion-icon>
                        </td>
                      </tr>
                    </tbody>
                  </table>
                </div>
              </div>
            </div>
          </div> -->

          <div class="col-md-12 mt-4">
            <Datatables ref="datatablesComp"/>
          </div>
        </div>
      </div>
      <!-- Modal Nich -->
      <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog">
          <div class="modal-content">
            <div class="modal-header">
              <h1 class="modal-title fs-5" id="exampleModalLabel">Add New Customer</h1>
              <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close" id="closeModal"></button>
            </div>
            <div class="modal-body">
              <form @submit.prevent="addUser">
                <div class="mb-3">
                  <label for="exampleInputEmail1" class="form-label">Nama Customer</label>
                  <input type="name" class="form-control" v-model="namaCustomer" aria-describedby="namaCustomer">
                </div>
                <div class="mb-3">
                  <label for="exampleInputEmail" class="form-label">Email address</label>
                  <input type="email" class="form-control" v-model="emailCustomer" aria-describedby="emailCustomer">
                </div>
                <div class="mb-3">
                  <label for="exampleInputPassword" class="form-label">Password</label>
                  <input type="password" class="form-control" v-model="passwordCustomer">
                </div>
              </form>
            </div>
            <div class="modal-footer">
              <button-custom type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button-custom>
              <button-custom class="btn btn-primary" type="submit" @click="addUser">Add Customer</button-custom>
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
import Datatables from '@/components/Vuetify/DataTables.vue';

const BASE_URL = import.meta.env.VITE_BASE_URL_API;

export default {
  name: 'KelolaPelanggan',
  components: {
    Navbar,
    Datatables
  },
  data() {
    return {
      users: [
        { id: '', name: '', email: '' },
      ],
      // Modal Add Customer
      namaCustomer: '',
      emailCustomer: '',
      passwordCustomer: '',
    }
  },

  computed: {
    getitems() {
      // Add a sequential number to each item
      return this.items.map((item, index) => {
        return { ...item, no: index + 1 };
      });
    },
  },
  mounted() {
    this.retrieveUser();
    // this.closeModal();
    // this.addUser();
    // this.deleteUser();
  },
  methods: {
    // Prompt
    closeModal() {
      document.getElementById('closeModal').click();
    },

    clearForm() {
      this.namaCustomer = '';
      this.emailCustomer = '';
      this.passwordCustomer = '';
    },


    // Method
    async addUser() {
      try {
        const response = await axios.post(BASE_URL + '/register', {
          name: this.namaCustomer,
          email: this.emailCustomer,
          password: this.passwordCustomer
        });
        this.closeModal(),
        this.$notify({
            type: 'success',
            title: 'Success',
            text: response.data.message,
            color: 'green'
          });
        this.$refs.datatablesComp.retrieveUser();
        this.clearForm();
          console.log(response.data);
      } catch (error) {
        console.error(error);

        if (error.response && error.response.data.message) {
          const errorMessage = error.response.data.message;
          // Display notification with red color
          this.$notify({
            type: 'error',
            title: 'Error',
            text: errorMessage,
            color: 'red'
          });
        }
      }
    },
    async retrieveUser() {
      try {
        const response = await axios.get(BASE_URL + '/getUser', {
          headers: {
            Authorization: "Bearer " + localStorage.getItem('access_token')
          }
        });
        this.users = response.data;
      } catch (error) {
        console.error(error);
      }
    },
    async deleteUser(id) {
      try {
        const response = await axios.delete(BASE_URL + '/deleteUser/' + id, {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('access_token'),
          },
        });
        this.retrieveUser();
      } catch (error) {
        console.error(error);
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
  background-image: linear-gradient(45deg, #0F7AC0 0%, #3CB7CB 50%, #4BC7CF 90%);
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
  