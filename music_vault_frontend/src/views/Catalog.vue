<script setup lang="ts">
import Navbar from '@/components/en/Navbar.vue';
import ShoppingMenu from '@/components/en/ShoppingMenu.vue';
import Footer from '@/components/en/Footer.vue';
import axiosInstance from '@/axios';
import {ref} from 'vue';
import { AdvancedImage } from '@cloudinary/vue';
import { Cloudinary } from '@cloudinary/url-gen';

interface Album {
  id: number;
  title: string;
  author: string;
  release_date: Date;
  genre: string;
  country: string;
  cover: string;
}

const imageFromCloud = new Cloudinary({
  cloud: {
    cloudName: 'gwfx5j43'
  }
});

const loading = ref(true);

const albums = ref<Album[]>([]);

const genres: string[] = [];

const countries: string[] = [];

const decades: string[] = [];

const selectedGenres = ref<string[]>([]);

const selectedCountries = ref<string[]>([]);

const selectedDecades = ref<string[]>([]);

const user = ref({
    username: '',
    email: '',
});

const isLoggedIn = ref(false);

const getUser = async () => {
    try {
        const response = await axiosInstance.get('/user');
        user.value = response.data;
        isLoggedIn.value = true;
        console.log(response.data);
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
  console.log('Function called...')
  albums.value.forEach(album => {
  if (!genres.includes(album.genre)) {
    genres.push(album.genre)
  }
  })
}

const getCountries = async () => {
  console.log('Function called...')
  albums.value.forEach(album => {
  if (!countries.includes(album.country)) {
    countries.push(album.country)
  }
  })
}

const getDecades = async () => {
  console.log('Function called...')
  albums.value.forEach(album => {
  const year = new Date(album.release_date).getFullYear();
  const decade = Math.floor(year / 10) * 10;
  if (!decades.includes(decade.toString())) {
    decades.push(decade.toString())
  }
  decades.sort((b, a) => Number(a) - Number(b));
  })
}

getUser();
filterAlbums();
</script>

<template>
    <body>
        <Navbar />
        <!--<Navbar :isLoggedIn="isLoggedIn" :user="user" @shoppingMenu="shoppingMenu" />-->

        <main>
        <ShoppingMenu />

        <form id="filters">
            <h1>Filters</h1>
                <div id="genre_filters">
                    <h2>Genre</h2>
                    <div class="genre_filter" v-for="genre in genres" :key="genre">
                        <input type="checkbox" :value="genre" v-model="selectedGenres" @change="filterAlbums">
                        <label>{{ genre }}</label>
                    </div>
                </div>

                <div id="decade_filters">
                    <h2>Decade</h2>
                    <div class="decade_filter" v-for="decade in decades" :key="decade">
                        <input type="checkbox" :value="decade" v-model="selectedDecades" @change="filterAlbums">
                        <label>{{ decade }}</label>
                    </div>
                </div>

                <div id="country_filters">
                    <h2>Country</h2>
                    <div class="country_filter" v-for="country in countries" :key="country">
                        <input type="checkbox" :value="country" v-model="selectedCountries" @change="filterAlbums">
                        <label>{{ country }}</label>
                    </div>
                </div>
        </form>

        <div id="albums">
            <h1>Catalog</h1>
            <div id="album_cards">
              <div id="album_data" v-if="loading == false" v-for="album in albums">
                      
                  <AdvancedImage v-if="album.cover" :cldImg="imageFromCloud.image(album.cover)" :alt="album.title"/>
                  <a :href="`/albuminfo/${album.id}`"><h3>{{ album.title }}</h3></a>
                  <p>{{ album.author }}</p>
                  <div id="genre_and_year">
                    <p>{{ album.genre }}</p>
                    <p>&nbsp;•&nbsp;</p>
                    <p>{{ new Date(album.release_date).getFullYear()}}</p>
                  </div>
              </div>
            </div> 
            <p v-show="albums.length == 0 && loading == false">No albums found.</p>
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

</style>