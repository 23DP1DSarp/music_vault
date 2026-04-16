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
import EmailVerification from '@/views/auth/EmailVerification.vue'
import ProfileSettings from '@/views/ProfileSettings.vue'
import SellItem from '@/views/SellItem.vue'
import Offer from '@/views/Offer.vue'
import Checkout from '@/views/Checkout.vue'

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
    {
      path: '/email/verify/:id/:hash',
      name: 'emailverification',
      component: EmailVerification,
    },
    {
      path: '/profilesettings',
      name: 'profilesettings',
      component: ProfileSettings,
    },
    {
      path: '/sell-item',
      name: 'sell-item',
      component: SellItem,
    },
    {
      path: '/offer/:id',
      name: 'offer',
      component: Offer,
    },
    {
      path: '/checkout',
      name: 'checkout',
      component: Checkout,
    },
  ],
})

export default router
