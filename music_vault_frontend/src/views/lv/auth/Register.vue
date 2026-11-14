<script setup lang="ts">
import axiosInstance from '@/axios';
import { ref } from 'vue';

interface RegisterForm {
    username: string;
    email: string;
    password: string;
    password_confirmation: string;
    country_id: number;
    date_of_birth: Date | null;
    error?: string;
    username_error?: string;
    email_error?: string;
    password_error?: string;
    password_confirmation_error?: string;
}

interface Country {
  id: number;
  country_name: string;
}

const countries = ref<Country[]>([]);

const form = ref<RegisterForm>({
    username: '',
    email: '',
    password: '',
    password_confirmation: '',
    country_id: 0,
    date_of_birth: null,
    error: '',
    username_error: '',
    email_error: '',
    password_error: '',
    password_confirmation_error: '',
});

const getCountries = async () => {
    try {
      const response = await axiosInstance.get('/getcountries');
      countries.value = response.data;
      console.log(countries.value);
    } catch (error) {
      console.error(error);
    }
}

const register = async (payload: RegisterForm) => {
    console.log('Register function called');
    try {
        const response = await axiosInstance.post("/register", payload);
        console.log(response.data);
        if (response.status === 201 || response.status === 200) {
            
            localStorage.setItem('auth_token', response.data.api_token);
            localStorage.setItem('csrf_token', response.data.csrf_token);
            localStorage.setItem('user', JSON.stringify(response.data.user));
            
            
            axiosInstance.defaults.headers.common['Authorization'] = `Bearer ${response.data.api_token}`;
            axiosInstance.defaults.headers.common['X-CSRF-TOKEN'] = response.data.csrf_token;
            
            window.location.href = "/";
        }
    } catch (error) {
        console.error(error);
        form.value.error = 'Reģistrācija neizdevās. Lūdzu, mēģiniet vēlreiz.';
    }
};

function isValidUsername(username: string): boolean {
    const MAX_USERNAME_LENGTH = 20;
    const MIN_USERNAME_LENGTH = 3;
    
    const isInvalidInput =
        !username || username.length === 0 || username.length < MIN_USERNAME_LENGTH || username.length > MAX_USERNAME_LENGTH;

    const usernameRegex = /^[a-zA-Z0-9_]{3,20}$/;

    if (usernameRegex.test(username) && !isInvalidInput) {
        form.value.username_error = '';
        return true;
    } else {
        form.value.username_error = 'Lietotājvārdam jābūt no 3 līdz 20 rakstzīmēm.';
        return false;
    }
}

function isValidEmail(email: string): boolean {
    const MAX_EMAIL_LENGTH = 50;
    const isInvalidInput =
        !email || email.length === 0 || email.length > MAX_EMAIL_LENGTH;

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (emailRegex.test(email) && !isInvalidInput) {
        form.value.email_error = '';
        return true;
    } else {
        form.value.email_error = 'Lūdzu, ievadiet derīgu e-pasta adresi.';
        return false;
    }

}

function isValidPasswordAndConfirmation(password: string, password_confirmation: string): boolean {
    const MAX_PASSWORD_LENGTH = 50;
    const MIN_PASSWORD_LENGTH = 8;
    
    const isInvalidInput =
        !password || password.length === 0 || password.length < MIN_PASSWORD_LENGTH || password.length > MAX_PASSWORD_LENGTH;

    const passwordRegex = /^(?=.*[a-zA-Z])(?=.*\d).{8,50}$/;

    if (passwordRegex.test(password) && !isInvalidInput) {
        form.value.password_error = '';
        if (password_confirmation != password) {
          form.value.password_confirmation_error = 'Paroles nesakrīt.';
          return false;
        } else {
          form.value.password_confirmation_error = '';
          return true;
        }
    } else {
        form.value.password_error = 'Parolei jāsatur vismaz 8 rakstzīmes.';
        return false;
    }
}

function validateForm(form: RegisterForm){
  let validusername = isValidUsername(form.username);
  let validEmail = isValidEmail(form.email);
  let validPasswordAndConfirmation = isValidPasswordAndConfirmation(form.password, form.password_confirmation);

  if (validusername && validEmail && validPasswordAndConfirmation) {
    register(form);
  }
}

