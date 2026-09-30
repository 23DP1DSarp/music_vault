<script setup lang="ts">
import Navbar from '@/components/ru/NavbarRu.vue';
import Footer from '@/components/ru/FooterRu.vue';
import axiosInstance from '@/axios';
import {ref} from 'vue';
import { useRouter } from 'vue-router';

const loading = ref(true);

const router = useRouter();

const user = ref({
    username: '',
    email: '',
});

const albums = ref<{id: number, title: string}[]>([]);

const categories = ref<string[]>([
    'Album',
    'Instrument',
    'Service',
]);

const conditions = ref<string[]>([
    'Good',
    'Used',
    'Bad',
]);

interface SellItemForm {
    title: string;
    category: string;
    quantity: number;
    price: number;
    model: string | null;
    type: string | null;
    duration: number;
    condition: string;
    description: string;
    picture: File | null;
    album_id: number | null;
}

const item = ref<SellItemForm>({
    title: '',
    category: '',
    quantity: 0,
    price: 0,
    model: null,
    type: null,
    duration: 0,
    condition: '',
    description: '',
    picture: null as File | null,
    album_id: null,
});

const isLoggedIn = ref(false);

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

const getAlbums = async () => {
  try {
    const response = await axiosInstance.get('/');
    albums.value = response.data;
    console.log(response.data);
  } catch (error) {
    console.error(error);
  }
}

const handleFileChange = (event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];
  console.log('Selected file:', file);
  if (file) {
    item.value.picture = file;
  }
};

const sellItem = async (payload: SellItemForm) => {
  try {
    const formData = new FormData();
    Object.entries(payload).forEach(([key, value]) => {
      if (value !== null && value !== undefined) {
        formData.append(key, value as any);
      }
    });
    if (item.value.picture) {
      formData.append('picture', item.value.picture);
    }
    const response = await axiosInstance.post('/sellitem', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    console.log(response.data);
    if (response.status === 200) {
        router.push('/ru');
    }
  } catch (error) {
    console.error(error);
  }
}

getUser();
getAlbums();
</script>

<template>

<body>
    <Navbar />

    <main>
        <ShoppingMenu />

        <h1>Продать товар</h1>
        <form id="add_album_with_tracks" @submit.prevent="sellItem(item)" method="POST" enctype="multipart/form-data">
                <div id="album_wrapper">
                    <div id="input_side">
                        <label>Название</label>
                        <input class="album_input" v-model="item.title" name="title" type="text">
                        <label>Категория</label>
                        <select class="album_input" name="category" v-model="item.category">
                          <option value="" disabled>Выберите категорию</option>
                          <option v-for="category in categories" :key="category" :value="category">{{ category }}</option>
                        </select>
                        <label v-show="item.category === 'Album'">Album Название</label>
                        <select class="album_input" name="album_id" v-model="item.album_id" v-show="item.category === 'Album'">
                          <option value="" disabled>Выберите альбом</option>
                          <option v-for="album in albums" :key="album.id" :value="album.id">{{ album.title }}</option>
                        </select>
                        <div v-show="item.category === 'Instrument'">
                        <label>Модель</label>
                        <input class="album_input" v-model="item.model" name="model" type="text">

                        <label>Тип</label>
                        <input class="album_input" v-model="item.type" name="type" type="text">
                        </div>


                        <label v-show="item.category !== 'Service'">Quantity</label>
                        <input class="album_input" v-show="item.category !== 'Service'" v-model.number="item.quantity" name="quantity" type="number" >
                        <label>Цена</label>
                        <input class="album_input" v-model.number="item.price" name="price" type="number" step="0.01">
                        <label v-show="item.category !== 'Service'">Состояние</label>
                        <select v-show="item.category !== 'Service'" class="album_input" name="condition" v-model="item.condition">
                          <option value="" disabled>Выберите состояние</option>
                          <option v-for="condition in conditions" :key="condition" :value="condition">{{ condition }}</option>
                        </select>

                        <label v-show="item.category === 'Service'">Длительность</label>
                        <input class="album_input" v-show="item.category === 'Service'" v-model.number="item.duration" name="price" type="number" step="0.01">

                        <label>Описание</label>
                        <input class="album_input" v-model="item.description" name="description" type="text">
                    </div>
                        
                    <div id="album_cover_side">
                        <label>Фото товара</label>
                        <input name="item_picture" type="file" accept="image/*" @change="handleFileChange">
                    </div>
                </div>
                <input id="submit_btn" type="submit" value="Продать товар">
            </form>
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
  width: fit-content;
  padding-top: 65px;
  padding-bottom: 65px;
  margin: 0 auto;
}

main h1 {
  text-align: center;
  margin-bottom: 25px;
}


#add_album_with_tracks {
  display: flex;
  flex-direction: column;
}


#album_wrapper {
  display: flex;
  flex-direction: row;
  gap: 150px;
}

#input_side {
  width: 380px;
  display: flex;
  flex-direction: column;
}

#album_cover_side {
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

#submit_btn {
  width: 386px;
  min-height: 54px;
  background-color: #000000;
  color: #E4E4E4;
  font-size: 18px;
  font-weight: normal;
  line-height: 28px;
  letter-spacing: -0.5px;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  border-radius: 8px;
  border: none;
  margin-top: 50px;
  align-self: center;
}

.album_input {
  width: 380px;
  height: 50px;
  border-style: solid;
  border-color: #000000;
  border-radius: 8px;
  border-width: 1px;
  padding: 1px 2px;
}
</style>