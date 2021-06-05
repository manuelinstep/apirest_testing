# get_plan_category (GET)

Retorna todas las categorías de plan

* Endpoint : ```fasttravelassistance.ilstechnik.com/apirest_testing/information```

# Campos

```JSON
{
    "request" : "get_plan_category",
    "token" : "123456789ABC",
    "language" : "spa"
}
```

# Resultados

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

# Errores Comunes retornados por la función

* ```6021``` : Lenguaje vacío
* ```1030``` : Lenguaje no implementado
* ```1040``` : No hay categorías de plan