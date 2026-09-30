<script setup lang="ts">
import Navbar from '@/components/lv/NavbarLv.vue';
import ShoppingMenu from '@/components/lv/ShoppingMenuLv.vue';
import Footer from '@/components/lv/FooterLv.vue';
import axiosInstance from '@/axios';
import {ref} from 'vue';
import { useRouter } from 'vue-router';

const loading = ref(true);

const router = useRouter();

const user = ref({
    username: '',
    email: '',
    user_role_id: 0,
});

const isLoggedIn = ref(false);

interface Item {
  id: string;
  title: string;
  quantity: number;
  price: number;
  origin_address: string;
  shipping_country_id: number;
  sellers_full_name: string;
  error?: string;
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
        //console.log(response.data);
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
      //console.log(countries.value);
    } catch (error) {
      console.error(error);
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

const updateItemInShoppingList = async (index: number, operator: string) => {
  if (operator === "-" && shoppingList.value[index]?.quantity) {
    shoppingList.value[index].quantity -= 1;
    localStorage.setItem("shoppingList", JSON.stringify(shoppingList.value))
  } else if (operator === "+" && shoppingList.value[index]?.quantity) {
    shoppingList.value[index].quantity += 1;
    localStorage.setItem("shoppingList", JSON.stringify(shoppingList.value))
  }
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
        formData.append(`shoppingList[${index}][shipping_country_id]`, String(item.shipping_country_id))
        formData.append(`shoppingList[${index}][sellers_full_name]`, item.sellers_full_name)
  })

  try {
    const response = await axiosInstance.post('/create_order', formData, {
      headers: { 'Content-Type': 'multipart/form-data' }
    });
    //console.log(response.data);
    if (response.status === 200) {
      localStorage.removeItem("shoppingList");
      router.push('/');
    } else if (response.status === 202) {

      shoppingList.value.forEach((item, index) => {
        response.data.unavailable_items.forEach((unavailableItem: any) => {
        if (unavailableItem.id == shoppingList.value[index]?.id && shoppingList.value[index]) {
          //console.log('Unavailable items:');
          shoppingList.value[index].error = `Prece "${item.title}" nav pieejama. Pieejamais daudzums: ${unavailableItem.available_quantity}`;
        }
      })
  })
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
        

        <h1>Norēķins</h1>

        <div id="form_wrapper">
            <div id="selected_items">
                <h4>Jūsu izvēlētās preces:</h4>
                <div class="selected_item" v-for="(item, index) in shoppingList">
                  <div id="info_div">
                    <h2>{{ item.title }}</h2>
                    <p @click="deleteFromShoppingList(index)">Dzēst</p>
                    <br>
                    <div class="error_box" v-if="item.error">
                      <p>{{ item.error }}</p>
                    </div>
                  </div>
                  <div id="price_div">
                    <b><p id="price">{{ item.price }}$</p></b>
                    <p>Daudzums: {{ item.quantity }}</p>
                    <div id="item_quantity">
                      <button class="quantity_btn" @click="updateItemInShoppingList(index, '-')">-</button>
                      <input id="quantity_input" type="number" v-model="item.quantity">
                      <button class="quantity_btn" @click="updateItemInShoppingList(index, '+')">+</button>
                    </div>
                  </div>
                </div>
            </div>

            <form id="checkout_form"  @submit.prevent="submitForm()" method="POST" enctype="multipart/form-data">
                <div id="checkout_wrapper">
                    <div id="contact_info_side">
                        <label>Vārds</label>
                        <input class="checkout_input" name="first_name" type="text" v-model="checkoutForm.buyers_first_name">
                        <label>Uzvārds</label>
                        <input class="checkout_input" name="last_name" type="text" v-model="checkoutForm.buyers_last_name">
                        <label>Valsts</label>
                        <select class="checkout_input" name="country" v-model="checkoutForm.country_id">
                          <option value="" disabled>Izvēlieties valsti</option>
                          <option v-for="country in countries" :key="country.id" :value="country.id">{{ country.country_name }}</option>
                        </select>
                        <label>Pilsēta</label>
                        <input class="checkout_input" name="city" type="text" v-model="checkoutForm.city">
                        <label>Adrese</label>
                        <input class="checkout_input" name="address" type="text" v-model="checkoutForm.shipping_address">
                        <label>Pasta indekss</label>
                        <input class="checkout_input" name="postal_code" type="text" v-model="checkoutForm.postal_code">
                        <label>Tālruņa numurs</label>
                        <input class="checkout_input" name="phone_number" type="tel" v-model="checkoutForm.phone_number">
                    </div>
                        
                    <div class="payment_info_side">
                        <label>Kredītkartes numurs</label>
                        <input class="checkout_input" name="card_number" type="number" v-model="checkoutForm.credit_card_number">
                        <div id="subpayment_info_side">
                            <div class="payment_info_side">
                                <label>Derīguma termiņš</label>
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
                <input id="submit_btn" type="submit" value="Apmaksāt un pasūtīt">
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

.error_box {
  color: #ec1c31;
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

#checkout_btn {
  width: 100%;
  height: 40px;
  background-color: #FFFFFF;
  color: #0A0A0A;
  border: solid rgba(0, 0, 0, .1) 1px;
  border-radius: 8px;
  text-align: center;
  vertical-align: middle;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  font-size:18px;
  line-height: 20px;
  letter-spacing: 0px;
  cursor: pointer;
}

@media (max-width:480px) {

#navwrapper {
  align-items: center;
}

body {
  width: 100%;
  margin: 0 auto;
  padding: 0;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  overflow-x: hidden;
}

nav {
  width: 100%;
}

#navbuttons, #rightbuttons {
  width: 0;
  height: 0;
  display: none;
}

#mobile_btns, #hamburger_menu {
  display: block;
}

