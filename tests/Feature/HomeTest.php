<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class HomeTest extends TestCase
{
    use RefreshDatabase;

    public function test_empty_feed_shows_searchable_public_examples(): void
    {
        $this->actingAs(User::factory()->create());
        $this->get('/home')->assertOk()->assertSee('Proyecto de ejemplo')
            ->assertViewHas('projects', fn ($projects) => $projects->pluck('slug')->all() === ['ecocampus', 'fic-conecta', 'turno-facil', 'tutorias-entre-companeros'])
            ->assertDontSee('Aula Inteligente');
        foreach (['recent', 'popular'] as $tab) {
            $this->get('/home?tab='.$tab)->assertOk()->assertViewHas('projects', fn ($projects) => $projects->first()->slug === 'turno-facil');
        }
        $this->get('/home?search=ECOCAMPUS')->assertOk()
            ->assertViewHas('projects', fn ($projects) => $projects->total() === 1 && $projects->first()->slug === 'ecocampus');
        $this->get('/home?search=inexistente')->assertOk()->assertSee('No encontramos proyectos');
    }

    public function test_feed_search_and_sort_only_include_published_projects(): void
    {
        $user = User::factory()->create();
        $category = Category::create(['name' => 'Software', 'slug' => 'software']);
        $base = ['category_id' => $category->id, 'summary' => 'Comunidad FIC', 'description' => 'Descripción', 'status' => 'published'];
        $older = $user->projects()->create($base + ['title' => 'Alpha', 'slug' => 'alpha']);
        $older->created_at = now()->subDay();
        $older->save();
        $newer = $user->projects()->create($base + ['title' => 'Zeta', 'slug' => 'zeta']);
        $older->reactions()->create(['user_id' => $user->id, 'type' => 'like']);
        $user->projects()->create(array_merge($base, ['title' => 'Privado', 'slug' => 'privado', 'status' => 'draft']));
        $user->projects()->create(array_merge($base, ['title' => 'Archivado', 'slug' => 'archivado', 'status' => 'archived']));

        $this->actingAs($user);
        foreach (['explore' => [$older->id, $newer->id], 'popular' => [$older->id, $newer->id], 'recent' => [$newer->id, $older->id]] as $tab => $ids) {
            $this->get('/home?tab='.$tab)->assertOk()->assertViewHas('projects', fn ($projects) => $projects->pluck('id')->all() === $ids);
        }
        $this->get('/home?tab=popular&search=ZETA')->assertOk()
            ->assertViewHas('projects', fn ($projects) => $projects->pluck('id')->all() === [$newer->id]);
        $this->get('/home?search=Comunidad')->assertOk()->assertViewHas('projects', fn ($projects) => $projects->total() === 2);
        $this->get('/home?search=inexistente')->assertOk()->assertSee('No encontramos proyectos');
    }

    public function test_profile_updates_only_current_users_name_and_bio(): void
    {
        $user = User::factory()->create();
        $other = User::factory()->create();
        $this->actingAs($user)->patch('/profile', ['name' => 'Nuevo nombre', 'bio' => 'Mi biografía', 'id' => $other->id, 'role' => 'admin', 'email' => 'otro@example.com'])
            ->assertRedirect('/home')->assertSessionHas('profile_status');
        $this->assertDatabaseHas('users', ['id' => $user->id, 'name' => 'Nuevo nombre', 'bio' => 'Mi biografía', 'role' => 'student', 'email' => $user->email]);
        $this->assertSame($other->name, $other->fresh()->name);
        $this->patch('/profile', ['name' => ' ', 'bio' => str_repeat('a', 1001)])->assertSessionHasErrors(['name', 'bio'], null, 'profile');
        $this->assertSame('Mi biografía', $user->fresh()->bio);
        $this->patch('/profile', ['name' => 'Nuevo nombre', 'bio' => ''])->assertRedirect('/home');
        $this->assertNull($user->fresh()->bio);
    }

    public function test_profile_requires_verified_authentication(): void
    {
        $this->patch('/profile', ['name' => 'Nombre'])->assertRedirect('/login');
        $this->actingAs(User::factory()->unverified()->create())->patch('/profile', ['name' => 'Nombre'])->assertRedirect('/email/verify');
    }
}
