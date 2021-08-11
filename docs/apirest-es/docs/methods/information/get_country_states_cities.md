# get_country_states_cities (GET)

Retorna las ciudades dentro del estado específico de un país

* Endpoint : ```rcibywta.com/apirest_v1/information```

# Campos

```JSON
{
    "request" : "get_currencies",
    "token" : "123456789ABC",
    "iso_country" : "CO",
    "iso_state" : "CO.14",
    "language" : "spa"
}
```

# Resultados

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

# Errores Comunes retornados por la función

* ```1030``` : Lenguaje no implementado
* ```6021``` : Lenguaje vacío
* ```9015``` : No hay resultados
* ```9173``` : Iso country invalido
* ```9174``` : Iso country vacio
* ```9175``` : Country states vacio
* ```9176``` : Iso states vacio
* ```9177``` : Iso states invalido