<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Comment;
use App\Models\Project;
use App\Models\Report;
use App\Models\Technology;
use App\Models\User;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Database\QueryException;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Notification;
use Illuminate\Support\Facades\URL;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class PlatformTest extends TestCase
{
    use RefreshDatabase;

    private function account(bool $verified = true): User
    {
        return User::factory()->create(['email' => fake()->unique()->userName().'@info.uas.edu.mx', 'email_verified_at' => $verified ? now() : null]);
    }

    private function project(?User $owner = null, string $status = 'published'): Project
    {
        $category = Category::firstOrCreate(['slug' => 'software'], ['name' => 'Software']);

        return ($owner ?? $this->account())->projects()->create(['category_id' => $category->id, 'title' => 'Proyecto', 'slug' => fake()->uuid(), 'summary' => 'Resumen', 'description' => 'Descripción', 'status' => $status]);
    }

    private function registration(string $email): array
    {
        return ['name' => 'Estudiante', 'email' => $email, 'password' => 'Password123!', 'password_confirmation' => 'Password123!', 'role' => 'admin', 'email_verified_at' => now()];
    }

    public function test_registration_normalizes_email_and_never_trusts_role_or_verification(): void
    {
        Notification::fake();
        $this->postJson('/api/v1/register', $this->registration('  Alumna@INFO.UAS.EDU.MX  '))->assertCreated()->assertJsonMissingPath('token')->assertJsonMissingPath('data.password');
        $user = User::firstOrFail();
        $this->assertSame('alumna@info.uas.edu.mx', $user->email);
        $this->assertSame('student', $user->role);
        $this->assertNull($user->email_verified_at);
        Notification::assertSentTo($user, VerifyEmail::class);
        $this->postJson('/api/v1/register', $this->registration('ALUMNA@info.uas.edu.mx'))->assertUnprocessable();
    }

    public function test_external_and_deceptive_domains_are_rejected(): void
    {
        foreach (['a@gmail.com', 'a@info.uas.edu.mx.otro.com', 'a@sub.info.uas.edu.mx'] as $email) {
            $this->postJson('/api/v1/register', $this->registration($email))->assertUnprocessable()->assertJsonValidationErrors('email');
        }
        $this->assertDatabaseCount('users', 0);
    }

    public function test_real_sanctum_token_login_and_logout(): void
    {
        $user = $this->account();
        $this->getJson('/api/v1/user')->assertUnauthorized();
        $this->postJson('/api/v1/login', ['email' => $user->email, 'password' => 'wrong', 'device_name' => 'Android'])->assertUnprocessable()->assertJsonMissingPath('token');
        $token = $this->postJson('/api/v1/login', ['email' => strtoupper($user->email), 'password' => 'password', 'device_name' => 'Android'])->assertOk()->json('token');
        $other = $user->createToken('other')->plainTextToken;
        $this->app['auth']->forgetGuards();
        $this->withToken($token)->getJson('/api/v1/user')->assertOk()->assertJsonPath('data.id', $user->id);
        $this->app['auth']->forgetGuards();
        $this->withToken($token)->postJson('/api/v1/logout')->assertNoContent();
        $this->app['auth']->forgetGuards();
        $this->withToken($token)->getJson('/api/v1/user')->assertUnauthorized();
        $this->app['auth']->forgetGuards();
        $this->withToken($other)->getJson('/api/v1/user')->assertOk();
    }

    public function test_unverified_accounts_cannot_interact(): void
    {
        $p = $this->project();
        Sanctum::actingAs($this->account(false));
        foreach (['/projects', "/projects/$p->id/comments", "/projects/$p->id/collaboration-requests", '/reports'] as $path) {
            $this->postJson('/api/v1'.$path, [])->assertForbidden();
        }
        $this->putJson("/api/v1/projects/$p->id/reaction")->assertForbidden();
    }

    public function test_drafts_are_private_and_only_owner_can_edit_or_archive(): void
    {
        $owner = $this->account();
        $p = $this->project($owner, 'draft');
        $this->getJson('/api/v1/projects')->assertJsonCount(0, 'data');
        $this->getJson("/api/v1/projects/$p->id")->assertForbidden();
        Sanctum::actingAs($this->account());
        $this->patchJson("/api/v1/projects/$p->id", ['title' => 'Hack'])->assertForbidden();
        $this->deleteJson("/api/v1/projects/$p->id")->assertForbidden();
        Sanctum::actingAs($owner);
        $this->getJson("/api/v1/projects/$p->id")->assertOk();
        $this->patchJson("/api/v1/projects/$p->id", ['status' => 'published', 'user_id' => 123])->assertOk();
        $this->assertSame($owner->id, $p->fresh()->user_id);
        $this->getJson('/api/v1/projects')->assertJsonCount(1, 'data');
        $this->deleteJson("/api/v1/projects/$p->id")->assertNoContent();
        $this->assertDatabaseHas('projects', ['id' => $p->id, 'status' => 'archived']);
    }

    public function test_project_creation_and_technologies(): void
    {
        $p = $this->project();
        $tech = Technology::create(['name' => 'Kotlin', 'slug' => 'kotlin']);
        Sanctum::actingAs($user = $this->account());
        $this->postJson('/api/v1/projects', ['title' => 'App', 'summary' => 'Una app', 'description' => 'Descripción', 'category_id' => $p->category_id, 'technology_ids' => [$tech->id], 'user_id' => $p->user_id])->assertCreated()->assertJsonPath('data.user_id', $user->id)->assertJsonCount(1, 'data.technologies');
        $this->assertSame(1, $tech->projects()->count());
    }

    public function test_reactions_are_idempotent_and_removable(): void
    {
        $p = $this->project();
        Sanctum::actingAs($user = $this->account());
        for ($i = 0; $i < 2; $i++) {
            $this->putJson("/api/v1/projects/$p->id/reaction", ['type' => 'like'])->assertOk();
        }
        $this->assertDatabaseCount('project_reactions', 1);
        $this->assertSame(1, $user->projectReactions()->count());
        $this->deleteJson("/api/v1/projects/$p->id/reaction")->assertNoContent();
        $this->assertDatabaseCount('project_reactions', 0);
    }

    public function test_collaboration_transitions_and_permissions(): void
    {
        $p = $this->project($owner = $this->account());
        Sanctum::actingAs($owner);
        $this->postJson("/api/v1/projects/$p->id/collaboration-requests")->assertUnprocessable();
        Sanctum::actingAs($student = $this->account());
        $id = $this->postJson("/api/v1/projects/$p->id/collaboration-requests", ['message' => 'Quiero participar'])->assertCreated()->json('data.id');
        $this->postJson("/api/v1/projects/$p->id/collaboration-requests")->assertConflict();
        $this->patchJson("/api/v1/collaboration-requests/$id", ['status' => 'accepted'])->assertForbidden();
        Sanctum::actingAs($this->account());
        $this->patchJson("/api/v1/collaboration-requests/$id", ['status' => 'rejected'])->assertForbidden();
        $this->patchJson("/api/v1/collaboration-requests/$id", ['status' => 'cancelled'])->assertForbidden();
        Sanctum::actingAs($owner);
        $this->patchJson("/api/v1/collaboration-requests/$id", ['status' => 'rejected'])->assertOk();
        Sanctum::actingAs($student);
        $this->postJson("/api/v1/projects/$p->id/collaboration-requests")->assertCreated();
        Sanctum::actingAs($owner);
        $this->patchJson("/api/v1/collaboration-requests/$id", ['status' => 'accepted'])->assertOk();
        $this->assertSame('accepted', $student->collaborationRequests()->first()->status);
    }

    public function test_comments_restrict_parents_and_edits(): void
    {
        $p = $this->project();
        $other = $this->project();
        Sanctum::actingAs($user = $this->account());
        $id = $this->postJson("/api/v1/projects/$p->id/comments", ['body' => 'Hola'])->assertCreated()->json('data.id');
        $this->postJson("/api/v1/projects/$other->id/comments", ['body' => 'Respuesta', 'parent_id' => $id])->assertUnprocessable();
        $reply = $this->postJson("/api/v1/projects/$p->id/comments", ['body' => 'Respuesta', 'parent_id' => $id])->assertCreated()->json('data.id');
        $this->postJson("/api/v1/projects/$p->id/comments", ['body' => 'Nivel 3', 'parent_id' => $reply])->assertUnprocessable();
        $this->assertSame(1, Comment::find($id)->replies()->count());
        Sanctum::actingAs($this->account());
        $this->patchJson("/api/v1/comments/$id", ['body' => 'Otro'])->assertForbidden();
        $this->deleteJson("/api/v1/comments/$id")->assertForbidden();
    }

    public function test_reports_require_exactly_one_target_and_moderator(): void
    {
        $p = $this->project();
        Sanctum::actingAs($this->account());
        $comment = $this->postJson("/api/v1/projects/$p->id/comments", ['body' => 'Texto'])->json('data.id');
        $this->postJson('/api/v1/reports', ['reason' => 'spam'])->assertUnprocessable();
        $this->postJson('/api/v1/reports', ['reason' => 'spam', 'project_id' => $p->id, 'comment_id' => $comment])->assertUnprocessable();
        $id = $this->postJson('/api/v1/reports', ['reason' => 'spam', 'project_id' => $p->id])->assertCreated()->json('data.id');
        $this->postJson('/api/v1/reports', ['reason' => 'spam', 'project_id' => $p->id])->assertConflict();
        $this->getJson('/api/v1/reports')->assertForbidden();
        $this->patchJson("/api/v1/reports/$id", ['status' => 'reviewed'])->assertForbidden();
        $mod = $this->account();
        $mod->role = 'moderator';
        $mod->save();
        Sanctum::actingAs($mod);
        $this->patchJson("/api/v1/reports/$id", ['status' => 'reviewed'])->assertOk()->assertJsonPath('data.reviewed_by', $mod->id);
        $this->assertSame($mod->id, Report::find($id)->reviewer->id);
    }

    public function test_verification_requires_valid_unexpired_signature(): void
    {
        $user = $this->account(false);
        $params = ['id' => $user->id, 'hash' => sha1($user->email)];
        $url = URL::temporarySignedRoute('api.verification.verify', now()->addMinutes(60), $params);
        $this->getJson($url.'&tampered=1')->assertForbidden();
        $this->getJson(URL::temporarySignedRoute('api.verification.verify', now()->subMinute(), $params))->assertForbidden();
        $this->assertNull($user->fresh()->email_verified_at);
        $this->getJson($url)->assertOk();
        $this->assertNotNull($user->fresh()->email_verified_at);
    }

    public function test_web_registration_and_session_share_institutional_rules(): void
    {
        Notification::fake();
        $this->get('/login')->assertOk();
        $this->get('/register')->assertOk();
        $this->post('/register', $this->registration('bad@example.com'))->assertSessionHasErrors('email');
        $this->post('/register', $this->registration('web@INFO.UAS.EDU.MX'))->assertRedirect('/home');
        $this->assertAuthenticated();
        $this->get('/home')->assertRedirect('/email/verify');
        $this->post('/logout')->assertRedirect('/');
        $this->assertGuest();
        $this->post('/login', ['email' => 'web@info.uas.edu.mx', 'password' => 'Password123!'])->assertRedirect('/home');
    }

    public function test_postgres_report_check_constraint(): void
    {
        if (DB::getDriverName() !== 'pgsql') {
            $this->markTestSkipped('Requiere PostgreSQL: phpunit.pgsql.xml');
        }
        $user = $this->account();
        $this->assertSame('incubadora_test', DB::selectOne('select current_database() as name')->name);
        $this->expectException(QueryException::class);
        DB::table('reports')->insert(['user_id' => $user->id, 'reason' => 'Sin objetivo']);
    }

    public function test_updates_are_owned_and_relations_resolve(): void
    {
        $p = $this->project($owner = $this->account());
        Sanctum::actingAs($this->account());
        $this->postJson("/api/v1/projects/$p->id/updates", ['title' => 'Avance', 'body' => 'Listo'])->assertForbidden();
        Sanctum::actingAs($owner);
        $this->postJson("/api/v1/projects/$p->id/updates", ['title' => 'Avance', 'body' => 'Listo'])->assertCreated();
        $this->getJson("/api/v1/projects/$p->id/updates")->assertJsonCount(1, 'data');
        $this->assertSame($p->id, $owner->projectUpdates()->first()->project->id);
        $this->assertSame($p->id, $p->category->projects()->first()->id);
    }
}
