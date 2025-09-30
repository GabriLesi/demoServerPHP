Progetto di esempio semplice semplice per una webapp da girare localmente su PHP 8.4

L'applicazione emula degli utenti di una libreria, che dopo un login effettuare operazioni CRUD sui libri a loro associati. 
L'admin può fare operazioni CRUD sul contenuto della libreria invece

Per semplicità di riproduzione, invece di usare tabelle ho usato un file JSON che ne simula la struttura.

Cosa serve (già installato)
- PHP 8.4 
- Composer

Vai nella root del progetto
Lancia "composer dump-autoload"
Lancia localmente PHP con "php -S localhost:8000" o qualsiasi porta preferisci

Ora le chiamate a localhost:8000 e simili ritorneranno il login, inserendo le credenziali di un utente si potrà armeggiare con la sua lista di libri