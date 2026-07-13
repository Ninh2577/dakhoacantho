<style>
/* ================================================
   PREMIUM SIDEBAR — Deep Indigo Gradient Design
   ================================================ */

/* === Root: Override Filament color tokens for sidebar === */
:root {
    --fi-sidebar-bg-color: transparent;
}

/* === Main Sidebar Shell === */
.fi-sidebar {
    background: linear-gradient(160deg, #1e1b4b 0%, #312e81 35%, #1e3a5f 70%, #0f172a 100%) !important;
    border-right: 1px solid rgba(255,255,255,0.07) !important;
    box-shadow: 4px 0 24px rgba(0, 0, 0, 0.25) !important;
}

/* === Header Area === */
.fi-sidebar-header {
    background: transparent !important;
    border-bottom: 1px solid rgba(255,255,255,0.1) !important;
    padding: 0.75rem 1rem !important;
    display: flex !important;
    align-items: center !important;
    justify-content: space-between !important;
    gap: 0.5rem !important;
    min-height: 56px !important;
}

/* === Brand name — hiển thị đẹp === */
.fi-sidebar-header .fi-brand-name,
.fi-sidebar-header .fi-logo,
.fi-sidebar-header a .fi-brand-name {
    display: block !important;
    color: #f1f5f9 !important;
    font-size: 0.875rem !important;
    font-weight: 800 !important;
    letter-spacing: -0.01em !important;
    line-height: 1.3 !important;
    white-space: normal !important;
}

/* === Brand link wrapper === */
.fi-sidebar-header > a,
.fi-sidebar-header .fi-brand {
    display: flex !important;
    align-items: center !important;
    flex: 1 !important;
    min-width: 0 !important;
    text-decoration: none !important;
}

/* === Toggle collapse/expand buttons — appearance only, NO display override === */
/* x-cloak and x-show are handled by Alpine.js — do NOT use display !important here */
.fi-sidebar-header button.fi-icon-btn {
    width: 32px !important;
    height: 32px !important;
    border-radius: 8px !important;
    background: rgba(255,255,255,0.08) !important;
    border: 1px solid rgba(255,255,255,0.12) !important;
    color: #c4b5fd !important;
    cursor: pointer !important;
    transition: background 0.2s, color 0.2s, transform 0.2s !important;
    /* DO NOT set display here — let Alpine x-show control it */
}

.fi-sidebar-header button.fi-icon-btn:hover {
    background: rgba(255,255,255,0.18) !important;
    border-color: rgba(255,255,255,0.3) !important;
    color: #fff !important;
    transform: scale(1.08) !important;
}

.fi-sidebar-header button.fi-icon-btn svg {
    width: 1rem !important;
    height: 1rem !important;
    color: inherit !important;
}

/* Ensure x-cloak still hides elements before Alpine init */
[x-cloak] { display: none !important; }

/* === Topbar open-sidebar button (when sidebar collapsed) === */
.fi-topbar-sidebar-open-action {
    color: #475569 !important;
}


/* === Sidebar Nav Background === */
.fi-sidebar-nav {
    background: transparent !important;
}

/* === Group Labels === */
.fi-sidebar .fi-sidebar-group-label,
.fi-sidebar-group-label span,
.fi-sidebar-group-label {
    color: rgba(196, 181, 253, 0.7) !important;
    font-size: 0.625rem !important;
    font-weight: 700 !important;
    letter-spacing: 0.12em !important;
    text-transform: uppercase !important;
    padding: 1rem 1.125rem 0.375rem !important;
}

/* === Group Divider === */
.fi-sidebar-group + .fi-sidebar-group {
    border-top: 1px solid rgba(255,255,255,0.05) !important;
    margin-top: 0.125rem !important;
    padding-top: 0.125rem !important;
}

/* === All navigation item button states === */
.fi-sidebar a:not(.fi-dropdown-list-item),
.fi-sidebar button:not(.fi-icon-btn):not(.fi-dropdown-list-item),
.fi-sidebar .fi-sidebar-item-button,
.fi-sidebar .fi-sidebar-item-button:link,
.fi-sidebar .fi-sidebar-item-button:visited,
.fi-sidebar .fi-sidebar-item-button:any-link {
    color: rgba(226, 232, 240, 0.9) !important;
    border-radius: 9px !important;
    padding: 0.5625rem 0.875rem !important;
    transition: background 0.15s, color 0.15s !important;
    text-decoration: none !important;
    font-weight: 500 !important;
    font-size: 0.8125rem !important;
    background: transparent !important;
    display: flex !important;
    align-items: center !important;
    gap: 0.625rem !important;
    margin: 1px 0.375rem !important;
}

/* === Hover State === */
.fi-sidebar a:not(.fi-dropdown-list-item):hover,
.fi-sidebar .fi-sidebar-item-button:hover,
.fi-sidebar .fi-sidebar-item-button:focus {
    background: rgba(255,255,255,0.08) !important;
    color: #f8fafc !important;
}

/* === Active/Current Item === */
.fi-sidebar a:not(.fi-dropdown-list-item)[aria-current="page"],
.fi-sidebar .fi-sidebar-item-button[aria-current="page"],
.fi-sidebar .fi-sidebar-item-button.fi-active {
    background: rgba(255, 255, 255, 0.15) !important;
    color: #ffffff !important;
    font-weight: 700 !important;
    box-shadow: inset 0 0 0 1px rgba(255,255,255,0.12), 0 2px 8px rgba(0,0,0,0.15) !important;
    backdrop-filter: blur(4px) !important;
}

/* === All Text Spans Inside Nav Items === */
.fi-sidebar span:not(.fi-dropdown-list-item-label):not(.fi-dropdown-header-label),
.fi-sidebar .fi-sidebar-item-label,
.fi-sidebar li span:not(.fi-dropdown-list-item-label):not(.fi-dropdown-header-label),
.fi-sidebar nav span:not(.fi-dropdown-list-item-label):not(.fi-dropdown-header-label) {
    color: inherit !important;
}

/* === All Icons Inside Sidebar === */
.fi-sidebar svg:not(.fi-dropdown-list-item-icon):not(.fi-dropdown-header-icon),
.fi-sidebar .fi-sidebar-item-icon,
.fi-sidebar .fi-sidebar-item-icon svg:not(.fi-dropdown-list-item-icon):not(.fi-dropdown-header-icon),
.fi-sidebar .fi-icon:not(.fi-dropdown-list-item-icon):not(.fi-dropdown-header-icon),
.fi-sidebar-group-label svg {
    color: rgba(196, 181, 253, 0.75) !important;
    width: 1.0625rem !important;
    height: 1.0625rem !important;
    flex-shrink: 0 !important;
    transition: color 0.15s, transform 0.15s !important;
}

.fi-sidebar a:not(.fi-dropdown-list-item):hover svg:not(.fi-dropdown-list-item-icon),
.fi-sidebar .fi-sidebar-item-button:hover svg:not(.fi-dropdown-list-item-icon),
.fi-sidebar .fi-sidebar-item-button:hover .fi-sidebar-item-icon {
    color: #c4b5fd !important;
    transform: scale(1.08) !important;
}

.fi-sidebar a:not(.fi-dropdown-list-item)[aria-current="page"] svg:not(.fi-dropdown-list-item-icon),
.fi-sidebar .fi-sidebar-item-button[aria-current="page"] svg:not(.fi-dropdown-list-item-icon),
.fi-sidebar .fi-sidebar-item-button[aria-current="page"] .fi-sidebar-item-icon {
    color: #ffffff !important;
}

/* === Group header large icons === */
.fi-sidebar-group-label > * > svg,
.fi-sidebar-group > button > svg {
    color: rgba(196, 181, 253, 0.6) !important;
    width: 1rem !important;
    height: 1rem !important;
}

/* === Badge count === */
.fi-sidebar .fi-sidebar-item-badge {
    margin-left: auto !important;
    background: rgba(139, 92, 246, 0.8) !important;
    color: #fff !important;
    font-size: 0.625rem !important;
    font-weight: 700 !important;
    padding: 1px 6px !important;
    border-radius: 999px !important;
    min-width: 18px !important;
    text-align: center !important;
    line-height: 16px !important;
    box-shadow: 0 0 8px rgba(139, 92, 246, 0.5) !important;
}

/* === Scrollbar === */
.fi-sidebar-nav::-webkit-scrollbar {
    width: 2px !important;
}
.fi-sidebar-nav::-webkit-scrollbar-track {
    background: transparent !important;
}
.fi-sidebar-nav::-webkit-scrollbar-thumb {
    background: rgba(139, 92, 246, 0.4) !important;
    border-radius: 10px !important;
}

/* === TopBar === */
.fi-topbar {
    background: #ffffff !important;
    border-bottom: 1px solid #e2e8f0 !important;
    box-shadow: 0 1px 4px rgba(0,0,0,0.04) !important;
}

/* === Collapse/chevron icons in group headers === */
[data-accordion-target] svg,
button[aria-expanded] svg {
    color: rgba(196, 181, 253, 0.5) !important;
}

/* === Active indicator dot - hide ugly dot === */
.fi-sidebar .fi-sidebar-item-active-indicator {
    display: none !important;
}

/* === Glassmorphism subtle top-right accent === */
.fi-sidebar::before {
    content: '';
    position: absolute;
    top: -60px;
    right: -60px;
    width: 200px;
    height: 200px;
    background: radial-gradient(circle, rgba(139, 92, 246, 0.15) 0%, transparent 70%);
    pointer-events: none;
    z-index: 0;
}
.fi-sidebar::after {
    content: '';
    position: absolute;
    bottom: 60px;
    left: -30px;
    width: 160px;
    height: 160px;
    background: radial-gradient(circle, rgba(59, 130, 246, 0.1) 0%, transparent 70%);
    pointer-events: none;
    z-index: 0;
}

/* === Premium Collapsed Sidebar Flyout Dropdown === */
.fi-sidebar .fi-dropdown-panel,
.fi-dropdown-panel:has(.fi-dropdown-header) {
    background: linear-gradient(145deg, #1e1b4b 0%, #121034 100%) !important;
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.45) !important;
    backdrop-filter: blur(12px) !important;
}

.fi-sidebar .fi-dropdown-panel .fi-dropdown-header,
.fi-dropdown-panel:has(.fi-dropdown-header) .fi-dropdown-header {
    background: rgba(255, 255, 255, 0.03) !important;
    border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
    padding: 0.75rem 1rem !important;
}

.fi-sidebar .fi-dropdown-panel .fi-dropdown-header-label,
.fi-dropdown-panel:has(.fi-dropdown-header) .fi-dropdown-header-label {
    color: #c4b5fd !important;
    font-weight: 700 !important;
    font-size: 0.75rem !important;
    text-transform: uppercase !important;
    letter-spacing: 0.08em !important;
}

.fi-sidebar .fi-dropdown-panel .fi-dropdown-list-item,
.fi-dropdown-panel:has(.fi-dropdown-header) .fi-dropdown-list-item {
    color: rgba(226, 232, 240, 0.85) !important;
    margin: 3px 6px !important;
    padding: 0.5rem 0.75rem !important;
    border-radius: 8px !important;
    transition: all 0.2s ease !important;
    background: transparent !important;
}

.fi-sidebar .fi-dropdown-panel .fi-dropdown-list-item-label,
.fi-dropdown-panel:has(.fi-dropdown-header) .fi-dropdown-list-item-label {
    color: inherit !important;
    font-weight: 500 !important;
    font-size: 0.8125rem !important;
}

.fi-sidebar .fi-dropdown-panel .fi-dropdown-list-item-icon,
.fi-dropdown-panel:has(.fi-dropdown-header) .fi-dropdown-list-item-icon {
    color: rgba(196, 181, 253, 0.75) !important;
    width: 1.0625rem !important;
    height: 1.0625rem !important;
    transition: all 0.2s ease !important;
}

/* Hover State */
.fi-sidebar .fi-dropdown-panel .fi-dropdown-list-item:hover,
.fi-sidebar .fi-dropdown-panel .fi-dropdown-list-item:focus,
.fi-dropdown-panel:has(.fi-dropdown-header) .fi-dropdown-list-item:hover,
.fi-dropdown-panel:has(.fi-dropdown-header) .fi-dropdown-list-item:focus {
    background: rgba(255, 255, 255, 0.08) !important;
    color: #ffffff !important;
}

.fi-sidebar .fi-dropdown-panel .fi-dropdown-list-item:hover .fi-dropdown-list-item-icon,
.fi-sidebar .fi-dropdown-panel .fi-dropdown-list-item:focus .fi-dropdown-list-item-icon,
.fi-dropdown-panel:has(.fi-dropdown-header) .fi-dropdown-list-item:hover .fi-dropdown-list-item-icon,
.fi-dropdown-panel:has(.fi-dropdown-header) .fi-dropdown-list-item:focus .fi-dropdown-list-item-icon {
    color: #ffffff !important;
    transform: scale(1.08) !important;
}

/* Active State */
.fi-sidebar .fi-dropdown-panel .fi-dropdown-list-item:not(.fi-color-gray),
.fi-dropdown-panel:has(.fi-dropdown-header) .fi-dropdown-list-item:not(.fi-color-gray) {
    background: rgba(255, 255, 255, 0.15) !important;
    color: #ffffff !important;
    font-weight: 700 !important;
}

.fi-sidebar .fi-dropdown-panel .fi-dropdown-list-item:not(.fi-color-gray) .fi-dropdown-list-item-icon,
.fi-dropdown-panel:has(.fi-dropdown-header) .fi-dropdown-list-item:not(.fi-color-gray) .fi-dropdown-list-item-icon {
    color: #ffffff !important;
}

/* === Premium Admin Dashboard Overrides === */

/* --- Main Layout Adjustments --- */
.fi-main {
    background-color: #f8fafc !important; /* Soft premium slate background for light mode */
}

.dark .fi-main {
    background-color: #030712 !important; /* Deep dark slate for dark mode */
}

.fi-header {
    margin-bottom: 2.25rem !important;
}

.fi-header-heading {
    font-size: 1.875rem !important;
    font-weight: 800 !important;
    letter-spacing: -0.03em !important;
    color: #0f172a !important;
}

.dark .fi-header-heading {
    color: #ffffff !important;
}

/* --- Stats Cards Overview --- */
.fi-wi-stats-overview {
    gap: 1.25rem !important;
}

.fi-wi-stats-overview-stat {
    position: relative !important;
    overflow: hidden !important;
    background: #ffffff !important;
    border: 1px solid rgba(226, 232, 240, 0.8) !important;
    border-radius: 14px !important;
    padding: 1.5rem !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01) !important;
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.25s !important;
}

