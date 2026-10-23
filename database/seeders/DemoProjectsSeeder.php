<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DemoProjectsSeeder extends Seeder
{
    public function run(): void
    {
        if (! app()->environment('local')) {
            $this->command?->warn(
                'Este seeder solo se puede ejecutar con APP_ENV=local.'
            );

            return;
        }

        $password = 'DemoLocal123!';

        // Categorías
        $categories = [];

        foreach ([
            'software' => 'Software',
            'investigacion' => 'Investigación',
            'impacto-social' => 'Impacto social',
            'emprendimiento' => 'Emprendimiento',
        ] as $slug => $name) {
            $categories[$slug] = Category::firstOrCreate(
                ['slug' => $slug],
                [
                    'name' => $name,
                    'is_active' => true,
                ]
            );
        }

        // Usuarios
        $users = [];

        $accounts = [
            [
                'name' => 'Administrador Demo',
                'email' => 'admin.demo@info.uas.edu.mx',
                'role' => 'admin',
            ],
            [
                'name' => 'Moderador Demo',
                'email' => 'moderador.demo@info.uas.edu.mx',
                'role' => 'moderator',
            ],
            [
                'name' => 'Ana Demo',
                'email' => 'ana.demo@info.uas.edu.mx',
                'role' => 'student',
            ],
            [
                'name' => 'Luis Demo',
                'email' => 'luis.demo@info.uas.edu.mx',
                'role' => 'student',
            ],
            [
                'name' => 'Sofía Demo',
                'email' => 'sofia.demo@info.uas.edu.mx',
                'role' => 'student',
            ],
        ];

        foreach ($accounts as $account) {
            $user = User::firstOrCreate(
                ['email' => $account['email']],
                [
                    'name' => $account['name'],
                    'password' => Hash::make($password),
                ]
            );

            $user->role = $account['role'];
            $user->email_verified_at ??= now();
            $user->save();

            $users[$account['email']] = $user;
        }

        // Proyectos
        $projects = [
            [
                'owner' => 'ana.demo@info.uas.edu.mx',
                'category' => 'software',
                'slug' => 'demo-asesorias-fic',
                'title' => 'Asesorías FIC',
                'summary' => 'Conecta estudiantes con compañeros que ofrecen asesorías.',
                'description' => 'Una plataforma para consultar materias, '
                    . 'encontrar estudiantes que ofrecen asesorías y organizar '
                    . 'sesiones de apoyo académico.',
                'status' => 'published',
            ],
            [
                'owner' => 'luis.demo@info.uas.edu.mx',
                'category' => 'impacto-social',
                'slug' => 'demo-reciclaje-fic',
                'title' => 'Reciclaje FIC',
                'summary' => 'Promueve el reciclaje dentro de la facultad.',
                'description' => 'Una iniciativa para identificar puntos de '
                    . 'reciclaje, organizar jornadas de recolección y compartir '
                    . 'información sobre el manejo responsable de residuos.',
                'status' => 'published',
            ],
            [
                'owner' => 'sofia.demo@info.uas.edu.mx',
                'category' => 'investigacion',
                'slug' => 'demo-monitoreo-ambiental',
                'title' => 'Monitoreo ambiental del campus',
                'summary' => 'Estudia las condiciones ambientales mediante sensores.',
                'description' => 'Un proyecto para recopilar mediciones de '
                    . 'temperatura y humedad en distintas áreas del campus '
                    . 'y analizar su variación durante el día.',
                'status' => 'published',
            ],
            [
                'owner' => 'ana.demo@info.uas.edu.mx',
                'category' => 'emprendimiento',
                'slug' => 'demo-mercado-estudiantil',
                'title' => 'Mercado estudiantil',
                'summary' => 'Da a conocer emprendimientos de estudiantes.',
                'description' => 'Una propuesta para reunir productos y '
                    . 'servicios ofrecidos por estudiantes y fomentar '
                    . 'el emprendimiento universitario.',
                'status' => 'draft',
            ],
            [
                'owner' => 'luis.demo@info.uas.edu.mx',
                'category' => 'software',
                'slug' => 'demo-agenda-universitaria',
                'title' => 'Agenda universitaria',
                'summary' => 'Organiza tareas, entregas y actividades académicas.',
                'description' => 'Un prototipo de agenda para registrar '
                    . 'actividades académicas y consultar fechas de entrega. '
                    . 'Se conserva archivado como ejemplo de un proyecto terminado.',
                'status' => 'archived',
            ],
        ];

        foreach ($projects as $data) {
            $project = Project::firstOrNew([
                'slug' => $data['slug'],
            ]);

            // Inicializa el contenido solo cuando el proyecto es nuevo.
            if (! $project->exists) {
                $project->category_id = $categories[$data['category']]->id;
                $project->title = $data['title'];
                $project->summary = $data['summary'];
                $project->description = $data['description'];
                $project->status = $data['status'];
            }

            // Corrige también el propietario de proyectos creados
            // anteriormente con cuentas @example.test.
            $project->user_id = $users[$data['owner']]->id;
            $project->save();
        }

        $this->command?->info('Usuarios y proyectos de prueba cargados.');
    }
}