@php
    $status = (int) ($status ?? 500);
    $title = (string) ($title ?? 'Algo salio mal');
    $message = (string) ($message ?? 'No pudimos completar la solicitud.');
    $detail = (string) ($detail ?? 'Si el problema continua, contacta al equipo de soporte.');
    $primaryAction = (string) ($primaryAction ?? 'Volver al inicio');
    $primaryHref = (string) ($primaryHref ?? url('/'));
    $secondaryAction = (string) ($secondaryAction ?? 'Regresar');
    $showRetry = (bool) ($showRetry ?? false);
@endphp
<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>{{ $status }} | {{ config('app.name', 'Apollo') }}</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Afacad:ital,wght@0,400..700;1,400..700&display=swap" rel="stylesheet">
    <style>
        :root {
            --apollo-pink: #e34786;
            --apollo-orange: #f48e00;
            --apollo-deep-orange: #d9560b;
            --apollo-yellow: #e8b82a;
            --apollo-green: #74e291;
            --apollo-cyan: #7bd3ea;
            --ink: #202020;
            --muted: #6b6b6b;
            --line: #e5e5e5;
            --surface: #ffffff;
            --surface-soft: #fafafa;
        }

        * {
            box-sizing: border-box;
        }

        body {
            min-height: 100vh;
            margin: 0;
            font-family: "Afacad", system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
            color: var(--ink);
            background:
                radial-gradient(circle at 12% 18%, rgba(244, 142, 0, 0.16), transparent 30%),
                radial-gradient(circle at 86% 22%, rgba(123, 211, 234, 0.18), transparent 28%),
                linear-gradient(135deg, #fff8ef 0%, #f9fbff 48%, #fff2f8 100%);
        }

        main {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 32px 20px;
        }

        .error-shell {
            width: min(920px, 100%);
            display: grid;
            grid-template-columns: minmax(0, 0.72fr) minmax(220px, 0.28fr);
            overflow: hidden;
            border: 1px solid rgba(32, 32, 32, 0.08);
            border-radius: 8px;
            background: rgba(255, 255, 255, 0.92);
            box-shadow: 0 24px 80px rgba(32, 32, 32, 0.12);
        }

        .content {
            padding: clamp(32px, 6vw, 64px);
        }

        .brand {
            display: flex;
            align-items: center;
            gap: 14px;
            margin-bottom: 48px;
            color: var(--muted);
            font-size: 15px;
            font-weight: 600;
            letter-spacing: 0;
        }

        .brand svg {
            width: 34px;
            height: 45px;
            flex: 0 0 auto;
        }

        .status {
            display: inline-flex;
            align-items: center;
            gap: 10px;
            margin-bottom: 20px;
            color: var(--apollo-pink);
            font-size: 16px;
            font-weight: 700;
        }

        .status::before {
            width: 34px;
            height: 3px;
            content: "";
            border-radius: 999px;
            background: linear-gradient(90deg, var(--apollo-deep-orange), var(--apollo-pink), var(--apollo-cyan));
        }

        h1 {
            max-width: 640px;
            margin: 0;
            font-size: clamp(40px, 7vw, 72px);
            line-height: 0.95;
            font-weight: 700;
            letter-spacing: 0;
        }

        .message {
            max-width: 560px;
            margin: 24px 0 0;
            color: var(--muted);
            font-size: clamp(18px, 2.4vw, 23px);
            line-height: 1.35;
        }

        .detail {
            max-width: 560px;
            margin: 12px 0 0;
            color: #7a7a7a;
            font-size: 16px;
            line-height: 1.5;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 12px;
            margin-top: 34px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 44px;
            padding: 10px 18px;
            border-radius: 8px;
            border: 1px solid transparent;
            font: inherit;
            font-size: 16px;
            font-weight: 700;
            text-decoration: none;
            cursor: pointer;
        }

        .button-primary {
            color: #ffffff;
            background: linear-gradient(135deg, var(--apollo-orange), var(--apollo-pink));
        }

        .button-secondary {
            color: var(--ink);
            background: var(--surface);
            border-color: var(--line);
        }

        .panel {
            display: grid;
            align-content: end;
            gap: 18px;
            min-height: 100%;
            padding: 28px;
            background:
                linear-gradient(180deg, rgba(32, 32, 32, 0.02), rgba(32, 32, 32, 0.06)),
                var(--surface-soft);
            border-left: 1px solid rgba(32, 32, 32, 0.08);
        }

        .signal {
            height: 12px;
            border-radius: 999px;
            background: linear-gradient(90deg, var(--apollo-deep-orange), var(--apollo-orange), var(--apollo-yellow), var(--apollo-green), var(--apollo-cyan), var(--apollo-pink));
        }

        .panel-code {
            font-size: clamp(76px, 14vw, 132px);
            line-height: 0.9;
            font-weight: 700;
            color: rgba(32, 32, 32, 0.1);
        }

        .panel-copy {
            margin: 0;
            color: var(--muted);
            font-size: 15px;
            line-height: 1.45;
        }

        @media (max-width: 760px) {
            main {
                place-items: stretch;
                padding: 18px;
            }

            .error-shell {
                grid-template-columns: 1fr;
            }

            .content {
                padding: 32px 24px;
            }

            .brand {
                margin-bottom: 34px;
            }

            .panel {
                min-height: 160px;
                border-left: 0;
                border-top: 1px solid rgba(32, 32, 32, 0.08);
            }

            .panel-code {
                font-size: 72px;
            }
        }
    </style>
</head>
<body>
    <main>
        <section class="error-shell" aria-labelledby="error-title">
            <div class="content">
                <div class="brand" aria-label="Apollo Suite">
                    <svg viewBox="0 0 24 32" fill="none" xmlns="http://www.w3.org/2000/svg" aria-hidden="true">
                        <path d="M15.96 0.04C12.99 0.55 10.52 2.53 8.96 5.02C7.41 7.51 6.78 10.5 7.53 12.99L7.55 13.05L7.49 13.03C7.43 13.01 6.7 12.74 6.04 12.15C5.36 11.53 4.75 10.55 4.96 9.09C1.02 13.07 0.03 18.02 0.03 21C0.03 27.98 5.39 31.97 12 31.97C18.61 31.97 23.97 27.98 23.97 21C23.97 17.47 22.47 14.02 19.98 12.03C17.48 10.02 16.29 8.01 15.83 6.01C15.36 4.02 15.61 2.02 15.96 0.04Z" fill="#E34786"/>
                        <path d="M21.55 17.09C22.45 17.58 23.66 16.94 23.65 15.99L23.61 -2.14C23.61 -2.99 22.63 -3.52 21.74 -3.15L2.88 4.8C1.88 5.22 1.75 6.43 2.65 6.92L21.55 17.09Z" fill="#D9560B"/>
                        <path d="M23.81 22.47C24.71 22.96 25.91 22.32 25.91 21.36L25.87 3.24C25.87 2.39 24.89 1.86 24 2.23L5.14 10.18C4.14 10.6 4.01 11.81 4.91 12.29L23.81 22.47Z" fill="#F48E00"/>
                        <path d="M23.81 33.23C24.71 33.71 25.91 33.08 25.91 32.12L25.87 14C25.87 13.14 24.89 12.61 24 12.99L5.14 20.93C4.14 21.35 4.01 22.56 4.91 23.05L23.81 33.23Z" fill="#E8B82A"/>
                        <path d="M23.99 43.98C24.89 44.46 26.1 43.83 26.09 42.87L26.05 24.75C26.05 23.89 25.07 23.36 24.18 23.74L5.32 31.68C4.32 32.1 4.19 33.31 5.09 33.8L23.99 43.98Z" fill="#74E291"/>
                        <path d="M-2.64 28.92C-2.64 29.89 -1.61 30.5 -0.76 30.04L17.02 20.46C17.92 19.98 17.92 18.69 17.02 18.21L-0.78 8.65C-1.63 8.2 -2.67 8.81 -2.67 9.78L-2.64 28.92Z" fill="#FF7F3F"/>
                        <path d="M-3.12 40.94C-3.12 41.91 -2.08 42.53 -1.23 42.06L18.57 31.33C19.47 30.84 19.47 29.56 18.57 29.08L-1.26 18.37C-2.11 17.9 -3.15 18.52 -3.14 19.49L-3.12 40.94Z" fill="#7BD3EA"/>
                        <path d="M7.51 26.67L12.34 15.19H12.36L17.21 26.67H15.18L14.23 24.36H10.42L9.47 26.67H7.51ZM11.09 22.73H13.55L12.97 21.34C12.85 21.04 12.73 20.76 12.62 20.48C12.52 20.19 12.41 19.86 12.31 19.48C12.22 19.86 12.11 20.18 12.01 20.46C11.91 20.74 11.8 21.04 11.67 21.34L11.09 22.73Z" fill="#fff"/>
                    </svg>
                    <span>Apollo Suite</span>
                </div>

                <div class="status">Error {{ $status }}</div>
                <h1 id="error-title">{{ $title }}</h1>
                <p class="message">{{ $message }}</p>
                <p class="detail">{{ $detail }}</p>

                <div class="actions">
                    <a class="button button-primary" href="{{ $primaryHref }}">{{ $primaryAction }}</a>

                    @if ($showRetry)
                        <button class="button button-secondary" type="button" onclick="window.location.reload()">Reintentar</button>
                    @else
                        <button class="button button-secondary" type="button" onclick="history.length > 1 ? history.back() : window.location.assign('/')">{{ $secondaryAction }}</button>
                    @endif
                </div>
            </div>

            <aside class="panel" aria-hidden="true">
                <div class="panel-code">{{ $status }}</div>
                <div class="signal"></div>
                <p class="panel-copy">La experiencia sigue alineada con Apollo aunque la solicitud no haya podido completarse.</p>
            </aside>
        </section>
    </main>
</body>
</html>
