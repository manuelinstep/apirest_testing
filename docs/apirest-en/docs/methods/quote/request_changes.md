# request_changes (PUT)

changes values of an added order

* Endpoint : ```fasttravelassistance.ilstechnik.com/apirest_testing/quote```

# Fields

```JSON
{
    "request" : "request_changes",
    "token" : "123456789ABC",
    "codigo" : "TESTCODE-789988",
    "referencia" : "12321231",
    "pais" : "VE",
    "nombre_contacto" : "example contact",
    "telefono_contacto" : "12344321",
    "email_contacto" : "example@email.com",
    "emision" : "1",
    "lenguaje" : "spa",
    "pasajeros" : "1",
    "nombres" : ["Alberto"],
    "apellidos" : ["Rodriguez"],
    "documentos" : ["1234431"],
    "emails" : ["email@contact.com"],
    "condiciones_medicas" : ["ninguna"],
    "telefonos" : ["123442321"]
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
* ```9120``` : Empty master voucher
* ```9121``` : Master voucher invalid
* ```9122``` : Voucher master differs from code