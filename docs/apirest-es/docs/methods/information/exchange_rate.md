# exchange_rate (GET)

Retorna la tasa de cambio del país especificado

* Endpoint : ```fasttravelassistance.ilstechnik.com/apirest_testing/information```

# Campos

```JSON
{
    "request" : "exchange_rate",
    "token" : "123456789ABC",
    "iso_country" : "VE"
}
```

# Resultados

```JSON
[
    {
        "description": "Venezuela",
        "iso_country": "VE",
        "currencyname": "Bolivar",
        "usd_exchange": "5000"
    }
]
```

# Errores Comunes retornados por la función

* ```5013``` : La tasa de cambio no existe