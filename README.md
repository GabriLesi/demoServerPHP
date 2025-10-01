📚 Book Manager – Progetto Demo

Questo progetto è una piccola applicazione full-stack per una libreria che dimostra: 
- Backend in PHP con API REST 
- Frontend in HTML + JavaScript che consuma le API 
- Persistenza simulata tramite file JSON (niente database)
- Componenti riutilizzabili
- Routing degli endpoint
- Permanenza della sessione

------------------------------------------------------------------------

🗂 Struttura del progetto
    ├── backend/
    │   ├── data/               # Dati in formato JSON
    │   │   ├── Users.json
    │   │   ├── Books.json
    │   │   └── UserBooks.json
    │   ├── src/
    │   │   ├── Controllers/    # Gestione richieste
    │   │   ├── Utils/          # Router, helpers ecc.
    │   │   ├── Models/         # Dati e logica applicativa
    |   |   └── autoload.php    # Autoload
    │   └── vendor/             # Composer autoload
    ├── frontend/
    │   ├── js/                 # Js condiviso nelle pagine
    │   │   ├── api.js          # codice API
    │   │   └── book-filter.js  # filtro libri condiviso
    │   ├── utils/
    │   │   ├── book-filter.html # codice API
    │   ├── book.html            # html delle varie pagine
    │   ├── books.html
    │   ├── login.html
    │   ├── my-books.html
    │   ├── user.html
    ├── index.php               # Entry point del server PHP
    ├── composer.json           # Impostazione composer
    └── README.md               # Questo readme

------------------------------------------------------------------------

⚙️ Requisiti

-   PHP >= 8.0 (testato sulla 8.4.13)
-   Composer (per autoload PSR-4)

------------------------------------------------------------------------

▶️ Avvio del server

0. Clonare il progetto da Github e aprire un terminale nella root del progetto

1.  Installare le dipendenze:

    composer dump-autoload

2.  Avviare il server PHP:

    php -S localhost:8000 index.php

3.  Aprire http://localhost:8000/login.html

------------------------------------------------------------------------

🔐 Autenticazione

- Tutte le richieste (tranne login/logout) e le pagine statiche (tranne login.html) richiedono autenticazione tramite sessione PHP.
- Se un utente non è loggato:
    -   Accedendo a una pagina HTML protetta → viene reindirizzato a login.html
    -   chiamando un endpoint protetto → viene restituito 401 Unauthorized

------------------------------------------------------------------------

📡 API disponibili

Autenticazione

-   POST /login → login utente
-   GET /logout → logout utente

Utente

-   GET /me → info utente loggato
-   PUT /me → modifica dettagli utente (eccetto id)

Libri

-   GET /books → elenco di tutti i libri
-   GET /books/{id} → dettaglio libro

Libri utente

-   GET /userbooks → elenco libri associati all’utente
-   POST /userbooks → associa libro all’utente
-   DELETE /userbooks → rimuove libro dall’utente

------------------------------------------------------------------------

🧪 Utenti di test

Il file users.json contiene utenti demo per testare diversi profili.
Ad esempio si può accedere con:
- username: mariorossi@gmail.com
- password: mario

------------------------------------------------------------------------

📖 Funzionalità frontend

-   login.html → login utente
-   my-books.html → elenco libri associati con pulsanti per aggiungerne/rimuoverne
-   books.html → elenco completo libri con filtro
-   book.html → dettaglio libro
-   user.html → profilo utente con modalità lettura/modifica

------------------------------------------------------------------------

🚀 Estensioni possibili

-   Registrazione utenti
-   Libri preferiti degli utenti
-   Quantità dei libri (posso aggiungere un libro ai miei solo se quantità > 0)
-   Aggiunta di ruoli (admin / user)
-   Pannello di amministrazione per operazioni CRUD sulla libreria