# MusicVault

MusicVault is an educational web application designed to store music album information and support the online sale of music-related products. The system allows users to browse albums, add albums to their personal collections, sellers to list products for sale, and registered users to place orders.

## Project Goal

The goal of the project is to develop a web application that combines a music album catalog with e-commerce functionality. Albums can exist in the system as informational records even if they are not available for sale, while products are used for the purchasing process.

## Main Features

- User registration and authentication
- User role management
- Seller profile creation
- Adding and browsing music albums
- Adding tracks to albums
- Filtering albums by genre, country, and release period
- Adding albums to a personal collection
- Listing products for sale
- Filtering album products by price and other criteria
- Creating orders
- Managing order items

## Technologies Used

- PHP 8.4.0
- Laravel 12.47.0
- MySQL
- Vue.js
- HTML
- CSS
- JavaScript

## Main Database Tables

- users
- user_roles
- sellers
- currencies
- albums
- tracks
- genres
- countries
- items
- album_items
- instruments
- services
- collections
- wishlist
- orders
- order_items

## Project Structure

```text
music_vault/
├── app/
│   ├── Http/Controllers/
│   └── Models/
├── database/
│   └── migrations/
├── routes/
│   ├── api.php
│   └── web.php
├── resources/
├── public/
├── composer.json
└── package.json
```

## Installation

1. Clone the repository:

```bash
git clone https://github.com/23DP1DSarp/music_vault.git
```

2. Navigate to the project directory:

```bash
cd music_vault
```

3. Install PHP dependencies:

```bash
composer install
```

4. Install frontend dependencies:

```bash
npm install
```

5. Create the environment file:

```bash
cp .env.example .env
```

6. Generate the application key:

```bash
php artisan key:generate
```

7. Configure your database credentials in the `.env` file.

8. Run the database migrations:

```bash
php artisan migrate
```

9. Start the backend development server:

```bash
php artisan serve
```

10. Start the frontend development server:

```bash
npm run dev
```

## Author

**Daniils Šarpans**  
DP3-1  
Riga State Technical School

## Project Status

This project is being developed for educational purposes. The core functionality has already been implemented, including user authentication, album management, product listings, personal collections, and order creation.
