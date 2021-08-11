# get_upgrades_rci (GET)

Retorna diferentes resultados dependiendo del type especificado
1- Retorna upgrades asociados con la orden especificada
2- Retorna todos los upgrades disponibles para la orden especificada basado en el plan de la orden

* Endpoint : ```rcibywta.com/apirest_v1/information```

# Campos

```JSON
{
    "request" : "get_upgrades_rci",
    "token" : "4E4HMJA9GBHE4BA7",
    "code" : "RC-MM7HAG",
    "type" : "1",
    "lenguaje" : "spa"
}
```

# Resultados

```JSON
[
    {
        "code": "RC-2D612J",
        "departure": "2017-08-13",
        "return": "2017-08-20",
        "status": "Expired",
        "completion_status": "pending",
        "product": "1523",
        "property_id": "6635",
        "upgrades": false
    }
]
```

# Errores Comunes retornados por la función

* ```1020``` : Invalid Token
* ```1021``` : Voucher not active
* ```1030``` : Language not implemented
* ```4444``` : Invalid Filter
* ```4445``` : No upgrades associated
* ```4446``` : No upgrades avaible
* ```6021``` : Empty language
* ```6023``` : Empty voucher
* ```6037``` : All the fields are empty