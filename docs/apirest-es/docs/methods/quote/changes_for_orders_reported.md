# changes_for_orders_reported (PUT)

Cambia los datos de una orden reportada

* Endpoint : ```fasttravelassistance.ilstechnik.com/apirest_testing/quote```

# Campos

```JSON
{
    "request" : "report_order",
    "token" : "123456789ABC",
    "codigo" : "TESTCODE-789988",
    "status" : "1",
    "pais_origen" : "VE",
    "pais_destino" : "1",
    "fecha_salida" : "01/01/2022",
    "fecha_retorno" : "02/02/2022",
    "costo" : "999",
}
```

# Resultados

```JSON
{
    "status": "OK"
}
```

# Errores comunes retornados por la función

* ```1005``` : Token invalido
* ```1080``` : Territorio invalido
* ```1081``` : Territorio restringido
* ```1090``` : Origen restringido
* ```1091``` : Pais restringido
* ```1247``` : Dias exceden los estipulados por el plan
* ```1248``` : Dias menores a los permitidos por el plan
* ```2000``` : Fecha invalida
* ```2001``` : Fecha de salida invalida
* ```2004``` : Fecha menor a la fecha del sistema
* ```3020``` : Formato de fecha invalido
* ```3030``` : Rango de fecha invalido
* ```6020``` : Token vacio
* ```6029``` : Fecha de salida vacia
* ```6030``` : Fecha de llegada vacia