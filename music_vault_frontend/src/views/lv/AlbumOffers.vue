<script setup lang="ts">
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

const shoppingList = ref<Item[]>([]);

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

interface Item {
  id: string;
  title: string;
  quantity: number;
  price: number;
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

const logout = async () => {
    try {
        const response = await axiosInstance.post('/logout');
        //console.log(response.data);
    } catch (error) {
        console.error(error);
    } finally {
        window.location.href='/';
    }
}

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

const shoppingMenu = async () => {

  let shoppingSlider = document.getElementById('shopping_menu') as HTMLFormElement;

  if (window.innerWidth <= 480) {
    if (shoppingSlider.style.visibility === "hidden" || shoppingSlider.style.visibility === '') {
      shoppingSlider?.style.setProperty('width','70%');
      shoppingSlider?.style.setProperty('visibility','visible');
    } else {
    shoppingSlider?.style.setProperty('width','0%');
    shoppingSlider?.style.setProperty('visibility','hidden');
    }
  }
  else {
    if (shoppingSlider.style.visibility === "hidden" || shoppingSlider.style.visibility === '') {
      shoppingSlider?.style.setProperty('width','25%');
      shoppingSlider?.style.setProperty('visibility','visible');
    } else {
      shoppingSlider?.style.setProperty('width','0%');
      shoppingSlider?.style.setProperty('visibility','hidden');
    }
  }
}

const hamburgerMenu = async () => {

  let hamburgerSlider = document.getElementById('hamburger_menu') as HTMLFormElement;

  if (hamburgerSlider.style.visibility === "hidden" || hamburgerSlider.style.visibility === '') {
    hamburgerSlider?.style.setProperty('width','70%');
    hamburgerSlider?.style.setProperty('visibility','visible');
  } else {
    hamburgerSlider?.style.setProperty('width','0%');
    hamburgerSlider?.style.setProperty('visibility','hidden');
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

const loadFromShoppingList = async () => {
  const stored = localStorage.getItem('shoppingList');
  if (stored) {
  shoppingList.value = JSON.parse(stored);
  }
}

const updateItemInShoppingList = async (index: number, operator: string) => {
  if (operator === "-" && shoppingList.value[index]?.quantity) {
    shoppingList.value[index].quantity -= 1;
    localStorage.setItem("shoppingList", JSON.stringify(shoppingList.value))
  } else if (operator === "+" && shoppingList.value[index]?.quantity) {
    shoppingList.value[index].quantity += 1;
    localStorage.setItem("shoppingList", JSON.stringify(shoppingList.value))
  }
}

const deleteFromShoppingList = async (index: number) => { 
  shoppingList.value.splice(index);
  localStorage.setItem("shoppingList", JSON.stringify(shoppingList.value));
}

getUser();
getAlbumItems();
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
                <RouterLink to="/catalog">Jaunumi</RouterLink>
                <RouterLink to="/add-album" v-if="isLoggedIn">Pievienot albumu</RouterLink>
                <RouterLink to="/sell-item" v-if="isLoggedIn && user.user_role_id === 2">Pārdot preci</RouterLink>
                <RouterLink to="/sellerform" v-if="isLoggedIn && user.user_role_id !== 2">Kļūt par pārdevēju</RouterLink>
            </ul>
        </div>

        <div id="rightbuttons">
            <div id="language_select" name="language">
              <p>LV</p>
              <div id="language_options">
                <RouterLink to="/en">
                  EN
                </RouterLink>
                <RouterLink to="/ru">
                  RU
                </RouterLink>
              </div>
            </div>
            <img id="shoppingcart" src="../../images/nav_images/shopping_cart_icon.svg" @click="shoppingMenu()">
            <RouterLink to="/userprofile" v-if="isLoggedIn">{{user?.username}}</RouterLink>
            <form action="/logout" @submit.prevent="logout" v-if="isLoggedIn">
                <button id="logoutbtn">Iziet</button>
            </form>
            <RouterLink to="/login" v-if="!isLoggedIn">Ieiet</RouterLink>
            <RouterLink to="/register" v-if="!isLoggedIn">Reģistrēties</RouterLink>
        </div>

        <div id="mobile_btns">
          <img id="shoppingcart" src="../../images/nav_images/shopping_cart_icon.svg" @click="shoppingMenu()">
          <img id="hamburger_icon" src="../../images/nav_images/burger-menu-svgrepo-com.svg" @click="hamburgerMenu()">
        </div>

        <div id="hamburger_menu">
              <div id="close_btn" @click="hamburgerMenu()">
                <img src="../../images/shopping_cart images/close-x-svgrepo-com.svg">
              </div>

              <div id="navbuttons_mobile">
                  <ul>
                      <RouterLink to="/catalog">Jaunumi</RouterLink>
                      <RouterLink to="/add-album" v-if="isLoggedIn">Pievienot albumu</RouterLink>
                      <RouterLink to="/sell-item" v-if="isLoggedIn && user.user_role_id === 2">Pārdot preci</RouterLink>
                      <RouterLink to="/sellerform" v-if="isLoggedIn && user.user_role_id !== 2">Kļūt par pārdevēju</RouterLink>
                  </ul>
              </div>

              <div id="rightbuttons_mobile">
                <ul>
                  <RouterLink to="/userprofile" v-if="isLoggedIn">{{user?.username}}</RouterLink>
                  <form action="/logout" @submit.prevent="logout" v-if="isLoggedIn">
                      <button id="logoutbtn">Iziet</button>
                  </form>
                  <RouterLink to="/login" v-if="!isLoggedIn">Ieiet</RouterLink>
                  <RouterLink to="/register" v-if="!isLoggedIn">Reģistrēties</RouterLink>
                </ul>
              </div>
              
              <div id="language_options_mobile">
                <ul>
                  <p>LV</p>
                  <RouterLink to="/en">
                    EN
                  </RouterLink>
                  <RouterLink to="/ru">
                    RU
                  </RouterLink>
                </ul>
              </div>
              
              
              
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
              <div id="item_quantity">
                <button class="quantity_btn" @click="updateItemInShoppingList(index, '-')">-</button>
                <input id="quantity_input" type="number" v-model="item.quantity">
                <button class="quantity_btn" @click="updateItemInShoppingList(index, '+')">+</button>
              </div>
            </div>
            
          </div>
          <a :href="`/checkout`"><button id="checkout_btn">Noformēt pasūtījumu</button></a>
        </div>

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
                    <p>{{ albumItem.price }}$</p>
                </div>

                </div>
            </div>
               
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

            <div class="footer_links">
                <h6>Ātrās saites</h6>

                <ul>
                    <li>Jaunumi</li>
                    <li>Priekšpasūtījumi</li>
                    <li>Izpārdošana</li>
                    <li>Reti atradumi</li>
                    <li>Dāvanu kartes</li>
                </ul>
            </div>

            <div class="footer_links">
                
                <h6>Žanri</h6>

                <ul>
                    <li>Roks</li>
                    <li>Džezs</li>
                    <li>Elektronika</li>
                    <li>Hip-Hopss</li>
                    <li>Klasika</li>
                </ul>
            </div>

          <!-- <div id="subscribe_form">
                <h6>Sekojiet jaunumiem</h6>
                <p>Saņemiet paziņojumus par jaunumiem un ekskluzīviem piedāvājumiem.</p>

                <form action="" method="post">
                    <input id="email_input" placeholder="Ievadiet e-pastu" name="subscription-email" type="email" required>
                    <input id="subscribe_form_submit" type="submit" value="Abonēt">
                </form>
            </div>-->
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

#mobile_btns, #hamburger_menu, #mobile_filters, #filters_btn, #mobile_list_filters {
  display: none;
}

main {
  width: 80vw;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
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

#price_div {
  text-align: end;
}

#price_div p {
  margin-top: 0px;
  width: 100px;
}

#price {
  margin-bottom: 8px;
}

#item_quantity {
  display: flex;
  flex-direction: row;
}

#item_quantity input {
  width: 53px;
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

footer {
  border-top: solid #ECECF0 1px;
}

#footer_top {
  width: 80vw;
  margin: 0 auto;
  padding-top: 50px;
  padding-bottom: 20px;
  display: flex;
  flex-direction: row;
}

footer h6 {
  font-size: 16px;
  margin-top: 15px;
  line-height: 24px;
}

#footer_bottom {
  margin: 0 auto;
  padding-left: 150px;
  padding-right: 150px;
  align-items: center;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  font-size: 14px;
  line-height: 20px;
  letter-spacing: 0px;
  color: #717182;
  border-top: solid #ECECF0 1px;
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


#footer_bottom ul {
  display: flex;
  flex-direction: row;
  gap: 24px;
  list-style: none;
  padding: 0;
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