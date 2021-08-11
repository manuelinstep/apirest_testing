# get_country_states (GET)

Returns the states of the specified country

* Endpoint : ```rcibywta.com/apirest_v1/information```

# Fields

```JSON
{
    "request" : "get_country_states",
    "token" : "123456789ABC",
    "iso_country" : "CO",
    "language" : "spa"
}
```

# Results

```JSON
[
    {
        "countries_description": "Colombia",
        "states_description": "Atlantico",
        "iso_state": "CO.04"
    },
    {
        "countries_description": "Colombia",
        "states_description": "Bogota D.C.",
        "iso_state": "CO.34"
    },
    {
        "countries_description": "Colombia",
        "states_description": "Cundinamarca",
        "iso_state": "CO.33"
    }
]
```

# Common Error Codes

* ```1030``` : Language not implemented
* ```6021``` : Empty language
* ```9015``` : No results
* ```9173``` : Invalid Iso Country
* ```9174``` : Empty Iso Country