# get_country_cities (GET)

Retorna las ciudades del país especificado

* Endpoint : ```rcibywta.com/apirest_v1/information```

# Campos

```JSON
{
    "request" : "get_country_cities",
    "token" : "123456789ABC",
    "iso_country" : "CO",
    "language" : "spa"
}
```

# Resultados

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

# Errores Comunes retornados por la función

* ```9173``` : Iso country invalido
* ```9174``` : Iso country vacio
* ```6021``` : Lenguaje vacío
* ```1030``` : Lenguaje no implementado