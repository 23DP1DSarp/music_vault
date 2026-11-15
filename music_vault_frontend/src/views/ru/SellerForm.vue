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

interface SellerForm {
    currency_id: number;
    full_name: string;
    shipping_address: string;
    minimal_order_total: string;
    seller_terms: string;
    currency_id_error?: string;
    full_name_error?: string;
    shipping_address_error?: string;
    minimal_order_total_error?: string;
    seller_terms_error?: string;
}

interface Currency {
  id: number;
  currency_name: string;
}

const currencies = ref<Currency[]>([]);

const sellerForm = ref(<SellerForm>({
    currency_id: 0,
    full_name: '',
    shipping_address: '',
    minimal_order_total: '',
    seller_terms: '',
}));

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

const getCurrencies = async () => {
    try {
      const response = await axiosInstance.get('/get_currencies');
      currencies.value = response.data;
    } catch (error) {
      console.error(error);
    }
}

const createSeller = async (payload: SellerForm) => {
  try {
    const response = await axiosInstance.post('/createseller', payload);
    console.log(response.data);
    if (response.status === 200) {
        router.push('/');
    }
  } catch (error) {
    console.error(error);
  }
}

function validateForm(sellerform: SellerForm){
  let validFullName = isValidFullName(sellerform.full_name);
  let validShippingAddress = isValidShippingAddress(sellerform.shipping_address);
  let validMinimalOrderTotal = isValidMinimalOrderTotal(sellerform.minimal_order_total);
  let validSellerTerms = isValidSellerTerms(sellerform.seller_terms);
  let isSelectedCurrency = sellerform.currency_id > 0;

  if (!isSelectedCurrency) {
    sellerForm.value.currency_id_error = 'Пожалуйста, выберите валюту.';
  } else {
    sellerForm.value.currency_id_error = '';
  }

  if (isSelectedCurrency && validFullName && validShippingAddress && validMinimalOrderTotal && validSellerTerms) {
    createSeller(sellerform);
  }
}

function isValidFullName(full_name: string): boolean {
    const MAX_FULLNAME_LENGTH = 30;

    const fullNameRegex = /^[a-zA-Z]$/;

    if (!full_name || full_name.length === 0 ) {
        sellerForm.value.full_name_error = 'Пожалуйста, введите ваше полное имя.';
        return false;
    } else if (full_name.length > MAX_FULLNAME_LENGTH) {
        sellerForm.value.full_name_error = 'Полное имя не может содержать более 30 символов.';
        return false;
    } else if (!fullNameRegex.test(full_name)) {
        sellerForm.value.full_name_error = 'Полное имя не должно содержать цифры и специальные символы, такие как: !@#$%^&*()';
        return false;
    } else {
        sellerForm.value.full_name_error = '';
        return true;
    }
}

