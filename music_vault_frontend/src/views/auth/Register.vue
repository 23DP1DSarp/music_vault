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
        form.value.error = 'Registration failed. Please try again.';
    }
};

getCountries();
</script>

<template>
<body>
    <nav>
        <div id="logo">
            <img src="@/images/nav_images/vinyl_icon.svg">
            <p><RouterLink to="/">MusicVault</RouterLink></p>
        </div>
        <RouterLink to="/login">Log In</RouterLink>
       
    </nav>

    <main>
            <h1>Sign Up</h1>
            <form id="sign_up_form" @submit.prevent="register(form)"  method="post">
                <p v-if="form.error" style="color: red;">{{ form.error }}</p>

                <div class="form_parts">
                    <label>Username</label>
                    <input name="username" type="text" v-model="form.username">
                </div>

                <div class="form_parts">
                    <label>Email</label>
                    <input name="email" type="email" v-model="form.email">
                </div>

                <div class="form_parts">
                    <label>Password</label>
                    <input name="password" type="password" v-model="form.password">
                </div>

                <div class="form_parts">
                    <label>Password Confirmation</label>
                    <input name="password_confirmation" type="password" v-model="form.password_confirmation">
                </div>

                <div class="form_parts">
                    <label>Country</label>
                    <select class="album_input" name="country" v-model="form.country_id">
                      <option value="" disabled>Select country</option>
                      <option v-for="country in countries" :key="country.id" :value="country.id">{{ country.country_name }}</option>
                    </select>
                </div>

                <div class="form_parts">
                    <label>Date of Birth</label>
                    <input name="date_of_birth" type="date" v-model="form.date_of_birth">
                </div>

                <div class="form_parts">
                    <input id="submit_btn" type="submit" value="Sign Up">
                </div>

            </form>
    </main>
    

    
    <footer>
        <div id="footer_wrapper">
        <div id="footer_top">
            <div id="footer_info">
                <div id="footer_logo">
                    <img src="@/images/footer_images/vinyl_icon.svg">
                    <p>MusicVault</p>
                </div>
                <p id="footer_info_text">
                    Your premier destination for music records. Discover,
                    collect, and enjoy music the way it was meant to be
                    heard.
                </p>

                <div id="icons">
                    <img class="icon" src="@/images/footer_images/facebook_icon.svg">
                    <img class="icon" src="@/images/footer_images/instagram_icon.svg">
                    <img class="icon" src="@/images/footer_images/twitter_icon.svg">
                    <img class="icon" src="@/images/footer_images/youtube_icon.svg">
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
  align-items: center;
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

footer {
  border-top: solid #ECECF0 1px;
}

footer h6 {
  font-size: 16px;
  margin-top: 15px;
  line-height: 24px;
}

#footer_wrapper {
  width: 1550px;
  margin: 0 auto;
  padding-left: 15px;
  padding-right: 15px;
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
}

#footer_top ul {
  width: 366px;
  font-size: 14px;
  line-height: 20px;
  letter-spacing: 0px;
  color: #717182;
  display: flex;
  flex-direction: column;
  gap: 14px;
}

#subscribe_form p {
  color: #717182;
  line-height: 20px;
  letter-spacing: 0px;
}

#email_input {
  background-color: #F3F3F5;
  border-style: none;
  width: 366px;
  height: 36px;
  color: #717182;
  padding: 0px 0px 0px 10px;
  border-radius: 8px;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  font-size: 14px;
}

#subscribe_form_submit {
  margin-top: 5px;
  width: 376px;
  height: 32px;
  border-style: none;
  border-radius: 8px;
  background-color: #030213;
  color: #E4E4E4;
  font-size: 14px;
  line-height: 20px;
  letter-spacing: 0px;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
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
}
</style>