<!-- Catalogue.vue -->
<template>
  <Navbar>
    <div>
      <div class="container-fluid px-4 py-2">
        <Breadcrumbs class="d-flex align-items-center" :items="breadcrumbsItems" />
        <div class="col-md-12 bg-white" style="border-radius: 20px;">
          <div class="row">
            <h1 class="mx-8 mt-4" style="padding-left: 10px;">Checkout</h1>
          </div>
          <div v-for="(item, index) in cartItems" :key="index" class="cart-item">
            <div class="col-2">
              <img :src="item.product.images" alt="Product Image" class="product-image">
            </div>
            <div class="col-5">
              <div class="product-details">
                <a style="font-size: 27px;">{{ item.product.name }}</a>
                <p style="font-weight: bold; font-size: large; color: green;">Rp. {{ formatPrice(item.product.price)
                }}</p>
              </div>
            </div>
            <div class="col-5">
              <div class="quantity-controls">
                <span class="mx-2" style="font-size: 20px;">Qty. {{ item.quantity }}</span>
                <p style="font-weight: bold; font-size: medium">Rp. {{ formatPrice(item.product.price * item.quantity)
                }}</p>
              </div>
            </div>
          </div>
        </div>
        <div class="col bg-white mt-2 px-2 py-2 mb-2" style="border-radius: 20px;">
          <div class="row">
            <h1 class="mx-8 mt-4" style="padding-left: 10px;">Pilih Alamat</h1>
          </div>
          <div class="row text-center">
            <div class="mx-2">
              <a>Select Address:</a>
              <select v-model="selectedAddressId" class="form-select">
                <option value="" disabled>Select Address</option>
                <option v-for="(address, index) in addresses" :key="index" :value="index">
                  {{ address.label }} - {{ address.receiver }}
                </option>
              </select>
            </div>
            <div v-if="selectedAddressId !== null" class="col mt-2 d-flex justify-content-center align-items-center">
              <div class="card mx-2" style="border-radius: 20px;">
                <div class="row">
                  <h3 style="font-weight: bold; font-size: 20px;">Address {{ selectedAddressId + 1 }} </h3>
                  <a>City: {{ selectedAddress.city }}</a>
                  <br>
                  <a>Province: {{ selectedAddress.province }}</a>
                  <br>
                  <a>Kode Pos: {{ selectedAddress.postal_code }}</a>
                  <br>
                  <a>Alamat: {{ selectedAddress.address_detail }}</a>
                  <br>
                  <a>No Telpon: {{ selectedAddress.phone_number }}</a>
                  <br>
                  <a>Penerima: {{ selectedAddress.receiver }}</a>
                  <br>
                </div>
                <div class="row d-flex justify-content-center align-items-center">
                  <div class="col-md-4">
                    <v-chip class="my-2">{{ selectedAddress.label }}</v-chip>
                  </div>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-12 bg-white mt-2 py-2 mt-2" style="border-radius: 20px;">
          <div class="row">
            <h1 class="mx-8 mt-4" style="padding-left: 10px;">Total</h1>
          </div>
          <div class="row mx-2">
            <div class="col-md-6">
              <a style="font-size: 20px;">Jumlah Barang: {{ checkedTotalItems }}</a>
              <br>
              <a style="font-size: 20px;">Total Harga: Rp. {{ formatPrice(checkedTotalPrice) }}</a>
            </div>
            <div class="col-md-4">

              <v-btn color="red" rounded="xl" @click="checkout">
                Checkout
              </v-btn>
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
      products: [],
      breadcrumbsItems: [
        {
          title: 'Home',
          disabled: false,
          href: '/dashboard',
        },
        {
          title: 'Keranjang',
          disabled: false,
          href: '/keranjang',
        },
        {
          title: 'Checkout',
          disabled: true,
          href: '/checkout',
        }
      ],
      searchQuery: '',
      cartItems: [],
      deleteDialog: false,
      deleteIndex: null,
      addresses: [],
      selectedAddressId: null
    };
  },
  computed: {
    checkedTotalItems() {
      return this.cartItems.reduce((acc, item) => acc + parseInt(item.quantity), 0);
    },
    checkedTotalPrice() {
      return this.cartItems.reduce((acc, item) => acc + (item.product.price * parseInt(item.quantity)), 0);
    },
    selectedAddress() {
      if (this.selectedAddressId !== null && this.addresses.length > this.selectedAddressId) {
        return this.addresses[this.selectedAddressId];
      }
      return null;
    }
  },
  mounted() {
    this.retrieveCart();
    this.retrieveAddress();
  },
  methods: {
    // incrementQuantity(index) {
    //   this.cartItems[index].quantity++;
    // },
    // decrementQuantity(index) {
    //   if (this.cartItems[index].quantity > 1) {
    //     this.cartItems[index].quantity--;
    //   }
    // },
    formatPrice(price) {
      const numericPrice = parseFloat(price);
      return numericPrice.toLocaleString('id-ID');
    },

    confirmDelete(index) {
      this.deleteIndex = index;
      this.deleteDialog = true;
    },
    async retrieveCart() {
      try {
        const response = await axios.get(BASE_URL + '/carts', {
          params: {
            active: 1
          },
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('access_token')
          }
        });
        this.cartItems = response.data;
      } catch (error) {
        console.error('Error retrieving selected cart data:', error);
      }
    },
    async retrieveAddress() {
      try {
        const token = localStorage.getItem('access_token');
        const response = await axios.get(BASE_URL + '/address', {
          headers: {
            Authorization: 'Bearer ' + token
          }
        });
        this.addresses = response.data;
      } catch (error) {
        console.error('Error fetching addresses:', error);
      }
    },
    async deleteItem() {
      try {
        const item_id = this.cartItems[this.deleteIndex].id;
        await axios.delete(BASE_URL + '/carts', {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('access_token')
          },
          data: {
            id: item_id
          }
        });

        this.cartItems.splice(this.deleteIndex, 1);
        this.deleteDialog = false;
        this.retrieveCart();
      } catch (error) {
        console.error('Error deleting item:', error);
      }
    }

  }
};



</script>
  
<style scoped>
/* .card-img-top {
  max-width: 450px;
  max-height: 300px;
  width: auto;
  height: auto;
} */
.shopping-cart {
  display: flex;
  justify-content: space-between;
  align-items: flex-start;
}

.cart-items {
  flex: 2;
}

.cart-item {
  display: flex;
  align-items: center;
  margin-bottom: 20px;
}

.product-image {
  width: 250px;
  height: 250px;
  object-fit: contain;
  margin: auto;
  padding: 20px;
  max-width: 100%;
  height: auto;
  object-fit: contain;
}

.quantity-controls button {
  margin: 0 5px;
  padding: 5px 10px;
  cursor: pointer;
}

.selected-address {
  border: 2px solid green;
}

.cart-summary {
  flex: 1;
  padding: 20px;
}

.cart-summary h2 {
  margin-bottom: 10px;
}

.cart-summary p {
  margin-bottom: 5px;
}

.checkbox-style {
  width: 20px;
  height: 20px;
}
</style>
  