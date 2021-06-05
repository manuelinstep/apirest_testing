# get_country_states_cities (GET)

Returns the city of a specified state in a country

* Endpoint : ```fasttravelassistance.ilstechnik.com/apirest_testing/information```

# Fields

```JSON
{
    "request" : "get_currencies",
    "token" : "123456789ABC",
    "iso_country" : "CO",
    "iso_state" : "CO.14",
    "language" : "spa"
}
```

# Results

```JSON
[
    {
        "countries_description": "Colombia",
        "cities_description": "Calamar",
        "iso_city": "3687975",
        "states_description": "Departamento del Guaviare",
        "iso_state": "CO.14"
    },
    {
        "countries_description": "Colombia",
        "cities_description": "El Retorno",
        "iso_city": "3792387",
        "states_description": "Departamento del Guaviare",
        "iso_state": "CO.14"
    },
    {
        "countries_description": "Colombia",
        "cities_description": "Miraflores",
        "iso_city": "3674740",
        "states_description": "Departamento del Guaviare",
        "iso_state": "CO.14"
    },
    {
        "countries_description": "Colombia",
        "cities_description": "San Jose del Guaviare",
        "iso_city": "3828545",
        "states_description": "Departamento del Guaviare",
        "iso_state": "CO.14"
    }
]
```

# Common Error Codes

* ```1030``` : Language not implemented
* ```6021``` : Empty language
* ```9015``` : No results
* ```9173``` : Invalid Iso Country
* ```9174``` : Empty Iso Country
* ```9175``` : Empty Country states
* ```9176``` : Empty Iso states
* ```9177``` : Invalid Iso state