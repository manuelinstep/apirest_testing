# get_voucher_rci (GET)

Returns a voucher specified by the subscriber's id

* Endpoint : ```rcibywta.com/apirest_v1/information```

# Fields

```JSON
{
    "request" : "get_voucher_rci",
    "token" : "4E4HMJA9GBHE4BA7",
    "sucriber_id" : "67170060500030001",
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

* ```1005``` : Invalid Token
* ```1030``` : Language not implemented
* ```4444``` : Invalid Type
* ```5101``` : Empty subscriber ID
* ```6020``` : Empty Token
* ```6021``` : Empty language
* ```6035``` : Empty emission code
* ```6037``` : All the fields are empty
* ```6060``` : No vouchers canceled
* ```6061``` : No active vouchers
* ```6062``` : No pending vouchers
* ```6063``` : No vouchers