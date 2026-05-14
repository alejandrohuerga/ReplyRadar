<?php
namespace App\Http\Controllers;

use App\Models\Project;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class ProjectController extends Controller
{
    public function index(Request $request): Response
    {
        $projects = $request->user()
            ->projects()
            ->withCount(['keywords', 'posts'])
            ->latest()
            ->get();

        return Inertia::render('Projects/Index', [
            'projects' => $projects,
            'canCreate' => $this->canCreateProject($request->user()),
        ]);
    }

    public function store(Request $request)
    {
        if (!$this->canCreateProject($request->user())) {
            return back()->withErrors(['limit' => 'Has alcanzado el límite de proyectos de tu plan.']);
        }

        $validated = $request->validate([
            'name'        => 'required|string|max:100',
            'description' => 'nullable|string|max:500',
        ]);

        $request->user()->projects()->create($validated);

        return redirect()->route('projects.index')->with('success', 'Proyecto creado.');
    }

    public function show(Request $request, Project $project): Response
    {
        // Asegura que el proyecto pertenece al usuario
        abort_if($project->user_id !== $request->user()->id, 403);

        $project->load('keywords');

        $posts = \App\Models\Post::whereHas('keyword', fn($q) =>
                $q->where('project_id', $project->id)
            )
            ->orderByDesc('final_score')
            ->limit(100)
            ->get([
                'id', 'title', 'subreddit', 'url', 'author',
                'reddit_score', 'num_comments', 'intent_score',
                'match_score', 'final_score', 'posted_at', 'keyword_id',
            ]);

        return Inertia::render('Projects/Show', [
            'project'  => $project,
            'posts'    => $posts,
            'canAddKeyword' => $this->canAddKeyword($request->user(), $project),
        ]);
    }

    public function destroy(Request $request, Project $project)
    {
        abort_if($project->user_id !== $request->user()->id, 403);
        $project->delete();
        return redirect()->route('projects.index')->with('success', 'Proyecto eliminado.');
    }

    private function canCreateProject($user): bool
    {
        $limit = config("replyradar.plans.{$user->plan}.max_projects");
        return $user->projects()->count() < $limit;
    }

    private function canAddKeyword($user, Project $project): bool
    {
        $limit   = config("replyradar.plans.{$user->plan}.max_keywords");
        $current = \App\Models\Keyword::whereHas('project', fn($q) =>
            $q->where('user_id', $user->id)
        )->count();
        return $current < $limit;
    }
}