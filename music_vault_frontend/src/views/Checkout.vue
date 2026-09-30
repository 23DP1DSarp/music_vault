<script setup lang="ts">
import Navbar from '@/components/en/Navbar.vue';
import ShoppingMenu from '@/components/en/ShoppingMenu.vue';
import Footer from '@/components/en/Footer.vue';
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

const getCountries = async () => {
    try {
      const response = await axiosInstance.get('/getcountries');
      countries.value = response.data;
      console.log(countries.value);
    } catch (error) {
      console.error(error);
    }
}

const createOrder = async () => {
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

const deleteFromShoppingList = async (index: number) => { 
  shoppingList.value.splice(index, 1);
  localStorage.setItem("shoppingList", JSON.stringify(shoppingList.value));
}

getUser();
getCountries();
</script>


<template>
<body>
    <Navbar />

    <main>
        <ShoppingMenu />

        <h1>Checkout</h1>

        <div id="form_wrapper">
            <div id="selected_items">
                <h4>Items you have selected:</h4>
                <div class="selected_item" v-for="(item, index) in shoppingList">
                  <div id="info_div">
                    <h2>{{ item.title }}</h2>
                    <p @click="deleteFromShoppingList(index)">Delete</p>
                  </div>
                  <div id="price_div">
                    <b><p id="price">{{ item.price }}$</p></b>
                    <p>Quantity: {{ item.quantity }}</p>
                  </div>
                </div>
            </div>

            <form id="checkout_form"  @submit.prevent="createOrder()" method="POST" enctype="multipart/form-data">
                <div id="checkout_wrapper">
                    <div id="contact_info_side">
                        <label>First Name</label>
                        <input class="checkout_input" name="first_name" type="text" v-model="checkoutForm.buyers_first_name">
                        <label>Last Name</label>
                        <input class="checkout_input" name="last_name" type="text" v-model="checkoutForm.buyers_last_name">
                        <label>Country</label>
                        <select class="checkout_input" name="country" v-model="checkoutForm.country_id">
                          <option value="" disabled>Select country</option>
                          <option v-for="country in countries" :key="country.id" :value="country.id">{{ country.country_name }}</option>
                        </select>
                        <label>City</label>
                        <input class="checkout_input" name="city" type="text" v-model="checkoutForm.city">
                        <label>Address</label>
                        <input class="checkout_input" name="address" type="text" v-model="checkoutForm.shipping_address">
                        <label>Postal Code</label>
                        <input class="checkout_input" name="postal_code" type="text" v-model="checkoutForm.postal_code">
                        <label>Phone Number</label>
                        <input class="checkout_input" name="phone_number" type="tel" v-model="checkoutForm.phone_number">
                    </div>
                        
                    <div class="payment_info_side">
                        <label>Credit Card Number</label>
                        <input class="checkout_input" name="card_number" type="number" v-model="checkoutForm.credit_card_number">
                        <div id="subpayment_info_side">
                            <div class="payment_info_side">
                                <label>Date of Expiration</label>
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
                <input id="submit_btn" type="submit" value="Pay and order">
            </form>
        </div>
    </main>

    <Footer />
</body>
</template>

<style scoped>
@font-face {
  font-family: Segoe UI Symbol;
  src: url('../assets/fonts/Segoe-UI-Symbol.ttf');
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

footer {
  border-top: solid #ECECF0 1px;
}

footer h6 {
  font-size: 16px;
  margin-top: 15px;
  line-height: 24px;
}

#footer_wrapper {
  width: 80vw;
  margin: 0 auto;
  padding-left: 100px;
  padding-right: 100px;
  padding-top: 50px;
  padding-bottom: 50px;
}


#footer_top {
  display: flex;
  flex-direction: row;
  padding-bottom: 20px;
  margin: 0 auto;
  border-bottom: solid #ECECF0 1px;
}

#footer_info {
  display: flex;
  flex-direction: column;
  width: 357px;
  margin-right: 67px;
  font-size: 13.89px;
  line-height: 20px;
  letter-spacing: 0px;
  color: #717182;
}

#footer_info_text {
  width: 317.84px;
  height: 54px;
  margin-bottom: 20px;
}

#footer_info p {
  margin-top: 15px;
}

#footer_logo {
  display: flex;
  flex-direction: row;
  align-items: center;
  font-size: 17.72px;
  font-weight: normal;
  line-height: 28px;
  letter-spacing: -0.5px;
  gap: 5px;
  color: #0A0A0A;
}

#footer_logo img {
  width: 24px;
  height: 24px;
}

#icons {
  display: flex;
  flex-direction: row;
  gap: 8px;
}

.icon {
  width: 16px;
  height: 16px;
  padding: 6px;
  border: #ECECF0 solid 1px;
  border-radius: 8px;
  cursor: pointer;
}

#footer_top ul {
  width: 18vw;
  font-size: 14px;
  line-height: 20px;
  letter-spacing: 0px;
  color: #717182;
  display: flex;
  flex-direction: column;
  gap: 14px;
  list-style: none;
  padding: 0;
}

#subscribe_form p {
  color: #717182;
  line-height: 20px;
  letter-spacing: 0px;
}

#email_input {
  background-color: #F3F3F5;
  border-style: none;
  width: 352px;
  height: 36px;
  color: #717182;
  padding: 0px 0px 0px 10px;
  border-radius: 8px;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  font-size: 14px;
}

#subscribe_form_submit {
  margin-top: 5px;
  width: 362px;
  height: 32px;
  border-style: none;
  border-radius: 8px;
  background-color: #030213;
  color: #E4E4E4;
  font-size: 14px;
  line-height: 20px;
  letter-spacing: 0px;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  cursor: pointer;
}

#footer_bottom {
  display: flex;
  flex-direction: row;
  justify-content: space-between;
  font-size: 14px;
  line-height: 20px;
  letter-spacing: 0px;
  color: #717182;
  margin-top: 40px;
}

#footer_bottom ul {
  display: flex;
  flex-direction: row;
  gap: 24px;
  list-style: none;
  padding: 0;
}
</style>