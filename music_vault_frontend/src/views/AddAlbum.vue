<script setup lang="ts">
import axiosInstance from '@/axios';
import {ref} from 'vue';
import type { s, un } from 'vue-router/dist/router-CWoNjPRp.mjs';


const loading = ref(true);

const user = ref({
    name: '',
    email: '',
});

const isLoggedIn = ref(false);

interface Genre {
  id: number;
  genre_title: string;
}

interface Country {
  id: number;
  country_name: string;
}

const genres = ref<Genre[]>([]);

const countries = ref<Country[]>([]);

interface Album {
  title: string
  author: string
  genre_id: number
  label: string
  release_date: string
  country_id: number
  notes: string
  cover: File | null
}

interface Track {
  position: string
  artist: string
  song_title: string
  duration: string
  error?: string
}

const album = ref<Album>({
  title: '',
  author: '',
  genre_id: 0,
  label: '',
  release_date: '',
  country_id: 0,
  notes: '',
  cover: null as File | null
})

const tracks = ref<Track[]>([
  { position: '', artist: '', song_title: '', duration: '' },
  { position: '', artist: '', song_title: '', duration: '' },
  { position: '', artist: '', song_title: '', duration: '' },
  { position: '', artist: '', song_title: '', duration: '' }
])

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
        window.location.href='/';
    }
}

const getGenres = async () => {
    try {
      const response = await axiosInstance.get('/getgenres');
      genres.value = response.data;
      console.log(genres.value);
    } catch (error) {
      console.error(error);
    }
}

const getCountries = async () => {
    try {
      const response = await axiosInstance.get('/getcountries');
      countries.value = response.data;
      console.log(countries.value);
    } catch (error) {
      console.error(error);
    }
}

const addTrack = () => {
  tracks.value.push({
    position: '',
    artist: '',
    song_title: '',
    duration: ''
  })
}


const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];
  console.log('Selected file:', file);
  if (file) {
    album.value.cover = file;
  }
};

const validateForm = (e: Event) => {
  console.log('Validating form...');
  let hasErrors = false

  tracks.value.forEach(track => {
    const values = Object.values(track).filter((v, i) => i < 4) as string[]
    const anyFilled = values.some(v => v !== '')
    console.log('Track values:', values);
    const allFilled = values.every(v => v !== '')
    console.log('Any filled:', anyFilled, 'All filled:', allFilled);

    track.error = undefined

    if (anyFilled && !allFilled) {
      track.error = 'Track is incomplete. Fill all fields or leave it empty.'
      hasErrors = true
    }
  })

  if (hasErrors) {
    e.preventDefault();
  } else {
    submitForm();
  }
}


