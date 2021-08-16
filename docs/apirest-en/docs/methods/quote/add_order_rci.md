# add_order_rci (PUT)

function used for RCI quotation

* Endpoint : ```rcibywta.com/apirest_v1/quote```

# Fields

```JSON
{
    "request" : "add_order_rci",
    "token" : "1234567890ABCDEF",
    "fecha_salida" : "01/01/2022",
    "fecha_llegada" : "08/01/2022",
    "id_plan" : "1672",
    "pais_destino" : "2", 
    "pais_origen" : "US",
    "moneda" : "usd",
    "pasajeros" : "1",
    "upgrade" : ["114"],
    "propertyid" : "56",
    "subscriberid" : "12",
    "relationid" : "23",
    "sequenceid" : "45",
    "documentos" : ["18928712"],
    "nacimientos" : ["01/01/1991"],
    "nombres" : ["Antonio"],
    "apellidos" : ["Nieves"],
    "correos" : ["email@example.com"],
    "telefonos" : ["521221121"],
    "condiciones_medicas" : ["ninguna"],
    "tipo_documentos" : "",
    "sexo" : ["M"],
    "lenguaje" : "spa",
    "consideraciones_generales" : "ninguna",
    "emision" : "1"
}
```

* The field "pasajeros" indicates the ammount of passengers, the fields where the input is surrounded by brackets [] depend on this field, i.e. if there are 3 passengers, then: 

```JSON
{
    "..." : "...",
    "nacimientos" : ["01/01/1991","02/02/1992","03/03/1993"],
    "..." : "..."
}
```

* The only exception to this rule is the "upgrades" field, which doesn't depends of the ammount of passengers

# Results

```JSON
{
    "status": "OK",
    "codigo": "RC-CDDFEA",
    "valor": 210.86,
    "costo": 96.48,
    "ruta": "https://rcibywta.com/app/reports/reporte_orderventas.php?codigo=RC-CDDFEA&selectLanguage=es&broker_sesion=375",
    "documento": "18928712",
    "referencia": "122345"
}
```

# Common Error Codes

* ```1005``` : Invalid Token 
* ```9061``` : Empty Token
* ```1003``` : Language not implemented
* ```4006``` : Invalid country ID
* ```6021``` : Empty Language
* ```6022``` : Empty plan
* ```1062``` : Invalid Birthday
* ```1095``` : Invalid upgrade
* ```6026``` : Empty amount of passengers
* ```6045``` : Order data already exists
* ```6027``` : Origin country empty
* ```6028``` : Destination territory empty
* ```6029``` : Empty date of departure
* ```6030``` : Empty date of arrival
* ```6034``` : Empty currency
* ```6035``` : Empty emission code
* ```6036``` : Empty contact name
* ```6037``` : All fields are empty
* ```2004``` : the departure date cannot be less than the current date
* ```1050``` : Invalid plan
* ```3030``` : Invalid date range
* ```1022``` : Invalid currency
* ```3153``` : Element conflict in request (Age of passenger exceeds plan age limit)
* ```3150``` :  Missing required element (an obligatory field is empty)
* ```4005``` : Passenger name is empty
* ```4006``` : Passenger document is empty
* ```4007``` : Passenger surname is empty
* ```5006``` : Medic conditions is empty
* ```4008``` : Passenger phone is emtpy
* ```1248``` : Amount of days less than allowed in the plan
* ```1247``` : Amount of days more than allowed in the plan
* ```1080``` : Invalid territory
* ```1081``` : Restricted territory
* ```5003``` : Error in ridership
* ```5005``` : Birthday required
* ```5009``` : Invalid passenger phone number
* ```4010``` : Invalid Beneficiary mail
* ```5012``` : Required passenger email
* ```1091``` : Restricted country
* ```9011``` : Exchange rate invalid