import { Link } from '@inertiajs/react';

const features = [
    { icon: '🎯', title: 'Intencion real de compra', desc: 'Nuestro motor detecta si un usuario busca activamente una solucion, no solo habla del tema.' },
    { icon: '⚡', title: 'Actualizacion automatica', desc: 'Monitoriza Reddit cada 30 minutos. Tu abres el dashboard y las oportunidades ya estan rankeadas.' },
    { icon: '📊', title: 'Score de oportunidad', desc: 'Cada post recibe un score 0-100 combinando intencion, relevancia y engagement.' },
    { icon: '🔍', title: 'Multi-keyword', desc: 'Crea proyectos con multiples keywords. ReplyRadar las monitoriza todas.' },
    { icon: '💾', title: 'Export CSV', desc: 'Exporta tus oportunidades a CSV para tu CRM, Notion o donde prefieras.' },
    { icon: '🚀', title: 'Sin setup tecnico', desc: 'Registro en 30 segundos. Añade tu primera keyword y en 2 minutos tienes resultados.' },
];

const useCases = [
    { icon: '🧑', role: 'Indie Hackers', desc: 'Valida ideas de negocio antes de escribir una linea de codigo.' },
    { icon: '📣', role: 'Creadores', desc: 'Encuentra que preguntas reales hace tu audiencia esta semana.' },
    { icon: '🏢', role: 'SaaS founders', desc: 'Detecta pain points de usuarios de tu competencia en tiempo real.' },
    { icon: '🎯', role: 'Marketers', desc: 'Localiza conversaciones donde tu producto es la respuesta perfecta.' },
];

const plans = [
    { name: 'Free', price: '$0', period: '', features: ['1 proyecto', '5 keywords', '50 oportunidades/mes'], cta: 'Empezar gratis', href: '/register', featured: false },
    { name: 'Pro', price: '$29', period: '/mes', features: ['5 proyectos', '50 keywords', 'Oportunidades ilimitadas', 'Export CSV', 'Alertas email'], cta: 'Empezar Pro', href: '/register', featured: true },
    { name: 'Business', price: '$99', period: '/mes', features: ['Todo ilimitado', 'API access', 'Multi-fuente', 'Soporte prioritario'], cta: 'Contactar', href: '/register', featured: false },
];

const previewPosts = [
    { title: 'researching subscription management platforms for a growing SaaS', score: 76, subreddit: 'SaaS', hot: true },
    { title: 'Is there a tool that detects buyer intent from Reddit posts?', score: 71, subreddit: 'entrepreneur', hot: true },
    { title: 'what SaaS niche has terrible UX but insane retention?', score: 54, subreddit: 'micro_saas', hot: false },
    { title: 'Is Micro SaaS a good side hustle in 2025?', score: 52, subreddit: 'sidehustle', hot: false },
];