getCountries();
</script>

<template>
  <body>
    <nav>
      <div id="logo">
        <img src="@/images/nav_images/vinyl_icon.svg">
        <p><RouterLink to="/">MusicVault</RouterLink></p>
      </div>
      <RouterLink to="/login">Pieslēgties</RouterLink>
    </nav>

    <main>
      <h1>Reģistrācija</h1>
      <form id="sign_up_form" @submit.prevent="register(form)" method="post">
        <p v-if="form.error" style="color: red;">{{ form.error }}</p>

        <div class="form_parts">
          <label>Lietotājvārds</label>
          <input name="username" type="text" v-model="form.username">
          <p v-if="form.username_error" style="color: red;">{{ form.username_error }}</p>
        </div>

        <div class="form_parts">
          <label>E-pasts</label>
          <input name="email" type="email" v-model="form.email">
          <p v-if="form.email_error" style="color: red;">{{ form.email_error }}</p>
        </div>

        <div class="form_parts">
          <label>Parole</label>
          <input name="password" type="password" v-model="form.password">
          <p v-if="form.password_error" style="color: red;">{{ form.password_error }}</p>
        </div>

        <div class="form_parts">
          <label>Paroles apstiprinājums</label>
          <input name="password_confirmation" type="password" v-model="form.password_confirmation">
          <p v-if="form.password_confirmation_error" style="color: red;">{{ form.password_confirmation_error }}</p>
        </div>

        <div class="form_parts">
          <label>Valsts</label>
          <select class="album_input" name="country" v-model="form.country_id">
            <option value="" disabled>Izvēlieties valsti</option>
            <option v-for="country in countries" :key="country.id" :value="country.id">{{ country.country_name }}</option>
          </select>
        </div>

        <div class="form_parts">
          <label>Dzimšanas datums</label>
          <input name="date_of_birth" type="date" v-model="form.date_of_birth">
        </div>

        <div class="form_parts">
          <input id="submit_btn" type="submit" value="Reģistrēties">
        </div>

      </form>
    </main>
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
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  align-items: center;
  margin-left: 200px;
  margin-right: 200px;
  padding: 0;
  font-size: 19.53px;
  line-height: 28px;
  letter-spacing: -0.5px;
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

li {
  list-style: none;
}

ul {
  padding: 0;
}

main {
  width: fit-content;
  padding-top: 65px;
  padding-bottom: 65px;
  margin: 0 auto;
}

main h1 {
  text-align: center;
  font-weight: normal;
  line-height: 28px;
  letter-spacing: -0.5px;
}

#sign_up_form {
  display: flex;
  flex-direction: column;
  width: fit-content;
  margin: 0 auto;
}

#sign_up_form input {
  width: 380px;
  height: 50px;
  border-style: solid;
  border-color: #000000;
  border-radius: 8px;
  border-width: 1px;
  padding: 1px 2px;
}

#sign_up_form select {
  width: 385.6px;
  height: 53.6px;
  border-style: solid;
  border-color: #000000;
  border-radius: 8px;
  border-width: 1px;
  padding: 1px 2px;
}

#sign_up_form label {
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
}

.form_parts {
  display: flex;
  flex-direction: column;
  width: fit-content;
}

form {
  display: flex;
  flex-direction: column;
}



@media (max-width:480px) {

nav {
  display: flex;
  flex-direction: row;
  margin-left: 4.167vw;
  margin-right: 4.167vw;
  justify-content: space-between;
  align-items: center;
  padding: 0;
  font-size: 19.53px;
  line-height: 28px;
  letter-spacing: -0.5px;
  color: #0A0A0A;
}

main {
  width: 83.333vw;
  padding-top: 13.542vw;
  padding-bottom: 13.542vw;
  margin: 0 auto;
}

#sign_up_form input {
  width: 82.333vw;
  height: 10.417vw;
  border-style: solid;
  border-color: #000000;
  border-radius: 8px;
  border-width: 1px;
  padding: 1px 2px;
}

#sign_up_form select {
  width: 84vw;
  height: 11.5vw;
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

}
</style>