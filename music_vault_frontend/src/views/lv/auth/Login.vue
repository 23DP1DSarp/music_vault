<script setup lang="ts">
import { ref } from 'vue'
import axiosInstance from '@/axios'

const form = ref({ email: '', password: '', error: '' })

const login = async (payload: any) => {
  try {
    const response = await axiosInstance.post('/login', payload)
    if (response.data && response.data.api_token) {
      localStorage.setItem('auth_token', response.data.api_token)
      localStorage.setItem('user', JSON.stringify(response.data.user))
      window.location.href = '/'
    }
  } catch (err) {
    form.value.error = 'Pieteikšanās neizdevās.'
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
      <RouterLink to="/register">Reģistrēties</RouterLink>
    </nav>

    <main>
      <h1>Pieteikties</h1>
      <form @submit.prevent="login(form)">
        <p v-if="form.error" style="color: red;">{{ form.error }}</p>
        <div class="form_parts">
          <label>E-pasts</label>
          <input type="email" v-model="form.email" />
        </div>
        <div class="form_parts">
          <label>Parole</label>
          <input type="password" v-model="form.password" />
        </div>
        <div class="form_parts">
          <input id="submit_btn" type="submit" value="Pieteikties" />
        </div>
      </form>
    </main>
  </body>
</template>
