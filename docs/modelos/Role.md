# Documentación del Modelo: Role

**Ruta física:** `app/Models/Role.php`

El modelo `Role` define el perfil del usuario para autorizar privilegios y accesos en el sistema (Ej: Administrador, Mesero, Cliente).

---

## 1. Código Fuente Explicado

```php
<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Role extends Model
{
    // Atributos asignables masivamente
    protected $fillable = [
        'nombre',
        'descripcion'
    ];

    // Relación con los Usuarios que poseen este rol
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'role_id');
    }
}
```

---

## 2. Explicación de la Lógica del Código

### 1. `$fillable` (Línea 10)
Permite ingresar el nombre del rol (como `admin`, `mesero` o `cliente`) y su descripción informativa.

### 2. Relaciones Eloquent
* **`users()`**: Relación `HasMany` con `User`. Permite consultar a todos los usuarios de la base de datos que pertenecen a una categoría de permisos en particular (Ej: listar a todos los meseros).
