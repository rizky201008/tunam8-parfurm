<template>
  <Navbar /> <!-- Include the Navbar component -->
  <div class="gradient-container container bg-white">
    <div class="row m-3 my-5">
      <div class="col-md-6 d-flex align-items-center justify-content-center">
        <div id="carouselExampleIndicators" class="carousel slide">
          <!-- Carousel Indicators -->
          <div class="carousel-indicators">
            <button v-for="(image, index) in images" :key="index" type="button"
              data-bs-target="#carouselExampleIndicators" :data-bs-slide-to="index" :class="{ active: index === 0 }"
              aria-label="Slide {{ index + 1 }}"></button>
          </div>

          <!-- Carousel Inner -->
          <div class="carousel-inner">
            <div v-for="(image, index) in images" :key="index" class="carousel-item" :class="{ active: index === 0 }">
              <img :src="image.src" class="d-block w-100" :alt="'Slide ' + (index + 1)">
            </div>
          </div>

          <!-- Carousel Controls -->
          <button class="carousel-control-prev" type="button" data-bs-target="#carouselExampleIndicators"
            data-bs-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Previous</span>
          </button>
          <button class="carousel-control-next" type="button" data-bs-target="#carouselExampleIndicators"
            data-bs-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="visually-hidden">Next</span>
          </button>
        </div>
      </div>
      <div class="col-md-6 d-flex align-items-center justify-content-center">
        <div class="mx-auto">
          <h1 style=" font-weight: bold;">WE ARE TUNAM8</h1>
          <h2 style=" font-weight: bold;">Discover Our Product</h2>
          <h2 style=" font-weight: bold;">Browse Your Favourite Perfume On Our Site!</h2>
          <button @click="scrollToNextSection" class="button-set default mt-2" style="font-weight: bold;">JELAJAHI
            KAMI</button>
        </div>
      </div>
    </div>
    <div class="row mt-5 my-5 m-5 d-flex justify-content-center align-items-center">
      <div class="col-md-6 my-auto">
        <div class="d-flex justify-content-between">
          <div class="image-frame">
            <img src="../assets/LandingPage/tunam8_1.jpg" class="rounded" alt="Oscar">
          </div>
          <div class="image-frame d-flex justify-content-center">
            <img src="../assets/LandingPage/tunam8_1.jpg" class="rounded" alt="Oscar">
          </div>
        </div>
      </div>
      <!-- Right column for the image -->
      <div class="col-md-6 text-black">
        <h1>Toko Parfum Terlengkap dan Terjangkau di kawasan Palangkaraya</h1>
        <p>Kami menyediakan berbagai jenis dan tipe parfum
        </p>
        <ul class="justify-content-start" style="list-style: none;">
          <li class="py-2">
            <span class="list-icon"><img src="https://i.ibb.co/Tcwn0PN/service-list.png" alt="service-list"></span>
            <p class="my-auto m-2" style="display: inline-block; vertical-align: middle;">Harga
              Murah!</p>
          </li>
          <li class="py-2">
            <span class="list-icon"><img src="https://i.ibb.co/Tcwn0PN/service-list.png" alt="service-list"></span>
            <p class="my-auto m-2" style="display: inline-block; vertical-align: middle;">Stok
              Selalu Tersedia</p>
          </li>
          <li class="py-2">
            <span class="list-icon"><img src="https://i.ibb.co/Tcwn0PN/service-list.png" alt="service-list"></span>
            <p class="my-auto m-2" style="display: inline-block; vertical-align: middle;">
              Layanan
              Antar</p>
          </li>
          <li class="py-2">
            <span class="list-icon"><img src="https://i.ibb.co/Tcwn0PN/service-list.png" alt="service-list"></span>
            <p class="my-auto m-2" style="display: inline-block; vertical-align: middle;">
              Pembayaran
              bisa via COD</p>
          </li>
          <li class="py-2">
            <span class="list-icon"><img src="https://i.ibb.co/Tcwn0PN/service-list.png" alt="service-list"></span>
            <p class="my-auto m-2" style="display: inline-block; vertical-align: middle;">
              Garansi 1
              bulan</p>
          </li>
        </ul>
      </div>
    </div>
    <div class="row m-3">
      <div>
        <h1 class="text-right">Find Us on</h1>
        <hr>
      </div>
      <div class="row">
        <div class="col-md-4">
          <iframe
            src="https://www.google.com/maps/embed?pb=!1m17!1m12!1m3!1d263.5697728083286!2d113.91026973777512!3d-2.2215533977149895!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m2!1m1!2zMsKwMTMnMTcuOCJTIDExM8KwNTQnMzcuNyJF!5e0!3m2!1sid!2sid!4v1707033163216!5m2!1sid!2sid"
            width="100%" height="100%" style="border:0;" allowfullscreen="" loading="lazy"
            referrerpolicy="no-referrer-when-downgrade"></iframe>
        </div>
        <div class="col-md-8">
          <h3>Tunam8 Parfum </h3>
          <p>Jl. Sisingamangaraja I, Menteng, Kec. Jekan Raya, Kota Palangka Raya, Kalimantan Tengah 73111</p>

          <p>Connect with us</p>
          <ul class="list-unstyled">
            <li class="mt-2 d-flex align-items-center" href="https://instagram.com/tunam8_parfum.sisinga">
              <a href="https://instagram.com/tunam8_parfum.sisinga" target="_blank" rel="noopener noreferrer">
                <img src="https://i.ibb.co/TqJsYch/icon-instagram.png" alt="icon-instagram" border="0" class="mx-2">
                Instagram
              </a>
            </li>
            <li class="mt-2 d-flex align-items-center">
              <img src="https://i.ibb.co/3TQ1Bgh/icon-facebook.png" alt="icon-facebook" border="0" class="mx-2">
              Facebook
            </li>
            <li class="mt-2 d-flex align-items-center">
              <img src="https://i.ibb.co/Dfw5cLB/icon-twitter.png" alt="icon-twitter" border="0" class="mx-2">
              Twitter
            </li>
            <li class="mt-2 d-flex align-items-center">
              <img src="https://i.ibb.co/tY0TB5Z/icon-mail.png" alt="icon-mail" border="0" class="mx-2">
              Email
            </li>
          </ul>
        </div>
      </div>
    </div>
  </div>
