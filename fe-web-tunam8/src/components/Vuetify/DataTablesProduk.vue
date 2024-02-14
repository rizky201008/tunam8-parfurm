<template>
    <v-card flat>
        <v-card-title class="d-flex align-center pe-2">
            <v-icon icon="mdi-invoice-list"></v-icon> &nbsp;
            Daftar Parfum
            <v-spacer></v-spacer>

            <v-text-field v-model="search" prepend-inner-icon="mdi-magnify" density="compact" label="Search" single-line
                flat hide-details variant="solo-filled"></v-text-field>
        </v-card-title>

        <v-divider></v-divider>
        <v-data-table v-model:search="search" :items="items" item-key="item.id">
            <template v-slot:item.id="{ item }">
                <div v-if="false">{{ item.id }}</div>
            </template>
            <template v-slot:header.id>
            </template>
            <template v-slot:item.slug="{ item }">
                <div v-if="false">{{ item.slug }}</div>
            </template>
            <template v-slot:header.slug>
            </template>
            <template v-slot:item.no="{ item }">
                <div class="text-start">
                    {{ item.no }}
                </div>
            </template>
            <template v-slot:item.images="{ item }">
                <v-card class="my-2" elevation="2" rounded>
                    <v-img :src="item.images" height="100%" cover></v-img>
                </v-card>
            </template>
            <template v-slot:item.category="{ item }">
                <v-chip>{{ item.category.name }}</v-chip>
            </template>
            <template v-slot:item.actions="{ item }">
                <v-icon size="large" class="me-2" @click="showModal(item)" color="green">
                    mdi-eye-circle-outline
                </v-icon>
                <v-icon size="large" class="me-2" @click="editProduk(item)" color="blue">
                    mdi-pencil
                </v-icon>
                <v-icon size="large" @click="confirmDelete(item)" color="red">
                    mdi-delete
                </v-icon>
            </template>
            <template v-slot:no-data>
                <v-btn color="primary" @click="initialize">
                    Reset
                </v-btn>
            </template>
        </v-data-table>
        <v-dialog v-model="dialogDelete" max-width="500px">
            <v-card title="Dialog">
                <v-card-text>
                    Are you sure you want to delete this item?
                </v-card-text>

                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn text @click="dialogDelete = false">Cancel</v-btn>
                    <v-btn color="red" @click="deleteProduct(item)">OK</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
    </v-card>
    <div class="modal fade" id="showProduct" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h1 class="modal-title fs-5" id="exampleModalLabel">Detail Product</h1>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"
                        id="closeModal"></button>
                </div>
                <div class="modal-body" style="max-height: 80vh; overflow-y: auto;">
                    <div class="row">
                        <h2>{{ selectedProduct.name }}</h2>
                    </div>
                    <hr>
                    <div class="col-12 pb-9">
                        <div v-if="selectedProduct.images" id="carouselExampleIndicators" class="carousel slide"
                            data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <div v-for="(image, index) in selectedProduct.images" :key="index"
                                    :class="{ 'carousel-item': true, 'active': index === 0 }">
                                    <img :src="image.link" class="d-block w-100" :alt="'Slide ' + (index + 1)"
                                        style="max-width: 450px; max-height: 350px; object-fit: contain;">
                                </div>
                            </div>
                            <div v-if="selectedProduct.images && selectedProduct.images.length > 1"
                                class="carousel-indicators">
                                <button v-for="(image, index) in selectedProduct.images" :key="index" type="button"
                                    :data-bs-target="'#carouselExampleIndicators'" :data-bs-slide-to="index"
                                    :class="{ 'active': index === 0 }" class="thumbnail"
                                    :aria-label="'Slide ' + (index + 1)">
                                    <img :src="image.link" class="d-block w-100" :alt="'Slide ' + (index + 1)"
                                        style="max-width: 100px; max-height: 80px; object-fit: contain;">
                                </button>
                            </div>
                        </div>
                    </div>
                    <div class="row mt-5">
                        <div class="col-12 mt-5">
                            <h5>Description : </h5>
                            <p>{{ selectedProduct.description }}</p>
                            <hr>
                            <p>Category:</p>
                            <v-chip v-if="selectedProduct.category">{{ selectedProduct.category.name }}</v-chip>
                            <hr>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Harga:</label>
                                        <div class="input-group">
                                            <span class="input-group-text">Rp</span>
                                            <input type="text" class="form-control" :value="selectedProduct.price" disabled>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="mb-3">
                                        <label class="form-label">Stok:</label>
                                        <input type="text" class="form-control" :value="selectedProduct.stock" disabled>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
