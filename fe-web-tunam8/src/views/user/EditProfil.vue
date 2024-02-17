<!-- Catalogue.vue -->
<template>
  <Navbar>
    <div class="dashboard-admin">
      <div class="container-fluid px-4 py-2">
        <Breadcrumbs class="d-flex align-items-center" :items="breadcrumbsItems" />
        <div class="row">
          <div class="col-md-12">
            <div class="card mb-4 pt-4 border-0 px-2">
              <div class="col px-4">
                <form @submit.prevent="saveParfum">
                  <div class="row">
                  </div>
                  <label for="judul">Nama</label>
                  <input class="form-control" type="text" v-model="users.name" id="judul" />
                  <br>
                  <label for="desc">Email</label>
                  <textarea class="form-control" v-model="users.email" id="desc"></textarea>
                  <br>
                  <button-custom class="btn btn-info mb-2" type="submit" @click="saveParfum">Save Parfum</button-custom>
                </form>
              </div>
            </div>
            <div class="card mb-4 pt-4 border-0 px-2">
              <div class="row">
                <div class="col-md-6 mx-2">
                  <a style="font-size: 28px; font-weight: bold; color: red; margin-bottom: 100px;">Personalize
                    Yourself</a>
                  <div v-for="(tag, index) in tags" :key="index" class="form-check"
                    style="font-size: 20px; margin-bottom: 10px;">
                    <input class="form-check-input" type="checkbox" :value="tag.id" v-model="selectedTags"
                      @change="handleCheckboxChange" style="width: 20px; height: 20px;">
                    <label class="form-check-label" :for="'checkbox_' + tag.id" style="padding-left: 10px;">{{ tag.name
                    }}</label>
                  </div>
                  <div class="col-md-6 my-4">
                    <button-custom class="btn btn-info mb-2" type="submit" @click="savePersonal">Save Your
                      Personalization</button-custom>
                  </div>
                </div>
              </div>
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
  name: 'EditBuku',
  components: {
    Navbar,
    Breadcrumbs
  },
  data() {
    return {
      users: {
        id: '',
        name: '',
        email: '',
      },
      tags: [],
      selectedTags: [],
      breadcrumbsItems: [
        {
          title: 'My Profile',
          disabled: false,
          href: '/profile',
        },
      ],
    }
  },
  async mounted() {
    try {
      const slug = this.$route.params.slug;
      const response = await axios.get(BASE_URL + '/user/detail', {
        headers: {
          Authorization: "Bearer " + localStorage.getItem('access_token')
        }
      });
      this.users = response.data
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
    };
    this.retrieveTags();
  },
  methods: {
    handleFileChange(event) {
      this.fotoFile = event.target.files[0];
    },
    async retrieveTags() {
      try {
        const response = await axios.get(BASE_URL + '/tags', {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('access_token')
          }
        });
        this.tags = response.data.data;
        await this.retrievePersonal();
      } catch (error) {
        console.error('Error fetching tags:', error);
      }
    },


    async retrievePersonal() {
      try {
        const response = await axios.get(BASE_URL + '/user/personal', {
          headers: {
            Authorization: 'Bearer ' + localStorage.getItem('access_token')
          }
        });

        if (response.data.tags && response.data.tags.length > 0) {
          const selectedTagIDs = response.data.tags.map(tagName =>
            this.tags.find(tag => tag.name === tagName)?.id
          ).filter(Boolean);
          this.selectedTags = selectedTagIDs;
        }
      } catch (error) {
        if (error.response && error.response.status === 404) {
          console.log('User has not personalized yet');
        } else {
          console.error('Error fetching personalization:', error);
        }
      }
    },

    async savePersonal() {
      try {
        const selectedTagNames = this.selectedTags.map(id =>
          this.tags.find(tag => tag.id === id).name
        );

        let response;
        try {
          response = await axios.get(BASE_URL + '/user/personal', {
            headers: {
              Authorization: 'Bearer ' + localStorage.getItem('access_token')
            }
          });
        } catch (error) {
          await axios.post(
            BASE_URL + '/user/personal',
            { tags: selectedTagNames },
            {
              headers: {
                Authorization: 'Bearer ' + localStorage.getItem('access_token')
              }
            }
          );

          console.log('Personalization created');
          this.$notify({
            type: 'success',
            title: 'Success',
            text: 'Personalization created successfully',
            color: 'green'
          });

          return;
        }

        if (response.data.tags) {
          await axios.put(
            BASE_URL + '/user/personal',
            { tags: selectedTagNames },
            {
              headers: {
                Authorization: 'Bearer ' + localStorage.getItem('access_token')
              }
            }
          );
          console.log('Personalization updated');
          this.$notify({
            type: 'success',
            title: 'Success',
            text: 'Personalization updated successfully',
            color: 'green'
          });
        }
      } catch (error) {
        console.error('Error saving personalization:', error);
      }
    },



    // async savePersonal() {
    //   try {
    //     const selectedTagNames = this.selectedTags.map(id => this.tags.find(tag => tag.id === id).name);


    //     const response = await axios.post(BASE_URL + '/user/personal', { tags: selectedTagNames }, {
    //       headers: {
    //         Authorization: 'Bearer ' + localStorage.getItem('access_token')
    //       }
    //     });

    //     const personalizationId = response.data.id;
    //     await axios.put(
    //       BASE_URL + '/user/personal/' + personalizationId,
    //       { tags: selectedTagNames },
    //       {
    //         headers: {
    //           Authorization: 'Bearer ' + localStorage.getItem('access_token')
    //         }
    //       }
    //     );
    //     console.log('Personalization saved:', response.data);
    //     this.$notify({
    //       type: 'success',
    //       title: 'Success',
    //       text: 'Personalisasi berhasil diatur',
    //       color: 'green',
    //     });
    //   } catch (error) {
    //     console.error('Error saving personalization:', error);
    //   }
    // }
  },
  handleCheckboxChange() {
    this.savePersonal();
  },


};
</script>
  
<style scoped>
.dashboard-admin {
  min-height: 100vh;

  /* background: url("../../../src/assets/LandingPage/Background.png"); */
  background-position: center;
  background-size: cover;
}

.button-set {
  display: block;
  width: 100%;
  clear: both;
  margin: 15px 0;

}

button-custom,
.button {
  outline: 0 none;
  border: 0 none;
  padding: 13px 30px;
  background-color: #0771B8;
  background-image: linear-gradient(45deg, #ff0000 0%, #ff5252 50%, #ff7b7b 90%);
  background-position: 100% 0;
  background-size: 200% 200%;
  color: #FFF;
  font-size: 12px;
  text-transform: uppercase;
  border-radius: 20px;
  letter-spacing: 2px;
  transition: .3s;
  cursor: pointer;
  text-decoration: none;
  transition: all 0.5s ease;

  &:hover {
    background-position: 0 0;
  }
}

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
  