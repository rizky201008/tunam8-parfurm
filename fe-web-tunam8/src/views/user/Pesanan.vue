<!-- Catalogue.vue -->
<template>
  <Navbar>
    <div>
      <div class="container px-4 py-2">
        <Breadcrumbs class="d-flex align-items-center" :items="breadcrumbsItems" />
        <div class="col-md-12 bg-white">
          <div class="row px-3">
            <div class="col tablist tab-active">
              Semua
            </div>
            <div class="col tablist">
              Belum Bayar
            </div>
            <div class="col tablist">
              Proses
            </div>
            <div class="col tablist">
              Dikirim
            </div>
            <div class="col tablist">
              Diterima
            </div>
            <div class="col tablist">
              Dibatalkan
            </div>
          </div>
        </div>

        <div class="col-md-12 bg-white mt-4">
          <div class="row px-3">
            <div class="col-4">
              <a style=" font-size: 18px;"> ID Transaction</a>
            </div>
            <div class="col-4">
              <a style=" font-size: 18px;"> Total</a>
            </div>
            <div class="col-4 d-flex justify-content-center align-items-center">
              <a style=" font-size: 18px;"> Status </a>
            </div>
          </div>
        </div>
        <div class="col-md-12 bg-white mt-2" v-for="(transaction, index) in transactions" :key="index"
          style="cursor: pointer;">
          <div class="row px-3 ls-transaction" @click="goToTransactionDetail(transaction.id)">
            <div class="col-4">
              <a style="font-size: 18px; font-weight: bold;">#{{ transaction.id }}</a>
            </div>
            <div class="col-4">
              <a style="font-size: 18px;">Rp. {{ formatPrice(transaction.total) }}</a>
            </div>
            <div class="col-4 d-flex justify-content-center align-items-center">
              <a :class="getClassForStatus(transaction.status)" style="font-size: 18px;">{{ transaction.status }}</a>
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

      ],
      transactions: [],
    };
  },
  mounted() {
    this.getUser();
    this.getTransactions();
    console.log(this.id)
  },
  methods: {
    formatPrice(price) {
      const numericPrice = parseFloat(price);
      return numericPrice.toLocaleString('id-ID');
    },
    async getUser() {
      try {
        const response = await axios.get(BASE_URL + '/user/detail', {
          headers: {
            Authorization: "Bearer " + localStorage.getItem('access_token')
          }
        });
        this.ids = response.data.id
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
    async getTransactions() {
      try {
        const token = localStorage.getItem('access_token');
        const response = await axios.get(BASE_URL + '/transactions', {
          headers: {
            Authorization: 'Bearer ' + token
          }
        });
        this.transactions = response.data;
      } catch (error) {
        console.error('Error fetching addresses:', error);
      }
    },
    goToTransactionDetail(id) {
      this.$router.push({ path: `/pesanan/` + id });
    },
    getClassForStatus(status) {
      if (status === 'unpaid') {
        return 'unpaid';
      } else if (status === 'shipping') {
        return 'shipping';
      } else if (status === 'received') {
        return 'received';
      } else if (status === 'canceled') {
        return 'canceled';
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

.proccess {
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
  background-color: orange;
}

.canceled {
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
}</style>
  