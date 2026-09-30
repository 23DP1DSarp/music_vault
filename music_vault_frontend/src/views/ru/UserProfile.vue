<script setup lang="ts">
import Navbar from '@/components/ru/NavbarRu.vue';
import ShoppingMenu from '@/components/ru/ShoppingMenuRu.vue';
import Footer from '@/components/ru/FooterRu.vue';
import axiosInstance from '@/axios';
import {ref} from 'vue';
import { useRouter } from 'vue-router';

const loading = ref(true);

const router = useRouter();

const sortOrder = ref('asc');

const sortBy = ref('title');

const user = ref({
    username: '',
    email: '',
    created_at: new Date(),
});

const isLoggedIn = ref(false);

interface Album {
  id: number;
  title: string;
  author: string;
  release_date: Date;
  genre: string;
  cover: string;
}

const collectionAlbums = ref<Album[]>([]);

const getCollectionAlbums = async () => {
  try {
    const response = await axiosInstance.get('/getcollection');
    collectionAlbums.value = response.data;
    console.log(response.data);
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
    } catch (error) {
        console.error(error);
        isLoggedIn.value = false;
    } finally {
        loading.value = false;
    }
};

const getImageUrl = (path: string): string => {
  if (path.startsWith('http')) {
    return path;
  }

  return `${'http://music-vault-main-sjukhk.laravel.cloud'}/storage/${path}`;
};

const sortAlbums = async (sortBy: string) => {
  loading.value = true;

  if (sortOrder.value === 'desc') {
    sortOrder.value = 'asc';
  } else {
    sortOrder.value = 'desc';
  }

  try {
    const { data } = await axiosInstance.get('/ordercollectionalbums', {
      params: {
        sortBy: sortBy,
        sortOrder: sortOrder.value,
      }
    });
    collectionAlbums.value = data;
  } catch (err) {
    console.error(err);
  } finally {
    loading.value = false;
  }
}

getUser();
getCollectionAlbums();
</script>


<template>
    <body>
    <Navbar />

    <main>
        <ShoppingMenu />
      
        <div id="profile_head">
            <img>
                <h2>{{user?.username}}</h2>
        </div>

        <div id="profile_main">
            <aside id="profile_info">
                <h3>Информация профиля</h3>
                <p><strong>Участник с: </strong>{{new Date(user.created_at).toLocaleDateString()}}</p>
                <p><strong>День рождения: </strong> </p>
                <p><strong>Страна: </strong> </p>
                <p><strong>Добавленных альбомов: </strong> </p>
                <p><strong>Написанных комментариев: </strong> </p>
            </aside>

            <div id="album_collection">
                <h3>Коллекция</h3>
                <div id="filters">
                    <button class="filter_btn" @click="sortAlbums('title')">Название</button>
                    <button class="filter_btn" @click="sortAlbums('author')">Исполнитель</button>
                    <button class="filter_btn" @click="sortAlbums('genre')">Жанр</button>
                    <button class="filter_btn" @click="sortAlbums('release_date')">Год</button>
                </div>
                <div id="albums_grid">
                <div class="album_card" v-for="album in collectionAlbums" :key="album.id">
                    <img v-if="album.cover" :src="getImageUrl(album.cover)" :alt="album.title">
                    <div id="text_info">
                        <p>{{ album.title }}</p>
                        <p>{{ album.author }}</p>
                        <p>{{ album.genre }}</p>
                        <p>{{ album.release_date }}</p>
                    </div>
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
  padding-top: 40px;
  padding-bottom: 40px;
}

#profile_main {
    display: flex;
    flex-direction: row;
    gap: 40px;
    margin-top: 30px;
}

#profile_info {
    width: 250px;
    height: fit-content;
    padding: 20px;
    border: #ECECF0 solid 1px;
    border-radius: 8px;
    font-size: 16px;
    line-height: 20px;
    letter-spacing: 0px;
    color: #0A0A0A;
    background-color: #ECECF0;
}

#album_collection {
    width: 100%;
    height: fit-content;
    padding: 20px;
    border: #ECECF0 solid 1px;
    border-radius: 8px;
    font-size: 16px;
    line-height: 20px;
    letter-spacing: 0px;
    color: #0A0A0A;
}

#filters {
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

.album_card {
    width: 100%;
    height: 200px;
    padding: 20px 0px 20px 0px;
    border-radius: 8px;
    display: flex;
    flex-direction: row;
    gap: 33px;
}

.album_card img {
    width: 150px;
    height: 150px;
    border-radius: 8px;
}

</style>