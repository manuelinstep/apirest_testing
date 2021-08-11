# get_regions (GET)

Retorna todas las regiones guardadas en el sistema

* Endpoint : ```rcibywta.com/apirest_v1/information```

# Campos

```JSON
{
    "request" : "get_regions",
    "token" : "123456789ABC"
}
```

# Resultados

```JSON
[
    {
        "id_territory": "1",
        "desc_small": "WorldWide"
    },
    {
        "id_territory": "2",
        "desc_small": "Europe"
    },
    {
        "id_territory": "9",
        "desc_small": "Local"
    }
]
```

# Errores Comunes retornados por la función

* ```1005``` : Token invalido