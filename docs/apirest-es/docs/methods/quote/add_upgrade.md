# add_upgrade (PUT)

Añade un upgrade a la orden especificada

* Endpoint : ```fasttravelassistance.ilstechnik.com/apirest_testing/quote```

# Campos

```JSON
{
    "request" : "add_upgrade",
    "token" : "123456789ABC",
    "codigo" : "FT-K21JDD",
    "upgrade" : "126"
}
```

# Resultados

```JSON
{
    "voucher": "FT-K21JDD",
    "valor_adicional": 55.72,
    "upgrades": [
        "126"
    ]
}
```

# Errores comunes retornados por la función

* ```6023``` : Voucher vacío
* ```6039``` : Upgrade vacío
* ```1021``` : Voucher inactivo
* ```1020``` : No se encontró el voucher