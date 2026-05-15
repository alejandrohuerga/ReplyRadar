import { FormEventHandler } from 'react';
import { Link, useForm } from '@inertiajs/react';

type Props = {
    status?: string;
    canResetPassword: boolean;
};

export default function Login({ status, canResetPassword }: Props) {
    const { data, setData, post, processing, errors, reset } = useForm({
        email: '',
        password: '',
        remember: false,
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('login'), { onFinish: () => reset('password') });
    };

    return (
        <div style={{ minHeight: '100vh', backgroundColor: '#f9fafb', fontFamily: 'system-ui,-apple-system,sans-serif', display: 'flex', flexDirection: 'column' }}>

            {/* Nav */}
            <nav style={{ backgroundColor: '#ffffff', borderBottom: '1px solid #f0f0f0', padding: '16px 24px' }}>
                <div style={{ maxWidth: 1100, margin: '0 auto', display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                    <Link href="/" style={{ fontSize: 20, fontWeight: 800, color: '#4f46e5', textDecoration: 'none' }}>
                        ReplyRadar
                    </Link>
                    <Link href="/register" style={{ fontSize: 14, color: '#6b7280', textDecoration: 'none' }}>
                        No tienes cuenta? <span style={{ color: '#4f46e5', fontWeight: 600 }}>Registrate</span>
                    </Link>
                </div>
            </nav>

            {/* Card */}
            <div style={{ flex: 1, display: 'flex', alignItems: 'center', justifyContent: 'center', padding: '40px 24px' }}>
                <div style={{ width: '100%', maxWidth: 440 }}>

                    {/* Header */}
                    <div style={{ textAlign: 'center', marginBottom: 32 }}>
                        <div style={{ width: 52, height: 52, backgroundColor: '#eef2ff', borderRadius: 14, display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 16px', fontSize: 24 }}>
                            🎯
                        </div>
                        <h1 style={{ fontSize: 26, fontWeight: 800, color: '#111827', margin: '0 0 8px' }}>
                            Bienvenido de vuelta
                        </h1>
                        <p style={{ fontSize: 14, color: '#6b7280', margin: 0 }}>
                            Accede a tus oportunidades de negocio
                        </p>
                    </div>

                    {/* Status */}
                    {status && (
                        <div style={{ backgroundColor: '#f0fdf4', border: '1px solid #bbf7d0', borderRadius: 10, padding: '12px 16px', marginBottom: 20, fontSize: 14, color: '#15803d', textAlign: 'center' }}>
                            {status}
                        </div>
                    )}

                    {/* Form */}
                    <div style={{ backgroundColor: '#ffffff', borderRadius: 20, border: '1px solid #e5e7eb', padding: '32px 28px', boxShadow: '0 4px 24px rgba(0,0,0,0.06)' }}>
                        <form onSubmit={submit}>

                            {/* Email */}
                            <div style={{ marginBottom: 20 }}>
                                <label style={{ display: 'block', fontSize: 13, fontWeight: 600, color: '#374151', marginBottom: 6 }}>
                                    Email
                                </label>
                                <input
                                    type="email"
                                    value={data.email}
                                    onChange={e => setData('email', e.target.value)}
                                    autoComplete="username"
                                    autoFocus
                                    style={{
                                        width: '100%',
                                        padding: '11px 14px',
                                        fontSize: 14,
                                        border: errors.email ? '1.5px solid #f87171' : '1.5px solid #e5e7eb',
                                        borderRadius: 10,
                                        outline: 'none',
                                        boxSizing: 'border-box',
                                        color: '#111827',
                                        backgroundColor: '#ffffff',
                                    }}
                                    placeholder="tu@email.com"
                                />
                                {errors.email && (
                                    <p style={{ fontSize: 12, color: '#ef4444', marginTop: 6, marginBottom: 0 }}>{errors.email}</p>
                                )}
                            </div>

                            {/* Password */}
                            <div style={{ marginBottom: 16 }}>
                                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'space-between', marginBottom: 6 }}>
                                    <label style={{ fontSize: 13, fontWeight: 600, color: '#374151' }}>
                                        Contraseña
                                    </label>
                                    {canResetPassword && (
                                        <Link href={route('password.request')} style={{ fontSize: 12, color: '#4f46e5', textDecoration: 'none', fontWeight: 500 }}>
                                            Olvidaste tu contraseña?
                                        </Link>
                                    )}
                                </div>
                                <input
                                    type="password"
                                    value={data.password}
                                    onChange={e => setData('password', e.target.value)}
                                    autoComplete="current-password"
                                    style={{
                                        width: '100%',
                                        padding: '11px 14px',
                                        fontSize: 14,
                                        border: errors.password ? '1.5px solid #f87171' : '1.5px solid #e5e7eb',
                                        borderRadius: 10,
                                        outline: 'none',
                                        boxSizing: 'border-box',
                                        color: '#111827',
                                        backgroundColor: '#ffffff',
                                    }}
                                    placeholder="••••••••"
                                />
                                {errors.password && (
                                    <p style={{ fontSize: 12, color: '#ef4444', marginTop: 6, marginBottom: 0 }}>{errors.password}</p>
                                )}
                            </div>

                            {/* Remember */}
                            <div style={{ display: 'flex', alignItems: 'center', gap: 8, marginBottom: 24 }}>
                                <input
                                    type="checkbox"
                                    id="remember"
                                    checked={data.remember}
                                    onChange={e => setData('remember', e.target.checked)}
                                    style={{ width: 16, height: 16, accentColor: '#4f46e5', cursor: 'pointer' }}
                                />
                                <label htmlFor="remember" style={{ fontSize: 13, color: '#6b7280', cursor: 'pointer' }}>
                                    Recordarme
                                </label>
                            </div>

                            {/* Submit */}
                            <button
                                type="submit"
                                disabled={processing}
                                style={{
                                    width: '100%',
                                    padding: '13px',
                                    backgroundColor: processing ? '#a5b4fc' : '#4f46e5',
                                    color: '#ffffff',
                                    fontSize: 15,
                                    fontWeight: 700,
                                    borderRadius: 12,
                                    border: 'none',
                                    cursor: processing ? 'not-allowed' : 'pointer',
                                    transition: 'background-color 0.2s',
                                }}
                            >
                                {processing ? 'Accediendo...' : 'Entrar a ReplyRadar'}
                            </button>

                        </form>
                    </div>

                    {/* Footer link */}
                    <p style={{ textAlign: 'center', marginTop: 24, fontSize: 14, color: '#6b7280' }}>
                        No tienes cuenta?{' '}
                        <Link href="/register" style={{ color: '#4f46e5', fontWeight: 600, textDecoration: 'none' }}>
                            Empieza gratis
                        </Link>
                    </p>

                </div>
            </div>

        </div>
    );
}