const s = {
    page: { minHeight: '100vh', backgroundColor: '#ffffff', fontFamily: 'system-ui,-apple-system,sans-serif', margin: 0 } as React.CSSProperties,
    nav: { borderBottom: '1px solid #f0f0f0', padding: '16px 24px' } as React.CSSProperties,
    navInner: { maxWidth: 1100, margin: '0 auto', display: 'flex', alignItems: 'center', justifyContent: 'space-between', flexWrap: 'wrap' as const, gap: 12 },
    navLinks: { display: 'flex', alignItems: 'center', gap: 20, flexWrap: 'wrap' as const },
    logo: { fontSize: 20, fontWeight: 800, color: '#4f46e5' } as React.CSSProperties,
    navLink: { fontSize: 14, color: '#6b7280', textDecoration: 'none' } as React.CSSProperties,
    btnPrimary: { fontSize: 14, color: '#ffffff', backgroundColor: '#4f46e5', padding: '9px 18px', borderRadius: 8, textDecoration: 'none', fontWeight: 600 } as React.CSSProperties,
    section: (bg?: string, py?: number) => ({ backgroundColor: bg || '#ffffff', padding: `${py || 80}px 24px` } as React.CSSProperties),
    inner: (maxW?: number) => ({ maxWidth: maxW || 1100, margin: '0 auto' } as React.CSSProperties),
    h1: { fontSize: 'clamp(30px,6vw,54px)', fontWeight: 800, color: '#111827', lineHeight: 1.15, marginBottom: 20, marginTop: 0 } as React.CSSProperties,
    h2: { fontSize: 'clamp(22px,4vw,32px)', fontWeight: 700, color: '#111827', textAlign: 'center' as const, marginBottom: 12, marginTop: 0 } as React.CSSProperties,
    lead: { fontSize: 'clamp(15px,2vw,18px)', color: '#6b7280', lineHeight: 1.7, marginBottom: 36, marginTop: 0 } as React.CSSProperties,
    muted: { fontSize: 13, color: '#9ca3af', marginTop: 14, marginBottom: 0 } as React.CSSProperties,
    grid: (min?: number) => ({ display: 'grid', gridTemplateColumns: `repeat(auto-fit,minmax(${min || 240}px,1fr))`, gap: 20 } as React.CSSProperties),
    card: { backgroundColor: '#ffffff', borderRadius: 16, border: '1px solid #e5e7eb', padding: 24 } as React.CSSProperties,
    tag: { display: 'inline-flex', alignItems: 'center', gap: 8, backgroundColor: '#eef2ff', color: '#4f46e5', fontSize: 12, fontWeight: 600, padding: '6px 14px', borderRadius: 999, marginBottom: 24 } as React.CSSProperties,
};

