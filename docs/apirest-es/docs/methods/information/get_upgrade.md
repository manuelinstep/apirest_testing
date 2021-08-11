# get_upgrade (GET)

Retorna la información del upgrade solicitado

* Endpoint : ```rcibywta.com/apirest_v1/information```

# Campos

```JSON
{
    "request" : "get_upgrade",
    "token" : "123456789ABC",
    "id_plan" : "1800",
    "language" : "spa"
}
```

# Resultados

```JSON
[
    {
        "type_raider": "Valor",
        "rd_calc_type": "Pasajero Especifico",
        "id_raider": "135",
        "cost_raider": "59.940",
        "name_raider": "USD 9,000 - Cancelación Multi-causa",
        "value_raider": "149.850"
    },
    {
        "type_raider": "Valor",
        "rd_calc_type": "Pasajero Especifico",
        "id_raider": "152",
        "cost_raider": "70.000",
        "name_raider": "USD 7,000 - Cancelación Multi causa Comp",
        "value_raider": "70.000"
    },
    {
        "type_raider": "Valor",
        "rd_calc_type": "Pasajero Especifico",
        "id_raider": "151",
        "cost_raider": "60.000",
        "name_raider": "USD 6,000 - Cancelación Multi Causa Comp",
        "value_raider": "60.000"
    },
    {
        "type_raider": "Valor",
        "rd_calc_type": "Pasajero Especifico",
        "id_raider": "132",
        "cost_raider": "39.960",
        "name_raider": "USD 6,000 - Cancelación Multi-causa",
        "value_raider": "99.900"
    },
    {
        "type_raider": "Porcentage %",
        "rd_calc_type": "Pasajero Especifico",
        "id_raider": "137",
        "cost_raider": "25.000",
        "name_raider": "Upgrade de futura Mamá (25% de la cobertura médica, sin exceder USD 10,000)",
        "value_raider": "25.000"
    }
]
```

# Errores Comunes retornados por la función

* ```6021``` : Lenguaje vacío
* ```6022``` : Plan Vacio
* ```1050``` : Plan invalido