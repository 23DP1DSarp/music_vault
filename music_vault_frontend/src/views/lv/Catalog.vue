<script setup lang="ts">
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

interface Item {
  id: string;
  title: string;
  quantity: number;
  price: number;
}

const loading = ref(true);

const albums = ref<Album[]>([]);

const genres: { id: number; title: string }[] = [];

const countries: string[] = [];

const decades: string[] = [];

const selectedGenres = ref<number[]>([]);

const selectedCountries = ref<string[]>([]);

const selectedDecades = ref<string[]>([]);

const shoppingList = ref<Item[]>([]);

const user = ref({
  username: '',
  email: '',
  role_id: 0,
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
        console.log(response.data);
    } catch (error) {
        console.error(error);
        isLoggedIn.value = false;
    }
};


const logout = async () => {
    try{
        const response = await axiosInstance.post('/logout');
        console.log(response.data);
    } catch (error) {
        console.error(error);
    } finally {
        window.location.href='/';
    }
}

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
  if (!genres.find(g => g.id === album.genre_id)) {
    genres.push({ id: album.genre_id, title: album.genre });
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

const shoppingMenu = async () => {

  let shoppingSlider = document.getElementById('shopping_menu') as HTMLFormElement;
  
  if (shoppingSlider.style.visibility === "hidden" || shoppingSlider.style.visibility === '') {
    shoppingSlider?.style.setProperty('width','25%');
    shoppingSlider?.style.setProperty('visibility','visible');
  } else {
    shoppingSlider?.style.setProperty('width','0%');
    shoppingSlider?.style.setProperty('visibility','hidden');
  }
  
}

const loadFromShoppingList = async () => {
  const stored = localStorage.getItem('shoppingList');
  if (stored) {
  shoppingList.value = JSON.parse(stored);
  }
}

const deleteFromShoppingList = async (index: number) => { 
  shoppingList.value.splice(index);
  localStorage.setItem("shoppingList", JSON.stringify(shoppingList.value));
}

getUser();
filterAlbums();
loadFromShoppingList();
</script>

<template>
    <body>
        <nav>
        <div id="navwrapper">
        <RouterLink to="/">
            <div id="logo">
            <img src="../../images/nav_images/vinyl_icon.svg">
            <p>MusicVault</p>
        </div></RouterLink>

        <div id="navbuttons">
            <ul>
                <li>Jaunumi</li>
                <li>Žanri</li>
                <li>Mākslinieki</li>
                <li>Forumi</li>
                <RouterLink to="/add-album" v-if="isLoggedIn && user.role_id === 2">Pievienot albumu</RouterLink>
            </ul>
        </div>

        <div id="rightbuttons">
            <div id="language_select" name="language">
              <p>LV</p>
              <div id="language_options">
                <RouterLink to="/">
                  EN
                </RouterLink>
                <RouterLink to="/ru">
                  RU
                </RouterLink>
              </div>
            </div>
            <input type="text" id="searchbar" name="recordsearch" placeholder="Meklēt ierakstus...">
            <img id="shoppingcart" src="../../images/nav_images/shopping_cart_icon.svg" @click="shoppingMenu()">
            <p>{{user?.username}}</p>
            <form action="/logout" @submit.prevent="logout" v-if="isLoggedIn">
                <button id="logoutbtn">Iziet</button>
            </form>
            <RouterLink to="/login" v-if="!isLoggedIn">Ieiet</RouterLink>
            <RouterLink to="/register" v-if="!isLoggedIn">Reģistrēties</RouterLink>
        </div>
    </div>
    </nav>


        <main>
        
        <div id="shopping_menu">
          <div id="close_btn" @click="shoppingMenu()">
            <img src="../../images/shopping_cart images/close-x-svgrepo-com.svg">
          </div>
          <div class="shopping_item" v-for="(item, index) in shoppingList">
            <div id="info_div">
              <h2>{{ item.title }}</h2>
              <p @click="deleteFromShoppingList(index)">Dzēst</p>
            </div>
            <div id="price_div">
              <b><p id="price">{{ item.price }}$</p></b>
              <p>Daudzums: {{ item.quantity }}</p>
            </div>
            
          </div>
        </div>

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

        <div id="albums">
            <h1>Katalogs</h1>
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

        <footer>
            <div id="footer_wrapper">
            <div id="footer_top">
                <div id="footer_info">
                    <div id="footer_logo">
                        <img src="../../images/footer_images/vinyl_icon.svg">
                        <p>MusicVault</p>
                    </div>
                    <p id="footer_info_text">
                        Jūsu galvenais mūzikas ierakstu galamērķis. Atklājiet,
                        kolekcionējiet un baudiet mūziku tā, kā tā ir radīta
                        skanēt.
                    </p>

                    <div id="icons">
                        <img class="icon" src="../../images/footer_images/facebook_icon.svg">
                        <img class="icon" src="../../images/footer_images/instagram_icon.svg">
                        <img class="icon" src="../../images/footer_images/twitter_icon.svg">
                        <img class="icon" src="../../images/footer_images/youtube_icon.svg">
                    </div>
                    
                </div>

                <div>
                    <h6>Ātrās saites</h6>

                    <ul>
                        <li>Jaunumi</li>
                        <li>Priekšpasūtījumi</li>
                        <li>Izpārdošana</li>
                        <li>Reti atradumi</li>
                        <li>Dāvanu kartes</li>
                    </ul>
                </div>

                <div>
                    
                    <h6>Žanri</h6>

                    <ul>
                        <li>Roks</li>
                        <li>Džezs</li>
                        <li>Elektronika</li>
                        <li>Hip-Hopss</li>
                        <li>Klasika</li>
                    </ul>
                </div>

                <div id="subscribe_form">
                    <h6>Sekojiet jaunumiem</h6>
                    <p>Saņemiet paziņojumus par jaunumiem un ekskluzīviem piedāvājumiem.</p>

                    <form action="" method="post">
                        <input id="email_input" placeholder="Ievadiet e-pastu" name="subscription-email" type="email" required>
                        <input id="subscribe_form_submit" type="submit" value="Abonēt">
                    </form>
                </div>
            </div>

            
            <div id="footer_bottom">

                <ul>
                    <li>Privātuma politika</li>
                    <li>Lietošanas noteikumi</li>
                    <li>Piegādes informācija</li>
                    <li>Atgriešana</li>
                </ul>

                <p>&copy; 2025 MusicVault. Visas tiesības aizsargātas.</p>

            </div>
            </div>
        </footer>
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

nav {
  border-bottom: solid #ECECF0 1px;
}

#navwrapper {
  width: 80vw;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  margin: 0 auto;
  padding: 0;
  font-size: 19.53px;
  color: #0A0A0A;
}

