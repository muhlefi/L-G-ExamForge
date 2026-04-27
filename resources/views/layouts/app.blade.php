<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'APAL AI - Smart Question Generator')</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <style>
        :root {
            --primary: #6366f1;
            --primary-light: #818cf8;
            --primary-dark: #4f46e5;
            --bg-body: #0a0f1d;
            --bg-card: #151c2f;
            --bg-sidebar: #0f172a;
            --text-main: #f8fafc;
            --text-muted: #94a3b8;
            --border: rgba(255, 255, 255, 0.06);
            --accent: #10b981;
            --danger: #ef4444;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 20px 25px -5px rgb(0 0 0 / 0.1);
        }

        * { margin: 0; padding: 0; box-sizing: border-box; font-family: 'Plus Jakarta Sans', sans-serif; }
        
        body { 
            background-color: var(--bg-body); 
            color: var(--text-main); 
            overflow-x: hidden;
            scroll-behavior: smooth;
        }

        /* Scrollbar */
        ::-webkit-scrollbar { width: 6px; }
        ::-webkit-scrollbar-track { background: var(--bg-body); }
        ::-webkit-scrollbar-thumb { background: #334155; border-radius: 10px; }

        .app-layout { display: flex; min-height: 100vh; }

        /* Sidebar */
        .sidebar {
            width: 280px;
            background: var(--bg-sidebar);
            border-right: 1px solid var(--border);
            position: fixed;
            height: 100vh;
            display: flex;
            flex-direction: column;
            padding: 2rem 1.25rem;
            z-index: 50;
        }

        .logo {
            display: flex;
            align-items: center;
            gap: 0.75rem;
            font-size: 1.5rem;
            font-weight: 800;
            letter-spacing: -0.02em;
            background: linear-gradient(135deg, #fff 0%, #818cf8 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            margin-bottom: 2.5rem;
        }

        .sidebar-title {
            font-size: 0.7rem;
            font-weight: 700;
            color: var(--text-muted);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            margin-bottom: 1rem;
            padding-left: 0.5rem;
        }

        .nav-list { list-style: none; flex: 1; overflow-y: auto; padding-right: 0.5rem; }
        .nav-item { margin-bottom: 0.5rem; }
        .nav-link {
            display: block;
            padding: 0.875rem 1rem;
            color: var(--text-muted);
            text-decoration: none;
            border-radius: 0.75rem;
            font-weight: 500;
            font-size: 0.9rem;
            transition: all 0.2s;
            border: 1px solid transparent;
        }
        .nav-link:hover {
            background: rgba(255, 255, 255, 0.03);
            color: white;
        }
        .nav-link.active {
            background: rgba(99, 102, 241, 0.1);
            color: var(--primary-light);
            border-color: rgba(99, 102, 241, 0.2);
        }

        /* Main Content */
        .main-container {
            flex: 1;
            margin-left: 280px;
            padding: 2.5rem;
            max-width: 1400px;
        }

        /* Cards & Components */
        .glass-card {
            background: var(--bg-card);
            border-radius: 1.25rem;
            border: 1px solid var(--border);
            padding: 2rem;
            box-shadow: var(--shadow-lg);
        }

        .section-title { font-size: 1.5rem; font-weight: 700; margin-bottom: 0.5rem; }
        .section-desc { color: var(--text-muted); font-size: 0.9rem; margin-bottom: 2rem; }

        /* Form Styling */
        .form-label { display: block; font-size: 0.85rem; font-weight: 600; margin-bottom: 0.6rem; color: var(--text-muted); }
        .form-input {
            width: 100%;
            padding: 0.875rem 1.125rem;
            background: #0f172a;
            border: 1px solid #334155;
            border-radius: 0.75rem;
            color: white;
            font-size: 0.95rem;
            transition: all 0.2s;
        }
        .form-input:focus {
            outline: none;
            border-color: var(--primary);
            box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15);
        }

        .btn {
            padding: 0.875rem 1.5rem;
            border-radius: 0.75rem;
            font-weight: 700;
            font-size: 0.95rem;
            cursor: pointer;
            transition: all 0.2s;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            gap: 0.6rem;
            border: none;
        }
        .btn-primary { background: linear-gradient(135deg, var(--primary) 0%, var(--primary-dark) 100%); color: white; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 10px 15px -3px rgba(99, 102, 241, 0.4); }
        .btn-secondary { background: rgba(255, 255, 255, 0.05); color: white; border: 1px solid var(--border); }
        .btn-secondary:hover { background: rgba(255, 255, 255, 0.1); }

        /* Question Cards */
        .q-card {
            background: var(--bg-card);
            border-radius: 1rem;
            border: 1px solid var(--border);
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            position: relative;
            transition: all 0.3s;
        }
        .q-card:hover { border-color: rgba(99, 102, 241, 0.3); transform: scale(1.01); }

        .badge {
            padding: 0.35rem 0.85rem;
            border-radius: 0.6rem;
            font-size: 0.7rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }
        .badge-blue { background: rgba(59, 130, 246, 0.1); color: #60a5fa; }
        .badge-green { background: rgba(16, 185, 129, 0.1); color: #34d399; }

        /* Print Modal */
        .modal-overlay {
            display: none;
            position: fixed;
            top: 0; left: 0; right: 0; bottom: 0;
            background: rgba(0,0,0,0.85);
            backdrop-filter: blur(8px);
            z-index: 2000;
            padding: 2rem;
            overflow-y: auto;
        }
        .paper-view {
            background: white;
            color: #1a1a1a;
            width: 210mm;
            margin: 0 auto;
            padding: 25mm;
            min-height: 297mm;
            box-shadow: 0 25px 50px -12px rgba(0,0,0,0.5);
            border-radius: 4px;
        }

        @media print {
            body { background: white; }
            .no-print { display: none !important; }
            .modal-overlay { position: static; background: none; padding: 0; overflow: visible; }
            .paper-view { box-shadow: none; margin: 0; width: 100%; padding: 0; }
        }

        /* Utilities */
        .loading-dot {
            width: 8px; height: 8px; background: white; border-radius: 50%;
            display: inline-block; animation: dotPulse 1.5s infinite ease-in-out;
        }
        @keyframes dotPulse { 0%, 100% { opacity: 0.4; } 50% { opacity: 1; transform: scale(1.2); } }
    </style>
    @stack('styles')
</head>
<body>
    <div class="app-layout">
        @include('components.sidebar')

        <main class="main-container">
            @yield('content')
        </main>
    </div>

    @yield('modals')
    @stack('scripts')
</body>
</html>
