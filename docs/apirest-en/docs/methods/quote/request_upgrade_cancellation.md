# request_upgrade_cancellation (PUT)

Cancels the specified order

* Endpoint : ```rcibywta.com/apirest_v1/quote```

# Fields

```JSON
{
    "request" : "request_upgrade_cancellation",
    "token" : "123456789ABC",
    "codigo" : "FT-K21JDD",
    "upgrade" : "126"
}
```

# Results

```JSON
{
    "voucher": "FT-K21JDD",
    "valor_descuento": "55.720",
    "pricer_order": 618.48
}
```

# Common Error Codes

* ```6023``` : Empty voucher 
* ```6039``` : Empty upgrade 
* ```6037``` : All the fields are empty 
* ```1021``` : Inactive Voucher
* ```1020``` : Voucher not found