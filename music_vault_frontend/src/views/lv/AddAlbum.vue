<script setup lang="ts">
import Navbar from '@/components/lv/NavbarLv.vue';
import ShoppingMenu from '@/components/lv/ShoppingMenuLv.vue';
import Footer from '@/components/lv/FooterLv.vue';
import axiosInstance from '@/axios';
import {ref} from 'vue';
import { useRouter } from 'vue-router';

const loading = ref(true);

const router = useRouter();

const user = ref({
    username: '',
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
  title_error?: string;
  author_error?: string;
  label_error?: string;
  notes_error?: string;
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
  cover: null as File | null,
  title_error: '',
  author_error: '',
  label_error: '',
  notes_error: '',
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

const validateTrackFields = (): boolean => {
  let hasErrors = false

  tracks.value.forEach(track => {
    const values = Object.values(track).filter((v, i) => i < 4) as string[]
    const anyFilled = values.some(v => v !== '')
    console.log('Track values:', values);
    const allFilled = values.every(v => v !== '')
    console.log('Any filled:', anyFilled, 'All filled:', allFilled);

    track.error = undefined

    if (anyFilled && !allFilled) {
      track.error = 'Lai pievienotu dziesmu sarakstam, jāaizpilda visi lauki.';
      hasErrors = true
    }
  })

  if (hasErrors) {
    return false;
  } else {
    return true;
  }
}

const submitForm = async () => {
  const formData = new FormData()

  Object.entries(album.value).forEach(([key, value]) => {
    if (value !== null) {
      formData.append(key, value as any)
    }
  })

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
    if (response.status === 200) {
        router.push('/');
    }
    } catch (error) {
    console.error(error);
    }
}

function validateForm(form: Album){
  let validTitle = isValidTitle(form.title);
  let validAuthor = isValidAuthor(form.author);
  let validLabel = isValidLabel(form.label);
  let validNotes = isValidNotes(form.notes);
  let validTracks = validateTrackFields(); // It takes tracks separately from the 'tracks' variable.

  // Since hasErrors variable is false then validTracks should also be false.
  if (validTitle && validAuthor && validLabel && validNotes && !validTracks) {
    submitForm();
  }
}

function isValidTitle(title: string): boolean {
    const MAX_TITLE_LENGTH = 20;

    if (title.length === 0) {
        album.value.title_error = 'Lūdzu, ievadiet nosaukumu.';
        return false;
    } else if (title.length > MAX_TITLE_LENGTH) {
        album.value.title_error = 'Nosaukums nedrīkst pārsniegt 20 rakstzīmes.';
        return false;
    } else {
      album.value.title_error = '';
      return true;
    }
}

function isValidAuthor(author: string): boolean {
    const MAX_AUTHOR_LENGTH = 20;

    if (author.length === 0) {
        album.value.author_error = 'Lūdzu, ievadiet autoru.';
        return false;
    } else if (author.length > MAX_AUTHOR_LENGTH) {
        album.value.author_error = 'Autors nedrīkst pārsniegt 20 rakstzīmes.';
        return false;
    } else {
      album.value.author_error = '';
      return true;
    }
}

function isValidLabel(label: string): boolean {
    const MAX_LABEL_LENGTH = 20;

    if (label.length === 0) {
        album.value.label_error = 'Lūdzu, ievadiet izdevniecību.';
        return false;
    } else if (label.length > MAX_LABEL_LENGTH) {
        album.value.label_error = 'Izdevniecība nedrīkst pārsniegt 20 rakstzīmes.';
        return false;
    } else {
      album.value.label_error = '';
      return true;
    }
}

function isValidNotes(notes: string): boolean {
    const MAX_NOTES_LENGTH = 200;

    if (notes.length > MAX_NOTES_LENGTH) {
        album.value.notes_error = 'Piezīmes nedrīkst pārsniegt 200 rakstzīmes.';
        return false;
    } else {
        album.value.notes_error = '';
        return true;
    }
}



getUser();
getGenres();
getCountries();
</script>

<template>
<body>
    <Navbar />

    <main>
        <ShoppingMenu />

        <h1>Pievienot albumu</h1>
        <div id="form_wrapper">
        
            <form id="add_album_with_tracks" @submit.prevent="validateForm(album)" method="POST" enctype="multipart/form-data">
                 <div id="album_wrapper">
                    <div id="input_side">
                        <label>Nosaukums</label>
                        <input class="album_input" name="title" type="text" v-model="album.title">
                        <p v-if="album.title_error" style="color: red;">{{ album.title_error }}</p>

                        <label>Autors</label>
                        <input class="album_input" name="author" type="text" v-model="album.author">
                        <p v-if="album.author_error" style="color: red;">{{ album.author_error }}</p>

                        <label>Žanrs</label>
                        <select class="album_input" name="genre" v-model="album.genre_id">
                          <option value="" disabled>Izvēlieties žanru</option>
                          <option v-for="genre in genres" :key="genre.id" :value="genre.id">{{ genre.genre_title }}</option>
                        </select>
                        <label>Izdevniecība</label>
                        <input class="album_input" name="label" type="text" v-model="album.label">
                        <p v-if="album.label_error" style="color: red;">{{ album.label_error }}</p>

                        <label>Izdošanas datums</label>
                        <input class="album_input" name="release_date" type="date" v-model="album.release_date">
                        <label>Valsts</label>
                        <select class="album_input" name="country" v-model="album.country_id">
                          <option value="" disabled>Izvēlieties valsti</option>
                          <option v-for="country in countries" :key="country.id" :value="country.id">{{ country.country_name }}</option>
                        </select>

                        <label>Piezīmes</label>
                        <input class="album_input" name="notes" type="text" v-model="album.notes">
                        <p v-if="album.notes_error" style="color: red;">{{ album.notes_error }}</p>
                        
                    </div>
                        
                        <div id="album_cover_side">
                            <label>Vāks</label>
                            <input name="cover" type="file" accept="image/*" @change="handleFileChange">
                        </div>
                </div> 
            
            
            
            
                        
                <h1>Dziesmu saraksts</h1>
                        
                        
                        <div id="track_list">

                            <div v-for="(track, index) in tracks" :key="index" class="track_info">
                                <div class="input_div">
                                <div class="input_labels">
                                    <label>Dziesmas Nr.</label>
                                    <input type="number" class="track_nr" :name="`tracks[${index}][position]`" v-model="track.position">
                                </div>
                                <div class="input_labels">
                                    <label>Autors</label>
                                    <input type="text" class="author" :name="`tracks[${index}][artist]`" v-model="track.artist">
                                </div>
                                <div class="input_labels">
                                    <label>Nosaukums</label>
                                    <input type="text" class="title" :name="`tracks[${index}][title]`" v-model="track.song_title">
                                </div>
                                <div class="input_labels">
                                    <label>Ilgums</label>
                                    <input type="text" class="duration" :name="`tracks[${index}][duration]`" v-model="track.duration">
                                </div>
                                </div>
                                <div class="error_box" v-if="track.error">
                                  <p>{{ track.error }}</p>
                                </div>
                            </div>
                        </div>
                <p id="add_more_tracks" @click="addTrack">+ Pievienot vairāk dziesmas</p> 
                <input id="submit_btn" type="submit" value="Pievienot albumu">
                </form>
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

#add_album_with_tracks h1 {
  margin-top: 100px;
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
  padding: 0;
}

