# country_restricted (GET)

Retorna los países restringidos por el plan

* Endpoint : ```fasttravelassistance.ilstechnik.com/apirest_testing/information```

# Campos

```JSON
{
    "request" : "get_currencies",
    "token" : "123456789ABC",
    "id_plan" : "1835",
    "language" : "spa"
}
```

# Resultados

```JSON
[
    {
        "iso_country": "AW",
        "description": "Aruba"
    },
    {
        "iso_country": "BO",
        "description": "Bolivia"
    },
    {
        "iso_country": "CL",
        "description": "Chile"
    },
    {
        "iso_country": "CR",
        "description": "Costa Rica"
    },
    {
        "iso_country": "CU",
        "description": "Cuba"
    },
    {
        "iso_country": "EC",
        "description": "Ecuador"
    },
    {
        "iso_country": "SV",
        "description": "El Salvador"
    },
    {
        "iso_country": "GT",
        "description": "Guatemala"
    },
    {
        "iso_country": "HN",
        "description": "Honduras"
    },
    {
        "iso_country": "MX",
        "description": "México"
    },
    {
        "iso_country": "AN",
        "description": "Antillas Holandesas"
    },
    {
        "iso_country": "NI",
        "description": "Nicaragua"
    },
    {
        "iso_country": "PA",
        "description": "Panamá"
    },
    {
        "iso_country": "PY",
        "description": "Paraguay"
    },
    {
        "iso_country": "PE",
        "description": "Perú"
    },
    {
        "iso_country": "UY",
        "description": "Uruguay"
    }
]
```

# Errores Comunes retornados por la función

* ```6021``` : Lenguaje vacío
* ```6022``` : Plan Vacio
* ```1050``` : Plan invalido