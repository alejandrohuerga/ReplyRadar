import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import OpportunityCard from '@/Components/ReplyRadar/OpportunityCard';
import { Link } from '@inertiajs/react';
import { useState } from 'react';

type Stats = {
    total_posts: number;
    hot_count: number;
    avg_score: number;
    top_subreddit: string;
};

type Props = {
    projects: any[];
    opportunities: any[];
    stats: Stats;
};

export default function Dashboard({ projects, opportunities, stats }: Props) {
    const [filter, setFilter] = useState<'all' | 'hot' | 'warm'>('all');
    const [search, setSearch] = useState('');

    const filtered = opportunities.filter(p => {
        const matchesFilter =
            filter === 'all' ? true :
            filter === 'hot' ? p.final_score >= 80 :
            p.final_score >= 60 && p.final_score < 80;

        const matchesSearch = search === '' ||
            p.title.toLowerCase().includes(search.toLowerCase()) ||
            p.subreddit.toLowerCase().includes(search.toLowerCase());

        return matchesFilter && matchesSearch;
    });

    return (
        <AuthenticatedLayout>
            {/* Header */}
            <div className="flex items-center justify-between mb-8">
                <div>
                    <h1 className="text-2xl font-bold text-gray-900">Oportunidades</h1>
                    <p className="text-sm text-gray-500 mt-1">
                        Conversaciones rankeadas por intención de compra real
                    </p>
                </div>
                <Link
                    href={route('projects.index')}
                    className="px-4 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors"
                >
                    + Nuevo proyecto
                </Link>
            </div>

            {/* Stats */}
            <div className="grid grid-cols-2 sm:grid-cols-4 gap-4 mb-8">
                {[
                    { label: 'Total detectadas', value: stats.total_posts, color: 'text-gray-900' },
                    { label: '🔥 Hot (score 80+)', value: stats.hot_count, color: 'text-red-600' },
                    { label: 'Score medio', value: stats.avg_score ?? 0, color: 'text-indigo-600' },
                    { label: 'Top subreddit', value: stats.top_subreddit ? `r/${stats.top_subreddit}` : '—', color: 'text-purple-600' },
                ].map(stat => (
                    <div key={stat.label} className="bg-white rounded-xl border border-gray-200 p-4">
                        <div className="text-xs text-gray-500 mb-1">{stat.label}</div>
                        <div className={`text-xl font-bold ${stat.color}`}>{stat.value}</div>
                    </div>
                ))}
            </div>

            {/* Sin proyectos */}
            {projects.length === 0 && (
                <div className="text-center py-16 bg-white rounded-xl border border-dashed border-gray-300">
                    <div className="text-4xl mb-3">🎯</div>
                    <h3 className="text-lg font-medium text-gray-900 mb-2">Crea tu primer proyecto</h3>
                    <p className="text-sm text-gray-500 mb-6">
                        Añade keywords y ReplyRadar detectará oportunidades en Reddit automáticamente.
                    </p>
                    <Link
                        href={route('projects.index')}
                        className="px-6 py-2.5 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 transition-colors"
                    >
                        Crear proyecto
                    </Link>
                </div>
            )}

            {/* Filtros */}
            {opportunities.length > 0 && (
                <>
                    <div className="flex items-center gap-3 mb-4 flex-wrap">
                        <input
                            type="text"
                            placeholder="Buscar oportunidades..."
                            value={search}
                            onChange={e => setSearch(e.target.value)}
                            className="flex-1 min-w-48 text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-300"
                        />
                        {(['all', 'hot', 'warm'] as const).map(f => (
                            <button
                                key={f}
                                onClick={() => setFilter(f)}
                                className={`text-sm px-4 py-2 rounded-lg border transition-colors ${
                                    filter === f
                                        ? 'bg-indigo-600 text-white border-indigo-600'
                                        : 'bg-white text-gray-600 border-gray-200 hover:border-indigo-300'
                                }`}
                            >
                                {f === 'all' ? 'Todas' : f === 'hot' ? '🔥 Hot' : '⚡ Warm'}
                            </button>
                        ))}
                        <span className="text-sm text-gray-400">{filtered.length} resultados</span>
                    </div>

                    {/* Lista */}
                    <div className="grid gap-3">
                        {filtered.length === 0 ? (
                            <div className="text-center py-12 text-gray-400">
                                No hay oportunidades con estos filtros
                            </div>
                        ) : (
                            filtered.map(post => (
                                <OpportunityCard key={post.id} post={post} />
                            ))
                        )}
                    </div>
                </>
            )}
        </AuthenticatedLayout>
    );
}