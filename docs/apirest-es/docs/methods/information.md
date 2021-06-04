# Information

* Type : GET

Este endpoint provee un método al cual se le indíca la operación requerida como un campo (request), la data recibída y campos adicionales dependen de que operación es especificada por el usuario

## Campos obligatorios en todos los llamados:

```JSON
{
    "token" : "123456789ABC",
    "request" : "example_request"
}
```

El token obtenido en el endpoint denominado auth es requerido cada vez que se llame a una función

## get_voucher

Muestra toda la información de una orden

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

## get_currencies

Retorna todas las monedas guardadas en el sistema

# Campos

```JSON
{
    "request" : "get_currencies",
    "token" : "123456789ABC"
}
```

# Resultados

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

# Errores Comunes retornados por la función

* ```1005``` : Token invalido
* ```9015``` : No hay resultados

## get_countries

Retorna todos los paises guardados en el sistem en el idioma indicado. Los idiomas disponibles actualmente son español (spa) e inglés (eng)

# Campos

```JSON
{
    "request" : "get_countries",
    "token" : "123456789ABC",
    "language" : "spa"
}
```

# Resultados

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

# Errores Comunes retornados por la función

* ```1030``` : Lenguaje no implementado
* ```1005``` : Token invalido

## get_regions

Retorna todas las regiones guardadas en el sistema

# Campos

```JSON
{
    "request" : "get_regions",
    "token" : "123456789ABC"
}
```

# Resultados

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

# Errores Comunes retornados por la función

* ```1005``` : Token invalido

## get_plans

Retorna todos los planes asociados con la agencia del usuario

# Campos

```JSON
{
    "request" : "get_plans",
    "token" : "123456789ABC",
    "language" : "spa"
}
```

# Resultados

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

# Errores Comunes retornados por la función

* ```1005``` : Token invalido
* ```9015``` : No hay resultados
* ```6021``` : Lenguaje vacío

## get_coverages

Retorna todas las coberturas de un plan

# Campos

```JSON
{
    "request" : "get_coverages",
    "token" : "123456789ABC",
    "language" : "spa",
    "id_plan" : "1840" 
}
```

# Resultados

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

# Errores Comunes retornados por la función

* `````` :
* `````` :

## get_pvp_price

Retorna los precios de un plan basado en el país especificado

# Campos

```JSON
{
    "request" : "get_currencies",
    "token" : "123456789ABC",
    "id_plan" : "1234",
    "iso_country" : "CL"
}
```

* El campo "iso_country" puede dejarse vacío, esto retornara todos los precios del plan sin especificar un país

# Resultados

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

# Errores Comunes retornados por la función

* ```1091``` : El plan está restringido para este país
* ```1060``` : No hay precio registrado para este país
* ```6022``` : Plan Vacio

## get_languages

Retorna todos los lenguajes disponibles en la plataforma

# Campos

```JSON
{
    "request" : "get_languages",
    "token" : "123456789ABC"
}
```

# Resultados

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

# Errores Comunes retornados por la función

* ```1005``` : Token invalido
* ```6020``` : Token vacio

## get_plan_category

Retorna todas las categorías de plan

# Campos

```JSON
{
    "request" : "get_plan_category",
    "token" : "123456789ABC",
    "language" : "spa"
}
```

# Resultados

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

# Errores Comunes retornados por la función

* ```6021``` : Lenguaje vacío
* ```1030``` : Lenguaje no implementado
* ```1040``` : No hay categorías de plan

## get_terms

Retorna los términos y condiciones del plan

# Campos

```JSON
{
    "request" : "get_currencies",
    "token" : "123456789ABC",
    "id_plan" : "1800",
    "language" : "spa"
}
```

# Resultados

```JSON
{
    "id": "1800",
    "name": "USD 20.000 L. Econ.",
    "description": "USD 20.000 L. Econ.",
    "terms": "fasttravelassistance.ilstechnik.com/app/admin/server/php/files/120181204031219.pdf"
}
```

# Errores Comunes retornados por la función

* ```6021``` : Lenguaje vacío
* ```6022``` : Plan Vacio
* ```1050``` : Invalid plan
* ```4012``` : Conditions not found
* ```1030``` : Lenguaje no implementado

## get_upgrade

Retorna la información del upgrade solicitado

# Campos

```JSON
{
    "request" : "get_upgrade",
    "token" : "123456789ABC",
    "id_plan" : "1800",
    "language" : "spa"
}
```

# Resultados

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

# Errores Comunes retornados por la función

* ```6021``` : Lenguaje vacío
* ```6022``` : Plan Vacio
* ```1050``` : Plan invalido

## country_restricted

Retorna los países restringidos por el plan

# Campos

```JSON
{
    "request" : "get_currencies",
    "token" : "123456789ABC",
    "id_plan" : "1835",
    "language" : "spa"
}
```

# Resultados

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

# Errores Comunes retornados por la función

* ```6021``` : Lenguaje vacío
* ```6022``` : Plan Vacio
* ```1050``` : Plan invalido

## exchange_rate

Retorna la tasa de cambio del país especificado

# Campos

```JSON
{
    "request" : "exchange_rate",
    "token" : "123456789ABC",
    "iso_country" : "VE"
}
```

# Resultados

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

# Errores Comunes retornados por la función

* ```5013``` : La tasa de cambio no existe

## get_country_cities

Retorna las ciudades del país especificado

# Campos

```JSON
{
    "request" : "get_country_cities",
    "token" : "123456789ABC",
    "iso_country" : "CO",
    "language" : "spa"
}
```

# Resultados

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

# Errores Comunes retornados por la función

* ```9173``` : Iso country invalido
* ```9174``` : Iso country vacio
* ```6021``` : Lenguaje vacío
* ```1030``` : Lenguaje no implementado

## get_country_states

Retorna los estados del país especificado

# Campos

```JSON
{
    "request" : "get_country_states",
    "token" : "123456789ABC",
    "iso_country" : "CO",
    "language" : "spa"
}
```

# Resultados

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

# Errores Comunes retornados por la función

* ```1030``` : Lenguaje no implementado
* ```6021``` : Lenguaje vacío
* ```9015``` : No results
* ```9173``` : Invalid Iso Country
* ```9174``` : Empty Iso Country

## get_country_states_cities

Retorna las ciudades dentro del estado específico de un país

# Campos

```JSON
{
    "request" : "get_currencies",
    "token" : "123456789ABC",
    "iso_country" : "CO",
    "iso_state" : "CO.14",
    "language" : "spa"
}
```

# Resultados

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

# Errores Comunes retornados por la función

* ```1030``` : Lenguaje no implementado
* ```6021``` : Lenguaje vacío
* ```9015``` : No hay resultados
* ```9173``` : Iso country invalido
* ```9174``` : Iso country vacio
* ```9175``` : Country states vacio
* ```9176``` : Iso states vacio
* ```9177``` : Iso states invalido