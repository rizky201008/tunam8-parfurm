<!-- Catalogue.vue -->
<template>
  <Navbar>
    <div class="container">
      <div class=" px-4">
        <Breadcrumbs class="d-flex align-items-center" :items="breadcrumbsItems" />
        <div class="row mb-2">
          <form class="search-container" @submit.prevent="searchProduct" style="max-width: 350px;">
            <v-card-text>
              <v-text-field :loading="loading" append-inner-icon="mdi-magnify" density="compact" label="Search Perfume"
                variant="solo" hide-details single-line @click:append-inner="onClick" @input="searchProduct"
                v-model="searchQuery"></v-text-field>
            </v-card-text>
            <select class="form-select form-select-sm mb-3" aria-label="Large select example"
              v-model="selectedCategory">
              <option selected>Select Category</option>
              <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}
              </option>
            </select>
          </form>
        </div>

        <div class="row" style="border-bottom: 2px solid black;" v-if="showRecommendations">
          <div class="row mb-2 text-center text-black">
            RECOMMENDATIONS
          </div>
          <router-link :to="'/product/' + item.slug" class="col-md-2 mb-2 col-6"
            v-for="(item, index) in personalized.slice(0, 6)" :key="item.id">
            <div class="product-single-card text-black">
              <div class="product-top-area">
                <div class="product-img">
                  <div class="first-view">
                    <img :src="item.images[0].link" alt="logo" class="img-fluid">
                  </div>
                  <div class="hover-view">
                    <img :src="item.images[0].link" alt="logo" class="img-fluid">
                  </div>
                </div>
                <div class="sideicons">
                  <button class="sideicons-btn">
                    <v-icon icon="mdi-heart"></v-icon>
                  </button>
                  <button class="sideicons-btn" @click.native.prevent="addCart(item.id)">
                    <v-icon icon="mdi-cart-plus"></v-icon>
                  </button>
                </div>
              </div>
              <div class="product-info">
                <h6 class="product-category"><a href="#">{{ item.category.name }}</a></h6>
                <h6 class="product-title text-truncate"><a href="#">{{ item.name }}</a></h6>
                <div class="d-flex align-items-center">
                  <span class="review-count"><b>Stock: </b>{{ item.stock }}</span>
                </div>
                <div class="d-flex flex-wrap align-items-center py-2">
                  <div class="new-price">
                    Rp. {{ formatPrice(item.price) }}
                  </div>
                </div>
              </div>
            </div>
          </router-link>
        </div>
        <div class="row mt-2">
          <router-link :to="'/product/' + item.slug" class="col-md-2 mb-2 col-6" v-for="(item, index) in products"
            :key="item.id">
            <div class="product-single-card text-black">
              <div class="product-top-area">
                <div class="product-img">
                  <div class="first-view">
                    <img :src="item.images[0].link" alt="logo" class="img-fluid">
                  </div>
                  <div class="hover-view">
                    <img :src="item.images[0].link" alt="logo" class="img-fluid">
                  </div>
                </div>
                <div class="sideicons">
                  <button class="sideicons-btn">
                    <v-icon icon="mdi-heart"></v-icon>
                  </button>
                  <button class="sideicons-btn" @click.native.prevent="addCart(item.id)">
                    <v-icon icon="mdi-cart-plus"></v-icon>
                  </button>
                </div>
              </div>
              <div class="product-info">
                <h6 class="product-category"><a href="#">{{ item.category.name }}</a></h6>
                <h6 class="product-title text-truncate"><a href="#">{{ item.name }}</a></h6>
                <div class="d-flex align-items-center">
                  <span class="review-count"><b>Stock: </b>{{ item.stock }}</span>
                </div>
                <div class="d-flex flex-wrap align-items-center py-2">
                  <div class="new-price">
                    Rp. {{ formatPrice(item.price) }}
                  </div>
                </div>
              </div>
            </div>
          </router-link>
        </div>
      </div>
    </div>
  </Navbar>
</template>

<script>
import Navbar from '@/components/AdminNavbar.vue';
import axios from 'axios';
const BASE_URL = import.meta.env.VITE_BASE_URL_API;
import Breadcrumbs from '@/components/Vuetify/Breadcrumbs.vue';