export default function Landing() {
    return (
        <div style={s.page}>

            {/* Nav */}
            <nav style={s.nav}>
                <div style={s.navInner}>
                    <span style={s.logo}>ReplyRadar</span>
                    <div style={s.navLinks}>
                        <a href="#features" style={s.navLink}>Features</a>
                        <a href="#pricing" style={s.navLink}>Precios</a>
                        <Link href="/login" style={s.navLink}>Login</Link>
                        <Link href="/register" style={s.btnPrimary}>Empezar gratis</Link>
                    </div>
                </div>
            </nav>

            {/* Hero */}
            <section style={{ ...s.section(), maxWidth: 900, margin: '0 auto', textAlign: 'center' }}>
                <div style={s.tag}>
                    <span style={{ width: 8, height: 8, backgroundColor: '#4f46e5', borderRadius: '50%', display: 'inline-block' }}></span>
                    Monitorizando Reddit en tiempo real
                </div>

                <h1 style={s.h1}>
                    Convierte conversaciones en{' '}
                    <span style={{ color: '#4f46e5' }}>oportunidades de negocio</span>
                </h1>

                <p style={{ ...s.lead, maxWidth: 600, margin: '0 auto 36px' }}>
                    ReplyRadar analiza Reddit, detecta intencion de compra real y te entrega
                    un ranking de oportunidades accionables antes que tu competencia.
                </p>

                <div style={{ display: 'flex', alignItems: 'center', justifyContent: 'center', gap: 12, flexWrap: 'wrap', marginBottom: 14 }}>
                    <Link href="/register" style={{ padding: '14px 32px', backgroundColor: '#4f46e5', color: '#ffffff', fontWeight: 700, borderRadius: 12, textDecoration: 'none', fontSize: 16 }}>
                        Empezar gratis, sin tarjeta
                    </Link>
                    <a href="#features" style={{ padding: '14px 32px', border: '1px solid #e5e7eb', color: '#374151', fontWeight: 600, borderRadius: 12, textDecoration: 'none', fontSize: 16, backgroundColor: '#ffffff' }}>
                        Ver como funciona
                    </a>
                </div>
                <p style={s.muted}>Setup en 30 segundos · Sin tarjeta · Cancela cuando quieras</p>

                {/* Preview */}
                <div style={{ marginTop: 56, backgroundColor: '#f9fafb', border: '1px solid #e5e7eb', borderRadius: 20, padding: 24, textAlign: 'left' }}>
                    <div style={{ display: 'flex', alignItems: 'center', gap: 7, marginBottom: 16 }}>
                        <div style={{ width: 11, height: 11, borderRadius: '50%', backgroundColor: '#f87171' }}></div>
                        <div style={{ width: 11, height: 11, borderRadius: '50%', backgroundColor: '#fbbf24' }}></div>
                        <div style={{ width: 11, height: 11, borderRadius: '50%', backgroundColor: '#34d399' }}></div>
                        <span style={{ marginLeft: 8, fontSize: 11, color: '#9ca3af' }}>replyradar.app/dashboard</span>
                    </div>
                    <div style={{ display: 'flex', flexDirection: 'column', gap: 10 }}>
                        {previewPosts.map((item, i) => (
                            <div key={i} style={{ backgroundColor: '#ffffff', borderRadius: 12, border: '1px solid #e5e7eb', padding: '12px 16px', display: 'flex', alignItems: 'center', justifyContent: 'space-between', gap: 12 }}>
                                <div style={{ flex: 1, minWidth: 0 }}>
                                    <p style={{ fontSize: 13, fontWeight: 500, color: '#111827', margin: 0, overflow: 'hidden', textOverflow: 'ellipsis', whiteSpace: 'nowrap' }}>{item.title}</p>
                                    <p style={{ fontSize: 11, color: '#6366f1', margin: '3px 0 0' }}>r/{item.subreddit}</p>
                                </div>
                                <span style={{ fontSize: 12, fontWeight: 700, padding: '3px 10px', borderRadius: 999, flexShrink: 0, backgroundColor: item.hot ? '#fee2e2' : '#ffedd5', color: item.hot ? '#b91c1c' : '#c2410c' }}>
                                    {item.hot ? 'Hot' : 'Warm'} {item.score}
                                </span>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Casos de uso */}
            <section style={s.section('#f9fafb')}>
                <div style={s.inner()}>
                    <h2 style={s.h2}>Para quien es ReplyRadar</h2>
                    <p style={{ ...s.lead, textAlign: 'center', marginBottom: 40 }}>Encuentra tu caso de uso y empieza hoy</p>
                    <div style={s.grid(220)}>
                        {useCases.map((uc) => (
                            <div key={uc.role} style={s.card}>
                                <div style={{ fontSize: 28, marginBottom: 10 }}>{uc.icon}</div>
                                <div style={{ fontSize: 15, fontWeight: 700, color: '#111827', marginBottom: 8 }}>{uc.role}</div>
                                <p style={{ fontSize: 14, color: '#6b7280', lineHeight: 1.6, margin: 0 }}>{uc.desc}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Features */}
            <section id="features" style={s.section()}>
                <div style={s.inner()}>
                    <h2 style={s.h2}>Todo lo que necesitas</h2>
                    <p style={{ ...s.lead, textAlign: 'center', marginBottom: 40 }}>Sin configuracion compleja. Sin APIs que conectar.</p>
                    <div style={s.grid(270)}>
                        {features.map((f) => (
                            <div key={f.title} style={{ ...s.card, border: '1px solid #e5e7eb' }}>
                                <div style={{ fontSize: 30, marginBottom: 14 }}>{f.icon}</div>
                                <h3 style={{ fontSize: 15, fontWeight: 700, color: '#111827', marginBottom: 8, marginTop: 0 }}>{f.title}</h3>
                                <p style={{ fontSize: 14, color: '#6b7280', lineHeight: 1.6, margin: 0 }}>{f.desc}</p>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* Pricing */}
            <section id="pricing" style={s.section('#f9fafb')}>
                <div style={s.inner(860)}>
                    <h2 style={s.h2}>Precios simples</h2>
                    <p style={{ ...s.lead, textAlign: 'center', marginBottom: 40 }}>Sin sorpresas. Sin contratos. Cancela cuando quieras.</p>
                    <div style={{ display: 'grid', gridTemplateColumns: 'repeat(auto-fit,minmax(230px,1fr))', gap: 20, alignItems: 'start' }}>
                        {plans.map((plan) => (
                            <div key={plan.name} style={{ backgroundColor: '#ffffff', borderRadius: 20, padding: 28, border: plan.featured ? '2px solid #4f46e5' : '2px solid #e5e7eb', boxShadow: plan.featured ? '0 8px 32px rgba(79,70,229,0.12)' : 'none' }}>
                                {plan.featured && (
                                    <div style={{ fontSize: 11, fontWeight: 700, color: '#4f46e5', backgroundColor: '#eef2ff', borderRadius: 999, padding: '4px 12px', display: 'inline-block', marginBottom: 12 }}>
                                        Mas popular
                                    </div>
                                )}
                                <div style={{ fontSize: 18, fontWeight: 700, color: '#111827' }}>{plan.name}</div>
                                <div style={{ fontSize: 36, fontWeight: 800, color: '#111827', margin: '10px 0 4px' }}>
                                    {plan.price}
                                    {plan.period && <span style={{ fontSize: 16, fontWeight: 400, color: '#9ca3af' }}>{plan.period}</span>}
                                </div>
                                <ul style={{ listStyle: 'none', padding: 0, margin: '18px 0 22px' }}>
                                    {plan.features.map((f) => (
                                        <li key={f} style={{ display: 'flex', alignItems: 'center', gap: 8, fontSize: 14, color: '#374151', padding: '4px 0' }}>
                                            <span style={{ color: '#22c55e', fontWeight: 700 }}>✓</span>{f}
                                        </li>
                                    ))}
                                </ul>
                                <Link href={plan.href} style={{ display: 'block', textAlign: 'center', padding: '12px', borderRadius: 12, fontSize: 14, fontWeight: 700, textDecoration: 'none', backgroundColor: plan.featured ? '#4f46e5' : '#f3f4f6', color: plan.featured ? '#ffffff' : '#374151' }}>
                                    {plan.cta}
                                </Link>
                            </div>
                        ))}
                    </div>
                </div>
            </section>

            {/* CTA */}
            <section style={s.section()}>
                <div style={{ ...s.inner(680), textAlign: 'center' }}>
                    <h2 style={{ ...s.h2, fontSize: 'clamp(26px,5vw,38px)' }}>Empieza a detectar oportunidades hoy</h2>
                    <p style={{ ...s.lead, margin: '16px auto 36px' }}>
                        Unete a los primeros usuarios que usan inteligencia real para encontrar oportunidades en Reddit.
                    </p>
                    <Link href="/register" style={{ display: 'inline-block', padding: '16px 40px', backgroundColor: '#4f46e5', color: '#ffffff', fontWeight: 700, borderRadius: 14, textDecoration: 'none', fontSize: 16 }}>
                        Crear cuenta gratis
                    </Link>
                    <p style={s.muted}>Sin tarjeta · Setup en 30 segundos</p>
                </div>
            </section>

            {/* Footer */}
            <footer style={{ borderTop: '1px solid #f0f0f0', padding: '24px 24px' }}>
                <div style={{ maxWidth: 1100, margin: '0 auto', display: 'flex', alignItems: 'center', justifyContent: 'space-between', flexWrap: 'wrap', gap: 12 }}>
                    <span style={{ fontSize: 14, color: '#9ca3af' }}>ReplyRadar 2026</span>
                    <div style={{ display: 'flex', gap: 24 }}>
                        <Link href="/login" style={{ fontSize: 14, color: '#9ca3af', textDecoration: 'none' }}>Login</Link>
                        <Link href="/register" style={{ fontSize: 14, color: '#9ca3af', textDecoration: 'none' }}>Registro</Link>
                    </div>
                </div>
            </footer>

        </div>
    );
}