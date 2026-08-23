<?php
$stats = $stats ?? [
    "completed_quizzes" => 0,
    "completion_rate" => 0,
    "total_score" => 0,
    "average_score" => 0,
    "categories" => ["Routing" => 0, "Firewall & NAT" => 0, "Wireless" => 0, "Network Management" => 0],
    "category_scores" => ["Routing" => 0, "Firewall & NAT" => 0, "Wireless" => 0, "Network Management" => 0],
    "recent_activities" => [],
    "unlocked_badges" => [],
    "locked_achievements" => [],
    "next_badge" => null
];

$unlockedBadges = $unlockedBadges ?? ($stats["unlocked_badges"] ?? []);
$badges = $badges ?? [];
$materials = $materials ?? [];

require_once dirname(__DIR__) . "/templates/header.php";
?>

<!-- 16x16 Pixel Art Sprite Sheet Definition (Hidden SVG) -->
<svg class="hidden" style="display:none;" xmlns="http://www.w3.org/2000/svg">
    <!-- 16-bit Gold Coin -->
    <g id="pixel-coin">
        <path fill="#F5A623" d="M3,1 h6 v1 h2 v2 h1 v6 h-1 v2 h-2 v1 h-6 v-1 h-2 v-2 h-1 v-6 h1 v-2 h2 z" />
        <path fill="#FFE17D" d="M4,2 h4 v1 h-4 z M3,3 h2 v2 h-2 z M3,5 h1 v3 h-1 z" />
        <path fill="#D48806" d="M4,9 h5 v1 h-5 z M9,3 h1 v6 h-1 z M7,10 h2 v1 h-2 z" />
        <path fill="#FFF" d="M5,4 h2 v4 h-2 z" />
    </g>

    <!-- 16-bit MikroTik Router -->
    <g id="pixel-router">
        <path fill="#27272A" d="M1,4 h14 v8 h-14 z" />
        <path fill="#52525B" d="M2,5 h12 v2 h-12 z" />
        <path fill="#000" d="M2,8 h12 v3 h-12 z" />
        <path fill="#71717A" d="M3,9 h2 v2 h-2 z M6,9 h2 v2 h-2 z M9,9 h2 v2 h-2 z" />
        <rect x="3" y="6" width="1" height="1" fill="#00FF66" />
        <rect x="5" y="6" width="1" height="1" fill="#00FF66" />
        <rect x="7" y="6" width="1" height="1" fill="#0070F3" />
        <rect x="9" y="6" width="1" height="1" fill="#FF0080" />
        <path fill="#3F3F46" d="M2,1 h1 v3 h-1 z M13,1 h1 v3 h-1 z" />
        <rect x="1" y="1" width="3" height="1" fill="#71717A" />
        <rect x="12" y="1" width="3" height="1" fill="#71717A" />
    </g>

    <!-- 16-bit CRT Computer Terminal -->
    <g id="pixel-computer">
        <path fill="#3F3F46" d="M1,1 h14 v10 h-14 z" />
        <path fill="#18181B" d="M2,2 h12 v8 h-12 z" />
        <path fill="#00FF66" d="M3,3 h3 v1 h-3 z M3,5 h6 v1 h-6 z M3,7 h4 v1 h-4 z" />
        <path fill="#52525B" d="M6,11 h4 v2 h-4 z M4,13 h8 v1 h-8 z" />
    </g>

    <!-- 16-bit AI NetBot Assistant -->
    <g id="pixel-robot">
        <path fill="#7928CA" d="M3,3 h10 v8 h-10 z" />
        <path fill="#FFF" d="M4,4 h8 v6 h-8 z" />
        <path fill="#0070F3" d="M5,5 h2 v2 h-2 z M9,5 h2 v2 h-2 z" />
        <path fill="#7928CA" d="M6,8 h4 v1 h-4 z" />
        <rect x="7.5" y="1" width="1" height="2" fill="#50E3C2" />
        <rect x="7" y="0" width="2" height="1" fill="#FF0080" />
    </g>

    <!-- 16-bit Pixel Book -->
    <g id="pixel-book">
        <path fill="#FF0080" d="M2,2 h11 v12 h-11 z" />
        <path fill="#FFF" d="M4,3 h8 v10 h-8 z" />
        <path fill="#333" d="M2,2 h2 v12 h-2 z M12,2 h1 v12 h-1 z" />
        <path fill="#0070F3" d="M6,5 h4 v1 h-4 z M6,7 h5 v1 h-5 z M6,9 h3 v1 h-3 z" />
        <path fill="#F5A623" d="M8,1 h2 v4 h-2 z" />
    </g>

    <!-- 16-bit Sparkle / Wand -->
    <g id="pixel-sparkle">
        <path fill="#50E3C2" d="M7,1 h2 v2 h-2 z M7,9 h2 v2 h-2 z M3,5 h2 v2 h-2 z M11,5 h2 v2 h-2 z" />
        <path fill="#FFF" d="M6,3 h4 v6 h-4 z M4,5 h8 v2 h-8 z" />
    </g>
</svg>

