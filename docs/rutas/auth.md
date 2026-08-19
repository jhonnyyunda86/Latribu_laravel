# Rutas de Autenticación (auth.php)

**Ubicación:** `routes/auth.php`

## Descripción
Este archivo define el flujo de autenticación y seguridad provisto por Laravel Breeze. Las rutas están organizadas mediante middleware para diferenciar entre invitados (usuarios no logueados) y usuarios autenticados.

## Rutas Definidas

### 1. Para Invitados (Middleware: `guest`)
Rutas accesibles únicamente para usuarios que no han iniciado sesión:
* **Registro de Usuarios:**
  * `GET /register`: Muestra el formulario de registro (`RegisteredUserController@create`).
  * `POST /register`: Procesa el registro y crea el usuario en base de datos (`RegisteredUserController@store`).
* **Inicio de Sesión:**
  * `GET /login`: Muestra la pantalla de login (`AuthenticatedSessionController@create`).
  * `POST /login`: Autentica al usuario en el sistema (`AuthenticatedSessionController@store`).
* **Recuperación de Contraseña:**
  * `GET /forgot-password`: Muestra el formulario para solicitar enlace de restablecimiento (`PasswordResetLinkController@create`).
  * `POST /forgot-password`: Envía el correo electrónico de recuperación (`PasswordResetLinkController@store`).
  * `GET /reset-password/{token}`: Muestra la pantalla de cambio de clave (`NewPasswordController@create`).
  * `POST /reset-password`: Registra la nueva contraseña (`NewPasswordController@store`).

### 2. Para Usuarios Autenticados (Middleware: `auth`)
Rutas de seguridad que requieren que el usuario esté logueado:
* **Verificación de Email:**
  * `GET /verify-email`: Notificación de verificación de correo (`EmailVerificationPromptController`).
  * `GET /verify-email/{id}/{hash}`: Enlace firmado para verificar la cuenta (`VerifyEmailController`).
  * `POST /email/verification-notification`: Reenvía el correo de verificación (`EmailVerificationNotificationController@store`).
* **Confirmación de Clave (Acciones Sensibles):**
  * `GET /confirm-password`: Solicita confirmar clave actual (`ConfirmablePasswordController@show`).
  * `POST /confirm-password`: Valida la contraseña (`ConfirmablePasswordController@store`).
* **Actualización de Seguridad:**
  * `PUT /password`: Cambia la contraseña desde la configuración de perfil (`PasswordController@update`).
* **Cerrar Sesión:**
  * `POST /logout`: Finaliza e invalida la sesión del usuario (`AuthenticatedSessionController@destroy`), destruyendo el token de sesión en servidor y redirigiendo a `/`.
