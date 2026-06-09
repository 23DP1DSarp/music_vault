<script setup lang="ts">
import axiosInstance from '@/axios';
import {ref} from 'vue';
import { useRouter } from 'vue-router';

const loading = ref(true);

const router = useRouter();

const user = ref({
    username: '',
    email: '',
    country: '',
    user_role_id: 0,
});

const isLoggedIn = ref(false);
const shoppingList = ref<Item[]>([]);

interface Country {
  id: number;
  country_name: string;
}

const countries = ref<Country[]>([]);

interface AccountInfo {
  username: string;
  email: string;
  country: string;
}

interface ResetPassword {
  current_password: string;
  new_password: string;
  confirm_new_password: string;
}

interface Item {
  id: string;
  title: string;
  quantity: number;
  price: number;
}

const accountInfo = ref<AccountInfo>({
  username: '',
  email: '',
  country: '',
});

const resetPassword = ref<ResetPassword>({
  current_password: '',
  new_password: '',
  confirm_new_password: '',
});

const getCountries = async () => {
    try {
      const response = await axiosInstance.get('/getcountries');
      countries.value = response.data;
      //console.log(countries.value);
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
        accountInfo.value.username = response.data.username;
        accountInfo.value.email = response.data.email;
        accountInfo.value.country = response.data.country;
    } catch (error) {
        console.error(error);
        isLoggedIn.value = false;
    } finally {
        loading.value = false;
    }
};

const logout = async () => {
    try {
        const response = await axiosInstance.post('/logout');
        //console.log(response.data);
    } catch (error) {
        console.error(error);
    } finally {
        window.location.href='/';
    }
}

const changeAccountInfo = async (accountInfo: AccountInfo) => {
  try {
    const response = await axiosInstance.put('/change-user-info', {
      username: accountInfo.username,
      email: accountInfo.email,
    });
    //console.log(response.data);
    alert('Account information updated successfully!');
  } catch (error) {
    console.error(error);
  }
  
};

const changePassword = async (resetPassword: ResetPassword) => {
  if (resetPassword.new_password !== resetPassword.confirm_new_password) {
    alert('New password and confirm new password do not match!');
    return;
  }

  try {
    const response = await axiosInstance.put('/reset-password', {
      current_password: resetPassword.current_password,
      password: resetPassword.confirm_new_password,
      password_confirmation: resetPassword.confirm_new_password,
    });
    //console.log(response.data);
    alert('Password changed successfully!');
  } catch (error) {
    console.error(error);
  }
};

const deleteAccount = async () => {
  try {
    const response = await axiosInstance.delete('/delete-account');
    //console.log(response.data);
    alert('Account deleted successfully!');
    window.location.href='/';
  } catch (error) {
    console.error(error);
  }
};


const shoppingMenu = async () => {

  let shoppingSlider = document.getElementById('shopping_menu') as HTMLFormElement;

  if (window.innerWidth <= 480) {
    if (shoppingSlider.style.visibility === "hidden" || shoppingSlider.style.visibility === '') {
      shoppingSlider?.style.setProperty('width','70%');
      shoppingSlider?.style.setProperty('visibility','visible');
    } else {
    shoppingSlider?.style.setProperty('width','0%');
    shoppingSlider?.style.setProperty('visibility','hidden');
    }
  }
  else {
    if (shoppingSlider.style.visibility === "hidden" || shoppingSlider.style.visibility === '') {
      shoppingSlider?.style.setProperty('width','25%');
      shoppingSlider?.style.setProperty('visibility','visible');
    } else {
      shoppingSlider?.style.setProperty('width','0%');
      shoppingSlider?.style.setProperty('visibility','hidden');
    }
  }
}

const hamburgerMenu = async () => {

  let hamburgerSlider = document.getElementById('hamburger_menu') as HTMLFormElement;

  if (hamburgerSlider.style.visibility === "hidden" || hamburgerSlider.style.visibility === '') {
    hamburgerSlider?.style.setProperty('width','70%');
    hamburgerSlider?.style.setProperty('visibility','visible');
  } else {
    hamburgerSlider?.style.setProperty('width','0%');
    hamburgerSlider?.style.setProperty('visibility','hidden');
  }
  
}

const loadFromShoppingList = async () => {
  const stored = localStorage.getItem('shoppingList');
  if (stored) {
  shoppingList.value = JSON.parse(stored);
  }
}

const updateItemInShoppingList = async (index: number, operator: string) => {
  if (operator === "-" && shoppingList.value[index]?.quantity) {
    shoppingList.value[index].quantity -= 1;
    localStorage.setItem("shoppingList", JSON.stringify(shoppingList.value))
  } else if (operator === "+" && shoppingList.value[index]?.quantity) {
    shoppingList.value[index].quantity += 1;
    localStorage.setItem("shoppingList", JSON.stringify(shoppingList.value))
  }
}

