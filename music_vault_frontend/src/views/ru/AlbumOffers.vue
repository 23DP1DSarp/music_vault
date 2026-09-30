<script setup lang="ts">
import Navbar from '@/components/ru/NavbarRu.vue';
import ShoppingMenu from '@/components/ru/ShoppingMenuRu.vue';
import Footer from '@/components/ru/FooterRu.vue';
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

const user = ref({
    username: '',
    email: '',
    created_at: '',
});

const isLoggedIn = ref(false);

interface AlbumItem {
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

const albumItems = ref<AlbumItem[]>([]);

const getUser = async () => {
    try {
        const response = await axiosInstance.get('/user');
        user.value = response.data;
        isLoggedIn.value = true;
        console.log(response.data);
    } catch (error) {
        console.error(error);
        isLoggedIn.value = false;
    } finally {
        loading.value = false;
    }
};

const logout = async () => {
    try{
        const response = await axiosInstance.post('/logout');
        console.log(response.data);
    } catch (error) {
        console.error(error);
    } finally {
        window.location.href='/ru';
    }
}

const getImageUrl = (path: string): string => {
  if (path.startsWith('http')) {
    return path;
  }

  return `${'http://music-vault-main-sjukhk.laravel.cloud'}/storage/${path}`;
};


const getAlbumItems = async () => {
  loading.value = true

  try {
    const { data } = await axiosInstance.get('/get_album_items', {
      params: {
        genres: selectedGenres.value,
        countries: selectedCountries.value,
        decades: selectedDecades.value,
      }
    })
    albumItems.value = data;
  } catch (err) {
    console.error(err)
  } finally {
    getGenres();
    getCountries();
    getDecades();
    loading.value = false;
  }
}


const filterAlbums = async () => {
  loading.value = true

  try {
    const { data } = await axiosInstance.get('/filter_album_items', {
      params: {
        genres: selectedGenres.value,
        countries: selectedCountries.value,
        decades: selectedDecades.value.sort((b, a) => Number(b) - Number(a)),
        minPrice: Number(minPrice.value),
        maxPrice: Number(maxPrice.value),
      }
    })
    albumItems.value = data;
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
  console.log('Function called...')
  albumItems.value.forEach(albumItem => {
  if (!genres.includes(albumItem.genre)) {
    genres.push(albumItem.genre)
  }
  })
}

const getCountries = async () => {
  console.log('Function called...')
  albumItems.value.forEach(albumItem => {
  if (!countries.includes(albumItem.country)) {
    countries.push(albumItem.country)
  }
  })
}

const getDecades = async () => {
  console.log('Function called...')
  albumItems.value.forEach(albumItem => {
  const year = new Date(albumItem.release_date).getFullYear();
  const decade = Math.floor(year / 10) * 10;
  if (!decades.includes(decade.toString())) {
    decades.push(decade.toString())
    decades.sort((a, b) => parseInt(b) - parseInt(a));
  }
  })
}

const sortAlbumItems = async (sortBy: string) => {
  loading.value = true;

  if (sortOrder.value === 'desc') {
    sortOrder.value = 'asc';
  } else {
    sortOrder.value = 'desc';
  }

  try {
    const { data } = await axiosInstance.get('/order_albums', {
      params: {
        sortBy: sortBy,
        sortOrder: sortOrder.value,
        genres: selectedGenres.value,
        countries: selectedCountries.value,
        decades: selectedDecades.value,
      }
    });
    albumItems.value = data;
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
  }
}

getUser();
getAlbumItems();
</script>

<template>
    <body>
    <Navbar />
      
    <main>
    <ShoppingMenu />

        <div id="album_info">
                <div id="album_text_info">
                    <h1> - </h1>
                    <p></p>
                </div>
        </div>


        <div id="offers_section">

            <form id="filters">
            <h1>Фильтры</h1>
                <div id="genre_filters">
                    <h2>Жанр</h2>
                    <div class="genre_filter" v-for="genre in genres" :key="genre">
                        <input type="checkbox" :value="genre" v-model="selectedGenres" @change="filterAlbums">
                        <label>{{ genre }}</label>
                    </div>
                </div>

                <div id="price_range_filters">
                    <h2>Диапазон цен</h2>
                    <div id="price_range_filters_inputs">
                        <input type="number" name="min" v-model="minPrice" @change="filterAlbums">
                        <p> - </p>
                        <input type="number" name="max" v-model="maxPrice" @change="filterAlbums"> 
                    </div>
                </div>

                <div id="decade_filters">
                    <h2>Десятилетие</h2>
                    <div class="decade_filter" v-for="decade in decades" :key="decade">
                        <input type="checkbox" :value="decade" v-model="selectedDecades" @change="filterAlbums">
                        <label>{{ decade }}</label>
                    </div>
                </div>

                <div id="country_filters">
                    <h2>Страна</h2>
                    <div class="country_filter" v-for="country in countries" :key="country">
                        <input type="checkbox" :value="country" v-model="selectedCountries" @change="filterAlbums">
                        <label>{{ country }}</label>
                    </div>
                </div>
        </form>


            <div id="offers_list">
                <h2>Предложения</h2>
                <div id="list_filters">
                  <button class="filter_btn" >Название</button>
                  <button class="filter_btn" >Исполнитель</button>
                  <button class="filter_btn">Жанр</button>
                  <button class="filter_btn">Год</button>
                  <button class="filter_btn">Продавец</button>
                  <button class="filter_btn" >Цена</button> 
                </div>
                <div class="album_card" v-for="albumItem in albumItems" :key="albumItem.id">
                  
                
                <div id="item_info_col">
                    <p>Название: <a :href="`/offer/${albumItem.id}`">{{ albumItem.title }}</a></p>
                    <p>Состояние: {{ albumItem.condition }}</p>
                    <p>Количество: {{ albumItem.quantity }}</p>
                </div>

                <div id="item_seller_col">  
                    <p>{{ albumItem.seller_name }}</p>
                </div>

                <div id="item_price_col">
                    <p>{{ albumItem.price }}</p>
                </div>

                </div>
            </div>
               
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
  width: 80vw;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
}

#offers_section {
    display: flex;
    gap: 25px;
}

