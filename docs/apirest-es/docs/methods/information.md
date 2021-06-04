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

## get_pvp_price

Gets price from a plan based on the country specified

# Fields

```JSON
{
    "request" : "get_currencies",
    "token" : "123456789ABC",
    "id_plan" : "1234",
    "iso_country" : "CL"
}
```

WARNING: The field "iso_country" can be left empty, this will return all the prices of the plan without regarding a specific country

# Results

```JSON
[
    {
        "unidad": "dias",
        "tiempo": "20",
        "valor": "2.70"
    },
    {
        "unidad": "dias",
        "tiempo": "40",
        "valor": "2.42"
    },
    {
        "unidad": "dias",
        "tiempo": "60",
        "valor": "2.10"
    },
    {
        "unidad": "dias",
        "tiempo": "90",
        "valor": "1.80"
    },
    {
        "unidad": "dias",
        "tiempo": "1",
        "valor": "2.54"
    }
]
```

# Common Error Codes

* ```1091``` : The country provided is restricted for the plan
* ```1060``` : Price not registered for this country
* ```6022``` : Empty plan

## get_languages

Returns all the languages avaible in the platform

# Fields

```JSON
{
    "request" : "get_languages",
    "token" : "123456789ABC"
}
```

# Results

```JSON
[
    {
        "id": "141",
        "lg_id": "eng",
        "name": "English",
        "short_name": "en"
    },
    {
        "id": "397",
        "lg_id": "por",
        "name": "Portuguese",
        "short_name": "pt"
    },
    {
        "id": "456",
        "lg_id": "spa",
        "name": "Español",
        "short_name": "es"
    }
]
```

# Common Error Codes

* ```1005``` : Invalid Token
* ```6020``` : Empty Token

## get_plan_category

Returns all plan categories

# Fields

```JSON
{
    "request" : "get_plan_category",
    "token" : "123456789ABC",
    "language" : "spa"
}
```

# Results

```JSON
[
    {
        "name_plan": "Larga Estadia",
        "id_plan_categoria": "22"
    },
    {
        "name_plan": "Anuales - Multiviajes",
        "id_plan_categoria": "23"
    },
    {
        "name_plan": "Viajes por día",
        "id_plan_categoria": "24"
    },
    {
        "name_plan": "Estudiantil",
        "id_plan_categoria": "27"
    },
    {
        "name_plan": "Catpruebaesp",
        "id_plan_categoria": "28"
    }
]
```

# Common Error Codes

* ```6021``` : Empty language
* ```1030``` : Language not implemented
* ```1040``` : There's no plan category

## get_terms

Returns the terms and conditions of the plan

# Fields

```JSON
{
    "request" : "get_currencies",
    "token" : "123456789ABC",
    "id_plan" : "1800",
    "language" : "spa"
}
```

# Results

```JSON
{
    "id": "1800",
    "name": "USD 20.000 L. Econ.",
    "description": "USD 20.000 L. Econ.",
    "terms": "fasttravelassistance.ilstechnik.com/app/admin/server/php/files/120181204031219.pdf"
}
```

# Common Error Codes

* ```6021``` : Empty language
* ```6022``` : Empty plan
* ```1050``` : Invalid plan
* ```4012``` : Conditions not found
* ```1030``` : Language not implemented

## get_upgrade

Returns the requested upgrade information

# Fields

```JSON
{
    "request" : "get_upgrade",
    "token" : "123456789ABC",
    "id_plan" : "1800",
    "language" : "spa"
}
```

# Results

