# get_pvp_price (GET)

Retorna los precios de un plan basado en el país especificado

* Endpoint : ```fasttravelassistance.ilstechnik.com/apirest_testing/information```

# Campos

```JSON
{
    "request" : "get_currencies",
    "token" : "123456789ABC",
    "id_plan" : "1234",
    "iso_country" : "CL"
}
```

* El campo "iso_country" puede dejarse vacío, esto retornara todos los precios del plan sin especificar un país

# Resultados

```JSON
[
    {
        "unidad": "dias",
        "tiempo": "20",
        "valor": "2.70"
    },
    {
        "unidad": "dias",
        "tiempo": "40",
        "valor": "2.42"
    },
    {
        "unidad": "dias",
        "tiempo": "60",
        "valor": "2.10"
    },
    {
        "unidad": "dias",
        "tiempo": "90",
        "valor": "1.80"
    },
    {
        "unidad": "dias",
        "tiempo": "1",
        "valor": "2.54"
    }
]
```

# Errores Comunes retornados por la función

* ```1091``` : El plan está restringido para este país
* ```1060``` : No hay precio registrado para este país
* ```6022``` : Plan Vacio