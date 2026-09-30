<script setup lang="ts">
import Navbar from '@/components/lv/NavbarLv.vue';
import ShoppingMenu from '@/components/lv/ShoppingMenuLv.vue';
import Footer from '@/components/lv/FooterLv.vue';
import axiosInstance from '@/axios';
import {ref} from 'vue';
import { useRoute } from 'vue-router';

const route = useRoute();
const albumId = route.params.id;

const loading = ref(true);

const user = ref({
    username: '',
    email: '',
    user_role_id: 0,
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

const isLoggedIn = ref(false);

const addedToCollection = ref(false);

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

const getAlbumwithTracks = async () => {
  try {
    const response = await axiosInstance.get(`/album_info/${albumId}`);
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

const addToCollection = async () => {
  try {
    const response = await axiosInstance.post(`/add_to_collection/${album.value.id}`);
    //console.log(response.data);
  } catch (error) {
    console.error(error);
  } finally {
    isAddedToCollection();
  }
}

const isAddedToCollection = async () => {
  try {
    const response = await axiosInstance.get(`/is_added_to_collection/${albumId}`);
    addedToCollection.value = response.data.added_to_collection;
  } catch (error) {
    console.error(error);
  }
}

const deleteFromCollection = async () => {
  try {
    const response = await axiosInstance.delete(`/delete_from_collection/${albumId}`);
    //console.log(response.data);
  } catch (error) {
    console.error(error);
  } finally {
    isAddedToCollection();
  }
}

getUser();
getAlbumwithTracks();
isAddedToCollection();
</script> 


<template>
    <body>
    <Navbar />

    <main>
        <ShoppingMenu />

        <div id="album_section">
            <div id="album_info">
                <img id="album_cover" v-if="album.cover" :src="getImageUrl(album.cover)" :alt="album.title">
                <div id="album_text_info">
                    <h1>{{album.title}} - {{album.author}}</h1>
                    <p>{{album.notes}}</p>
                </div>
            </div>

            <div id="album_info_mobile">
              <div id="album_text_info_mobile">
                <img id="album_cover" v-if="album.cover" :src="getImageUrl(album.cover)" :alt="album.title">
                <h1>{{album.title}} - {{album.author}}</h1>
              </div>
              <p>{{album.notes}}</p>
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
                <h1>Albuma dati</h1>
                <p id="author">Autors: {{album.author}}</p>
                <p id="release_date">Izdošanas datums: {{album.release_date}}</p>
                <p id="country">Valsts: {{album.country}}</p>
                <p id="genre">Žanrs: {{album.genre}}</p>
                <p id="label">Izdevniecība: {{album.label}}</p>
                <hr>

                <div id="button_sec">
                    <button id="add_to_collection_btn" @click="addToCollection" v-if="!addedToCollection">Pievienot kolekcijai</button>
                    <button id="already_added_btn" @click="deleteFromCollection" v-else>Albums ir pievienots kolekcijai</button>
                </div>
            </div>

            
           <div id="album_data_mobile">
              <h1>Albuma dati</h1>
              <div id="album_data_mobile_wrapper">
                <p id="author">Autors: {{album.author}}</p>
                <p id="release_date">Izdošanas datums: {{album.release_date}}</p>
                <p id="country">Valsts: {{album.country}}</p>
                <p id="genre">Žanrs: {{album.genre}}</p>
                <p id="label">Izdevniecība: {{album.label}}</p>
                <hr>

                <div id="button_sec">
                    <button id="add_to_collection_btn" @click="addToCollection" v-if="!addedToCollection">Pievienot kolekcijai</button>
                    <button id="already_added_btn" @click="deleteFromCollection" v-else>Albums ir pievienots kolekcijai</button>
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

#add_to_collection_btn {
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
  font-size: 19.53px;
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

#tracklist_title {
  width: max-content;
  margin-top: 50px;
  margin-left: 25px;
  text-align: center;
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

#footer_wrapper {
  width: 90vw;
  margin: 0 auto;
  padding: 0;
  align-items: center;
  text-align: center;
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
  text-align: center;
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
  justify-content: center;
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
  text-align: center;
}

#footer_bottom, #footer_bottom ul {
  display: flex;
  flex-direction: column;
  width: 100%;
  gap: 20px;
  padding-top: 20px;
  padding-bottom: 20px;
  padding-left: 0;
  padding-right: 0;
  align-items: center;
  text-align: center;
}


}


</style>