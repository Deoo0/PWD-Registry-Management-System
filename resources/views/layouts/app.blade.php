<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'PWD Registry') — DSWD</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;600&family=Plus+Jakarta+Sans:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        :root {
            --navy:      #08214a;
            --blue:      #1549a8;
            --blue-h:    #1a5bc4;
            --blue-lt:   #dbeafe;
            --blue-xlt:  #eff6ff;
            --gold:      #d97706;
            --gold-lt:   #fef3c7;
            --green:     #047857;
            --green-lt:  #d1fae5;
            --red:       #b91c1c;
            --red-lt:    #fee2e2;
            --ink:       #0f1f3d;
            --s50:  #f8fafc; --s100: #f1f5f9; --s200: #e2e8f0;
            --s300: #cbd5e1; --s400: #94a3b8; --s500: #64748b;
            --s600: #475569; --s700: #334155; --s800: #1e293b;
            --sw: 248px; --th: 58px;
        }

        *,*::before,*::after { box-sizing: border-box; margin: 0; padding: 0; }
        body { font-family: 'Plus Jakarta Sans', sans-serif; background: #f0f4f9; color: var(--ink); min-height: 100vh; font-size: 13.5px; -webkit-font-smoothing: antialiased; }
        .serif { font-family: 'Playfair Display', serif; }
        ::-webkit-scrollbar { width: 4px; }
        ::-webkit-scrollbar-thumb { background: var(--s300); border-radius: 99px; }
        a { text-decoration: none; }

        /* ─── SIDEBAR ─────────────────────────────── */
        #sb {
            position: fixed; inset: 0 auto 0 0; width: var(--sw);
            background: var(--navy);
            display: flex; flex-direction: column;
            z-index: 100;
            box-shadow: 4px 0 32px rgba(8,33,74,.18);
        }
        #sb::after {
            content: '';
            position: absolute; inset: 0;
            background:
                radial-gradient(ellipse at 10% 0%, rgba(21,73,168,.45) 0%, transparent 55%),
                radial-gradient(ellipse at 90% 100%, rgba(217,119,6,.08) 0%, transparent 55%);
            pointer-events: none;
        }

        .sb-top {
            position: relative; z-index: 1;
            padding: 18px 16px 14px;
            border-bottom: 1px solid rgba(255,255,255,.07);
            display: flex; align-items: center; gap: 10px;
        }
        .sb-logo {
            width: 34px; height: 34px; border-radius: 8px;
            background: #FDD835; display: flex; align-items: center; justify-content: center; flex-shrink: 0;
        }
        .sb-sys { font-family: 'Playfair Display', serif; font-size: 13px; color: white; line-height: 1.25; }
        .sb-ver { font-size: 9px; color: rgba(255,255,255,.28); text-transform: uppercase; letter-spacing: .12em; margin-top: 1px; }

        .sb-nav { position: relative; z-index: 1; flex: 1; padding: 10px 10px; overflow-y: auto; }
        .sb-sec { font-size: 8.5px; font-weight: 700; color: rgba(255,255,255,.2); text-transform: uppercase; letter-spacing: .15em; padding: 14px 8px 4px; }

        .nav-a {
            display: flex; align-items: center; gap: 9px;
            padding: 8px 10px; border-radius: 8px;
            color: rgba(255,255,255,.48); font-size: 13px; font-weight: 500;
            margin-bottom: 1px; transition: all .14s; position: relative;
        }
        .nav-a:hover { color: rgba(255,255,255,.82); background: rgba(255,255,255,.06); }
        .nav-a.on {
            color: white; background: rgba(255,255,255,.1);
            box-shadow: inset 3px 0 0 #FDD835;
        }
        .ni {
            width: 30px; height: 30px; border-radius: 7px;
            display: flex; align-items: center; justify-content: center;
            background: rgba(255,255,255,.06); flex-shrink: 0; transition: background .14s;
        }
        .nav-a.on .ni { background: rgba(255,255,255,.11); }
        .ni svg { width: 15px; height: 15px; }
        .nb { margin-left: auto; background: #FDD835; color: var(--navy); font-size: 9px; font-weight: 800; padding: 1px 6px; border-radius: 99px; }

        .sb-foot { position: relative; z-index: 1; padding: 10px; border-top: 1px solid rgba(255,255,255,.07); }
        .u-card { display: flex; align-items: center; gap: 9px; padding: 8px 10px; border-radius: 8px; background: rgba(255,255,255,.05); }
        .u-av { width: 30px; height: 30px; border-radius: 50%; background: linear-gradient(135deg,#f59e0b,#dc2626); display: flex; align-items: center; justify-content: center; font-size: 10.5px; font-weight: 800; color: white; flex-shrink: 0; }
        .u-nm { font-size: 11.5px; font-weight: 600; color: white; line-height: 1.25; }
        .u-rl { font-size: 9.5px; color: rgba(255,255,255,.3); }

        /* ─── TOPBAR ──────────────────────────────── */
        #tb {
            position: fixed; top: 0; left: var(--sw); right: 0;
            height: var(--th); background: white;
            border-bottom: 1px solid var(--s200);
            display: flex; align-items: center; justify-content: space-between;
            padding: 0 22px; z-index: 90;
            box-shadow: 0 1px 6px rgba(0,0,0,.04);
        }
        .tb-title { font-family: 'Playfair Display', serif; font-size: 17px; color: var(--ink); }
        .tb-crumb { font-size: 11px; color: var(--s400); margin-top: 1px; display: flex; align-items: center; gap: 4px; }
        .tb-crumb a { color: var(--s400); }
        .tb-crumb a:hover { color: var(--blue); }
        .tb-r { display: flex; align-items: center; gap: 8px; }
        .tb-btn { width: 32px; height: 32px; border-radius: 7px; border: 1px solid var(--s200); background: white; display: flex; align-items: center; justify-content: center; cursor: pointer; color: var(--s500); transition: all .14s; position: relative; }
        .tb-btn:hover { background: var(--s50); }
        .tb-btn svg { width: 15px; height: 15px; }
        .tb-dot { position: absolute; top: 6px; right: 6px; width: 6px; height: 6px; border-radius: 50%; background: var(--red); border: 1.5px solid white; }
        .tb-out { display: inline-flex; align-items: center; gap: 5px; padding: 6px 12px; border-radius: 7px; border: 1px solid var(--s200); background: white; font-size: 12px; font-weight: 500; color: var(--s500); cursor: pointer; transition: all .14s; font-family: 'Plus Jakarta Sans', sans-serif; }
        .tb-out:hover { background: #fef2f2; border-color: #fecaca; color: var(--red); }
        .tb-out svg { width: 13px; height: 13px; }

        /* ─── MAIN ────────────────────────────────── */
        #main { margin-left: var(--sw); padding-top: var(--th); min-height: 100vh; }
        .pg { padding: 22px; }

        /* ─── COMPONENTS ──────────────────────────── */

        /* Page header */
        .ph { display: flex; align-items: flex-start; justify-content: space-between; margin-bottom: 20px; }
        .ph-t { font-family: 'Playfair Display', serif; font-size: 24px; color: var(--ink); line-height: 1.1; }
        .ph-s { font-size: 12px; color: var(--s400); margin-top: 2px; }

        /* Stat card */
        .sc { background: white; border-radius: 12px; border: 1px solid var(--s200); padding: 16px 18px; position: relative; overflow: hidden; transition: transform .18s, box-shadow .18s; }
        .sc:hover { transform: translateY(-2px); box-shadow: 0 6px 20px rgba(0,0,0,.07); }
        .sc-bar { position: absolute; top: 0; left: 0; right: 0; height: 3px; border-radius: 12px 12px 0 0; }
        .sc-ic { width: 36px; height: 36px; border-radius: 9px; display: flex; align-items: center; justify-content: center; margin-bottom: 10px; }
        .sc-ic svg { width: 17px; height: 17px; }
        .sc-v { font-family: 'Playfair Display', serif; font-size: 28px; line-height: 1; color: var(--ink); margin-bottom: 2px; }
        .sc-l { font-size: 12px; color: var(--s400); font-weight: 500; }

        /* Card */
        .card { background: white; border-radius: 12px; border: 1px solid var(--s200); overflow: hidden; }
        .card-hd { padding: 14px 18px; border-bottom: 1px solid var(--s100); display: flex; align-items: center; justify-content: space-between; }
        .card-t { font-family: 'Playfair Display', serif; font-size: 15px; color: var(--ink); }
        .card-st { font-size: 11px; color: var(--s400); margin-top: 1px; }

        /* Table */
        .tbl { width: 100%; border-collapse: collapse; }
        .tbl thead tr { background: var(--s50); }
        .tbl thead th { text-align: left; padding: 9px 15px; font-size: 9.5px; font-weight: 700; color: var(--s400); text-transform: uppercase; letter-spacing: .1em; border-bottom: 1px solid var(--s100); white-space: nowrap; }
        .tbl tbody tr { border-bottom: 1px solid var(--s100); transition: background .1s; }
        .tbl tbody tr:last-child { border-bottom: none; }
        .tbl tbody tr:hover { background: var(--s50); }
        .tbl tbody td { padding: 11px 15px; color: var(--s700); vertical-align: middle; }
        .cp { font-weight: 600; color: var(--ink); font-size: 13px; }
        .cs { font-size: 11px; color: var(--s400); margin-top: 1px; }

        /* Badges */
        .badge { display: inline-flex; align-items: center; gap: 4px; padding: 2px 8px; border-radius: 99px; font-size: 10.5px; font-weight: 600; }
        .bd { width: 5px; height: 5px; border-radius: 50%; }
        .b-m   { background: var(--blue-lt); color: #1e40af; }
        .b-f   { background: #fce7f3; color: #9d174d; }
        .b-adm { background: var(--red-lt); color: #991b1b; }
        .b-enc { background: var(--blue-lt); color: #1e40af; }
        .b-apr { background: var(--green-lt); color: #065f46; }
        .b-on  { background: var(--green-lt); color: #065f46; } .b-on .bd  { background: var(--green); }
        .b-off { background: var(--s100); color: var(--s500); } .b-off .bd { background: var(--s400); }

        /* Buttons */
        .btn { display: inline-flex; align-items: center; gap: 5px; padding: 8px 15px; border-radius: 8px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; font-family: 'Plus Jakarta Sans', sans-serif; transition: all .14s; }
        .btn svg { width: 14px; height: 14px; }
        .btn-p { background: var(--blue); color: white; }
        .btn-p:hover { background: var(--blue-h); box-shadow: 0 4px 12px rgba(21,73,168,.28); }
        .btn-o { background: white; color: var(--s600); border: 1px solid var(--s200); }
        .btn-o:hover { background: var(--s50); border-color: var(--s300); }
        .btn-d { background: var(--red); color: white; }
        .btn-d:hover { background: #991b1c; }
        .btn-g { background: var(--green); color: white; }
        .btn-g:hover { background: #065f46; }
        .btn-sm { padding: 5px 10px; font-size: 12px; }
        .btn-sm svg { width: 12px; height: 12px; }

        /* Form */
        .fl { display: block; font-size: 10.5px; font-weight: 700; color: var(--s500); text-transform: uppercase; letter-spacing: .08em; margin-bottom: 4px; }
        .fi { width: 100%; padding: 8px 11px; border-radius: 7px; border: 1px solid var(--s200); background: white; font-size: 13px; font-family: 'Plus Jakarta Sans', sans-serif; color: var(--s800); outline: none; transition: border-color .14s, box-shadow .14s; }
        .fi:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(21,73,168,.08); }
        .fi.err { border-color: var(--red); }
        .fe { font-size: 11px; color: var(--red); margin-top: 3px; }
        .fg { margin-bottom: 14px; }
        .g2 { display: grid; grid-template-columns: 1fr 1fr; gap: 12px; }
        .g3 { display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 12px; }
        .fsel { width: 100%; padding: 8px 28px 8px 11px; border-radius: 7px; border: 1px solid var(--s200); background: white url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 20 20' fill='%2394a3b8'%3E%3Cpath fill-rule='evenodd' d='M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z'/%3E%3C/svg%3E") no-repeat right 8px center/15px; appearance: none; font-size: 13px; font-family: 'Plus Jakarta Sans', sans-serif; color: var(--s700); outline: none; cursor: pointer; transition: border-color .14s; }
        .fsel:focus { border-color: var(--blue); }

        /* Search */
        .sw { position: relative; }
        .sw svg { position: absolute; left: 9px; top: 50%; transform: translateY(-50%); width: 14px; height: 14px; color: var(--s400); pointer-events: none; }
        .sinp { padding: 8px 11px 8px 30px; border-radius: 7px; border: 1px solid var(--s200); font-size: 13px; font-family: 'Plus Jakarta Sans', sans-serif; color: var(--s700); background: white; outline: none; width: 210px; transition: all .14s; }
        .sinp:focus { border-color: var(--blue); box-shadow: 0 0 0 3px rgba(21,73,168,.08); }

        /* Flash */
        .flash { padding: 11px 15px; border-radius: 9px; font-size: 13px; margin-bottom: 14px; display: flex; align-items: center; gap: 9px; }
        .flash svg { width: 15px; height: 15px; flex-shrink: 0; }
        .f-ok  { background: var(--green-lt); color: #065f46; border: 1px solid #a7f3d0; }
        .f-err { background: var(--red-lt);   color: #991b1b; border: 1px solid #fecaca; }

        /* Modal */
        .mbg { position: fixed; inset: 0; background: rgba(8,33,74,.5); z-index: 200; display: none; align-items: center; justify-content: center; backdrop-filter: blur(3px); }
        .mbg.open { display: flex; }
        .modal { background: white; border-radius: 14px; width: 100%; max-width: 560px; margin: 16px; box-shadow: 0 24px 64px rgba(0,0,0,.22); animation: fu .22s ease; }
        .modal-lg { max-width: 740px; }
        .mhd { padding: 18px 22px 14px; border-bottom: 1px solid var(--s100); display: flex; align-items: center; justify-content: space-between; }
        .mt { font-family: 'Playfair Display', serif; font-size: 17px; color: var(--ink); }
        .mx { width: 26px; height: 26px; border-radius: 6px; border: none; background: var(--s100); cursor: pointer; display: flex; align-items: center; justify-content: center; color: var(--s500); }
        .mx:hover { background: var(--s200); }
        .mx svg { width: 13px; height: 13px; }
        .mbd { padding: 18px 22px; }
        .mft { padding: 12px 22px; border-top: 1px solid var(--s100); display: flex; justify-content: flex-end; gap: 8px; }

        /* Empty */
        .empty { text-align: center; padding: 48px 20px; color: var(--s400); }
        .empty svg { width: 44px; height: 44px; margin: 0 auto 10px; opacity: .3; }
        .empty-t { font-size: 14px; font-weight: 600; color: var(--s500); margin-bottom: 3px; }
        .empty-s { font-size: 12px; }

        /* Pagination */
        .pag { display: flex; align-items: center; gap: 3px; padding: 12px 18px; border-top: 1px solid var(--s100); }
        .pb { width: 28px; height: 28px; border-radius: 6px; border: 1px solid var(--s200); background: white; font-size: 12px; font-weight: 600; color: var(--s500); cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all .12s; text-decoration: none; }
        .pb:hover { background: var(--s50); }
        .pb.on { background: var(--blue); border-color: var(--blue); color: white; }
        .pb svg { width: 12px; height: 12px; }
        .pi { font-size: 11.5px; color: var(--s400); margin-left: auto; }

        /* Bar chart component */
        .bar-row { display: flex; align-items: center; gap: 10px; margin-bottom: 9px; }
        .bar-lbl { font-size: 12px; color: var(--s600); flex-shrink: 0; width: 160px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis; }
        .bar-track { flex: 1; background: var(--s100); border-radius: 4px; height: 7px; }
        .bar-fill { height: 7px; border-radius: 4px; transition: width .6s cubic-bezier(.4,0,.2,1); }
        .bar-cnt { font-size: 12px; font-weight: 600; color: var(--s700); min-width: 28px; text-align: right; }

        /* Donut segment (CSS only) */
        .donut-wrap { display: flex; align-items: center; gap: 20px; padding: 16px 18px; }
        .donut-legend { flex: 1; }
        .dl-row { display: flex; align-items: center; gap: 8px; margin-bottom: 7px; font-size: 12px; }
        .dl-dot { width: 10px; height: 10px; border-radius: 3px; flex-shrink: 0; }
        .dl-lbl { color: var(--s600); flex: 1; }
        .dl-val { font-weight: 700; color: var(--ink); }
        .dl-pct { color: var(--s400); font-size: 11px; margin-left: 3px; }

        /* Animations */
        @keyframes fu { from { opacity:0; transform: translateY(10px); } to { opacity:1; transform: translateY(0); } }
        .ani { animation: fu .38s ease forwards; opacity: 0; }
        .a1{animation-delay:.04s} .a2{animation-delay:.09s} .a3{animation-delay:.14s} .a4{animation-delay:.19s} .a5{animation-delay:.24s}

        /* Mobile */
        #sb-tog { display: none; }
        @media(max-width:768px){
            #sb { transform: translateX(-100%); transition: transform .3s; }
            #sb.open { transform: translateX(0); }
            #tb { left: 0; }
            #main { margin-left: 0; }
            #sb-tog { display: flex; }
        }
    </style>
    @yield('styles')
</head>
<body>

{{-- ═══ SIDEBAR ═══ --}}
<aside id="sb">
    <div class="sb-top">
        <div class="sb-logo">
            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 32 32" style="width:24px;height:24px;">
                <rect x="1" y="1" width="30" height="30" rx="3" fill="#FDD835"/>
                <rect x="3" y="3" width="26" height="26" rx="2" fill="white"/>
                <g fill="#1565C0">
                    <ellipse cx="10" cy="21" rx="4" ry="2.5"/>
                    <rect x="7" y="13" width="2" height="7" rx="1"/>
                    <rect x="10" y="11" width="2" height="8" rx="1"/>
                    <rect x="13" y="12" width="2" height="7" rx="1"/>
                    <ellipse cx="22" cy="21" rx="4" ry="2.5"/>
                    <rect x="23" y="13" width="2" height="7" rx="1"/>
                    <rect x="20" y="11" width="2" height="8" rx="1"/>
                    <rect x="17" y="12" width="2" height="7" rx="1"/>
                </g>
                <path d="M16,20 C16,20 8,15 8,10 C8,7 10.5,5.5 13,7 C14.2,7.8 15.2,9 16,9 C16.8,9 17.8,7.8 19,7 C21.5,5.5 24,7 24,10 C24,15 16,20 16,20Z" fill="#E53935"/>
            </svg>
        </div>
        <div>
            <div class="sb-sys">PWD Registry</div>
            <div class="sb-ver">DSWD · v4.0</div>
        </div>
    </div>

    <nav class="sb-nav">
        <div class="sb-sec">Main</div>
        <a href="{{ route('dashboard') }}" class="nav-a {{ request()->routeIs('dashboard') ? 'on' : '' }}">
            <div class="ni"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"/></svg></div>
            Dashboard
        </a>

        <div class="sb-sec">Registry</div>
        <a href="{{ route('pwd.index') }}" class="nav-a {{ request()->routeIs('pwd.*') ? 'on' : '' }}">
            <div class="ni"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0z"/></svg></div>
            PWD Registry
            {{-- Backend: \App\Models\Applicant::count() --}}
            <span class="nb">{{ $sidebarTotal ?? 0 }}</span>
        </a>
        <a href="{{ route('reports.index') }}" class="nav-a {{ request()->routeIs('reports.*') ? 'on' : '' }}">
            <div class="ni"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg></div>
            Reports
        </a>

        @if(Auth::user()->usertype->name === 'Admin')
        <div class="sb-sec">Administration</div>
        <a href="{{ route('users.index') }}" class="nav-a {{ request()->routeIs('users.*') ? 'on' : '' }}">
            <div class="ni"><svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"/></svg></div>
            User Management
        </a>
        @endif
    </nav>

    <div class="sb-foot">
        <div class="u-card">
            <div class="u-av">{{ strtoupper(substr(Auth::user()->first_name,0,1)) }}{{ strtoupper(substr(Auth::user()->last_name,0,1)) }}</div>
            <div style="min-width:0;flex:1;">
                <div class="u-nm">{{ Auth::user()->first_name }} {{ Auth::user()->last_name }}</div>
                <div class="u-rl">{{ Auth::user()->usertype->name }}</div>
            </div>
        </div>
    </div>
</aside>

{{-- ═══ TOPBAR ═══ --}}
<header id="tb">
    <div>
        <div class="tb-title">@yield('page-title','Dashboard')</div>
        <div class="tb-crumb">
            <a href="{{ route('dashboard') }}">Home</a>
            @yield('breadcrumb')
        </div>
    </div>
    <div class="tb-r">
        <button id="sb-tog" class="tb-btn" onclick="document.getElementById('sb').classList.toggle('open')">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h16"/></svg>
        </button>
        <form method="POST" action="{{ route('logout') }}">
            @csrf
            <button type="submit" class="tb-out">
                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="1.8"><path stroke-linecap="round" stroke-linejoin="round" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/></svg>
                Sign Out
            </button>
        </form>
    </div>
</header>

{{-- ═══ CONTENT ═══ --}}
<main id="main">
    @if(session('success'))
    <div style="padding:14px 22px 0;">
        <div class="flash f-ok">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('success') }}
        </div>
    </div>
    @endif
    @if(session('error'))
    <div style="padding:14px 22px 0;">
        <div class="flash f-err">
            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
            {{ session('error') }}
        </div>
    </div>
    @endif

    @yield('content')
</main>

@yield('scripts')
<script>
// Close modal on backdrop click
document.querySelectorAll('.mbg').forEach(el => {
    el.addEventListener('click', e => { if (e.target === el) el.classList.remove('open'); });
});
</script>
</body>
</html>