<!-- Catalogue.vue -->
<template>
  <Navbar>
    <div class="dashboard-admin">
      <div class="container-fluid px-4">
        <Breadcrumbs class="d-flex align-items-center" :items="breadcrumbsItems" />

        <v-dialog v-model="dialog1" hide-overlay persistent width="300" lazy>
          <v-card color="light">
            <v-card-text>
              Please stand by
              <v-progress-linear indeterminate color="red" class="mb-0"></v-progress-linear>
            </v-card-text>
          </v-card>
        </v-dialog>
        <div class="card border-0" v-if="!loading">
          <div class="row p-2 pt-4">
            <div class="col-md-6 ">
              <div v-if="parfum.images" id="carouselExampleIndicators" class="carousel slide " data-bs-ride="carousel">
                <div class="carousel-inner">
                  <div v-for="(image, index) in parfum.images" :key="index"
                    :class="{ 'carousel-item': true, 'active': index === 0 }">
                    <img :src="image.link" class="d-block w-100" :alt="'Slide ' + (index + 1)"
                      style="max-width: 450px; object-fit: co;">
                  </div>
                </div>
                <div v-if="parfum.images && parfum.images.length > 1" class=" carousel-indicators ">
                  <button v-for="(image, index) in parfum.images" :key="index" type="button"
                    :data-bs-target="'#carouselExampleIndicators'" :data-bs-slide-to="index"
                    :class="{ 'active': index === 0 }" class="thumbnail" :aria-label="'Slide ' + (index + 1)">
                    <img :src="image.link" class="d-block w-100" :alt="'Slide ' + (index + 1)"
                      style="max-width: 100px; max-height: 80px; object-fit: contain;">
                  </button>
                </div>
              </div>
            </div>
            <div class="col-md-6 d-flex flex-column justify-content-between">
              <a style="font-size: 32px; font-weight: bold;">{{ parfum.name }}</a>
              <br>
              <div class="ratings">
                <div class="stars d-flex">
                  <div class="theme-text mr-2">Product Ratings: </div>
                  <div>&#9733;</div>
                  <div>&#9733;</div>
                  <div>&#9733;</div>
                  <div>&#9733;</div>
                  <div>&#9733;</div>
                  <div class="ml-2">(4.5) 50 Reviews</div>
                </div>
              </div>
              <div class="price my-2" style="font-weight: bold; font-size: 32px;">Rp. {{ formatPrice(parfum.price) }}</div>
              <div class="theme-text subtitle">Deskripsi:</div>
              <div class="brief-description">
                {{ parfum.description }}
              </div>
              <br>
              <div class="mt-auto pb-2">
                <a>Category &nbsp;: </a>
                <br>
                <v-chip class="mt-3">{{ parfum.category.name }}</v-chip>
                <hr>
                <div class="row">
                  <div class="col-md-3 mt-2">
                    <input type="number" class="form-control" value="1">
                  </div>
                  <div class="col-md-9">
                    <button class="btn addBtn btn-block mt-2">Add to basket</button>
                  </div>
                </div>
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
import Breadcrumbs from '@/components/Vuetify/Breadcrumbs.vue';
import { TimeScale } from 'chart.js';
const BASE_URL = import.meta.env.VITE_BASE_URL_API


export default {
  name: 'EditBuku',
  components: {
    Navbar,
    Breadcrumbs
  },
  data() {
    return {
      dialog1: true,
      loading: true,
      parfum: {
        id: '',
        name: '',
        description: '',
        price: '',
        images: [],
        category: []
      },
      fotoFile: null,
      breadcrumbsItems: [
        {
          title: 'Home',
          disabled: false,
          href: '/dashboard',
        },
        {
          title: '',
          disabled: true,
          href: '/',
        }
      ],
    }
  },
  async mounted() {
    try {
      const slug = this.$route.params.slug;
      const response = await axios.get(BASE_URL + '/product/' + slug, {
        headers: {
          Authorization: "Bearer " + localStorage.getItem('access_token')
        }
      });

      this.parfum = response.data
      this.parfum.images = response.data.images
      this.breadcrumbsItems[1].title = this.parfum.name

      setTimeout(() => {
        this.dialog1 = false;
        this.loading = false;
      }, 500);
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
  computed: {
    imageUrl() {
      return BASE_URL + '/' + this.foto;
    }
  },
  methods: {
    handleFileChange(event) {
      this.fotoFile = event.target.files[0];
    },
    formatPrice(price) {
        const numericPrice = parseFloat(price);
        return numericPrice.toLocaleString('id-ID');
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

.carousel-indicators button.thumbnail {
  width: 100px;
}

.carousel-indicators button.thumbnail:not(.active) {
  opacity: 0.7;
}

.carousel-indicators {
  position: static;
  margin-right: 40%;
  margin-bottom: 100px
}

@media screen and (min-width: 992px) {
  .carousel {
    max-width: 70%;
    margin: 0 auto;
    margin-right: 5%;
  }
}

.details-snippet1 {
  color: #585656;
}

/* Main text uses this styling and color */

.details-snippet1 .mini-preview img {
  border: 1px solid #585656;
  border: 1px solid purple;
  margin-bottom: 8px;
}



.brief-description {
  /* color: #585656; */
  color: #464343;
}


.select-colors .color {
  display: inline-block;
  border: 1px solid grey;
  height: 35px;
  width: 35px;
  border-radius: 50%;
  margin-right: 5px;
  background-color: black;
}

.select-colors .color.red {
  background-color: red;
}

.select-colors .color.silver {
  background-color: silver;
}

.select-colors .color.black {
  background-color: black;
}



.addBtn {
  background-color: #D0011B;
  color: white;
  text-transform: uppercase;
}

.addBtn:hover {
  background-color: #500150;
  color: white;
}
</style>
  