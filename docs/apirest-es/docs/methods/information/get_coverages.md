# get_coverages (GET)

Retorna todas las coberturas de un plan

* Endpoint : ```rcibywta.com/apirest_v1/information```

# Campos

```JSON
{
    "request" : "get_coverages",
    "token" : "123456789ABC",
    "language" : "spa",
    "id_plan" : "1840" 
}
```

# Resultados

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

# Errores Comunes retornados por la función

* ```6021``` : Lenguaje vacio
* ```6022``` : Plan vacio
* ```6037``` : Todos los campos estan vacios
* ```1030``` : Lenguaje no implementado
* ```1050``` : Plan invalido