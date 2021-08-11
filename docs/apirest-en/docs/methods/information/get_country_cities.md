# get_country_cities (GET)

Returns the cities of the specified country

* Endpoint : ```rcibywta.com/apirest_v1/information```

# Fields

```JSON
{
    "request" : "get_country_cities",
    "token" : "123456789ABC",
    "iso_country" : "CO",
    "language" : "spa"
}
```

# Results

```JSON
[
    {
        "countries_description": "Colombia",
        "cities_description": "Baranoa",
        "iso_city": "3689235",
        "states_description": "Atlantico",
        "iso_state": "CO.04"
    },
    {
        "countries_description": "Colombia",
        "cities_description": "Barranquilla",
        "iso_city": "3689147",
        "states_description": "Atlantico",
        "iso_state": "CO.04"
    },
    {
        "countries_description": "Colombia",
        "cities_description": "Campo de la Cruz",
        "iso_city": "3687758",
        "states_description": "Atlantico",
        "iso_state": "CO.04"
    },
    {
        "countries_description": "Colombia",
        "cities_description": "Candelaria",
        "iso_city": "3687634",
        "states_description": "Atlantico",
        "iso_state": "CO.04"
    },
    {
        "countries_description": "Colombia",
        "cities_description": "Galapa",
        "iso_city": "3682238",
        "states_description": "Atlantico",
        "iso_state": "CO.04"
    },
    {
        "countries_description": "Colombia",
        "cities_description": "Juan de Acosta",
        "iso_city": "3680176",
        "states_description": "Atlantico",
        "iso_state": "CO.04"
    },
    {
        "countries_description": "Colombia",
        "cities_description": "Luruaco",
        "iso_city": "3675826",
        "states_description": "Atlantico",
        "iso_state": "CO.04"
    },
    {
        "countries_description": "Colombia",
        "cities_description": "Malambo",
        "iso_city": "3675595",
        "states_description": "Atlantico",
        "iso_state": "CO.04"
    },
    {
        "countries_description": "Colombia",
        "cities_description": "Manati",
        "iso_city": "3675512",
        "states_description": "Atlantico",
        "iso_state": "CO.04"
    },
    {
        "countries_description": "Colombia",
        "cities_description": "Palmar de Varela",
        "iso_city": "3673220",
        "states_description": "Atlantico",
        "iso_state": "CO.04"
    },
    {
        "countries_description": "Colombia",
        "cities_description": "Piojo",
        "iso_city": "3672176",
        "states_description": "Atlantico",
        "iso_state": "CO.04"
    }
]
```

# Common Error Codes

* ```9173``` : Invalid Iso country
* ```9174``` : Empty Iso country
* ```6021``` : Empty language
* ```1030``` : Language not implemented