const submitForm = async () => {
  const formData = new FormData()

  // Album fields
  Object.entries(album.value).forEach(([key, value]) => {
    if (value !== null) {
      formData.append(key, value as any)
    }
  })

  // Tracks
  tracks.value.forEach((track, index) => {
    if (track.position !== '' && track.artist !== '' && track.song_title !== '' && track.duration !== '') {
        formData.append(`tracks[${index}][position]`, track.position)
        formData.append(`tracks[${index}][artist]`, track.artist)
        formData.append(`tracks[${index}][song_title]`, track.song_title)
        formData.append(`tracks[${index}][duration]`, track.duration)
    }
  })

  try {
    console.log('Submitting form with data:', {
      album: album.value,
      tracks: tracks.value
    });
    const response = await axiosInstance.post('/add_album_with_tracks', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    console.log(response.data);
    } catch (error) {
    console.error(error);
    }
}

getUser();
getGenres();
getCountries();
</script>

<template>
<body>
    <nav>
        <div id="navwrapper">
        <RouterLink to="/">
            <div id="logo">
            <img src="../images/nav_images/vinyl_icon.svg">
            <p>MusicVault</p>
        </div></RouterLink>

        <div id="navbuttons">
            <ul>
                <li>New Releases</li>
                <li>Genres</li>
                <li>Artists</li>
                <li>Forums</li>
            </ul>
        </div>

        <div id="rightbuttons">
            
            <input type="text" id="searchbar" name="recordsearch" placeholder="Search records...">
            <p>{{user?.name}}</p>
            <form action="/logout" @submit.prevent="logout" v-if="isLoggedIn">
                <button id="logoutbtn">Log out</button>
            </form>
            <RouterLink to="/login" v-if="!isLoggedIn">Log In</RouterLink>
            <RouterLink to="/register" v-if="!isLoggedIn">Sign Up</RouterLink>
        </div>
    </div>
    </nav>

    <main>
        <h1>Add Album</h1>
        <div id="form_wrapper">
        
            <form id="add_album_with_tracks" @submit.prevent="validateForm($event)" method="POST" enctype="multipart/form-data">
                <div id="album_wrapper">
                    <div id="input_side">
                        <label>Title</label>
                        <input class="album_input" name="title" type="text" v-model="album.title">
                        <label>Author</label>
                        <input class="album_input" name="author" type="text" v-model="album.author">
                        <label>Genre</label>
                        <select class="album_input" name="genre" v-model="album.genre_id">
                          <option value="" disabled>Select genre</option>
                          <option v-for="genre in genres" :key="genre.id" :value="genre.id">{{ genre.genre_title }}</option>
                        </select>
                        <label>Label</label>
                        <input class="album_input" name="label" type="text" v-model="album.label">
                        <label>Date of release</label>
                        <input class="album_input" name="release_date" type="date" v-model="album.release_date">
                        <label>Country</label>
                        <select class="album_input" name="country" v-model="album.country_id">
                          <option value="" disabled>Select country</option>
                          <option v-for="country in countries" :key="country.id" :value="country.id">{{ country.country_name }}</option>
                        </select>
                        <label>Notes</label>
                        <input class="album_input" name="notes" type="text" v-model="album.notes">
                    </div>
                        
                        <div id="album_cover_side">
                            <label>Cover</label>
                            <input name="cover" type="file" accept="image/*" @change="handleFileChange">
                        </div>
                </div>
            
            
            
            
                        
                        <h1>Track List</h1>
                        
                        
                        <div id="track_list">

                            <div v-for="(track, index) in tracks" :key="index" class="track_info">
                                <div class="input_div">
                                <div class="input_labels">
                                    <label>Track Nr.</label>
                                    <input type="number" class="track_nr" :name="`tracks[${index}][position]`" v-model="track.position">
                                </div>
                                <div class="input_labels">
                                    <label>Author</label>
                                    <input type="text" class="author" :name="`tracks[${index}][artist]`" v-model="track.artist">
                                </div>
                                <div class="input_labels">
                                    <label>Title</label>
                                    <input type="text" class="title" :name="`tracks[${index}][title]`" v-model="track.song_title">
                                </div>
                                <div class="input_labels">
                                    <label>Duration</label>
                                    <input type="text" class="duration" :name="`tracks[${index}][duration]`" v-model="track.duration">
                                </div>
                                </div>
                                <div class="error_box" v-if="track.error">
                                  <p>{{ track.error }}</p>
                                </div>
                            </div>
                        </div>
                <p id="add_more_tracks" @click="addTrack">+ Add more tracks</p> 
                <input id="submit_btn" type="submit" value="Add Album">        
                </form>
            </div> 
                
    </main>

    <footer>
        <div id="footer_wrapper">
        <div id="footer_top">
            <div id="footer_info">
                <div id="footer_logo">
                    <img src="../images/footer_images/vinyl_icon.svg">
                    <p>MusicVault</p>
                </div>
                <p id="footer_info_text">
                    Your premier destination for music records. Discover,
                    collect, and enjoy music the way it was meant to be
                    heard.
                </p>

                <div id="icons">
                    <img class="icon" src="../images/footer_images/facebook_icon.svg">
                    <img class="icon" src="../images/footer_images/instagram_icon.svg">
                    <img class="icon" src="../images/footer_images/twitter_icon.svg">
                    <img class="icon" src="../images/footer_images/youtube_icon.svg">
                </div>
                
            </div>

            <div>
                <h6>Quick Links</h6>

                <ul>
                    <li>New Releases</li>
                    <li>Pre-Orders</li>
                    <li>Sale Items</li>
                    <li>Rare Finds</li>
                    <li>Gift Cards</li>
                </ul>
            </div>

            <div>
                
                <h6>Genres</h6>

                <ul>
                    <li>Rock</li>
                    <li>Jazz</li>
                    <li>Electronic</li>
                    <li>Hip-Hop</li>
                    <li>Classical</li>
                </ul>
            </div>

            <div id="subscribe_form">
                <h6>Stay Updated</h6>
                <p>Get notified about new releases and exclusive deals.</p>

                <form action="" method="post">
                    <input id="email_input" placeholder="Enter your email" name="subscription-email" type="email" required>
                    <input id="subscribe_form_submit" type="submit" value="Subscribe">
                </form>
            </div>
        </div>

        
        <div id="footer_bottom">

            <ul>
                <li>Privacy Policy</li>
                <li>Terms of Service</li>
                <li>Shipping Info</li>
                <li>Returns</li>
            </ul>

            <p>&copy; 2025 MusicVault. All rights reserved.</p>

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
  padding-top: 65px;
  padding-bottom: 65px;
  margin: 0 auto;
}

main h1 {
  text-align: center;
  margin-bottom: 25px;
}

#form_wrapper {
  display: flex;
  flex-direction: column;
  justify-content: center;
  width: fit-content;
  align-items: start;
  margin: 0 auto;
  gap: 85px;
}

#add_album_with_tracks {
  display: flex;
  flex-direction: column;
}



#album_wrapper {
  display: flex;
  flex-direction: row;
  gap: 150px;
}

#input_side {
  width: 380px;
  display: flex;
  flex-direction: column;
}

#album_cover_side {
  display: flex;
  flex-direction: column;
}

label {
  line-height: 28px;
  letter-spacing: -0.5px;
  margin-left: 10px;
  margin-top: 6px;
  margin-bottom: 6px;
  color: #C3C3C3;
}

#submit_btn {
  min-width: 386px;
  min-height: 54px;
  margin-top: 50px;
  background-color: #000000;
  color: #E4E4E4;
  font-size: 18px;
  font-weight: normal;
  line-height: 28px;
  letter-spacing: -0.5px;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  border-radius: 8px;
  border: none;
  margin: 0 auto;
}

.album_input {
  width: 380px;
  height: 50px;
  border-style: solid;
  border-color: #000000;
  border-radius: 8px;
  border-width: 1px;
  padding: 1px 2px;
}

#track_list_form {
  display: flex;
  flex-direction: column;
  width: 860px;
  margin: 0 auto;
}

#track_list {
  display: flex;
  flex-direction: column;
  gap: 30px;
}

.input_div {
  display: flex;
  flex-direction: row;
  gap: 40px;
}

.input_labels {
 display: flex;
 flex-direction: column; 
}

.input_labels label {
  margin-left: 5px;
  margin-bottom: 6px;
}

#track_list input {
  border-style: solid;
  border-color: #000000;
  border-radius: 8px;
  border-width: 1px;
  padding: 1px 2px;
}

.track_info {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.track_nr {
  width: 70px;
  height: 50px;
}

.author {
  width: 320px;
  height: 50px;
}

.title {
  width: 250px;
  height: 50px;
}

.duration {
  width: 70px;
  height: 50px;
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