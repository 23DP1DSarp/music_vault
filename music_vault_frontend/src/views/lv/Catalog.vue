<script setup lang="ts">
import Navbar from '@/components/lv/NavbarLv.vue';
import ShoppingMenu from '@/components/lv/ShoppingMenuLv.vue';
import Footer from '@/components/lv/FooterLv.vue';
import axiosInstance from '@/axios';
import {ref} from 'vue';
import { RouterLink } from 'vue-router';

interface Album {
  id: number;
  title: string;
  author: string;
  release_date: Date;
  genre: string;
  genre_id: number;
  country: string;
  cover: string;
}

const loading = ref(true);

const albums = ref<Album[]>([]);

const genres: { id: number; title: string }[] = [];

const countries: string[] = [];

const decades: string[] = [];

const selectedGenres = ref<number[]>([]);

const selectedCountries = ref<string[]>([]);

const selectedDecades = ref<string[]>([]);

const user = ref({
  username: '',
  email: '',
  role_id: 0,
  user_role_id: 0,
});

const isLoggedIn = ref(false);

const getImageUrl = (path: string): string => {
  if (path.startsWith('http')) {
    return path;
  }

  return `${'http://music-vault-main-sjukhk.laravel.cloud'}/storage/${path}`;
};

const getUser = async () => {
    try {
        const response = await axiosInstance.get('/user');
        user.value = response.data;
        isLoggedIn.value = true;
        //console.log(response.data);
    } catch (error) {
        console.error(error);
        isLoggedIn.value = false;
    }
};

const filterAlbums = async () => {
  loading.value = true

  try {
    const { data } = await axiosInstance.get('/filter_albums', {
      params: {
        genres: selectedGenres.value,
        countries: selectedCountries.value,
        decades: selectedDecades.value.sort((b, a) => Number(b) - Number(a)),
      }
    })
    albums.value = data;
  } catch (err) {
    console.error(err)
  } finally {
    getGenres();
    getCountries();
    getDecades();
    loading.value = false;
  }
}

const getGenres = async () => {
  //console.log('Function called...')
  albums.value.forEach(album => {
  if (!genres.find(g => g.id === album.genre_id)) {
    genres.push({ id: album.genre_id, title: album.genre });
  }
  })
}

const getCountries = async () => {
  //console.log('Function called...')
  albums.value.forEach(album => {
  if (!countries.includes(album.country)) {
    countries.push(album.country)
  }
  })
}

const getDecades = async () => {
  //console.log('Function called...')
  albums.value.forEach(album => {
  const year = new Date(album.release_date).getFullYear();
  const decade = Math.floor(year / 10) * 10;
  if (!decades.includes(decade.toString())) {
    decades.push(decade.toString())
  }
  decades.sort((b, a) => Number(a) - Number(b));
  })
}

const filterMenu = async () => {

  let hamburgerSlider = document.getElementById('mobile_filters') as HTMLFormElement;

  if (hamburgerSlider.style.visibility === "hidden" || hamburgerSlider.style.visibility === '') {
    hamburgerSlider?.style.setProperty('width','70%');
    hamburgerSlider?.style.setProperty('visibility','visible');
  } else {
    hamburgerSlider?.style.setProperty('width','0%');
    hamburgerSlider?.style.setProperty('visibility','hidden');
  }
  
}

getUser();
filterAlbums();
</script>

