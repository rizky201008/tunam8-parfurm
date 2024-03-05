<!-- Catalogue.vue -->
<template>
  <Navbar>
    <div>
      <div class="container px-4 py-2">
        <Breadcrumbs class="d-flex align-items-center" :items="breadcrumbsItems" />
        <div class="bg-white col-md-12">
          <div v-for="(item, index) in cartItems" :key="index" class="cart-item">
            <div class="col-1 d-flex justify-content-center align-item-center">
              <input type="checkbox" v-model="item.checked" class="checkbox-style">
            </div>
            <div class="col-3 d-flex justify-content-center align-item-center">
              <img :src="item.product.image" alt="Product Image" class="product-image">
            </div>
            <div class="col-5 text-left">
              <div class="product-details">
                <a style="font-size: 27px;">{{ item.product.name }}</a>
                <p style="font-weight: bold; font-size: large; color: green;">Rp. {{ formatPrice(item.product.price)
                }}</p>
              </div>
            </div>
            <div class="col-3">
              <div class="quantity-controls">
                <v-icon @click="decrementQuantity(index)" class="mx-2" color="red" size="24"
                  icon="mdi-minus-circle-outline"></v-icon>
                <span class="mx-2" style="font-size: 20px;">{{ item.quantity }}</span>
                <v-icon @click="incrementQuantity(index)" class="mx-2" color="green" size="24"
                  icon="mdi-plus-circle-outline"></v-icon>
                <v-icon @click="confirmDelete(index)" class="mx-2" color="red" icon="mdi-delete" size="26"></v-icon>
                <p style="font-weight: bold; font-size: medium">Rp. {{ formatPrice(item.product.price * item.quantity)
                }}</p>
              </div>
            </div>
          </div>
        </div>
        <div class="row-12 bg-white mt-2 px-2 py-2 text-center" style="border-radius: 20px;">
          <div>
            <h2>Cart Summary</h2>
            <p>Total Items: {{ checkedTotalItems }}</p>
            <p>Total Price: Rp. {{ formatPrice(checkedTotalPrice) }}</p>
            <v-btn color="red" rounded="xl" @click="checkout">
              Checkout
            </v-btn>
          </div>
        </div>
        <v-dialog v-model="deleteDialog" max-width="500px">
          <v-card title="Confirm Delete">
            <v-card-text>
              Are you sure you want to remove this item from your cart?
            </v-card-text>
            <v-card-actions>
              <v-btn text @click="deleteDialog = false">Cancel</v-btn>
              <v-btn color="red" @click="deleteItem()">Delete</v-btn>
            </v-card-actions>
          </v-card>
        </v-dialog>
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
          disabled: true,
          href: '/dashboard',
        }
      ],
      searchQuery: '',
      cartItems: [],
      deleteDialog: false,
      deleteIndex: null
    };
  },
  computed: {
    checkedTotalItems() {
      return this.cartItems.reduce((acc, item) => item.checked ? acc + parseInt(item.quantity) : acc, 0);
    },
    checkedTotalPrice() {
      return this.cartItems.reduce((acc, item) => item.checked ? acc + (item.product.price * item.quantity) : acc, 0);
    }
  },
  mounted() {
    this.retrieveCart();
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
    incrementQuantity(index) {
      this.cartItems[index].quantity++;
      this.updateQuantity(this.cartItems[index]);
    },
    decrementQuantity(index) {
      if (this.cartItems[index].quantity > 1) {
        this.cartItems[index].quantity--;
        this.updateQuantity(this.cartItems[index]);
      } else {
        console.warn('Quantity cannot be less than 1');
      }
    },
    confirmDelete(index) {
      this.deleteIndex = index;
      this.deleteDialog = true;
    },
    async retrieveCart() {
      try {
        const response = await axios.get(BASE_URL + '/carts', {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('access_token')
          }
        });
        this.cartItems = response.data;
      } catch (error) {
        console.error('Error retrieving cart data:', error);
      }
    },
    updateQuantity(item) {
      setTimeout(() => {
        axios.put(BASE_URL + '/carts', {
          id: item.id,
          quantity: item.quantity
        }, {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('access_token')
          }
        })
          .then(response => {
            console.log('Quantity updated:', response.data);
            this.retrieveCart();
          })
          .catch(error => {
            console.error('Error updating quantity:', error);
          });
      }, 4000);
    },
    checkout() {
      const checkedItems = this.cartItems.filter(item => item.checked);
      checkedItems.forEach(item => {
        axios.put(BASE_URL + '/carts', {
          id: item.id,
          quantity: item.quantity,
          selected: true
        }, {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('access_token')
          }
        })
          .then(response => {
            this.$router.push('/checkout');
          })
          .catch(error => {
            console.error('Error updating quantity:', error);
          });
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
  