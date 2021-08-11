# get_terms (GET)

Returns the terms and conditions of the plan

* Endpoint : ```rcibywta.com/apirest_v1/information```

# Fields

```JSON
{
    "request" : "get_terms",
    "token" : "123456789ABC",
    "id_plan" : "1800",
    "language" : "spa"
}
```

# Results

```JSON
{
    "id": "1800",
    "name": "USD 20.000 L. Econ.",
    "description": "USD 20.000 L. Econ.",
    "terms": "fasttravelassistance.ilstechnik.com/app/admin/server/php/files/120181204031219.pdf"
}
```

# Common Error Codes

* ```6021``` : Empty language
* ```6022``` : Empty plan
* ```1050``` : Invalid plan
* ```4012``` : Conditions not found
* ```1030``` : Language not implemented