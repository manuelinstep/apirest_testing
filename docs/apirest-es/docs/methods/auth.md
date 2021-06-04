# Auth

* Type: POST

Este endpoint provee el token que debe proveer el usuario con cada llamado a una función

## Campos

```JSON
{
    "usuario" : "example_user",
    "password" : "example_password"
}
```

## Results

Si el usuario y la contraseña son correctos, el método retorna un token de 12 dígitos
```JSON
{
    "status": "OK",
    "result": {
        "token": "123456789ABC"
    }
}
```

## Common Error Codes

* ```6037``` : Ambos campos estan vacíos
* ```6040``` : Campo 'usuario' esta vacío
* ```6041``` : Campo 'password' esta vacío