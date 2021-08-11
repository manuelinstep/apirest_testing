# get_terms (GET)

Retorna los términos y condiciones del plan

* Endpoint : ```rcibywta.com/apirest_v1/information```

# Campos

```JSON
{
    "request" : "get_currencies",
    "token" : "123456789ABC",
    "id_plan" : "1800",
    "language" : "spa"
}
```

# Resultados

```JSON
{
    "id": "1800",
    "name": "USD 20.000 L. Econ.",
    "description": "USD 20.000 L. Econ.",
    "terms": "fasttravelassistance.ilstechnik.com/app/admin/server/php/files/120181204031219.pdf"
}
```

# Errores Comunes retornados por la función

* ```6021``` : Lenguaje vacío
* ```6022``` : Plan Vacio
* ```1050``` : Invalid plan
* ```4012``` : Conditions not found
* ```1030``` : Lenguaje no implementado