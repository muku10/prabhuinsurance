<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="robots" content="noindex, nofollow">
    <title>Page Not Found · Prabhu Insurance Limited</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            color-scheme: light;
            --brand: #b3261e;
            --brand-deep: #7a1a15;
            --brand-soft: #fbecea;
            --ink: #241612;
            --muted: #6b5b57;
            --line: #ecdfdc;
            --background: #fbf7f5;
        }

        * { box-sizing: border-box; }

        body {
            min-height: 100vh;
            margin: 0;
            display: grid;
            place-items: center;
            padding: 32px 20px;
            overflow-x: hidden;
            background:
                radial-gradient(circle at 15% 15%, rgba(179, 38, 30, .09), transparent 30%),
                radial-gradient(circle at 85% 85%, rgba(224, 164, 88, .12), transparent 28%),
                var(--background);
            color: var(--ink);
            font-family: Inter, system-ui, -apple-system, BlinkMacSystemFont, "Segoe UI", sans-serif;
        }

        .error-card {
            position: relative;
            width: min(100%, 720px);
            padding: clamp(32px, 7vw, 64px);
            overflow: hidden;
            border: 1px solid var(--line);
            border-radius: 28px;
            background: rgba(255, 255, 255, .94);
            box-shadow: 0 24px 70px -35px rgba(74, 25, 20, .35);
            text-align: center;
        }

        .error-card::after {
            content: "404";
            position: absolute;
            right: -18px;
            bottom: -42px;
            color: var(--brand-soft);
            font-size: clamp(110px, 24vw, 190px);
            font-weight: 700;
            line-height: 1;
            pointer-events: none;
        }

        .logo-ring {
            position: relative;
            z-index: 1;
            width: 86px;
            height: 86px;
            margin: 0 auto 28px;
            display: grid;
            place-items: center;
            padding: 12px;
            border: 1px solid rgba(179, 38, 30, .18);
            border-radius: 22px;
            background: #fff;
            box-shadow: 0 12px 30px -18px rgba(122, 26, 21, .65);
        }

        .logo-ring img { width: 100%; height: 100%; object-fit: contain; }

        .content { position: relative; z-index: 1; }

        .code {
            margin: 0 0 10px;
            color: var(--brand);
            font-size: 13px;
            font-weight: 700;
            letter-spacing: .22em;
            text-transform: uppercase;
        }

        h1 {
            margin: 0;
            font-size: clamp(32px, 7vw, 48px);
            letter-spacing: -.04em;
            line-height: 1.1;
        }

        .message {
            max-width: 500px;
            margin: 18px auto 0;
            color: var(--muted);
            font-size: 16px;
            line-height: 1.7;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 12px;
            margin-top: 32px;
        }

        .button {
            display: inline-flex;
            align-items: center;
            justify-content: center;
            min-height: 46px;
            padding: 0 20px;
            border: 1px solid var(--line);
            border-radius: 12px;
            color: var(--ink);
            background: #fff;
            font-size: 14px;
            font-weight: 600;
            text-decoration: none;
            transition: transform .18s ease, border-color .18s ease, box-shadow .18s ease;
        }

        .button:hover {
            transform: translateY(-2px);
            border-color: rgba(179, 38, 30, .35);
            box-shadow: 0 10px 24px -16px rgba(122, 26, 21, .6);
        }

        .button-primary { border-color: var(--brand); background: var(--brand); color: #fff; }
        .button-primary:hover { border-color: var(--brand-deep); background: var(--brand-deep); }

        @media (max-width: 480px) {
            .actions { flex-direction: column; }
            .button { width: 100%; }
        }
    </style>
</head>
<body>
    <main class="error-card">
        <div class="logo-ring">
            <img src="{{ asset('images/logo.png') }}" alt="Prabhu Insurance Limited">
        </div>

        <div class="content">
            <p class="code">Error 404</p>
            <h1>Page not found</h1>
            <p class="message">The page you’re looking for may have been moved, renamed, or is no longer available.</p>

            <div class="actions">
                <a href="{{ route('public.view') }}" class="button button-primary">Return to dashboard</a>
                <a href="https://prabhuinsurance.com" target="_blank" rel="noopener noreferrer" class="button">Visit main website</a>
            </div>
        </div>
    </main>
</body>
</html>