.fi-wi-stats-overview-stat:hover {
    transform: translateY(-4px) !important;
    box-shadow: 0 12px 20px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02) !important;
    border-color: rgba(99, 102, 241, 0.25) !important;
}

.dark .fi-wi-stats-overview-stat {
    background: #1e1b4b !important;
    border: 1px solid rgba(255, 255, 255, 0.05) !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2), 0 2px 4px -1px rgba(0, 0, 0, 0.1) !important;
}

.dark .fi-wi-stats-overview-stat:hover {
    border-color: rgba(165, 180, 252, 0.2) !important;
    box-shadow: 0 12px 25px -3px rgba(0, 0, 0, 0.4), 0 4px 10px -2px rgba(0, 0, 0, 0.2) !important;
}

/* Stat Card Top Accent Line */
.fi-wi-stats-overview-stat::before {
    content: '' !important;
    position: absolute !important;
    top: 0 !important;
    left: 0 !important;
    right: 0 !important;
    height: 4px !important;
    border-radius: 9999px 9999px 0 0 !important;
    background: transparent;
    transition: background 0.3s !important;
    z-index: 10 !important;
}

.fi-wi-stats-overview-stat:has(.fi-color-info)::before {
    background: #0284c7 !important;
}

.fi-wi-stats-overview-stat:has(.fi-color-success)::before {
    background: #10b981 !important;
}

