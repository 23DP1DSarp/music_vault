<script setup lang="ts">
import axiosInstance from '@/axios';
import {ref} from 'vue';
import { useRouter } from 'vue-router';

const loading = ref(true);

const router = useRouter();

const sortOrder = ref('asc');

const sortBy = ref('title');

const user = ref({
    id: 0,
    username: '',
    email: '',
    created_at: new Date(),
    date_of_birth: new Date(),
    country_id: 0,
    country: '',
});

const isLoggedIn = ref(false);

interface Album {
  id: number;
  title: string;
  author: string;
  release_date: Date;
  genre: string;
  cover: string;
}

interface Item {
  id: string;
  title: string;
  quantity: number;
  seller_name: string;
  price: number;
  picture: string;
}

const collectionAlbums = ref<Album[]>([]);

const wishlistItems = ref<Item[]>([]);

const shoppingList = ref<Item[]>([]);

const getCollectionAlbums = async () => {
  try {
    const response = await axiosInstance.get('/getcollection');
    collectionAlbums.value = response.data;
    console.log(response.data);
  } catch (error) {
    console.error(error);
  }
}

const getWishlistItems = async () => {
  try {
    const response = await axiosInstance.get('/getwishlist');
    wishlistItems.value = response.data;
    console.log(response.data);
  } catch (error) {
    console.error(error);
  }
}

const getUser = async () => {
    try {
        const response = await axiosInstance.get('/user');
        user.value = response.data;
        isLoggedIn.value = true;
        console.log(response.data);
        console.log(user.value);
    } catch (error) {
        console.error(error);
        isLoggedIn.value = false;
    } finally {
        getUserCountry({ userId: user.value.id });
        loading.value = false;
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

const getImageUrl = (path: string): string => {
  if (path.startsWith('http')) {
    return path;
  }

  return `${'http://music-vault-main-sjukhk.laravel.cloud'}/storage/${path}`;
};

const sortAlbums = async (sortBy: string) => {
  loading.value = true;

  if (sortOrder.value === 'desc') {
    sortOrder.value = 'asc';
  } else {
    sortOrder.value = 'desc';
  }

  try {
    const { data } = await axiosInstance.get('/ordercollectionalbums', {
      params: {
        sortBy: sortBy,
        sortOrder: sortOrder.value,
      }
    });
    collectionAlbums.value = data;
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
  }
}

const getUserCountry = async (payload: { userId: number }) => {
  try {
    const response = await axiosInstance.get('/getusercountry', {
      params: {
        userId: payload.userId,
      }
    });
    user.value.country = response.data.name;
  } catch (error) {
    console.error(error);
  }
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
getCollectionAlbums();
getWishlistItems();
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
                <RouterLink to="/add-album" v-if="isLoggedIn">Pievienot albumu</RouterLink>
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
      
        <div id="profile_head">
            <img>
                <h2>{{user?.username}}</h2>
        </div>

        <div id="profile_main">
            <aside id="profile_info">
                <h3>Profila informācija</h3>
                <p><strong>Dalībnieks kopš: </strong>{{new Date(user.created_at).toLocaleDateString()}}</p>
                <p><strong>Dzimšanas diena: </strong>{{ new Date(user.date_of_birth).toLocaleDateString() }}</p>
                <p><strong>Valsts: </strong>{{ user.country }}</p>
                <a :href="`/profilesettings`"><button id="profile_settings_btn">Profila iestatījumi</button></a>
            </aside>

            <div id="lists">
            <div id="album_collection">
                <h3>Kolekcija</h3>
                <div id="filters">
                    <button class="filter_btn" @click="sortAlbums('title')">Nosaukums</button>
                    <button class="filter_btn" @click="sortAlbums('author')">Autors</button>
                    <button class="filter_btn" @click="sortAlbums('genre')">Žanrs</button>
                    <button class="filter_btn" @click="sortAlbums('release_date')">Gads</button>
                </div>
                <div id="albums_grid">
                <div class="album_card" v-for="album in collectionAlbums" :key="album.id">
                    <img v-if="album.cover" :src="getImageUrl(album.cover)" :alt="album.title">
                    <div id="text_info">
                        <p>{{ album.title }}</p>
                        <p>{{ album.author }}</p>
                        <p>{{ album.genre }}</p>
                        <p>{{ album.release_date }}</p>
                    </div>
                </div>
                </div>
            </div>

            <div id="wishlist">
                <h3>Vēlmju saraksts</h3>
                <div id="items_grid">
                  <div id="filters">
                    <button class="filter_btn" @click="sortAlbums('title')">Nosaukums</button>
                    <button class="filter_btn" @click="sortAlbums('author')">Autors</button>
                    <button class="filter_btn" @click="sortAlbums('genre')">Žanrs</button>
                    <button class="filter_btn" @click="sortAlbums('release_date')">Gads</button>
                </div>
                <div class="item_card" v-for="item in wishlistItems" :key="item.id">
                    <img v-if="item.picture" :src="getImageUrl(item.picture)" :alt="item.title">
                    <div id="text_info">
                        <p>Nosaukums: {{ item.title }}</p>
                        <p>Pārdevējs: {{ item.seller_name }}</p>
                        <p>Cena: {{ item.price }}$</p>
                        <p>Daudzums: {{ item.quantity }}</p>
                    </div>
                </div>
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
  src: url('../../assets/fonts/Segoe-UI-Symbol.ttf');
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
  padding-top: 40px;
  padding-bottom: 40px;
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

#profile_main {
    display: flex;
    flex-direction: row;
    gap: 40px;
    margin-top: 30px;
}


#profile_info {
    width: 250px;
    height: fit-content;
    padding: 20px;
    border: #ECECF0 solid 1px;
    border-radius: 8px;
    font-size: 16px;
    line-height: 20px;
    letter-spacing: 0px;
    color: #0A0A0A;
    background-color: #ECECF0;
}

#profile_settings_btn {
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

#album_collection, #wishlist {
    width: 100%;
    height: fit-content;
    padding: 20px;
    border: #ECECF0 solid 1px;
    border-radius: 8px;
    font-size: 16px;
    line-height: 20px;
    letter-spacing: 0px;
    color: #0A0A0A;
}

#filters {
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

#lists {
    display: flex;
    flex-direction: column;
    gap: 40px;
    width: 100%;
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

.item_card {
    width: 100%;
    height: 200px;
    padding: 20px 0px 20px 0px;
    border-radius: 8px;
    display: flex;
    flex-direction: row;
    gap: 33px;
}

.album_card img, .item_card img {
    width: 150px;
    height: 150px;
    border-radius: 8px;
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