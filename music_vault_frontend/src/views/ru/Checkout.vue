<script setup lang="ts">
import Navbar from '@/components/ru/NavbarRu.vue';
import ShoppingMenu from '@/components/ru/ShoppingMenuRu.vue';
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

const isLoggedIn = ref(false);

interface Item {
  id: string;
  title: string;
  quantity: number;
  price: number;
  origin_address: string;
  country_id: number;
  sellers_full_name: string;
}

interface CheckoutForm {
  buyers_first_name: string;
  buyers_last_name: string;
  country_id: number;
  city: string;
  shipping_address: string;
  postal_code: string;
  phone_number: number;
  credit_card_number: number;
  expiry_month: number;
  expiry_year: number;
  cvv: number;
}

interface Country {
  id: number;
  country_name: string;
}

const countries = ref<Country[]>([]);

const shoppingList = ref<Item[]>([]);

const checkoutForm = ref<CheckoutForm>({
    buyers_first_name: '',
    buyers_last_name: '',
    country_id: 0,
    city: '',
    shipping_address: '',
    postal_code: '',
    phone_number: 0,
    credit_card_number: 0,
    expiry_month: 0,
    expiry_year: 0,
    cvv: 0,
});


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

const getCountries = async () => {
    try {
      const response = await axiosInstance.get('/getcountries');
      countries.value = response.data;
      console.log(countries.value);
    } catch (error) {
      console.error(error);
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
  if (stored) {
  shoppingList.value = JSON.parse(stored);
  }
}

const deleteFromShoppingList = async (index: number) => { 
  shoppingList.value.splice(index);
  localStorage.setItem("shoppingList", JSON.stringify(shoppingList.value));
}

const submitForm = async () => {
  const formData = new FormData()

  Object.entries(checkoutForm.value).forEach(([key, value]) => {
    if (value !== null) {
      formData.append(key, value as any)
    }
  })

  shoppingList.value.forEach((item, index) => {
        formData.append(`shoppingList[${index}][id]`, item.id)
        formData.append(`shoppingList[${index}][title]`, item.title)
        formData.append(`shoppingList[${index}][quantity]`, String(item.quantity))
        formData.append(`shoppingList[${index}][price]`, String(item.price))
        formData.append(`shoppingList[${index}][origin_address]`, item.origin_address)
        formData.append(`shoppingList[${index}][country_id]`, String(item.country_id))
        formData.append(`shoppingList[${index}][sellers_full_name]`, item.sellers_full_name)
  })

  try {
    const response = await axiosInstance.post('/create_order', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    console.log(response.data);
    if (response.status === 200) {
        router.push('/');
    }
  } catch (error) {
    console.error(error);
  }
}

const createOrder = async (payload: CheckoutForm) => {
  try {
    const response = await axiosInstance.post('/create_order', payload);
    console.log(response.data);
    if (response.status === 200) {
        router.push('/');
    }
  } catch (error) {
    console.error(error);
  }
}





getUser();
getCountries();
loadFromShoppingList();
</script>


<template>
    <body>
    <Navbar />

    <main>
        <ShoppingMenu />

        <h1>Оформление заказа</h1>

        

        <div id="form_wrapper">
            <div id="selected_items">
                <h4>Выбранные товары:</h4>
                <div class="selected_item" v-for="(item, index) in shoppingList">
                  <div id="info_div">
                    <h2>{{ item.title }}</h2>
                    <p @click="deleteFromShoppingList(index)">Удалить</p>
                  </div>
                  <div id="price_div">
                    <b><p id="price">{{ item.price }}$</p></b>
                    <p>Количество: {{ item.quantity }}</p>
                  </div>
            </div>
        </div>

            <form id="checkout_form"  @submit.prevent="submitForm()" method="POST" enctype="multipart/form-data">
                <div id="checkout_wrapper">
                    <div id="contact_info_side">
                        <label>Имя</label>
                        <input class="checkout_input" name="first_name" type="text" v-model="checkoutForm.buyers_first_name">
                        <label>Фамилия</label>
                        <input class="checkout_input" name="last_name" type="text" v-model="checkoutForm.buyers_last_name">
                        <label>Страна</label>
                        <select class="checkout_input" name="country" v-model="checkoutForm.country_id">
                          <option value="" disabled>Выберите страну</option>
                          <option v-for="country in countries" :key="country.id" :value="country.id">{{ country.country_name }}</option>
                        </select>
                        <label>Город</label>
                        <input class="checkout_input" name="city" type="text" v-model="checkoutForm.city">
                        <label>Адрес</label>
                        <input class="checkout_input" name="address" type="text" v-model="checkoutForm.shipping_address">
                        <label>Почтовый индекс</label>
                        <input class="checkout_input" name="postal_code" type="text" v-model="checkoutForm.postal_code">
                        <label>Номер телефона</label>
                        <input class="checkout_input" name="phone_number" type="tel" v-model="checkoutForm.phone_number">
                    </div>
                        
                    <div class="payment_info_side">
                        <label>Номер кредитной карты</label>
                        <input class="checkout_input" name="card_number" type="number" v-model="checkoutForm.credit_card_number">
                        <div id="subpayment_info_side">
                            <div class="payment_info_side">
                                <label>Срок действия</label>
                                <div id="expiry_date_input">
                                    <input class="checkout_input" name="month_of_expiration" type="text" autocomplete="cc-exp" placeholder="MM" v-model="checkoutForm.expiry_month">
                                    <input class="checkout_input" name="year_of_expiration" type="text" autocomplete="cc-exp" placeholder="YY" v-model="checkoutForm.expiry_year">
                                </div>
                            </div>
                            <div class="payment_info_side">
                                <label>CVV</label>
                                <input class="checkout_input" name="cvv" type="number" v-model="checkoutForm.cvv">
                            </div>
                        </div>
                    </div>
                </div>
                <input id="submit_btn" type="submit" value="Оплатить и заказать">
            </form>
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
  padding-top: 65px;
  padding-bottom: 65px;
  margin: 0 auto;
}

main h1 {
  text-align: center;
  margin-bottom: 25px;
}

#form_wrapper {
  display: flex;
  flex-direction: column;
  justify-content: center;
  width: fit-content;
  align-items: start;
  margin: 0 auto;
  gap: 85px;
}

