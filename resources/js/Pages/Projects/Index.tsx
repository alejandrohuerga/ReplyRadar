import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Link, useForm } from '@inertiajs/react';

type Project = {
    id: number;
    name: string;
    description: string;
    keywords_count: number;
    posts_count: number;
    is_active: boolean;
};

type Props = { projects: Project[]; canCreate: boolean };

export default function ProjectsIndex({ projects, canCreate }: Props) {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        description: '',
    });

    const submit = (e: React.FormEvent) => {
        e.preventDefault();
        post(route('projects.store'), { onSuccess: () => reset() });
    };

    return (
        <AuthenticatedLayout>
            <div className="mb-8">
                <h1 className="text-2xl font-bold text-gray-900">Proyectos</h1>
                <p className="text-sm text-gray-500 mt-1">Cada proyecto agrupa keywords relacionadas</p>
            </div>

            <div className="grid lg:grid-cols-3 gap-8">
                {/* Formulario nuevo proyecto */}
                <div className="lg:col-span-1">
                    <div className="bg-white rounded-xl border border-gray-200 p-5">
                        <h2 className="text-base font-semibold text-gray-900 mb-4">Nuevo proyecto</h2>
                        {!canCreate && (
                            <div className="mb-4 p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-700">
                                Has alcanzado el límite de tu plan.{' '}
                                <Link href={route('billing.plans')} className="underline font-medium">Haz upgrade</Link>
                            </div>
                        )}
                        <form onSubmit={submit} className="space-y-3">
                            <div>
                                <input
                                    type="text"
                                    placeholder="Nombre del proyecto"
                                    value={data.name}
                                    onChange={e => setData('name', e.target.value)}
                                    disabled={!canCreate}
                                    className="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-300 disabled:opacity-50"
                                />
                                {errors.name && <p className="text-xs text-red-500 mt-1">{errors.name}</p>}
                            </div>
                            <div>
                                <textarea
                                    placeholder="Descripción (opcional)"
                                    value={data.description}
                                    onChange={e => setData('description', e.target.value)}
                                    disabled={!canCreate}
                                    rows={3}
                                    className="w-full text-sm border border-gray-200 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-indigo-300 disabled:opacity-50 resize-none"
                                />
                            </div>
                            <button
                                type="submit"
                                disabled={processing || !canCreate}
                                className="w-full py-2 bg-indigo-600 text-white text-sm rounded-lg hover:bg-indigo-700 disabled:opacity-50 transition-colors"
                            >
                                {processing ? 'Creando...' : 'Crear proyecto'}
                            </button>
                        </form>
                    </div>
                </div>

                {/* Lista de proyectos */}
                <div className="lg:col-span-2 space-y-3">
                    {projects.length === 0 ? (
                        <div className="text-center py-16 bg-white rounded-xl border border-dashed border-gray-300">
                            <div className="text-4xl mb-3">📁</div>
                            <p className="text-gray-500 text-sm">Crea tu primer proyecto para empezar</p>
                        </div>
                    ) : (
                        projects.map(project => (
                            <div key={project.id} className="bg-white rounded-xl border border-gray-200 p-5 hover:border-indigo-300 transition-colors">
                                <div className="flex items-start justify-between">
                                    <div>
                                        <Link
                                            href={route('projects.show', project.id)}
                                            className="font-semibold text-gray-900 hover:text-indigo-600 transition-colors"
                                        >
                                            {project.name}
                                        </Link>
                                        {project.description && (
                                            <p className="text-sm text-gray-500 mt-1">{project.description}</p>
                                        )}
                                        <div className="flex gap-4 mt-3">
                                            <span className="text-xs text-gray-400">🔑 {project.keywords_count} keywords</span>
                                            <span className="text-xs text-gray-400">📊 {project.posts_count} posts</span>
                                        </div>
                                    </div>
                                    <div className="flex items-center gap-2">
                                        <Link
                                            href={route('projects.show', project.id)}
                                            className="text-sm px-3 py-1.5 bg-indigo-50 text-indigo-600 rounded-lg hover:bg-indigo-100 transition-colors"
                                        >
                                            Ver →
                                        </Link>
                                        <Link
                                            href={route('projects.destroy', project.id)}
                                            method="delete"
                                            as="button"
                                            className="text-sm px-3 py-1.5 text-gray-400 hover:text-red-500 transition-colors"
                                            onClick={e => { if (!confirm('¿Eliminar proyecto?')) e.preventDefault(); }}
                                        >
                                            ✕
                                        </Link>
                                    </div>
                                </div>
                            </div>
                        ))
                    )}
                </div>
            </div>
        </AuthenticatedLayout>
    );
}