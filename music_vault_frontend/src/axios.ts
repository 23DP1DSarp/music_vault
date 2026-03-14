import axios from 'axios';

const axiosInstance = axios.create({
    baseURL: 'https://music-vault-main-0eyyqx.laravel.cloud/api',
    withCredentials: true,
    withXSRFToken: true,
});


export default axiosInstance;