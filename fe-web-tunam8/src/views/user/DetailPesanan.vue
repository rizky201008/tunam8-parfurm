<!-- Catalogue.vue -->
<template>
  <Navbar>
    <div>
      <div class="container px-4 py-2">
        <Breadcrumbs class="d-flex align-items-center" :items="breadcrumbsItems" />
        <div class="col-md-12 bg-white p-4">
          <div class="row px-3 border-bottom">
            <div class="col-3">
              <v-chip class="mt-3">{{ transaction.status }}</v-chip>
            </div>
            <h4>#{{ transaction.id }}
            </h4>
          </div>
          <div class="row px-3 pt-2">
            <div class="col-md-3">
              <a style="font-size: 20px;">Order Date</a>
              <br>
              <a style="font-size: 20px;">Total Harga</a>
              <br>
              <a style="font-size: 20px;">Nomor Resi</a>
            </div>
            <div class="col-md-9">
              <a style="font-size: 20px;">: {{ formatDate(transaction.created_at) }}</a>
              <br>
              <a style="font-size: 20px;">: Rp. {{ formatPrice(transaction.total) }}</a>
              <br>
              <a style="font-size: 20px;">: {{ transaction.tracking_number }}</a>
            </div>
          </div>
        </div>
        <div class="col-md-12 bg-white p-4 mt-2">
          <div class="row px-3 border-bottom">
            <h4>Alamat</h4>
          </div>
          <div class="row px-3 pt-2">
            <div class="col-md-3">
              <a style="font-size: 20px;">Nama Penerima</a>
              <br>
              <a style="font-size: 20px;">City</a>
              <br>
              <a style="font-size: 20px;">Alamat Detail</a>
            </div>
            <div class="col-md-9">
              <a style="font-size: 20px;">: {{ getAddressById(transaction.address_id).receiver }}</a>
              <br>
              <a style="font-size: 20px;">: {{ getAddressById(transaction.address_id).city }}</a>
              <br>
              <a style="font-size: 20px;">: {{ getAddressById(transaction.address_id).address_detail }}</a>
            </div>
          </div>
        </div>
        <div class="col-md-12 bg-white p-4 mt-2">
          <div class="row px-3 border-bottom">
            <h4>Produk Dibeli</h4>
          </div>
          <div class="row px-3 pt-2">
            <div v-for="item in transaction.transaction_items" :key="item.id" class="col-md-12 bg-white p-4 mt-2">
              <div class="row px-3 pt-2">
                <div class="col-md-3">
                  <img :src="item.product.image" alt="Product Image" class="product-image">
                </div>
                <div class="col-md-9 pt-2">
                  <a style="font-size: 20px; font-weight: bold;">{{ item.product.name }}</a>
                  <br>
                  <a style="font-size: 20px;">Price: Rp. {{ formatPrice(item.price) }}</a>
                  <br>
                  <a style="font-size: 20px;">Quantity: {{ item.qty }}</a>
                  <br>
                </div>
              </div>
            </div>
          </div>
        </div>
        <div class="col-md-12 bg-white p-4 mt-2 mb-4">
          <div class="row px-3 border-bottom">
            <h4>Bayar</h4>
          </div>
          <div class="row px-3 pt-2">

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
import moment from 'moment';
import Breadcrumbs from '@/components/Vuetify/Breadcrumbs.vue';

export default {
  name: 'Daftar',
  components: {
    Navbar,
    Breadcrumbs
  },
  data() {
    return {
      ids: '',
      breadcrumbsItems: [
        {
          title: 'Pesanan Saya',
          disabled: false,
          href: '/pesanan',
        },
        {
          title: 'Detail Pesanan',
          disabled: true,
          href: '/',
        }
      ],
      transaction: {},
      addresses: [],
      products: [],

    };
  },
  mounted() {
    this.getAddress().then(() => {
      this.getTransactionDetails();
    });
  },
  methods: {
    formatPrice(price) {
      const numericPrice = parseFloat(price);
      return numericPrice.toLocaleString('id-ID');
    },
    formatDate(data_date) {
      return moment.utc(data_date).format('YYYY-MM-DD')
    },
    async getTransactionDetails() {
      try {
        const transactionId = this.$route.params.id;
        const response = await axios.get(BASE_URL + `/transactions/${transactionId}`, {
          headers: {
            Authorization: "Bearer " + localStorage.getItem('access_token')
          }
        });
        this.transaction = response.data;
        this.transaction.transaction_items.forEach(item => {
          item.product = this.getProductById(item.product_id);
        });
      } catch (error) {
        console.error('Error fetching transaction details:', error);
      }
    },

    async getAddress() {
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
    getAddressById(id) {
      return this.addresses.find(address => address.id == id) || {};
    },
    getClassForStatus(status) {
      if (status === 'unpaid') {
        return 'unpaid';
      } else if (status === 'shipping') {
        return 'shipping';
      } else if (status === 'received') {
        return 'received';
      } else {
        // Add more conditions for other status if needed
        return '';
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

.ls-transaction {
  padding-top: 20px;
  padding-bottom: 20px;
}

.process {
  padding-right: 5px;
  padding-left: 5px;
  border-radius: 5px;
  color: black;
  font-weight: 600;
  text-decoration: none;
  background-color: yellow;
}

.shipping {
  padding-right: 5px;
  padding-left: 5px;
  border-radius: 5px;
  color: black;
  font-weight: 600;
  text-decoration: none;
  background-color: yellow;
}

.unpaid {
  padding-right: 5px;
  padding-left: 5px;
  border-radius: 5px;
  color: white;
  font-weight: 600;
  text-decoration: none;
  background-color: red;
}

.received {
  padding-right: 5px;
  padding-left: 5px;
  border-radius: 5px;
  color: 600;
  font-weight: bold;
  text-decoration: none;
  background-color: lightgreen;
}

.tablist {
  padding-top: 10px;
  padding-bottom: 10px;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
}

.tab-active {
  border-bottom: 5px solid #D0011B;
}

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

  max-width: 200px;
  max-width: 200px;
  object-fit: contain;
  margin: auto;
  padding: 20px;
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
  