# add_order_rci (PUT)

Funcion utilizada para cotización de RCI

* Endpoint : ```rcibywta.com/apirest_v1/quote```

# Campos

```JSON
{
    "request" : "add_order_rci",
    "token" : "4E4HMJA9GBHE4BA7",
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

* El campo "pasajeros" indica la cantidad de pasajeros, los campos donde el input esa rodeado por brackets [] dependen de este campo. Por ejemplo, si hay 3 pasajeros, entonces:

```JSON
{
    "..." : "...",
    "nacimientos" : ["01/01/1991","02/02/1992","03/03/1993"],
    "..." : "..."
}
```

* The only exception to this rule is the "upgrades" field, which doesn't depends of the ammount of passengers

* La única excepción a esta regla es el campo "upgrades", el cual no depende de la cantidad de pasajeros

# Resultados

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

# Errores comunes retornados por la función

* ```1005``` : Token invalido
* ```9061``` : Token vacio
* ```1003``` : Lenguaje no implementado
* ```4006``` : Id del country es invalido
* ```6021``` : Lenguaje vacio
* ```6022``` : Plan vacio
* ```1062``` : Fecha de nacimiento vacia
* ```1095``` : Upgrade invalido
* ```6026``` : Cantidad de pasajeros vacia
* ```6045``` : Datos de la orden ya existen
* ```6027``` : Pais de origen vacio
* ```6028``` : Territorio destino vacio
* ```6029``` : Fecha de salida vacia
* ```6030``` : Fecha de llegada vacia
* ```6034``` : Moneda vacia
* ```6035``` : Codigo de emision vacia
* ```6036``` : Nombre de contacto vacia
* ```6037``` : Todos los campos estan vacios
* ```2004``` : La fecha de salida no puede ser menor a la fecha actual
* ```1050``` : Plan invalido
* ```3030``` : Rango de fecha invalido
* ```1022``` : Moneda invalida
* ```3153``` : Conflicto de elementos en el llamado (Edad del pasajero excede el limite del plan)
* ```3150``` : Hace falta un elemento requerido (Un campo obligatorio esta vacio)
* ```4005``` : Nombre del pasajero esta vacio
* ```4006``` : Documento del pasajero esta vacio
* ```4007``` : Apellido del pasajero esta vacio
* ```5006``` : Condiciones medicas esta vacio
* ```4008``` : Telefono del pasajero vacio
* ```1248``` : Cantidad de dias menores a los permitidos por el plan
* ```1247``` : Cantidad de dias mayores a los permitidos por el plan
* ```1080``` : Territorio invalido
* ```1081``` : Territorio restringido
* ```5003``` : Error en numero de pasajeros
* ```5005``` : Fecha de nacimiento requerida
* ```5009``` : Numero de telefono del pasajero invalido
* ```4010``` : Email del beneficiario invalido
* ```5012``` : Se requiere email del pasajero
* ```1091``` : Pais restringido
* ```9011``` : Tasa de cambio invalida