<script setup lang="ts">
import axiosInstance from '@/axios';
import { ref } from 'vue';

interface RegisterForm {
    name: string;
    email: string;
    password: string;
    password_confirmation: string;
    country_id: number;
    date_of_birth: Date | null;
    error?: string;
}

const countries = ref([] as any[]);
const form = ref<RegisterForm>({
    name: '',
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
    } catch (error) {
      console.error(error);
    }
}

const register = async (payload: RegisterForm) => {
    try {
        const response = await axiosInstance.post("/register", payload);
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
        form.value.error = 'Reģistrācija neizdevās. Mēģiniet vēlreiz.';
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
      <RouterLink to="/login">Pieteikties</RouterLink>
    </nav>

    <main>
      <h1>Reģistrācija</h1>
      <form id="sign_up_form" @submit.prevent="register(form)" method="post">
        <p v-if="form.error" style="color: red;">{{ form.error }}</p>

        <div class="form_parts">
          <label>Lietotājvārds</label>
          <input name="name" type="text" v-model="form.name">
        </div>

        <div class="form_parts">
          <label>E-pasts</label>
          <input name="email" type="email" v-model="form.email">
        </div>

        <div class="form_parts">
          <label>Parole</label>
          <input name="password" type="password" v-model="form.password">
        </div>

        <div class="form_parts">
          <label>Paroles apstiprinājums</label>
          <input name="password_confirmation" type="password" v-model="form.password_confirmation">
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
