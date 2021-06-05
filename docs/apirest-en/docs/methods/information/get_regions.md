# get_regions (GET)

Returns all the currently stored regions

* Endpoint : ```fasttravelassistance.ilstechnik.com/apirest_testing/information```

# Fields

```JSON
{
    "request" : "get_regions",
    "token" : "123456789ABC"
}
```

# Results

```JSON
[
    {
        "id_territory": "1",
        "desc_small": "WorldWide"
    },
    {
        "id_territory": "2",
        "desc_small": "Europe"
    },
    {
        "id_territory": "9",
        "desc_small": "Local"
    }
]
```

# Common Error Codes

* ```1005``` : Invalid Token