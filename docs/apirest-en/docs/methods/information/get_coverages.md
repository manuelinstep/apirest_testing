# get_coverages (GET)

Returns all of a plan coverages

* Endpoint : ```fasttravelassistance.ilstechnik.com/apirest_testing/information```

# Fields

```JSON
{
    "request" : "get_coverages",
    "token" : "123456789ABC",
    "language" : "spa",
    "id_plan" : "1840" 
}
```

# Results

```JSON
[
    {
        "valor_spa": "titulo",
        "valor_eng": "title",
        "id_benefit": "207",
        "name": "Asistencia médica por accidente",
        "language_id": "spa",
        "extended_info": "Asistencia médica por accidente"
    }
]
```

# Common Error Codes

* ```6021``` : Empty language
* ```6022``` : Empty plan
* ```6037``` : All the fields are empty
* ```1030``` : Language not implemented
* ```1050``` : Plan not valid