</template>
<script>
import Navbar from '@/components/HomeNavbar.vue';
import image1 from '@/assets/LandingPage/Perfume2.png'
import image2 from '@/assets/LandingPage/Perfume3.png'
import image3 from '@/assets/LandingPage/Perfume1.png'
export default {
  name: 'HomePage',
  data() {
    return {
      currentIndex: 1,
      images: [
        { src: image1, height: 350, width: 300 },
        { src: image2, height: 350, width: 300 },
        { src: image3, height: 350, width: 300 }
        // Add more images as needed
      ]
    };
  },
  components: {
    Navbar,
  },
  methods: {


  },
  mounted() {
    setInterval(this.nextImage, 5000);
    const scrollWrapper = this.$refs.scrollWrapper;
    if (scrollWrapper) {
      scrollWrapper.addEventListener('scroll', this.handleScroll);
    }
  },
  beforeDestroy() {
    const scrollWrapper = this.$refs.scrollWrapper;
    if (scrollWrapper) {
      scrollWrapper.removeEventListener('scroll', this.handleScroll);
    }
  },
};
</script>
<style scoped>
@import url(https://fonts.googleapis.com/css?family=Questrial);
@import url(https://fonts.googleapis.com/css?family=Mooli);


.gallerywrapper {
  width: 640px;
  height: 450px;
  margin: 0 auto;
  position: relative;
  font-family: verdana, arial, sans-serif;
}

.gallerywrapper .gallery {
  position: absolute;
  left: 0;
  top: 0;
  height: 450px;
  width: 640px;
  overflow: hidden;
  text-align: center;
}

.gallerywrapper .gallery div {
  width: 640px;
  height: 900px;
  padding-top: 10px;
  position: relative;
}

.gallerywrapper .gallery div img {

  margin: 0 auto;
  border: 0;
}

.gallerywrapper .gallery div h3 {
  padding: 10px 0 0 0;
  margin: 0;
  font-size: 18px;
}

.gallerywrapper #gallery div p {
  padding: 5px 0;
  margin: 0;
  font-size: 12px;
  line-height: 18px;
}

.gallery .previous {
  display: inline;
  float: left;
  margin-left: 80px;
  text-decoration: none;
}

.gallery .next {
  display: inline;
  float: right;
  margin-right: 80px;
  text-decoration: none;
}

.list-unstyled li a {
  text-decoration: none;
  /* Remove underline */
  color: inherit;
  /* Use the default text color */
}

.list-unstyled li a:hover {
  /* Optional: Add hover styling if desired */
  text-decoration: underline;
  /* Underline on hover, for example */
}

.cursive {
  font-family: 'Mooli', sans-serif;
  text-transform: none;
}

.gradient-container {
  /* background-image: url('../assets/LandingPage/Background.png'); */
  width: 100%;
  margin-top: 100px;
}

.scroll-wrapper {
  scroll-snap-type: y proximity;
  overflow-y: scroll;
  height: 100vh;
  scroll-behavior: smooth;
  width: 100vw;
  /* Set the width to 100vw */
}


/* .welcome-section {
  height: 100vh;
  scroll-snap-align: start;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  background-image: url('../assets/LandingPage/main-bg.png');
} */


.third-section {
  padding: 20px;
  height: 100vh;
  background-color: white;
  scroll-snap-align: none;
}

.third-section .col-md-6 {
  margin-bottom: 20px;
  background-color: white;
}


.image-frame {
  width: 48%;
  overflow: hidden;
  position: relative;
  border-radius: 10px;
}

.image-frame img {
  width: 100%;
  height: auto;
  object-fit: cover;
}


.button-set {
  display: inline-block;
  padding: 10px 20px;
  text-align: center;
  text-decoration: none;
  font-size: 16px;
  cursor: pointer;
  transition: background-color 0.3s, color 0.3s;
}

.default {
  border: 2px solid black;
  color: #3498db;
  background-color: #fff;
}

.default-set:hover {
  background-color: #3498db;
  color: #fff;
}

@media only screen and (min-width: 950px) {

  .dekstop-view {
    color: white;

    .default {
      border: 2px solid white;
      color: #3498db;
      background-color: #fff;
    }

  }
}


@media only screen and (max-width: 950px) {
  .third-section {
    margin-bottom: 800px;
    height: 100vh;
    background-color: white;
  }

  .home-photo {
    width: 400px;

  }

  .mobile-view {
    margin-top: 15px;
    padding-left: 20px;
    color: black;
  }
}
</style>