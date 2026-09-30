<script setup lang="ts">
import Navbar from '@/components/lv/NavbarLv.vue';
import ShoppingMenu from '@/components/lv/ShoppingMenuLv.vue';
import Footer from '@/components/lv/FooterLv.vue';
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
    user_role_id: 0,
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

const getCollectionAlbums = async () => {
  try {
    const response = await axiosInstance.get('/getcollection');
    collectionAlbums.value = response.data;
    //console.log(response.data);
  } catch (error) {
    console.error(error);
  }
}

const getWishlistItems = async () => {
  try {
    const response = await axiosInstance.get('/getwishlist');
    wishlistItems.value = response.data;
    //console.log(response.data);
  } catch (error) {
    console.error(error);
  }
}

const getUser = async () => {
    try {
        const response = await axiosInstance.get('/user');
        user.value = response.data;
        isLoggedIn.value = true;
        //console.log(response.data);
        //console.log(user.value);
    } catch (error) {
        console.error(error);
        isLoggedIn.value = false;
    } finally {
        getUserCountry({ userId: user.value.id });
        loading.value = false;
    }
};

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

getUser();
getCollectionAlbums();
getWishlistItems();
</script>


<template>
    <body>
    <Navbar />

    <main>
        <ShoppingMenu />

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
                <div class="item_card" v-for="item in wishlistItems" :key="item.id">
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
  padding-top: 40px;
  padding-bottom: 40px;
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
  width: 90vw;
  display: flex;
  flex-direction: column;
  padding-top: 0px;
}

#profile_main {
  display: flex;
  flex-direction: column;
  align-items: center;
}


#profile_head {
  align-items: center;
  text-align: center;
}

#profile_info {
  padding-left: 40px;
  padding-right: 40px;
}


#album_collection, #wishlist {
  width: auto;
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