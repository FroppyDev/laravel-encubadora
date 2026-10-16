<?php

namespace App\Services;

use App\Models\Category;
use App\Models\Project;
use App\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class PlaceholderProjects
{
    public function paginate(string $search, string $tab): LengthAwarePaginator
    {
        $categories = [1 => 'Servicios estudiantiles', 2 => 'Sustentabilidad', 3 => 'Educación', 4 => 'Internet de las cosas'];
        $rows = json_decode(file_get_contents(resource_path('data/placeholder-projects.json')), true, 512, JSON_THROW_ON_ERROR);
        $projects = collect($rows)
            ->filter(fn ($row) => $row['status'] === 'published' && $row['visibility'] === 'public' && $row['deleted_at'] === null)
            ->filter(fn ($row) => $search === '' || str_contains(mb_strtolower($row['title'].' '.$row['summary']), mb_strtolower($search)))
            ->map(function ($row) use ($categories) {
                $project = new Project;
                $project->forceFill($row + ['reactions_count' => 0, 'is_placeholder' => true]);
                $project->setRelation('user', new User(['name' => 'Estudiante de ejemplo '.$row['user_id']]));
                $project->setRelation('category', new Category(['name' => $categories[$row['category_id']]]));

                return $project;
            });
        $projects = $tab === 'explore'
            ? $projects->sortBy('title', SORT_NATURAL | SORT_FLAG_CASE)
            : $projects->sortByDesc('created_at');
        // Los ejemplos tienen cero reacciones; Popular desempata por fecha, como el listado real.
        $page = LengthAwarePaginator::resolveCurrentPage();

        return new LengthAwarePaginator($projects->values()->forPage($page, 12), $projects->count(), 12, $page, [
            'path' => LengthAwarePaginator::resolveCurrentPath(),
            'query' => request()->query(),
        ]);
    }
}
