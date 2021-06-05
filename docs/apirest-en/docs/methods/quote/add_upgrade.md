# add_upgrade (PUT)

Adds an upgrade to the specified order

* Endpoint : ```fasttravelassistance.ilstechnik.com/apirest_testing/quote```

# Fields

```JSON
{
    "request" : "add_upgrade",
    "token" : "123456789ABC",
    "codigo" : "FT-K21JDD",
    "upgrade" : "126"
}
```

# Results

```JSON
{
    "voucher": "FT-K21JDD",
    "valor_adicional": 55.72,
    "upgrades": [
        "126"
    ]
}
```

# Common Error Codes

* ```6023``` : Empty Voucher 
* ```6039``` : Empty Upgrade 
* ```1021``` : inactive Voucher
* ```1020``` : Voucher not found