function isValidShippingAddress(shipping_address: string): boolean {
    const MAX_SHIPPING_ADDRESS_LENGTH = 50;

    const shippingAddressRegex = /^[^!@#\$%\^&\*\(\)]{1,50}$/;

    if (!shipping_address || shipping_address.length === 0 ) {
        sellerForm.value.shipping_address_error = 'Пожалуйста, введите адрес доставки.';
        return false;
    } else if (shipping_address.length > MAX_SHIPPING_ADDRESS_LENGTH) {
        sellerForm.value.shipping_address_error = 'Адрес доставки не может содержать более 50 символов.';
        return false;
    } else if (!shippingAddressRegex.test(shipping_address)) {
        sellerForm.value.shipping_address_error = 'Адрес доставки не должен содержать специальные символы, такие как: !@#$%^&*()';
        return false;
    } else {
        sellerForm.value.shipping_address_error = '';
        return true;
    }
}

function isValidMinimalOrderTotal(minimal_order_total: string): boolean {
    const MAX_MINIMAL_ORDER_TOTAL_LENGTH = 50;

    const minimalOrderTotalRegex = /^\d+([.,]\d+)?$/;

    if (!minimal_order_total || minimal_order_total.length === 0 ) {
        sellerForm.value.minimal_order_total_error = 'Пожалуйста, введите минимальную сумму заказа.';
        return false;
    } else if (!minimalOrderTotalRegex.test(minimal_order_total)) {
        sellerForm.value.minimal_order_total_error = 'Минимальная сумма заказа не может содержать буквы.';
        return false;
    } else if (minimal_order_total.length > MAX_MINIMAL_ORDER_TOTAL_LENGTH) {
        sellerForm.value.minimal_order_total_error = 'Минимальная сумма заказа не может содержать более 50 символов.';
        return false;
    } else {
        sellerForm.value.minimal_order_total_error = '';
        return true;
    }
}

function isValidSellerTerms(seller_terms: string): boolean {
    const MAX_SELLER_TERMS_LENGTH = 200;

    if (seller_terms.length > MAX_SELLER_TERMS_LENGTH) {
        sellerForm.value.seller_terms_error = 'Условия продавца не могут содержать более 200 символов.';
        return false;
    } else {
        sellerForm.value.seller_terms_error = '';
        return true;
    }
}

getUser();
getCurrencies();
</script>

<template>
    <body>
    <Navbar />

    <main>
        <ShoppingMenu />

        <h1>Форма продавца</h1>

        <form id="seller_form" @submit.prevent="createSeller(sellerForm)" action="/login" method="post">

                <div class="form_parts">
                    <label>Валюта</label>
                    <select class="album_input" name="currency" v-model="sellerForm.currency_id">
                      <option value="" disabled>Выберите валюту</option>
                      <option v-for="currency in currencies" :key="currency.id" :value="currency.id">{{ currency.currency_name }}</option>
                    </select>
                </div>
                <p v-if="sellerForm.currency_id_error" style="color: red;">{{ sellerForm.currency_id_error }}</p>

                <div class="form_parts">
                    <label>Полное имя</label>
                    <input v-model="sellerForm.full_name" name="full_name" type="text">
                    <p v-if="sellerForm.full_name_error" style="color: red;">{{ sellerForm.full_name_error }}</p>
                </div>

                <div class="form_parts">
                    <label>Адрес отправки</label>
                    <input v-model="sellerForm.shipping_address" name="shipping_address" type="text">
                    <p v-if="sellerForm.shipping_address_error" style="color: red;">{{ sellerForm.shipping_address_error }}</p>
                </div>

                <div class="form_parts">
                    <label>Минимальная сумма заказа</label>
                    <input v-model="sellerForm.minimal_order_total" name="minimal_order_total" type="text">
                    <p v-if="sellerForm.minimal_order_total_error" style="color: red;">{{ sellerForm.minimal_order_total_error }}</p>
                </div>

                <div class="form_parts">
                    <label>Условия продавца</label>
                    <input v-model="sellerForm.seller_terms" name="seller_terms" type="text">
                    <p v-if="sellerForm.seller_terms_error" style="color: red;">{{ sellerForm.seller_terms_error }}</p>
                </div>

                <div class="form_parts">
                    <input id="submit_btn" type="submit" value="Сохранить">
                </div>

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
  width: 380px;
  margin: 0 auto;
  display: flex;
  flex-direction: column;
  align-items: center;
}

#seller_form {
  display: flex;
  flex-direction: column;
  margin-bottom: 50px;
}

#seller_form input {
  width: 380px;
  height: 50px;
  border-style: solid;
  border-color: #000000;
  border-radius: 8px;
  border-width: 1px;
  padding: 1px 2px;
}

#seller_form select {
  width: 385.6px;
  height: 53.6px;
  border-style: solid;
  border-color: #000000;
  border-radius: 8px;
  border-width: 1px;
  padding: 1px 2px;
}

.form_parts {
  width: 380px;
  display: flex;
  flex-direction: column;
}

#seller_form label {
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
  margin-top: 30px;
  background-color: #000000;
  color: #E4E4E4;
  font-size: 18px;
  font-weight: normal;
  line-height: 28px;
  letter-spacing: -0.5px;
  font-family: Segoe UI Symbol, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  border-radius: 8px;
  border: none;
}
</style>

