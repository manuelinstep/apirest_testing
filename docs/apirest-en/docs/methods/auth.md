# auth (POST)

This endpoint provides the mandatory Token that the user must provide everytime a method is called
(being the only exception this method itself)

* Endpoint : ```rcibywta.com/apirest_v1/auth```

## Fields

```JSON
{
    "usuario" : "example_user",
    "password" : "example_password"
}
```

If using a client for these requests (i.e. Postman), fields are named the same way

## Results

When consumed succesfully, the method returns a 12 character Token
```JSON
{
    "status": "OK",
    "result": {
        "token": "123456789ABC"
    }
}
```

## Common Error Codes

* ```6037``` : Both Fields are empty 
* ```6040``` : Field 'usuario' is empty
* ```6041``` : Field 'password' is empty