# Deploy op Plesk (mijnpizzeria.nl)

De app draait op subdomeinen per pizzeria: `zaaknaam.mijnpizzeria.nl`. De routing daarvoor zit al in [routes/web.php](routes/web.php) en werkt zodra de punten hieronder kloppen.

## 1. Bestanden op de server

De hele app (dus niet alleen `public/`) staat in `httpdocs`. De document root van zowel `mijnpizzeria.nl` als `*.mijnpizzeria.nl` mag gewoon op `httpdocs` staan: de `.htaccess` in de projectroot stuurt alles door naar `public/`.

Na elke deploy:

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build
php artisan migrate --force
php artisan config:clear
```

Let op: geen `php artisan route:cache` draaien. De routes gebruiken closures en die kunnen niet gecachet worden, het commando faalt dan.

## 2. .env op de server

De regels die anders zijn dan lokaal:

```
APP_ENV=production
APP_DEBUG=false
APP_URL=https://mijnpizzeria.nl
CENTRAAL_DOMEIN=mijnpizzeria.nl
```

Zonder `CENTRAAL_DOMEIN=mijnpizzeria.nl` bestaan de subdomein-routes niet en geeft elk subdomein een 404. Na het aanpassen altijd `php artisan config:clear`.

`SESSION_DOMAIN` blijft leeg (null): zo krijgt elke pizzeria zijn eigen sessiecookie en lopen klantlogins per zaak niet door elkaar.

## 3. Wildcard-subdomein in Plesk

- Maak onder het abonnement een subdomein aan met de naam `*` (dus `*.mijnpizzeria.nl`) en document root `httpdocs`.
- Zet bij Apache & nginx-instellingen van het wildcard-subdomein "Serve static files directly by nginx" uit, of accepteer dat alleen bestanden in `public/` bedoeld zijn om direct geserveerd te worden.

## 4. DNS

Er moet een wildcard-record bestaan: `*.mijnpizzeria.nl` als A-record naar het server-IP (of CNAME naar `mijnpizzeria.nl`). Ligt de DNS bij Plesk, dan zet Plesk dit record zelf bij het aanmaken van het wildcard-subdomein. Ligt de DNS bij de registrar, voeg het record daar handmatig toe.

## 5. SSL

Een gewoon Let's Encrypt-certificaat dekt geen wildcard. Vraag in Plesk een wildcard-certificaat aan (Let's Encrypt met DNS-challenge). Ligt de DNS extern, dan vraagt Plesk om een TXT-record (`_acme-challenge`) bij de registrar te zetten. Zonder wildcard-certificaat geeft elk pizzeria-subdomein een certificaatwaarschuwing.

## 6. Testen

1. `https://mijnpizzeria.nl` toont de marketingsite.
2. `https://pizzeriasola.mijnpizzeria.nl` toont de bestelpagina van de zaak met slug `pizzeriasola`. De slug moet wel bestaan (kolom `slug` in de `users`-tabel), anders is een 404 juist.
3. `https://www.mijnpizzeria.nl` valt bewust terug op de marketingsite, `www` is uitgesloten van de subdomein-routing.
