# get_plans (GET)

Retorna todos los planes asociados con la agencia del usuario

* Endpoint : ```fasttravelassistance.ilstechnik.com/apirest_testing/information```

# Campos

```JSON
{
    "request" : "get_plans",
    "token" : "123456789ABC",
    "language" : "spa"
}
```

# Resultados

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

# Errores Comunes retornados por la función

* ```1005``` : Token invalido
* ```9015``` : No hay resultados
* ```6021``` : Lenguaje vacío