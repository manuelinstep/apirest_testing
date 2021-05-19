# Information

* Type : GET

This endpoint provides a method that gets the required operation as a field, the data received and aditional fields depend on which method is being specified by the user

## Mandatory fields

```JSON
{
    "token" : "123456789ABC"
}
```

The token provided in the aforementioned auth method is required every time this method is consumed

* It must be noted that, the name of the function specified in the following list is the parameter that must be sent
with every call to the function to consume said method.

## get_voucher

Returns all the information of an order

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

## get_currencies

Returns all the currencies stored in the database

# Fields

```JSON
{
    "request" : "get_currencies",
    "token" : "123456789ABC"
}
```

# Results

```JSON
[
    {
        "id_country": "AD",
        "value_iso": "AFN",
        "desc_small": "Afghani"
    },
    {
        "id_country": "6",
        "value_iso": "ALL",
        "desc_small": "Lek"
    },
    {
        "id_country": "60",
        "value_iso": "DZD",
        "desc_small": "Algerian Dinar"
    },
    {
        "id_country": "12",
        "value_iso": "USD",
        "desc_small": "US Dollar"
    },
    {
        "id_country": "AD",
        "value_iso": "EUR",
        "desc_small": "Euro"
    },
    {
        "id_country": "9",
        "value_iso": "AOA",
        "desc_small": "Kwanza"
    },
    {
        "id_country": "5",
        "value_iso": "XCD",
        "desc_small": "East Caribbean Dollar"
    },
    {
        "id_country": "10",
        "value_iso": "N/A",
        "desc_small": "No universal currency"
    },
    {
        "id_country": "4",
        "value_iso": "XCD",
        "desc_small": "East Caribbean Dollar"
    },
    {
        "id_country": "VE",
        "value_iso": "VEF",
        "desc_small": "Bolivar Fuerte"
    },
    {
        "id_country": "DZ",
        "value_iso": "ADEZ",
        "desc_small": "prueba"
    },
    {
        "id_country": "AL",
        "value_iso": "AL",
        "desc_small": "testing"
    }
]
```

# Common Error Codes

* ```1005``` : Invalid Token
* ```9015``` : No results

## get_countries

Returns all the currently stored countries, depending on the language, currently supports spanish (spa), or english (eng)

# Fields

```JSON
{
    "request" : "get_countries",
    "token" : "123456789ABC",
    "language" : "spa"
}
```

# Results

```JSON
[
    {
        "iso_country": "AD",
        "description": "Andorra"
    },
    {
        "iso_country": "AE",
        "description": "Emiratos Árabes Unidos"
    },
    {
        "iso_country": "AF",
        "description": "Afganistán"
    },
    {
        "iso_country": "AG",
        "description": "Antigua y Barbuda"
    },
    {
        "iso_country": "AI",
        "description": "Anguilla"
    },
    {
        "iso_country": "AL",
        "description": "Albania"
    }
]
```

# Common Error Codes

* ```1030``` : Language not implemented
* ```1005``` : Invalid Token

## get_regions

Returns all the currently stored regions

# Fields

```JSON
{
    "request" : "get_regions",
    "token" : "123456789ABC"
}
```

# Results

```JSON
[
    {
        "id_territory": "1",
        "desc_small": "WorldWide"
    },
    {
        "id_territory": "2",
        "desc_small": "Europe"
    },
    {
        "id_territory": "9",
        "desc_small": "Local"
    }
]
```

# Common Error Codes

* ```1005``` : Invalid Token

## get_plans

Returns all the plans associated with the user's agency

# Fields

```JSON
{
    "request" : "get_plans",
    "token" : "123456789ABC",
    "language" : "spa"
}
```

# Results

```JSON
[
    {
        "id" : "1840",
        "titulo" : "titulo",
        "description" : "description",
        "language_id" : "spa",
        "plan_id" : "1840",
        "id_plan_categoria" : "24",
        "num_pas" : "9",
        "min_tiempo" : "1",
        "max_tiempo" : "90",
        "id_currence" : "31",
        "family_plan" : "N",
        "min_age" : "1",
        "max_age" : "90",
        "normal_age" : "21",
        "plan_local" : "N",
        "modo_plan" : "W",
        "original_id" : null
    }
]
```

# Common Error Codes

* ```1005``` : Invalid Token
* ```9015``` : No Results
* ```6021``` : Empty language 

## get_coverages

Returns all of a plan coverages

# Fields

```JSON
{
    "request" : "get_coverages",
    "token" : "123456789ABC",
    "language" : "spa",
    "id_plan" : "1840" 
}
```

# Results

```JSON
[
    {
        "valor_spa": "titulo",
        "valor_eng": "title",
        "id_benefit": "207",
        "name": "Asistencia médica por accidente",
        "language_id": "spa",
        "extended_info": "Asistencia médica por accidente"
    }
]
```

# Common Error Codes

* `````` :
* `````` :

## get_template

Example Text

# Fields

```JSON
{
    "request" : "get_currencies",
    "token" : "123456789ABC"
}
```

# Results

```JSON
```

# Common Error Codes

* `````` :
* `````` :