export default {
  name: 'DaftarBuku',
  components: {
    Navbar,
    Breadcrumbs
  },
  data() {
    return {
      personalized: [],
      products: [],
      categories: [],
      selectedCategory:'',
      breadcrumbsItems: [
        {
          title: 'Home',
          disabled: false,
          href: '/dashboard',
        }
      ],
      searchResults: [],
      showRecommendations: true,
    };
  },
  computed: {

  },
  watch: {
    // Watch for changes in searchQuery and trigger searchProduct method
    searchQuery(newValue) {
      if (newValue.trim() === '') {
        // If searchQuery becomes empty, retrieve parfum (all products)
        this.retrieveParfum();
      } else {
        // If searchQuery is not empty, perform search
        this.searchProduct();
      }
    }
  },
  mounted() {
    this.retrieveParfum();
    this.retrievePersonalized();
    this.retrieveCategories();
  },
  methods: {
    formatPrice(price) {
      const numericPrice = parseFloat(price);
      return numericPrice.toLocaleString('id-ID');
    },
    async retrieveCategories() {
      try {
        const response = await axios.get(BASE_URL + '/categories', {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('access_token')
          }
        });
        this.categories = response.data;
      } catch (error) {
        console.error(error);
      }
    },
    async retrievePersonalized() {
      try {
        const response = await axios.get(BASE_URL + '/personalized-products', {
          headers: {
            Authorization: "Bearer " + localStorage.getItem('access_token')
          }
        });

        this.personalized = response.data;

        if (response.data.length > 0) {
          this.fotoUrl = response.data[0].foto;
        }
      } catch (error) {
        console.error(error);
      }
    },

    clearSearch() {
      this.searchQuery = ''; // Clear the search input
    },
    async searchProduct() {
      if (this.searchTimer) {
        clearTimeout(this.searchTimer);
      }

      this.searchTimer = setTimeout(() => {
        if (this.searchQuery.trim() === '') {
          this.retrieveParfum();
        } else {
          this.searchProductMethod();
        }

        // Setelah selesai pencarian, atur showRecommendations berdasarkan searchQuery
        this.showRecommendations = this.searchQuery.trim() === '';
      }, 2000);
    },
    async searchProductMethod() {
      try {
        const response = await axios.post(BASE_URL + '/search-products', {
          query: this.searchQuery.trim()
        }, {
          headers: {
            Authorization: "Bearer " + localStorage.getItem('access_token')
          }
        });

        this.products = response.data;
      } catch (error) {
        console.error('Error searching products:', error);
        this.products = []; // Clear products if there's an error
      }
    },
    async retrieveParfum() {
      try {
        const response = await axios.get(BASE_URL + '/products', {
          headers: {
            Authorization: "Bearer " + localStorage.getItem('access_token')
          }
        });

        this.products = response.data.data;

        if (response.data.length > 0) {
          this.fotoUrl = response.data[0].foto;
        }
      } catch (error) {
        console.error(error);
      }
    },
    addCart(id) {
      axios.post(BASE_URL + '/carts', { product_id: id }, {
        headers: {
          Authorization: 'Bearer ' + localStorage.getItem('access_token')
        }
      })

        .then(response => {
          console.log('Item added to cart:', response.data);
          this.$notify({
            type: 'success',
            title: 'Success',
            text: 'Produk berhasil ditambahkan',
            color: 'green',
          });
        })
        .catch(error => {
          console.error('Error adding item to cart:', error);
          this.$notify({
            type: 'error',
            title: 'Tambah Produk Gagal',
            text: 'Produk sudah ada pada keranjang',
            color: 'red',
          });
        });
    },
  },
};



</script>

<style scoped>
/* .card-img-top {
  max-width: 450px;
  max-height: 300px;
  width: auto;
  height: auto;
} */

.user-select-none {
  user-select: none;
}

a {
  text-decoration: none;
  color: unset;
}

.product-single-card {
  padding: 20px;
  border-radius: 5px;
  box-shadow: 1px 1px 15px #cccccc40;
  transition: 0.5s ease-in;
  background-color: white;
}

.product-single-card:hover {
  -webkit-box-shadow: 1px 1px 28.5px -7px #d6d6d6;
  -moz-box-shadow: 1px 1px 28.5px -7px #d6d6d6;
  box-shadow: 1px 1px 28.5px -7px #d6d6d6;
}

