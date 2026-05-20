<script setup lang="ts">
import axiosInstance from '@/axios';
import {ref} from 'vue';
import { useRouter } from 'vue-router';

const loading = ref(true);

const router = useRouter();

const user = ref({
    username: '',
    email: '',
});

const albums = ref<{id: number, title: string}[]>([]);

const shoppingList = ref<Item[]>([]);

const categories = ref<string[]>([
    'Album',
    'Instrument',
    'Service',
]);

const conditions = ref<string[]>([
    'Good',
    'Used',
    'Bad',
]);

interface SellItemForm {
    title: string;
    category: string;
    quantity: number;
    price: number;
    model: string | null;
    type: string | null;
    duration: number;
    condition: string;
    description: string;
    picture: File | null;
    album_id: number | null;
}

interface Item {
  id: string;
  title: string;
  quantity: number;
  price: number;
}

const item = ref<SellItemForm>({
    title: '',
    category: '',
    quantity: 0,
    price: 0,
    model: null,
    type: null,
    duration: 0,
    condition: '',
    description: '',
    picture: null as File | null,
    album_id: null,
});


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
        window.location.href='/';
    }
}

const getAlbums = async () => {
  try {
    const response = await axiosInstance.get('/');
    albums.value = response.data;
    console.log(response.data);
  } catch (error) {
    console.error(error);
  }
}

const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];
  console.log('Selected file:', file);
  if (file) {
    item.value.picture = file;
  }
};

const sellItem = async (payload: SellItemForm) => {
  try {
    const formData = new FormData();
    Object.entries(payload).forEach(([key, value]) => {
      if (value !== null && value !== undefined) {
        formData.append(key, value as any);
      }
    });
    if (item.value.picture) {
      formData.append('picture', item.value.picture);
    }
    const response = await axiosInstance.post('/sellitem', formData, {
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
getAlbums();
loadFromShoppingList();
</script>

<template>

<body v-if="loading !== true">
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
            <RouterLink to="/userprofile" v-if="isLoggedIn">{{user?.username}}</RouterLink>
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

        <h1>Pārdot preci</h1>
        <form id="add_album_with_tracks" @submit.prevent="sellItem(item)" method="POST" enctype="multipart/form-data">
                <div id="album_wrapper">
                    <div id="input_side">
                        <label>Nosaukums</label>
                        <input class="album_input" v-model="item.title" name="title" type="text">
                        <label>Kategorija</label>
                        <select class="album_input" name="category" v-model="item.category">
                          <option value="" disabled>Izvēlieties kategoriju</option>
                          <option v-for="category in categories" :key="category" :value="category">{{ category }}</option>
                        </select>
                        <label v-show="item.category === 'Album'">Albuma nosaukums</label>
                        <select class="album_input" name="album_id" v-model="item.album_id" v-show="item.category === 'Album'">
                          <option value="" disabled>Izvēlieties albumu</option>
                          <option v-for="album in albums" :key="album.id" :value="album.id">{{ album.title }}</option>
                        </select>
                        <div v-show="item.category === 'Instrument'">
                        <label>Modelis</label>
                        <input class="album_input" v-model="item.model" name="model" type="text">

                        <label>Tips</label>
                        <input class="album_input" v-model="item.type" name="type" type="text">
                        </div>


                        <label v-show="item.category !== 'Service'">Daudzumus</label>
                        <input class="album_input" v-show="item.category !== 'Service'" v-model.number="item.quantity" name="quantity" type="number" >
                        <label>Cena</label>
                        <input class="album_input" v-model.number="item.price" name="price" type="number" step="0.01">
                        <label v-show="item.category !== 'Service'">Stāvoklis</label>
                        <select v-show="item.category !== 'Service'" class="album_input" name="condition" v-model="item.condition">
                          <option value="" disabled>Izvēlieties stāvokli</option>
                          <option v-for="condition in conditions" :key="condition" :value="condition">{{ condition }}</option>
                        </select>

                        <label v-show="item.category === 'Service'">Ilgums</label>
                        <input class="album_input" v-show="item.category === 'Service'" v-model.number="item.duration" name="price" type="number" step="0.01">

                        <label>Apraksts</label>
                        <input class="album_input" v-model="item.description" name="description" type="text">
                    </div>
                        
                    <div id="album_cover_side">
                        <label>Preces attēls</label>
                        <input name="item_picture" type="file" accept="image/*" @change="handleFileChange">
                    </div>
                </div>
                <input id="submit_btn" type="submit" value="Pārdot preci">
            </form>
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
  width: fit-content;
  padding-top: 65px;
  padding-bottom: 65px;
  margin: 0 auto;
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

main h1 {
  text-align: center;
  margin-bottom: 25px;
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
  width: 386px;
  min-height: 54px;
  background-color: #000000;
  color: #E4E4E4;
  font-size: 18px;
  font-weight: normal;
  line-height: 28px;
  letter-spacing: -0.5px;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  border-radius: 8px;
  border: none;
  margin-top: 50px;
  align-self: center;
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