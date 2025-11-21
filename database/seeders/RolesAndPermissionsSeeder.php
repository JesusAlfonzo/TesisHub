<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User; // Importamos User para crear al Super Admin

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Limpiar cache (Siempre necesario)
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        //
        // PERMISOS
        //

        // GRUPO: ESTUDIANTE
        Permission::create(['name' => 'crear tesis']);
        Permission::create(['name' => 'ver mis tesis']);
        Permission::create(['name' => 'editar tesis propia']);
        Permission::create(['name' => 'eliminar tesis propia']);

        // GRUPO: DOCENTE/TUTOR
        Permission::create(['name' => 'ver tesis asignadas']); // Para no ver todas, solo las suyas
        Permission::create(['name' => 'comentar tesis']);      // Feedback intermedio
        Permission::create(['name' => 'aprobar defensa']);     // "Visto bueno" académico

        // GRUPO: COORDINADOR
        Permission::create(['name' => 'publicar tesis']);         // Aprobación final para biblioteca
        Permission::create(['name' => 'ver metricas globales']);  // Dashboard admin
        Permission::create(['name' => 'descargar reportes']);     // Excel/PDF

        // Gestión de Usuarios
        Permission::create(['name' => 'crear usuarios']);
        Permission::create(['name' => 'editar usuarios']);
        Permission::create(['name' => 'desactivar usuarios']);

        // GRUPO: PUBLICO
        Permission::create(['name' => 'ver repositorio publico']);

        //
        // ROLES Y ASIGNACIÓN
        //

        // 1. ROL: ESTUDIANTE
        // "Sube su tesis, edita su propia tesis, ve tesis públicas"
        $roleEstudiante = Role::create(['name' => 'estudiante']);
        $roleEstudiante->givePermissionTo([
            'ver repositorio publico',
            'crear tesis',
            'ver mis tesis',
            'editar tesis propia',
            'eliminar tesis propia',
        ]);

        // 2. ROL: TUTOR (DOCENTE)
        // "Revisa tesis asignadas, comenta, aprueba para defensa"
        $roleTutor = Role::create(['name' => 'tutor']);
        $roleTutor->givePermissionTo([
            'ver repositorio publico',
            'ver tesis asignadas',
            'comentar tesis',
            'aprobar defensa',
        ]);

        // 3. ROL: COORDINADOR
        // "Gestiona usuarios, aprueba publicación final, reportes"
        $roleCoordinador = Role::create(['name' => 'coordinador']);
        $roleCoordinador->givePermissionTo([
            'ver repositorio publico',
            'ver metricas globales',
            'publicar tesis',        // Diferente a aprobar defensa
            'descargar reportes',
            'crear usuarios',
            'editar usuarios',
            'desactivar usuarios',
        ]);

        // 4. ROL: SUPER ADMIN
        // "Acceso total al sistema"
        $roleSuperAdmin = Role::create(['name' => 'super-admin']);
        $roleSuperAdmin->givePermissionTo(Permission::all()); // ¡Poder absoluto!

        //
        // CREACIÓN DE USUARIO ADMIN
        //

        // Creamos el usuario para ti, para que puedas entrar al sistema ya mismo.
        $user = User::firstOrCreate(
            ['email' => 'admin@iujo.edu.ve'], // Evita duplicados si corres db:seed varias veces
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'), // Cambia esto en producción
                'cedula' => '00000000',
                'is_active' => true,
                'carrera_id' => null, // El super admin no tiene carrera
            ]
        );

        $user->assignRole($roleSuperAdmin);
    }
}
