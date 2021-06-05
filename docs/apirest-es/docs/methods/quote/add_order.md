# add_order (PUT)

Añade una orden

* Endpoint : ```fasttravelassistance.ilstechnik.com/apirest_testing/quote```

# Campos

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

* El campo "pasajeros" indica la cantidad de pasajeros, los campos donde el input esta entre brackets [] dependen de dicho campo. Por ejemplo, si hay 3 pasajeros, entonces:

```JSON
{
    "..." : "...",
    "nacimientos" : ["01/01/1991","02/02/1992","03/03/1993"],
    "..." : "..."
}
```

* La unica excepción a esta regla es el campo "upgrades"

# Resultados

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

# Errores comunes retornados por la función

* ```1005``` : Token invalido 
* ```9061``` : Token vacío
* ```1003``` : Lenguaje no implementado
* ```4006``` : id del país es invalido
* ```6021``` : Lenguaje vacío
* ```6022``` : Plan vacío 
* ```1062``` : Fecha de nacimiento inválida
* ```1095``` : Upgrade invalido
* ```6026``` : Cantidad de pasajeros vacía
* ```6045``` : Ya existe una orden con estos daos
* ```6027``` : País de origen vacío
* ```6028``` : Territorio destino vacío
* ```6029``` : Fecha de salida vacía
* ```6030``` : Fecha de llegada vacía
* ```6034``` : Moneda vacía
* ```6035``` : Codigo de emisión vacío
* ```6037``` : Todos los campos estan vacíos
* ```2004``` : La fecha de salida no puede ser menor a la fecha actual
* ```1050``` : Plan invalido
* ```3030``` : Rango de fecha invalido
* ```1022``` : Moneda invalida
* ```3153``` : Conflicto en elemento de la petición (la edad del pasajero excede la edad límite del plan)
* ```3150``` : Un campo obligatorio esta vacio
* ```4005``` : Nombre de un pasajero vacío
* ```4006``` : Documento de un pasajero vacío
* ```4007``` : Apellido de un pasajero vacío
* ```5006``` : Condiciones médicas vacía
* ```4008``` : Telefono de un pasajero vacío
* ```1248``` : Cantidad de días menor a lo permitido por el plan
* ```1247``` : Cantidad de días mayor a lo permitido por el plan
* ```1080``` : Territorio invalido
* ```1081``` : Territorio restringido
* ```5003``` : Error en cantidad de pasajeros
* ```5005``` : Fecha de nacimiento requerida
* ```5009``` : Telefono de un pasajero invalido
* ```4010``` : Correo de un beneficiario invalido
* ```5012``` : Correo del pasajero requerido
* ```1091``` : Pais restringido
* ```9011``` : Tasa de cambio invalida