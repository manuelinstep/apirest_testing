# exchange_rate (GET)

Returns the exchange rate of a specified country

* Endpoint : ```rcibywta.com/apirest_v1/information```

# Fields

```JSON
{
    "request" : "exchange_rate",
    "token" : "123456789ABC",
    "iso_country" : "VE"
}
```

# Results

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

# Common Error Codes

* ```5013``` : Change rate doesn't exist