#mobile_btns {
  display: flex;
  flex-direction: row;
  gap: 20px;
}

#logoutbtn {
  font-size: 19.53px;
  padding: 0;
}

#navbuttons_mobile {
  height: min-content;
}

#navbuttons_mobile ul {
  display: flex;
  flex-direction: column;
  gap: 20px;
  padding: 0;
}

#rightbuttons_mobile {
  display: flex;
  flex-direction: column;
  gap: 20px;
  margin-top: 0px;
}

#rightbuttons_mobile ul {
  display: flex;
  flex-direction: column;
  gap: 20px;
  padding: 0;
  margin: 0;
}

#hamburger_icon {
  width: 24px;
  height: 24px;
  padding: 5px;
  border-style: solid;
  border: #ECECF0 solid 1px;
  border-radius: 8px;
  text-align: center;
  cursor: pointer;
}

#hamburger_menu {
  height: 100%;
  width: 0px;
  position: fixed;
  z-index: 1;
  top: 0;
  right: 0;
  background-color: #E4E4E4;
  overflow-x: hidden; 
  padding-top: 20px;
  padding-left: 20px;
  padding-right: 20px;
  transition: 0.5s;
  visibility: hidden;
  display: flex;
  flex-direction: column;
  gap: 50px;
}

#language_options_mobile {
  display: flex;
  flex-direction: row;
  gap: 10px;
}

#language_options_mobile ul {
  display: flex;
  flex-direction: row;
  gap: 10px;
  padding: 0;
}

main {
  padding-top: 0px;
}

#language_options_mobile p {
  margin: 0;
}

#form_wrapper {
  display: flex;
  flex-direction: column;
  width: 90vw;
}

.selected_item {
  display: flex;
  flex-direction: row;
}

#info_div h2 {
  margin-bottom: 0px;
}

#info_div p {
  margin-top: 13px;
}

#price_div {
  text-align: end;
}

#price_div p {
  margin-top: 0px;
  width: 100px;
}

#price {
  margin-bottom: 8px;
}

#item_quantity {
  display: flex;
  flex-direction: row;
}

#item_quantity input {
  width: 53px;
}

#checkout_wrapper {
  display: flex;
  flex-direction: column;
  width: fit-content;
  margin-left: 10px;
  gap: 80px;
}

#checkout_wrapper input {
  width: 82.333vw;
  height: 10.417vw;
}

#checkout_wrapper select {
  width: 84vw;
  height: 11.5vw;
}

#payment_info_side {
  display: flex;
  flex-direction: column;
}

#payment_info_side label {
  width: 10px;
}

#subpayment_info_side {
  display: flex;
  flex-direction: column;
}

#expiry_date_input {
  display: flex;
  flex-direction: row;
}

#expiry_date_input input {
  width: 141px;
}

#submit_btn {
  min-width: 83vw;
  min-height: 11.25vw;
  margin-top: 50px;
  background-color: #000000;
  color: #E4E4E4;
  font-size: 18px;
  font-weight: normal;
  line-height: 28px;
  letter-spacing: -0.5px;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  border-radius: 8px;
  border: none;
  margin-left: 13px;
}

#footer_top {
  width: 90vw;
  margin: 0 auto;
  padding-top: 50px;
  padding-bottom: 20px;
  display: flex;
  flex-direction: column;
  gap: 50px;
  align-items: center;
}

#footer_info {
  display: flex;
  flex-direction: column;
  width: 100%;
  max-width: 357px;
  margin-right: 0;
  font-size: 13.89px;
  line-height: 20px;
  letter-spacing: 0px;
  color: #717182;
  text-align: center;
  align-items: center;
}

.footer_links {
  display: flex;
  width: 100%;
  flex-direction: column;
  align-items: center;
  text-align: center;
}

.footer_links ul {
  display: flex;
  width: 100%;
  flex-direction: column;
  gap: 14px;
  padding: 0;
  align-items: center;
}

#footer_bottom, #footer_bottom ul {
  display: flex;
  flex-direction: column;
  gap: 20px;
  padding-top: 20px;
  padding-bottom: 20px;
  padding-left: 0;
  padding-right: 0;
  align-items: center;
}

}


</style>