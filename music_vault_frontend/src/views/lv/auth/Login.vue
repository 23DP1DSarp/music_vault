<script setup lang="ts">
import { ref } from 'vue'
import axiosInstance from '@/axios'

interface LoginForm {
    email: string;
    password: string;
    error?: string;
    email_error?: string;
    password_error?: string;
}

const form = ref<LoginForm>({
    email: '',
    password: '',
    error: '',
    email_error: '',
    password_error: '',
});


const login = async (payload: LoginForm) => {
    console.log('Login function called');
    try { 
        
        const response = await axiosInstance.post("/login", payload);
        console.log(response.data);
        if (response.status === 200) {
            
            localStorage.setItem('auth_token', response.data.api_token);
            localStorage.setItem('csrf_token', response.data.csrf_token);
            localStorage.setItem('user', JSON.stringify(response.data.user));
            
            
            axiosInstance.defaults.headers.common['Authorization'] = `Bearer ${response.data.api_token}`;
            axiosInstance.defaults.headers.common['X-CSRF-TOKEN'] = response.data.csrf_token;
            
            window.location.href = "/";
        }
    } catch (error) {
        console.error(error);
        form.value.error = 'Pieslēgšanās neizdevās. Lūdzu, pārbaudiet ievadītos datus un mēģiniet vēlreiz.';
    }
};

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

function isValidPassword(password: string): boolean {
    const MAX_PASSWORD_LENGTH = 50;
    const MIN_PASSWORD_LENGTH = 8;
    
    const isInvalidInput =
        !password || password.length === 0;

    if (!isInvalidInput) {
        form.value.password_error = '';
        return true;
    } else {
        form.value.password_error = 'Lūdzu, ievadiet paroli.';
        return false;
    }
}

function validateForm(form: LoginForm){
  let validEmail = isValidEmail(form.email);
  let validPassword = isValidPassword(form.password);

  if (validEmail && validPassword) {
    login(form);
  }
}

</script>

<template>
<body>
    <nav>
        <div id="logo">
            <img src="@/images/nav_images/vinyl_icon.svg">
            <p><RouterLink to="/">MusicVault</RouterLink></p>
        </div>
         <div id="rightbuttons">
        <RouterLink to="/register">Reģistrēties</RouterLink>
        </div>
    </nav>

   <!-- <img id="shoppingcart" src="../../images/nav_images/shopping_cart_icon.svg" @click="hamburgerMenu()">

    <div id="hamburger_menu">
          <div id="close_btn" @click="hamburgerMenu()">
            <img src="../../images/shopping_cart images/close-x-svgrepo-com.svg">
          </div>
          
          <RouterLink to="/register">Reģistrēties</RouterLink>
          
          
    </div>-->


    <main>
            <h1>Pieslēgties</h1>
            <form id="sign_up_form" @submit.prevent="login(form)">

                <p v-if="form.error" style="color: red;">{{ form.error }}</p>

                <div class="form_parts">
                    <label>E-pasts</label>
                    <input email="email" type="text" v-model="form.email">
                    <p v-if="form.email_error" style="color: red;">{{ form.email_error }}</p>
                </div>

                <div class="form_parts">
                    <label>Parole</label>
                    <input name="password" type="password" v-model="form.password">
                    <p v-if="form.password_error" style="color: red;">{{ form.password_error }}</p>
                </div>

                <div class="form_parts">
                    <input id="submit_btn" type="submit" value="Pieslēgties">
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
  width: 400px;
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

#submit_btn {
  min-width: 84vw;
  min-height: 11.25vw;
  margin-top: 10.417vw;
  background-color: #000000;
  color: #E4E4E4;
  font-size: 18px;
  font-weight: normal;
  line-height: 28px;
  letter-spacing: -0.5px;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
  gap: 20px;
}

}

</style>
