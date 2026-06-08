# MusicVault

MusicVault ir mācību projekts, kas paredzēts mūzikas albumu informācijas saglabāšanai un ar mūziku saistītu preču iegādei tiešsaistē. Sistēmā lietotāji var apskatīt albumus, pievienot albumus savai kolekcijai, pārdevēji var ievietot preces pārdošanā, bet reģistrēti lietotāji var veidot pasūtījumus.

## Projekta mērķis

Projekta mērķis ir izveidot tīmekļa sistēmu, kas apvieno mūzikas albumu katalogu un e-komercijas funkcionalitāti. Albums sistēmā var pastāvēt kā informācijas vienība arī tad, ja tas netiek pārdots, savukārt preces tiek izmantotas pārdošanas procesam.

## Galvenā funkcionalitāte

- Lietotāju reģistrācija un pieslēgšanās
- Lietotāju lomu izmantošana
- Pārdevēja profila izveide
- Albumu pievienošana un apskate
- Dziesmu pievienošana albumiem
- Albumu filtrēšana pēc žanra, valsts un izdošanas perioda
- Albumu pievienošana lietotāja kolekcijai
- Preču ievietošana pārdošanā
- Albumu preču filtrēšana pēc cenas un citiem kritērijiem
- Pasūtījumu izveide
- Pasūtījuma rindu saglabāšana

## Izmantotās tehnoloģijas

- PHP 8.4.0
- Laravel 12.47.0
- MySQL
- Vue.js
- HTML
- CSS
- JavaScript

## Galvenās datubāzes tabulas

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

## Projekta struktūra

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

## Uzstādīšana

1. Noklonēt repozitoriju:

```bash
git clone https://github.com/23DP1DSarp/music_vault.git
```

2. Atvērt projekta mapi:

```bash
cd music_vault
```

3. Instalēt PHP atkarības:

```bash
composer install
```

4. Instalēt frontend atkarības:

```bash
npm install
```

5. Izveidot `.env` failu:

```bash
cp .env.example .env
```

6. Ģenerēt aplikācijas atslēgu:

```bash
php artisan key:generate
```

7. Konfigurēt datubāzes piekļuves datus `.env` failā.

8. Izpildīt migrācijas:

```bash
php artisan migrate
```

9. Palaist backend serveri:

```bash
php artisan serve
```

10. Palaist frontend izstrādes serveri:

```bash
npm run dev
```

## Autors

Daniils Šarpans  
DP3-1  
Rīgas Valsts tehnikums

## Projekta statuss

Projekts tiek izstrādāts mācību nolūkos. Pašlaik ir realizēta pamatfunkcionalitāte: lietotāju autentifikācija, albumu pārvaldība, preču pārdošana, kolekcija un pasūtījumu izveide.
