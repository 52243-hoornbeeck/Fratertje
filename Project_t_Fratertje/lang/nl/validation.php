<?php
return [
    'required' => 'Het veld :attribute is verplicht.',
    'string' => 'Het veld :attribute moet tekst zijn.',
    'email' => 'Vul een geldig e-mailadres in.',
    'integer' => 'Het veld :attribute moet een geheel getal zijn.',
    'boolean' => 'Kies een geldige waarde voor :attribute.',
    'date_format' => 'Vul :attribute in als een geldige datum (jaar-maand-dag).',
    'before_or_equal' => 'Het veld :attribute mag niet in de toekomst liggen.',
    'exists' => 'De geselecteerde waarde voor :attribute bestaat niet.',
    'in' => 'Kies een geldige waarde voor :attribute.',
    'unique' => 'Deze waarde voor :attribute is al in gebruik, mogelijk in het archief.',
    'regex' => 'Het formaat van :attribute is ongeldig.',
    'max' => ['string' => 'Het veld :attribute mag maximaal :max tekens bevatten.'],
    'between' => ['numeric' => 'Het veld :attribute moet tussen :min en :max liggen.'],
    'attributes' => [
        'first_name' => 'voornaam', 'last_name' => 'achternaam', 'birth_date' => 'geboortedatum',
        'member_type_id' => 'lidsoort', 'street' => 'straat', 'house_number' => 'huisnummer',
        'postal_code' => 'postcode', 'city' => 'woonplaats', 'breeding_number' => 'kweeknummer',
        'issue_year' => 'uitgiftejaar', 'is_active' => 'actieve status', 'email' => 'e-mailadres',
        'password' => 'wachtwoord', 'name' => 'naam', 'description' => 'omschrijving', 'price' => 'jaarprijs',
    ],
];