.product-single-card .product-info {
  padding: 15px 0 0 0;
}

.product-single-card .product-top-area {
  position: relative;
  display: flex;
  align-items: center;
  overflow: hidden;
  border-radius: 5px;
}

.product-single-card .product-top-area .product-discount {
  position: absolute;
  top: 10px;
  left: 10px;
  background: white;
  border-radius: 3px;
  padding: 5px 10px;
  box-shadow: 1px 1px 28.5px -7px #dddddd;
  user-select: none;
  z-index: 999;
}

.product-single-card .product-top-area .product-img {
  aspect-ratio: 1/1;
  overflow: hidden;
}

.product-single-card .product-top-area .product-img .first-view {
  transition: 0.5s ease-in;
}

.product-single-card .product-top-area .product-img .hover-view {
  opacity: 0;
  transition: 0.5s ease-in;
}

/* 
.product-single-card .product-top-area:hover .product-img .first-view {
  opacity: 0;
  width: 0;
  height: 0;
}

.product-single-card .product-top-area:hover .product-img .hover-view {
  opacity: 100%;
  scale: 1.2;
} */
.product-single-card .product-top-area .product-img img {
  /* width: 250px;  */
  /* height: 150px;  */
  /* object-fit: cover; */
  border: 1px solid black;
  /* Add a 5px solid white border */
  box-sizing: border-box;
}

.product-single-card .product-top-area .sideicons {
  position: absolute;
  right: 15px;
  display: grid;
  gap: 10px;
}

.product-single-card .product-top-area .sideicons .sideicons-btn {
  background-color: #fff;
  color: #000;
  border-radius: 50%;
  border: none;
  width: 35px;
  height: 35px;
  display: flex;
  justify-content: center;
  align-items: center;
  opacity: 0;
  visibility: hidden;
  transform: translateX(60px);
  transition: 0.3s ease-in;
  -webkit-box-shadow: 1px 1px 28.5px -7px #dddddd;
  -moz-box-shadow: 1px 1px 28.5px -7px #dddddd;
  box-shadow: 1px 1px 28.5px -7px #dddddd;
}

.product-single-card .product-top-area .sideicons .sideicons-btn:hover {
  color: #fff;
  background-color: #000;
}

.product-single-card .product-top-area .sideicons .sideicons-btn:nth-child(1) {
  transition-delay: 100ms;
}

.product-single-card .product-top-area .sideicons .sideicons-btn:nth-child(2) {
  transition-delay: 200ms;
}

.product-single-card .product-top-area .sideicons .sideicons-btn:nth-child(3) {
  transition-delay: 300ms;
}

.product-single-card .product-top-area .sideicons .sideicons-btn:nth-child(4) {
  transition-delay: 400ms;
}

.product-single-card .product-top-area:hover .sideicons .sideicons-btn {
  opacity: 100%;
  visibility: visible;
  transform: translateX(0);
}

.product-single-card .product-info .product-category {
  font-weight: 600;
  opacity: 60%;
}

.product-single-card .product-info .product-title {
  font-size: 16px;
  font-weight: 600;
}

.product-single-card .product-info .old-price,
.product-single-card .product-info .new-price {
  padding-right: 15px;
  font-size: 18px;
  font-weight: 600;
  letter-spacing: 1px;
}

.product-single-card .product-info .old-price {
  text-decoration: line-through;
  opacity: 70%;
}


.search-container {
  width: 400px;
  display: block;
  margin: 0 auto;
  padding-right: 30px;
}

input#search-bar {
  margin: 0 auto;
  width: 100%;
  height: 45px;
  padding: 0 20px;
  font-size: 1rem;
  background-color: white;
  border: 1px solid #D0CFCE;
  outline: none;

  &:focus {
    border: 1px solid #D0011B;
    transition: 0.35s ease;
    color: #D0011B;

    &::-webkit-input-placeholder {
      transition: opacity 0.45s ease;
      opacity: 0;
    }

    &::-moz-placeholder {
      transition: opacity 0.45s ease;
      opacity: 0;
    }

    &:-ms-placeholder {
      transition: opacity 0.45s ease;
      opacity: 0;
    }
  }
}

.search-icon {
  position: relative;
  float: right;
  width: 75px;
  height: 75px;
  top: -62px;
  right: -70px;
}
</style>