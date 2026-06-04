<?php
namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Project;
use Illuminate\Http\Request;

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        $projects = $request->user()
            ->projects()
            ->withCount(['keywords', 'posts'])
            ->latest()
            ->get();

        return view('projects.index', [
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

    public function show(Request $request, Project $project)
    {
        abort_if($project->user_id !== $request->user()->id, 403);

        $project->load('keywords');

        $sort = $request->input('sort', 'final_score');
        $sortColumn = in_array($sort, ['final_score', 'posted_at']) ? $sort : 'final_score';

        $posts = Post::whereHas('keyword', fn($q) =>
                $q->where('project_id', $project->id)
            )
            ->orderByDesc($sortColumn)
            ->limit(100)
            ->get([
                'id', 'title', 'subreddit', 'url', 'author',
                'reddit_score', 'num_comments', 'intent_score',
                'match_score', 'final_score', 'posted_at', 'keyword_id',
            ]);

        $blurredIds = collect([]);
        if ($request->user()->plan === 'free') {
            $blurredIds = $posts->take(5)->pluck('id');
        }

        return view('projects.show', [
            'project'  => $project,
            'posts'    => $posts,
            'blurredIds' => $blurredIds,
            'canAddKeyword' => $this->canAddKeyword($request->user(), $project),
            'canExport' => $request->user()->isPro(),
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

