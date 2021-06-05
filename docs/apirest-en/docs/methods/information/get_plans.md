# get_plans (GET)

Returns all the plans associated with the user's agency

* Endpoint : ```fasttravelassistance.ilstechnik.com/apirest_testing/information```

# Fields

```JSON
{
    "request" : "get_plans",
    "token" : "123456789ABC",
    "language" : "spa"
}
```

# Results

```JSON
[
    {
        "id" : "1840",
        "titulo" : "titulo",
        "description" : "description",
        "language_id" : "spa",
        "plan_id" : "1840",
        "id_plan_categoria" : "24",
        "num_pas" : "9",
        "min_tiempo" : "1",
        "max_tiempo" : "90",
        "id_currence" : "31",
        "family_plan" : "N",
        "min_age" : "1",
        "max_age" : "90",
        "normal_age" : "21",
        "plan_local" : "N",
        "modo_plan" : "W",
        "original_id" : null
    }
]
```

# Common Error Codes

* ```1005``` : Invalid Token
* ```9015``` : No Results
* ```6021``` : Empty language