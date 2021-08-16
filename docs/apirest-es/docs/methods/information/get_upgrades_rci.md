# get_upgrades_rci (GET)

Retorna diferentes resultados dependiendo del type especificado
1- Retorna upgrades asociados con la orden especificada
2- Retorna todos los upgrades disponibles para la orden especificada basado en el plan de la orden

* Endpoint : ```rcibywta.com/apirest_v1/information```

# Campos

```JSON
{
    "request" : "get_upgrades_rci",
    "token" : "1234567890ABCDEF",
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

* ```1020``` : Token invalido
* ```1021``` : Voucher no activo
* ```1030``` : Lenguaje no implementado
* ```4444``` : Filtro invalido
* ```4445``` : No hay upgrades asociados
* ```4446``` : No hay upgrades disponibles
* ```6021``` : Lenguaje vacio
* ```6023``` : Voucher vacio
* ```6037``` : Todos los campos estan vacios