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
    {
      path: '/ru',
      name: 'main-ru',
      component: () => import('@/views/ru/Main.vue'),
    },
    {
      path: '/ru/register',
      name: 'register-ru',
      component: () => import('@/views/ru/auth/Register.vue'),
    },
    {
      path: '/ru/login',
      name: 'login-ru',
      component: () => import('@/views/ru/auth/Login.vue'),
    },
    {
      path: '/ru/add-album',
      name: 'add-album-ru',
      component: () => import('@/views/ru/AddAlbum.vue'),
    },
    {
      path: '/ru/catalog',
      name: 'catalog-ru',
      component: () => import('@/views/ru/Catalog.vue'),
    },
    {
      path: '/ru/albuminfo/:id',
      name: 'albuminfo-ru',
      component: () => import('@/views/ru/AlbumInfo.vue'),
    },
    {
      path: '/ru/userprofile',
      name: 'userprofile-ru',
      component: () => import('@/views/ru/UserProfile.vue'),
    },
    {
      path: '/ru/albumoffers',
      name: 'albumoffers-ru',
      component: () => import('@/views/ru/AlbumOffers.vue'),
    },
    {
      path: '/ru/sellerform',
      name: 'sellerform-ru',
      component: () => import('@/views/ru/SellerForm.vue'),
    },
    {
      path: '/ru/profilesettings',
      name: 'profilesettings-ru',
      component: () => import('@/views/ru/ProfileSettings.vue'),
    },
    {
      path: '/ru/sell-item',
      name: 'sell-item-ru',
      component: () => import('@/views/ru/SellItem.vue'),
    },
    {
      path: '/ru/offer/:id',
      name: 'offer-ru',
      component: () => import('@/views/ru/Offer.vue'),
    },
    {
      path: '/ru/checkout',
      name: 'checkout-ru',
      component: () => import('@/views/ru/Checkout.vue'),
    },
    {
      path: '/lv',
      name: 'main-lv',
      component: () => import('@/views/lv/Main.vue'),
    },
    {
      path: '/lv/register',
      name: 'register-lv',
      component: () => import('@/views/lv/auth/Register.vue'),
    },
    {
      path: '/lv/login',
      name: 'login-lv',
      component: () => import('@/views/lv/auth/Login.vue'),
    },
    {
      path: '/lv/add-album',
      name: 'add-album-lv',
      component: () => import('@/views/lv/AddAlbum.vue'),
    },
    {
      path: '/lv/catalog',
      name: 'catalog-lv',
      component: () => import('@/views/lv/Catalog.vue'),
    },
    {
      path: '/lv/albuminfo/:id',
      name: 'albuminfo-lv',
      component: () => import('@/views/lv/AlbumInfo.vue'),
    },
    {
      path: '/lv/userprofile',
      name: 'userprofile-lv',
      component: () => import('@/views/lv/UserProfile.vue'),
    },
    {
      path: '/lv/albumoffers',
      name: 'albumoffers-lv',
      component: () => import('@/views/lv/AlbumOffers.vue'),
    },
    {
      path: '/lv/sellerform',
      name: 'sellerform-lv',
      component: () => import('@/views/lv/SellerForm.vue'),
    },
    {
      path: '/lv/profilesettings',
      name: 'profilesettings-lv',
      component: () => import('@/views/lv/ProfileSettings.vue'),
    },
    {
      path: '/lv/sell-item',
      name: 'sell-item-lv',
      component: () => import('@/views/lv/SellItem.vue'),
    },
    {
      path: '/lv/offer/:id',
      name: 'offer-lv',
      component: () => import('@/views/lv/Offer.vue'),
    },
    {
      path: '/lv/checkout',
      name: 'checkout-lv',
      component: () => import('@/views/lv/Checkout.vue'),
    },
  ],
})

export default router
