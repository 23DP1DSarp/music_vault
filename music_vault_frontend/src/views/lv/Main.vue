<script setup lang="ts">
import axiosInstance from '@/axios';
import Navbar from '@/components/lv/NavbarLv.vue';
import ShoppingMenu from '@/components/lv/ShoppingMenuLv.vue';
import Footer from '@/components/lv/FooterLv.vue';
import {ref} from 'vue';
import { useRouter } from 'vue-router';

const loading = ref(true);

const router = useRouter();

const user = ref({
    username: '',
    email: '',
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

const albums = ref<Album[]>([]);

const getAlbums = async () => {
  try {
    const response = await axiosInstance.get('/');
    albums.value = response.data;
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
        //console.log(response.data);
    } catch (error) {
        console.error(error);
        isLoggedIn.value = false;
    } finally {
        loading.value = false;
    }
};

const getImageUrl = (path: string): string => {
  if (path.startsWith('http')) {
    return path;
  }

  return `${'http://music-vault-main-sjukhk.laravel.cloud'}/storage/${path}`;
};

getUser();
getAlbums();
</script>

<template>
<html lang="lv">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>MusicVault</title>
</head>

<body>
    <Navbar />

    <main>
        <ShoppingMenu />

        <div id="hero_section">

            <div id="left_side">
                <h1>Atklājiet savu nākamo mīļāko ierakstu</h1>
                <p id="subtext">No retiem nospiedumiem līdz jaunākajiem izdevumiem. Atlasīti vinila ieraksti katram mūzikas cienītājam.</p>

                <div id="hero_buttons">
                    <RouterLink to="/catalog" id="shop_button">Jaunumi</RouterLink>
                    <RouterLink to="/albumoffers" id="browse_button">Apskatīt piedavājumus</RouterLink>
                </div>
            </div>

            <div id="right_side">
                <img src="../../images/main_page_images/Vinyl_records_collection.png">
            </div>
            
            <div id="order_items">
                
            </div>

        </div>

    </main>

    <Footer />
</body>
</html>
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
  flex-direction: column;
}

input::-webkit-outer-spin-button,
input::-webkit-inner-spin-button {
  -webkit-appearance: none;
  margin: 0;
}

#hero_section {
  height: 485px;
  display: flex;
  flex-direction: row;
  margin-top: 150px;
  margin-bottom: 150px;
}

#left_side {
  width: 50%;
  height: 360px;
  display: flex;
  flex-direction: column;
}

#left_side h1 {
  width: 500px;
  font-size: 60px;
  font-weight: normal;
  line-height: 60px;
  letter-spacing: -1.5px;
  margin-bottom: 5px;
}

#subtext {
  width: 500px;
  font-size: 20px;
  line-height: 28px;
  letter-spacing: 0px;
  margin-bottom: 0;
  color: #717182;
}

#hero_buttons {
  display: flex;
  flex-direction: row;
  gap: 15px;
  margin-top: 40px;
  margin-bottom: 0;
}

#shop_button {
  width: 230px;
  height: 40px;
  background-color: #030213;
  color: #FFFFFF;
  border-style: none;
  border-radius: 8px;
  text-align: center;
  
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  font-size:18px;
  line-height: 40px;
  letter-spacing: 0px;
  cursor: pointer;
}

#browse_button {
  width: 230px;
  height: 40px;
  background-color: #FFFFFF;
  color: #0A0A0A;
  border: solid rgba(0, 0, 0, .1) 1px;
  border-style: solid;
  border-radius: 8px;
  text-align: center;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  font-size:18px;
  line-height: 40px;
  letter-spacing: 0px;
  cursor: pointer;
}

#stats {
  margin-top: 50px;
  display: flex;
  flex-direction: row;
  gap: 32px;
}

.stat {
  display: flex;
  gap: 10px;
  flex-direction: column;
  text-align: center;
}

