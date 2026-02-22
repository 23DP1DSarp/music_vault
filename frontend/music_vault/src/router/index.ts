import { createRouter, createWebHistory } from 'vue-router'
import Main from '../views/Main.vue'
import Register from '@/views/auth/Register.vue'
import Login from '@/views/auth/Login.vue'
import AddAlbum from '@/views/AddAlbum.vue'
import Catalog from '@/views/Catalog.vue'
import AlbumInfo from '@/views/AlbumInfo.vue'
import UserProfile from '@/views/UserProfile.vue'
import AlbumOffers from '@/views/AlbumOffers.vue'
import SellerForm from '@/views/SellerForm.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    {
      path: '/',
      name: 'main',
      component: Main,
    },
    {
      path: '/register',
      name: 'register',
      component: Register,
    },
    {
      path: '/login',
      name: 'login',
      component: Login,
    },
    {
      path: '/add-album',
      name: 'add-album',
      component: AddAlbum,
    },
    {
      path: '/catalog',
      name: 'catalog',
      component: Catalog,
    },
    {
      path: '/albuminfo/:id',
      name: 'albuminfo',
      component: AlbumInfo,
    },
    {
      path: '/userprofile',
      name: 'userprofile',
      component: UserProfile,
    },
    {
      path: '/albumoffers',
      name: 'albumoffers',
      component: AlbumOffers,
    },
    {
      path: '/sellerform',
      name: 'sellerform',
      component: SellerForm,
    },
  ],
})

export default router
