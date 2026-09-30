<script setup lang="ts">
import axiosInstance from '@/axios';
import Navbar from '@/components/lv/NavbarLv.vue';
import ShoppingMenu from '@/components/lv/ShoppingMenuLv.vue';
import Footer from '@/components/lv/FooterLv.vue';
import {ref, type Ref} from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const itemId = route.params.id;

const loading = ref(true);

const user = ref({
    username: '',
    email: '',
    user_role_id: 0,
});

interface Item {
  id: string;
  title: string;
  quantity: number;
  price: number;
  origin_address: string;
  shipping_country_id: number;
  sellers_full_name: string;
  available_quantity: number;
}

const item: Item = {
  id: '',
  title: '',
  quantity: 0,
  price: 0,
  origin_address: '',
  shipping_country_id: 0,
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
    shipping_country_id: 0,
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

const addedToWishlist = ref(false);

const addedToShoppingList = ref(false);

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

const getItem = async () => {
  try {
    const response = await axiosInstance.get(`/get_item/${itemId}`);
    albumItem.value = response.data[0];
    item.id = albumItem.value.id;
    item.title = albumItem.value.title;
    item.available_quantity = albumItem.value.quantity;
    item.quantity = 1;
    item.price = albumItem.value.price;
    item.origin_address = albumItem.value.origin_address;
    item.shipping_country_id = albumItem.value.shipping_country_id;
    item.sellers_full_name = albumItem.value.sellers_full_name;
    //console.log(response.data);
  } catch (error) {
    console.error(error);
  } finally {
    if (albumItem.value && albumItem.value.album_id) {
      await getAlbumWithTracks(String(albumItem.value.album_id));
    }
    isAddedToShoppingList();
  }
};


const getAlbumWithTracks = async (itemId: string) => {
  try {
    const response = await axiosInstance.get(`/album_info/${itemId}`);
    album.value = response.data[0];
    tracks.value = response.data[1];
    //console.log(response.data);
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

const addToShoppingList = async () => {
  shoppingList.value.push(item);
  localStorage.setItem("shoppingList", JSON.stringify(shoppingList.value));
  addedToShoppingList.value = true;
}

const loadFromShoppingList = async () => {
  const stored = localStorage.getItem('shoppingList');
  if (stored) {
  shoppingList.value = JSON.parse(stored);
  }
}

const deleteFromShoppingList = async (index?: number) => { 
  if (typeof index === 'number') {
    shoppingList.value.splice(index, 1);
    localStorage.setItem("shoppingList", JSON.stringify(shoppingList.value));
    return;
  }

  if (!item.id) return;
  const idx = shoppingList.value.findIndex(i => String(i.id) === String(item.id));
  if (idx !== -1) {
    shoppingList.value.splice(idx, 1);
    localStorage.setItem("shoppingList", JSON.stringify(shoppingList.value));
    addedToShoppingList.value = false;
  }
}

const addToWishlist = async () => {
  try {
    const response = await axiosInstance.post(`/add_to_wishlist/${itemId}`);
    //console.log(response.data);
  } catch (error) {
    console.error(error);
  } finally {
    isAddedToWishlist();
  }
}

const deleteFromWishlist = async () => {
  try {
    const response = await axiosInstance.delete(`/delete_from_wishlist/${itemId}`);
    //console.log(response.data);
  } catch (error) {
    console.error(error);
  } finally {
    isAddedToWishlist();
  }
}

const isAddedToWishlist = async () => {
  try {
    const response = await axiosInstance.get(`/is_added_to_wishlist/${itemId}`);
    addedToWishlist.value = response.data.added_to_wishlist;
    //console.log(response.data);
  } catch (error) {
    console.error(error);
  }
}

const isAddedToShoppingList = () => {
  let list: Item[] = [];
  if (shoppingList.value && shoppingList.value.length) {
    list = shoppingList.value;
  } else {
    const stored = localStorage.getItem('shoppingList');
    if (stored) {
      try {
        list = JSON.parse(stored);
      } catch (e) {
        list = [];
      }
    }
  }

  if (!item.id) {
    addedToShoppingList.value = false;
    return false;
  }

  addedToShoppingList.value = list.some(shoppingItem => String(shoppingItem.id) === String(item.id));
  return addedToShoppingList.value;
}

getUser();
getItem();
loadFromShoppingList();
isAddedToWishlist();
isAddedToShoppingList();
</script> 

<template>
    <body v-if="loading !== true">
    <Navbar />

    <main>
        <ShoppingMenu />

        <div id="album_section">
            <div id="album_info">
                <img id="album_cover" v-if="album.cover" :src="getImageUrl(album.cover)" :alt="albumItem.title">
                <div id="album_text_info">
                    <h1>{{album.title}} - {{album.author}}</h1>
                    <p>{{albumItem.description}}</p>
                </div>
            </div>

            <div id="album_info_mobile">
              <div id="album_text_info_mobile">
                <img id="album_cover" v-if="album.cover" :src="getImageUrl(album.cover)" :alt="album.title">
                <h1>{{album.title}} - {{album.author}}</h1>
              </div>
              <p>{{albumItem.description}}</p>
            </div>

            <h1 id="tracklist_title">Dziesmu saraksts</h1>
            <div id="tracklist">
                <div id="track_position_col">
                    <h4 id="track_position_title">№</h4>
                    
                    <p class="track_nr" v-for="track in tracks" :key="track.position">{{ track.position }}</p>
                    
                </div>

                <div id="track_title_col">
                    <h4 id="track_title">Nosaukums</h4>
                    
                    <p class="title" v-for="track in tracks" :key="track.position">{{ track.song_title }}</p>
                   
                </div>

                <div id="track_artist_col">
                    <h4 id="artist_title">Mākslinieks</h4>
                    <p class="artist" v-for="track in tracks" :key="track.position">{{ track.artist }}</p>
                </div>

                <div id="track_duration_col">
                    <h4 id="duration_title">Ilgums</h4>
                    <p class="duration" v-for="track in tracks" :key="track.position">{{ track.duration }}</p>
                </div>
                
            </div>
            

        </div>

            <div id="album_data">
                <h1>Piedāvājuma dati</h1>
                <p id="author">Pārdevējs: {{ albumItem.seller_name }}</p>
                <p id="release_date">Pievienots: {{ albumItem.created_at }}</p>
                <p id="country">Sūtīšanas valsts: {{ albumItem.shipping_country }}</p>
                <p id="genre">Stāvoklis: {{ albumItem.condition }}</p>
                <p id="label">Daudzums: {{ albumItem.quantity }}</p>
                <p id="format">Cena: {{ albumItem.price + "€"}}</p>

                <hr>

                <div id="button_sec">
                    <button id="add_to_cart_btn" @click="addToShoppingList()" v-if="!addedToShoppingList">Pievienot grozam</button>
                    <button id="already_added_btn" @click="deleteFromShoppingList()" v-else>Albums ir pievienots grozam</button>
                    <button id="add_to_wishlist_btn" @click="addToWishlist" v-if="!addedToWishlist">Pievienot vēlmju sarakstam</button>
                    <button id="already_added_btn" @click="deleteFromWishlist" v-else>Albums ir pievienots vēlmju sarakstam</button>
                    <a :href="`/albuminfo/${album.id}`"><button id="release_page_btn">Skatīt albuma lapu</button></a>
                </div>
            </div>

            <div id="album_data_mobile">
              <h1>Albuma dati</h1>
              <div id="album_data_mobile_wrapper">
                <p id="author">Pārdevējs: {{ albumItem.seller_name }}</p>
                <p id="release_date">Pievienots: {{ albumItem.created_at }}</p>
                <p id="country">Sūtīšanas valsts: {{ albumItem.shipping_country }}</p>
                <p id="genre">Stāvoklis: {{ albumItem.condition }}</p>
                <p id="label">Daudzums: {{ albumItem.quantity }}</p>
                <p id="format">Cena: {{ albumItem.price + "€"}}</p>
                <hr>

                 <div id="button_sec">
                    <button id="add_to_cart_btn" @click="addToShoppingList()" v-if="!addedToShoppingList">Pievienot grozam</button>
                    <button id="already_added_btn" @click="deleteFromShoppingList()" v-else>Albums ir pievienots grozam</button>
                    <button id="add_to_wishlist_btn" @click="addToWishlist" v-if="!addedToWishlist">Pievienot vēlmju sarakstam</button>
                    <button id="already_added_btn" @click="deleteFromWishlist" v-else>Albums ir pievienots vēlmju sarakstam</button>
                    <a :href="`/albuminfo/${album.id}`"><button id="release_page_btn">Skatīt albuma lapu</button></a>
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

main {
  width: 80vw;
  margin: 0 auto;
  display: flex;
  flex-direction: row;
  gap: 150px;
  padding-bottom: 50px;
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

#add_to_wishlist_btn {
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

#already_added_btn {
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


main {
  display: flex;
  flex-direction: column;
  gap: 50px;
}

#album_info {
  display: none;
}

#album_info_mobile {
  display: flex;
  flex-direction: column;
  gap: 20px;
  width: 100%;
}

#album_text_info_mobile {
  display: flex;
  flex-direction: row;
  width: fit-content;
  gap: 55px;
  align-items: center;
}

#album_text_info_mobile img {
  height: 100px;
  border-radius: 16px;
  width: 200px;
}


#album_text_info_mobile h1 {
  font-size: 19.53px;
  line-height: 28px;
  letter-spacing: -0.5px;
  margin: 0;
}

#album_info_mobile p {
  font-size: 13.89px;
  line-height: 20px;
  letter-spacing: 0px;
  width: fit-content;
}

#tracklist  {
  background-color: #ECECF0;
  border-radius: 10px;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  padding-left: 35px;
  padding-right: 35px;
  width: max-content;
  gap: 40px;
}

#tracklist_title {
  width: max-content;
  margin-top: 50px;
  margin-left: 25px;
  text-align: center;
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

#track_artist_col {
  display: none;
}

.duration {
  text-align: right;
}

#album_data {
  display: none;
}

#album_data_mobile {
  display: block;
}

#album_data_mobile_wrapper {
  width: 82%;
  max-width: 357px;
  padding-top: 5px;
  background-color: #ECECF0;
  border-radius: 10px;
  padding-left: 30px;
  padding-right: 30px;
  padding-bottom: 21.440px;
  z-index: 1;
}

#album_data_mobile h1 {
  text-align: center;
  vertical-align: middle;
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