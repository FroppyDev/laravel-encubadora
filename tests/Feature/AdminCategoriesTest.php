<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminCategoriesTest extends TestCase
{
    use RefreshDatabase;

    private function account(string $role = 'admin'): User
    {
        return User::factory()->create(['role' => $role, 'email_verified_at' => now()]);
    }

    public function test_only_verified_admins_can_access_and_modify_categories(): void
    {
        $category = Category::create(['name' => 'Software', 'slug' => 'software']);
        $this->get('/admin')->assertRedirect('/login');
        foreach (['student', 'moderator'] as $role) {
            $this->actingAs($this->account($role))->get('/admin')->assertForbidden();
            $this->post('/admin/categories', ['name' => 'Otra', 'slug' => 'otra'])->assertForbidden();
            $this->put('/admin/categories/'.$category->id, ['name' => 'Otra', 'slug' => 'otra'])->assertForbidden();
            $this->patch('/admin/categories/'.$category->id.'/status', ['is_active' => false])->assertForbidden();
        }
        $user = $this->account();
        $user->forceFill(['email_verified_at' => null])->save();
        $this->actingAs($user)->get('/admin')->assertRedirect('/email/verify');
        $this->actingAs($this->account())->get('/admin')->assertOk()->assertSee('Organiza las ideas');
    }

    public function test_admin_creates_edits_and_reactivates_without_deleting_categories(): void
    {
        $this->actingAs($this->account());
        $this->post('/admin/categories', ['name' => 'Software', 'slug' => 'software'])->assertRedirect();
        $category = Category::firstOrFail();
        $this->post('/admin/categories', ['name' => 'Duplicada', 'slug' => 'software'])->assertSessionHasErrors('slug');
        $this->put('/admin/categories/'.$category->id, ['name' => 'Desarrollo', 'slug' => 'software'])->assertRedirect();
        $this->patch('/admin/categories/'.$category->id.'/status', ['is_active' => false])->assertRedirect();
        $this->assertFalse($category->refresh()->is_active);
        $this->getJson('/api/v1/categories')->assertJsonMissing(['slug' => 'software']);
        $this->actingAs($this->account())->patch('/admin/categories/'.$category->id.'/status', ['is_active' => true])->assertRedirect();
        $this->assertTrue($category->refresh()->is_active);
    }

    public function test_inactive_categories_preserve_existing_projects_and_reject_new_assignments(): void
    {
        $owner = $this->account('student');
        $category = Category::create(['name' => 'Software', 'slug' => 'software']);
        $project = $owner->projects()->create(['category_id' => $category->id, 'title' => 'Proyecto', 'slug' => 'proyecto', 'summary' => 'Resumen', 'description' => 'Descripción', 'status' => 'published']);
        $this->actingAs($this->account())->patch('/admin/categories/'.$category->id.'/status', ['is_active' => false])->assertRedirect();
        $this->actingAs($owner)->getJson('/api/v1/projects/'.$project->id)->assertOk()->assertJsonPath('data.category.id', $category->id);
        $this->withToken($owner->createToken('test')->plainTextToken);
        $data = ['title' => 'Nuevo', 'summary' => 'Resumen', 'description' => 'Descripción', 'category_id' => $category->id];
        $this->postJson('/api/v1/projects', $data)->assertUnprocessable()->assertJsonValidationErrors('category_id');
        $this->patchJson('/api/v1/projects/'.$project->id, ['title' => 'Actualizado', 'category_id' => $category->id])->assertOk();
        $other = Category::create(['name' => 'Otra', 'slug' => 'otra', 'is_active' => false]);
        $this->patchJson('/api/v1/projects/'.$project->id, ['category_id' => $other->id])->assertUnprocessable();
        $this->assertDatabaseHas('projects', ['id' => $project->id, 'category_id' => $category->id]);
    }
}
