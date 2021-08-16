# get_order_price_edad_plan (GET)

Gets different plans based on the ages provided

* Endpoint : ```rcibywta.com/apirest_v1/information```

# Fields

```JSON
{
    "request" : "get_order_price_edad_plan",
    "token" : "1234567890ABCDEF",
    "id_plan" : "1703",
    "pais_origen" : "AR",
    "territorio_destino" : "2",
    "fecha_salida" : "01/01/2022",
    "fecha_llegada" : "08/01/2022",
    "pasajeros" : "2",
    "edad" : ["42","43"]
}
```

# Results

```JSON
{
    "total_orden": "109.760",
    "idplan": "1703",
    "fecha_salida": "01/01/2022",
    "fecha_regreso": "08/01/2022",
    "dias": "8",
    "edades": [
        "42",
        "43"
    ],
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
```

# Common Error Codes

* ```1005``` : Invalid Token
* ```1050``` : Plan not valid
* ```1060``` : No price for selection
* ```1062``` : Invalid birthday
* ```1080``` : Invalid territory
* ```1081``` : restricted territory
* ```1090``` : Invalid country ID
* ```1091``` : Restricted country
* ```1100``` : Invalid ID agency
* ```1247``` : Days Exceeding
* ```1248``` : Undersized days in the plan
* ```2000``` : Invalid date (NULL)
* ```2001``` : Invalid date from
* ```2002``` : Invalid date to
* ```2004``` : Date from earlier than system date
* ```3020``` : Invalid date
* ```3030``` : Invalid date range
* ```3150``` : Missing required element
* ```5003``` : Number of passengers greater than permitted
* ```5005``` : Birthday required
* ```5015``` : Age required
* ```5016``` : Invalid age
* ```5017``` : No upgrade
* ```6020``` : Empty Token
* ```6022``` : Empty plan
* ```6026``` : Amount of empty passengers
* ```6027``` : Country of empty origin
* ```6028``` : Destination territory empty
* ```6029``` : Empty date of departure
* ```6030``` : return date empty
* ```6037``` : All the fields are empty
* ```6038``` : Empty passenger age
* ```6042``` : Amount of passengers minor at ages provided