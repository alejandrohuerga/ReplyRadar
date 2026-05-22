import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout';
import { Link, useForm } from '@inertiajs/react';

type Props = {
    currentPlan: string;
    onTrial?: boolean;
    subscribed?: boolean;
};

const plans = [
    {
        id: 'free',
        name: 'Free',
        price: '$0',
        description: 'Para explorar ReplyRadar',
        features: ['1 proyecto', '5 keywords', '50 oportunidades/mes', 'Histórico 7 días'],
        color: 'border-gray-200',
        button: 'bg-gray-100 text-gray-500 cursor-default',
    },
    {
        id: 'pro',
        name: 'Pro',
        price: '$29',
        description: 'Para creadores y solopreneurs',
        features: ['5 proyectos', '50 keywords', 'Oportunidades ilimitadas', 'Histórico 90 días', 'Export CSV'],
        color: 'border-indigo-400',
        button: 'bg-indigo-600 hover:bg-indigo-700 text-white',
        featured: true,
    },
    {
        id: 'business',
        name: 'Business',
        price: '$99',
        description: 'Para agencias y equipos',
        features: ['Proyectos ilimitados', 'Keywords ilimitadas', 'Multi-fuente', 'API access', 'Soporte prioritario'],
        color: 'border-gray-200',
        button: 'bg-gray-900 hover:bg-gray-800 text-white',
    },
];

export default function Plans({ currentPlan, subscribed }: Props) {
    const { data, setData, post, processing } = useForm({
        plan: '',
    });

    const checkout = (planId: string) => {
        if (planId === currentPlan || planId === 'free') return;
        
        setData('plan', planId);
        post(route('billing.checkout'));
    };

    return (
        <AuthenticatedLayout>
            <div className="text-center mb-10">
                <h1 className="text-3xl font-bold text-gray-900">Elige tu plan</h1>
                <p className="text-gray-500 mt-2">Sin contratos. Cancela cuando quieras.</p>
            </div>

            {/* Botón portal si ya está suscrito */}
            {subscribed && (
                <div className="text-center mb-8">
                    <Link
                        href={route('billing.portal')}
                        className="inline-block px-6 py-2 bg-gray-100 text-gray-700 text-sm rounded-lg hover:bg-gray-200 transition-colors"
                    >
                        🔧 Gestionar suscripción en Stripe
                    </Link>
                </div>
            )}

            <div className="grid md:grid-cols-3 gap-6 max-w-4xl mx-auto">
                {plans.map(plan => (
                    <div
                        key={plan.id}
                        className={`bg-white rounded-2xl border-2 p-6 ${plan.color} ${plan.featured ? 'shadow-lg scale-105' : ''}`}
                    >
                        {plan.featured && (
                            <div className="text-xs font-semibold text-indigo-600 bg-indigo-50 rounded-full px-3 py-1 inline-block mb-3">
                                ⭐ Más popular
                            </div>
                        )}
                        <div className="text-lg font-bold text-gray-900">{plan.name}</div>
                        <div className="text-3xl font-bold text-gray-900 mt-2">
                            {plan.price}<span className="text-base font-normal text-gray-500">/mes</span>
                        </div>
                        <p className="text-sm text-gray-500 mt-1 mb-5">{plan.description}</p>

                        <ul className="space-y-2 mb-6">
                            {plan.features.map(f => (
                                <li key={f} className="flex items-center gap-2 text-sm text-gray-700">
                                    <span className="text-green-500">✓</span> {f}
                                </li>
                            ))}
                        </ul>

                        <button
                            onClick={() => checkout(plan.id)}
                            disabled={processing || plan.id === currentPlan || plan.id === 'free'}
                            className={`w-full py-2.5 rounded-xl text-sm font-medium transition-colors disabled:opacity-60 ${plan.button}`}
                        >
                            {plan.id === currentPlan ? '✓ Plan actual' : plan.id === 'free' ? 'Plan gratuito' : `Cambiar a ${plan.name}`}
                        </button>
                    </div>
                ))}
            </div>
        </AuthenticatedLayout>
    );
}