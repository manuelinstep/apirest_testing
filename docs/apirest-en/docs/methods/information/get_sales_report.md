# get_sales_report (GET)

Returns a report of all sales made in the specified period of time

* Endpoint : ```rcibywta.com/apirest_v1/information```

# Fields

```JSON
{
    "request" : "get_sales_report",
    "token" : "123456789ABC",
    "desde" : "01/01/2022",
    "hasta" : "02/02/2022",
    "estatus" : "1",
    "formato" : "excel"
}
```

# Results

```JSON
{
    "status": "OK",
    "Enlace de Descarga": "https://bit.ly/3uTyuGU"
}
```

# Common Error Codes

* ```9067``` : Invalid date
* ```9066``` : Invalid format
* ```9017``` : Invalid status
* ```3030``` : Invalid date range
* ```9064``` : Invalid end date
* ```9063``` : Invalid start date
* ```6020``` : Empty token
* ```1005``` : Invalid token