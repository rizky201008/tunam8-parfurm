<!-- Catalogue.vue -->
<template>
  <Navbar>
    <div class="dashboard-admin">
      <div class="container-fluid px-4 py-2">
        <div class="row">
          <div class="col-md-12">
            <div class="border-0 px-2 mb-4">
              here
            </div>
          </div>
        </div>
        <div class="row">
          <div class="col-md-12">
            <div class="card mb-4 pt-4 border-0 px-2">
              <div class="col px-4">
                <form @submit.prevent="saveParfum">
                  <!-- <div class="mb-3 d-flex justify-content-center">
                    <div class="row mb-6">
                      <div class="col-12"></div>
                      <div v-if="parfum.images" id="carouselExampleIndicators" class="carousel slide"
                        data-bs-ride="carousel">
                        <div class="carousel-inner">
                          <div v-for="(image, index) in parfum.images" :key="index"
                            :class="{ 'carousel-item': true, 'active': index === 0 }">
                            <img :src="image.link" class="d-block w-100" :alt="'Slide ' + (index + 1)"
                              style="max-width: 450px; max-height: 350px; object-fit: co;">
                          </div>
                        </div>
                        <div v-if="parfum.images && parfum.images.length > 1" class="carousel-indicators">
                          <button v-for="(image, index) in parfum.images" :key="index" type="button"
                            :data-bs-target="'#carouselExampleIndicators'" :data-bs-slide-to="index"
                            :class="{ 'active': index === 0 }" class="thumbnail" :aria-label="'Slide ' + (index + 1)">
                            <img :src="image.link" class="d-block w-100" :alt="'Slide ' + (index + 1)"
                              style="max-width: 100px; max-height: 80px; object-fit: contain;">
                          </button>
                        </div>
                      </div>
                    </div>
                  </div> -->
                  <div class="row">
                  </div>
                  <label for="judul">Nama</label>
                  <input class="form-control" type="text" v-model="users.name" id="judul" />
                  <br>
                  <label for="desc">Email</label>
                  <textarea class="form-control" v-model="users.email" id="desc"></textarea>
                  <br>
                  <button-custom class="btn btn-info mb-2" type="submit" @click="saveParfum">Save Parfum</button-custom>
                </form>
              </div>
            </div>
          </div>
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
  name: 'EditBuku',
  components: {
    Navbar
  },
  data() {
    return {
      dataLoaded: false,
      users: {
        id: '',
        name: '',
        email: '',
      },
      selectedFile: '',
      fotoFile: null,
    }
  },
  async mounted() {
    try {
      const slug = this.$route.params.slug;
      const response = await axios.get(BASE_URL + '/user/detail', {
        headers: {
          Authorization: "Bearer " + localStorage.getItem('access_token')
        }
      });
      this.users = response.data
    } catch (error) {
      console.error(error);

      if (error.response && error.response.data.message) {
        const errorMessage = error.response.data.message;
        this.$notify({
          type: 'error',
          title: 'Error',
          text: errorMessage,
          color: 'red'
        });
      }
    }
  },
  methods: {
    handleFileChange(event) {
      this.fotoFile = event.target.files[0];
    },
    async saveParfum() {
      try {
        if (this.fotoFile) {
          const formData = new FormData();
          formData.append('images', this.fotoFile);
          formData.append('product_id', this.parfum.id);

          await axios.post(BASE_URL + '/product-image', formData, {
            headers: {
              'Content-Type': 'multipart/form-data',
              Authorization: 'Bearer ' + localStorage.getItem('access_token')
            }
          });
        }

        const requestData = {
          id: this.parfum.id,
          name: this.parfum.name,
          description: this.parfum.description,
          price: this.parfum.price,
          stock: this.parfum.stock,
          category_id: this.parfum.category,
        };
        const response = await axios.put(BASE_URL + '/products', requestData, {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('access_token')
          }
        });
        console.log(this.category_id);
        this.$router.push('/admin/daftarproduk');
        this.$notify({
          type: 'success',
          title: 'Success',
          text: 'Produk telah ter-update!',
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
            color: 'red'
          });
        }
      }
    },
  }

};
</script>
  
<style scoped>
.dashboard-admin {
  min-height: 100vh;

  /* background: url("../../../src/assets/LandingPage/Background.png"); */
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

.carousel-indicators button.thumbnail {
  width: 100px;
}

.carousel-indicators button.thumbnail:not(.active) {
  opacity: 0.7;
}

.carousel-indicators {
  position: static;
}

@media screen and (min-width: 992px) {
  .carousel {
    max-width: 70%;
    margin: 0 auto;
  }
}
</style>
  