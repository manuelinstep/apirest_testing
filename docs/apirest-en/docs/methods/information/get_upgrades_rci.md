# get_upgrades_rci (GET)

Returns different results based on the type requested
1- Returns upgrades asociated with the specified order
2- Returns all avaible upgrades for the specified order based on the order´s plan

* Endpoint : ```rcibywta.com/apirest_v1/information```

# Fields

```JSON
{
    "request" : "get_upgrades_rci",
    "token" : "4E4HMJA9GBHE4BA7",
    "code" : "RC-MM7HAG",
    "type" : "1",
    "lenguaje" : "spa"
}
```

# Results

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

# Common Error Codes

* ```1020``` : Invalid Token
* ```1021``` : Voucher not active
* ```1030``` : Language not implemented
* ```4444``` : Invalid Filter
* ```4445``` : No upgrades associated
* ```4446``` : No upgrades avaible
* ```6021``` : Empty language
* ```6023``` : Empty voucher
* ```6037``` : All the fields are empty