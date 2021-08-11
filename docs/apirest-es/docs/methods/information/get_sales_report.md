# get_sales_report (GET)

Retorna un enlace hacia el reporte de ventas generado en base al rango de fechas especificado

* Endpoint : ```rcibywta.com/apirest_v1/information```

# Campos

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

# Resultados

```JSON
{
    "status": "OK",
    "Enlace de Descarga": "https://bit.ly/3uTyuGU"
}
```

# Errores comunes retornados por la función

* ```9067``` : Fecha invalida
* ```9066``` : Formato de fecha invalido
* ```9017``` : Estatus invalido
* ```3030``` : Rango de fechas invalido
* ```9064``` : Fecha de inicio invalida
* ```9063``` : Fecha final invalida
* ```6020``` : Token vacío
* ```1005``` : Token invalido