.fi-wi-stats-overview-stat:has(.fi-color-warning)::before {
    background: #f59e0b !important;
}

.fi-wi-stats-overview-stat:has(.fi-color-danger)::before {
    background: #ef4444 !important;
}

.fi-wi-stats-overview-stat:has(.fi-color-primary)::before {
    background: #6366f1 !important;
}

/* Stat Card Labels & Elements */
.fi-wi-stats-overview-stat-label {
    font-size: 0.8125rem !important;
    text-transform: uppercase !important;
    letter-spacing: 0.06em !important;
    font-weight: 700 !important;
    color: #475569 !important;
}

.dark .fi-wi-stats-overview-stat-label {
    color: #94a3b8 !important;
}

.fi-wi-stats-overview-stat-value {
    font-size: 2.25rem !important;
    font-weight: 800 !important;
    letter-spacing: -0.04em !important;
    color: #0f172a !important;
    margin-top: 0.375rem !important;
    margin-bottom: 0.375rem !important;
}

.dark .fi-wi-stats-overview-stat-value {
    color: #ffffff !important;
}

.fi-wi-stats-overview-stat-description {
    font-size: 0.8125rem !important;
    font-weight: 600 !important;
}

/* Rounded Soft Background Badges for Stat Card Icons */
.fi-wi-stats-overview-stat-icon {
    color: #6366f1 !important;
    background: rgba(99, 102, 241, 0.08) !important;
    padding: 7px !important;
    width: 20px !important;
    height: 20px !important;
    border-radius: 50% !important;
    box-sizing: content-box !important;
}