.stat h2 {
  font-size: 24px;
  line-height: 32px;
  letter-spacing: 0px;
  margin: 0;
}

.stat p {
  margin: 0;
  font-size:14px;
  line-height: 20px;
  letter-spacing: 0px;
  color: #717182;
}

#right_side {
  width: 728px;
  height: 486px;
}

#record_browse {
  margin-top: 20px;
}

#record_browse h4 {
  font-size:30px;
  line-height: 36px;
  letter-spacing: 0px;
  margin: 0;
}

#results_count {
  font-size:16px;
  line-height: 24px;
  letter-spacing: 0px;
  color: #717182;
  margin-top: 15px;
}

#filters {
  margin-left: 15px;
  margin-bottom: 50px;
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 10px;
}

#filters form {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 10px;
}

#filters select {
  width: 128px;
  height: 36px;
  background-color: #F3F3F5;
  border-style: none;
  border-radius: 8px;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  font-size: 14px;
  line-height: 20px;
  letter-spacing: 0px;
}

#album_cards {
  display: grid;
  grid-template-columns: repeat(5, 1fr);
  gap: 25px;
  margin-bottom: 50px;
}

#album_data {
  display: flex;
  flex-direction: column;
  gap: 24px;
  width: 100%;
  max-width: 358px;
  height: 434px;
  border: solid #E4E4E4 1px;
  border-radius: 14px;
}

#album_data img {
  width: 100%;
  height: 256px;
  border-radius: 14px;
}

#album_data h3 {
  font-size: 20px;
  line-height: 24px;
  margin: 0;
  margin-left: 15px;
}

#album_data p {
  margin: 0;
  margin-left: 15px;
  font-size: 16px;
  line-height: 20px;
  color: #717182;
}

#genre_and_year {
  display: flex;
  flex-direction: row;
}

.form_button {
  height: 32px;
  background-color: #FFFFFF;
  border: solid rgba(0, 0, 0, .1) 1px;
  border-radius: 8px;
  padding: 15px;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  font-size:14px;
  line-height: 20px;
  letter-spacing: 0px;
  display: flex;
  flex-direction: row;
  align-items: center;
  cursor: pointer;
}

path {
  stroke: #ffffff;
  fill: black;
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
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  align-self: center;
}


#hero_section {
  width: 80vw;
  height: 485px;
  display: flex;
  flex-direction: column;
  margin-top: 25px;
  justify-content: center;
  align-items: center;
}

#left_side {
  width: 100%;
  height: 360px;
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

#left_side h1 {
  width: 100%;
  font-size: 48px;
  font-weight: normal;
  line-height: 60px;
  letter-spacing: -1.5px;
  margin-bottom: 5px;
  text-align: center;
}

#subtext {
  width: 100%;
  font-size: 20px;
  line-height: 28px;
  letter-spacing: 0px;
  margin-bottom: 0;
  text-align: center;
  color: #717182;
}

#hero_buttons {
  width: 100%;
  display: flex;
  flex-direction: column;
  gap: 15px;
  margin-top: 40px;
  margin-bottom: 0;
}

#shop_button {
  width: 100%;
  height: 40px;
  background-color: #030213;
  color: #FFFFFF;
  border-style: none;
  border-radius: 8px;
  text-align: center;
  
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  font-size:18px;
  line-height: 40px;
  letter-spacing: 0px;
  cursor: pointer;
}

#browse_button {
  width: 100%;
  height: 40px;
  background-color: #FFFFFF;
  color: #0A0A0A;
  border: solid rgba(0, 0, 0, .1) 1px;
  border-style: solid;
  border-radius: 8px;
  text-align: center;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  font-size:18px;
  line-height: 40px;
  letter-spacing: 0px;
  cursor: pointer;
}

#right_side {
  width: 0;
  height: 0;
  display: none;
}

#right_side img {
  width: 0;
  height: 0;
  display: none;
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
