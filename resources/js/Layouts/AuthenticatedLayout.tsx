import { Link, usePage } from '@inertiajs/react';
import { PropsWithChildren } from 'react';

export default function AuthenticatedLayout({ children }: PropsWithChildren) {
    const { auth } = usePage().props as any;

    return (
        <div className="min-h-screen bg-gray-50">
            <nav className="bg-white border-b border-gray-200">
                <div className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                    <div className="flex justify-between h-16 items-center">
                        {/* Logo */}
                        <div className="flex items-center gap-8">
                            <Link href="/dashboard" className="flex items-center gap-2">
                                <span className="text-xl font-bold text-indigo-600">🎯 ReplyRadar</span>
                            </Link>
                            <div className="hidden sm:flex gap-6">
                                <Link
                                    href={route('dashboard')}
                                    className="text-sm text-gray-600 hover:text-indigo-600 transition-colors"
                                >
                                    Dashboard
                                </Link>
                                <Link
                                    href={route('projects.index')}
                                    className="text-sm text-gray-600 hover:text-indigo-600 transition-colors"
                                >
                                    Proyectos
                                </Link>
                            </div>
                        </div>

                        {/* Right side */}
                        <div className="flex items-center gap-4">
                            <Link
                                href={route('billing.plans')}
                                className="text-xs font-medium px-3 py-1 rounded-full bg-indigo-50 text-indigo-600 hover:bg-indigo-100 transition-colors"
                            >
                                {auth.user.plan === 'free' ? '⬆ Upgrade' : `✓ ${auth.user.plan}`}
                            </Link>
                            <span className="text-sm text-gray-500">{auth.user.name}</span>
                            <Link
                                href={route('logout')}
                                method="post"
                                as="button"
                                className="text-sm text-gray-500 hover:text-red-500 transition-colors"
                            >
                                Salir
                            </Link>
                        </div>
                    </div>
                </div>
            </nav>
            <main className="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
                {children}
            </main>
        </div>
    );
}