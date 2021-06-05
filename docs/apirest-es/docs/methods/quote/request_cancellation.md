# request_cancellation (PUT)

Cancela la orden especificada

* Endpoint : ```fasttravelassistance.ilstechnik.com/apirest_testing/quote```

# Campos

```JSON
{
    "request" : "request_cancellation",
    "token" : "123456789ABC",
    "codigo" : "FT-K21JDD",
    "notificar" : "1"
}
```

# Resultados

```JSON
{
    "status": "OK"
}
```

# Errores comunes retornados por la función

* ```6037``` : Todos los campos estan vacíos
* ```4001``` : Increased cancellation date for departure
* ```4050``` : Notificación invalida
* ```1020``` : Voucher no encontrado
* ```1021``` : Voucher inactivo
* ```3220``` : Operación no autorizada
* ```3051``` : Usuario no asociado a esta agencia