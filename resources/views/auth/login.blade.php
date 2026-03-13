<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Iniciar sesión</title>
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
            --discord-bg: #eceeff;
            --discord:    #5865F2;
            --twitch-bg:  #f2ecff;
            --twitch:     #9146FF;
            --r:      8px;
        }

        html, body {
            height: 100%;
        }

        body {
            background: var(--bg);
            font-family: 'DM Sans', system-ui, sans-serif;
            color: var(--ink);
            display: grid;
            grid-template-columns: 1fr 1fr;
            min-height: 100vh;
        }

        /* Panel izquierdo — decorativo */
        .left {
            background: var(--ink);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            padding: 48px;
        }

        .left-brand {
            font-family: 'DM Serif Display', serif;
            font-size: 22px;
            color: var(--bg);
            letter-spacing: -0.5px;
        }

        .left-copy {
            color: #555;
            font-size: 13px;
            line-height: 1.6;
        }

        .left-headline {
            font-family: 'DM Serif Display', serif;
            font-size: 42px;
            line-height: 1.1;
            color: var(--bg);
            letter-spacing: -1px;
        }

        .left-sub {
            margin-top: 16px;
            font-size: 14px;
            color: #666;
            line-height: 1.7;
            max-width: 280px;
        }

        /* Panel derecho — formulario */
        .right {
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 48px 32px;
        }

        .form-wrap {
            width: 100%;
            max-width: 340px;
        }

        .form-label {
            font-size: 11px;
            letter-spacing: 2.5px;
            text-transform: uppercase;
            color: var(--muted);
            margin-bottom: 28px;
        }

        .form-title {
            font-family: 'DM Serif Display', serif;
            font-size: 30px;
            color: var(--ink);
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }

        .form-sub {
            font-size: 13px;
            color: var(--muted);
            margin-bottom: 36px;
            line-height: 1.6;
        }

        /* Alertas */
        .alert {
            padding: 10px 14px;
            border-radius: var(--r);
            font-size: 13px;
            margin-bottom: 20px;
            border-left: 3px solid;
        }
        .alert-success { background: #f0faf4; border-color: #4caf82; color: #2d6b4f; }
        .alert-error   { background: #fdf3f3; border-color: #e57373; color: #8b2e2e; }

        /* Botones */
        .btn {
            display: flex;
            align-items: center;
            gap: 14px;
            width: 100%;
            padding: 14px 18px;
            background: var(--white);
            border: 1px solid var(--border);
            border-radius: var(--r);
            text-decoration: none;
            color: var(--ink);
            font-family: 'DM Sans', sans-serif;
            font-size: 14px;
            font-weight: 400;
            cursor: pointer;
            transition: border-color 0.15s, background 0.15s;
        }

        .btn:hover { border-color: var(--light); background: #faf9f7; }

        .btn + .btn { margin-top: 10px; }

        .btn-icon {
            width: 32px;
            height: 32px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            flex-shrink: 0;
        }

        .icon-discord { background: var(--discord-bg); }
        .icon-twitch  { background: var(--twitch-bg); }

        .btn-text { flex: 1; }
        .btn-name { display: block; font-weight: 500; font-size: 14px; }
        .btn-hint { display: block; font-size: 11px; color: var(--muted); margin-top: 1px; }

        .btn-arrow { color: var(--light); font-size: 16px; }

        /* Separador */
        .sep {
            display: flex;
            align-items: center;
            gap: 12px;
            margin: 28px 0;
        }
        .sep::before, .sep::after {
            content: '';
            flex: 1;
            height: 1px;
            background: var(--border);
        }
        .sep span { font-size: 11px; color: var(--light); }

        .footer-note {
            margin-top: 32px;
            font-size: 11px;
            color: var(--light);
            line-height: 1.7;
        }

        @media (max-width: 640px) {
            body { grid-template-columns: 1fr; }
            .left { display: none; }
        }
    </style>
</head>
<body>

    <!-- Panel izquierdo -->
    <div class="left">
        <div class="left-brand">app.</div>
        <div>
            <div class="left-headline">Connect<br>your account.</div>
            <p class="left-sub">OAuth 2.0 y OpenID Connect. Sin contraseñas locales. Tu identidad la gestiona el proveedor.</p>
        </div>
        <div class="left-copy">OAuth 2.0 / OpenID Connect</div>
    </div>

    <!-- Panel derecho -->
    <div class="right">
        <div class="form-wrap">
            <p class="form-label">Autenticación</p>
            <h1 class="form-title">Bienvenido</h1>
            <p class="form-sub">Elige un proveedor para continuar.</p>

            @if (session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if ($errors->has('oauth'))
                <div class="alert alert-error">{{ $errors->first('oauth') }}</div>
            @endif

            <a href="{{ route('oauth.redirect', 'discord') }}" class="btn">
                <div class="btn-icon icon-discord">
                    <svg width="16" height="16" viewBox="0 0 127 96" fill="#5865F2">
                        <path d="M107.7,8.07A105.15,105.15,0,0,0,81.47,0a72.06,72.06,0,0,0-3.36,6.83A97.68,97.68,0,0,0,49,6.83,72.37,72.37,0,0,0,45.64,0,105.89,105.89,0,0,0,19.39,8.09C2.79,32.65-1.71,56.6.54,80.21h0A105.73,105.73,0,0,0,32.71,96.36,77.7,77.7,0,0,0,39.6,85.25a68.42,68.42,0,0,1-10.85-5.18c.91-.66,1.8-1.34,2.66-2a75.57,75.57,0,0,0,64.32,0c.87.71,1.76,1.39,2.66,2a68.68,68.68,0,0,1-10.87,5.19,77,77,0,0,0,6.89,11.1A105.25,105.25,0,0,0,126.6,80.22h0C129.24,52.84,122.09,29.11,107.7,8.07ZM42.45,65.69C36.18,65.69,31,60,31,53s5-12.74,11.43-12.74S54,46,53.89,53,48.84,65.69,42.45,65.69Zm42.24,0C78.41,65.69,73.25,60,73.25,53s5-12.74,11.44-12.74S96.23,46,96.12,53,91.08,65.69,84.69,65.69Z"/>
                    </svg>
                </div>
                <div class="btn-text">
                    <span class="btn-name">Discord</span>
                    <span class="btn-hint">discord.com/oauth2</span>
                </div>
                <span class="btn-arrow">›</span>
            </a>

            <a href="{{ route('oauth.redirect', 'twitch') }}" class="btn">
                <div class="btn-icon icon-twitch">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="#9146FF">
                        <path d="M11.571 4.714h1.715v5.143H11.57zm4.715 0H18v5.143h-1.714zM6 0L1.714 4.286v15.428h5.143V24l4.286-4.286h3.428L22.286 12V0zm14.571 11.143l-3.428 3.428h-3.429l-3 3v-3H6.857V1.714h13.714z"/>
                    </svg>
                </div>
                <div class="btn-text">
                    <span class="btn-name">Twitch</span>
                    <span class="btn-hint">id.twitch.tv/oauth2</span>
                </div>
                <span class="btn-arrow">›</span>
            </a>

            <p class="footer-note">
                No almacenamos contraseñas. Tu sesión es gestionada<br>
                por el proveedor mediante OAuth 2.0.
            </p>
        </div>
    </div>

</body>
</html>