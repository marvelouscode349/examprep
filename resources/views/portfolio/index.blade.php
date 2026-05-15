<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Marvelous Peter — Laravel Backend Developer</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link
        href="https://fonts.googleapis.com/css2?family=Cabinet+Grotesk:wght@400;500;700;800;900&family=Fraunces:ital,opsz,wght@0,9..144,300;1,9..144,300;1,9..144,400&family=DM+Mono:wght@300;400&display=swap"
        rel="stylesheet">
    <style>
        *,
        *::before,
        *::after {
            box-sizing: border-box;
            margin: 0;
            padding: 0
        }

        :root {
            --bg: #080808;
            --bg2: #0f0f0f;
            --surface: #141414;
            --surface2: #1c1c1c;
            --border: #232323;
            --border2: #2e2e2e;
            --text: #eeebe4;
            --muted: #666560;
            --muted2: #4a4845;
            --accent: #d4f55c;
            --accent-dim: rgba(212, 245, 92, .08);
            --accent-border: rgba(212, 245, 92, .18);
            --white: #fafaf7;
        }

        html {
            scroll-behavior: smooth;
            font-size: 16px
        }

        body {
            background: var(--bg);
            color: var(--text);
            font-family: 'Cabinet Grotesk', sans-serif;
            overflow-x: hidden;
            -webkit-font-smoothing: antialiased
        }

        /* CURSOR */
        .cur,
        .cur-ring {
            position: fixed;
            border-radius: 50%;
            pointer-events: none;
            z-index: 9999;
            transform: translate(-50%, -50%)
        }

        .cur {
            width: 8px;
            height: 8px;
            background: var(--accent)
        }

        .cur-ring {
            width: 32px;
            height: 32px;
            border: 1px solid rgba(212, 245, 92, .35);
            transition: width .35s cubic-bezier(.16, 1, .3, 1), height .35s cubic-bezier(.16, 1, .3, 1), border-color .3s
        }

        body:has(a:hover) .cur-ring {
            width: 52px;
            height: 52px;
            border-color: rgba(212, 245, 92, .7)
        }

        @media(pointer:coarse) {

            .cur,
            .cur-ring {
                display: none
            }

            body {
                cursor: auto
            }
        }

        /* GRAIN */
        body::after {
            content: '';
            position: fixed;
            inset: 0;
            background-image: url("data:image/svg+xml,%3Csvg xmlns='http://www.w3.org/2000/svg'%3E%3Cfilter id='n'%3E%3CfeTurbulence type='fractalNoise' baseFrequency='.75' numOctaves='4' stitchTiles='stitch'/%3E%3C/filter%3E%3Crect width='100%25' height='100%25' filter='url(%23n)' opacity='.035'/%3E%3C/svg%3E");
            pointer-events: none;
            z-index: 9990;
            opacity: .5
        }

        /* NAV */
        nav {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            z-index: 500;
            padding: 1.5rem 3rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            transition: all .4s
        }

        nav.scrolled {
            background: rgba(8, 8, 8, .88);
            backdrop-filter: blur(24px);
            padding: 1rem 3rem;
            border-bottom: 1px solid var(--border)
        }

        .logo {
            font-size: 1rem;
            font-weight: 900;
            letter-spacing: -.03em;
            color: var(--white);
            text-decoration: none
        }

        .logo span {
            color: var(--accent)
        }

        .nav-links {
            display: flex;
            gap: 2.5rem;
            list-style: none;
            align-items: center
        }

        .nav-links a {
            color: var(--muted);
            text-decoration: none;
            font-size: .8rem;
            font-weight: 500;
            letter-spacing: .06em;
            text-transform: uppercase;
            transition: color .2s
        }

        .nav-links a:hover {
            color: var(--text)
        }

        .nav-hire {
            background: var(--accent);
            color: #080808;
            padding: .55rem 1.4rem;
            border-radius: 100px;
            font-size: .8rem;
            font-weight: 800;
            text-decoration: none;
            letter-spacing: .02em;
            transition: all .2s
        }

        .nav-hire:hover {
            background: var(--white);
            transform: translateY(-1px)
        }

        /* HERO */
        .hero {
            min-height: 100vh;
            padding: 0 3rem;
            display: grid;
            grid-template-rows: 1fr auto;
            position: relative;
            overflow: hidden
        }

        .hero-glow {
            position: absolute;
            top: -10%;
            right: -5%;
            width: 700px;
            height: 700px;
            background: radial-gradient(ellipse, rgba(212, 245, 92, .05) 0%, transparent 65%);
            pointer-events: none
        }

        .hero-glow2 {
            position: absolute;
            bottom: 10%;
            left: -15%;
            width: 500px;
            height: 500px;
            background: radial-gradient(ellipse, rgba(212, 245, 92, .03) 0%, transparent 70%);
            pointer-events: none
        }

        .hero-inner {
            display: flex;
            flex-direction: column;
            justify-content: center;
            padding-top: 6rem
        }

        .hero-badge {
            display: inline-flex;
            align-items: center;
            gap: .6rem;
            padding: .45rem 1rem .45rem .5rem;
            background: var(--surface);
            border: 1px solid var(--border2);
            border-radius: 100px;
            font-family: 'DM Mono', monospace;
            font-size: .72rem;
            color: var(--muted);
            margin-bottom: 2.5rem;
            width: fit-content
        }

        .badge-dot {
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background: rgba(212, 245, 92, .12);
            border: 1px solid var(--accent-border);
            display: flex;
            align-items: center;
            justify-content: center
        }

        .badge-dot::after {
            content: '';
            width: 6px;
            height: 6px;
            border-radius: 50%;
            background: var(--accent);
            animation: blink 2s ease infinite
        }

        @keyframes blink {

            0%,
            100% {
                opacity: 1;
                transform: scale(1)
            }

            50% {
                opacity: .5;
                transform: scale(.8)
            }
        }

        .hero h1 {
            font-size: clamp(4rem, 9vw, 8rem);
            font-weight: 900;
            line-height: .9;
            letter-spacing: -.05em;
            margin-bottom: 2rem;
            color: var(--white)
        }

        .hero h1 .italic {
            font-family: 'Fraunces', serif;
            font-style: italic;
            font-weight: 300;
            color: var(--accent);
            display: block
        }

        .hero-sub {
            font-size: 1.05rem;
            color: var(--muted);
            max-width: 480px;
            line-height: 1.75;
            margin-bottom: 3rem;
            font-weight: 400
        }

        .hero-sub strong {
            color: var(--text);
            font-weight: 600
        }

        .hero-actions {
            display: flex;
            gap: .875rem;
            flex-wrap: wrap;
            margin-bottom: 5rem
        }

        .btn-main {
            background: var(--accent);
            color: #080808;
            padding: .9rem 2.25rem;
            border-radius: 100px;
            font-size: .9rem;
            font-weight: 800;
            text-decoration: none;
            transition: all .25s;
            letter-spacing: -.01em
        }

        .btn-main:hover {
            background: var(--white);
            transform: translateY(-2px);
            box-shadow: 0 20px 40px rgba(212, 245, 92, .15)
        }

        .btn-out {
            border: 1px solid var(--border2);
            color: var(--text);
            padding: .9rem 2.25rem;
            border-radius: 100px;
            font-size: .9rem;
            font-weight: 600;
            text-decoration: none;
            transition: all .25s
        }

        .btn-out:hover {
            border-color: var(--muted2);
            transform: translateY(-2px)
        }

        .hero-bottom {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            padding: 2.5rem 0;
            border-top: 1px solid var(--border)
        }

        .stats {
            display: flex;
            gap: 4rem
        }

        .stat-n {
            font-size: 2.75rem;
            font-weight: 900;
            letter-spacing: -.05em;
            line-height: 1;
            color: var(--white)
        }

        .stat-n em {
            font-family: 'Fraunces', serif;
            font-style: italic;
            color: var(--accent);
            font-weight: 300
        }

        .stat-l {
            font-family: 'DM Mono', monospace;
            font-size: .68rem;
            color: var(--muted);
            margin-top: .4rem;
            letter-spacing: .04em;
            text-transform: uppercase
        }

        .scroll-hint {
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: .5rem;
            font-family: 'DM Mono', monospace;
            font-size: .65rem;
            color: var(--muted2);
            letter-spacing: .08em;
            text-transform: uppercase
        }

        .scroll-line {
            width: 1px;
            height: 48px;
            background: linear-gradient(to bottom, var(--accent), transparent);
            animation: scroll-anim 2s ease infinite
        }

        @keyframes scroll-anim {
            0% {
                transform: scaleY(0);
                transform-origin: top
            }

            50% {
                transform: scaleY(1);
                transform-origin: top
            }

            51% {
                transform: scaleY(1);
                transform-origin: bottom
            }

            100% {
                transform: scaleY(0);
                transform-origin: bottom
            }
        }

        /* SECTIONS */
        .wrap {
            max-width: 1080px;
            margin: 0 auto;
            padding: 0 3rem
        }

        section {
            padding: 8rem 0
        }

        .sec-label {
            font-family: 'DM Mono', monospace;
            font-size: .7rem;
            color: var(--accent);
            letter-spacing: .12em;
            text-transform: uppercase;
            margin-bottom: 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem
        }

        .sec-label::before {
            content: '';
            width: 24px;
            height: 1px;
            background: var(--accent)
        }

        h2 {
            font-size: clamp(2.25rem, 4.5vw, 3.75rem);
            font-weight: 900;
            letter-spacing: -.04em;
            line-height: 1;
            margin-bottom: 4rem;
            color: var(--white)
        }

        h2 em {
            font-family: 'Fraunces', serif;
            font-style: italic;
            font-weight: 300;
            color: var(--accent)
        }

        /* ABOUT */
        .about-grid {
            display: grid;
            grid-template-columns: 1.1fr .9fr;
            gap: 5rem;
            align-items: start
        }

        .about-text p {
            color: var(--muted);
            line-height: 1.85;
            margin-bottom: 1.4rem;
            font-size: 1rem
        }

        .about-text p strong {
            color: var(--text);
            font-weight: 700
        }

        .about-text p:first-child {
            font-size: 1.1rem
        }

        .skills-wrap {
            display: grid;
            gap: .5rem
        }

        .sk {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: .8rem 1rem;
            border-radius: .5rem;
            border: 1px solid var(--border);
            background: var(--surface);
            transition: all .25s;
            cursor: default
        }

        .sk:hover {
            border-color: var(--accent-border);
            background: var(--accent-dim);
            transform: translateX(4px)
        }

        .sk-name {
            font-size: .88rem;
            font-weight: 600;
            color: var(--text)
        }

        .sk-tag {
            font-family: 'DM Mono', monospace;
            font-size: .65rem;
            color: var(--accent);
            background: rgba(212, 245, 92, .08);
            padding: .2rem .65rem;
            border-radius: 100px;
            border: 1px solid var(--accent-border)
        }

        /* PROJECTS */
        .projects-grid {
            display: grid;
            gap: 1px;
            background: var(--border)
        }

        .proj {
            background: var(--bg);
            padding: 2.75rem;
            position: relative;
            overflow: hidden;
            transition: background .3s
        }

        .proj:hover {
            background: var(--surface)
        }

        .proj-shine {
            pointer-events: none;
            position: absolute;
            inset: 0;
            background: linear-gradient(135deg, rgba(212, 245, 92, .04) 0%, transparent 50%);
            opacity: 0;
            transition: opacity .3s
        }

        .proj:hover .proj-shine {
            opacity: 1
        }

        .proj.featured {
            background: var(--bg2)
        }

        .proj.featured:hover {
            background: var(--surface)
        }

        .proj-top {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-bottom: 1.5rem
        }

        .proj-type {
            font-family: 'DM Mono', monospace;
            font-size: .68rem;
            color: var(--muted);
            letter-spacing: .06em;
            text-transform: uppercase
        }

        .proj-ext {
            display: flex;
            align-items: center;
            gap: .4rem;
            color: var(--muted);
            font-family: 'DM Mono', monospace;
            font-size: .72rem;
            text-decoration: none;
            padding: .35rem .85rem;
            border: 1px solid var(--border2);
            border-radius: 100px;
            transition: all .2s
        }

        .proj-ext:hover {
            color: var(--accent);
            border-color: var(--accent-border)
        }

        .proj-metric {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            background: rgba(212, 245, 92, .08);
            border: 1px solid var(--accent-border);
            border-radius: 100px;
            padding: .3rem 1rem;
            font-family: 'DM Mono', monospace;
            font-size: .72rem;
            color: var(--accent);
            margin-bottom: 1rem
        }

        .proj-metric::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--accent);
            flex-shrink: 0
        }

        .proj-title {
            font-size: 1.85rem;
            font-weight: 900;
            letter-spacing: -.04em;
            color: var(--white);
            margin-bottom: .75rem;
            line-height: 1
        }

        .proj-desc {
            color: var(--muted);
            font-size: .92rem;
            line-height: 1.75;
            max-width: 640px;
            margin-bottom: 1.5rem
        }

        .feat-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(175px, 1fr));
            gap: .4rem;
            margin-bottom: 1.5rem;
            padding: 1.25rem;
            background: var(--bg);
            border: 1px solid var(--border);
            border-radius: .625rem
        }

        .feat-label {
            grid-column: 1/-1;
            font-family: 'DM Mono', monospace;
            font-size: .65rem;
            color: var(--muted2);
            letter-spacing: .08em;
            text-transform: uppercase;
            margin-bottom: .5rem
        }

        .feat-item {
            display: flex;
            align-items: center;
            gap: .5rem;
            font-size: .8rem;
            color: var(--muted);
            font-weight: 500
        }

        .feat-item::before {
            content: '';
            width: 4px;
            height: 4px;
            border-radius: 50%;
            background: var(--accent);
            flex-shrink: 0
        }

        .stack {
            display: flex;
            flex-wrap: wrap;
            gap: .4rem
        }

        .stk {
            font-family: 'DM Mono', monospace;
            font-size: .68rem;
            color: var(--muted2);
            background: var(--surface2);
            border: 1px solid var(--border);
            padding: .25rem .7rem;
            border-radius: .375rem
        }

        /* NUMBERS ROW */
        .num-row {
            display: grid;
            grid-template-columns: repeat(4, 1fr);
            gap: 1px;
            background: var(--border);
            margin-bottom: 1px
        }

        .num-cell {
            background: var(--bg);
            padding: 2.5rem;
            text-align: center
        }

        .num-big {
            font-size: 3.5rem;
            font-weight: 900;
            letter-spacing: -.05em;
            color: var(--white);
            line-height: 1
        }

        .num-big em {
            font-family: 'Fraunces', serif;
            font-style: italic;
            color: var(--accent);
            font-weight: 300
        }

        .num-lab {
            font-family: 'DM Mono', monospace;
            font-size: .68rem;
            color: var(--muted);
            letter-spacing: .06em;
            text-transform: uppercase;
            margin-top: .5rem
        }

        /* EXPERIENCE */
        .exp-list {
            display: grid;
            gap: 0
        }

        .exp-item {
            display: grid;
            grid-template-columns: 180px 1fr;
            gap: 3rem;
            padding: 2.5rem 0;
            border-top: 1px solid var(--border)
        }

        .exp-period {
            font-family: 'DM Mono', monospace;
            font-size: .72rem;
            color: var(--muted2);
            padding-top: .35rem;
            line-height: 1.6
        }

        .exp-co {
            font-size: .78rem;
            color: var(--accent);
            font-family: 'DM Mono', monospace;
            margin-bottom: .3rem
        }

        .exp-role {
            font-size: 1.15rem;
            font-weight: 800;
            color: var(--white);
            letter-spacing: -.02em;
            margin-bottom: .75rem
        }

        .exp-desc {
            font-size: .9rem;
            color: var(--muted);
            line-height: 1.75
        }

        /* CONTACT */
        .contact-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1px;
            background: var(--border)
        }

        .contact-left {
            background: var(--bg2);
            padding: 4rem
        }

        .contact-right {
            background: var(--surface);
            padding: 4rem;
            display: flex;
            flex-direction: column;
            gap: .625rem
        }

        .contact-heading {
            font-size: clamp(2rem, 4vw, 3.25rem);
            font-weight: 900;
            letter-spacing: -.04em;
            line-height: 1;
            margin-bottom: 1rem;
            color: var(--white)
        }

        .contact-heading em {
            font-family: 'Fraunces', serif;
            font-style: italic;
            font-weight: 300;
            color: var(--accent)
        }

        .contact-sub {
            color: var(--muted);
            font-size: .95rem;
            line-height: 1.7;
            margin-bottom: 2rem
        }

        .avail-badge {
            display: inline-flex;
            align-items: center;
            gap: .5rem;
            padding: .4rem .9rem;
            background: rgba(212, 245, 92, .07);
            border: 1px solid var(--accent-border);
            border-radius: 100px;
            font-family: 'DM Mono', monospace;
            font-size: .68rem;
            color: var(--accent);
            margin-bottom: 1.5rem
        }

        .avail-badge::before {
            content: '';
            width: 5px;
            height: 5px;
            border-radius: 50%;
            background: var(--accent);
            animation: blink 2s ease infinite
        }

        .clink {
            display: flex;
            align-items: center;
            gap: .875rem;
            color: var(--text);
            text-decoration: none;
            font-size: .9rem;
            padding: 1rem 1.25rem;
            border: 1px solid var(--border);
            border-radius: .625rem;
            transition: all .25s;
            background: var(--bg2)
        }

        .clink:hover {
            border-color: var(--accent-border);
            background: var(--accent-dim);
            transform: translateX(4px)
        }

        .clink:hover .carr {
            transform: translateX(3px);
            color: var(--accent)
        }

        .cicon {
            width: 34px;
            height: 34px;
            background: var(--surface2);
            border: 1px solid var(--border2);
            border-radius: .4rem;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: .85rem;
            flex-shrink: 0
        }

        .clink-text {
            flex: 1;
            font-weight: 500
        }

        .carr {
            color: var(--muted2);
            transition: all .2s;
            font-size: .85rem
        }

        footer {
            padding: 2rem 3rem;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center
        }

        .foot-l {
            font-family: 'DM Mono', monospace;
            font-size: .7rem;
            color: var(--muted2)
        }

        .foot-r {
            font-family: 'DM Mono', monospace;
            font-size: .7rem;
            color: var(--muted2);
            display: flex;
            gap: 2rem
        }

        .foot-r a {
            color: var(--muted2);
            text-decoration: none;
            transition: color .2s
        }

        .foot-r a:hover {
            color: var(--accent)
        }

        .div-line {
            height: 1px;
            background: var(--border)
        }

        /* ANIMATIONS */
        .reveal {
            opacity: 0;
            transform: translateY(28px);
            transition: opacity .7s cubic-bezier(.16, 1, .3, 1), transform .7s cubic-bezier(.16, 1, .3, 1)
        }

        .reveal.in {
            opacity: 1;
            transform: none
        }

        .reveal-d1 {
            transition-delay: .1s
        }

        .reveal-d2 {
            transition-delay: .2s
        }

        .reveal-d3 {
            transition-delay: .3s
        }

        /* RESPONSIVE */
        @media(max-width:900px) {
            nav {
                padding: 1.25rem 1.5rem
            }

            nav.scrolled {
                padding: .875rem 1.5rem
            }

            .nav-links {
                display: none
            }

            .hero {
                padding: 0 1.5rem
            }

            .hero-bottom {
                flex-direction: column;
                align-items: flex-start;
                gap: 2rem
            }

            .stats {
                gap: 2rem;
                flex-wrap: wrap
            }

            .wrap {
                padding: 0 1.5rem
            }

            section {
                padding: 5rem 0
            }

            .about-grid {
                grid-template-columns: 1fr;
                gap: 3rem
            }

            .num-row {
                grid-template-columns: repeat(2, 1fr)
            }

            .exp-item {
                grid-template-columns: 1fr;
                gap: .5rem
            }

            .contact-grid {
                grid-template-columns: 1fr
            }

            footer {
                flex-direction: column;
                gap: 1rem;
                text-align: center;
                padding: 1.5rem
            }

            .foot-r {
                justify-content: center
            }
        }
    </style>