#selected_items {
  background-color: #E4E4E4;
  border-radius: 10px;
  padding: 10px;
  display: flex;
  flex-direction: column;
  gap: 10px;
  width: 100%;
  box-sizing: border-box;
}

.selected_item {
  display: flex;
  flex-direction: row;
  align-items: baseline;
  justify-content: space-between;
  background-color: #FFFFFF;
  border-radius: 20px;
  padding: 20px;
  max-width: inherit;
}

#checkout_form {
  display: flex;
  flex-direction: column;
}

#checkout_wrapper {
  display: flex;
  flex-direction: row;
  gap: 150px;
}

#contact_info_side {
  width: 380px;
  display: flex;
  flex-direction: column;
}

.payment_info_side {
  display: flex;
  flex-direction: column;
}

#subpayment_info_side {
  display: flex;
  flex-direction: row;
max-width: 386px;
}

#subpayment_info_side input{
  width: fit-content;
}

#expiry_date_input {
  display: flex;
  flex-direction: row;
  gap: 20px;
}

#expiry_date_input input{
  width: 30%;
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
  min-width: 386px;
  min-height: 54px;
  margin-top: 50px !important;
  background-color: #000000;
  color: #E4E4E4;
  font-size: 18px;
  font-weight: normal;
  line-height: 28px;
  letter-spacing: -0.5px;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  border-radius: 8px;
  border: none;
  margin: 0 auto;
}

.checkout_input {
  width: 380px;
  height: 50px;
  border-style: solid;
  border-color: #000000;
  border-radius: 8px;
  border-width: 1px;
  padding: 1px 2px;
}

</style>