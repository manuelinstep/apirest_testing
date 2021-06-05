# get_voucher (GET)

Muestra toda la información de una orden

* Endpoint : ```fasttravelassistance.ilstechnik.com/apirest_testing/information```

# Campos

```JSON
{
    "request" : "get_voucher",
    "token" : "123456789ABC",
    "codigo" : "EXMPL-1234"
}
```

# Resultados

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


# Errores Comunes retornados por la función

* ```1005``` : Token invalido
* ```9015``` : No hay resultados