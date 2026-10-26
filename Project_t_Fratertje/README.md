# Ledenadministratie ’t Fratertje

Eerste uitbreiding van het aangeleverde Laravel 12-project. MVC, Blade, Eloquent en MySQL blijven de basis. Deze versie voegt de administratie toe; de volledige schoolopdracht is **nog niet af**. Zie `docs/OPLEVERING.md` voor de status en open beslissingen.

## Vereisten

- PHP 8.2 of hoger, passend bij `composer.lock`, met onder andere PDO MySQL, mbstring, XML, ctype, fileinfo en openssl.
- Composer 2 en een draaiende MySQL-database.
- Voor de standaardtests ook PDO SQLite.
- Visual Studio Code kan de uitgepakte projectmap openen. De app gebruikt gewone CSS en JavaScript uit `public`; Node/npm is voor deze schermen niet nodig.

## Installatie in VS Code

1. Pak de ZIP uit in een **nieuwe map** en open `Project_t_Fratertje` via **File → Open Folder**. Bewaar je oorspronkelijke project en database als back-up.
2. Open de terminal in de map met `artisan` en `composer.json`:

   ```sh
   composer install
   ```

3. Kopieer `.env.example` naar `.env`.

   PowerShell:
   ```powershell
   Copy-Item .env.example .env
   ```

   macOS/Linux:
   ```sh
   cp .env.example .env
   ```

4. Maak in MySQL/phpMyAdmin een lege database `fratertje` aan met `utf8mb4`. Vul in `.env` de eigen `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` en `DB_PASSWORD` in. De meegeleverde voorbeeldinstelling heeft geen echt wachtwoord.
5. Voer uit:

   ```sh
   php artisan key:generate
   php artisan optimize:clear
   php artisan migrate --seed
   php artisan serve
   ```

6. Open `http://127.0.0.1:8000`.

De voorbeeldconfiguratie gebruikt `SESSION_DRIVER=file`, `CACHE_STORE=file` en `QUEUE_CONNECTION=sync`. Er zijn dus geen ontbrekende sessie-, cache- of jobtabellen nodig. De tijdzone is `Europe/Amsterdam`.

### Lokale testinlog

- E-mail: `beheerder@fratertje.test`
- Wachtwoord: `Fratertje-Demo-2026!`

Het demoaccount wordt alleen bij `APP_ENV=local` of `testing` aangemaakt. Een opnieuw uitgevoerde seeder overschrijft geen bestaand wachtwoord. Gebruik dit bekende demoaccount uitsluitend lokaal. Voor gebruik met echte gegevens zijn een eigen beheeraccount, HTTPS, passende toegangsrechten en `APP_DEBUG=false` nodig.

## Bestaande database behouden

De oorspronkelijke vijf migrations zijn behouden. Een **nieuwe** migration hernoemt `members.is_activate` naar `is_active` en voegt de nieuwe velden en tarieventabel toe. Gebruik `php artisan migrate --seed`, **geen** `migrate:fresh` op een database die je wilt bewaren. `migrate:fresh` wist tabellen en gegevens.

De seeder herkent bestaande lidsoorten met gangbare namen (zoals Standaardlid, Volwassen lid, Jeugdlid en Gastlid) en behoudt hun ID en prijs. Controleer daarna `/member-types`: onbekende of dubbele oude namen kunnen nog `Nog niet ingedeeld` tonen. Die moeten eerst handmatig aan de juiste vaste soort worden gekoppeld; bestaande leden worden niet automatisch verplaatst. Bestaande leden zonder de verplichte gegevens worden niet door deze migration gerepareerd.

Het aangeleverde `.env`, bestaande databasebestanden, logs, caches, `vendor` en `node_modules` zijn niet opgenomen in de oplevering. Je eigen database wordt niet naar deze versie gekopieerd. Bij een bestaande installatie houd je je eigen `.env` en `APP_KEY`; neem alleen de noodzakelijke instellingen over.

## Beschikbare functies