```JSON
[
    {
        "type_raider": "Valor",
        "rd_calc_type": "Pasajero Especifico",
        "id_raider": "135",
        "cost_raider": "59.940",
        "name_raider": "USD 9,000 - Cancelación Multi-causa",
        "value_raider": "149.850"
    },
    {
        "type_raider": "Valor",
        "rd_calc_type": "Pasajero Especifico",
        "id_raider": "152",
        "cost_raider": "70.000",
        "name_raider": "USD 7,000 - Cancelación Multi causa Comp",
        "value_raider": "70.000"
    },
    {
        "type_raider": "Valor",
        "rd_calc_type": "Pasajero Especifico",
        "id_raider": "151",
        "cost_raider": "60.000",
        "name_raider": "USD 6,000 - Cancelación Multi Causa Comp",
        "value_raider": "60.000"
    },
    {
        "type_raider": "Valor",
        "rd_calc_type": "Pasajero Especifico",
        "id_raider": "132",
        "cost_raider": "39.960",
        "name_raider": "USD 6,000 - Cancelación Multi-causa",
        "value_raider": "99.900"
    },
    {
        "type_raider": "Porcentage %",
        "rd_calc_type": "Pasajero Especifico",
        "id_raider": "137",
        "cost_raider": "25.000",
        "name_raider": "Upgrade de futura Mamá (25% de la cobertura médica, sin exceder USD 10,000)",
        "value_raider": "25.000"
    }
]
```

# Common Error Codes

* ```6021``` : Empty language
* ```6022``` : Empty plan
* ```1050``` : Plan not valid

## country_restricted

Returns the plan's restricted countries

# Fields

```JSON
{
    "request" : "get_currencies",
    "token" : "123456789ABC",
    "id_plan" : "1835",
    "language" : "spa"
}
```

# Results

```JSON
[
    {
        "iso_country": "AW",
        "description": "Aruba"
    },
    {
        "iso_country": "BO",
        "description": "Bolivia"
    },
    {
        "iso_country": "CL",
        "description": "Chile"
    },
    {
        "iso_country": "CR",
        "description": "Costa Rica"
    },
    {
        "iso_country": "CU",
        "description": "Cuba"
    },
    {
        "iso_country": "EC",
        "description": "Ecuador"
    },
    {
        "iso_country": "SV",
        "description": "El Salvador"
    },
    {
        "iso_country": "GT",
        "description": "Guatemala"
    },
    {
        "iso_country": "HN",
        "description": "Honduras"
    },
    {
        "iso_country": "MX",
        "description": "México"
    },
    {
        "iso_country": "AN",
        "description": "Antillas Holandesas"
    },
    {
        "iso_country": "NI",
        "description": "Nicaragua"
    },
    {
        "iso_country": "PA",
        "description": "Panamá"
    },
    {
        "iso_country": "PY",
        "description": "Paraguay"
    },
    {
        "iso_country": "PE",
        "description": "Perú"
    },
    {
        "iso_country": "UY",
        "description": "Uruguay"
    }
]
```

# Common Error Codes

* ```6021``` : Empty language
* ```6022``` : Empty plan
* ```1050``` : Plan not valid

## exchange_rate

Returns the exchange rate of a specified country

# Fields

```JSON
{
    "request" : "exchange_rate",
    "token" : "123456789ABC",
    "iso_country" : "VE"
}
```

# Results

```JSON
[
    {
        "description": "Venezuela",
        "iso_country": "VE",
        "currencyname": "Bolivar",
        "usd_exchange": "5000"
    }
]
```

# Common Error Codes

* ```5013``` : Change rate doesn't exist

## get_country_cities

Returns the cities of the specified country

# Fields

```JSON
{
    "request" : "get_country_cities",
    "token" : "123456789ABC",
    "iso_country" : "CO",
    "language" : "spa"
}
```

# Results

