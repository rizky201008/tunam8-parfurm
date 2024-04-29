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
          <div class="row px-3 py-2">
            <div class="col">
              <a style=" font-size: 18px;"> ID Transaction</a>
            </div>
            <div class="col">
              <a style=" font-size: 18px;"> User</a>
            </div>
            <div class="col">
              <a style=" font-size: 18px;"> Total</a>
            </div>
            <div class="col">
              <a style=" font-size: 18px;"> Tanggal</a>
            </div>
            <div class="col d-flex justify-content-center align-items-center">
              <a style=" font-size: 18px;"> Status </a>
            </div>
            <div class="col d-flex justify-content-center align-items-center">
              <a style=" font-size: 18px;"> Action </a>
            </div>
          </div>
        </div>
        <div class="col-md-12 bg-white mt-2" v-for="(transaction, index) in transactions" :key="index" style="cursor: pointer;">
          <div class="row px-3 ls-transaction" @click="goToTransactionDetail(transaction.id)">
            <div class="col">
              <a style="font-size: 18px; font-weight: bold;">#{{ transaction.id }}</a>
            </div>
            <div class="col">
              <a style="font-size: 18px;">{{ transaction.user.name }}</a>
            </div>
            <div class="col">
              <a style="font-size: 18px;">Rp. {{ formatPrice(transaction.total) }}</a>
            </div>
            <div class="col">
              <a style="font-size: 18px;">{{ formatDate(transaction.created_at) }}</a>
            </div>
            <div class="col d-flex justify-content-center align-items-center">
              <a :class="getClassForStatus(transaction.status)" style="font-size: 18px;">{{ transaction.status }}</a>
            </div>
            <div class="col d-flex justify-content-center align-items-cente">
              <v-icon size="large" class="me-2" @click.stop="editStatus(index)" color="blue">
                mdi-pencil-circle-outline
              </v-icon>
            </div>
          </div>
        </div>
        <v-dialog v-model="dialogForm" max-width="600">
          <v-card>
            <v-card-title>
              Update Transaction
            </v-card-title>
            <v-card-text>
              <v-select v-model="selectedStatus" :items="statusOptions" label="Status"></v-select>
              <v-text-field v-model="trackingNumber" label="Tracking Number"></v-text-field>
            </v-card-text>
            <v-card-actions>
              <v-btn color="blue darken-1" text @click="dialogForm = false">Cancel</v-btn>
              <v-btn color="blue darken-1" text @click="saveTransaction">Save</v-btn>
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
import moment from 'moment';

export default {
  name: 'DaftarBuku',
  components: {
    Navbar,
    Breadcrumbs
  },
  data() {
    return {
      breadcrumbsItems: [
        {
          title: 'Pesanan Saya',
          disabled: false,
          href: '/pesanan',
        },
      ],
      transactions: [],
      dialogForm: false,
      selectedTransactionIndex: null,
      selectedStatus: "",
      trackingNumber: "",
      activeTab: 'Semua',
    };
  },
  computed: {
    statusOptions() {
      return ['proccess', 'canceled', 'shipping', 'received', 'unpaid'];
    },
    filteredTransactions() {
      if (this.selectedTab === 'Semua') {
        return this.transactions;
      } else {
        return this.transactions.filter(transaction => transaction.status === this.selectedTab);
      }
    }
  },
  mounted() {
    this.getTransactions();
  },
  methods: {
    formatDate(data_date) {
      return moment.utc(data_date).format('YYYY-MM-DD')
    },
    formatPrice(price) {
      const numericPrice = parseFloat(price);
      return numericPrice.toLocaleString('id-ID');
    },
    async getTransactions(status = '') {
      try {
        const token = localStorage.getItem('access_token');
        const response = await axios.get(BASE_URL + '/all-transactions', {
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
    editStatus(index) {
      this.selectedTransactionIndex = index;
      this.selectedStatus = this.transactions[index].status;
      this.dialogForm = true;
    },
    saveTransaction() {
      const transactionId = this.transactions[this.selectedTransactionIndex].id;
      const requestData = {
        id: transactionId,
        status: this.selectedStatus,
        tracking_number: this.trackingNumber
      };
      axios.put(BASE_URL + '/transactions', requestData, {
        headers: {
          Authorization: 'Bearer ' + localStorage.getItem('access_token')
        }
      })
        .then(response => {
          console.log('Transaction status updated successfully:', response.data);
          this.$notify({
            type: 'success',
            title: 'Success',
            text: 'Status Updated',
            color: 'green'
          });
        })
        .catch(error => {
          console.error('Error updating transaction status:', error);
        });
      this.getTransactions();
      this.dialogForm = false;
    },
    getClassForStatus(status) {
      if (status === 'unpaid') {
        return 'unpaid';
      } else if (status === 'shipping') {
        return 'shipping';
      } else if (status === 'proccess') {
        return 'proccess';
      } else if (status === 'received') {
        return 'received';
      } else if (status === 'canceled') {
        return 'canceled';
      } else {
        return '';
      }
    },
    goToTransactionDetail(id) {
      this.$router.push({ path: `/admin/detailpesanan/` + id });
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
}
</style>
  