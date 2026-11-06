# Ledenadministratie ’t Fratertje

Laravel 12-ledenadministratie met authenticatie, ledenbeheer, lidsoortenbeheer, zelfstandig kweeknummerbeheer, validatie en herstelbare archieven. De onderstaande installatie gebruikt bestaande migrations en bewaart bestaande gegevens. Deze oplevering richt zich op de afgesproken checklist; facturatie en publieke aanmeldprocessen vallen daarbuiten.

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

Bewaar je eigen `.env` en `APP_KEY`. Voer `php artisan migrate --force` uit om de aanvullende migrations toe te passen. De migration van 6 november 2026 voegt uitsluitend `deleted_at` aan `member_types` toe; de bestaande tabellen en gegevens blijven behouden. Gebruik nooit `migrate:fresh` op een database met te bewaren gegevens.

De bestaande seeder maakt de drie standaardlidsoorten aan zonder hun IDs of bestaande tarieven te vervangen. Een gearchiveerde standaardlidsoort wordt bij opnieuw seeden niet stilzwijgend opnieuw aangemaakt. Bestaande onvolledige leden worden niet automatisch aangepast; bij opnieuw opslaan gelden de actuele validatieregels.

## Beschikbare functies

- Inloggen, uitloggen, sessievernieuwing, begrensde inlogpogingen en beheer achter authenticatie.
- Leden toevoegen, bekijken, wijzigen, archiveren en herstellen, inclusief volledig adres en NBvV-kweeknummer.
- Ledenoverzicht met naam, lidsoort, woonplaats, kweeknummer en actieve status; aparte archiefweergave.
- Zoeken op naam of kweeknummer en combineren van lidsoort- en statusfilters.
- Drie geseede standaardlidsoorten, plus lidsoorten toevoegen, wijzigen, archiveren en herstellen. Gekoppelde lidsoorten kunnen niet worden gearchiveerd totdat de leden, ook uit het archief, een andere soort hebben.
- Zelfstandig kweeknummers toevoegen, bekijken, wijzigen, archiveren en herstellen. Zowel huidige als gearchiveerde nummers blijven uniek.
- Ieder actief lid heeft verplicht een kweeknummer en uitgiftejaar, ook een actief gastlid of een zelf aangemaakte lidsoort. Een inactief lid mag zonder nummer bestaan.
- Archiveren van een kweeknummer zet het lid in dezelfde transactie op inactief. Herstellen van het nummer verandert de lidstatus niet automatisch.
- Zichtbare bevestigingsdialogen bij archiveren van leden, lidsoorten en kweeknummers. Niets wordt definitief verwijderd.
- Leeftijdscontrole voor jeugd- en volwassen leden; Nederlandstalige foutmeldingen via Laravel-validatie.
- Jaartarieven; wijzigingen gelden vanaf volgend kalenderjaar en behouden eerdere tarieven.

## Tests en controle

Voer `php artisan test --no-ansi` uit. De tests gebruiken SQLite in het geheugen en veranderen de normale MySQL-database niet. De suite dekt authenticatie, routes, relaties, leeftijd, actieve leden inclusief gastleden, unieke nummers, alle CRUD/archiefacties, tariefhistorie en transacties. Nieuwe regressietests staan in `tests/Feature/CrudCompletionTest.php`.

De browserdemonstratie gebruikt een aparte testdatabase met fictieve gegevens en echte formulieren met CSRF-tokens. De testuitvoer en de videogegevens worden in het opleververslag vastgelegd. Op productie of bestaande gegevens worden geen testresets uitgevoerd.

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
- `docs/OPLEVERING.md`: gemaakte keuzes, verificatie en reikwijdte.

## Git en schooloplevering

Werk lokaal verder met betekenisvolle commits per onderdeel. Als je deze map nog niet in Git beheert:

```sh
git init
git add .
git commit -m "Voeg basis ledenadministratie met authenticatie en validatie toe"
```

Commit nooit `.env`, databasebestanden met persoonsgegevens of logs. Voeg echte testuitkomsten aan je verslag toe zodra je de tests lokaal hebt uitgevoerd. Een uiteindelijke SQL-export en demonstratievideo moeten uit de werkende, gecontroleerde app worden gemaakt; deze zijn niet gefabriceerd of meegeleverd.
