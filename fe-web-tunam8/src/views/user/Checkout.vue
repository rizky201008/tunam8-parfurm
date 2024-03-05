<!-- Catalogue.vue -->
<template>
  <Navbar>
    <div>
      <div class="container px-4 py-2">
        <Breadcrumbs class="d-flex align-items-center" :items="breadcrumbsItems" />
        <div class="col-md-12 bg-white" style="border-radius: 20px;">
          <div class="row">
            <h3 class="mx-8 mt-4" style="padding-left: 10px;">Produk Dipesan</h3>
          </div>
          <div v-for="(item, index) in cartItems" :key="index" class="cart-item">
            <div class="col-2">
              <img :src="item.product.image" alt="Product Image" class="product-image">
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
        <div class="col bg-white" style="border-radius: 20px; margin-bottom:150px">
          <div class="row">
            <div class="col-md-6  p-4 mb-2">
              <div class="row">
                <h3 class="ml-2">Pilih Alamat</h3>
              </div>
              <div class="row">
                <div class="mx-2 col-md-6">
                  <select v-model="selectedAddressId" class="form-select">
                    <option value="" disabled>Select Address</option>
                    <option v-for="(address, index) in addresses" :key="address.id" :value="address.id">
                      {{ address.label }} - {{ address.receiver }}
                    </option>
                  </select>
                  <div class="mt-4">
                    <div v-if="selectedAddressId !== null" class="col d-flex justify-content-center align-items-center">
                      <div class="card mx-2" style="border-radius: 20px; text-align: center; width: 400px;">
                        <div class="row">
                          <div class="card">
                            <div class="card-body">
                              <h5 class="card-title">Alamat </h5>
                              <p class="card-text text-left" id="regular">
                                <a>Nama Penerima: {{ selectedAddress.receiver }}</a>
                                <br>
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
                              </p>
                            </div>
                            <div class="card-footer my-2">
                              <div class="div">
                                <div v-if="loadingOngkir">
                                  <v-progress-linear indeterminate></v-progress-linear>
                                </div>
                                <div v-else>
                                  <small class="text-bold" id="regularGas">Ongkos Kirim : Rp. {{ formatPrice(ongkir) }}
                                  </small>
                                </div>
                              </div>
                            </div>
                          </div>
                        </div>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
            <div class="col-md-6 mt-2 mb-2 ">
              <div class="row">
                <h3 class="mt-4 ml-3">Total</h3>
              </div>
              <div class="row mx-2 border-bottom">
                <div class="col-md-4 col-6">
                  <a style="font-size: 20px;">Jumlah Barang</a>
                  <br>
                  <a style="font-size: 20px;">Harga Barang</a>
                  <br>
                  <a style="font-size: 20px;">Harga Ongkos Kirim</a>
                </div>
                <div class="col-md-4 col-6">
                  <a style="font-size: 20px;">: {{ checkedTotalItems }}</a>
                  <br>
                  <a style="font-size: 20px;">: Rp. {{ formatPrice(checkedTotalPrice) }}</a>
                  <br>
                  <a style="font-size: 20px;">: Rp. {{ formatPrice(ongkir) }}</a>
                </div>
              </div>
              <div class="row mt-20 mx-2">
                <div class="col-md-4 col-6">
                  <a style="font-size: 20px;">Total Dibayar</a>
                </div>
                <div class="col-md-4 col-6">
                  <a style="font-size: 20px;"> : <span style="font-weight: 500; color: #D0011B">Rp. {{
                    formatPrice(totalPaid) }} </span></a>
                </div>
              </div>
              <div class="row mt-8 mx-2">
                <button class="btn btn-danger" style="width: 100%;height:50px; margin-bottom:100px"
                  @click="processPayment">
                  Bayar
                </button>
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
      selectedAddressId: null,
      loadingOngkir: false,
      ongkir: '',
    };
  },
  watch: {
    selectedAddressId(newVal, oldVal) {
      if (newVal !== oldVal && newVal !== null) {
        this.sendShippingRequest();
      }
    }
  },
  computed: {
    checkedTotalItems() {
      return this.cartItems.reduce((acc, item) => acc + parseInt(item.quantity), 0);
    },
    checkedTotalPrice() {
      return this.cartItems.reduce((acc, item) => acc + (item.product.price * parseInt(item.quantity)), 0);
    },
    selectedAddress() {
      if (this.selectedAddressId !== null) {
        return this.addresses.find(address => address.id === this.selectedAddressId);
      }
      return null;
    },
    totalPaid() {
      return this.checkedTotalPrice + this.ongkir;
    }
  },
  mounted() {
    this.retrieveCart();
    this.retrieveAddress();
  },
  methods: {
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
        this.$nextTick(() => {
          this.checkedTotalItems;
        });
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

    async sendShippingRequest() {
      try {
        this.loadingOngkir = true;
        const requestData = {
          address_id: this.selectedAddressId,
          products: this.cartItems.map(item => ({
            id: item.product.id,
            qty: item.quantity
          }))
        };

        const response = await axios.post(BASE_URL + '/shipping', requestData, {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('access_token')
          }
        });

        console.log('Shipping request sent successfully:', response.data);
        this.ongkir = response.data.cost;
      } catch (error) {
        console.error('Error sending shipping request:', error);
      } finally {
        this.loadingOngkir = false;
      }
    },

    processPayment() {
      console.log(this.selectedAddressId)
      const requestData = {
        products: this.cartItems.map(item => ({
          id: item.product.id,
          qty: item.quantity
        })),
        address_id: this.selectedAddressId
      };

      axios.post(BASE_URL + '/transactions', requestData, {
        headers: {
          Authorization: 'Bearer ' + localStorage.getItem('access_token'),
        }
      })
        .then(response => {

          console.log('Payment created:', response.data);
          window.open(response.data.link, '_blank');
          this.$router.push('/keranjang');

        })
        .catch(error => {
          console.error('Error processing payment:', error);
          console.log(requestData)
        });
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
  width: 200px;
  height: 200px;
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
  