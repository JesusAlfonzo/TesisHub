<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Limpiar caché de permisos
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Helper para crear permisos sin duplicados
        $createPermission = function ($name) {
            Permission::firstOrCreate(['name' => $name, 'guard_name' => 'web']);
        };

        //
        // 1. Permisos
        //

        // Estudiantes
        $createPermission('crear tesis');
        $createPermission('ver mis tesis');
        $createPermission('editar tesis propia');
        $createPermission('eliminar tesis propia');

        // Docente/Tutor
        $createPermission('ver tesis asignadas');
        $createPermission('comentar tesis');
        $createPermission('aprobar defensa');
        $createPermission('evaluar tesis');

        // Coordinador
        $createPermission('publicar tesis');
        $createPermission('ver metricas globales');
        $createPermission('descargar reportes');

        // Gestion de Usuarios
        $createPermission('crear usuarios');
        $createPermission('editar usuarios');
        $createPermission('desactivar usuarios');

        // Publico
        $createPermission('ver repositorio publico');

        //
        // 2. Roles y Asignacion
        //

        // Estudiante
        $roleEstudiante = Role::firstOrCreate(['name' => 'estudiante', 'guard_name' => 'web']);
        $roleEstudiante->syncPermissions([
            'ver repositorio publico',
            'crear tesis',
            'ver mis tesis',
            'editar tesis propia',
            'eliminar tesis propia',
        ]);

        // Docente/Tutor
        $roleTutor = Role::firstOrCreate(['name' => 'tutor', 'guard_name' => 'web']);
        $roleTutor->syncPermissions([
            'ver repositorio publico',
            'ver tesis asignadas',
            'comentar tesis',
            'aprobar defensa',
            'evaluar tesis',
        ]);

        // Coordinador
        $roleCoordinador = Role::firstOrCreate(['name' => 'coordinador', 'guard_name' => 'web']);
        $roleCoordinador->syncPermissions([
            'ver repositorio publico',
            'ver metricas globales',
            'publicar tesis',
            'descargar reportes',
            'crear usuarios',
            'editar usuarios',
            'desactivar usuarios',
            'evaluar tesis',
        ]);

        // Super Admin
        $roleSuperAdmin = Role::firstOrCreate(['name' => 'super-admin', 'guard_name' => 'web']);
        $roleSuperAdmin->syncPermissions(Permission::all());

        //
        // 3. Creacion de Usuario Super Admin (Primer Usuario del sistema)
        //

        $user = User::firstOrCreate(
            ['email' => 'admin@iujo.edu.ve'],
            [
                'name' => 'Super Admin',
                'password' => bcrypt('password'),
                'cedula' => '00000000',
                'is_active' => true,
                'carrera_id' => null,
            ]
        );

        if (!$user->hasRole('super-admin')) {
            $user->assignRole($roleSuperAdmin);
        }
    }
}
