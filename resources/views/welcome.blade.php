<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ config('app.name', 'CMS Boilerplate') }} - Starter</title>

    @php
        $defaultPanel = config('admin.default_panel', 'filament');
        $filamentPath = '/' . ltrim(config('admin.panels.filament.path', 'admin'), '/');
        $inertiaPath = '/' . ltrim(config('admin.panels.inertia.path', 'dashboard'), '/');
        $defaultPanelPath = $defaultPanel === 'inertia' ? $inertiaPath : $filamentPath;
    @endphp

    <style>
        :root {
            color-scheme: light dark;
            --bg: #0f172a;
            --surface: #111827;
            --card: #1f2937;
            --text: #e5e7eb;
            --muted: #9ca3af;
            --primary: #60a5fa;
            --accent: #34d399;
            --border: #374151;
        }

        @media (prefers-color-scheme: light) {
            :root {
                --bg: #f8fafc;
                --surface: #ffffff;
                --card: #f8fafc;
                --text: #0f172a;
                --muted: #475569;
                --primary: #2563eb;
                --accent: #059669;
                --border: #dbeafe;
            }
        }

        * { box-sizing: border-box; }

        body {
            margin: 0;
            font-family: Inter, system-ui, -apple-system, Segoe UI, Roboto, Helvetica, Arial, sans-serif;
            background: var(--bg);
            color: var(--text);
        }

        .container {
            max-width: 1100px;
            margin: 0 auto;
            padding: 32px 20px 56px;
        }

        .hero {
            background: linear-gradient(135deg, color-mix(in srgb, var(--surface) 90%, var(--primary) 10%), var(--surface));
            border: 1px solid var(--border);
            border-radius: 16px;
            padding: 28px;
            margin-bottom: 20px;
        }

        .title {
            margin: 0 0 8px;
            font-size: clamp(28px, 5vw, 40px);
            line-height: 1.1;
        }

        .lead {
            margin: 0;
            color: var(--muted);
            max-width: 780px;
        }

        .pill {
            display: inline-block;
            margin-bottom: 14px;
            padding: 6px 10px;
            border-radius: 999px;
            border: 1px solid var(--border);
            color: var(--accent);
            font-size: 12px;
            letter-spacing: 0.02em;
            text-transform: uppercase;
            font-weight: 600;
        }

        .actions {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-top: 18px;
        }

        .btn {
            display: inline-block;
            text-decoration: none;
            padding: 10px 14px;
            border-radius: 10px;
            border: 1px solid var(--border);
            color: var(--text);
            font-weight: 600;
            font-size: 14px;
        }

        .btn.primary {
            background: var(--primary);
            border-color: transparent;
            color: white;
        }

        .grid {
            display: grid;
            gap: 14px;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            margin-bottom: 14px;
        }

        .card {
            background: var(--card);
            border: 1px solid var(--border);
            border-radius: 12px;
            padding: 16px;
        }

        .card h3 {
            margin: 0 0 10px;
            font-size: 16px;
        }

        ul {
            margin: 0;
            padding-left: 18px;
            color: var(--muted);
        }

        li { margin: 4px 0; }

        pre {
            margin: 0;
            white-space: pre-wrap;
            word-break: break-word;
            font-size: 13px;
            color: var(--muted);
        }

        code {
            font-family: ui-monospace, SFMono-Regular, Menlo, Monaco, Consolas, Liberation Mono, Courier New, monospace;
        }

        .footer-note {
            color: var(--muted);
            font-size: 13px;
            margin-top: 8px;
        }
    </style>
</head>
<body>
<div class="container">
    <section class="hero">
        <span class="pill">Laravel CMS Boilerplate</span>
        <h1 class="title">Build faster with a reusable CMS starter</h1>
        <p class="lead">
            Filament-first template with dual-panel architecture (Filament or Inertia), seeded demo content,
            and docs-driven setup so each new project starts from a stable base.
        </p>

        <div class="actions">
            <a class="btn primary" href="{{ $defaultPanelPath }}">Open Default Admin ({{ strtoupper($defaultPanel) }})</a>
            <a class="btn" href="{{ $filamentPath }}">Open Filament Panel</a>
            <a class="btn" href="{{ $inertiaPath }}">Open Inertia Path</a>
        </div>
    </section>

    <section class="grid">
        <article class="card">
            <h3>What is already included</h3>
            <ul>
                <li>Filament admin setup with CMS resources.</li>
                <li>Roles and permissions (admin/editor).</li>
                <li>Media, activity log, and slugs packages.</li>
                <li>Dual-admin switching strategy via config/env.</li>
            </ul>
        </article>

        <article class="card">
            <h3>Quick start (local)</h3>
            <pre><code>php artisan migrate:fresh --seed
php artisan serve</code></pre>
            <p class="footer-note">Seeded admin: <code>admin@example.com</code> / <code>password</code> (dev only)</p>
        </article>

        <article class="card">
            <h3>MySQL example from docs</h3>
            <pre><code>DB_CONNECTION=mysql
DB_DATABASE=cms-filament
DB_USERNAME=filament
DB_PASSWORD=filament</code></pre>
            <p class="footer-note">See <code>docs/database-setup.md</code> for DB/user/grant steps.</p>
        </article>
    </section>

    <section class="grid">
        <article class="card">
            <h3>Suggested project flow</h3>
            <ul>
                <li>Start Filament-first for fast delivery.</li>
                <li>Use <code>ADMIN_PANEL=filament|inertia</code> per project.</li>
                <li>Add domain rules in app/services, not panel UI classes.</li>
                <li>Switch panel later with <code>scripts/switch-admin-panel.sh</code>.</li>
            </ul>
        </article>

        <article class="card">
            <h3>Docs to open next</h3>
            <ul>
                <li><code>docs/filament-user-manual.md</code></li>
                <li><code>docs/database-setup.md</code></li>
                <li><code>docs/dual-admin-architecture.md</code></li>
                <li><code>docs/admin-panel-switching.md</code></li>
                <li><code>docs/install-bundles.md</code></li>
            </ul>
        </article>
    </section>
</div>
</body>
</html>
