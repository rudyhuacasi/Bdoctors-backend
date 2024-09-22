# TEMPLATE LARAVEL BREEZE

> [!NOTE]
>
> nome repo: templete-laravel-breeze


### Passi per la creazione del Template:

#### creiamo prima la cartella del progetto:
- creiamo la cartella con il nome progetto, una volta aperto in VSCode da terminale:
```
composer create-project laravel/laravel:^10.0 .
```
*oppure:*
- da terminale del computer ci spostiamo con il comando `cd` dentro la cartella in cui vogliamo creare il progetto e digitiamo da terminale:
```
composer create-project laravel/laravel:^10.0 nome-del-progetto
```
con il comando `cd` ci spostiamo dentro il progetto e con il comando `code .` apriamo il progetto con VSCode.


#### Installiamo Breeze nel progetto:

1. Installiamo **Laravel Breeze**, servendoci del comando da terminale:
```
composer require laravel/breeze --dev
```

2. Creiamo lo scaffolding di default con balde:
```
php artisan breeze:install
```
- una CLI ci guidarà per la scelta e l'istallazione dello stack breeze:
• Blade with Aplpine;
• Livewire (Volt Class API) with Alpine;
• Livewire (Volt Functional API) with Alpine;
• React with Inertia;
• Vue with Inertia;
• API only.

Per questo template sceglieremo:
```
Blade with Aplpine
```

- Ci chiederà se vogliamo utilizzare il dark mode, per questo template sceglieremo:
```
YES
```

- Ci chiederà che testing framework vogliamo utilizzare:
• PHPUnit;
• Pest.

Per questo template sceglieremo:
```
PHPUnit
```

3. Abbiamo le migrations nella cartella **migrations**, e tramite il comando da terminale:
```
php artisan migration
```

#### Installiamo Boostrap:
1. tramite terminali eseguiamo questo comandi:
```
npm remove postcss
npm remove tailwindcss
npm i --save-dev sass
npm i --save bootstrap @popperjs/core
```

2. Cancellare il file `tailwind.config.js` e `postcss.config.js`:

```
rm tailwind.config.js
rm postcss.config.js
```

3. Rinominiamo la cartella css in scss:

```
mv resources/css -> resources/scss
```

4. Rinominiamo il file app.css in app.scss:

```
mv resources/scss/app.css ->  resources/scss/app.scss
```

5. Nel file `app.scss` cancelliamo gli import di `tailwind` dal file `app.scss` e inseriamo:

```
@import "~bootstrap/scss/bootstrap";
```

6. Nel file `vite.config.js`:

- modifichiamo il percorso del `css` in `scss` e `app.css` in `app.scss`;
- aggiungiamo un alias per resources e per il bootstrap:

```
import path from 'path';

resolve: {
        alias: {
            '~resources': '/resources/',
            '~bootstrap': path.resolve(__dirname, 'node_modules/bootstrap')
        }
    },
```

7. Nel file `app.js`:

- togliere il codice che imposta alpine, lasciando solo la prima riga
- importare app.css, bootstrap e img

```
import '~resources/scss/app.scss'
import * as bootstrap from 'bootstrap'
import.meta.glob([
    '../img/**'
])
```


### Impostazioni per utilizzare il template:

1. Duplicare il file `.env.exemple` e rinominarlo `.env`, o da terminale eseguire il comando:
```
cp .env.example .env
```

2. Da terminale eseguire il comando: 
```
composer install
```

4. Da terminale eseguire il comando:
```
php artisan key:generate
```

5. Da terminale eseguire il comando:
```
npm install
```

6. Da terminale eseguire il comando:
```
php artisan serve
```

7. Aprire secondo terminale ed eseguire il comando:
```
npm run dev
```

- È stato già creato il `Controller` con il comando da terminale:
```
php artisan make:controller Guest/PageController
```
Il file `PageCntroller.php` si occuperà di ritornare la **view** della pagina, andrà aggunta questa riga di codice:
```
return view('index', $data);
```
- Il file `web.php` gestirà solamente la chiamata che verrà girata alla `pageController`, nel caso non ci fosse andrà aggiunta questa righa di codice:
```
Route::get('/', [PageController::class, 'index']);
```

8. Resta da creare il **Model** secondo esigenze con il comando da terminale te uscirà una lista di comani per chiamare migration, seeder ..., controller:
```
php artisan make:model NomeModel --help

```
9. Questo comando serve per vedere le immagini
```
php artisan storage:link
```
10. Questo comando serve per mettere la tabella a mysql
```
php artisan migrate
```
11. Questo comando serve per mettere tutta la informazione del seeder alla tabella
```
php artisan db:seed --class=NombreDelSeeder
php artisan db:seed
```