#add_album_with_tracks {
  width: 90vw;
  
}

#input_side {
  align-items: center;
  margin: 0 auto;
  width: fit-content;
}

#input_side label {
  align-self: baseline;
}

#input_side input {
  width: 82.333vw;
  height: 10.417vw;
}

#input_side select {
  width: 84vw;
  height: 11.5vw;
}


#form_wrapper {
  display: flex;
  flex-direction: column;
  margin: 0 auto;
  align-items: center;
}

#album_wrapper {
  display: flex;
  flex-direction: column;
  width: 90vw;
  gap: 20px;
}

.input_div {
  display: grid;
  grid-template-columns: repeat(2, 1fr);
  width: fit-content;
  gap: 10px;
  margin: 0 auto;
}

#album_cover_side {
  width: 90vw;
  margin-left: 15px;
  margin-bottom: 50px;
}

.track_info {
  width: fit-content;
}

#track_list {
  width: fit-content;
  align-items: center;
  margin: 0 auto;
}

.input_labels input {
  width: 146px;
  height: 10.417vw;
}

.error_box {
  margin-left: 12px;
}

#add_more_tracks {
  margin-top: 30px;
  margin-left: 12px;
  margin-bottom: 30px;
}


#submit_btn {
  min-width: 84vw;
  min-height: 11.25vw;
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