#filters {
  display: flex;
  flex-direction: column;
  align-items: flex-start;
}

#filters h1 {
  font-size: 24px;
}

#filters h2 {
  font-size: 18px;
  margin-top: 0;
}

#filters input[type="checkbox"] {
  width: 18px;
  height: 18px;
}

.genre_filter, .decade_filter, .country_filter {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 10px;
}

#genre_filters {
  padding: 0;
}

#price_range_filters_inputs {
  display: flex;
  flex-direction: row;
  align-items: center;
  gap: 10px;
  height: 25px;
}

#price_range_filters_inputs input{
  width: 100px;
  height: 20px;
}

#genre_filters, #price_range_filters, #decade_filters, #country_filters {
    padding-bottom: 20px;
}

#list_filters {
    width: 100%;
    height: fit-content;
    padding: 0px 0px 0px 0px;
    display: flex;
    flex-direction: row;
    border: #ECECF0 solid 1px;
    border-radius: 8px;
    font-size: 16px;
    line-height: 20px;
    letter-spacing: 0px;
    color: #0A0A0A;
    background-color: #ECECF0;
}

.filter_btn {
    font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: transparent;
    border-style: solid;
    border: #030213 1px;
    border-radius: 8px;
    padding: 6px 12px 6px 12px;
    font-size: 16px;
    line-height: 20px;
    letter-spacing: 0px;
    cursor: pointer;
}


#offers_list {
    width: 100%;
    height: fit-content;
    font-size: 16px;
    line-height: 20px;
    letter-spacing: 0px;
    color: #0A0A0A;
}

.album_card {
    width: 100%;
    height: 200px;
    padding: 20px 0px 20px 0px;
    border-radius: 8px;
    display: flex;
    flex-direction: row;
    gap: 33px;
}

.item_info_col {
  display: flex;
  flex-direction: column;
  gap: 10px;
}

.album_card img {
    width: 150px;
    height: 150px;
    border-radius: 8px;
}

</style>