const deleteFromShoppingList = async (index: number) => { 
  shoppingList.value.splice(index);
  localStorage.setItem("shoppingList", JSON.stringify(shoppingList.value));
}

getUser();
getCountries();
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
                <RouterLink to="/catalog">Jaunumi</RouterLink>
                <RouterLink to="/add-album" v-if="isLoggedIn">Pievienot albumu</RouterLink>
                <RouterLink to="/sell-item" v-if="isLoggedIn && user.user_role_id === 2">Pārdot preci</RouterLink>
                <RouterLink to="/sellerform" v-if="isLoggedIn && user.user_role_id !== 2">Kļūt par pārdevēju</RouterLink>
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
            <img id="shoppingcart" src="../../images/nav_images/shopping_cart_icon.svg" @click="shoppingMenu()">
            <RouterLink to="/userprofile" v-if="isLoggedIn">{{user?.username}}</RouterLink>
            <form action="/logout" @submit.prevent="logout" v-if="isLoggedIn">
                <button id="logoutbtn">Iziet</button>
            </form>
            <RouterLink to="/login" v-if="!isLoggedIn">Ieiet</RouterLink>
            <RouterLink to="/register" v-if="!isLoggedIn">Reģistrēties</RouterLink>
        </div>

        <div id="mobile_btns">
          <img id="shoppingcart" src="../../images/nav_images/shopping_cart_icon.svg" @click="shoppingMenu()">
          <img id="hamburger_icon" src="../../images/nav_images/burger-menu-svgrepo-com.svg" @click="hamburgerMenu()">
        </div>

        <div id="hamburger_menu">
              <div id="close_btn" @click="hamburgerMenu()">
                <img src="../../images/shopping_cart images/close-x-svgrepo-com.svg">
              </div>

              <div id="navbuttons_mobile">
                  <ul>
                      <RouterLink to="/catalog">Jaunumi</RouterLink>
                      <RouterLink to="/add-album" v-if="isLoggedIn">Pievienot albumu</RouterLink>
                      <RouterLink to="/sell-item" v-if="isLoggedIn && user.user_role_id === 2">Pārdot preci</RouterLink>
                      <RouterLink to="/sellerform" v-if="isLoggedIn && user.user_role_id !== 2">Kļūt par pārdevēju</RouterLink>
                  </ul>
              </div>

              <div id="rightbuttons_mobile">
                <ul>
                  <RouterLink to="/userprofile" v-if="isLoggedIn">{{user?.username}}</RouterLink>
                  <form action="/logout" @submit.prevent="logout" v-if="isLoggedIn">
                      <button id="logoutbtn">Iziet</button>
                  </form>
                  <RouterLink to="/login" v-if="!isLoggedIn">Ieiet</RouterLink>
                  <RouterLink to="/register" v-if="!isLoggedIn">Reģistrēties</RouterLink>
                </ul>
              </div>
              
              <div id="language_options_mobile">
                <ul>
                  <p>LV</p>
                  <RouterLink to="/en">
                    EN
                  </RouterLink>
                  <RouterLink to="/ru">
                    RU
                  </RouterLink>
                </ul>
              </div>
              
              
              
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
              <b><p id="price">{{ item.price }}€</p></b>
              <p>Daudzums: {{ item.quantity }}</p>
              <div id="item_quantity">
                <button class="quantity_btn" @click="updateItemInShoppingList(index, '-')">-</button>
                <input id="quantity_input" type="number" v-model="item.quantity">
                <button class="quantity_btn" @click="updateItemInShoppingList(index, '+')">+</button>
              </div>
            </div>
            
          </div>
          <a :href="`/checkout`"><button id="checkout_btn">Noformēt pasūtījumu</button></a>
        </div>

        <h1>Profila iestatījumi</h1>
        <div id="account_info">
            <h2>Konta informācija</h2>
            <form @submit.prevent="changeAccountInfo(accountInfo)">
                <div class="form_parts">
                    <label>Lietotājvārds</label>
                    <input type="text" v-model="accountInfo.username">
                </div>

                <div class="form_parts">
                    <label>E-pasts</label>
                    <input type="text" v-model="accountInfo.email">
                </div>

                <!--<div class="form_parts">
                    <label>Valsts</label>
                    <select name="country">
                      <option value="" disabled>Izvēlieties valsti</option>
                      <option></option>
                    </select>
                </div>-->

                <div class="form_parts">
                    <input id="submit_btn" type="submit" value="Iesniegt">
                </div>
            </form>
            </div>

            <div id="change_password">
            <h2>Mainīt paroli</h2>
            <form @submit.prevent="changePassword(resetPassword)">
                <div class="form_parts">
                    <label>Pašreizējā parole</label>
                    <input type="password" v-model="resetPassword.current_password">
                </div>

                <div class="form_parts">
                    <label>Jaunā parole</label>
                    <input type="password" v-model="resetPassword.new_password">
                </div>

                <div class="form_parts">
                    <label>Apstiprināt paroli</label>
                    <input type="password" v-model="resetPassword.confirm_new_password">
                </div>

                <div class="form_parts">
                    <input id="submit_btn" type="submit" value="Iesniegt">
                </div>
            </form>
            </div>

            <div id="delete_account">
            <h2>Bīstamā zona</h2>
            <p>Konta dzēšana ir neatgriezeniska. Rīkojieties uzmanīgi.</p>
            <form @submit.prevent="deleteAccount">
                <div class="form_parts">
                    <input id="delete_btn" type="submit" value="Dzēst kontu">
                </div>
            </form>
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

            <div class="footer_links">
                <h6>Ātrās saites</h6>

                <ul>
                    <li>Jaunumi</li>
                    <li>Priekšpasūtījumi</li>
                    <li>Izpārdošana</li>
                    <li>Reti atradumi</li>
                    <li>Dāvanu kartes</li>
                </ul>
            </div>

            <div class="footer_links">
                
                <h6>Žanri</h6>

                <ul>
                    <li>Roks</li>
                    <li>Džezs</li>
                    <li>Elektronika</li>
                    <li>Hip-Hopss</li>
                    <li>Klasika</li>
                </ul>
            </div>

          <!-- <div id="subscribe_form">
                <h6>Sekojiet jaunumiem</h6>
                <p>Saņemiet paziņojumus par jaunumiem un ekskluzīviem piedāvājumiem.</p>

                <form action="" method="post">
                    <input id="email_input" placeholder="Ievadiet e-pastu" name="subscription-email" type="email" required>
                    <input id="subscribe_form_submit" type="submit" value="Abonēt">
                </form>
            </div>-->
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

