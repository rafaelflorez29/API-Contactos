## Endpoints

| Método | Ruta                 | Descripción                       | Auth   |
|--------|----------------------|-----------------------------------|--------|
| GET    | /api/v1/health       | Estado del servicio               | No     |
| POST   | /api/v1/auth/login   | Inicia sesión y entrega un token  | No     |
| GET    | /api/v1/auth/me      | Perfil del usuario autenticado    | Bearer |
| POST   | /api/v1/auth/logout  | Cierra sesión y revoca el token   | Bearer |

## Autenticación

1. POST /api/v1/auth/login con email, password y device_name.
2. Guardar el token de la respuesta.
3. Enviar en cada petición protegida:
   Authorization: Bearer {token}
   Accept: application/json

El login admite 5 intentos por minuto por correo e IP.