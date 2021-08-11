# get_countries (GET)

Retorna todos los paises guardados en el sistem en el idioma indicado. Los idiomas disponibles actualmente son español (spa) e inglés (eng)

* Endpoint : ```rcibywta.com/apirest_v1/information```

# Campos

```JSON
{
    "request" : "get_countries",
    "token" : "123456789ABC",
    "language" : "spa"
}
```

# Resultados

```JSON
[
    {
        "iso_country": "AD",
        "description": "Andorra"
    },
    {
        "iso_country": "AE",
        "description": "Emiratos Árabes Unidos"
    },
    {
        "iso_country": "AF",
        "description": "Afganistán"
    },
    {
        "iso_country": "AG",
        "description": "Antigua y Barbuda"
    },
    {
        "iso_country": "AI",
        "description": "Anguilla"
    },
    {
        "iso_country": "AL",
        "description": "Albania"
    }
]
```

# Errores Comunes retornados por la función

* ```1030``` : Lenguaje no implementado
* ```1005``` : Token invalido