# report_order (PUT)

Utilizado para reportar ordenes

* Endpoint : ```rcibywta.com/apirest_v1/quote```

# Campos

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
    "costo" : "4423",
    "consideraciones_generales" : "ninguna",
    "lenguaje" : "spa"
}
```

# Resultados

```JSON
{
    "status": "OK",
    "El valor de cambio fue ajustado a:": "5,000.00"
}
```

# Errores comunes retornados por la función

* ```1005``` : Token invalido
* ```1030``` : Lenguaje no implementado
* ```1050``` : Plan invalido
* ```1062``` : Cumpleaños invalido
* ```1080``` : Territorio Invalido
* ```1090``` : Origen restringido
* ```1100``` : Id del agente es invaliod
* ```1246``` : Limite de edad excedido
* ```2001``` : Fecha de salida invalida
* ```2002``` : Fecha de llegada invalida
* ```2004``` : Fecha menor a la fecha del sistema
* ```3030``` : Rango de fechas invalido
* ```4005``` : Nombre de un pasajero vacío
* ```4006``` : Documento de un pasajero vacío
* ```4007``` : Apellido de un pasajero vacío
* ```4008``` : Telefono de un pasajero vacío
* ```4010``` : Correo de un pasajero vacío
* ```4011``` : Correo de un pasajero invalido
* ```5005``` : Cumpleaños requerido
* ```5006``` : Condiciones médicas vacias
* ```5012``` : Correo del pasajero requerido
* ```6021``` : Lenguaje vacío
* ```6022``` : Plan vacío
* ```6025``` : Telefono del pasajero vacío
* ```6026``` : Cantidad de pasajeros vacía
* ```6027``` : Origen vacío
* ```6028``` : Territorio de destino vacío
* ```6029``` : Fecha de salida vacía
* ```6030``` : Fecha de llegada vacía
* ```6034``` : Moneda vacía
* ```6037``` : Todos los campos estan vacíos
* ```6045``` : Ya existe una orden con estos datos
* ```6054``` : Ya existe el código
* ```9011``` : Tasa de cambio vacía
* ```9032``` : Formato del nombre del beneficiario invalido
* ```9034``` : Telefono del beneficiario invalido
* ```9035``` : Apellido del beneficiario invalido
* ```9061``` : Token vacío
* ```9120``` : Voucher maestro vacío
* ```9121``` : Voucher maestro invalido
* ```9122``` : Código del voucher maestro difiere del código provisto