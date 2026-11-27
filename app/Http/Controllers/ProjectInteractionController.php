<?php

namespace App\Http\Controllers;

use App\Models\Comment;
use App\Models\Project;
use App\Models\CollaborationRequest;
use Illuminate\Http\Request;

class ProjectInteractionController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | REACCIONES
    |--------------------------------------------------------------------------
    */

    public function toggleReaction(Project $project)
    {
        abort_unless($project->status === 'published', 404);

        $user = auth()->user();

        $reaction = $project->reactions()
            ->where('user_id', $user->id)
            ->where('type', 'like')
            ->first();

        if ($reaction) {
            $reaction->delete();
        } else {
            $project->reactions()->create([
                'user_id' => $user->id,
                'type' => 'like',
            ]);
        }

        return back()->with('success', 'Reacción actualizada.');
    }


    /*
    |--------------------------------------------------------------------------
    | COMENTARIOS
    |--------------------------------------------------------------------------
    */

    public function storeComment(Request $request, Project $project)
    {
        abort_unless($project->status === 'published', 404);

        $validated = $request->validate([
            'body' => ['required', 'string', 'max:5000'],
            'parent_id' => ['nullable', 'integer', 'exists:comments,id'],
        ]);

        // Verificar que el comentario padre pertenece al mismo proyecto.
        if (!empty($validated['parent_id'])) {

            $parent = Comment::findOrFail($validated['parent_id']);

            abort_unless(
                $parent->project_id === $project->id,
                422
            );
        }

        $project->comments()->create([
            'user_id' => auth()->id(),
            'body' => $validated['body'],
            'parent_id' => $validated['parent_id'] ?? null,
        ]);

        return back()->with('success', 'Comentario publicado.');
    }


    public function deleteComment(Comment $comment)
    {
        abort_unless(
            $comment->user_id === auth()->id(),
            403
        );

        $project = $comment->project;

        $comment->delete();

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Comentario eliminado.');
    }


    /*
    |--------------------------------------------------------------------------
    | SOLICITUDES DE COLABORACIÓN
    |--------------------------------------------------------------------------
    */

    public function storeCollaboration(
        Request $request,
        Project $project
    ) {
        abort_unless($project->status === 'published', 404);

        // El propietario no puede solicitar colaborar en su proyecto.
        abort_if(
            $project->user_id === auth()->id(),
            403,
            'No puedes solicitar colaborar en tu propio proyecto.'
        );

        $validated = $request->validate([
            'message' => ['nullable', 'string', 'max:3000'],
        ]);

        $existing = $project->collaborationRequests()
            ->where('user_id', auth()->id())
            ->first();

        if ($existing) {

            // Permitir volver a solicitar si la anterior fue cancelada
            // o rechazada.
            if (in_array($existing->status, ['cancelled', 'rejected'])) {

                $existing->update([
                    'message' => $validated['message'] ?? null,
                    'status' => 'pending',
                ]);

                return back()->with(
                    'success',
                    'Tu solicitud de colaboración ha sido enviada nuevamente.'
                );
            }

            return back()->with(
                'error',
                'Ya tienes una solicitud para este proyecto.'
            );
        }

        $project->collaborationRequests()->create([
            'user_id' => auth()->id(),
            'message' => $validated['message'] ?? null,
            'status' => 'pending',
        ]);

        return back()->with(
            'success',
            'Tu solicitud de colaboración ha sido enviada.'
        );
    }


    public function cancelCollaboration(
        CollaborationRequest $collaborationRequest
    ) {
        abort_unless(
            $collaborationRequest->user_id === auth()->id(),
            403
        );

        abort_unless(
            $collaborationRequest->status === 'pending',
            422
        );

        $collaborationRequest->update([
            'status' => 'cancelled',
        ]);

        return back()->with(
            'success',
            'Has cancelado tu solicitud.'
        );
    }


    public function updateCollaborationStatus(
        Request $request,
        CollaborationRequest $collaborationRequest
    ) {
        $project = $collaborationRequest->project;

        // Solo el propietario del proyecto puede responder.
        abort_unless(
            $project->user_id === auth()->id(),
            403
        );

        $validated = $request->validate([
            'status' => ['required', 'in:accepted,rejected'],
        ]);

        abort_unless(
            $collaborationRequest->status === 'pending',
            422
        );

        $collaborationRequest->update([
            'status' => $validated['status'],
        ]);

        $message = $validated['status'] === 'accepted'
            ? 'Solicitud aceptada.'
            : 'Solicitud rechazada.';

        return back()->with('success', $message);
    }


    /*
    |--------------------------------------------------------------------------
    | ACTUALIZACIONES DEL PROYECTO
    |--------------------------------------------------------------------------
    */

    public function storeUpdate(Request $request, Project $project)
    {
        // Solo el propietario puede publicar actualizaciones.
        abort_unless(
            $project->user_id === auth()->id(),
            403
        );

        $validated = $request->validate([
            'title' => ['required', 'string', 'max:180'],
            'body' => ['required', 'string', 'max:10000'],
        ]);

        $project->updates()->create([
            'user_id' => auth()->id(),
            'title' => $validated['title'],
            'body' => $validated['body'],
        ]);

        return back()->with(
            'success',
            'Actualización publicada.'
        );
    }


    public function deleteUpdate(
        \App\Models\ProjectUpdate $update
    ) {
        abort_unless(
            $update->user_id === auth()->id(),
            403
        );

        $project = $update->project;

        $update->delete();

        return redirect()
            ->route('projects.show', $project)
            ->with('success', 'Actualización eliminada.');
    }
}