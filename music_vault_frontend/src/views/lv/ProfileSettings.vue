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
    country: '',
    user_role_id: 0,
});

const isLoggedIn = ref(false);

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

getUser();
getCountries();
</script>

<template>

<body>
    <Navbar />


    <main>
       <ShoppingMenu />

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
  width: 300px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  align-items: center;
  gap: 80px;
  padding-bottom: 50px;
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