<style>
    /* ==========================================================================
       VERCEL PIXEL USER DASHBOARD - PURE DARK MODE SYSTEM
       ========================================================================== */
    .pixelated {
        image-rendering: pixelated;
    }

    /* Hero Bar */
    .dashboard-hero-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        flex-wrap: wrap;
        gap: 1rem;
        margin-bottom: 1.5rem;
        padding-bottom: 1.25rem;
        border-bottom: 1px solid #222222;
    }

    .hero-brand-group {
        display: flex;
        align-items: center;
        gap: 0.85rem;
    }

    .hero-router-box {
        width: 44px;
        height: 44px;
        background-color: #0A0A0A;
        border: 1.5px solid #50E3C2;
        border-radius: 8px;
        display: flex;
        align-items: center;
        justify-content: center;
        position: relative;
        box-shadow: 0 0 16px rgba(80, 227, 194, 0.28);
    }

    .hero-router-box .live-radar-dot {
        position: absolute;
        top: -3px;
        right: -3px;
        width: 8px;
        height: 8px;
        background-color: #10B981;
        border-radius: 50%;
        box-shadow: 0 0 8px #10B981;
        animation: radar-ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
    }

    @keyframes radar-ping {

        75%,
        100% {
            transform: scale(2);
            opacity: 0;
        }
    }

    .hero-title-area {
        display: flex;
        flex-direction: column;
        gap: 0.2rem;
    }

    .hero-breadcrumb {
        font-family: var(--font-mono);
        font-size: 0.725rem;
        color: #71717A;
        display: flex;
        align-items: center;
        gap: 0.35rem;
    }

    .hero-breadcrumb .active-tag {
        color: #50E3C2;
        font-weight: 700;
    }

    .hero-main-title {
        font-family: var(--font-heading);
        font-size: 1.5rem;
        font-weight: 800;
        color: #FFFFFF;
        letter-spacing: -0.02em;
        line-height: 1.2;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        flex-wrap: wrap;
    }

    .hero-cursor {
        color: #50E3C2;
        animation: cursor-pulse 1.2s infinite;
    }

    @keyframes cursor-pulse {

        0%,
        100% {
            opacity: 1;
        }

        50% {
            opacity: 0.2;
        }
    }

    .hero-pill-version {
        font-family: var(--font-mono);
        font-size: 0.65rem;
        font-weight: 700;
        color: #4ADE80;
        background-color: rgba(34, 197, 94, 0.12);
        border: 1px solid rgba(34, 197, 94, 0.3);
        padding: 2px 7px;
        border-radius: 4px;
    }

    .hero-pill-status {
        font-family: var(--font-mono);
        font-size: 0.65rem;
        font-weight: 600;
        color: #A1A1AA;
        background-color: #18181B;
        border: 1px solid #27272A;
        padding: 2px 7px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 0.35rem;
    }

    .hero-action-bar {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        flex-wrap: wrap;
    }

    .btn-hero-primary {
        background-color: #FFFFFF;
        color: #000000;
        border: 1.5px solid #FFFFFF;
        border-radius: 6px;
        padding: 0.55rem 1rem;
        font-family: var(--font-pixel);
        font-size: 0.775rem;
        font-weight: 800;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        box-shadow: 0 3px 0 #71717A;
        transition: transform 0.1s ease, background-color 0.15s ease, box-shadow 0.1s ease;
    }

    .btn-hero-primary:hover {
        background-color: #E4E4E7;
        color: #000000;
        box-shadow: 0 3px 0 #52525B;
    }

    .btn-hero-primary:active {
        transform: translateY(2px);
        box-shadow: 0 1px 0 #52525B;
    }

    .btn-hero-secondary {
        background-color: #0A0A0A;
        color: #E4E4E7;
        border: 1.5px solid #27272A;
        border-radius: 6px;
        padding: 0.55rem 0.9rem;
        font-family: var(--font-mono);
        font-size: 0.775rem;
        font-weight: 600;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.45rem;
        transition: border-color 0.15s ease, color 0.15s ease, background-color 0.15s ease;
    }

    .btn-hero-secondary:hover {
        border-color: #50E3C2;
        color: #FFFFFF;
        background-color: #18181B;
    }

    /* ==========================================================================
       4 QUICK METRICS GRID (VERCEL PIXEL DARK GLOW CARDS)
       ========================================================================== */
    .pixel-metrics-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.75rem;
    }

    .pixel-metric-card {
        background-color: #0A0A0A;
        border: 1.5px solid #222222;
        border-radius: 10px;
        padding: 1.15rem 1.25rem;
        position: relative;
        overflow: hidden;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        box-shadow: 0 10px 25px rgba(0, 0, 0, 0.8);
        transition: transform 0.18s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.18s ease, box-shadow 0.18s ease;
    }

    .pixel-metric-card .card-crosshair {
        position: absolute;
        font-family: var(--font-mono);
        font-size: 10px;
        font-weight: 800;
        line-height: 1;
        pointer-events: none;
        opacity: 0.85;
    }

    .pixel-metric-card .corner-tl {
        top: -5px;
        left: -5px;
    }

    .pixel-metric-card .corner-tr {
        top: -5px;
        right: -5px;
    }

    .pixel-metric-card .corner-bl {
        bottom: -5px;
        left: -5px;
    }

    .pixel-metric-card .corner-br {
        bottom: -5px;
        right: -5px;
    }

    /* Watermark Background Pixel Icon */
    .metric-watermark {
        position: absolute;
        right: -10px;
        bottom: -10px;
        width: 84px;
        height: 84px;
        opacity: 0.07;
        pointer-events: none;
        transition: opacity 0.2s ease, transform 0.2s ease;
    }

    .pixel-metric-card:hover .metric-watermark {
        opacity: 0.18;
        transform: scale(1.08) rotate(-4deg);
    }

    /* Glow Variant 1: Blue / Router */
    .glow-blue .card-crosshair {
        color: #0070F3;
    }

    .glow-blue:hover {
        border-color: #0070F3;
        box-shadow: 0 0 25px rgba(0, 112, 243, 0.25), 0 10px 25px rgba(0, 0, 0, 0.9);
        transform: translateY(-2px);
    }

    /* Glow Variant 2: Emerald / Accuracy */
    .glow-emerald .card-crosshair {
        color: #50E3C2;
    }

    .glow-emerald:hover {
        border-color: #50E3C2;
        box-shadow: 0 0 25px rgba(80, 227, 194, 0.25), 0 10px 25px rgba(0, 0, 0, 0.9);
        transform: translateY(-2px);
    }

    /* Glow Variant 3: Gold / Coins */
    .glow-gold .card-crosshair {
        color: #F5A623;
    }

    .glow-gold:hover {
        border-color: #F5A623;
        box-shadow: 0 0 25px rgba(245, 166, 35, 0.25), 0 10px 25px rgba(0, 0, 0, 0.9);
        transform: translateY(-2px);
    }

    /* Glow Variant 4: Purple / Badges */
    .glow-purple .card-crosshair {
        color: #7928CA;
    }

    .glow-purple:hover {
        border-color: #7928CA;
        box-shadow: 0 0 25px rgba(121, 40, 202, 0.25), 0 10px 25px rgba(0, 0, 0, 0.9);
        transform: translateY(-2px);
    }

    .metric-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 0.65rem;
    }

    .metric-label {
        font-family: var(--font-mono);
        font-size: 0.7rem;
        font-weight: 700;
        color: #A1A1AA;
        text-transform: uppercase;
        letter-spacing: 0.04em;
    }

    .metric-icon-badge {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        border: 1px solid;
    }

    .icon-badge-blue {
        background-color: rgba(0, 112, 243, 0.12);
        border-color: rgba(0, 112, 243, 0.3);
    }

    .icon-badge-emerald {
        background-color: rgba(80, 227, 194, 0.12);
        border-color: rgba(80, 227, 194, 0.3);
    }

    .icon-badge-gold {
        background-color: rgba(245, 166, 35, 0.12);
        border-color: rgba(245, 166, 35, 0.3);
    }

    .icon-badge-purple {
        background-color: rgba(121, 40, 202, 0.12);
        border-color: rgba(121, 40, 202, 0.3);
    }

    .metric-value-row {
        display: flex;
        align-items: baseline;
        gap: 0.45rem;
    }

    .metric-main-number {
        font-family: var(--font-mono);
        font-size: 1.85rem;
        font-weight: 800;
        color: #FFFFFF;
        line-height: 1.1;
        letter-spacing: -0.03em;
    }

    .metric-unit-text {
        font-family: var(--font-mono);
        font-size: 0.775rem;
        font-weight: 600;
        color: #71717A;
    }

    .metric-sub-note {
        margin-top: 0.45rem;
        font-family: var(--font-mono);
        font-size: 0.675rem;
        display: flex;
        align-items: center;
        gap: 0.3rem;
    }

    /* ==========================================================================
       MAIN 2-COLUMN DASHBOARD GRID (12 COLS: 7 LEFT, 5 RIGHT)
       ========================================================================== */
    .dashboard-main-columns {
        display: grid;
        grid-template-columns: 7fr 5fr;
        gap: 1.5rem;
        align-items: start;
    }

    .dashboard-panel-box {
        background-color: #0A0A0A;
        border: 1.5px solid #222222;
        border-radius: 10px;
        padding: 1.35rem 1.45rem;
        position: relative;
        box-shadow: 0 10px 30px rgba(0, 0, 0, 0.85);
    }

    .dashboard-panel-box .panel-crosshair {
        position: absolute;
        color: #50E3C2;
        font-family: var(--font-mono);
        font-size: 10px;
        font-weight: 800;
        line-height: 1;
        pointer-events: none;
        opacity: 0.85;
    }

    .dashboard-panel-box .corner-tl {
        top: -5px;
        left: -5px;
    }

    .dashboard-panel-box .corner-tr {
        top: -5px;
        right: -5px;
    }

    .dashboard-panel-box .corner-bl {
        bottom: -5px;
        left: -5px;
    }

    .dashboard-panel-box .corner-br {
        bottom: -5px;
        right: -5px;
    }

    .panel-header-row {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding-bottom: 0.85rem;
        margin-bottom: 1rem;
        border-bottom: 1px solid #222222;
    }

    .panel-title-wrap {
        display: flex;
        align-items: center;
        gap: 0.6rem;
    }

    .panel-title-text {
        font-family: var(--font-heading);
        font-size: 1.05rem;
        font-weight: 800;
        color: #FFFFFF;
        margin: 0;
        letter-spacing: -0.01em;
    }

    /* Live Indicator Dot */
    .vercel-live-indicator {
        position: relative;
        width: 8px;
        height: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
    }

    .vercel-live-indicator .pulse-ring {
        position: absolute;
        width: 100%;
        height: 100%;
        border-radius: 50%;
        background-color: #10B981;
        opacity: 0.75;
        animation: indicator-ping 2s cubic-bezier(0, 0, 0.2, 1) infinite;
    }

    .vercel-live-indicator .pulse-core {
        position: relative;
        width: 6px;
        height: 6px;
        border-radius: 50%;
        background-color: #10B981;
    }

    @keyframes indicator-ping {

        75%,
        100% {
            transform: scale(2.2);
            opacity: 0;
        }
    }

    /* ==========================================================================
       EXAM ACTIVITY ROWS (HARMONIOUS COLORFUL VERCEL SYSTEM ON DARK SURFACE)
       ========================================================================== */
    .activity-row-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.75rem 0.95rem;
        background-color: #111111;
        border: 1px solid #222222;
        border-left-width: 3.5px;
        border-left-color: var(--theme-accent, #10B981);
        border-radius: 8px;
        gap: 0.85rem;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1), border-color 0.2s ease, box-shadow 0.2s ease;
        position: relative;
    }

    .activity-row-item:hover {
        transform: translateX(3px);
        border-color: var(--theme-accent, #10B981);
        box-shadow: 0 0 20px var(--theme-glow, rgba(16, 185, 129, 0.25));
    }

    /* Theme Tokens */
    .theme-emerald {
        --theme-accent: #10B981;
        --theme-badge-bg: #064E3B;
        --theme-badge-color: #34D399;
        --theme-badge-border: #059669;
        --theme-pill-bg: rgba(16, 185, 129, 0.15);
        --theme-pill-color: #34D399;
        --theme-pts-color: #34D399;
        --theme-glow: rgba(16, 185, 129, 0.25);
    }

    .theme-cyan {
        --theme-accent: #0EA5E9;
        --theme-badge-bg: #082F49;
        --theme-badge-color: #38BDF8;
        --theme-badge-border: #0284C7;
        --theme-pill-bg: rgba(14, 165, 233, 0.15);
        --theme-pill-color: #38BDF8;
        --theme-pts-color: #38BDF8;
        --theme-glow: rgba(14, 165, 233, 0.25);
    }

    .theme-purple {
        --theme-accent: #8B5CF6;
        --theme-badge-bg: #2E1065;
        --theme-badge-color: #C084FC;
        --theme-badge-border: #7C3AED;
        --theme-pill-bg: rgba(139, 92, 246, 0.15);
        --theme-pill-color: #C084FC;
        --theme-pts-color: #C084FC;
        --theme-glow: rgba(139, 92, 246, 0.25);
    }

    .theme-amber {
        --theme-accent: #F59E0B;
        --theme-badge-bg: #451A03;
        --theme-badge-color: #FBBF24;
        --theme-badge-border: #D97706;
        --theme-pill-bg: rgba(245, 158, 11, 0.15);
        --theme-pill-color: #FBBF24;
        --theme-pts-color: #FBBF24;
        --theme-glow: rgba(245, 158, 11, 0.25);
    }

    .activity-score-badge {
        width: 42px;
        height: 42px;
        background-color: var(--theme-badge-bg, #064E3B);
        border: 1.5px solid var(--theme-badge-border, #059669);
        border-radius: 6px;
        display: flex;
        align-items: center;
        justify-content: center;
        flex-shrink: 0;
        box-shadow: 0 0 10px var(--theme-glow, rgba(16, 185, 129, 0.2));
    }

    .activity-score-number {
        font-family: var(--font-mono);
        font-size: 0.95rem;
        font-weight: 800;
        color: var(--theme-badge-color, #34D399);
        line-height: 1;
    }

    .activity-info-box {
        flex: 1;
        min-width: 0;
    }

    .activity-quiz-title {
        font-family: var(--font-body);
        font-size: 0.875rem;
        font-weight: 700;
        color: #FFFFFF;
        margin: 0 0 0.25rem 0;
        line-height: 1.35;
        white-space: nowrap;
        overflow: hidden;
        text-overflow: ellipsis;
    }

    .activity-meta-row {
        display: flex;
        align-items: center;
        gap: 0.4rem;
        font-size: 0.725rem;
        color: #A1A1AA;
        flex-wrap: wrap;
    }

    .vercel-topic-tag {
        font-family: var(--font-mono);
        font-size: 0.675rem;
        font-weight: 600;
        color: var(--theme-pill-color, #34D399);
        background-color: var(--theme-pill-bg, rgba(16, 185, 129, 0.15));
        border: 1px solid var(--theme-accent);
        padding: 1.5px 6px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 0.3rem;
    }

    .activity-right-side {
        display: flex;
        align-items: center;
        gap: 0.65rem;
        flex-shrink: 0;
    }

    .activity-pts-pill {
        font-size: 0.725rem;
        font-weight: 700;
        color: var(--theme-pts-color, #34D399);
    }

    .activity-action-pill {
        font-size: 0.7rem;
        font-weight: 700;
        background-color: #18181B;
        color: #E4E4E7;
        border: 1px solid #27272A;
        padding: 3px 8px;
        border-radius: 4px;
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
        transition: background-color 0.15s ease, color 0.15s ease, border-color 0.15s ease;
    }

    .activity-row-item:hover .activity-action-pill {
        background-color: var(--theme-accent, #10B981);
        color: #000000;
        border-color: var(--theme-accent, #10B981);
    }

    .activity-action-arrow {
        display: inline-block;
        transition: transform 0.2s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .activity-row-item:hover .activity-action-arrow {
        transform: translateX(2.5px);
    }

    .activity-panel-footer {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-top: 1rem;
        padding-top: 0.75rem;
        border-top: 1px solid #222222;
        font-size: 0.75rem;
    }

    /* Learning Material Cards */
    .material-item-card {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 0.85rem 1rem;
        background-color: #111111;
        border: 1px solid #222222;
        border-radius: 8px;
        text-decoration: none;
        gap: 0.85rem;
        transition: transform 0.18s ease, border-color 0.18s ease, background-color 0.18s ease;
    }

    .material-item-card:hover {
        background-color: #18181B;
        border-color: #50E3C2;
        transform: translateX(2px);
    }

    .material-title {
        font-family: var(--font-body);
        font-size: 0.85rem;
        font-weight: 700;
        color: #FFFFFF;
        margin: 0;
        line-height: 1.35;
        overflow: hidden;
        text-overflow: ellipsis;
        display: -webkit-box;
        -webkit-line-clamp: 1;
        -webkit-box-orient: vertical;
    }

    /* Topic Mastery Progress Bars */
    .topic-progress-item {
        display: flex;
        flex-direction: column;
        gap: 0.4rem;
    }

    .topic-progress-header {
        display: flex;
        align-items: center;
        justify-content: space-between;
        font-size: 0.775rem;
        font-weight: 600;
        color: #E4E4E7;
    }

    .topic-progress-track {
        width: 100%;
        height: 6px;
        background-color: #18181B;
        border-radius: 9999px;
        overflow: hidden;
        border: 1px solid #27272A;
    }

    .topic-progress-fill {
        height: 100%;
        border-radius: 9999px;
        transition: width 0.8s cubic-bezier(0.16, 1, 0.3, 1);
    }

    .fill-routing {
        background-color: #0EA5E9;
        box-shadow: 0 0 8px rgba(14, 165, 233, 0.6);
    }

    .fill-firewall {
        background-color: #F59E0B;
        box-shadow: 0 0 8px rgba(245, 158, 11, 0.6);
    }

    .fill-wireless {
        background-color: #EC4899;
        box-shadow: 0 0 8px rgba(236, 72, 153, 0.6);
    }

    .fill-mgmt {
        background-color: #10B981;
        box-shadow: 0 0 8px rgba(16, 185, 129, 0.6);
    }

    /* 4-Column Badges Grid */
    .pixel-badges-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 0.65rem;
    }

    .pixel-badge-slot {
        background-color: #0A0A0A;
        border: 1.5px solid #222222;
        border-radius: 8px;
        padding: 0.75rem 0.5rem;
        display: flex;
        flex-direction: column;
        align-items: center;
        text-align: center;
        gap: 0.35rem;
        transition: transform 0.15s ease, border-color 0.15s ease, background-color 0.15s ease;
        position: relative;
    }

    .pixel-badge-slot.unlocked {
        background-color: #111111;
        border-color: #F59E0B;
        box-shadow: 0 0 12px rgba(245, 158, 11, 0.2);
    }

    .pixel-badge-slot.unlocked:hover {
        transform: translateY(-2px) scale(1.03);
        border-color: #FBBF24;
    }

    .pixel-badge-slot.locked {
        opacity: 0.35;
        filter: grayscale(100%);
    }

    .badge-icon-box {
        width: 36px;
        height: 36px;
        display: flex;
        align-items: center;
        justify-content: center;
    }

    .badge-slot-title {
        font-family: var(--font-mono);
        font-size: 0.65rem;
        font-weight: 700;
        color: #E4E4E7;
        width: 100%;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }

    /* Responsive Queries */
    @media (max-width: 1024px) {
        .dashboard-main-columns {
            grid-template-columns: 1fr;
        }

        .pixel-metrics-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 640px) {
        .pixel-metrics-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.65rem;
        }

        .pixel-metric-card {
            padding: 0.85rem 0.95rem;
        }

        .metric-main-number {
            font-size: 1.45rem;
        }

        .hero-main-title {
            font-size: 1.25rem;
        }

        .pixel-badges-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>

<!-- DASHBOARD HERO HEADER -->

<div class="dashboard-hero-header">
    <div class="hero-breadcrumb">
        <span>Siswa /</span>
        <span class="active-tag">Dashboard</span>
    </div>
</div>

<!-- 4 QUICK METRICS GRID (VERCEL PIXEL DARK GLOW CARDS) -->
<div class="pixel-metrics-grid">
    <!-- Metric 1: Total Ujian Selesai -->
    <div class="pixel-metric-card glow-blue">
        <span class="card-crosshair corner-tl">+</span>
        <span class="card-crosshair corner-tr">+</span>
        <span class="card-crosshair corner-bl">+</span>
        <span class="card-crosshair corner-br">+</span>

        <svg class="metric-watermark pixelated" viewBox="0 0 16 16" aria-hidden="true">
            <use href="#pixel-router"></use>
        </svg>

        <div class="metric-header-row">
            <span class="metric-label">Total Ujian</span>
            <div class="metric-icon-badge icon-badge-blue">
                <svg class="pixelated" width="18" height="18" viewBox="0 0 16 16" aria-hidden="true">
                    <use href="#pixel-router"></use>
                </svg>
            </div>
        </div>

        <div class="metric-value-row">
            <span class="metric-main-number"><?= (int)($stats["completed_quizzes"] ?? 0) ?></span>
            <span class="metric-unit-text">Selesai</span>
        </div>

        <div class="metric-sub-note text-zinc-400">
            <span style="color: #0EA5E9; font-weight: 700;">↑ Active</span>
            <span>learning track</span>
        </div>
    </div>

    <!-- Metric 2: Rata-Rata Nilai -->
    <div class="pixel-metric-card glow-emerald">
        <span class="card-crosshair corner-tl">+</span>
        <span class="card-crosshair corner-tr">+</span>
        <span class="card-crosshair corner-bl">+</span>
        <span class="card-crosshair corner-br">+</span>

        <svg class="metric-watermark pixelated" viewBox="0 0 16 16" aria-hidden="true">
            <use href="#pixel-computer"></use>
        </svg>

        <div class="metric-header-row">
            <span class="metric-label">Rata-Rata Nilai</span>
            <div class="metric-icon-badge icon-badge-emerald">
                <svg class="pixelated" width="18" height="18" viewBox="0 0 16 16" aria-hidden="true">
                    <use href="#pixel-computer"></use>
                </svg>
            </div>
        </div>

        <div class="metric-value-row">
            <span class="metric-main-number" style="color: #34D399;"><?= (int)($stats["average_score"] ?? 0) ?>%</span>
            <span class="metric-unit-text">Akurasi</span>
        </div>

        <div class="metric-sub-note text-zinc-400">
            <span style="color: #10B981; font-weight: 700;">↑ Skor</span>
            <span>evaluasi kumulatif</span>
        </div>
    </div>

    <!-- Metric 3: Total Poin -->
    <div class="pixel-metric-card glow-gold">
        <span class="card-crosshair corner-tl">+</span>
        <span class="card-crosshair corner-tr">+</span>
        <span class="card-crosshair corner-bl">+</span>
        <span class="card-crosshair corner-br">+</span>

        <svg class="metric-watermark pixelated" viewBox="0 0 16 16" aria-hidden="true">
            <use href="#pixel-coin"></use>
        </svg>

        <div class="metric-header-row">
            <span class="metric-label">Total Poin</span>
            <div class="metric-icon-badge icon-badge-gold">
                <svg class="pixelated" width="18" height="18" viewBox="0 0 16 16" aria-hidden="true">
                    <use href="#pixel-coin"></use>
                </svg>
            </div>
        </div>

        <div class="metric-value-row">
            <span class="metric-main-number" style="color: #FBBF24;"><?= number_format((int)($stats["total_score"] ?? 0)) ?></span>
            <span class="metric-unit-text">Pts</span>
        </div>

        <div class="metric-sub-note text-zinc-400">
            <span style="color: #F59E0B; font-weight: 700;">★ Tier 2</span>
            <span>Network Tech</span>
        </div>
    </div>

    <!-- Metric 4: Lencana Prestasi -->
    <div class="pixel-metric-card glow-purple">
        <span class="card-crosshair corner-tl">+</span>
        <span class="card-crosshair corner-tr">+</span>
        <span class="card-crosshair corner-bl">+</span>
        <span class="card-crosshair corner-br">+</span>

        <svg class="metric-watermark pixelated" viewBox="0 0 16 16" aria-hidden="true">
            <use href="#pixel-robot"></use>
        </svg>

        <div class="metric-header-row">
            <span class="metric-label">Lencana Prestasi</span>
            <div class="metric-icon-badge icon-badge-purple">
                <svg class="pixelated" width="18" height="18" viewBox="0 0 16 16" aria-hidden="true">
                    <use href="#pixel-robot"></use>
                </svg>
            </div>
        </div>

        <div class="metric-value-row">
            <span class="metric-main-number" style="color: #C084FC;"><?= count($unlockedBadges) ?></span>
            <span class="metric-unit-text">Terbuka</span>
        </div>

        <div class="metric-sub-note text-zinc-400">
            <span style="color: #8B5CF6; font-weight: 700;">Koleksi</span>
            <span>prestasi akademi</span>
        </div>
    </div>
</div>

<!-- MAIN 2-COLUMN DASHBOARD GRID (12 COLS: 7 LEFT, 5 RIGHT) -->
<div class="dashboard-main-columns">
    <!-- LEFT COLUMN (7 COLS): Riwayat Aktivitas & Materi Belajar -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Panel 1: Riwayat Aktivitas Ujian -->
        <div class="dashboard-panel-box">
            <span class="panel-crosshair corner-tl">+</span>
            <span class="panel-crosshair corner-tr">+</span>
            <span class="panel-crosshair corner-bl">+</span>
            <span class="panel-crosshair corner-br">+</span>

            <div class="panel-header-row">
                <div class="panel-title-wrap">
                    <div class="vercel-live-indicator" aria-hidden="true">
                        <span class="pulse-ring"></span>
                        <span class="pulse-core"></span>
                    </div>
                    <h3 class="panel-title-text">Riwayat Aktivitas Ujian</h3>
                </div>
                <?php if (!empty($stats["recent_activities"])): ?>
                    <span class="font-mono text-xs font-semibold px-2 py-0.5 rounded bg-zinc-900 border border-zinc-800 text-zinc-300">
                        <?= count($stats["recent_activities"]) ?> Sesi
                    </span>
                <?php endif; ?>
            </div>

            <?php if (empty($stats["recent_activities"])): ?>
                <div style="padding: 2.25rem 1rem; text-align: center; display: flex; flex-direction: column; align-items: center; justify-content: center; gap: 0.5rem;">
                    <div style="width: 44px; height: 44px; background: #18181B; border: 1.5px solid #27272A; border-radius: 8px; display: flex; align-items: center; justify-content: center;">
                        <svg class="pixelated" width="24" height="24" viewBox="0 0 16 16">
                            <use href="#pixel-router"></use>
                        </svg>
                    </div>
                    <h4 style="font-family: var(--font-heading); font-size: 0.95rem; font-weight: 700; color: #FFFFFF; margin: 0;">Belum Ada Riwayat Ujian</h4>
                    <p style="font-size: 0.8rem; color: #A1A1AA; margin: 0; max-width: 280px;">Selesaikan kuis untuk mulai mengumpulkan skor dan membuka lencana prestasimu!</p>
                    <a href="<?= BASE_URL ?>/quiz" class="btn-hero-primary" style="margin-top: 0.5rem; font-size: 0.725rem; padding: 0.4rem 0.85rem;">
                        <span>Mulai Kuis &rarr;</span>
                    </a>
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 0.65rem;">
                    <?php foreach (array_slice($stats["recent_activities"], 0, 5) as $act): ?>
                        <?php
                        $score = (int)($act["score"] ?? 0);
                        $categoryName = $act["category"] ?? "MikroTik";
                        $catLower = strtolower($categoryName);

                        // Harmonious Category Color Families
                        if (strpos($catLower, "mikrotik") !== false || strpos($catLower, "routing") !== false) {
                            $themeClass = "theme-emerald";
                            $catDotColor = "#10B981";
                        } elseif (strpos($catLower, "cisco") !== false || strpos($catLower, "switch") !== false || strpos($catLower, "lan") !== false) {
                            $themeClass = "theme-cyan";
                            $catDotColor = "#0EA5E9";
                        } elseif (strpos($catLower, "keamanan") !== false || strpos($catLower, "security") !== false || strpos($catLower, "linux") !== false) {
                            $themeClass = "theme-purple";
                            $catDotColor = "#8B5CF6";
                        } else {
                            $themeClass = "theme-amber";
                            $catDotColor = "#F59E0B";
                        }

                        // Score action label
                        if ($score >= 75) {
                            $earnedPts = max(10, $score * 2);
                            $actionLabel = "Passed";
                        } elseif ($score >= 50) {
                            $earnedPts = max(5, $score);
                            $actionLabel = "Review";
                        } else {
                            $earnedPts = 0;
                            $actionLabel = "Retake";
                        }
                        ?>
                        <div class="activity-row-item <?= $themeClass ?>">
                            <div class="activity-score-badge">
                                <span class="activity-score-number"><?= $score ?></span>
                            </div>
                            <div class="activity-info-box">
                                <h4 class="activity-quiz-title">
                                    <?= htmlspecialchars($act["quiz_title"] ?? $act["title"] ?? "Ujian MikroTik") ?>
                                </h4>
                                <div class="activity-meta-row">
                                    <span class="vercel-topic-tag">
                                        <span style="width: 5px; height: 5px; border-radius: 50%; background-color: <?= $catDotColor ?>; display: inline-block;"></span>
                                        <?= htmlspecialchars($categoryName) ?>
                                    </span>
                                    <span>•</span>
                                    <span class="font-mono text-zinc-400"><?= date("d M Y, H:i", strtotime($act["created_at"] ?? "now")) ?></span>
                                </div>
                            </div>
                            <div class="activity-right-side">
                                <span class="activity-pts-pill font-mono">+<?= $earnedPts ?> pts</span>
                                <span class="activity-action-pill font-mono">
                                    <?= $actionLabel ?> <span class="activity-action-arrow">&rarr;</span>
                                </span>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>

        <!-- Panel 2: Materi Belajar Terkini -->
        <div class="dashboard-panel-box">
            <span class="panel-crosshair corner-tl">+</span>
            <span class="panel-crosshair corner-tr">+</span>
            <span class="panel-crosshair corner-bl">+</span>
            <span class="panel-crosshair corner-br">+</span>

            <div class="panel-header-row">
                <div class="panel-title-wrap">
                    <svg class="pixelated" width="18" height="18" viewBox="0 0 16 16">
                        <use href="#pixel-book"></use>
                    </svg>
                    <h3 class="panel-title-text">Materi Belajar Terkini</h3>
                </div>
            </div>

            <?php if (empty($materials)): ?>
                <div style="padding: 1.75rem 1rem; text-align: center; color: #71717A; font-size: 0.85rem;">
                    Belum ada materi pembelajaran yang diterbitkan.
                </div>
            <?php else: ?>
                <div style="display: flex; flex-direction: column; gap: 0.65rem;">
                    <?php foreach (array_slice($materials, 0, 4) as $mat): ?>
                        <?php
                        $cleanText = strip_tags($mat["content"] ?? "");
                        $readTime = max(1, (int)ceil(str_word_count($cleanText) / 180));
                        ?>
                        <a href="<?= BASE_URL ?>/learn/<?= (int)$mat["id"] ?>" class="material-item-card">
                            <div style="min-width: 0; flex: 1;">
                                <div style="display: flex; align-items: center; gap: 0.4rem; margin-bottom: 0.2rem;">
                                    <span style="font-size: 0.675rem; color: #A1A1AA;" class="font-mono">~<?= $readTime ?> menit baca</span>
                                </div>
                                <h4 class="material-title">
                                    <?= htmlspecialchars($mat["title"]) ?>
                                </h4>
                            </div>
                            <div style="flex-shrink: 0; font-size: 0.775rem; font-weight: 700; color: #50E3C2; font-family: var(--font-mono); display: inline-flex; align-items: center; gap: 0.25rem;">
                                <span>Baca</span>
                                <span>&rarr;</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- RIGHT COLUMN (5 COLS): Distribusi Topik & Grid Lencana -->
    <div style="display: flex; flex-direction: column; gap: 1.5rem;">
        <!-- Panel 3: Distribusi Topik Ujian -->
        <div class="dashboard-panel-box">
            <span class="panel-crosshair corner-tl">+</span>
            <span class="panel-crosshair corner-tr">+</span>
            <span class="panel-crosshair corner-bl">+</span>
            <span class="panel-crosshair corner-br">+</span>

            <div class="panel-header-row">
                <div class="panel-title-wrap">
                    <div style="width: 8px; height: 8px; background-color: #0EA5E9; border-radius: 2px;"></div>
                    <h3 class="panel-title-text">Distribusi Topik Ujian</h3>
                </div>
            </div>

            <div style="display: flex; flex-direction: column; gap: 0.95rem;">
                <?php
                $cats = $stats["categories"] ?? ["Routing" => 0, "Firewall & NAT" => 0, "Wireless" => 0, "Network Management" => 0];
                foreach ($cats as $catName => $count):
                    $cLower = strtolower($catName);
                    if (strpos($cLower, "routing") !== false) {
                        $fillClass = "fill-routing";
                        $dotColor = "#0EA5E9";
                    } elseif (strpos($cLower, "firewall") !== false) {
                        $fillClass = "fill-firewall";
                        $dotColor = "#F59E0B";
                    } elseif (strpos($cLower, "wireless") !== false) {
                        $fillClass = "fill-wireless";
                        $dotColor = "#EC4899";
                    } else {
                        $fillClass = "fill-mgmt";
                        $dotColor = "#10B981";
                    }
                ?>
                    <div class="topic-progress-item">
                        <div class="topic-progress-header">
                            <span style="display: flex; align-items: center; gap: 0.35rem;">
                                <span style="width: 6px; height: 6px; border-radius: 50%; background-color: <?= $dotColor ?>; display: inline-block;"></span>
                                <span><?= htmlspecialchars($catName) ?></span>
                            </span>
                            <span class="font-mono text-xs text-zinc-400"><?= (int)$count ?> Kuis</span>
                        </div>
                        <div class="topic-progress-track">
                            <div class="topic-progress-fill <?= $fillClass ?>" style="width: <?= min(100, max(5, $count * 25)) ?>%;"></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

        <!-- Panel 4: Lencana Prestasi (4-Column Pixel Badge Grid) -->
        <div class="dashboard-panel-box">
            <span class="panel-crosshair corner-tl">+</span>
            <span class="panel-crosshair corner-tr">+</span>
            <span class="panel-crosshair corner-bl">+</span>
            <span class="panel-crosshair corner-br">+</span>

            <div class="panel-header-row">
                <div class="panel-title-wrap">
                    <svg class="pixelated" width="18" height="18" viewBox="0 0 16 16">
                        <use href="#pixel-coin"></use>
                    </svg>
                    <h3 class="panel-title-text">Lencana Prestasi</h3>
                </div>
                <span class="font-mono text-xs font-semibold text-amber-400">
                    <?= count($unlockedBadges) ?> Terbuka
                </span>
            </div>

            <div class="pixel-badges-grid">
                <?php
                $displayBadges = !empty($badges) ? array_slice($badges, 0, 8) : [
                    ["id" => 1, "title" => "RouterOS Master", "unlocked" => true, "sprite" => "pixel-router"],
                    ["id" => 2, "title" => "Ping Explorer", "unlocked" => true, "sprite" => "pixel-computer"],
                    ["id" => 3, "title" => "Firewall Hero", "unlocked" => false, "sprite" => "pixel-sparkle"],
                    ["id" => 4, "title" => "NetBot Scholar", "unlocked" => false, "sprite" => "pixel-robot"],
                ];

                $spriteMap = [
                    0 => "pixel-router",
                    1 => "pixel-computer",
                    2 => "pixel-coin",
                    3 => "pixel-robot",
                    4 => "pixel-sparkle",
                    5 => "pixel-book",
                    6 => "pixel-router",
                    7 => "pixel-coin"
                ];

                foreach ($displayBadges as $idx => $bdg):
                    $isUnlocked = !empty($bdg["unlocked"]) || in_array($bdg["id"] ?? 0, array_column($unlockedBadges, "id"));
                    $spriteId = $bdg["sprite"] ?? ($spriteMap[$idx % count($spriteMap)] ?? "pixel-router");
                ?>
                    <div class="pixel-badge-slot <?= $isUnlocked ? "unlocked" : "locked" ?>" title="<?= htmlspecialchars($bdg["title"] ?? "Lencana") ?>">
                        <div class="badge-icon-box">
                            <svg class="pixelated" width="24" height="24" viewBox="0 0 16 16">
                                <use href="#<?= $spriteId ?>"></use>
                            </svg>
                        </div>
                        <span class="badge-slot-title">
                            <?= htmlspecialchars($bdg["title"] ?? "Badge") ?>
                        </span>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>

    </div>
</div>

<?php require_once dirname(__DIR__) . "/templates/footer.php"; ?>