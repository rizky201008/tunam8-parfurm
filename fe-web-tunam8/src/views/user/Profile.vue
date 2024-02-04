<!-- Catalogue.vue -->
<template>
  <Navbar>
    <div class="dashboard-admin">
      <div class="container-fluid px-4 py-2">
        <div class="row">
          <div class="col-md-12">
            <div class="card mb-4 border-0 px-2 " style="text-align: end;">
              <div class="col text-right">
                <div class="button-set my-4">
                  <button-custom data-bs-toggle="modal" data-bs-target="#addAddress">Add Address</button-custom>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-3">
            <div class="card px-4 border-0">
              <div class="row">
                <h4 class="mt-4">User Profile</h4>
                <div class="inputbox">
                  <ion-icon name="person-outline"></ion-icon>
                  <input class="input-type" type="input" v-model="username" disabled required>
                  <label for="">Name</label>
                </div>
                <div class="inputbox">
                  <ion-icon name="mail-outline"></ion-icon>
                  <input class="input-type" type="email" v-model="email" disabled required>
                  <label for="">Email</label>
                </div>
                <div class="inputbox">
                  <ion-icon name="calendar-outline"></ion-icon>
                  <input class="input-type" type="date" v-model="tanggalLahir" required>
                  <label for="">Tanggal</label>
                </div>
              </div>
            </div>
          </div>

          <div class="col-3 " v-for="(item, index) in address" :key="item.id" v-if="dataLoaded">
            <div class="card px-6 py-2 border-0">
              <div class="row">
                <h4 class="mt-4">Address {{ index + 1 }} <ion-icon name="trash" type="button"
                    @click="deleteAddress(item.id)"> </ion-icon></h4>
                <div class="inputbox">
                  <input class="input-type" type="email" v-model="item.nama_penerima" disabled required>
                  <label for="">Nama Penerima</label>
                </div>
                <div class="inputbox">
                  <input class="input-type" type="email" v-model="item.no_telp_penerima" disabled required>
                  <label for="">Nomor Telpon Penerima</label>
                </div>
                <div class="inputbox">
                  <input class="input-type" type="input" v-model="item.jalan" disabled required>
                  <label for="">Jalan</label>
                </div>
                <div class="inputbox">
                  <input class="input-type" type="email" v-model="item.kelurahan" disabled required>
                  <label for="">Kelurahan</label>
                </div>
                <div class="inputbox">
                  <input class="input-type" type="email" v-model="item.kecamatan" disabled required>
                  <label for="">Kecamatan</label>
                </div>
                <div class="inputbox">
                  <input class="input-type" type="email" v-model="item.kota" disabled required>
                  <label for="">Kota</label>
                </div>
                <div class="inputbox">
                  <input class="input-type" type="email" v-model="item.provinsi" disabled required>
                  <label for="">Provinsi</label>
                </div>
                <div class="inputbox">
                  <input class="input-type" type="email" v-model="item.kode_pos" disabled required>
                  <label for="">Kode Pos</label>
                </div>
              </div>
            </div>
          </div>
        </div>
      </div>
      <!-- Modal Nich -->
      <div class="modal fade" id="addAddress" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
          <form ref="form" @submit.stop.prevent="handleAddress">
            <div class="modal-content">
              <div class="modal-header">
                <h1 class="modal-title fs-5" id="exampleModalLabel">Add New Address</h1>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                  id="closeModal"></button>
              </div>
              <div class="modal-body">
                <div class="row">
                  <div class="col-md-4">
                    <div class="mb-3">
                      <label for="exampleInputPassword" class="form-label">Nama Penerima</label>
                      <input type="text" class="form-control" v-model="addAddress.nama_penerima">
                    </div>
                    <div class="mb-3">
                      <label for="exampleInputPassword" class="form-label">No Telp Penerima</label>
                      <input class="form-control" type="text" pattern="[0-9]*" inputmode="numeric"
                        v-model="addAddress.no_telp_penerima" style="-moz-appearance: textfield;">
                    </div>
                  </div>
                  <div class="col-md-8">
                    <div class="mb-3">
                      <label for="exampleInputEmail1" class="form-label">Jalan</label>
                      <input type="text" class="form-control" v-model="addAddress.jalan" aria-describedby="namaCustomer">
                    </div>
                    <div class="mb-3">
                      <label for="exampleInputEmail" class="form-label">Kelurahan</label>
                      <input type="text" class="form-control" v-model="addAddress.kelurahan"
                        aria-describedby="emailCustomer">
                    </div>
                    <div class="mb-3">
                      <label for="exampleInputPassword" class="form-label">Kecamatan</label>
                      <input type="text" class="form-control" v-model="addAddress.kecamatan">
                    </div>
                    <div class="mb-3">
                      <label for="exampleInputPassword" class="form-label">Kota</label>
                      <input type="text" class="form-control" v-model="addAddress.kota">
                    </div>
                    <div class="mb-3">
                      <label for="exampleInputPassword" class="form-label">Provinsi</label>
                      <input type="text" class="form-control" v-model="addAddress.provinsi">
                    </div>
                    <div class="mb-3">
                      <label for="exampleInputPassword" class="form-label">Kode Pos</label>
                      <input type="text" class="form-control" v-model="addAddress.kode_pos">
                    </div>
                  </div>
                </div>
              </div>
              <div class="modal-footer">
                <button-custom type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button-custom>
                <button-custom class="btn btn-primary" type="submit" @click="handleAddress">Add Customer</button-custom>
              </div>
            </div>
          </form>
        </div>
      </div>
    </div>
  </Navbar>
