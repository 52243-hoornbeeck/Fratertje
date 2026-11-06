# Oplevering ledenadministratie - 6 november 2026

De afgesproken checklist omvat login, afgeschermde administratie, leden-CRUD, drie standaardlidsoorten met CRUD, kweeknummer-CRUD, Nederlandse validatie, zoeken/filteren, bevestiging en softdeletes/archief/herstel.

## Aanpassingen

- Volledige lidsoort-CRUD met een nieuw softdeleteveld. Soorten met gekoppelde leden kunnen niet worden gearchiveerd, inclusief koppelingen met gearchiveerde leden. Namen blijven uniek via validatie; codes houden de leeftijdsregels stabiel.
- Zelfstandige kweeknummerformulieren en detailpagina's met unieke nummers, een vaste koppeling met het lid, archiveren en herstellen. Ook gearchiveerde nummers blijven gereserveerd.
- Een actief lid vereist altijd een nummer en uitgiftejaar, zonder uitzondering voor gastleden of nieuwe lidsoorten. Nummerarchivering zet het lid atomair op inactief. Een nummer of lid herstellen kan nooit een actief lid zonder nummer opleveren.
- Zichtbare bevestigingsdialogen, inclusief annuleren; gegevens worden alleen zacht verwijderd.
- Nieuwe regressietests voor bovenstaande functies; bestaande tests zijn aangepast aan de expliciet gewijzigde gastlidregel.

## Gegevens en installatie

De oorspronkelijke migrations en gegevens blijven behouden. De nieuwe migration voegt alleen `member_types.deleted_at` toe. Geen bestaande database wordt gereset. De README bevat de installatie en het uitsluitend lokale testaccount. Geheimen worden niet in video of verslag herhaald.

## Verificatie

De tests draaien in een tijdelijke SQLite-database in het geheugen. De demonstratie gebruikt een aparte database met fictieve leden en echte browserhandelingen. Concrete aantallen en bestandsgegevens staan bij het uiteindelijke demonstratieverslag en de opgeslagen testuitvoer.

## Reikwijdte

Facturen, publieke digitale aan- en afmelding en automatische leeftijdsovergangen behoren niet tot de nu afgesproken checklist en zijn niet toegevoegd. Er is geen aparte ringnummertabel: het NBvV-kweeknummer is het nummer uit de checklist. Een lid heeft een adres en maximaal een huidig of gearchiveerd kweeknummer; nummerwijzigingen worden in dezelfde rij opgeslagen. Er is geen permanente verwijderfunctie.
