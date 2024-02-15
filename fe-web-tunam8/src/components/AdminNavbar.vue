<template>
    <div class="transition-content" :class="{ pushMainContent: isActive }">
        <div>
            <div v-if="Role === 'admin'">
                <div id="mySidenav" class="sidenav shadow" :class="{ openNavClass: isActive }">
                    <a class="closebtn" @click="isActive = !isActive" style="cursor: pointer;">&times;</a>
                    <a href="/admin/dashboard"><v-icon icon="mdi-home"></v-icon>&nbsp;Home</a>
                    <a href="/admin/daftarproduk"><v-icon icon="mdi-invoice-list"></v-icon>&nbsp;Daftar Produk</a>
                    <a href="/admin/kelolapelanggan"><v-icon icon="mdi-account"></v-icon>&nbsp;Kelola Pelanggan</a>
                    <a @click="onLogout" style="cursor:pointer; align-self: flex-end; bottom: 20px; position: fixed;"><v-icon
                            icon="mdi-run"></v-icon>Logout</a>
                </div>
            </div>

            <div v-else>
                <div id="mySidenav" class="sidenav shadow" :class="{ openNavClass: isActive }">
                    <a class="closebtn" @click="isActive = !isActive" style="cursor: pointer;">&times;</a>
                    <a href="/dashboard"><v-icon icon="mdi-home"></v-icon>&nbsp;Home</a>
                    <a href="/keranjang"><v-icon icon="mdi-cart"></v-icon>&nbsp;Keranjang</a>
                    <a @click="onLogout" style="cursor:pointer; align-self: flex-end; bottom: 60px; position: fixed;"><v-icon
                            icon="mdi-account"></v-icon>&nbsp;My Profile</a>
                    <a @click="onLogout" style="cursor:pointer; align-self: flex-end; bottom: 20px; position: fixed;"><v-icon
                            icon="mdi-run"></v-icon>&nbsp;Logout</a>

                </div>
            </div>
            <div class="content">
                <div class="button-side">
                    <nav class="navbar navbar-expand-lg navbar-light white bgnav shadow-sm rounded">
                        <span style="font-size: 25px; cursor: pointer;" @click="isActive = !isActive"
                            class="text-white">&#9776;</span>
                        <a class="navbar-brand text-white" href="/" style="margin-left: 15px;">Tunam8</a>
                        <div class="ms-auto mx-2">
                            <button class="navbar-toggler" type="button" data-bs-toggle="collapse"
                                data-bs-target="#navbarSupportedContent" aria-controls="navbarSupportedContent"
                                aria-expanded="false" aria-label="Toggle navigation">
                                <ion-icon name="person"></ion-icon>
                            </button>
                            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                                <div class="div">
                                    <ul class="navbar-nav mb-2 mb-lg-0">
                                        <li class="nav-item dropdown">
                                            <button class="mr-2 text-white" data-bs-toggle="dropdown" aria-expanded="false"
                                                style="font-size: 16px;" disabled>
                                                <b><v-icon icon="mdi-account"></v-icon>{{ username }}</b>
                                            </button>
                                            <!-- <ul class="dropdown-menu dropdown-menu-end">
                                                <li><a class="dropdown-item" @click="profile"><ion-icon
                                                            name="person"></ion-icon>&nbsp;Profile</a>
                                                </li>
                                                <li><a class="dropdown-item" @click="onLogout()"
                                                        style="cursor: pointer;"><ion-icon
                                                            name="log-out"></ion-icon>&nbsp;Logout</a></li>
                                            </ul> -->
                                        </li>
                                    </ul>
                                </div>
                            </div>
                        </div>
                    </nav>
                </div>
                <slot>
                </slot>
            </div>
        </div>
    </div>
</template>
  
    
<script>
import axios from 'axios';
const BASE_URL = import.meta.env.VITE_BASE_URL_API
import Footer from '@/components/Vuetify/Footer.vue';
export default {
    name: 'AdminNavbar',
    data() {
        return {
            isActive: false,
            username: '',
            id: '',
            Role: null
        };
    },
    components: {
    Footer
  },
    async mounted() {
        try {
            const response = await axios.get(BASE_URL + '/user/detail', {
                headers: {
                    Authorization: "Bearer " + localStorage.getItem('access_token')
                }
            });
            this.id = response.data.id
            this.username = response.data.name
            this.Role = response.data.role;
        } catch (error) {
            console.error(error);

            if (error.response && error.response.data.message) {
                const errorMessage = error.response.data.message;
                // Display notification with red color
                this.$notify({
                    type: 'error',
                    title: 'Error',
                    text: errorMessage,
                    color: 'red'
                });
            }
        }
    },
    methods: {
        profile() {
            const id = this.id
            this.$router.push({ path: `/profile/${id}` })
        },
        onLogout() {
            axios.post(BASE_URL + '/account/logout', {}, {
                headers: {
                    Authorization: "Bearer " + localStorage.getItem('access_token'),
                }
            })
                .then(response => {
                    localStorage.removeItem('access_token');
                    this.$router.push('/login');
                })
                .catch(error => {
                    console.error(error);
                });
        },
    },
};

</script>
    
<style scoped>
.bgnav {
    /* background: url("../../../src/assets/Navbar/blue_ocean.png"); */
    background-color: #D0011B;
}

.navbar {
    padding-left: 15px;
}

.navbar-brand {
    font-family: 'Merriweather', serif;
    font-weight: light;
    font-size: 25px;
    color: black;
}

.sidenav {
    height: 100%;
    width: 0;
    position: fixed;
    z-index: 1;
    top: 0;
    left: -250px;
    background-color: #D0011B;
    overflow-x: hidden;
    transition: 0.5s;
    padding-top: 60px;
    width: 250px;
}

.sidebar-link {
    padding: 16px 8px;
    text-decoration: none;
    color: black;
    display: block;
    border-bottom: 1px solid transparent;
    /* Initial border */
}

.sidebar-link:hover {
    background-color: rgba(255, 165, 5, 0.2);
    /* Light 20% opacity highlight */
    border-color: rgba(0, 0, 0, 0.1);
    /* Highlight border color */
}

.openNavClass {
    left: 0;
}

.transition-content {
    transition: margin-left .5s;
    flex-grow: 1;
    /* Let the content grow to fill remaining space */
}

.pushMainContent {
    margin-left: 250px;
}

@media screen and (max-width: 768px) {
    .pushMainContent {
        margin-left: 0;
    }
}

.sidenav a {
    padding: 8px 8px 8px 32px;
    text-decoration: none;
    font-size: 18px;
    color: #ffffff;
    display: block;
    transition: 0.3s;
}

.sidenav a:hover {
    color: #f1f1f1;
}

.sidenav .closebtn {
    position: absolute;
    top: 0;
    right: 25px;
    font-size: 36px;
    margin-left: 50px;
}

.button-side {
    padding: 16px;

}

.overlay {
    position: fixed;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0, 0, 0, 0.6);
    /* Change the alpha value to adjust transparency */
    display: none;
    z-index: 0;
}

.showOverlay {
    display: block;
    z-index: 1;
}

@media screen and (max-height: 450px) {
    .sidenav {
        padding-top: 15px;
    }

    .sidenav a {
        font-size: 18px;
    }
}

.content {
    min-height: 100vh;

    /* background: url("../../../src/assets/LandingPage/Background.png"); */
    background-color: #F5F5F5;
    background-position: center;
    background-size: cover;
}
</style>
    