</template>
  
<script>
import Navbar from '@/components/AdminNavbar.vue';
import axios from 'axios';
const BASE_URL = import.meta.env.VITE_BASE_URL_API

export default {
  name: 'DaftarBuku',
  components: {
    Navbar
  },
  data() {
    return {
      dataLoaded: false,
      id: '',
      username: '',
      email: '',
      addAddress: [
        {
          nama_penerima: '', no_telp_penerima: '', jalan: '', kelurahan: '', kecamatan: '', kota: '', provinsi: '', kode_pos: ''
        }
      ],
      address: [
        { nama_penerima: '', no_telp_penerima: '', jalan: '', kelurahan: '', kecamatan: '', kota: '', provinsi: '', kode_pos: '' },
      ],

    }
  },
  async mounted() {
    try {
      const response = await axios.get(BASE_URL + '/user', {
        headers: {
          Authorization: "Bearer " + localStorage.getItem('access_token')
        }
      });
      this.id = response.data.user.id
      this.username = response.data.user.name
      this.email = response.data.user.email
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
    this.retrieveAddress();
  },
  methods: {
    closeModal() {
      document.getElementById('closeModal').click();
    },
    clearAddressForm() {
      this.addAddress = {
        nama_penerima: '',
        no_telp_penerima: '',
        jalan: '',
        kelurahan: '',
        kecamatan: '',
        kota: '',
        provinsi: '',
        kode_pos: ''
      };
    },


    // async addAddress() {
    //   try {
    //     const formData = new FormData();
    //     formData.append('jalan', this.addAddress.jalan);
    //     formData.append('kelurahan', this.addAddress.kelurahan);
    //     formData.append('kecamatan', this.addAddress.kecamatan);
    //     formData.append('kota', this.addAddress.kota);
    //     formData.append('kode_pos', this.addAddress.kode_pos);
    //     formData.append('user_id', this.id);

    //     const response = await axios.post(BASE_URL + '/address/store', formData, {
    //       headers: {
    //         Authorization: "Bearer " + localStorage.getItem('access_token')
    //       }
    //     });
    //     this.closeModal();
    //     this.retrieveAddress();

    //   } catch (error) {
    //     console.error(error);

    //     if (error.response && error.response.data.message) {
    //       const errorMessage = error.response.data.message;
    //       // Display notification with red color
    //       this.$notify({
    //         type: 'error',
    //         title: 'Error',
    //         text: errorMessage,
    //         color: 'red'
    //       });
    //     }
    //   }
    // },
    handleAddress() {
      const formData = new FormData();

      formData.append('no_telp_penerima', this.addAddress.no_telp_penerima);
      formData.append('nama_penerima', this.addAddress.nama_penerima);
      formData.append('jalan', this.addAddress.jalan);
      formData.append('kelurahan', this.addAddress.kelurahan);
      formData.append('kecamatan', this.addAddress.kecamatan);
      formData.append('kota', this.addAddress.kota);
      formData.append('provinsi', this.addAddress.provinsi);
      formData.append('kode_pos', this.addAddress.kode_pos);
      formData.append('user_id', this.id);

      const token = localStorage.getItem('access_token')
      const axiosConfig = {
        headers: {
          Authorization: "Bearer " + token
        }
      };

      axios.post(BASE_URL + '/address/store', formData, axiosConfig)
        .then(response => {

          this.closeModal();
          this.retrieveAddress();
          this.clearAddressForm();
        })
        .catch(error => {
          console.error(error);

        });
    },
    async retrieveAddress() {
      try {
        const response = await axios.get(BASE_URL + '/address/getbyid/' + this.id, {
          headers: {
            Authorization: "Bearer " + localStorage.getItem('access_token')
          }
        });
        this.address = response.data;
        this.dataLoaded = true;
      } catch (error) {
        console.error(error);
        this.dataLoaded = false;
      }
    },
    async deleteAddress(id) {
      try {
        const response = await axios.delete(BASE_URL + '/address/delete/' + id, {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('access_token'),
          },
        });
        this.retrieveAddress();
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

.form-box {
  position: relative;
  width: 400px;
  height: 450px;
  background: transparent;
  border: 2px solid rgba(0, 0, 0, 0.5);
  border-radius: 20px;
  backdrop-filter: blur(15px);
  display: flex;
  justify-content: center;
  align-items: center;

}

h2 {
  font-size: 2em;
  color: black;
  text-align: center;
}

.inputbox {
  position: relative;
  margin: 10px 0;
  width: fill;
  border-bottom: 2px solid black;
}

.inputbox label {
  position: absolute;
  /* top: 50%; */
  left: 5px;
  transform: translateY(-50%);
  color: black;
  font-size: 1em;
  pointer-events: none;
  transition: .5s;
}

.input-type:focus~label,
.input-type:valid~label {
  top: -5px;
}

.inputbox input {
  width: 100%;
  height: 50px;
  background: transparent;
  border: none;
  outline: none;
  font-size: 1em;
  padding: 0 35px 0 5px;
  color: black;
}

.inputbox input[type="date"]::-webkit-calendar-picker-indicator {
  opacity: 0;
  position: absolute;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
}

.inputbox ion-icon {
  position: absolute;
  right: 8px;
  color: black;
  font-size: 1.2em;
  top: 20px;
}

.forget {
  margin: -15px 0 15px;
  font-size: .9em;
  color: black;
  display: flex;
  justify-content: space-between;
}

.forget label input {
  margin-right: 3px;

}

.forget label a {
  color: black;
  text-decoration: none;
}

.forget label a:hover {
  text-decoration: underline;
}

.login-button {
  width: 100%;
  height: 40px;
  border-radius: 40px;
  background: black;
  border: none;
  outline: none;
  cursor: pointer;
  font-size: 1em;
  font-weight: 600;
}

.register {
  font-size: .9em;
  color: black;
  text-align: center;
  margin: 25px 0 10px;
}

.register p a {
  text-decoration: none;
  color: black;
  font-weight: 600;
}

.register p a:hover {
  text-decoration: underline;
}

.container {
  padding: 20px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.padding-container {
  padding: 20px;
  display: flex;
  justify-content: center;
  align-items: center;
}

.fade-enter-active,
.fade-leave-active {
  transition: opacity 0.5s;
}

.fade-enter,
.fade-leave-to {
  opacity: 0;
}
</style>
  