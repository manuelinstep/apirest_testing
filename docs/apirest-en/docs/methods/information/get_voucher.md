# get_voucher (GET)

Returns all the information of an order

* Endpoint : ```fasttravelassistance.ilstechnik.com/apirest_testing/information```

# Fields

```JSON
{
    "request" : "get_voucher",
    "token" : "123456789ABC",
    "codigo" : "EXMPL-1234"
}
```

# Results

```JSON
[
    {
        "id": "9568",
        "origen": "CO",
        "destino": "XX",
        "salida": "2019-02-01",
        "retorno": "2019-02-10",
        "programaplan": "Viajes por día",
        "nombre_contacto": "pruena",
        "email_contacto": "prue@ilsols.com",
        "comentarios": "",
        "telefono_contacto": "57-845-545-5",
        "producto": "1745",
        "agencia": "2267",
        "nombre_agencia": "Fast Travel Assistance",
        "total": "33.800",
        "codigo": "FA-420BEA",
        "fecha": "2018-12-05",
        "vendedor": "10702",
        "cantidad": "1",
        "status": "3",
        "des_status": "Expirado",
        "es_emision_corp": "0",
        "origin_ip": "190.43.20.124",
        "alter_cur": "0",
        "tasa_cambio": "0.000",
        "family_plan": "no",
        "referencia": ""
    }
]
```


# Common Error Codes

* ```1005``` : Invalid Token
* ```9015``` : No results