<template>
    <body>
        <Navbar />

        <main>
        <ShoppingMenu />
        
        <form id="filters">
            <h1>Filtri</h1>
                <div id="genre_filters">
                    <h2>Žanrs</h2>
                    <div class="genre_filter" v-for="genre in genres" :key="genre.id">
                      <input type="checkbox" :value="genre.id" v-model="selectedGenres" @change="filterAlbums">
                      <label>{{ genre.title }}</label>
                    </div>
                </div>

                <div id="decade_filters">
                    <h2>Dekāde</h2>
                    <div class="decade_filter" v-for="decade in decades" :key="decade">
                        <input type="checkbox" :value="decade" v-model="selectedDecades" @change="filterAlbums">
                        <label>{{ decade }}</label>
                    </div>
                </div>

                <div id="country_filters">
                    <h2>Valsts</h2>
                    <div class="country_filter" v-for="country in countries" :key="country">
                        <input type="checkbox" :value="country" v-model="selectedCountries" @change="filterAlbums">
                        <label>{{ country }}</label>
                    </div>
                </div>
        </form>


        <form id="mobile_filters">
          <div id="mobile_filters_header">
            <h1>Filtri</h1>
            <div id="close_btn" @click="filterMenu()">
                <img src="../../images/shopping_cart images/close-x-svgrepo-com.svg">
            </div>
          </div>
                <div id="genre_filters">
                    <h2>Žanrs</h2>
                    <div class="genre_filter" v-for="genre in genres" :key="genre.id">
                      <input type="checkbox" :value="genre.id" v-model="selectedGenres" @change="filterAlbums">
                      <label>{{ genre.title }}</label>
                    </div>
                </div>

                <div id="decade_filters">
                    <h2>Dekāde</h2>
                    <div class="decade_filter" v-for="decade in decades" :key="decade">
                        <input type="checkbox" :value="decade" v-model="selectedDecades" @change="filterAlbums">
                        <label>{{ decade }}</label>
                    </div>
                </div>

                <div id="country_filters">
                    <h2>Valsts</h2>
                    <div class="country_filter" v-for="country in countries" :key="country">
                        <input type="checkbox" :value="country" v-model="selectedCountries" @change="filterAlbums">
                        <label>{{ country }}</label>
                    </div>
                </div>
        </form>

        <div id="albums">
            <div id="albums_header">
              <h1>Katalogs</h1>
              <img id="filters_btn" src="../../images/catalog_images/filter-svgrepo-com.svg" @click="filterMenu()">
            </div>
            <div id="album_cards">
              <div id="album_data" v-if="loading == false" v-for="album in albums">
                      
                  <img v-if="album.cover" :src="getImageUrl(album.cover)" :alt="album.title">
                      
                  <a :href="`/albuminfo/${album.id}`"><h3>{{ album.title }}</h3></a>
                  <p>{{ album.author }}</p>
                  <div id="genre_and_year">
                    <p>{{ album.genre }}</p>
                    <p>&nbsp;•&nbsp;</p>
                    <p>{{ new Date(album.release_date).getFullYear()}}</p>
                  </div>
              </div>
            </div> 
            <p v-show="albums.length == 0 && loading == false">Albumi nav atrasti.</p>
        </div>

        </main>

        <Footer />
    </body>
</template>

<style scoped>
@font-face {
  font-family: Segoe UI Symbol;
  src: url('../assets/fonts/Segoe-UI-Symbol.ttf');
}

html {
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

body {
  padding: 0;
  margin: 0;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}

a {
  text-decoration: none;
  color: #0A0A0A;
}

a:visited {
  color: #0A0A0A;
}

main {
  width: 80vw;
  margin: 0 auto;
  display: flex;
  flex-direction: row;
  gap: 80px;
  padding-bottom: 50px;
}

#mobile_filters, #filters_btn {
  display: none;
}

#filters {
  display: flex;
  flex-direction: column;
  gap: 30px;
  align-items: flex-start;
}

#filters h1 {
  font-size: 24px;
}

#filters h2 {
  font-size: 18px;
  margin-top: 0;
}

#filters input[type="checkbox"] {
  width: 18px;
  height: 18px;
}

.genre_filter, .decade_filter {
  display: flex;
  flex-direction: row;
  gap: 10px;
}

#genre_filters {
  padding: 0;
}

#price_range_filters_inputs {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 10px;
  height: 25px;
}

#price_range_filters_inputs input{
  width: 100px;
  height: 20px;
}

#albums h1 {
  font-size: 24px;
  padding-bottom: 30px;
}

#album_cards {
  display: grid;
  grid-template-columns: repeat(4, 1fr);
  gap: 25px;
  width: 300px;
  margin-bottom: 50px;
}

#album_data {
  display: flex;
  flex-direction: column;
  gap: 10px;
  width: 230px;
  height: 350px;
  border: solid #E4E4E4 1px;
  border-radius: 14px;
}

#album_data img {
  width: 100%;
  height: 220px;
  border-radius: 14px;
}

#album_data h3 {
  font-size: 20px;
  line-height: 24px;
  margin: 0;
  margin-left: 15px;
  margin-right: 15px;
}

#album_data p {
  margin: 0;
  margin-left: 15px;
  font-size: 16px;
  line-height: 20px;
  color: #717182;
}

#genre_and_year {
  display: flex;
  flex-direction: row;
}

.form_button {
  height: 32px;
  background-color: #FFFFFF;
  border: solid rgba(0, 0, 0, .1) 1px;
  border-radius: 8px;
  padding: 15px;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  font-size:14px;
  line-height: 20px;
  letter-spacing: 0px;
  display: flex;
  flex-direction: row;
  align-items: center;
  cursor: pointer;
}

#checkout_btn {
  width: 100%;
  height: 40px;
  background-color: #FFFFFF;
  color: #0A0A0A;
  border: solid rgba(0, 0, 0, .1) 1px;
  border-radius: 8px;
  text-align: center;
  vertical-align: middle;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  font-size:18px;
  line-height: 20px;
  letter-spacing: 0px;
  cursor: pointer;
}


@media (max-width:480px) {


#navwrapper {
  align-items: center;
}

body {
  width: 100%;
  margin: 0 auto;
  padding: 0;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  overflow-x: hidden;
}

nav {
  width: 100%;
}

#navbuttons, #rightbuttons {
  width: 0;
  height: 0;
  display: none;
}

#mobile_btns, #hamburger_menu {
  display: block;
}

#mobile_btns {
  display: flex;
  flex-direction: row;
  gap: 20px;
}

