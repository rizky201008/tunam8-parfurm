<!-- Catalogue.vue -->
<template>
  <Navbar>
    <div>
      <div class="container px-4 py-2">
        <Breadcrumbs class="d-flex align-items-center" :items="breadcrumbsItems" />
        <v-dialog v-model="showDialog" hide-overlay persistent width="300" lazy>
          <v-progress-circular indeterminate color="red" :size="90" class="mb-0"
            style="margin: auto;"></v-progress-circular>
        </v-dialog>
        <div v-if="loading === false">
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
                <br>
                <a style="font-size: 20px;">Harga Ongkos Kirim</a>
              </div>
              <div class="col-md-9">
                <a style="font-size: 20px;">: {{ formatDate(transaction.created_at) }}</a>
                <br>
                <a style="font-size: 20px;">: Rp. {{ formatPrice(transaction.total) }}</a>
                <br>
                <a style="font-size: 20px;">: {{ transaction.tracking_number }}</a>
                <br>
                <a style="font-size: 20px;">: Rp. {{ formatPrice(transaction.cost) }}</a>
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
          <div class="col-md-12 bg-white p-4 mt-2 mb-4" v-if="transaction.status === 'unpaid'">
            <div class="row px-3 border-bottom">
              <h4>Bayar</h4>
            </div>
            <div class="row px-3 pt-2">
              <div class="col-md-4">
                Total Belum Bayar:
              </div>
              <div class="col-md-6">
                <a style="font-size: 20px; font-weight: bold;">: Rp. {{ formatPrice(transaction.total) }}</a>
              </div>
              <div class="col-md-2">
                <v-btn color="red" rounded="xl" @click="showTermsDialog" class="mx-4 mb-2 mt-2">
                  Bayar
                </v-btn>
              </div>
            </div>
          </div>
          <div class="col-md-12 bg-white p-4 mt-2 mb-4" v-if="transaction.status === 'shipping'">
            <div class="row px-3 border-bottom">
              <h4>Konfirmasi Barang Diterima</h4>
            </div>
            <div class="row px-3 pt-2">
              <div class="col-md-4">
                Total Harga:
              </div>
              <div class="col-md-6">
                <a style="font-size: 20px; font-weight: bold;">: Rp. {{ formatPrice(transaction.total) }}</a>
              </div>
              <div class="col-md-2">
                <v-btn color="green" rounded="xl" @click="barangDiterima" class="mx-4 mb-2 mt-2">
                  Barang Diterima
                </v-btn>
              </div>
            </div>
          </div>
        </div>
        <v-dialog v-model="termsDialog" max-width="600px">
          <v-card>
            <v-card-title class="headline">Syarat dan Ketentuan</v-card-title>
            <v-card-text>
              <ol>
                <li>Pengembalian barang maksimal dilakukan pada saat status belum shipping</li>
                <li>Wajib melakukan video unboxing pada saat membuka paket!</li>
                <li>Refund hanya diberikan sebesar 80% dari total transaksi yang dibatalkan</li>
              </ol>
            </v-card-text>
            <v-card-actions>
              <v-spacer></v-spacer>
              <v-btn color="green darken-1" text @click="acceptTerms">Saya Setuju</v-btn>
              <v-btn color="red darken-1" text @click="termsDialog = false">Batal</v-btn>
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
      loading: true,
      termsDialog: false,
      showDialog: false,
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
    this.showDialog = true;
    this.getAddress().then(() => {
      this.getTransactionDetails();
    }).finally(() => {
      this.showDialog = false;
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
    showTermsDialog() {
      this.termsDialog = true;
    },
    acceptTerms() {
      this.termsDialog = false;
      window.open(this.transaction.transaction_payment.link, '_blank');
    },
    async barangDiterima() {
      try {
        const transactionId = this.$route.params.id;
        const response = await axios.put(BASE_URL + `/transactions`, {
          id: transactionId,
          status: 'received'
        }, {
          headers: {
            Authorization: "Bearer " + localStorage.getItem('access_token')
          }
        });
        this.transaction.status = 'received';
        this.$notify({
          type: 'success',
          title: 'Success',
          text: 'Barang terkonfirmasi diterima',
          color: 'green'
        });
        console.log('Transaction marked as received:', response.data);
      } catch (error) {
        console.error('Error marking transaction as received:', error);
      }
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
          // Ensure item.product is defined and has images
          if (item.product && item.product.images && item.product.images.length > 0) {
            item.product.image = item.product.images[0].link;
          } else {
            item.product.image = 'default_image_link'; // Fallback image if no images are available
          }
        });
      } catch (error) {
        console.error('Error fetching transaction details:', error);
      } finally {
        this.loading = false
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
      } else if (status === 'canceled') {
        return 'canceled';
      } else {
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