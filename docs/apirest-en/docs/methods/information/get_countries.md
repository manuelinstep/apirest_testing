# get_countries (GET)

Returns all the currently stored countries, depending on the language, currently supports spanish (spa), or english (eng)

* Endpoint : ```rcibywta.com/apirest_v1/information```

# Fields

```JSON
{
    "request" : "get_countries",
    "token" : "123456789ABC",
    "language" : "spa"
}
```

# Results

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

# Common Error Codes

* ```1030``` : Language not implemented
* ```1005``` : Invalid Token