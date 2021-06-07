# report_order_master (PUT)

Reports an order

* Endpoint : ```fasttravelassistance.ilstechnik.com/apirest_testing/quote```

# Fields

```JSON
{
    "request" : "report_order",
    "token" : "123456789ABC",
    "codigo" : "FT-K21JDD",
    "fecha_salida" : "01/01/2022",
    "fecha_llegada" : "02/02/2022",
    "referencia" : "REFEXAMPLE1234",
    "id_plan" : "1835",
    "pais_destino" : "1",
    "pais_origen" : "AR",
    "moneda" : "usd",
    "tasa_cambio" : "1",
    "pasajeros" : "1",
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
    "numero_dias" : "120",
    "costo" : "4423",
    "consideraciones_generales" : "ninguna",
    "lenguaje" : "spa"
}
```

# Results

```JSON
{
    "status": "OK"
}
```

# Common Error Codes

* ```1005``` : Invalid token
* ```1030``` : Language not implemented
* ```1050``` : Plan not valid
* ```1062``` : Invalid birthday
* ```1080``` : Invalid Territory
* ```1090``` : Restricted origin
* ```1100``` : Invalid ID agent
* ```1246``` : Age limit exceeded
* ```2001``` : Invalid departure date
* ```2002``` : Invalid arrival date
* ```2004``` : Date from earlier than system date
* ```3030``` : Invalid date range
* ```4004``` : Invalid email contact
* ```4005``` : Empty passenger name
* ```4006``` : Empty passenger document
* ```4007``` : Empty passenger surname
* ```4008``` : Empty passenger phone
* ```4010``` : Empty passenger email
* ```4011``` : Invalid passenger email
* ```5005``` : Birthday required
* ```5006``` : Empty medical conditions
* ```5012``` : Required passenger email
* ```6021``` : Emtpy language
* ```6022``` : Empty plan
* ```6025``` : Empty passenger phone
* ```6026``` : Empty amount of passengers
* ```6027``` : Emtpy origin
* ```6028``` : Empty destination territory
* ```6029``` : Empty date of departure
* ```6030``` : Empty return date
* ```6034``` : Empty currency
* ```6036``` : Empty contact name
* ```6037``` : All the fields are empty
* ```6045``` : Order data already exists
* ```6054``` : Code already exists
* ```9011``` : Exchange rate invalid
* ```9032``` : Invalid beneficiary name format
* ```9034``` : Phone beneficiary not valid
* ```9035``` : Last name beneficiary not valid
* ```9060``` : Invalid name contact
* ```9061``` : Empty token