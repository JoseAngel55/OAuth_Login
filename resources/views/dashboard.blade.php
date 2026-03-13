<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=DM+Serif+Display&family=DM+Sans:wght@300;400;500&display=swap" rel="stylesheet">
    <style>
        *, *::before, *::after { box-sizing: border-box; margin: 0; padding: 0; }

        :root {
            --ink:    #111010;
            --muted:  #888580;
            --light:  #c8c5c0;
            --bg:     #f7f5f0;
            --white:  #ffffff;
            --border: #e2dfd8;
            --r:      8px;
            --r-lg:   12px;
        }

        body {
            background: var(--bg);
            font-family: 'DM Sans', system-ui, sans-serif;
            color: var(--ink);
            min-height: 100vh;
        }

        /* ── Topbar ─────────────────────────── */
        .topbar {
            background: var(--white);
            border-bottom: 1px solid var(--border);
            padding: 0 40px;
            height: 56px;
            display: flex;
            align-items: center;
            justify-content: space-between;
        }

        .topbar-brand {
            font-family: 'DM Serif Display', serif;
            font-size: 18px;
            color: var(--ink);
            letter-spacing: -0.3px;
        }

        .topbar-right {
            display: flex;
            align-items: center;
            gap: 16px;
        }

        .user-chip {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .user-avatar {
            width: 32px;
            height: 32px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid var(--border);
        }

        .user-name {
            font-size: 13px;
            font-weight: 500;
            color: var(--ink);
        }

        .btn-logout {
            font-family: 'DM Sans', sans-serif;
            font-size: 12px;
            color: var(--muted);
            background: none;
            border: 1px solid var(--border);
            border-radius: var(--r);
            padding: 6px 14px;
            cursor: pointer;
            transition: color 0.15s, border-color 0.15s;
        }
        .btn-logout:hover { color: var(--ink); border-color: var(--light); }

        /* ── Layout ─────────────────────────── */
        .page {
            max-width: 860px;
            margin: 0 auto;
            padding: 48px 24px;
        }

        /* ── Alertas ─────────────────────────── */
        .alert {
            padding: 10px 14px;
            border-radius: var(--r);
            font-size: 13px;
            margin-bottom: 24px;
            border-left: 3px solid;
        }
        .alert-success { background: #f0faf4; border-color: #4caf82; color: #2d6b4f; }
        .alert-error   { background: #fdf3f3; border-color: #e57373; color: #8b2e2e; }

        /* ── Header de página ─────────────────── */
        .page-header {
            margin-bottom: 48px;
        }

        .page-eyebrow {
            font-size: 11px;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 10px;
        }

        .page-title {
            font-family: 'DM Serif Display', serif;
            font-size: 36px;
            color: var(--ink);
            letter-spacing: -0.8px;
            line-height: 1.1;
            margin-bottom: 6px;
        }

        .page-sub {
            font-size: 14px;
            color: var(--muted);
        }

        /* ── Grid de secciones ─────────────────── */
        .grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
            margin-bottom: 20px;
        }

        /* ── Tarjeta base ────────────────────── */
        .card {
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--r-lg);
            overflow: hidden;
        }

        .card-header {
            padding: 18px 20px 0;
        }

        .card-label {
            font-size: 10px;
            letter-spacing: 2px;
            text-transform: uppercase;
            color: var(--light);
            margin-bottom: 16px;
        }

        .card-body {
            padding: 0 20px 20px;
        }

        /* ── Info del usuario ────────────────── */
        .user-block {
            display: flex;
            align-items: center;
            gap: 14px;
            padding: 18px 20px;
        }

        .user-avatar-lg {
            width: 52px;
            height: 52px;
            border-radius: 50%;
            object-fit: cover;
            border: 1px solid var(--border);
            flex-shrink: 0;
        }

        .user-info-name {
            font-size: 17px;
            font-weight: 500;
            color: var(--ink);
        }

        .user-info-email {
            font-size: 12px;
            color: var(--muted);
            margin-top: 2px;
        }

        /* Filas de datos */
        .data-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 11px 20px;
            border-top: 1px solid var(--border);
            font-size: 13px;
        }

        .data-key { color: var(--muted); }
        .data-val { font-weight: 500; color: var(--ink); }

        /* ── Proveedores ──────────────────────── */
        .provider-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 20px;
            border-top: 1px solid var(--border);
        }

        .provider-row:first-child { border-top: none; }

        .provider-left {
            display: flex;
            align-items: center;
            gap: 12px;
        }

        .provider-dot {
            width: 8px;
            height: 8px;
            border-radius: 50%;
            flex-shrink: 0;
        }
        .dot-on  { background: #4caf82; }
        .dot-off { background: var(--border); }

        .provider-name {
            font-size: 14px;
            font-weight: 500;
            color: var(--ink);
        }

        .provider-id {
            font-size: 11px;
            color: var(--muted);
            margin-top: 1px;
        }

        .provider-actions {
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .badge {
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 20px;
            font-weight: 500;
        }
        .badge-on  { background: #edf7f3; color: #2d6b4f; }
        .badge-off { background: var(--bg); color: var(--muted); }

        .btn-action {
            font-family: 'DM Sans', sans-serif;
            font-size: 12px;
            padding: 5px 12px;
            border-radius: var(--r);
            cursor: pointer;
            border: 1px solid var(--border);
            background: none;
            transition: background 0.15s, border-color 0.15s;
        }
        .btn-connect    { color: var(--ink); }
        .btn-connect:hover { background: var(--bg); }
        .btn-disconnect { color: #c0392b; border-color: #f5c6c6; }
        .btn-disconnect:hover { background: #fdf3f3; }

        /* ── Debug ────────────────────────────── */
        .debug-block {
            background: var(--ink);
            border-radius: var(--r-lg);
            padding: 20px;
            font-family: 'Courier New', monospace;
            font-size: 12px;
            color: #a0e4b0;
            line-height: 1.7;
        }

        .debug-comment { color: #555; }

        @media (max-width: 640px) {
            .topbar { padding: 0 16px; }
            .page   { padding: 28px 16px; }
            .grid   { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>

    <div class="topbar">
        <span class="topbar-brand">app.</span>
        <div class="topbar-right">
            <div class="user-chip">
                <img src="{{ auth()->user()->avatar_url }}" alt="" class="user-avatar">
                <span class="user-name">{{ auth()->user()->name }}</span>
            </div>
            <form action="{{ route('logout') }}" method="POST" style="display:inline;">
                @csrf
                <button type="submit" class="btn-logout">Salir</button>
            </form>
        </div>
    </div>

    <div class="page">

        @if (session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif
        @if ($errors->has('oauth'))
            <div class="alert alert-error">{{ $errors->first('oauth') }}</div>
        @endif

        <div class="page-header">
            <p class="page-eyebrow">Dashboard</p>
            <h1 class="page-title">Hola, {{ auth()->user()->name }}.</h1>
            <p class="page-sub">Sesión activa · autenticado con OAuth 2.0</p>
        </div>

        <div class="grid">

            {{-- Tarjeta de cuenta --}}
            <div class="card">
                <div class="card-header">
                    <p class="card-label">Cuenta</p>
                </div>
                <div class="user-block">
                    <img src="{{ auth()->user()->avatar_url }}" alt="" class="user-avatar-lg">
                    <div>
                        <div class="user-info-name">{{ auth()->user()->name }}</div>
                        <div class="user-info-email">{{ auth()->user()->email ?? 'sin email registrado' }}</div>
                    </div>
                </div>
                <div class="data-row">
                    <span class="data-key">ID</span>
                    <span class="data-val">#{{ auth()->id() }}</span>
                </div>
                <div class="data-row">
                    <span class="data-key">Registro</span>
                    <span class="data-val">{{ auth()->user()->created_at->format('d/m/Y') }}</span>
                </div>
                <div class="data-row">
                    <span class="data-key">Password</span>
                    <span class="data-val" style="color: var(--muted);">{{ auth()->user()->password ? 'establecida' : 'ninguna (OAuth)' }}</span>
                </div>
            </div>

            {{-- Tarjeta de proveedores --}}
            @php
                $linked = auth()->user()->oauthProviders->keyBy('provider');
                $providers = [
                    'discord' => 'Discord',
                    'twitch'  => 'Twitch',
                ];
            @endphp

            <div class="card">
                <div class="card-header">
                    <p class="card-label">Proveedores vinculados</p>
                </div>

                @foreach($providers as $key => $name)
                    @php $record = $linked->get($key); @endphp
                    <div class="provider-row">
                        <div class="provider-left">
                            <div class="provider-dot {{ $record ? 'dot-on' : 'dot-off' }}"></div>
                            <div>
                                <div class="provider-name">{{ $name }}</div>
                                <div class="provider-id">
                                    {{ $record ? 'ID: ' . $record->provider_id : 'no conectado' }}
                                </div>
                            </div>
                        </div>
                        <div class="provider-actions">
                            @if($record)
                                <span class="badge badge-on">activo</span>
                                <form action="{{ route('oauth.disconnect', $key) }}" method="POST">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn-action btn-disconnect"
                                        onclick="return confirm('Desconectar {{ $name }}?')">
                                        desconectar
                                    </button>
                                </form>
                            @else
                                <span class="badge badge-off">inactivo</span>
                                <a href="{{ route('oauth.redirect', $key) }}" class="btn-action btn-connect">
                                    conectar
                                </a>
                            @endif
                        </div>
                    </div>
                @endforeach
            </div>

        </div>

        {{-- Debug de sesión --}}
        <div class="debug-block">
            <span class="debug-comment">// sesión activa</span><br>
            user_id  &nbsp;→ {{ auth()->id() }}<br>
            name     &nbsp;→ "{{ auth()->user()->name }}"<br>
            providers → [{{ auth()->user()->oauthProviders->pluck('provider')->implode(', ') ?: 'ninguno' }}]<br>
            auth_via &nbsp;→ oauth2.0 / oidc
        </div>

    </div>

</body>
</html>