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
    name: '',
    email: '',
    created_at: '',
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
  console.log('Function called...')
  albumItems.value.forEach(albumItem => {
  if (!genres.includes(albumItem.genre)) {
    genres.push(albumItem.genre)
  }
  })
}

const getCountries = async () => {
  console.log('Function called...')
  albumItems.value.forEach(albumItem => {
  if (!countries.includes(albumItem.country)) {
    countries.push(albumItem.country)
  }
  })
}

const getDecades = async () => {
  console.log('Function called...')
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
getAlbumItems();
loadFromShoppingList();
</script>

<template>
    <body>
        <nav>
        <div id="navwrapper">
        <RouterLink to="/ru">
            <div id="logo">
                <img src="../../images/nav_images/vinyl_icon.svg">
                <p>MusicVault</p>
            </div>
        </RouterLink>

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
            
            <input type="text" id="searchbar" name="recordsearch" placeholder="Поиск записей...">
            <img id="shoppingcart" src="../../images/nav_images/shopping_cart_icon.svg" @click="shoppingMenu()">
            <p>{{user?.name}}</p>
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

        <div id="album_info">
                <div id="album_text_info">
                    <h1> - </h1>
                    <p></p>
                </div>
        </div>


        <div id="offers_section">

            <form id="filters">
            <h1>Фильтры</h1>
                <div id="genre_filters">
                    <h2>Жанр</h2>
                    <div class="genre_filter" v-for="genre in genres" :key="genre">
                        <input type="checkbox" :value="genre" v-model="selectedGenres" @change="filterAlbums">
                        <label>{{ genre }}</label>
                    </div>
                </div>

                <div id="price_range_filters">
                    <h2>Диапазон цен</h2>
                    <div id="price_range_filters_inputs">
                        <input type="number" name="min" v-model="minPrice" @change="filterAlbums">
                        <p> - </p>
                        <input type="number" name="max" v-model="maxPrice" @change="filterAlbums"> 
                    </div>
                </div>

                <div id="decade_filters">
                    <h2>Десятилетие</h2>
                    <div class="decade_filter" v-for="decade in decades" :key="decade">
                        <input type="checkbox" :value="decade" v-model="selectedDecades" @change="filterAlbums">
                        <label>{{ decade }}</label>
                    </div>
                </div>

                <div id="country_filters">
                    <h2>Страна</h2>
                    <div class="country_filter" v-for="country in countries" :key="country">
                        <input type="checkbox" :value="country" v-model="selectedCountries" @change="filterAlbums">
                        <label>{{ country }}</label>
                    </div>
                </div>
        </form>


            <div id="offers_list">
                <h2>Предложения</h2>
                <div id="list_filters">
                  <button class="filter_btn" >Название</button>
                  <button class="filter_btn" >Исполнитель</button>
                  <button class="filter_btn">Жанр</button>
                  <button class="filter_btn">Год</button>
                  <button class="filter_btn">Продавец</button>
                  <button class="filter_btn" >Цена</button> 
                </div>
                <div class="album_card" v-for="albumItem in albumItems" :key="albumItem.id">
                  
                
                <div id="item_info_col">
                    <p>Название: <a :href="`/offer/${albumItem.id}`">{{ albumItem.title }}</a></p>
                    <p>Состояние: {{ albumItem.condition }}</p>
                    <p>Количество: {{ albumItem.quantity }}</p>
                </div>

                <div id="item_seller_col">  
                    <p>{{ albumItem.seller_name }}</p>
                </div>

                <div id="item_price_col">
                    <p>{{ albumItem.price }}</p>
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

#offers_section {
    display: flex;
    gap: 25px;
}

#filters {
  display: flex;
  flex-direction: column;
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