#logoutbtn {
  font-size: 19.53px;
  padding: 0;
}

#navbuttons_mobile {
  height: min-content;
}

#navbuttons_mobile ul {
  display: flex;
  flex-direction: column;
  gap: 20px;
  padding: 0;
}

#rightbuttons_mobile {
  display: flex;
  flex-direction: column;
  gap: 20px;
  margin-top: 0px;
}

#rightbuttons_mobile ul {
  display: flex;
  flex-direction: column;
  gap: 20px;
  padding: 0;
  margin: 0;
}

#hamburger_icon {
  width: 24px;
  height: 24px;
  padding: 5px;
  border-style: solid;
  border: #ECECF0 solid 1px;
  border-radius: 8px;
  text-align: center;
  cursor: pointer;
}

#hamburger_menu {
  height: 100%;
  width: 0px;
  position: fixed;
  z-index: 1;
  top: 0;
  right: 0;
  background-color: #E4E4E4;
  overflow-x: hidden; 
  padding-top: 20px;
  padding-left: 20px;
  padding-right: 20px;
  transition: 0.5s;
  visibility: hidden;
  display: flex;
  flex-direction: column;
  gap: 50px;
  font-size: 19.53px;
}

#language_options_mobile {
  display: flex;
  flex-direction: row;
  gap: 10px;
}

#language_options_mobile ul {
  display: flex;
  flex-direction: row;
  gap: 10px;
  padding: 0;
}

#language_options_mobile p {
  margin: 0;
}


#mobile_filters {
  height: 100%;
  width: 0px;
  position: fixed;
  z-index: 1;
  top: 0;
  left: 0;
  background-color: #E4E4E4;
  overflow-x: hidden; 
  padding-top: 20px;
  padding-left: 20px;
  padding-right: 20px;
  transition: 0.5s;
  visibility: hidden;
  display: flex;
  flex-direction: column;
  gap: 25px;
  font-size: 16px;
}

#mobile_filters_header {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  vertical-align: middle;
}

#mobile_filters_header h1 {
  margin: 0;
}

#close_btn {
  height: 48px;
}

#mobile_filters [type="checkbox"] {
  width: 20px;
  height: 20px;
}

#price_range_filters_inputs {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 10px;
  height: 25px;
}

#country_filters {
  margin-bottom: 50px;
}

#albums_header {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  margin-top: 25px;
  margin-bottom: 50px;
  margin-left: 5px;
}

#albums_header h1 {
  padding-bottom: 0;
  margin: 0;
}

#filters_btn {
  width: 16px;
  height: 16px;
  padding: 9px;
  border-style: solid;
  border: #ECECF0 solid 1px;
  border-radius: 8px;
  text-align: center;
  cursor: pointer;
  display: block;
}

#filters {
  display: none; 
}

#album_cards {
  display: grid;
  grid-template-columns: repeat(1, 1fr);
  justify-items: center;
  gap: 25px;
  width: 300px;
  margin-bottom: 50px;
}

#album_data {
  display: flex;
  flex-direction: column;
  gap: 10px;
  width: 230px;
  height: 350px;
  border: solid #E4E4E4 1px;
  border-radius: 14px;
}

#album_data img {
  width: 100%;
  height: 220px;
  border-radius: 14px;
}

#album_data h3 {
  font-size: 20px;
  line-height: 24px;
  margin: 0;
  margin-left: 15px;
  margin-right: 15px;
}

#album_data p {
  margin: 0;
  margin-left: 15px;
  font-size: 16px;
  line-height: 20px;
  color: #717182;
}

#genre_and_year {
  display: flex;
  flex-direction: row;
}

#footer_wrapper {
  width: 90vw;
  margin: 0 auto;
  padding: 0;
  align-items: center;
  text-align: center;
}


#footer_top {
  width: 90vw;
  margin: 0 auto;
  padding-top: 50px;
  padding-bottom: 20px;
  display: flex;
  flex-direction: column;
  gap: 50px;
  align-items: center;
  text-align: center;
}

#footer_info {
  display: flex;
  flex-direction: column;
  width: 100%;
  max-width: 357px;
  margin-right: 0;
  font-size: 13.89px;
  line-height: 20px;
  letter-spacing: 0px;
  color: #717182;
  text-align: center;
  align-items: center;
}

.footer_links {
  display: flex;
  width: 100%;
  flex-direction: column;
  justify-content: center;
  align-items: center;
  text-align: center;
}

.footer_links ul {
  display: flex;
  width: 100%;
  flex-direction: column;
  gap: 14px;
  padding: 0;
  align-items: center;
  text-align: center;
}

#footer_bottom, #footer_bottom ul {
  display: flex;
  flex-direction: column;
  width: 100%;
  gap: 20px;
  padding-top: 20px;
  padding-bottom: 20px;
  padding-left: 0;
  padding-right: 0;
  align-items: center;
  text-align: center;
}

}

</style>