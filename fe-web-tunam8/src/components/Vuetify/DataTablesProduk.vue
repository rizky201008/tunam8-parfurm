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
            <template v-slot:item.actions="{ item }">
                <v-icon size="large" class="me-2" @click="editBuku(item)">
                    mdi-pencil
                </v-icon>
                <v-icon size="large" @click="deleteProduct(item)">
                    mdi-delete
                </v-icon>
            </template>
            <template v-slot:no-data>
                <v-btn color="primary" @click="initialize">
                    Reset
                </v-btn>
            </template>
        </v-data-table>
    </v-card>
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
        }
    },
    computed: {
        getitems() {
            // Add a sequential number to each item
            return this.items.map((item, index) => {
                return { ...item, no: index + 1 };
            });
        },
    },
    mounted() {
        this.retrieveParfum();
    },
    methods: {
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
                    "Harga (Rp.)": item.price,
                    stok: item.stock,
                    actions: '',
                }));

                if (response.data.length > 0) {
                    this.fotoUrl = response.data[0].foto;
                }
            } catch (error) {
                console.error(error);
            }
        },
        async deleteProduct(item) {
            try {
                const id = item.id
                const response = await axios.delete(BASE_URL + '/products', {
                    headers: {
                        Authorization: 'Bearer ' + localStorage.getItem('access_token'),
                    },
                    data: { id: id },
                });
                this.$notify({
                    type: 'success',
                    title: 'Success',
                    text: 'Parfum berhasil dihapus',
                    color: 'green'
                });
                this.retrieveParfum();
            } catch (error) {
                console.error(error);
            }
        },
        editBuku(item) {
            const id = item.id
            // console.log(id)
            this.$router.push({ path: `/admin/daftarbuku/editbuku/` + id })
        },
    }
}
</script>