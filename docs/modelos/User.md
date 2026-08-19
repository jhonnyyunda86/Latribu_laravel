# Documentación del Modelo: User

**Ruta física:** `app/Models/User.php`

El modelo `User` gestiona la información de perfil, credenciales de acceso y autorización de roles para administradores, meseros y clientes del restaurante.

---

## 1. Código Fuente Explicado

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class User extends Authenticatable
{
    use HasFactory, Notifiable;

    // Atributos asignables masivamente
    protected $fillable = [
        'name',
        'email',
        'password',
        'role_id',
    ];

    // Atributos que se ocultan en la serialización JSON
    protected $hidden = [
        'password',
        'remember_token',
    ];

    // Conversión de tipos automática
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    // Relación con el Rol de Usuario
    public function role(): BelongsTo
    {
        return $this->belongsTo(Role::class, 'role_id');
    }

    // Relación con los Pedidos hechos por el usuario
    public function pedidos(): HasMany
    {
        return $this->hasMany(Pedido::class, 'user_id');
    }

    // Relación con las Reservas del usuario
    public function reservas(): HasMany
    {
        return $this->hasMany(Reserva::class, 'user_id');
    }
}
```

---

## 2. Explicación de la Lógica del Código

### 1. `Authenticatable`
El modelo hereda de `Illuminate\Foundation\Auth\User` en lugar de la clase básica `Model`, lo que le permite interactuar directamente con las interfaces del sistema de inicio de sesión, guardado de sesión e inicio de sesión de Laravel.

### 2. `$hidden` (Línea 23)
Oculta atributos sensibles (como la contraseña hash y el token de recordar sesión) cuando el modelo se convierte a arreglos o formato JSON (por ejemplo, en peticiones API o respuestas frontend en Javascript).

### 3. `$casts` (Línea 29)
* **`email_verified_at => datetime`**: Parsea la fecha a objeto Carbon.
* **`password => hashed`**: Encripta de manera automática la contraseña usando el algoritmo seguro bcrypt al guardarse.

### 4. Relaciones Eloquent
* **`role()`**: Relación `BelongsTo` con `Role`. Conecta al usuario con su rol (`admin`, `mesero`, `cliente`) para definir sus privilegios.
* **`pedidos()`** y **`reservas()`**: Relaciones `HasMany` que agrupan todas las compras y reservas del usuario.