</head>

<body>

    <div class="cur" id="cur"></div>
    <div class="cur-ring" id="ring"></div>

    <nav id="nav">
        <a href="#" class="logo">MP<span>.</span></a>
        <ul class="nav-links">
            <li><a href="#about">About</a></li>
            <li><a href="#projects">Work</a></li>
            <li><a href="#experience">Experience</a></li>
            <li>
                <div style="display:flex;gap:.75rem;align-items:center">
                    <a href="/marvelous-cv.pdf" download class="btn-out"
                        style="padding:.55rem 1.4rem;font-size:.8rem;border-radius:100px">↓ CV</a>
                </div>
            </li>
        </ul>
        <a href="#contact" class="nav-hire">Hire me</a>
    </nav>

    <!-- HERO -->
    <div class="hero">
        <div class="hero-glow"></div>
        <div class="hero-glow2"></div>
        <div class="hero-inner">
            <div class="hero-badge">
                <div class="badge-dot"></div>
                Available for remote roles &amp; freelance
            </div>
            <h1>Marvelous<span class="italic">Peter.</span></h1>
            <p class="hero-sub">Backend engineer who ships. I build <strong>AI-powered platforms</strong> that scale —
                from zero to 60K users, 15K question banks, and <strong>paying customers in 24 hours.</strong></p>
            <div class="hero-actions">
                <a href="#projects" class="btn-main">View my work</a>
                <a href="mailto:marvelouspeter90@gmail.com" class="btn-out">Get in touch</a>
                <a href="/marvelous-cv.pdf" download class="btn-out">↓ Download CV</a>
            </div>
        </div>
        <div class="hero-bottom">
            <div class="stats">
                <div>
                    <div class="stat-n">60K<em>+</em></div>
                    <div class="stat-l">Platform users</div>
                </div>
                <div>
                    <div class="stat-n">15K<em>+</em></div>
                    <div class="stat-l">Question bank</div>
                </div>
                <div>
                    <div class="stat-n">4<em>yrs</em></div>
                    <div class="stat-l">Shipping</div>
                </div>
                <div>
                    <div class="stat-n">8<em>+</em></div>
                    <div class="stat-l">AI features</div>
                </div>
            </div>
            <div class="scroll-hint">
                <div class="scroll-line"></div>
                Scroll
            </div>
        </div>
    </div>

    <div class="div-line"></div>

    <!-- ABOUT -->
    <section id="about">
        <div class="wrap">
            <div class="sec-label">About me</div>
            <h2>I build things that<br><em>actually work</em></h2>
            <div class="about-grid">
                <div class="about-text reveal">
                    <!-- YOUR PHOTO — replace your-photo.jpg with your actual filename -->
                    <img src="/images/peter-black-white.jpeg" alt="Marvelous Peter"
                        style="width:100%;max-width:280px;height:340px;object-fit:cover;object-position:top;border:1px solid var(--border2);filter:grayscale(15%);margin-bottom:2rem;display:block;transition:filter .3s"
                        onmouseover="this.style.filter='grayscale(0%)'" onmouseout="this.style.filter='grayscale(25%)'">
                    <!-- END PHOTO -->
                    <p>I'm a <strong>backend-heavy full stack developer</strong> based in Nigeria with 4 years of
                        experience turning real problems into scalable web systems people actually use.</p>
                    <p>At RSJobHub I grew from full stack developer into the engineer responsible for <strong>all
                            backend infrastructure and AI integrations</strong> — scaling to 60,000+ active users while
                        shipping 8+ AI-powered features built on OpenAI GPT-4.</p>
                    <p>I independently built and launched <strong>ExamPrep NG</strong> — a full SaaS platform I
                        designed, coded, deployed, and grew entirely alone. Zero funding. Paying users within 24 hours
                        of going live.</p>
                    <p>Currently serving as <strong>Lead Backend Engineer at RSJobHub</strong>, owning every technical
                        decision from architecture to deployment.</p>
                </div>
                <div class="skills-wrap reveal reveal-d1">
                    <div class="sk"><span class="sk-name">PHP &amp; Laravel 11</span><span
                            class="sk-tag">Core</span></div>
                    <div class="sk"><span class="sk-name">OpenAI GPT-4 API</span><span class="sk-tag">AI</span>
                    </div>
                    <div class="sk"><span class="sk-name">RESTful API Design</span><span
                            class="sk-tag">Backend</span></div>
                    <div class="sk"><span class="sk-name">MySQL &amp; Optimization</span><span
                            class="sk-tag">Database</span></div>
                    <div class="sk"><span class="sk-name">Ubuntu VPS &amp; Nginx</span><span
                            class="sk-tag">DevOps</span></div>
                    <div class="sk"><span class="sk-name">AWS SES &amp; EC2</span><span class="sk-tag">Cloud</span>
                    </div>
                    <div class="sk"><span class="sk-name">Paystack &amp; Stripe</span><span
                            class="sk-tag">Payments</span></div>
                    <div class="sk"><span class="sk-name">Vanilla JS PWA</span><span class="sk-tag">Frontend</span>
                    </div>
                    <div class="sk"><span class="sk-name">HTML &amp; CSS</span><span class="sk-tag">Frontend</span>
                    </div>
                    <div class="sk"><span class="sk-name">Bootstrap</span><span class="sk-tag">Frontend</span>
                    </div>
                    <div class="sk"><span class="sk-name">Node.js &amp; Express</span><span
                            class="sk-tag">Backend</span></div>
                </div>
            </div>
        </div>
    </section>

    <div class="div-line"></div>

    <!-- PROJECTS -->
    <section id="projects" style="padding:0">
        <div class="wrap" style="padding-top:8rem;padding-bottom:3rem">
            <div class="sec-label">Selected work</div>
            <h2>Projects I'm <em>proud of</em></h2>
        </div>

        <div class="num-row">
            <div class="num-cell reveal">
                <div class="num-big">60K<em>+</em></div>
                <div class="num-lab">Active users</div>
            </div>
            <div class="num-cell reveal reveal-d1">
                <div class="num-big">15K<em>+</em></div>
                <div class="num-lab">Questions built</div>
            </div>
            <div class="num-cell reveal reveal-d2">
                <div class="num-big">24<em>hr</em></div>
                <div class="num-lab">To first paying user</div>
            </div>
            <div class="num-cell reveal reveal-d3">
                <div class="num-big">8<em>+</em></div>
                <div class="num-lab">AI features shipped</div>
            </div>
        </div>

        <div class="projects-grid">
            <div class="proj featured reveal">
                <div class="proj-shine"></div>
                <div class="proj-top">
                    <span class="proj-type">SaaS · Job Tech · AI · Production</span>
                    <a href="https://rsjobhub.com" target="_blank" class="proj-ext">↗ rsjobhub.com</a>
                </div>
                <div class="proj-metric">60,000+ active users</div>
                <div class="proj-title">RSJobHub</div>
                <p class="proj-desc">An AI-powered job and career growth platform built on Laravel. Scaled from zero to
                    60K+ active users. Migrated from shared hosting to Ubuntu VPS. Shipped 8+ production AI features
                    integrated with OpenAI GPT-4.</p>
                <div class="feat-grid">
                    <div class="feat-label">Features I built</div>
                    <div class="feat-item">AI CV Builder</div>
                    <div class="feat-item">Audio Mock Interviews</div>
                    <div class="feat-item">Career Insights Engine</div>
                    <div class="feat-item">Smart Job Matching</div>
                    <div class="feat-item">CV vs JD Analysis</div>
                    <div class="feat-item">Skill Assessment</div>
                    <div class="feat-item">AI Salary Insights</div>
                    <div class="feat-item">Email Sequencing</div>
                    <div class="feat-item">WhatsApp Job Alerts</div>
                    <div class="feat-item">AI Autosave Jobs</div>
                    <div class="feat-item">Payment Systems</div>
                    <div class="feat-item">Automated Job API</div>
                </div>
                <div class="stack">
                    <span class="stk">Laravel 11</span><span class="stk">OpenAI GPT-4</span><span
                        class="stk">MySQL</span><span class="stk">AWS SES</span><span class="stk">Ubuntu
                        VPS</span><span class="stk">Nginx</span><span class="stk">Redis</span><span
                        class="stk">JavaScript</span>
                </div>
            </div>

            <div class="proj featured reveal">
                <div class="proj-shine"></div>
                <div class="proj-top">
                    <span class="proj-type">SaaS · EdTech · Mobile-First · Solo Build</span>
                    <a href="https://myexamprep.online" target="_blank" class="proj-ext">↗ myexamprep.online</a>
                </div>
                <div class="proj-metric">Paying users in 24hrs · Zero funding · 15,000+ questions</div>
                <div class="proj-title">ExamPrep NG</div>
                <p class="proj-desc">Built entirely alone. A mobile-first SaaS exam prep platform for Nigerian students
                    sitting JAMB and WAEC. 15,000+ question bank with custom importers, automated classification, and
                    AI-powered study tools.</p>
                <div class="feat-grid">
                    <div class="feat-label">Features I built</div>
                    <div class="feat-item">AI Explanations</div>
                    <div class="feat-item">Personalized Study Guide</div>
                    <div class="feat-item">AI Topic Notes</div>
                    <div class="feat-item">Weak Area Detection</div>
                    <div class="feat-item">Custom Question Importers</div>
                    <div class="feat-item">Subscription Payments</div>
                </div>
                <div class="stack">
                    <span class="stk">Laravel 11</span><span class="stk">OpenAI GPT-4</span><span
                        class="stk">Vanilla JS PWA</span><span class="stk">Paystack</span> <span
                        class="stk">MySQL</span><span class="stk">PHP 8.2-FPM</span>
                </div>
            </div>

            <div class="proj reveal">
                <div class="proj-shine"></div>
                <div class="proj-top">
                    <span class="proj-type">Enterprise System · Logistics · Freelance</span>
                </div>
                <div class="proj-title">Logistics Management System</div>
                <p class="proj-desc">Custom logistics management system with fleet tracking, driver management,
                    delivery assignment, and operations reporting. Multi-role access control for admin, dispatcher, and
                    driver roles. Full deployment included.</p>
                <div class="stack">
                    <span class="stk">Laravel</span><span class="stk">MySQL</span><span class="stk">REST
                        API</span><span class="stk">Role-Based Auth</span><span class="stk">Ubuntu
                        VPS</span><span class="stk">Nginx</span>
                </div>
            </div>
        </div>
    </section>

    <div class="div-line"></div>

    <!-- EXPERIENCE -->
    <section id="experience">
        <div class="wrap">
            <div class="sec-label">Experience</div>
            <h2>Where I've <em>worked</em></h2>
            <div class="exp-list">
                <div class="exp-item reveal">
                    <div class="exp-period">Jun 2024<br>— Present</div>
                    <div>
                        <div class="exp-co">RSJobHub · rsjobhub.com</div>
                        <div class="exp-role">Backend Engineer &amp; AI Features Developer</div>
                        <div class="exp-desc">Core backend engineer for platform infrastructure, API architecture,
                            database management, and all AI integrations. Scaled from zero to 60K+ users. Built 8+
                            AI-powered features on OpenAI GPT-4. Migrated to Ubuntu VPS with AWS SES and Redis caching.
                        </div>
                    </div>
                </div>
                <div class="exp-item reveal reveal-d1">
                    <div class="exp-period">Sep 2025<br>— Present</div>
                    <div>
                        <div class="exp-co">ExamPrep NG · myexamprep.online</div>
                        <div class="exp-role">Founder &amp; Full Stack Developer</div>
                        <div class="exp-desc">Independently built and launched a mobile-first SaaS exam platform from
                            scratch. Own all product, backend, DevOps, AI, and payment decisions. 15,000+ question bank.
                            Paying users within 24 hours of launch with zero marketing budget.</div>
                    </div>
                </div>
                <div class="exp-item reveal reveal-d2">
                    <div class="exp-period">2023 — 2024</div>
                    <div>
                        <div class="exp-co">Private Clients · Freelance</div>
                        <div class="exp-role">Freelance Laravel Developer</div>
                        <div class="exp-desc">Built custom web systems for clients including a full logistics
                            management system with fleet tracking, driver management, delivery assignment, and
                            multi-role dashboards. Full deployment and server management included.</div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <div class="div-line"></div>

    <!-- CONTACT -->
    <section id="contact" style="padding:0">
        <div class="contact-grid">
            <div class="contact-left reveal">
                <div class="avail-badge">Available now</div>
                <div class="contact-heading">Let's build<br>something <em>great.</em></div>
                <p class="contact-sub">Open to remote roles, freelance projects, and founding engineer opportunities. I
                    respond fast — usually within a few hours.</p>
                <div style="display:flex;gap:1rem;flex-wrap:wrap">
                    <a href="mailto:marvelouspeter90@gmail.com" class="btn-main">Send an email</a>
                    <a href="https://wa.me/2347012337556" target="_blank" class="btn-out">WhatsApp me</a>
                </div>
            </div>
            <div class="contact-right reveal reveal-d1">
                <a href="mailto:marvelouspeter90@gmail.com" class="clink">
                    <div class="cicon">✉</div><span class="clink-text">marvelouspeter90@gmail.com</span><span
                        class="carr">→</span>
                </a>
                <a href="https://wa.me/2347012337556" target="_blank" class="clink">
                    <div class="cicon">💬</div><span class="clink-text">+234 701 233 7556</span><span
                        class="carr">→</span>
                </a>
                <a href="https://rsjobhub.com" target="_blank" class="clink">
                    <div class="cicon">🔗</div><span class="clink-text">rsjobhub.com</span><span
                        class="carr">→</span>
                </a>
                <a href="https://myexamprep.online" target="_blank" class="clink">
                    <div class="cicon">🔗</div><span class="clink-text">myexamprep.online</span><span
                        class="carr">→</span>
                </a>
            </div>
        </div>
    </section>

    <footer>
        <div class="foot-l">© 2026 Marvelous Peter · Benin City, Nigeria · Available worldwide</div>
        <div class="foot-r">
            <a href="https://rsjobhub.com" target="_blank">RSJobHub</a>
            <a href="https://myexamprep.online" target="_blank">ExamPrep</a>
            <a href="mailto:marvelouspeter90@gmail.com">Email</a>
        </div>
    </footer>

    <script>
        // Cursor
        const cur = document.getElementById('cur'),
            ring = document.getElementById('ring');
        let mx = 0,
            my = 0,
            rx = 0,
            ry = 0;
        document.addEventListener('mousemove', e => {
            mx = e.clientX;
            my = e.clientY;
            cur.style.left = mx + 'px';
            cur.style.top = my + 'px'
        });
        (function loop() {
            rx += (mx - rx) * .12;
            ry += (my - ry) * .12;
            ring.style.left = rx + 'px';
            ring.style.top = ry + 'px';
            requestAnimationFrame(loop)
        })();

        // Nav scroll
        const nav = document.getElementById('nav');
        window.addEventListener('scroll', () => nav.classList.toggle('scrolled', scrollY > 60), {
            passive: true
        });

        // Reveal on scroll
        const obs = new IntersectionObserver(e => e.forEach(x => {
            if (x.isIntersecting) {
                x.target.classList.add('in');
                obs.unobserve(x.target)
            }
        }), {
            threshold: .08
        });
        document.querySelectorAll('.reveal').forEach(el => obs.observe(el));
    </script>
</body>

</html>
