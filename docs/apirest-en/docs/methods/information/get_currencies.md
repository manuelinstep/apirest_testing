# get_currencies (GET)

Returns all the currencies stored in the database

* Endpoint : ```rcibywta.com/apirest_v1/information```

# Fields

```JSON
{
    "request" : "get_currencies",
    "token" : "123456789ABC"
}
```

# Results

```JSON
[
    {
        "id_country": "AD",
        "value_iso": "AFN",
        "desc_small": "Afghani"
    },
    {
        "id_country": "6",
        "value_iso": "ALL",
        "desc_small": "Lek"
    },
    {
        "id_country": "60",
        "value_iso": "DZD",
        "desc_small": "Algerian Dinar"
    },
    {
        "id_country": "12",
        "value_iso": "USD",
        "desc_small": "US Dollar"
    },
    {
        "id_country": "AD",
        "value_iso": "EUR",
        "desc_small": "Euro"
    },
    {
        "id_country": "9",
        "value_iso": "AOA",
        "desc_small": "Kwanza"
    },
    {
        "id_country": "5",
        "value_iso": "XCD",
        "desc_small": "East Caribbean Dollar"
    },
    {
        "id_country": "10",
        "value_iso": "N/A",
        "desc_small": "No universal currency"
    },
    {
        "id_country": "4",
        "value_iso": "XCD",
        "desc_small": "East Caribbean Dollar"
    },
    {
        "id_country": "VE",
        "value_iso": "VEF",
        "desc_small": "Bolivar Fuerte"
    },
    {
        "id_country": "DZ",
        "value_iso": "ADEZ",
        "desc_small": "prueba"
    },
    {
        "id_country": "AL",
        "value_iso": "AL",
        "desc_small": "testing"
    }
]
```

# Common Error Codes

* ```1005``` : Invalid Token
* ```9015``` : No results