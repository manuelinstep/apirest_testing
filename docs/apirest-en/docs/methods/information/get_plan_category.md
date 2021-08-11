# get_plan_category (GET)

Returns all plan categories

* Endpoint : ```rcibywta.com/apirest_v1/information```

# Fields

```JSON
{
    "request" : "get_plan_category",
    "token" : "123456789ABC",
    "language" : "spa"
}
```

# Results

```JSON
[
    {
        "name_plan": "Larga Estadia",
        "id_plan_categoria": "22"
    },
    {
        "name_plan": "Anuales - Multiviajes",
        "id_plan_categoria": "23"
    },
    {
        "name_plan": "Viajes por día",
        "id_plan_categoria": "24"
    },
    {
        "name_plan": "Estudiantil",
        "id_plan_categoria": "27"
    },
    {
        "name_plan": "Catpruebaesp",
        "id_plan_categoria": "28"
    }
]
```

# Common Error Codes

* ```6021``` : Empty language
* ```1030``` : Language not implemented
* ```1040``` : There's no plan category