.dark .fi-wi-stats-overview-stat-icon {
    color: #a5b4fc !important;
    background: rgba(165, 180, 252, 0.1) !important;
}

.fi-wi-stats-overview-stat:has(.fi-color-success) .fi-wi-stats-overview-stat-icon {
    color: #10b981 !important;
    background: rgba(16, 185, 129, 0.08) !important;
}

.fi-wi-stats-overview-stat:has(.fi-color-info) .fi-wi-stats-overview-stat-icon {
    color: #0284c7 !important;
    background: rgba(2, 132, 199, 0.08) !important;
}

.fi-wi-stats-overview-stat:has(.fi-color-warning) .fi-wi-stats-overview-stat-icon {
    color: #f59e0b !important;
    background: rgba(245, 158, 11, 0.08) !important;
}

.fi-wi-stats-overview-stat:has(.fi-color-danger) .fi-wi-stats-overview-stat-icon {
    color: #ef4444 !important;
    background: rgba(239, 68, 68, 0.08) !important;
}

.dark .fi-wi-stats-overview-stat:has(.fi-color-success) .fi-wi-stats-overview-stat-icon {
    color: #34d399 !important;
    background: rgba(52, 211, 153, 0.12) !important;
}

.dark .fi-wi-stats-overview-stat:has(.fi-color-info) .fi-wi-stats-overview-stat-icon {
    color: #38bdf8 !important;
    background: rgba(56, 189, 248, 0.12) !important;
}

