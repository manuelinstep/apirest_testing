# request_cancellation (PUT)

Cancels the specified order

* Endpoint : ```rcibywta.com/apirest_v1/quote```

# Fields

```JSON
{
    "request" : "request_cancellation",
    "token" : "123456789ABC",
    "codigo" : "FT-K21JDD",
    "notificar" : "1"
}
```

# Results

```JSON
{
    "status": "OK"
}
```

# Common Error Codes

* ```6037``` : All fields are empty
* ```4001``` : Increased cancellation date for departure
* ```4050``` : Invalid notificaction
* ```1020``` : Voucher not found
* ```1021``` : Inactive voucher
* ```3220``` : Unauthorized operation
* ```3051``` : User not associated to an agency