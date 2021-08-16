# get_order_price_fecha (GET)

Gets plans and their upgrades based on the provided birthday
Retorna planes y sus respectivos upgrades dependiendo de la fecha de nacimiento especificada

* Endpoint : ```rcibywta.com/apirest_v1/information```

# Campos

```JSON
{
    "request" : "get_order_price_fecha",
    "token" : "1234567890ABCDEF",
    "pais_origen" : "AR",
    "territorio_destino" : "2",
    "fecha_salida" : "01/01/2022",
    "fecha_llegada" : "08/01/2022",
    "pasajeros" : "2",
    "fecha_nacimiento" : ["01/01/1991","02/02/1992"]
}
```

# Resultados

```JSON
{
    "fecha_salida": "01/01/2022",
    "fecha_regreso": "08/01/2022",
    "dias": "8",
    "nacimientos": [
        "01/01/1991",
        "02/02/1992"
    ],
    "planes": {
        "1703": {
            "name": "nuevaprueba",
            "total_orden": "109.760",
            "upgrade": [
                {
                    "id_raider": "114",
                    "name_raider": "USD 250,000 en asistencia medica (dividido entre los 8 integrantes de la reserva x 2 semanas)",
                    "type_raider": "1",
                    "value_raider": "204.00",
                    "cost_raider": "89.62",
                    "rd_calc_type": "1",
                    "price_upgrade": "204.00",
                    "costo_upgrade": "89.62"
                },
                {
                    "id_raider": "70",
                    "name_raider": "USD 250,000 en asistencia medica (dividido entre los 8 integrantes de la reserva x 1 semana)",
                    "type_raider": "1",
                    "value_raider": "59.40",
                    "cost_raider": "59.40",
                    "rd_calc_type": "1",
                    "price_upgrade": "59.40",
                    "costo_upgrade": "59.40"
                },
                {
                    "id_raider": "44",
                    "name_raider": "USD 250,000  en asistencia medica (dividido entre los 4 integrantes de la reserva x 1 semana)",
                    "type_raider": "1",
                    "value_raider": "29.70",
                    "cost_raider": "29.70",
                    "rd_calc_type": "1",
                    "price_upgrade": "29.70",
                    "costo_upgrade": "29.70"
                },
                {
                    "id_raider": "47",
                    "name_raider": "Dias adicionales: hasta 8 dias mas por 4 pasajeros",
                    "type_raider": "1",
                    "value_raider": "7.55",
                    "cost_raider": "7.55",
                    "rd_calc_type": "1",
                    "price_upgrade": "7.55",
                    "costo_upgrade": "7.55"
                },
                {
                    "id_raider": "50",
                    "name_raider": "4 pasajeros mas por 8 dias, la cobertura sera de USD 12,000 no acumulativos",
                    "type_raider": "1",
                    "value_raider": "7.55",
                    "cost_raider": "7.55",
                    "rd_calc_type": "1",
                    "price_upgrade": "7.55",
                    "costo_upgrade": "7.55"
                },
                {
                    "id_raider": "59",
                    "name_raider": "USD 1,000 - Cancelacion Cualquier Motivo",
                    "type_raider": "1",
                    "value_raider": "7.33",
                    "cost_raider": "7.33",
                    "rd_calc_type": "1",
                    "price_upgrade": "7.33",
                    "costo_upgrade": "7.33"
                },
                {
                    "id_raider": "60",
                    "name_raider": "USD 2,000 - Cancelacion Cualquier Motivo",
                    "type_raider": "1",
                    "value_raider": "14.62",
                    "cost_raider": "14.62",
                    "rd_calc_type": "1",
                    "price_upgrade": "14.62",
                    "costo_upgrade": "14.62"
                },
                {
                    "id_raider": "61",
                    "name_raider": "USD 3,000 - Cancelacion Cualquier Motivo",
                    "type_raider": "1",
                    "value_raider": "21.93",
                    "cost_raider": "21.93",
                    "rd_calc_type": "1",
                    "price_upgrade": "21.93",
                    "costo_upgrade": "21.93"
                },
                {
                    "id_raider": "62",
                    "name_raider": "USD 4,000 - Cancelacion Cualquier Motivo",
                    "type_raider": "1",
                    "value_raider": "29.23",
                    "cost_raider": "29.23",
                    "rd_calc_type": "1",
                    "price_upgrade": "29.23",
                    "costo_upgrade": "29.23"
                },
                {
                    "id_raider": "63",
                    "name_raider": "USD 5,000 - Cancelacion Cualquier Motivo",
                    "type_raider": "1",
                    "value_raider": "36.54",
                    "cost_raider": "36.54",
                    "rd_calc_type": "1",
                    "price_upgrade": "36.54",
                    "costo_upgrade": "36.54"
                },
                {
                    "id_raider": "76",
                    "name_raider": "USD 250,000  en asistencia medica (dividido entre los 4 integrantes de la reserva x 2 semanas)",
                    "type_raider": "1",
                    "value_raider": "66.95",
                    "cost_raider": "66.95",
                    "rd_calc_type": "1",
                    "price_upgrade": "66.95",
                    "costo_upgrade": "66.95"
                },
                {
                    "id_raider": "108",
                    "name_raider": "Dias adicionales: hasta 8 dias mas por 8 pasajeros",
                    "type_raider": "1",
                    "value_raider": "15.10",
                    "cost_raider": "15.10",
                    "rd_calc_type": "1",
                    "price_upgrade": "15.10",
                    "costo_upgrade": "15.10"
                }
            ]
        }
    }
}
```

# Errores Comunes retornados por la funcioón

* ```1005``` : Token Invalido
* ```1050``` : Plan no valido
* ```1060``` : No hay precio para la selección
* ```1062``` : Fecha de nacimiento invalida
* ```1080``` : Territorio invalido
* ```1081``` : Territorio restringido
* ```1090``` : ID del pais es invalido
* ```1091``` : Pais restringido
* ```1100``` : Id de la agencia es invalido
* ```2000``` : Fecha invalida (NULL)
* ```2001``` : Fecha inicial invalida
* ```2002``` : Fecha final invalida
* ```2004``` : Fecha anterior a la fecha del sistema
* ```2010``` : Hace falta el id de propiedad
* ```3020``` : Fecha invalida
* ```3030``` : Rngo de la fecha invalido
* ```3150``` : Hace falta un elemento requerido
* ```5003``` : Numero de pasajeros mayor al permitido
* ```5005``` : Fecha de nacimiento requerida
* ```5017``` : No upgrades
* ```6020``` : Token vacio
* ```6026``` : Cantidad de pasajeros vacia
* ```6027``` : Pais de origen vacio
* ```6028``` : Territorio de destino vacio
* ```6029``` : Fecha de salida vacia
* ```6030``` : Fecha de retorno vacia
* ```6031``` : Fecha de nacimiento vacia
* ```6037``` : Todos los campos estan vacios
* ```6043``` : Numero de pasajeros menor a las fechas de nacimiento provistas