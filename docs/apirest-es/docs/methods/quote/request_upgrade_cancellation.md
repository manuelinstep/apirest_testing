# request_upgrade_cancellation (PUT)

Cancela la orden especificada

* Endpoint : ```rcibywta.com/apirest_v1/quote```

# Campos

```JSON
{
    "request" : "request_cancellation",
    "token" : "123456789ABC",
    "codigo" : "FT-K21JDD",
    "upgrade" : "126"
}
```

# Resultados

```JSON
{
    "voucher": "FT-K21JDD",
    "valor_descuento": "55.720",
    "pricer_order": 618.48
}
```

# Errores comunes retornados por la función

* ```6023``` : Voucher vacío
* ```6039``` : Upgrade vacío
* ```6037``` : Todos los campos estan vacíos
* ```1021``` : Voucher inactivo
* ```1020``` : Voucher no encontrado