#logo {
  display: flex;
  flex-direction: row;
  gap: 5px;
  font-size: 19.53px;
  line-height: 28px;
  letter-spacing: -0.5px;
}

#searchbar {
  background-color: #F3F3F5;
  border-style: none;
  width: 256px;
  height: 36px;
  color: #717182;
  padding: 0px 0px 0px 10px;
  border-radius: 8px;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  font-size: 14px;
}

#shoppingcart {
  width: 16px;
  height: 16px;
  padding: 9px;
  border-style: solid;
  border: #ECECF0 solid 1px;
  border-radius: 8px;
  text-align: center;
  cursor: pointer;
}

#navbuttons {
  display: flex;
  flex-direction: row;
  justify-content: center;
  margin: 0 auto;
  font-size: 16px;
  line-height: 24px;
  letter-spacing: 0px;
  align-items: center;
}

#navbuttons ul {
    display: flex;
    flex-direction: row;
    padding: 0;
    list-style: none;
    gap: 24px;
}

#rightbuttons {
    display: flex;
    flex-direction: row;
    gap: 20px;
    align-items: center;
    font-size: 16px;
    line-height: 28px;
    letter-spacing: -0.5px;
}

#language_select {
  position: relative;
  width: 16px;
  height: 16px;
  padding: 9px;
  border: #ECECF0 solid 1px;
  border-radius: 8px;
  display: flex;
  align-items: center;
  justify-content: center;
  cursor: pointer;
}

#language_select p {
  margin: 0;
  font-size: 14px;
}