#mobile_btns, #hamburger_menu {
  display: none;
}

main {
  width: 300px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 80px;
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

#price_div {
  text-align: end;
}

#price_div p {
  margin-top: 0px;
  width: 100px;
}

#price {
  margin-bottom: 8px;
}

#item_quantity {
  display: flex;
  flex-direction: row;
  align-items: center;
  height: 20px;
}

#item_quantity input {
  width: 53px;
  height: 20px;
}

.form_parts {
  width: 380px;
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

input, select {
  width: 380px;
  height: 50px;
  border-style: solid;
  border-color: #000000;
  border-radius: 8px;
  border-width: 1px;
  padding: 1px 2px;
}

#submit_btn {
  min-width: 386px;
  min-height: 54px;
  margin-top: 30px;
  background-color: #000000;
  color: #E4E4E4;
  font-size: 18px;
  font-weight: normal;
  line-height: 28px;
  letter-spacing: -0.5px;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  border-radius: 8px;
  border: none;
}

#delete_btn {
  min-width: 386px;
  min-height: 54px;
  margin-top: 30px;
  background-color: #E7000B;
  color: #E4E4E4;
  font-size: 18px;
  font-weight: normal;
  line-height: 28px;
  letter-spacing: -0.5px;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  border-radius: 8px;
  border: none;
}

footer {
  border-top: solid #ECECF0 1px;
}

#footer_top {
  width: 80vw;
  margin: 0 auto;
  padding-top: 50px;
  padding-bottom: 20px;
  display: flex;
  flex-direction: row;
}

footer h6 {
  font-size: 16px;
  margin-top: 15px;
  line-height: 24px;
}

#footer_bottom {
  margin: 0 auto;
  padding-left: 150px;
  padding-right: 150px;
  align-items: center;
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  font-size: 14px;
  line-height: 20px;
  letter-spacing: 0px;
  color: #717182;
  border-top: solid #ECECF0 1px;
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


#footer_bottom ul {
  display: flex;
  flex-direction: row;
  gap: 24px;
  list-style: none;
  padding: 0;
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
  gap: 50px;
}

#account_info, #change_password, #delete_account {
  display: flex;
  flex-direction: column;
  justify-content: center;
  align-items: center;
}

.form_parts {
  align-items: center;
  width: auto;
}

.form_parts label {
  align-self: baseline;
}



input {
  width: 82.333vw;
  height: 10.417vw;
  border-style: solid;
  border-color: #000000;
  border-radius: 8px;
  border-width: 1px;
  padding: 1px 2px;
}


#submit_btn {
  min-width: 85vw;
  min-height: 11.5vw;
  margin-top: 10.417vw;
  background-color: #000000;
  color: #E4E4E4;
  font-size: 18px;
  font-weight: normal;
  line-height: 28px;
  letter-spacing: -0.5px;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
}


#delete_account p {
  width: 90vw;
  margin-left: 20px;
}

#delete_btn {
  min-width: 85vw;
  min-height: 11.5vw;
  margin-top: 10.417vw;
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