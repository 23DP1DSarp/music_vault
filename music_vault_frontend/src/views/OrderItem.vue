<script setup lang="ts">
import axiosInstance from '@/axios';
import {ref} from 'vue';
import { useRouter } from 'vue-router';

const loading = ref(true);

const router = useRouter();

const user = ref({
    name: '',
    email: '',
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

const albums = ref<Album[]>([]);

const getAlbums = async () => {
  try {
    const response = await axiosInstance.get('/');
    albums.value = response.data;
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

const logout = async () => {
    try{
        const response = await axiosInstance.post('/logout');
        console.log(response.data);
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




getUser();
getAlbums();
</script>

<template>

<body v-if="loading !== true">
    <nav>
        <div id="navwrapper">
        <RouterLink to="/">
            <div id="logo">
            <img src="../images/nav_images/vinyl_icon.svg">
            <p>MusicVault</p>
        </div></RouterLink>

        <div id="navbuttons">
            <ul>
                <li>New Releases</li>
                <li>Genres</li>
                <li>Artists</li>
                <li>Forums</li>
                <RouterLink to="/add-album" v-if="isLoggedIn">Add Album</RouterLink>
            </ul>
        </div>

        <div id="rightbuttons">
            
            <input type="text" id="searchbar" name="recordsearch" placeholder="Search records...">
            <img id="shoppingcart" src="../images/nav_images/shopping_cart_icon.svg">
            <RouterLink to="/userprofile" v-if="isLoggedIn">{{user?.name}}</RouterLink>
            <form action="/logout" @submit.prevent="logout" v-if="isLoggedIn">
                <button id="logoutbtn">Log out</button>
            </form>
            <RouterLink to="/login" v-if="!isLoggedIn">Log In</RouterLink>
            <RouterLink to="/register" v-if="!isLoggedIn">Sign Up</RouterLink>
        </div>
    </div>
    </nav>


    <main>
        
    </main>

    <footer>
        <div id="footer_wrapper">
        <div id="footer_top">
            <div id="footer_info">
                <div id="footer_logo">
                    <img src="../images/footer_images/vinyl_icon.svg">
                    <p>MusicVault</p>
                </div>
                <p id="footer_info_text">
                    Your premier destination for music records. Discover,
                    collect, and enjoy music the way it was meant to be
                    heard.
                </p>

                <div id="icons">
                    <img class="icon" src="../images/footer_images/facebook_icon.svg">
                    <img class="icon" src="../images/footer_images/instagram_icon.svg">
                    <img class="icon" src="../images/footer_images/twitter_icon.svg">
                    <img class="icon" src="../images/footer_images/youtube_icon.svg">
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