#language_options {
  position: absolute;
  top: 40px;
  left: 0;
  width: 100%;
  background-color: #FFFFFF;
  border: #ECECF0 solid 1px;
  border-radius: 8px;
  display: flex;
  flex-direction: column;
  opacity: 0;
  visibility: hidden;
  transform: translateY(-5px);
  transition: 0.2s ease;
  z-index: 100;
}

#language_select:hover #language_options {
  opacity: 1;
  visibility: visible;
  transform: translateY(0);
}

#language_options a {
  padding: 8px;
  text-align: center;
  font-size: 14px;
  color: #0A0A0A;
}

#language_options a:hover {
  background-color: #F3F3F5;
}

#logoutbtn {
  background-color: transparent;
  border-style: none;
  font-size: 16px;
  line-height: 28px;
  letter-spacing: -0.5px;
  color: #0A0A0A;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  cursor: pointer;
}

#logoutbtn:hover {
  color: #717182;
}

main {
  width: 80vw;
  margin: 0 auto;
  display: flex;
  flex-direction: row;
  gap: 80px;
  padding-bottom: 50px;
}

#shopping_menu {
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
  gap: 20px;
}

#close_btn {
  align-self: flex-end;
}

#close_btn img {
  width: 48px;
  height: 48px;
}

.shopping_item {
  display: flex;
  flex-direction: row;
  align-items: baseline;
  justify-content: space-between;
  background-color: #FFFFFF;
  border-radius: 20px;
  padding: 10px;
}

.shopping_item h2 {
  margin-bottom: 24px;
}

#price {
  font-size: 24px;
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

footer {
  border-top: solid #ECECF0 1px;
}

footer h6 {
  font-size: 16px;
  margin-top: 15px;
  line-height: 24px;
}

#footer_wrapper {
  width: 80vw;
  margin: 0 auto;
  padding-left: 100px;
  padding-right: 100px;
  padding-top: 50px;
  padding-bottom: 50px;
}


#footer_top {
  display: flex;
  flex-direction: row;
  padding-bottom: 20px;
  margin: 0 auto;
  border-bottom: solid #ECECF0 1px;
}

#footer_info {
  display: flex;
  flex-direction: column;
  width: 357px;
  margin-right: 67px;
  font-size: 13.89px;
  line-height: 20px;
  letter-spacing: 0px;
  color: #717182;
}

#footer_info_text {
  width: 317.84px;
  height: 54px;
  margin-bottom: 20px;
}

#footer_info p {
  margin-top: 15px;
}

#footer_logo {
  display: flex;
  flex-direction: row;
  align-items: center;
  font-size: 17.72px;
  font-weight: normal;
  line-height: 28px;
  letter-spacing: -0.5px;
  gap: 5px;
  color: #0A0A0A;
}

#footer_logo img {
  width: 24px;
  height: 24px;
}

#icons {
  display: flex;
  flex-direction: row;
  gap: 8px;
}

.icon {
  width: 16px;
  height: 16px;
  padding: 6px;
  border: #ECECF0 solid 1px;
  border-radius: 8px;
  cursor: pointer;
}

#footer_top ul {
  width: 18vw;
  font-size: 14px;
  line-height: 20px;
  letter-spacing: 0px;
  color: #717182;
  display: flex;
  flex-direction: column;
  gap: 14px;
  list-style: none;
  padding: 0;
}

#subscribe_form p {
  color: #717182;
  line-height: 20px;
  letter-spacing: 0px;
}

#email_input {
  background-color: #F3F3F5;
  border-style: none;
  width: 352px;
  height: 36px;
  color: #717182;
  padding: 0px 0px 0px 10px;
  border-radius: 8px;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  font-size: 14px;
}

#subscribe_form_submit {
  margin-top: 5px;
  width: 362px;
  height: 32px;
  border-style: none;
  border-radius: 8px;
  background-color: #030213;
  color: #E4E4E4;
  font-size: 14px;
  line-height: 20px;
  letter-spacing: 0px;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  cursor: pointer;
}

#footer_bottom {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  font-size: 14px;
  line-height: 20px;
  letter-spacing: 0px;
  color: #717182;
  margin-top: 40px;
}

#footer_bottom ul {
  display: flex;
  flex-direction: row;
  gap: 24px;
  list-style: none;
  padding: 0;
}
</style>