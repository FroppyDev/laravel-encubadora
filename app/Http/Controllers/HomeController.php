<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Services\PlaceholderProjects;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $filters = $request->validate([
            'search' => ['nullable', 'string', 'max:100'],
            'tab' => ['nullable', Rule::in(['explore', 'popular', 'recent'])],
        ]);
        $search = trim($filters['search'] ?? '');
        $tab = $filters['tab'] ?? 'explore';
        $query = Project::with(['user:id,name', 'category'])->withCount('reactions')->where('status', 'published');
        if ($search !== '') {
            $query->where(function ($query) use ($search) {
                $term = '%'.mb_strtolower($search).'%';
                $query->whereRaw('LOWER(title) LIKE ?', [$term])->orWhereRaw('LOWER(summary) LIKE ?', [$term]);
            });
        }
        match ($tab) {
            'popular' => $query->orderByDesc('reactions_count')->latest('created_at'),
            'recent' => $query->latest('created_at'),
            default => $query->orderBy('title'),
        };
        $showPlaceholders = ! Project::where('status', 'published')->exists();
        $projects = $showPlaceholders
            ? app(PlaceholderProjects::class)->paginate($search, $tab)
            : $query->orderByDesc('id')->paginate(12)->withQueryString();

        return view('home', compact('projects', 'search', 'tab', 'showPlaceholders'));
    }

    public function updateProfile(Request $request)
    {
        $data = $request->validateWithBag('profile', [
            'name' => ['required', 'string', 'max:100'],
            'bio' => ['nullable', 'string', 'max:1000'],
        ]);
        $user = $request->user();
        $user->name = $data['name'];
        $user->bio = $data['bio'] ?? null;
        $user->save();

        return redirect()->route('home')->with('profile_status', 'Tu perfil se actualizó correctamente.');
    }
}