- Veilig inloggen en uitloggen, sessievernieuwing en begrenzing van inlogpogingen.
- Dashboard met aantallen en recent toegevoegde leden.
- Leden toevoegen, bekijken en wijzigen, inclusief één adres.
- Zoeken op voornaam, achternaam of kweeknummer; filteren op lidsoort en status.
- Archiveren met bevestiging, archief bekijken en leden terugzetten.
- Leeftijdscontrole: jeugdlid onder 18; volwassen lid vanaf 18; gastlid alle leeftijden.
- Een verplicht, uniek kweeknummer voor jeugd- en volwassen leden; geen kweeknummer voor gastleden.
- Centrale kweeknummerlijst, met beheer via het lidformulier.
- Namen en omschrijvingen van de drie vaste lidsoorten wijzigen.
- Nieuwe jaarprijzen instellen voor volgend kalenderjaar, zonder lopende en oudere jaartarieven te overschrijven.
- Nederlandstalige foutmeldingen en een responsive interface zonder externe frontendafhankelijkheden.

## Tests en controle

**Uitvoeringsstatus:** in de bewerkingsomgeving ontbreekt PHP/Composer. De code is statisch nagelopen, maar de Laravel-app, migrations, Blade-views en PHPUnit-tests zijn hier niet uitgevoerd. Er is dus geen claim dat alle tests slagen of dat MySQL-integratie al is aangetoond.

Voer lokaal uit:

```sh
php artisan test
php artisan route:list --except-vendor
```

`phpunit.xml` gebruikt een tijdelijke SQLite-database in het geheugen en een uitsluitend voor tests bedoelde APP_KEY. De tests wissen je normale MySQL-database niet bij deze standaardconfiguratie. Verander testdatabase-instellingen niet naar je werkdatabase.

Er zijn tests voor authenticatie, opgeslagen relaties, paginaresponsen, leeftijdsgrenzen, verplichte en unieke kweeknummers, gastleden, archiveren/herstellen, zoeken/filteren, tariefhistorie en het terugdraaien van een mislukte transactie. `MoneyTest` controleert de geldhelper.

Test daarnaast met MySQL op een aparte lege database: migrations en seeders, gastlid toevoegen, volwassen lid met nummer toevoegen, dubbele nummers weigeren, bewerken en archiveren/herstellen. Controleer de schermen ook op mobiel. De tests schakelen CSRF uit zoals Laravel-testverzoeken standaard doen; CSRF moet daarom aanvullend via de echte browser worden gecontroleerd.

## Structuur

- `routes/web.php`: publieke login en administratie achter `auth`.
- `app/Http/Controllers`: dunne controllers voor routes en responses.
- `app/Http/Requests/MemberRequest.php`: invoer, leeftijd en NBvV-regels.
- `app/Services/MemberService.php`: lid/adres/nummer in één databasetransactie.
- `app/Models`: Eloquent-relaties en soft deletes.
- `app/Support/Money.php`: helper met integer centen en euro-opmaak.
- `database/migrations`: oorspronkelijke tabellen plus aanvullende migration.
- `database/seeders`: vaste lidsoorten en lokaal demoaccount.
- `resources/views`: Blade-schermen met escaping en CSRF-tokens.
- `public/css/admin.css`, `public/js/admin.js`: vormgeving en archiefbevestiging.
- `tests`: featuretests en unittests.
- `docs/OPLEVERING.md`: beperkingen, beslissingen en vervolgwerk.

## Git en schooloplevering

Werk lokaal verder met betekenisvolle commits per onderdeel. Als je deze map nog niet in Git beheert:

```sh
git init
git add .
git commit -m "Voeg basis ledenadministratie met authenticatie en validatie toe"
```

Commit nooit `.env`, databasebestanden met persoonsgegevens of logs. Voeg echte testuitkomsten aan je verslag toe zodra je de tests lokaal hebt uitgevoerd. Een uiteindelijke SQL-export en demonstratievideo moeten uit de werkende, gecontroleerde app worden gemaakt; deze zijn niet gefabriceerd of meegeleverd.
