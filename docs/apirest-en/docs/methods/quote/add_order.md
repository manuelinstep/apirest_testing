# add_order (PUT)

function used for quotation

* Endpoint : ```fasttravelassistance.ilstechnik.com/apirest_testing/quote```

# Fields

```JSON
{
    "request" : "add_order",
    "token" : "123456789ABC",
    "fecha_salida" : "01/01/2022",
    "fecha_llegada" : "02/02/2022",
    "referencia" : "REFEXAMPLE1234",
    "id_plan" : "1835",
    "pais_destino" : "1",
    "pais_origen" : "AR",
    "moneda" : "usd",
    "tasa_cambio" : "1",
    "pasajeros" : "1",
    "upgrades" : ["123"],
    "nacimientos" : ["01/01/1991"],
    "documentos" : ["12344321"],
    "nombres" : ["Rodrigo"],
    "apellidos" : ["Aristigueta"],
    "telefonos" : ["5753544243"],
    "correos" : ["correo@cliente.com"],
    "condiciones_med" : ["ninguna"],
    "nombre_contacto" : "Alfredo Rodriguez",
    "telefono_contacto" : "123455432",
    "email_contacto" : "correo@contacto.com",
    "consideraciones_generales" : "ninguna",
    "emision" : "1",
    "lenguaje" : "spa"
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
    "codigo": "FT-2J4I74",
    "valor": "77.440",
    "ruta": "https://fasttravelassistance.ilstechnik.com/app/reports/reporte_orderventas.php?codigo=-2J4I74&selectLanguage=es&broker_sesion=2267",
    "documento": "0986867671",
    "referencia": "1244232",
    "El valor de cambio fue ajustado a:": "5,000.00"
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