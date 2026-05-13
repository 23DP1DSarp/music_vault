<script setup lang="ts">
import axiosInstance from '@/axios';
import {ref, type Ref} from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const itemId = route.params.id;

const loading = ref(true);

const user = ref({
    username: '',
    email: '',
});

interface Item {
  id: string;
  title: string;
  quantity: number;
  price: number;
  origin_address: string;
  country_id: number;
  sellers_full_name: string;
  available_quantity: number;
}

const item: Item = {
  id: '',
  title: '',
  quantity: 0,
  price: 0,
  origin_address: '',
  country_id: 0,
  sellers_full_name: '',
  available_quantity: 0,
} 

const albumItem = ref({
    id: '',
    title: '',
    condition: '',
    quantity: 0,
    price: 0,
    description: '',
    picture: '',
    seller_name: '',
    sellers_full_name: '',
    shipping_country: 0,
    origin_address: '',
    album_id: '',
    created_at: '',
});

const album = ref({
    id: '',
    title: '',
    author: '',
    release_date: '',
    genre: '',
    country: '',
    label: '',
    format: '',
    cover: '',
    notes: ''
});

interface Track {
  position: string;
  song_title: string;
  artist: string;
  duration: string;
}

const tracks = ref<Track[]>([]);

const shoppingList = ref<Item[]>([]);

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
    } finally {
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
        window.location.href='/ru';
    }
};


const getItem = async () => {
  try {
    const response = await axiosInstance.get(`/get_album_item/${itemId}`);
    albumItem.value = response.data[0];
    item.id = albumItem.value.id;
    item.title = albumItem.value.title;
    item.available_quantity = albumItem.value.quantity;
    item.quantity = 1;
    item.price = albumItem.value.price;
    item.origin_address = albumItem.value.origin_address;
    item.country_id = albumItem.value.shipping_country;
    item.sellers_full_name = albumItem.value.sellers_full_name;
    console.log(response.data);
  } catch (error) {
    console.error(error);
  } finally {
    getAlbumWithTracks(albumItem.value.album_id);
  }
};


const getAlbumWithTracks = async (itemId: string) => {
  try {
    const response = await axiosInstance.get(`/album_info/${itemId}`);
    album.value = response.data[0];
    tracks.value = response.data[1];
    console.log(response.data);
  } catch (error) {
    console.error(error);
  }
};

const getImageUrl = (path: string): string => {
  if (path.startsWith('http')) {
    return path;
  }

  return `${'http://music-vault-main-sjukhk.laravel.cloud'}/storage/${path}`;
};

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

