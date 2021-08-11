# get_pvp_price (GET)

Gets price from a plan based on the country specified

* Endpoint : ```rcibywta.com/apirest_v1/information```

# Fields

```JSON
{
    "request" : "get_pvp_price",
    "token" : "123456789ABC",
    "id_plan" : "1234",
    "iso_country" : "CL"
}
```

WARNING: The field "iso_country" can be left empty, this will return all the prices of the plan without regarding a specific country

# Results

```JSON
[
    {
        "unidad": "dias",
        "tiempo": "20",
        "valor": "2.70"
    },
    {
        "unidad": "dias",
        "tiempo": "40",
        "valor": "2.42"
    },
    {
        "unidad": "dias",
        "tiempo": "60",
        "valor": "2.10"
    },
    {
        "unidad": "dias",
        "tiempo": "90",
        "valor": "1.80"
    },
    {
        "unidad": "dias",
        "tiempo": "1",
        "valor": "2.54"
    }
]
```

# Common Error Codes

* ```1091``` : The country provided is restricted for the plan
* ```1060``` : Price not registered for this country
* ```6022``` : Empty plan