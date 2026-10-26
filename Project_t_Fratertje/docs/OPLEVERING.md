# Oplevernotities — basisversie 29 september 2026

## Wat in het oorspronkelijke project stond

Laravel 12, de tabellen users/member_types/addresses/members/breeding_numbers, lege functionele modellen en de standaardwelkomstpagina. Er waren nog geen beheercontrollers, formulieren of loginroutes.

## Aangebrachte wijzigingen

1. Relaties en veilige mass assignment voor de bestaande modellen toegevoegd.
2. Aanvullende migration voor de typefout `is_activate`, gebruikersvelden voor Laravel-auth/factories, vaste lidsoortcodes, soft deletes op kweeknummers en jaartarieven.
3. Login/logout, authenticatiemiddleware, sessievernieuwing en loginbeperking toegevoegd.
4. Leden-CRUD met Nederlandse Form Request-validatie en één transactie voor lid/adres/nummer toegevoegd.
5. Dashboard, zoeken/filteren, detail/formulieren, archief en herstel toegevoegd.
6. Kweeknummerlijst en beheer via het lidformulier toegevoegd.
7. Lidsoorten bewerken en tariefplanning voor het volgende jaar toegevoegd.
8. Sessies en cache standaard op bestanden ingesteld: de oorspronkelijke database-instellingen verwezen naar ontbrekende tabellen.
9. Installatiehandleiding, geldhelper en gerichte tests toegevoegd.

## Gemaakte keuzes

- `breeding_numbers.breeding_number` is de enige bron van het NBvV-nummer. Er is geen tweede `members.NBvV_number` met dezelfde waarde toegevoegd; zo kunnen twee kopieën niet uiteenlopen.
- Database-uniqueness op `members.address_id`, `breeding_numbers.member_id` en `breeding_numbers.breeding_number` blijft behouden. Foreign keys bewaken de koppelingen.
- Leeftijd en de gastlid/NBvV-combinatie worden door het Form Request afgedwongen. Rechtstreekse SQL of eigen aanroepen van de service kunnen die regels omzeilen; volledige databaseafdwinging van die bedrijfsregels is nog niet gerealiseerd.
- Ook inactieve jeugd-/volwassen leden moeten bij opslaan een nummer hebben, volgens de strengere regel in de opdracht.
- Een gearchiveerd lid houdt zijn adres en nummer. Het standaardoverzicht sluit het lid uit; het archief bevat de eerdere actieve status. Herstellen zet die eerdere status terug.
- Bij omzetting naar gastlid wordt het nummer soft-deleted. Het nummer blijft gereserveerd zolang het in de tabel staat. Bij terugzetten naar NBvV-lid wordt dezelfde rij hergebruikt. Het aanpassen van een nummer overschrijft die rij; volledige nummerhistorie is nog niet aanwezig.
- Lidsoortcodes `adult`, `youth`, `guest` blijven vast. De weergavenaam kan veranderen zonder de bedrijfsregels te wijzigen.
- Tarieven staan in centen per ingangsjaar. Voor een jaar zonder eigen tarief geldt het laatst vastgelegde eerdere tarief. De oude kolom `member_types.price` blijft voor compatibiliteit bewaard, maar wordt niet meer voor tariefwijzigingen gebruikt.
- De bestaande database wordt niet gewist en de oorspronkelijke migrations zijn niet herschreven.

## Nog nodig voor de volledige opdracht

| Onderdeel | Status / vervolg |
| --- | --- |
| Publiek digitaal aanmelden | Nog bouwen: aanvraaggegevens, toestemming, quarantaine en goedkeuren. |
| Publiek digitaal afmelden | Nog bouwen: verificatie van de afzender, verzoekstatus, verwerking en ingangsdatum. |
| Signaal naar administratie | Nog bouwen bij nieuwe aanvragen en opzeggingen. |
| Handtekening/toestemming | Nog afstemmen welke verklaring, bewijsregistratie en verificatie de opleiding verwacht. |
| Facturen en restitutie | Nog bouwen; jaartarieven zijn voorbereid, factuurbedragen worden nog niet berekend. |
| Events voor prijsberekening | Nog toevoegen met de definitieve factuurregels. De geldhelper is aanwezig. |
| Jeugdvoordeel in jaar van 18 worden | Nog toepassen in factuurregels, los van de actuele lidsoort. |
| Automatische overgang jeugd naar volwassen | Nog bepalen en implementeren; formulier valideert de leeftijd op moment van opslaan. |
| Volledige CRUD lidsoorten | Alleen lezen en bewerken aanwezig. Bespreek toevoegen/archiveren tegenover de eis van precies drie vaste soorten. |
| Zelfstandig kweeknummer verwijderen | Geen losse verwijderactie: verplichte koppeling blijft beschermd. Bespreek de gewenste lifecycle. |
| Ringnummer versus kweeknummer | Deze versie behandelt kweeknummer als NBvV-nummer; geen apart ringnummermodel. Bevestigen met docent. |
| Databaseafdwinging alle bedrijfsregels | Unieke waarden en relaties aanwezig; cross-table leeftijd/NBvV-regels nog niet volledig in database. |
| Bestaande data opschonen | Onbekende lidsoorten en ontbrekende nummers vereisen gerichte migratie na inspectie van de eigen database. |
| Uitvoering tests en MySQL-proef | Niet uitgevoerd: PHP/Composer ontbreken in de bewerkingsomgeving. Lokaal uitvoeren. |
| ERD-afbeelding, SQL-export, verslag, video | Nog maken voor de definitieve, draaiende versie. |

## Eerst verduidelijken: ingangsdatum

Voorbeeld 1 is consistent met **aanmelddatum + 21 dagen, daarna de eerste dag van de volgende maand**: 5 maart → 26 maart → 1 april, negen maanden contributie.

Voorbeeld 2 is tegenstrijdig: 25 maart + 21 dagen is **15 april**, niet 11 april. De eerste dag van de volgende maand is **1 mei**, en mei–december zijn acht maanden. De genoemde **1 juni** geeft zeven maanden. Laat de docent bevestigen welke datumregel geldt en of de opzeg-/restitutiedatum exact dezelfde regel gebruikt.

Voor aanvragen moet ook duidelijk worden of het kweeknummer al bij de publieke aanvraag bekend is of pas bij administratieve goedkeuring wordt toegewezen. Dit beïnvloedt quarantaine, validatie en de verplichte relaties.

## Verificatie in deze omgeving

- De bronbestanden zijn onderzocht; nieuwe routes, relaties, formulieren, transactiegrenzen en unieke databasekoppelingen zijn statisch nagelopen.
- Gebruikte Laravel-methoden zoals `withTrashed()` op resource-routes en `whereIn()` op validatieregels zijn tegen de meegeleverde frameworkbron gecontroleerd.
- Geen PHP-runtime beschikbaar: geen uitgevoerde PHPUnit-, migration-, Blade-render- of browsertest van de werkende applicatie.
- De ZIP is gecontroleerd op aanwezigheid van de broncode en uitsluiting van `.env`, databases, vendor, node_modules, runtimecaches en logs.

Deze notities zijn geen bewijs dat de acceptatiecriteria van de volledige schoolopdracht al zijn behaald.
