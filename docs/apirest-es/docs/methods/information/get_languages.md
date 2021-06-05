# get_languages (GET)

Retorna todos los lenguajes disponibles en la plataforma

* Endpoint : ```fasttravelassistance.ilstechnik.com/apirest_testing/information```

# Campos

```JSON
{
    "request" : "get_languages",
    "token" : "123456789ABC"
}
```

# Resultados

```JSON
[
    {
        "id": "141",
        "lg_id": "eng",
        "name": "English",
        "short_name": "en"
    },
    {
        "id": "397",
        "lg_id": "por",
        "name": "Portuguese",
        "short_name": "pt"
    },
    {
        "id": "456",
        "lg_id": "spa",
        "name": "Español",
        "short_name": "es"
    }
]
```

# Errores Comunes retornados por la función

* ```1005``` : Token invalido
* ```6020``` : Token vacio