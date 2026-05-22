import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import OpportunityCard from '@/Components/ReplyRadar/OpportunityCard';
import { Link, useForm } from '@inertiajs/react';
import { useState } from 'react';


type Props = {
    project: any;
    posts: any[];
    canAddKeyword: boolean;
    canExport: boolean;
};

export default function ProjectShow({ project, posts, canAddKeyword,canExport }: Props) {
    const [sortBy, setSortBy] = useState<'final_score' | 'posted_at'>('final_score');

    const { data, setData, post, processing, errors, reset } = useForm({ term: '' });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('keywords.store', project.id), { onSuccess: () => reset() });
    };

    const sorted = [...posts].sort((a, b) =>
        sortBy === 'final_score'
            ? b.final_score - a.final_score
            : new Date(b.posted_at).getTime() - new Date(a.posted_at).getTime()
    );

    return (
        <AuthenticatedLayout>
            {/* Breadcrumb */}
            <div className="flex items-center gap-2 text-sm text-gray-500 mb-6">
                <Link href={route('projects.index')} className="hover:text-indigo-600">Proyectos</Link>
                <span>/</span>
                <span className="text-gray-900 font-medium">{project.name}</span>
            </div>

            <div className="grid lg:grid-cols-4 gap-8">
                {/* Sidebar keywords */}
                <div className="lg:col-span-1 space-y-4">
                    <div className="bg-white rounded-xl border border-gray-200 p-4">
                        <h3 className="text-sm font-semibold text-gray-900 mb-3">Keywords</h3>

                        {/* Añadir keyword */}
                        <form onSubmit={submit} className="mb-4">
                            <div className="flex gap-2">
                                <input
                                    type="text"
                                    placeholder="Nueva keyword..."
                                    value={data.term}
                                    onChange={e => setData('term', e.target.value)}
                                    disabled={!canAddKeyword}
                                    className="flex-1 text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-300 disabled:opacity-50"
                                />
                                <button
                                    type="submit"
                                    disabled={processing || !canAddKeyword}
                                    className="px-3 py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition-colors"
                                >
                                    {processing ? '...' : '+'}
                                </button>
                            </div>
                            {errors.term && <p className="text-xs text-red-500 mt-1">{errors.term}</p>}
                            {!canAddKeyword && (
                                <p className="text-xs text-amber-600 mt-1">
                                    Límite alcanzado.{' '}
                                    <Link href={route('billing.plans')} className="underline">Upgrade</Link>
                                </p>
                            )}
                        </form>

                        {/* Lista keywords */}
                        <div className="space-y-2">
                            {project.keywords.length === 0 ? (
                                <p className="text-xs text-gray-400 text-center py-4">Sin keywords aún</p>
                            ) : (
                                project.keywords.map((kw: any) => (
                                    <div key={kw.id} className="flex items-center justify-between gap-2 p-2 rounded-lg bg-gray-50">
                                        <span className={`text-sm ${kw.is_active ? 'text-gray-800' : 'text-gray-400 line-through'}`}>
                                            {kw.term}
                                        </span>
                                        <div className="flex gap-1">
                                            <Link
                                                href={route('keywords.toggle', kw.id)}
                                                method="patch"
                                                as="button"
                                                className="text-xs text-gray-400 hover:text-indigo-500 transition-colors"
                                                title={kw.is_active ? 'Pausar' : 'Activar'}
                                            >
                                                {kw.is_active ? '⏸' : '▶'}
                                            </Link>
                                            <Link
                                                href={route('keywords.destroy', kw.id)}
                                                method="delete"
                                                as="button"
                                                className="text-xs text-gray-400 hover:text-red-500 transition-colors"
                                                onClick={(e) => {
                                                    if (!confirm('¿Eliminar keyword?')) e.preventDefault();
                                                }}
                                            >
                                                ✕
                                            </Link>
                                        </div>
                                    </div>
                                ))
                            )}
                        </div>
                    </div>
                </div>

                {/* Posts / Oportunidades */}
                <div className="lg:col-span-3">
                    <div className="flex items-center justify-between mb-4">
                        <h2 className="text-lg font-semibold text-gray-900">
                            {posts.length} oportunidades detectadas
                        </h2>
                        <select
                            value={sortBy}
                            onChange={e => setSortBy(e.target.value as any)}
                            className="text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-300"
                        >   
                            <option value="final_score">Ordenar por score</option>
                            <option value="posted_at">Ordenar por fecha</option>
                        </select>
                        {canExport ? (
                            <Link
                                href={`/export/posts?project_id=${project.id}`}
                                className="px-4 py-2 text-sm bg-green-600 text-white rounded-lg hover:bg-green-700 transition-colors"
                            >
                                ↓ Export CSV
                            </Link>
                        ) : (
                            <Link
                                href={route('billing.plans')}
                                className="px-4 py-2 text-sm bg-gray-100 text-gray-500 rounded-lg hover:bg-gray-200 transition-colors"
                                title="Disponible en plan Pro"
                            >
                                🔒 Export CSV
                            </Link>
                        )}
                    </div>

                    {posts.length === 0 ? (
                        <div className="text-center py-16 bg-white rounded-xl border border-dashed border-gray-300">
                            <div className="text-4xl mb-3">🔍</div>
                            <p className="text-gray-500 text-sm">
                                Añade una keyword para empezar a detectar oportunidades
                            </p>
                        </div>
                    ) : (
                        <div className="grid gap-3">
                            {sorted.map(post => (
                                <OpportunityCard key={post.id} post={post} />
                            ))}
                        </div>
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}