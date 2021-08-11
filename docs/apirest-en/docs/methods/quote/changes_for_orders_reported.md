# changes_for_orders_reported (PUT)

Changes values of a reported order

* Endpoint : ```rcibywta.com/apirest_v1/quote```

# Fields

```JSON
{
    "request" : "changes_for_orders_reported",
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

# Results

```JSON
{
    "status": "OK"
}
```

# Common Error Codes

* ```1005``` : Invalid Token
* ```1080``` : Invalid territory
* ```1081``` : Restricted territory
* ```1090``` : Restricted origin
* ```1091``` : Restricted country
* ```1247``` : Days exceeding
* ```1248``` : Undersized days in the plan
* ```2000``` : Invalid date
* ```2001``` : Invalid date from
* ```2004``` : Date from earlier than system date
* ```3020``` : Invalid date format
* ```3030``` : Invalid date range
* ```6020``` : Empty token
* ```6029``` : Empty date of departure
* ```6030``` : Return date empty