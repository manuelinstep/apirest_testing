# get_voucher_rci (GET)

Retorna un voucher especificado por el ID del suscriptor

* Endpoint : ```rcibywta.com/apirest_v1/information```

# Campos

```JSON
{
    "request" : "get_voucher_rci",
    "token" : "4E4HMJA9GBHE4BA7",
    "sucriber_id" : "67170060500030001",
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

* ```1005``` : Token invalido
* ```1030``` : Lenguaje no implementado
* ```4444``` : Tipo invalido
* ```5101``` : Id del subscriptor vacio
* ```6020``` : Token vacio
* ```6021``` : Lenguaje vacio
* ```6035``` : Codigo de emision vacio
* ```6037``` : Todos los campos estan vacios
* ```6060``` : No hay vouchers cancelados
* ```6061``` : No hay vouchers activos
* ```6062``` : No hay vouchers pendientes
* ```6063``` : No hay vouchers