<script>
import axios from 'axios';
const BASE_URL = import.meta.env.VITE_BASE_URL_API;

export default {
    data() {
        return {
            search: '',
            items: [],
            items_edit: [],
            fotoUrl: '',
            foto: '',
            selectedProduct: {},
            currentIndex: 0,
            dialogDelete: false,
        }
    },
    computed: {
        getitems() {
            // Add a sequential number to each item
            return this.items.map((item, index) => {
                return { ...item, no: index + 1 };
            });
        },
        shouldShowPreviousIcon() {
            return this.selectedProduct.images.length > 1 && this.currentIndex > 0;
        },
        shouldShowNextIcon() {
            return this.selectedProduct.images.length > 1 && this.currentIndex < this.selectedProduct.images.length - 1;
        }
    },
    mounted() {
        this.retrieveParfum();
    },
    methods: {
        //Promtp
        confirmDelete(item) {
            this.itemToDelete = item;
            // console.log(item.id)
            this.dialogDelete = true;
        },
        showImage(index) {
            this.currentIndex = (index + this.selectedProduct.images.length) % this.selectedProduct.images.length;
        },
        async showModal(item) {
            // Set the selected product
            this.selectedProduct = item;

            try {
                // Make a GET request to fetch the categories for the selected product
                const response = await axios.get(BASE_URL + '/product/' + item.slug, {
                    headers: {
                        Authorization: "Bearer " + localStorage.getItem('access_token')
                    }
                });

                // Update the selected product with the categories data
                this.selectedProduct = response.data;

                // Show the modal
                $('#showProduct').modal('show');
            } catch (error) {
                console.error(error);
            }
        },
        async retrieveParfum() {
            try {
                const response = await axios.get(BASE_URL + '/products', {
                    headers: {
                        Authorization: "Bearer " + localStorage.getItem('access_token')
                    }
                });

                this.items = response.data.data.map((item, index) => ({
                    id: item.id,
                    no: index + 1,
                    images: item.images.length > 0 ? item.images[0].link : '',
                    "Nama Parfum": item.name,
                    category: item.category ? item.category : { name: "Uncategorized" },
                    "Harga (Rp.)": "Rp." + Number(item.price).toLocaleString('id-ID'),
                    stok: item.stock,
                    slug: item.slug,
                    actions: '',
                }));

                if (response.data.length > 0) {
                    this.fotoUrl = response.data[0].foto;
                }
            } catch (error) {
                console.error(error);
            }
        },
        editProduk(item) {
            const slug = item.slug
            // console.log(id)
            this.$router.push({ path: `/admin/daftarproduk/editproduk/` + slug })
        },
        async deleteProduct(item) {
            try {
                const response = await axios.delete(BASE_URL + '/products', {
                    headers: {
                        Authorization: 'Bearer ' + localStorage.getItem('access_token'),
                    },
                    data: {
                        id: this.itemToDelete.id
                    }
                });
                this.$notify({
                    type: 'success',
                    title: 'Success',
                    text: 'Parfum berhasil dihapus',
                    color: 'green'
                });
                this.retrieveParfum();
                this.dialogDelete = false;
            } catch (error) {
                console.error(error);
            }
        },

    }
}
</script>

<style>
.carousel-indicators button.thumbnail {
    width: 100px;
}

.carousel-indicators button.thumbnail:not(.active) {
    opacity: 0.7;
}

.carousel-indicators {
    position: static;
}

@media screen and (min-width: 992px) {
    .carousel {
        max-width: 70%;
        margin: 0 auto;
    }
}
</style>