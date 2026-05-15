import { FormEventHandler } from 'react';
import { Link, useForm } from '@inertiajs/react';

export default function Register() {
    const { data, setData, post, processing, errors, reset } = useForm({
        name: '',
        email: '',
        password: '',
        password_confirmation: '',
    });

    const submit: FormEventHandler = (e) => {
        e.preventDefault();
        post(route('register'), { onFinish: () => reset('password', 'password_confirmation') });
    };

    const inputStyle = (hasError: boolean) => ({
        width: '100%',
        padding: '11px 14px',
        fontSize: 14,
        border: hasError ? '1.5px solid #f87171' : '1.5px solid #e5e7eb',
        borderRadius: 10,
        outline: 'none',
        boxSizing: 'border-box' as const,
        color: '#111827',
        backgroundColor: '#ffffff',
    });

    return (
        <div style={{ minHeight: '100vh', backgroundColor: '#f9fafb', fontFamily: 'system-ui,-apple-system,sans-serif', display: 'flex', flexDirection: 'column' }}>

            {/* Nav */}
            <nav style={{ backgroundColor: '#ffffff', borderBottom: '1px solid #f0f0f0', padding: '16px 24px' }}>
                <div style={{ maxWidth: 1100, margin: '0 auto', display: 'flex', alignItems: 'center', justifyContent: 'space-between' }}>
                    <Link href="/" style={{ fontSize: 20, fontWeight: 800, color: '#4f46e5', textDecoration: 'none' }}>
                        ReplyRadar
                    </Link>
                    <Link href="/login" style={{ fontSize: 14, color: '#6b7280', textDecoration: 'none' }}>
                        Ya tienes cuenta? <span style={{ color: '#4f46e5', fontWeight: 600 }}>Inicia sesion</span>
                    </Link>
                </div>
            </nav>

            {/* Card */}
            <div style={{ flex: 1, display: 'flex', alignItems: 'center', justifyContent: 'center', padding: '40px 24px' }}>
                <div style={{ width: '100%', maxWidth: 460 }}>

                    {/* Header */}
                    <div style={{ textAlign: 'center', marginBottom: 32 }}>
                        <div style={{ width: 52, height: 52, backgroundColor: '#eef2ff', borderRadius: 14, display: 'flex', alignItems: 'center', justifyContent: 'center', margin: '0 auto 16px', fontSize: 24 }}>
                            🎯
                        </div>
                        <h1 style={{ fontSize: 26, fontWeight: 800, color: '#111827', margin: '0 0 8px' }}>
                            Crea tu cuenta gratis
                        </h1>
                        <p style={{ fontSize: 14, color: '#6b7280', margin: 0 }}>
                            Empieza a detectar oportunidades en Reddit hoy
                        </p>
                    </div>

                    {/* Beneficios */}
                    <div style={{ display: 'flex', justifyContent: 'center', gap: 20, marginBottom: 28, flexWrap: 'wrap' }}>
                        {['Sin tarjeta', '1 proyecto gratis', 'Setup en 30s'].map((b) => (
                            <div key={b} style={{ display: 'flex', alignItems: 'center', gap: 5, fontSize: 12, color: '#6b7280' }}>
                                <span style={{ color: '#22c55e', fontWeight: 700 }}>✓</span>
                                {b}
                            </div>
                        ))}
                    </div>

                    {/* Form */}
                    <div style={{ backgroundColor: '#ffffff', borderRadius: 20, border: '1px solid #e5e7eb', padding: '32px 28px', boxShadow: '0 4px 24px rgba(0,0,0,0.06)' }}>
                        <form onSubmit={submit}>

                            {/* Nombre */}
                            <div style={{ marginBottom: 18 }}>
                                <label style={{ display: 'block', fontSize: 13, fontWeight: 600, color: '#374151', marginBottom: 6 }}>
                                    Nombre
                                </label>
                                <input
                                    type="text"
                                    value={data.name}
                                    onChange={e => setData('name', e.target.value)}
                                    autoComplete="name"
                                    autoFocus
                                    style={inputStyle(!!errors.name)}
                                    placeholder="Tu nombre"
                                />
                                {errors.name && <p style={{ fontSize: 12, color: '#ef4444', marginTop: 5, marginBottom: 0 }}>{errors.name}</p>}
                            </div>

                            {/* Email */}
                            <div style={{ marginBottom: 18 }}>
                                <label style={{ display: 'block', fontSize: 13, fontWeight: 600, color: '#374151', marginBottom: 6 }}>
                                    Email
                                </label>
                                <input
                                    type="email"
                                    value={data.email}
                                    onChange={e => setData('email', e.target.value)}
                                    autoComplete="username"
                                    style={inputStyle(!!errors.email)}
                                    placeholder="tu@email.com"
                                />
                                {errors.email && <p style={{ fontSize: 12, color: '#ef4444', marginTop: 5, marginBottom: 0 }}>{errors.email}</p>}
                            </div>

                            {/* Password */}
                            <div style={{ marginBottom: 18 }}>
                                <label style={{ display: 'block', fontSize: 13, fontWeight: 600, color: '#374151', marginBottom: 6 }}>
                                    Contraseña
                                </label>
                                <input
                                    type="password"
                                    value={data.password}
                                    onChange={e => setData('password', e.target.value)}
                                    autoComplete="new-password"
                                    style={inputStyle(!!errors.password)}
                                    placeholder="Minimo 8 caracteres"
                                />
                                {errors.password && <p style={{ fontSize: 12, color: '#ef4444', marginTop: 5, marginBottom: 0 }}>{errors.password}</p>}
                            </div>

                            {/* Confirm Password */}
                            <div style={{ marginBottom: 28 }}>
                                <label style={{ display: 'block', fontSize: 13, fontWeight: 600, color: '#374151', marginBottom: 6 }}>
                                    Confirmar contraseña
                                </label>
                                <input
                                    type="password"
                                    value={data.password_confirmation}
                                    onChange={e => setData('password_confirmation', e.target.value)}
                                    autoComplete="new-password"
                                    style={inputStyle(!!errors.password_confirmation)}
                                    placeholder="Repite tu contraseña"
                                />
                                {errors.password_confirmation && <p style={{ fontSize: 12, color: '#ef4444', marginTop: 5, marginBottom: 0 }}>{errors.password_confirmation}</p>}
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
                                }}
                            >
                                {processing ? 'Creando cuenta...' : 'Crear cuenta gratis'}
                            </button>

                            <p style={{ fontSize: 11, color: '#9ca3af', textAlign: 'center', marginTop: 14, marginBottom: 0, lineHeight: 1.6 }}>
                                Al registrarte aceptas nuestros terminos de uso y politica de privacidad.
                            </p>

                        </form>
                    </div>

                    {/* Footer */}
                    <p style={{ textAlign: 'center', marginTop: 24, fontSize: 14, color: '#6b7280' }}>
                        Ya tienes cuenta?{' '}
                        <Link href="/login" style={{ color: '#4f46e5', fontWeight: 600, textDecoration: 'none' }}>
                            Inicia sesion
                        </Link>
                    </p>

                </div>
            </div>

        </div>
    );
}