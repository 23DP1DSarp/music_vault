<script setup lang="ts">
import Navbar from '@/components/ru/NavbarRu.vue';
import ShoppingMenu from '@/components/ru/ShoppingMenuRu.vue';
import Footer from '@/components/ru/FooterRu.vue';
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
    item.country_id = albumItem.value.shipping_country_id;
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


const addToShoppingList = async () => {
  shoppingList.value.push(item);
  localStorage.setItem("shoppingList", JSON.stringify(shoppingList.value));
}

getUser();
getItem();
</script> 

<template>
    <body>
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
</style>