const addToShoppingList = async () => {
  shoppingList.value.push(item);
  localStorage.setItem("shoppingList", JSON.stringify(shoppingList.value));
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
getItem();
loadFromShoppingList();
</script> 





<template>
    <body v-if="loading !== true">
     <nav>
        <div id="navwrapper">
        <RouterLink to="/ru">
            <div id="logo">
            <img src="../../images/nav_images/vinyl_icon.svg">
            <p>MusicVault</p>
        </div></RouterLink>

        <div id="navbuttons">
            <ul>
                <li>Новинки</li>
                <li>Жанры</li>
                <li>Исполнители</li>
                <li>Форумы</li>
                <RouterLink to="/add-album" v-if="isLoggedIn">Добавить альбом</RouterLink>
            </ul>
        </div>

        <div id="rightbuttons">
            <div id="language_select" name="language">
              <p>RU</p>
              <div id="language_options">
                <RouterLink to="/en">
                  EN
                </RouterLink>
                <RouterLink to="/">
                  LV
                </RouterLink>
              </div>
            </div>
            <input type="text" id="searchbar" name="recordsearch" placeholder="Поиск записей...">
            <img id="shoppingcart" src="../../images/nav_images/shopping_cart_icon.svg" @click="shoppingMenu()">
            <p>{{user?.username}}</p>
            <form action="/logout" @submit.prevent="logout" v-if="isLoggedIn">
                <button id="logoutbtn">Выйти</button>
            </form>
            <RouterLink to="/login" v-if="!isLoggedIn">Войти</RouterLink>
            <RouterLink to="/register" v-if="!isLoggedIn">Регистрация</RouterLink>
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
              <p @click="deleteFromShoppingList(index)">Удалить</p>
            </div>
            <div id="price_div">
              <b><p id="price">{{ item.price }}$</p></b>
              <p>Количество: {{ item.quantity }}</p>
            </div>
            
          </div>
        </div>


        <div id="album_section">
            <div id="album_info">
                <img id="album_cover" v-if="album.cover" :src="getImageUrl(album.cover)" :alt="albumItem.title">
                <div id="album_text_info">
                    <h1>{{album.title}} - {{album.author}}</h1>
                    <p>{{albumItem.description}}</p>
                </div>
            </div>

            <h1>Список треков</h1>
            <div id="tracklist">
                <div id="track_position_col">
                    <h4 id="track_position_title">№</h4>
                    
                    <p class="track_nr" v-for="track in tracks" :key="track.position">{{ track.position }}</p>
                    
                </div>

                <div id="track_title_col">
                    <h4 id="track_title">Название</h4>
                    
                    <p class="title" v-for="track in tracks" :key="track.position">{{ track.song_title }}</p>
                   
                </div>

                <div id="track_artist_col">
                    <h4 id="artist_title">Исполнитель</h4>
                    <p class="artist" v-for="track in tracks" :key="track.position">{{ track.artist }}</p>
                </div>

                <div id="track_duration_col">
                    <h4 id="duration_title">Длительность</h4>
                    <p class="duration" v-for="track in tracks" :key="track.position">{{ track.duration }}</p>
                </div>
                
            </div>
            

        </div>

            <div id="album_data">
                <h1>Данные предложения</h1>
                <p id="author">Продавец: {{ albumItem.seller_name }}</p>
                <p id="release_date">Добавлено: {{ albumItem.created_at }}</p>
                <p id="country">Shipping Страна: {{ albumItem.shipping_country }}</p>
                <p id="genre">Состояние: {{ albumItem.condition }}</p>
                <p id="label">Количество: {{ albumItem.quantity }}</p>
                <p id="format">Цена: {{ albumItem.price }}</p>

                <hr>

                <div id="button_sec">
                    <button id="add_to_cart_btn" @click="addToShoppingList()">Добавить в корзину</button>
                    <a :href="`/albuminfo/${album.id}`"><button id="release_page_btn">Страница релиза</button></a>
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
                    Ваше главное место для музыкальных записей. Открывайте,
                    коллекционируйте и наслаждайтесь музыкой так,
                    как она была создана звучать.
                </p>

                <div id="icons">
                    <img class="icon" src="../../images/footer_images/facebook_icon.svg">
                    <img class="icon" src="../../images/footer_images/instagram_icon.svg">
                    <img class="icon" src="../../images/footer_images/twitter_icon.svg">
                    <img class="icon" src="../../images/footer_images/youtube_icon.svg">
                </div>
                
            </div>

            <div>
                <h6>Быстрые ссылки</h6>

                <ul>
                    <li>Новинки</li>
                    <li>Предзаказы</li>
                    <li>Распродажа</li>
                    <li>Редкие находки</li>
                    <li>Подарочные карты</li>
                </ul>
            </div>

            <div>
                
                <h6>Жанры</h6>

                <ul>
                    <li>Рок</li>
                    <li>Джаз</li>
                    <li>Электроника</li>
                    <li>Хип-хопs</li>
                    <li>Классика</li>
                </ul>
            </div>

            <div id="subscribe_form">
                <h6>Будьте в курсе</h6>
                <p>Получайте уведомления о новинках и эксклюзивных предложениях.</p>

                <form action="" method="post">
                    <input id="email_input" placeholder="Введите email" name="subscription-email" type="email" required>
                    <input id="subscribe_form_submit" type="submit" value="Подписаться">
                </form>
            </div>
        </div>

        
        <div id="footer_bottom">

            <ul>
                <li>Политика конфиденциальности</li>
                <li>Условия использования</li>
                <li>Информация о доставке</li>
                <li>Возврат</li>
            </ul>

            <p>&copy; 2025 MusicVault. Все права защищены.</p>

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
  gap: 150px;
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

#album_section {
    width: 70%;
    display: flex;
    flex-direction: column;
    margin-top: 50px;
}

#album_info {
    display: flex;
    flex-direction: row;
    gap: 30px;
    margin-bottom: 60px;
}

#album_cover {
  width: 250px;
  height: 250px;
  border-radius: 16px;
}

#tracklist  {
  background-color: #ECECF0;
  border-radius: 10px;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  padding-left: 35px;
  padding-right: 35px;
}

#tracklist h4 {
  padding-bottom: 20px;
}

#tracklist p {
  padding-bottom: 15px;
}


.track_data {
  display: flex;
  flex-direction: row;
  margin-left: 35px;
  margin-right: 35px;
  justify-content: space-between;
}

.duration {
  text-align: right;
}

#album_data {
    height: fit-content;
    width: 300px;
    margin-top: 50px;
    background-color: #ECECF0;
    border-radius: 10px;
    padding-left: 30px;
    padding-right: 30px;
    padding-bottom: 21.440px;
}

#album_data h1 {
  padding-bottom: 20px;
}

#album_data p {
  padding-bottom: 10px;
}

#button_sec {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

#add_to_cart_btn {
  width: 100%;
  height: 40px;
  background-color: #030213;
  color: #FFFFFF;
  border-style: none;
  border-radius: 8px;
  text-align: center;
  vertical-align: middle;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  font-size:18px;
  line-height: 20px;
  letter-spacing: 0px;
  cursor: pointer;
}

#release_page_btn {
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