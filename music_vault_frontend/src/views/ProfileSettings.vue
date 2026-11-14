<script setup lang="ts">
import axiosInstance from '@/axios';
import Navbar from '@/components/en/Navbar.vue';
import ShoppingMenu from '@/components/en/ShoppingMenu.vue';
import Footer from '@/components/en/Footer.vue';
import {ref} from 'vue';
import { useRouter } from 'vue-router';

const loading = ref(true);

const router = useRouter();

const user = ref({
    username: '',
    email: '',
    country: '',
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
  username_error?: string;
  email_error?: string;
}

interface ResetPassword {
  current_password: string;
  new_password: string;
  confirm_new_password: string;
  password_error?: string;
  password_confirmation_error?: string;
}

const accountInfo = ref<AccountInfo>({
  username: '',
  email: '',
  country: '',
  username_error: '',
  email_error: '',
});

const resetPassword = ref<ResetPassword>({
  current_password: '',
  new_password: '',
  confirm_new_password: '',
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

const getUser = async () => {
    try {
        const response = await axiosInstance.get('/user');
        user.value = response.data;
        isLoggedIn.value = true;
        console.log(response.data);
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
    console.log(response.data);
    alert('Account information updated successfully!');
  } catch (error) {
    console.error(error);
  }
  
};

const changePassword = async (resetPassword: ResetPassword) => {
  try {
    const response = await axiosInstance.put('/reset-password', {
      current_password: resetPassword.current_password,
      password: resetPassword.confirm_new_password,
      password_confirmation: resetPassword.confirm_new_password,
    });
    console.log(response.data);
    alert('Password changed successfully!');
  } catch (error) {
    console.error(error);
  }
};

const deleteAccount = async () => {
  try {
    const response = await axiosInstance.delete('/delete-account');
    console.log(response.data);
    alert('Account deleted successfully!');
    window.location.href='/';
  } catch (error) {
    console.error(error);
  }
};

function validateAccountInfoForm(accountInfo: AccountInfo){
  let validusername = isValidUsername(accountInfo.username);
  let validEmail = isValidEmail(accountInfo.email);

  if (validusername && validEmail) {
    changeAccountInfo(accountInfo);
  }
}

function validateResetPasswordForm(resetPassword: ResetPassword){
  let validPasswordAndConfirmation = isValidPasswordAndConfirmation(resetPassword.new_password, resetPassword.confirm_new_password);
  
  if (validPasswordAndConfirmation) {
    changePassword(resetPassword);
  }
}

function isValidPasswordAndConfirmation(password: string, password_confirmation: string): boolean {
    const MAX_PASSWORD_LENGTH = 50;
    const MIN_PASSWORD_LENGTH = 8;
    
    const isInvalidInput =
        !password || password.length === 0 || password.length < MIN_PASSWORD_LENGTH || password.length > MAX_PASSWORD_LENGTH;

    const passwordRegex = /^(?=.*[a-zA-Z])(?=.*\d).{8,50}$/;

    if (passwordRegex.test(password) && !isInvalidInput) {
        resetPassword.value.password_error = '';
        if (password_confirmation != password) {
          resetPassword.value.password_confirmation_error = 'Passwords are not the same.';
          return false;
        } else {
          resetPassword.value.password_confirmation_error = '';
          return true;
        }
    } else {
        resetPassword.value.password_error = 'Password must be at least 8 characters.';
        return false;
    }
}

function isValidUsername(username: string): boolean {
    const MAX_USERNAME_LENGTH = 20;
    const MIN_USERNAME_LENGTH = 3;
    
    const isInvalidInput =
        !username || username.length === 0 || username.length < MIN_USERNAME_LENGTH || username.length > MAX_USERNAME_LENGTH;

    const usernameRegex = /^[a-zA-Z0-9_]{3,20}$/;
    console.log(username)
    console.log(usernameRegex.test(username))
    if (usernameRegex.test(username) && !isInvalidInput) {
        accountInfo.value.username_error = '';
        return true;
    } else {
        accountInfo.value.username_error = 'Username must be from 3 to 20 characters';
        return false;
    }
}

function isValidEmail(email: string): boolean {
    const MAX_EMAIL_LENGTH = 50;
    const isInvalidInput =
        !email || email.length === 0 || email.length > MAX_EMAIL_LENGTH;

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if (emailRegex.test(email) && !isInvalidInput) {
        accountInfo.value.email_error = '';
        return true;
    } else {
        accountInfo.value.email_error = 'Please enter your valid email.'
        return false;
    }

}

getUser();
getCountries();
</script>

<template>
<body >
    <Navbar />
    
    <main>

        <ShoppingMenu />

        <h1>Profile Settings</h1>
        <div id="account_info">
            <h2>Account Information</h2>
            <form @submit.prevent="validateAccountInfoForm(accountInfo)">
                <div class="form_parts">
                    <label>Username</label>
                    <input type="text" v-model="accountInfo.username">
                    <p v-if="accountInfo.username_error" style="color: red;">{{ accountInfo.username_error }}</p>
                </div>

                <div class="form_parts">
                    <label>Email</label>
                    <input type="text" v-model="accountInfo.email">
                    <p v-if="accountInfo.email_error" style="color: red;">{{ accountInfo.email_error }}</p>
                </div>

                <!---<div class="form_parts">
                    <label>Country</label>
                    <select name="country" v-model="accountInfo.country">
                      <option value="">Select your country</option>
                      <option v-for="country in countries" :key="country.id" :value="country.id">{{ country.country_name }}</option>
                    </select>
                </div>-->

                <div class="form_parts">
                    <input id="submit_btn" type="submit" value="Submit Form">
                </div>
            </form>
            </div>

            <div id="change_password">
            <h2>Change Password</h2>
            <form @submit.prevent="validateResetPasswordForm(resetPassword)">
                <div class="form_parts">
                    <label>Current Password</label>
                    <input type="password" v-model="resetPassword.current_password">
                </div>

                <div class="form_parts">
                    <label>New Password</label>
                    <input type="password" v-model="resetPassword.new_password">
                    <p v-if="resetPassword.password_error" style="color: red;">{{ resetPassword.password_error }}</p>
                </div>

                <div class="form_parts">
                    <label>Confirm New Password</label>
                    <input type="password" v-model="resetPassword.confirm_new_password">
                    <p v-if="resetPassword.password_confirmation_error" style="color: red;">{{ resetPassword.password_confirmation_error }}</p>
                </div>

                <div class="form_parts">
                    <input id="submit_btn" type="submit" value="Submit Form">
                </div>
            </form>
            </div>

            <div id="change_password">
            <h2>Danger Zone</h2>
            <p>Deleting your account is irreversible. Proceed with caution.</p>
            <form @submit.prevent="deleteAccount">
                <div class="form_parts">
                    <input id="delete_btn" type="submit" value="Delete Account">
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

</style>