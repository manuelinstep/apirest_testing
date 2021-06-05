# get_country_states (GET)

Retorna los estados del país especificado

* Endpoint : ```fasttravelassistance.ilstechnik.com/apirest_testing/information```

# Campos

```JSON
{
    "request" : "get_country_states",
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
        "states_description": "Atlantico",
        "iso_state": "CO.04"
    },
    {
        "countries_description": "Colombia",
        "states_description": "Bogota D.C.",
        "iso_state": "CO.34"
    },
    {
        "countries_description": "Colombia",
        "states_description": "Cundinamarca",
        "iso_state": "CO.33"
    }
]
```

# Errores Comunes retornados por la función

* ```1030``` : Lenguaje no implementado
* ```6021``` : Lenguaje vacío
* ```9015``` : No results
* ```9173``` : Invalid Iso Country
* ```9174``` : Empty Iso Country