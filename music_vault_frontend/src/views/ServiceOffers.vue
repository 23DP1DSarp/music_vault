<script setup lang="ts">
import axiosInstance from '@/axios';
import {ref} from 'vue';
import { useRouter } from 'vue-router';

const loading = ref(true);

const router = useRouter();

const genres: string[] = [];

const countries: string[] = [];

const decades: string[] = [];

const minPrice = ref(0);

const maxPrice = ref(0);

const selectedGenres = ref<string[]>([]);

const selectedCountries = ref<string[]>([]);

const selectedDecades = ref<string[]>([]);

const sortOrder = ref('asc');

const sortBy = ref('title');

const shoppingList = ref<Item[]>([]);

const user = ref({
    username: '',
    email: '',
    created_at: '',
});

const isLoggedIn = ref(false);

interface ServiceItem {
  id: number;
  title: string;
  release_date: Date;
  genre: string;
  country: string;
  condition: string;
  quantity: number;
  price: number;
  notes: string;
  picture: string;
  seller_name: string;
}

interface Item {
  id: string;
  title: string;
  quantity: number;
  price: number;
}

const serviceItems = ref<ServiceItem[]>([]);

const getUser = async () => {
    try {
        const response = await axiosInstance.get('/user');
        user.value = response.data;
        isLoggedIn.value = true;
    } catch (error) {
        console.error(error);
        isLoggedIn.value = false;
    } finally {
        loading.value = false;
    }
};

const logout = async () => {
    try{
        await axiosInstance.post('/logout');
    } catch (error) {
        console.error(error);
    } finally {
        window.location.href='/';
    }
}

const getImageUrl = (path: string): string => {
  if (path.startsWith('http')) {
    return path;
  }
  return `${'http://music-vault-main-sjukhk.laravel.cloud'}/storage/${path}`;
};

const getServiceItems = async () => {
  loading.value = true
  try {
    const { data } = await axiosInstance.get('/get_service_items', {
      params: {
        genres: selectedGenres.value,
        countries: selectedCountries.value,
        decades: selectedDecades.value,
      }
    })
    serviceItems.value = data;
  } catch (err) {
    console.error(err)
  } finally {
    getGenres();
    getCountries();
    getDecades();
    loading.value = false;
  }
}

const filterServices = async () => {
  loading.value = true
  try {
    const { data } = await axiosInstance.get('/filter_service_items', {
      params: {
        genres: selectedGenres.value,
        countries: selectedCountries.value,
        decades: selectedDecades.value.sort((b, a) => Number(b) - Number(a)),
        minPrice: Number(minPrice.value),
        maxPrice: Number(maxPrice.value),
      }
    })
    serviceItems.value = data;
  } catch (err) {
    console.error(err)
  } finally {
    getGenres();
    getCountries();
    getDecades();
    loading.value = false;
  }
}

const getGenres = async () => {
  serviceItems.value.forEach(item => {
    if (!genres.includes(item.genre)) genres.push(item.genre)
  })
}

const getCountries = async () => {
  serviceItems.value.forEach(item => {
    if (!countries.includes(item.country)) countries.push(item.country)
  })
}

const getDecades = async () => {
  serviceItems.value.forEach(item => {
    const year = new Date(item.release_date).getFullYear();
    const decade = Math.floor(year / 10) * 10;
    if (!decades.includes(decade.toString())) {
      decades.push(decade.toString())
      decades.sort((a, b) => parseInt(b) - parseInt(a));
    }
  })
}

const sortServiceItems = async (sortBy: string) => {
  loading.value = true;
  sortOrder.value = sortOrder.value === 'desc' ? 'asc' : 'desc';
  try {
    const { data } = await axiosInstance.get('/order_services', {
      params: {
        sortBy: sortBy,
        sortOrder: sortOrder.value,
        genres: selectedGenres.value,
        countries: selectedCountries.value,
        decades: selectedDecades.value,
      }
    });
    serviceItems.value = data;
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
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
  if (stored) shoppingList.value = JSON.parse(stored);
}

const deleteFromShoppingList = async (index: number) => { 
  shoppingList.value.splice(index, 1);
  localStorage.setItem("shoppingList", JSON.stringify(shoppingList.value));
}

getUser();
getServiceItems();
loadFromShoppingList();
</script>

<template>
  <div>
    <!-- Reuse AlbumOffers markup but render serviceItems -->
    <nav>...</nav>
    <main>
      <!-- Filters -->
      <form id="filters">
        <h1>Filtri</h1>
        <div id="genre_filters">
          <h2>Žanrs</h2>
          <div class="genre_filter" v-for="genre in genres" :key="genre">
            <input type="checkbox" :value="genre" v-model="selectedGenres" @change="filterServices">
            <label>{{ genre }}</label>
          </div>
        </div>
      </form>

      <div id="offers_list">
        <h2>Piedāvājumi</h2>
        <div class="album_card" v-for="item in serviceItems" :key="item.id">
          <div id="item_info_col">
            <p>Nosaukums: <a :href="`/offer/${item.id}`">{{ item.title }}</a></p>
            <p>Stāvoklis: {{ item.condition }}</p>
            <p>Daudzums: {{ item.quantity }}</p>
          </div>
          <div id="item_seller_col"><p>{{ item.seller_name }}</p></div>
          <div id="item_price_col"><p>{{ item.price }}</p></div>
        </div>
      </div>
    </main>
  </div>
</template>

<style scoped>
/* Keep styles minimal here — app uses global/album styles */
</style>
