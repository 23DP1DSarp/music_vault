<script setup lang="ts">
import Navbar from '@/components/lv/NavbarLv.vue';
import ShoppingMenu from '@/components/lv/ShoppingMenuLv.vue';
import Footer from '@/components/lv/FooterLv.vue';
import axiosInstance from '@/axios';
import {ref} from 'vue';
import { useRouter } from 'vue-router';

const loading = ref(true);

const router = useRouter();

const genres: string[] = [];

const countries: string[] = [];

const decades: string[] = [];

const minPrice = ref(0);

const maxPrice = ref(0);

const selectedGenres = ref<string[]>([]);

const selectedCountries = ref<string[]>([]);

const selectedDecades = ref<string[]>([]);

const sortOrder = ref('asc');

const sortBy = ref('title');

const user = ref({
    username: '',
    email: '',
    created_at: '',
    user_role_id: 0,
});

const isLoggedIn = ref(false);

interface AlbumItem {
  id: number;
  title: string;
  release_date: Date;
  genre: string;
  country: string;
  condition: string;
  quantity: number;
  price: number;
  notes: string;
  picture: string;
  seller_name: string;
}

const albumItems = ref<AlbumItem[]>([]);



const getUser = async () => {
    try {
        const response = await axiosInstance.get('/user');
        user.value = response.data;
        isLoggedIn.value = true;
        //console.log(response.data);
    } catch (error) {
        console.error(error);
        isLoggedIn.value = false;
    } finally {
        loading.value = false;
    }
};

const getImageUrl = (path: string): string => {
  if (path.startsWith('http')) {
    return path;
  }

  return `${'http://music-vault-main-sjukhk.laravel.cloud'}/storage/${path}`;
};


const getAlbumItems = async () => {
  loading.value = true

  try {
    const { data } = await axiosInstance.get('/get_album_items', {
      params: {
        genres: selectedGenres.value,
        countries: selectedCountries.value,
        decades: selectedDecades.value,
      }
    })
    albumItems.value = data;
  } catch (err) {
    console.error(err)
  } finally {
    getGenres();
    getCountries();
    getDecades();
    loading.value = false;
  }
}


const filterAlbums = async () => {
  loading.value = true

  try {
    const { data } = await axiosInstance.get('/filter_album_items', {
      params: {
        genres: selectedGenres.value,
        countries: selectedCountries.value,
        decades: selectedDecades.value.sort((b, a) => Number(b) - Number(a)),
        minPrice: Number(minPrice.value),
        maxPrice: Number(maxPrice.value),
      }
    })
    albumItems.value = data;
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
  albumItems.value.forEach(albumItem => {
  if (!genres.includes(albumItem.genre)) {
    genres.push(albumItem.genre)
  }
  })
}

const getCountries = async () => {
  //console.log('Function called...')
  albumItems.value.forEach(albumItem => {
  if (!countries.includes(albumItem.country)) {
    countries.push(albumItem.country)
  }
  })
}

const getDecades = async () => {
  //console.log('Function called...')
  albumItems.value.forEach(albumItem => {
  const year = new Date(albumItem.release_date).getFullYear();
  const decade = Math.floor(year / 10) * 10;
  if (!decades.includes(decade.toString())) {
    decades.push(decade.toString())
    decades.sort((a, b) => parseInt(b) - parseInt(a));
  }
  })
}

const sortAlbumItems = async (sortBy: string) => {
  loading.value = true;

  if (sortOrder.value === 'desc') {
    sortOrder.value = 'asc';
  } else {
    sortOrder.value = 'desc';
  }

  try {
    const { data } = await axiosInstance.get('/order_albums', {
      params: {
        sortBy: sortBy,
        sortOrder: sortOrder.value,
        genres: selectedGenres.value,
        countries: selectedCountries.value,
        decades: selectedDecades.value,
      }
    });
    albumItems.value = data;
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
  }
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
getAlbumItems();
</script>

<template>
    <body>
        <Navbar />


     <main>
        <ShoppingMenu />

       <!-- <div id="album_info">
                <div id="album_text_info">
                    <h1> - </h1>
                    <p></p>
                </div>
        </div>-->


        <div id="offers_section">

           <form id="filters">
            <h1>Filtri</h1>
                <div id="genre_filters">
                    <h2>Žanrs</h2>
                    <div class="genre_filter" v-for="genre in genres" :key="genre">
                        <input type="checkbox" :value="genre" v-model="selectedGenres" @change="filterAlbums">
                        <label>{{ genre }}</label>
                    </div>
                </div>

                <div id="price_range_filters">
                    <h2>Cenu diapazons</h2>
                    <div id="price_range_filters_inputs">
                        <input type="number" name="min" v-model="minPrice" @change="filterAlbums">
                        <p> - </p>
                        <input type="number" name="max" v-model="maxPrice" @change="filterAlbums"> 
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
                    <div class="genre_filter" v-for="genre in genres" :key="genre">
                        <input type="checkbox" :value="genre" v-model="selectedGenres" @change="filterAlbums">
                        <label>{{ genre }}</label>
                    </div>
                </div>

                <div id="price_range_filters">
                    <h2>Cenu diapazons</h2>
                    <div id="price_range_filters_inputs">
                        <input type="number" name="min" v-model="minPrice" @change="filterAlbums">
                        <p> - </p>
                        <input type="number" name="max" v-model="maxPrice" @change="filterAlbums"> 
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


            <div id="offers_list">
              <div id="offers_list_header">
                <h1>Piedāvājumi</h1>
                <img id="filters_btn" src="../../images/catalog_images/filter-svgrepo-com.svg" @click="filterMenu()">
              </div>
                <div id="list_filters">
                  <button class="filter_btn" >Nosaukums</button>
                  <button class="filter_btn" >Mākslinieks</button>
                  <button class="filter_btn">Žanrs</button>
                  <button class="filter_btn">Gads</button>
                  <button class="filter_btn">Pārdevējs</button>
                  <button class="filter_btn" >Cena</button> 
                </div>
                <div id="mobile_list_filters">
                  <button class="filter_btn" >Nosaukums</button>
                  <button class="filter_btn">Pārdevējs</button>
                  <button class="filter_btn" >Cena</button> 
                </div>
                <div class="album_card" v-for="albumItem in albumItems" :key="albumItem.id">
                  
                
                <div id="item_info_col">
                    <p>Nosaukums: <a :href="`/offer/${albumItem.id}`">{{ albumItem.title }}</a></p>
                    <p>Stāvoklis: {{ albumItem.condition }}</p>
                    <p>Daudzums: {{ albumItem.quantity }}</p>
                </div>

                <div id="item_seller_col">  
                    <p>{{ albumItem.seller_name }}</p>
                </div>

                <div id="item_price_col">
                    <p>{{ albumItem.price }}€</p>
                </div>

                </div>
            </div>     
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
  flex-direction: column;
}

#offers_section {
    display: flex;
    gap: 80px;
}

