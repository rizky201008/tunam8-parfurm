<!-- Catalogue.vue -->
<template>
  <Navbar>
    <div>
      <div class="container px-4 py-2">
        <Breadcrumbs class="d-flex align-items-center" :items="breadcrumbsItems" />
        <div class="col-md-12 bg-white">
          <div class="row px-3">
            <div :class="{ 'col': true, 'tablist': true, 'tab-active': activeTab === 'Semua' }"
              @click="toggleTab('Semua')">
              Semua
            </div>
            <div :class="{ 'col': true, 'tablist': true, 'tab-active': activeTab === 'Belum Bayar' }"
              @click="toggleTab('Belum Bayar')">
              Belum Bayar
            </div>
            <div :class="{ 'col': true, 'tablist': true, 'tab-active': activeTab === 'Proses' }"
              @click="toggleTab('Proses')">
              Proses
            </div>
            <div :class="{ 'col': true, 'tablist': true, 'tab-active': activeTab === 'Dikirim' }"
              @click="toggleTab('Dikirim')">
              Dikirim
            </div>
            <div :class="{ 'col': true, 'tablist': true, 'tab-active': activeTab === 'Diterima' }"
              @click="toggleTab('Diterima')">
              Diterima
            </div>
            <div :class="{ 'col': true, 'tablist': true, 'tab-active': activeTab === 'Dibatalkan' }"
              @click="toggleTab('Dibatalkan')">
              Dibatalkan
            </div>
          </div>
        </div>

        <div class="col-md-12 bg-white mt-4">
          <div class="row px-3">
            <div class="col-3">
              <a style=" font-size: 18px;"> ID Transaction</a>
            </div>
            <div class="col-3">
              <a style=" font-size: 18px;"> Tanggal Pemesanan</a>
            </div>
            <div class="col-3">
              <a style=" font-size: 18px;"> Total</a>
            </div>
            <div class="col-3 d-flex justify-content-center align-items-center">
              <a style=" font-size: 18px;"> Status </a>
            </div>
          </div>
        </div>
        <div class="col-md-12 bg-white mt-2" v-for="(transaction, index) in transactions" :key="index"
          style="cursor: pointer;">
          <div class="row px-3 ls-transaction" @click="goToTransactionDetail(transaction.id)">
            <div class="col-3">
              <a style="font-size: 18px; font-weight: bold;">#{{ transaction.id }}</a>
            </div>
            <div class="col-3">
              <a style="font-size: 18px;">{{ formatDate(transaction.created_at) }}</a>
            </div>
            <div class="col-3">
              <a style="font-size: 18px;">Rp. {{ formatPrice(transaction.total) }}</a>
            </div>
            <div class="col-3 d-flex justify-content-center align-items-center">
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
import moment from 'moment';

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
      activeTab: 'Semua',
    };
  },
  computed: {
    filteredTransactions() {
      if (this.selectedTab === 'Semua') {
        return this.transactions;
      } else {
        return this.transactions.filter(transaction => transaction.status === this.selectedTab);
      }
    }
  },
  mounted() {
    this.getUser();
    this.getTransactions();
    console.log(this.id)
  },
  methods: {
    formatDate(data_date) {
      return moment.utc(data_date).format('YYYY-MM-DD')
    },
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
    toggleTab(tab) {
      // Update activeTab state
      this.activeTab = tab;

      // Call getTransactions method with appropriate status based on the selected tab
      switch (tab) {
        case 'Belum Bayar':
          this.getTransactions('unpaid');
          break;
        case 'Proses':
          this.getTransactions('proccess');
          break;
        case 'Dikirim':
          this.getTransactions('shipping');
          break;
        case 'Diterima':
          this.getTransactions('received');
          break;
        case 'Dibatalkan':
          this.getTransactions('canceled');
          break;
        default:
          this.getTransactions(); // For default tab 'Semua', no status parameter is passed
      }
    },
    async getTransactions(status = '') {
      try {
        const token = localStorage.getItem('access_token');
        const response = await axios.get(BASE_URL + '/transactions', {
          params: {
            status: status
          },
          headers: {
            Authorization: 'Bearer ' + token
          }
        });
        this.transactions = response.data;
      } catch (error) {
        console.error('Error fetching transactions:', error);
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
      } else if (status === 'proccess') {
        return 'proccess';
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
  text-decoration: none;
  padding-top: 10px;
  padding-bottom: 10px;
  display: flex;
  justify-content: center;
  align-items: center;
  cursor: pointer;
  color: black;
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
}
</style>
  