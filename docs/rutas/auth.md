# Explicación Detallada del Archivo de Rutas: auth.php

**Ruta física:** `routes/auth.php`

Este archivo contiene la declaración de las rutas HTTP dedicadas al subsistema de autenticación y verificación de credenciales del software (impulsado por Laravel Breeze). Controla los flujos de registro, inicio de sesión, cambio de claves y cierres de sesión.

---

## 1. Código Fuente Completo

```php
<?php

use App\Http\Controllers\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Auth\ConfirmablePasswordController;
use App\Http\Controllers\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Auth\EmailVerificationPromptController;
use App\Http\Controllers\Auth\NewPasswordController;
use App\Http\Controllers\Auth\PasswordController;
use App\Http\Controllers\Auth\PasswordResetLinkController;
use App\Http\Controllers\Auth\RegisteredUserController;
use App\Http\Controllers\Auth\VerifyEmailController;
use Illuminate\Support\Facades\Route;

Route::middleware('guest')->group(function () {
    Route::get('register', [RegisteredUserController::class, 'create'])
        ->name('register');

    Route::post('register', [RegisteredUserController::class, 'store']);

    Route::get('login', [AuthenticatedSessionController::class, 'create'])
        ->name('login');

    Route::post('login', [AuthenticatedSessionController::class, 'store']);

    Route::get('forgot-password', [PasswordResetLinkController::class, 'create'])
        ->name('password.request');

    Route::post('forgot-password', [PasswordResetLinkController::class, 'store'])
        ->name('password.email');

    Route::get('reset-password/{token}', [NewPasswordController::class, 'create'])
        ->name('password.reset');

    Route::post('reset-password', [NewPasswordController::class, 'store'])
        ->name('password.store');
});

Route::middleware('auth')->group(function () {
    Route::get('verify-email', EmailVerificationPromptController::class)
        ->name('verification.notice');

    Route::get('verify-email/{id}/{hash}', VerifyEmailController::class)
        ->middleware(['signed', 'throttle:6,1'])
        ->name('verification.verify');

    Route::post('email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
        ->middleware('throttle:6,1')
        ->name('verification.send');

    Route::get('confirm-password', [ConfirmablePasswordController::class, 'show'])
        ->name('password.confirm');

    Route::post('confirm-password', [ConfirmablePasswordController::class, 'store']);

    Route::put('password', [PasswordController::class, 'update'])->name('password.update');

    Route::post('logout', [AuthenticatedSessionController::class, 'destroy'])
        ->name('logout');
});
```

---

## 2. Explicación Detallada de Cada Ruta

### 1. Grupo para Invitados (Middleware: `guest`)
Este middleware encapsula las rutas que solo deben ser accedidas por usuarios no autenticados en el sistema (visitantes externos).

* **Registro de Usuarios Nuevos:**
  * `GET /register`: Invoca `RegisteredUserController@create`. Muestra el formulario web para que un nuevo cliente cree una cuenta en el restaurante.
  * `POST /register`: Invoca `RegisteredUserController@store`. Valida y guarda el nuevo usuario asignándole el rol por defecto de cliente, iniciando sesión de forma automática.
* **Inicio de Sesión (Login):**
  * `GET /login`: Invoca `AuthenticatedSessionController@create`. Renderiza la pantalla de login con campos de correo y contraseña.
  * `POST /login`: Invoca `AuthenticatedSessionController@store`. Valida credenciales, regenera la sesión en el servidor para evitar secuestros y redirecciona al `/dashboard` dinámico.
* **Recuperación de Contraseña Olvidada:**
  * `GET /forgot-password`: Muestra la pantalla para ingresar el correo electrónico.
  * `POST /forgot-password`: Genera un token único y envía un enlace seguro de restablecimiento por email.
  * `GET /reset-password/{token}`: Captura el token enviado por correo y muestra el formulario para escribir la nueva clave.
  * `POST /reset-password`: Valida el token contra la base de datos y actualiza la contraseña del usuario de forma encriptada.

---

### 2. Grupo para Usuarios Autenticados (Middleware: `auth`)
Contiene rutas privadas que requieren que el cliente, mesero o administrador tenga una sesión de navegación activa en el servidor.

* **Verificación de Correo Electrónico:**
  * `GET /verify-email`: Muestra la indicación en pantalla si el correo aún no ha sido confirmado.
  * `GET /verify-email/{id}/{hash}`: Enlace firmado (`middleware: signed`) que procesa la validación del correo del usuario.
* **Confirmación Intermedia de Contraseña:**
  * `GET /confirm-password`: Solicita reingresar la contraseña antes de permitir acceder a secciones altamente críticas.
* **Actualización de Seguridad:**
  * `PUT /password`: Cambia y actualiza la contraseña del usuario logueado en su perfil de manera encriptada.
* **Cierre de Sesión (Logout):**
  * `POST /logout`: Llama a `AuthenticatedSessionController@destroy`.
    * **¿Qué hace en detalle?** Ejecuta el logout de la sesión web en el servidor, destruye los datos de la sesión actual en el almacenamiento (`session()->invalidate()`) y regenera el token CSRF (`session()->regenerateToken()`) para máxima seguridad, impidiendo ataques de robo de sesión y redireccionando de vuelta a la página principal `/`.
