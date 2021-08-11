# get_languages (GET)

Returns all the languages avaible in the platform

* Endpoint : ```rcibywta.com/apirest_v1/information```

# Fields

```JSON
{
    "request" : "get_languages",
    "token" : "123456789ABC"
}
```

# Results

```JSON
[
    {
        "id": "141",
        "lg_id": "eng",
        "name": "English",
        "short_name": "en"
    },
    {
        "id": "397",
        "lg_id": "por",
        "name": "Portuguese",
        "short_name": "pt"
    },
    {
        "id": "456",
        "lg_id": "spa",
        "name": "Español",
        "short_name": "es"
    }
]
```

# Common Error Codes

* ```1005``` : Invalid Token
* ```6020``` : Empty Token