#filters {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
  gap: 30px;
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

.genre_filter, .decade_filter, .country_filter {
  display: flex;
  flex-direction: row;
  align-items: center;
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

#genre_filters, #price_range_filters, #decade_filters, #country_filters {
    padding-bottom: 20px;
}

#list_filters {
    width: 100%;
    height: fit-content;
    padding: 0px 0px 0px 0px;
    display: flex;
    flex-direction: row;
    border: #ECECF0 solid 1px;
    border-radius: 8px;
    font-size: 16px;
    line-height: 20px;
    letter-spacing: 0px;
    color: #0A0A0A;
    background-color: #ECECF0;
}

.filter_btn {
    font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: transparent;
    border-style: solid;
    border: #030213 1px;
    border-radius: 8px;
    padding: 6px 12px 6px 12px;
    font-size: 16px;
    line-height: 20px;
    letter-spacing: 0px;
    cursor: pointer;
}

#offers_list {
    width: 100%;
    height: fit-content;
    font-size: 16px;
    line-height: 20px;
    letter-spacing: 0px;
    color: #0A0A0A;
}

#offers_list_header h1 {
  margin-top: 20px;
  margin-bottom: 50px;
  font-size: 24px;
}

.album_card {
    width: 100%;
    height: 200px;
    padding: 20px 0px 20px 0px;
    border-radius: 8px;
    display: flex;
    flex-direction: row;
    gap: 33px;
}

.item_info_col {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.album_card img {
    width: 150px;
    height: 150px;
    border-radius: 8px;
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

#filters {
  display: none;
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

#offers_list_header {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  margin-top: 25px;
  margin-bottom: 25px;
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

#country_filters {
  margin-bottom: 50px;
}

#mobile_list_filters {
    width: 100%;
    height: fit-content;
    padding: 0px 0px 0px 0px;
    display: flex;
    flex-direction: row;
    border: #ECECF0 solid 1px;
    border-radius: 8px;
    font-size: 16px;
    line-height: 20px;
    letter-spacing: 0px;
    color: #0A0A0A;
    background-color: #ECECF0;
}

.filter_btn {
  padding-left: 20px;
  padding-right: 20px;
}

#list_filters {
  display: none;
}


#offers_list_header {
  margin-top: 0px;
}

#offers_list_header h1 {
  margin-top: 50px;
}

.album_card {
  gap: 20px;
}

#item_info_col {
  width: 60%;
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
}

#footer_bottom, #footer_bottom ul {
  display: flex;
  flex-direction: column;
  gap: 20px;
  padding-top: 20px;
  padding-bottom: 20px;
  padding-left: 0;
  padding-right: 0;
  align-items: center;
}


}

</style>