.dark .fi-wi-stats-overview-stat:has(.fi-color-warning) .fi-wi-stats-overview-stat-icon {
    color: #fbbf24 !important;
    background: rgba(251, 191, 36, 0.12) !important;
}

.dark .fi-wi-stats-overview-stat:has(.fi-color-danger) .fi-wi-stats-overview-stat-icon {
    color: #f87171 !important;
    background: rgba(248, 113, 113, 0.12) !important;
}

/* --- Chart Cards --- */
.fi-wi-chart .fi-section {
    border-radius: 16px !important;
    background: #ffffff !important;
    border: 1px solid rgba(226, 232, 240, 0.8) !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01) !important;
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.25s !important;
}

.fi-wi-chart .fi-section:hover {
    transform: translateY(-4px) !important;
    box-shadow: 0 12px 20px -3px rgba(0, 0, 0, 0.05), 0 4px 6px -2px rgba(0, 0, 0, 0.02) !important;
    border-color: rgba(99, 102, 241, 0.25) !important;
}

.dark .fi-wi-chart .fi-section {
    background: #1e1b4b !important;
    border: 1px solid rgba(255, 255, 255, 0.05) !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2), 0 2px 4px -1px rgba(0, 0, 0, 0.1) !important;
}

.dark .fi-wi-chart .fi-section:hover {
    border-color: rgba(165, 180, 252, 0.2) !important;
    box-shadow: 0 12px 25px -3px rgba(0, 0, 0, 0.4), 0 4px 10px -2px rgba(0, 0, 0, 0.2) !important;
}

.fi-wi-chart .fi-section-header-heading {
    font-size: 1rem !important;
    font-weight: 700 !important;
    letter-spacing: -0.02em !important;
    color: #0f172a !important;
}

.dark .fi-wi-chart .fi-section-header-heading {
    color: #ffffff !important;
}

.fi-wi-chart .fi-section-header-description {
    font-size: 0.8125rem !important;
    font-weight: 500 !important;
}
</style>