```JSON
[
    {
        "countries_description": "Colombia",
        "cities_description": "Baranoa",
        "iso_city": "3689235",
        "states_description": "Atlantico",
        "iso_state": "CO.04"
    },
    {
        "countries_description": "Colombia",
        "cities_description": "Barranquilla",
        "iso_city": "3689147",
        "states_description": "Atlantico",
        "iso_state": "CO.04"
    },
    {
        "countries_description": "Colombia",
        "cities_description": "Campo de la Cruz",
        "iso_city": "3687758",
        "states_description": "Atlantico",
        "iso_state": "CO.04"
    },
    {
        "countries_description": "Colombia",
        "cities_description": "Candelaria",
        "iso_city": "3687634",
        "states_description": "Atlantico",
        "iso_state": "CO.04"
    },
    {
        "countries_description": "Colombia",
        "cities_description": "Galapa",
        "iso_city": "3682238",
        "states_description": "Atlantico",
        "iso_state": "CO.04"
    },
    {
        "countries_description": "Colombia",
        "cities_description": "Juan de Acosta",
        "iso_city": "3680176",
        "states_description": "Atlantico",
        "iso_state": "CO.04"
    },
    {
        "countries_description": "Colombia",
        "cities_description": "Luruaco",
        "iso_city": "3675826",
        "states_description": "Atlantico",
        "iso_state": "CO.04"
    },
    {
        "countries_description": "Colombia",
        "cities_description": "Malambo",
        "iso_city": "3675595",
        "states_description": "Atlantico",
        "iso_state": "CO.04"
    },
    {
        "countries_description": "Colombia",
        "cities_description": "Manati",
        "iso_city": "3675512",
        "states_description": "Atlantico",
        "iso_state": "CO.04"
    },
    {
        "countries_description": "Colombia",
        "cities_description": "Palmar de Varela",
        "iso_city": "3673220",
        "states_description": "Atlantico",
        "iso_state": "CO.04"
    },
    {
        "countries_description": "Colombia",
        "cities_description": "Piojo",
        "iso_city": "3672176",
        "states_description": "Atlantico",
        "iso_state": "CO.04"
    }
]
```

# Common Error Codes

* ```9173``` : Invalid Iso country
* ```9174``` : Empty Iso country
* ```6021``` : Empty language
* ```1030``` : Language not implemented

## get_country_states

Returns the states of the specified country

# Fields

```JSON
{
    "request" : "get_country_states",
    "token" : "123456789ABC",
    "iso_country" : "CO",
    "language" : "spa"
}
```

# Results

```JSON
[
    {
        "countries_description": "Colombia",
        "states_description": "Atlantico",
        "iso_state": "CO.04"
    },
    {
        "countries_description": "Colombia",
        "states_description": "Bogota D.C.",
        "iso_state": "CO.34"
    },
    {
        "countries_description": "Colombia",
        "states_description": "Cundinamarca",
        "iso_state": "CO.33"
    }
]
```

# Common Error Codes

* ```1030``` : Language not implemented
* ```6021``` : Empty language
* ```9015``` : No results
* ```9173``` : Invalid Iso Country
* ```9174``` : Empty Iso Country

## get_country_states_cities

Returns the city of a specified state in a country

# Fields

```JSON
{
    "request" : "get_currencies",
    "token" : "123456789ABC",
    "iso_country" : "CO",
    "iso_state" : "CO.14",
    "language" : "spa"
}
```

# Results

```JSON
[
    {
        "countries_description": "Colombia",
        "cities_description": "Calamar",
        "iso_city": "3687975",
        "states_description": "Departamento del Guaviare",
        "iso_state": "CO.14"
    },
    {
        "countries_description": "Colombia",
        "cities_description": "El Retorno",
        "iso_city": "3792387",
        "states_description": "Departamento del Guaviare",
        "iso_state": "CO.14"
    },
    {
        "countries_description": "Colombia",
        "cities_description": "Miraflores",
        "iso_city": "3674740",
        "states_description": "Departamento del Guaviare",
        "iso_state": "CO.14"
    },
    {
        "countries_description": "Colombia",
        "cities_description": "San Jose del Guaviare",
        "iso_city": "3828545",
        "states_description": "Departamento del Guaviare",
        "iso_state": "CO.14"
    }
]
```

# Common Error Codes

* ```1030``` : Language not implemented
* ```6021``` : Empty language
* ```9015``` : No results
* ```9173``` : Invalid Iso Country
* ```9174``` : Empty Iso Country
* ```9175``` : Empty Country states
* ```9176``` : Empty Iso states
* ```9177``` : Invalid Iso state

