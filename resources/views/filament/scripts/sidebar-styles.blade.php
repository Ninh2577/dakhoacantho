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

/* === Premium Category Tree Page Overrides === */

.filament-tree-page #nestable-menu {
    gap: 0.75rem !important;
    margin-bottom: 1.5rem !important;
    display: flex !important;
}

.filament-tree-page #nestable-menu .btn-group {
    display: flex !important;
    gap: 0.5rem !important;
}

.filament-tree-page #nestable-menu button {
    border-radius: 8px !important;
    font-weight: 600 !important;
    font-size: 0.8125rem !important;
    transition: all 0.2s ease !important;
}

/* Save Button */
.filament-tree-page #nestable-menu button[data-action="save"] {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25) !important;
    border: none !important;
}

.filament-tree-page #nestable-menu button[data-action="save"]:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 16px rgba(99, 102, 241, 0.35) !important;
}

/* Expand / Collapse All */
.filament-tree-page #nestable-menu button[data-action="expand-all"],
.filament-tree-page #nestable-menu button[data-action="collapse-all"] {
    border: 1px solid rgba(226, 232, 240, 0.8) !important;
    background: #ffffff !important;
    color: #475569 !important;
}

.filament-tree-page #nestable-menu button[data-action="expand-all"]:hover,
.filament-tree-page #nestable-menu button[data-action="collapse-all"]:hover {
    background: #f8fafc !important;
    color: #0f172a !important;
    border-color: rgba(99, 102, 241, 0.2) !important;
}

/* Create New Category Header Button */
.fi-header-actions button,
.fi-header-actions a {
    border-radius: 8px !important;
    font-weight: 600 !important;
    font-size: 0.8125rem !important;
    transition: all 0.2s ease !important;
    background: linear-gradient(135deg, #1e40af 0%, #1d4ed8 100%) !important;
    box-shadow: 0 4px 12px rgba(30, 64, 175, 0.2) !important;
    border: none !important;
}

.fi-header-actions button:hover,
.fi-header-actions a:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 16px rgba(30, 64, 175, 0.3) !important;
}

/* Tree Rows */
.filament-tree-row {
    margin-bottom: 0.625rem !important;
}

.filament-tree-row .dd-handle {
    height: 48px !important;
    min-height: 48px !important;
    border-radius: 10px !important;
    border: 1px solid rgba(226, 232, 240, 0.8) !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.01) !important;
    background: #ffffff !important;
    padding-left: 0 !important;
    transition: transform 0.2s ease, box-shadow 0.2s ease, border-color 0.2s ease !important;
}

.filament-tree-row .dd-handle:hover {
    border-color: rgba(99, 102, 241, 0.2) !important;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.05) !important;
}

.dark .filament-tree-row .dd-handle {
    background: #1e1b4b !important;
    border: 1px solid rgba(255, 255, 255, 0.05) !important;
}

.dark .filament-tree-row .dd-handle:hover {
    border-color: rgba(165, 180, 252, 0.15) !important;
}

/* Drag Handle Button (left-side) */
.filament-tree-row .dd-handle > button {
    height: 100% !important;
    width: 32px !important;
    background: #f8fafc !important;
    border-right: 1px solid rgba(226, 232, 240, 0.8) !important;
    border-radius: 10px 0 0 10px !important;
    display: flex !important;
    align-items: center !important;
    justify-content: center !important;
    color: #94a3b8 !important;
    padding: 0 !important;
    transition: all 0.2s ease !important;
}

.filament-tree-row .dd-handle > button:hover {
    background: #f1f5f9 !important;
    color: #64748b !important;
}

.dark .filament-tree-row .dd-handle > button {
    background: rgba(255, 255, 255, 0.03) !important;
    border-right: 1px solid rgba(255, 255, 255, 0.05) !important;
}

/* Content wrapper */
.filament-tree-row .dd-content {
    align-items: center !important;
    padding-left: 0.75rem !important;
    gap: 0.5rem !important;
}

/* Category Label Text */
.filament-tree-row .filament-tree-row-display,
.filament-tree-row .filament-tree-row-display span,
.filament-tree-row .dd-content span {
    font-size: 0.875rem !important;
    font-weight: 600 !important;
    color: #1e293b !important;
}

.dark .filament-tree-row .filament-tree-row-display,
.dark .filament-tree-row .dd-content span {
    color: #f1f5f9 !important;
}

/* --- Tree Hierarchical Connection Lines --- */
.filament-tree .dd-list .dd-list {
    padding-left: 24px !important; /* space for nesting */
    position: relative !important;
    border-left: 1.5px dashed rgba(99, 102, 241, 0.3) !important; /* dashed connection line */
    margin-left: 16px !important;
    margin-top: 0.25rem !important;
    margin-bottom: 0.5rem !important;
}

.dark .filament-tree .dd-list .dd-list {
    border-left: 1.5px dashed rgba(165, 180, 252, 0.2) !important;
}

/* Right side action buttons */
.filament-tree-row .dd-nodrag.ml-auto {
    display: flex !important;
    gap: 0.375rem !important;
    align-items: center !important;
}

/* Expand / Collapse Chevrons */
.filament-tree-row .dd-item-btns button svg {
    color: #64748b !important;
    width: 1.125rem !important;
    height: 1.125rem !important;
    transition: transform 0.2s ease !important;
}

.filament-tree-row .dd-item-btns button:hover svg {
    color: #6366f1 !important;
    transform: scale(1.1) !important;
}

/* --- Edit & Delete Action Buttons --- */
.filament-tree-row .filament-tree-icon-button-action {
    width: 32px !important;
    height: 32px !important;
    border-radius: 8px !important;
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
    border: 1px solid transparent !important;
    background: transparent !important;
    margin-left: 6px !important;
}

/* Edit Button Styling */
.filament-tree-row .filament-tree-icon-button-action:has(svg[class*="pencil"]),
.filament-tree-row .filament-tree-icon-button-action:has(svg[class*="edit"]),
.filament-tree-row .filament-tree-icon-button-action.fi-color-primary,
.filament-tree-row .filament-tree-icon-button-action.fi-color-custom:not(.fi-color-danger) {
    color: #6366f1 !important; /* Premium Indigo */
    background: rgba(99, 102, 241, 0.06) !important;
    border-color: rgba(99, 102, 241, 0.12) !important;
}

.filament-tree-row .filament-tree-icon-button-action:has(svg[class*="pencil"]):hover,
.filament-tree-row .filament-tree-icon-button-action:has(svg[class*="edit"]):hover,
.filament-tree-row .filament-tree-icon-button-action.fi-color-primary:hover,
.filament-tree-row .filament-tree-icon-button-action.fi-color-custom:not(.fi-color-danger):hover {
    background: #6366f1 !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.3) !important;
    border-color: #6366f1 !important;
    transform: translateY(-2px) !important;
}

/* Delete Button Styling */
.filament-tree-row .filament-tree-icon-button-action:has(svg[class*="trash"]),
.filament-tree-row .filament-tree-icon-button-action.fi-color-danger {
    color: #ef4444 !important; /* Premium Red */
    background: rgba(239, 68, 68, 0.06) !important;
    border-color: rgba(239, 68, 68, 0.12) !important;
}

.filament-tree-row .filament-tree-icon-button-action:has(svg[class*="trash"]):hover,
.filament-tree-row .filament-tree-icon-button-action.fi-color-danger:hover {
    background: #ef4444 !important;
    color: #ffffff !important;
    box-shadow: 0 4px 12px rgba(239, 68, 68, 0.3) !important;
    border-color: #ef4444 !important;
    transform: translateY(-2px) !important;
}

/* SVG icons inside action buttons */
.filament-tree-row .filament-tree-icon-button-action svg {
    width: 1rem !important;
    height: 1rem !important;
    color: inherit !important;
    transition: transform 0.2s ease !important;
}

/* === Premium Media Library Grid Overrides === */
.fi-ta-content-grid {
    gap: 1.5rem !important;
}

/* 1. Card Container (.fi-ta-record) */
.fi-ta-content-grid .fi-ta-record {
    display: flex !important;
    flex-direction: column !important;
    height: 100% !important; /* Make all cards match height */
    background: #ffffff !important;
    border: 1px solid rgba(226, 232, 240, 0.8) !important;
    border-radius: 16px !important;
    padding: 0 !important; /* image flush to borders */
    overflow: hidden !important;
    position: relative !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.02), 0 2px 4px -1px rgba(0, 0, 0, 0.01) !important;
    transition: transform 0.25s cubic-bezier(0.4, 0, 0.2, 1), box-shadow 0.25s cubic-bezier(0.4, 0, 0.2, 1), border-color 0.25s !important;
}

.fi-ta-content-grid .fi-ta-record:hover {
    transform: translateY(-6px) !important;
    box-shadow: 0 16px 24px -4px rgba(0, 0, 0, 0.06), 0 8px 8px -4px rgba(0, 0, 0, 0.03) !important;
    border-color: rgba(99, 102, 241, 0.25) !important;
}

.dark .fi-ta-content-grid .fi-ta-record {
    background: #1e1b4b !important;
    border: 1px solid rgba(255, 255, 255, 0.05) !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.2), 0 2px 4px -1px rgba(0, 0, 0, 0.1) !important;
}

.dark .fi-ta-content-grid .fi-ta-record:hover {
    border-color: rgba(165, 180, 252, 0.20) !important;
    box-shadow: 0 16px 25px -4px rgba(0, 0, 0, 0.4), 0 8px 10px -4px rgba(0, 0, 0, 0.2) !important;
}

/* 2. Inner Wrapper (line 546) */
.fi-ta-content-grid .fi-ta-record > div {
    display: flex !important;
    flex-direction: column !important;
    height: 100% !important;
    width: 100% !important;
    align-items: stretch !important;
}

/* 3. Content Container (line 576) */
.fi-ta-content-grid .fi-ta-record > div > div {
    display: flex !important;
    flex-direction: column !important;
    height: 100% !important;
    width: 100% !important;
    padding-top: 0 !important;
    padding-bottom: 0 !important;
}

/* Image preview crop fixes inside the card */
.fi-ta-content-grid .fi-ta-col-instant img,
.fi-ta-content-grid .fi-ta-col-preview img {
    object-fit: cover !important;
    width: 100% !important;
    height: 160px !important;
    border-radius: 14px 14px 0 0 !important;
}

/* Push actions block to the bottom of the card and style it */
.fi-ta-content-grid .fi-ta-record > div > div > div:last-child {
    background: #f8fafc !important;
    border-top: 1px solid rgba(226, 232, 240, 0.8) !important;
    padding: 0.75rem 1rem !important;
    display: flex !important;
    justify-content: flex-end !important;
    gap: 0.5rem !important;
    margin-top: auto !important; /* push footer to the bottom */
}

.dark .fi-ta-content-grid .fi-ta-record > div > div > div:last-child {
    background: rgba(255, 255, 255, 0.02) !important;
    border-top: 1px solid rgba(255, 255, 255, 0.05) !important;
}

/* Action button styles in the card footer */
.fi-ta-content-grid .fi-ta-actions button,
.fi-ta-content-grid .fi-ta-actions a {
    border-radius: 8px !important;
    font-size: 0.75rem !important;
    font-weight: 600 !important;
    padding: 0.375rem 0.75rem !important;
    transition: all 0.2s ease !important;
}

/* Floating checkbox container inside the card */
.fi-ta-content-grid .fi-ta-record-checkbox {
    position: absolute !important;
    top: 12px !important;
    left: 12px !important;
    z-index: 10 !important;
    margin: 0 !important;
    padding: 4px !important;
    background: rgba(255, 255, 255, 0.9) !important;
    border-radius: 6px !important;
    border: 1px solid rgba(226, 232, 240, 0.8) !important;
    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05) !important;
}

.dark .fi-ta-content-grid .fi-ta-record-checkbox {
    background: rgba(30, 27, 75, 0.9) !important;
    border: 1px solid rgba(255, 255, 255, 0.05) !important;
}

/* === Premium Role Permissions Page Overrides === */

/* Tabs segmented controller styling */
.fi-role-permissions-page .fi-tabs,
.fi-schema-settings-page .fi-tabs {
    background: #f1f5f9 !important; /* light slate */
    border-radius: 12px !important;
    padding: 6px !important;
    gap: 6px !important;
    border: none !important;
    margin-bottom: 1.5rem !important;
    display: inline-flex !important;
    width: auto !important;
}

.dark .fi-role-permissions-page .fi-tabs,
.dark .fi-schema-settings-page .fi-tabs {
    background: rgba(255, 255, 255, 0.03) !important;
    border: 1px solid rgba(255, 255, 255, 0.05) !important;
}

.fi-role-permissions-page .fi-tabs-item,
.fi-schema-settings-page .fi-tabs-item {
    border-radius: 8px !important;
    padding: 0.5rem 1.25rem !important;
    font-weight: 600 !important;
    font-size: 0.8125rem !important;
    color: #64748b !important;
    background: transparent !important;
    border: none !important;
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1) !important;
}

.fi-role-permissions-page .fi-tabs-item:hover,
.fi-schema-settings-page .fi-tabs-item:hover {
    color: #0f172a !important;
    background: rgba(255, 255, 255, 0.5) !important;
}

.dark .fi-role-permissions-page .fi-tabs-item:hover,
.dark .fi-schema-settings-page .fi-tabs-item:hover {
    color: #ffffff !important;
    background: rgba(255, 255, 255, 0.05) !important;
}

.fi-role-permissions-page .fi-tabs-item.fi-active,
.fi-schema-settings-page .fi-tabs-item.fi-active {
    background: #ffffff !important;
    color: #6366f1 !important;
    box-shadow: 0 4px 10px rgba(0, 0, 0, 0.04), 0 2px 4px rgba(0, 0, 0, 0.02) !important;
}

.dark .fi-role-permissions-page .fi-tabs-item.fi-active,
.dark .fi-schema-settings-page .fi-tabs-item.fi-active {
    background: #1e1b4b !important;
    color: #a5b4fc !important;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.2) !important;
}

/* Inner form card panels */
.fi-role-permissions-page .fi-fo-tabs > div:last-child,
.fi-schema-settings-page .fi-fo-tabs > div:last-child {
    background: transparent !important;
    border: none !important;
    padding: 0 !important;
    box-shadow: none !important;
}

/* Repeater Items (custom roles cards) */
.fi-role-permissions-page .fi-fo-repeater-item {
    background: #ffffff !important;
    border: 1px solid rgba(226, 232, 240, 0.8) !important;
    border-left: 4px solid #6366f1 !important; /* Premium left bar */
    border-radius: 12px !important;
    padding: 1.5rem !important;
    box-shadow: 0 2px 8px rgba(0, 0, 0, 0.02) !important;
    margin-bottom: 1.5rem !important;
    transition: all 0.25s ease !important;
}

.fi-role-permissions-page .fi-fo-repeater-item:hover {
    transform: translateY(-2px) !important;
    box-shadow: 0 10px 20px rgba(99, 102, 241, 0.05) !important;
    border-color: rgba(99, 102, 241, 0.2) !important;
}

.dark .fi-role-permissions-page .fi-fo-repeater-item {
    background: #1e1b4b !important;
    border: 1px solid rgba(255, 255, 255, 0.05) !important;
    border-left: 4px solid #818cf8 !important;
}

/* Repeater header lines */
.fi-role-permissions-page .fi-fo-repeater-item-header {
    padding-bottom: 0.75rem !important;
    border-bottom: 1px dashed rgba(226, 232, 240, 0.8) !important;
    margin-bottom: 1.25rem !important;
}

.dark .fi-role-permissions-page .fi-fo-repeater-item-header {
    border-bottom: 1px dashed rgba(255, 255, 255, 0.05) !important;
}

/* CheckboxList grid label options (detailed permissions) */
.fi-role-permissions-page .fi-fo-checkbox-list-option-label {
    background: #ffffff !important;
    border: 1px solid rgba(226, 232, 240, 0.8) !important;
    border-radius: 10px !important;
    padding: 0.75rem 1rem !important;
    transition: all 0.2s ease !important;
    cursor: pointer !important;
    display: flex !important;
    align-items: center !important;
}

.fi-role-permissions-page .fi-fo-checkbox-list-option-label:hover {
    border-color: rgba(99, 102, 241, 0.3) !important;
    background: #f8fafc !important;
}

.fi-role-permissions-page .fi-fo-checkbox-list-option-label:has(input:checked) {
    border-color: #6366f1 !important;
    background: rgba(99, 102, 241, 0.04) !important;
}

.dark .fi-role-permissions-page .fi-fo-checkbox-list-option-label {
    background: #1e1b4b !important;
    border: 1px solid rgba(255, 255, 255, 0.05) !important;
}

.dark .fi-role-permissions-page .fi-fo-checkbox-list-option-label:hover {
    background: rgba(255, 255, 255, 0.02) !important;
}

.dark .fi-role-permissions-page .fi-fo-checkbox-list-option-label:has(input:checked) {
    border-color: #818cf8 !important;
    background: rgba(99, 102, 241, 0.08) !important;
}

/* Add Repeater Item Button styling */
.fi-role-permissions-page .fi-fo-repeater-add-button button,
.fi-role-permissions-page .fi-fo-repeater button[wire\:click*="add"] {
    background: #ffffff !important;
    border: 1px dashed rgba(99, 102, 241, 0.4) !important;
    color: #6366f1 !important;
    font-weight: 600 !important;
    border-radius: 8px !important;
    padding: 0.5rem 1.25rem !important;
    transition: all 0.2s ease !important;
}

.fi-role-permissions-page .fi-fo-repeater-add-button button:hover,
.fi-role-permissions-page .fi-fo-repeater button[wire\:click*="add"]:hover {
    background: rgba(99, 102, 241, 0.04) !important;
    border-style: solid !important;
    border-color: #6366f1 !important;
    transform: translateY(-1px) !important;
}

.dark .fi-role-permissions-page .fi-fo-repeater-add-button button,
.dark .fi-role-permissions-page .fi-fo-repeater button[wire\:click*="add"] {
    background: rgba(255, 255, 255, 0.02) !important;
    border: 1px dashed rgba(165, 180, 252, 0.3) !important;
    color: #a5b4fc !important;
}

/* Save Form Button */
.fi-role-permissions-page button[type="submit"] {
    background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%) !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    border-radius: 8px !important;
    padding: 0.625rem 1.5rem !important;
    box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25) !important;
    border: none !important;
    transition: all 0.2s ease !important;
}

.fi-role-permissions-page button[type="submit"]:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 16px rgba(99, 102, 241, 0.35) !important;
}

/* --- Code Block & JSON-LD Previews --- */
.fi-fo-placeholder pre {
    font-family: 'Fira Code', 'Courier New', Courier, monospace !important;
    background: #090d16 !important; /* deep dark blue */
    border: 1px solid rgba(255, 255, 255, 0.05) !important;
    color: #34d399 !important; /* Emerald green text */
    box-shadow: inset 0 4px 12px rgba(0, 0, 0, 0.6) !important;
    border-radius: 12px !important;
    padding: 1.25rem !important;
}

.dark .fi-fo-placeholder pre {
    background: #05070c !important;
    border: 1px solid rgba(255, 255, 255, 0.03) !important;
}

/* Monospace Textarea Code Editor */
.fi-fo-textarea textarea.font-mono {
    font-family: 'Fira Code', 'Courier New', Courier, monospace !important;
    background: #090d16 !important;
    color: #34d399 !important;
    border: 1px solid rgba(255, 255, 255, 0.06) !important;
    box-shadow: inset 0 4px 12px rgba(0, 0, 0, 0.6) !important;
    border-radius: 12px !important;
    padding: 1.25rem !important;
    font-size: 11px !important;
    line-height: 1.6 !important;
}

.dark .fi-fo-textarea textarea.font-mono {
    background: #05070c !important;
    border: 1px solid rgba(255, 255, 255, 0.04) !important;
}

/* === Premium API Sync Settings Page Overrides === */

/* 1. Security Lock Screen Card */
.fi-api-sync-page .fi-lock-card {
    background: #ffffff !important;
    border: 1px solid rgba(226, 232, 240, 0.8) !important;
    box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.04), 0 8px 10px -6px rgba(0, 0, 0, 0.04) !important;
}

.dark .fi-api-sync-page .fi-lock-card {
    background: #111827 !important;
    border: 1px solid rgba(255, 255, 255, 0.05) !important;
}

/* 2. Top Banner Card */
.fi-api-sync-page .fi-info-banner {
    background: linear-gradient(135deg, #1e1b4b 0%, #311042 100%) !important; /* deep rich indigo/purple */
    color: #ffffff !important;
    border: 1px solid rgba(99, 102, 241, 0.15) !important;
}

.fi-api-sync-page .fi-info-banner h4 {
    color: #ffffff !important;
    font-weight: 700 !important;
}

.fi-api-sync-page .fi-info-banner p {
    color: #cbd5e1 !important; /* high contrast slate text */
}

/* 3. Developer Console Terminal Card */
.fi-api-sync-page .fi-terminal-card {
    background: #0f172a !important; /* Slate 900 code terminal background */
    border: 1px solid rgba(255, 255, 255, 0.08) !important;
    color: #cbd5e1 !important;
    box-shadow: 0 10px 30px rgba(0, 0, 0, 0.25) !important;
}

.dark .fi-api-sync-page .fi-terminal-card {
    background: #090d16 !important; /* deep pitch black code terminal in dark mode */
}

.fi-api-sync-page .fi-terminal-card label {
    color: #94a3b8 !important; /* light slate labels */
}

.fi-api-sync-page .fi-terminal-card .fi-terminal-header {
    border-bottom: 1px solid rgba(255, 255, 255, 0.08) !important;
}

/* Terminal input codes */
.fi-api-sync-page .fi-code-box {
    background: #090d16 !important; /* deep pitch black */
    border: 1px solid rgba(255, 255, 255, 0.05) !important;
    color: #38bdf8 !important; /* clean light blue code text */
}

.dark .fi-api-sync-page .fi-code-box {
    background: #05070c !important;
}

.fi-api-sync-page .fi-code-box button {
    color: #64748b !important;
}

.fi-api-sync-page .fi-code-box button:hover {
    color: #34d399 !important; /* neon emerald on hover */
}

/* === Security Lock Form Design === */
.fi-api-sync-page .fi-lock-card {
    max-width: 400px !important;
    margin: 3rem auto !important;
    background: #ffffff !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.05), 0 10px 10px -5px rgba(0, 0, 0, 0.04) !important;
    border-radius: 20px !important;
    padding: 2.5rem 2rem !important;
}

.dark .fi-api-sync-page .fi-lock-card {
    background: #111827 !important;
    border: 1px solid rgba(255, 255, 255, 0.05) !important;
    box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3), 0 10px 10px -5px rgba(0, 0, 0, 0.2) !important;
}

/* Custom Password Input Wrapper */
.fi-api-sync-page .fi-lock-input-wrapper {
    position: relative !important;
    margin-top: 1.5rem !important;
    margin-bottom: 1.5rem !important;
}

.fi-api-sync-page .fi-lock-input {
    width: 100% !important;
    padding: 0.75rem 1rem 0.75rem 2.75rem !important; /* space for left icon */
    background: #f8fafc !important;
    border: 1px solid #cbd5e1 !important;
    border-radius: 12px !important;
    font-size: 0.875rem !important;
    color: #0f172a !important;
    transition: all 0.2s ease-in-out !important;
    outline: none !important;
}

.dark .fi-api-sync-page .fi-lock-input {
    background: #090d16 !important;
    border: 1px solid #1e293b !important;
    color: #f8fafc !important;
}

.fi-api-sync-page .fi-lock-input:focus {
    border-color: #6366f1 !important;
    box-shadow: 0 0 0 4px rgba(99, 102, 241, 0.15) !important;
    background: #ffffff !important;
}

.dark .fi-api-sync-page .fi-lock-input:focus {
    background: #090d16 !important;
}

/* Left & Right Icons inside Input */
.fi-api-sync-page .fi-lock-input-icon-left {
    position: absolute !important;
    left: 0.875rem !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    color: #64748b !important;
    pointer-events: none !important;
}

.fi-api-sync-page .fi-lock-input-icon-right {
    position: absolute !important;
    right: 0.875rem !important;
    top: 50% !important;
    transform: translateY(-50%) !important;
    color: #64748b !important;
    cursor: pointer !important;
    transition: color 0.15s ease !important;
    background: none !important;
    border: none !important;
    padding: 0 !important;
}

.fi-api-sync-page .fi-lock-input-icon-right:hover {
    color: #6366f1 !important;
}

/* Custom Unlock Button */
.fi-api-sync-page .fi-lock-btn {
    width: 100% !important;
    padding: 0.75rem 1.5rem !important;
    background: linear-gradient(135deg, #4f46e5 0%, #6366f1 100%) !important;
    color: #ffffff !important;
    font-weight: 600 !important;
    font-size: 0.875rem !important;
    border-radius: 12px !important;
    border: none !important;
    cursor: pointer !important;
    box-shadow: 0 4px 12px rgba(79, 70, 229, 0.25) !important;
    transition: all 0.2s ease-in-out !important;
    text-align: center !important;
    display: block !important;
}

.fi-api-sync-page .fi-lock-btn:hover {
    transform: translateY(-1px) !important;
    box-shadow: 0 6px 16px rgba(79, 70, 229, 0.35) !important;
}

.fi-api-sync-page .fi-lock-btn:active {
    transform: translateY(1px) !important;
}

/* === Security Scan Page Overrides === */

/* Scoped Container spacing */
.fi-security-scan-page {
    display: block;
}

/* Base Card Styling */
.fi-security-scan-page .fi-scan-card {
    border-radius: 16px !important;
    padding: 1.5rem !important;
    min-height: 140px !important;
    display: flex !important;
    flex-direction: column !important;
    justify-content: space-between !important;
    transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1) !important;
    border: 1px solid #e2e8f0 !important;
    box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05) !important;
}

.dark .fi-security-scan-page .fi-scan-card {
    border: 1px solid rgba(255, 255, 255, 0.05) !important;
}

/* Hover effects */
.fi-security-scan-page .fi-scan-card:hover {
    transform: translateY(-4px) !important;
    box-shadow: 0 12px 20px -10px rgba(0, 0, 0, 0.15) !important;
}

/* 1. Status: Healthy */
.fi-security-scan-page .fi-scan-card.status-healthy {
    background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
    color: #ffffff !important;
    border: 1px solid rgba(16, 185, 129, 0.2) !important;
}

/* 2. Status: Warning */
.fi-security-scan-page .fi-scan-card.status-warning {
    background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
    color: #ffffff !important;
    border: 1px solid rgba(245, 158, 11, 0.2) !important;
}

/* 3. Status: Danger */
.fi-security-scan-page .fi-scan-card.status-danger {
    background: linear-gradient(135deg, #ef4444 0%, #b91c1c 100%) !important;
    color: #ffffff !important;
    border: 1px solid rgba(239, 68, 68, 0.2) !important;
}

/* Light theme for numeric/metadata cards */
.fi-security-scan-page .fi-scan-card.info-card {
    background: #ffffff !important;
    color: #1e293b !important;
}

.dark .fi-security-scan-page .fi-scan-card.info-card {
    background: #111827 !important;
    color: #cbd5e1 !important;
}

/* Pulsing Cyber Dot Animation */
.fi-security-scan-page .pulse-dot {
    width: 10px;
    height: 10px;
    border-radius: 50%;
    position: relative;
    display: inline-block;
}

.fi-security-scan-page .pulse-dot.dot-healthy {
    background-color: #34d399;
    box-shadow: 0 0 8px #34d399;
}
.fi-security-scan-page .pulse-dot.dot-warning {
    background-color: #fbbf24;
    box-shadow: 0 0 8px #fbbf24;
}
.fi-security-scan-page .pulse-dot.dot-danger {
    background-color: #f87171;
    box-shadow: 0 0 8px #f87171;
}

.fi-security-scan-page .pulse-dot::after {
    content: '';
    position: absolute;
    width: 100%;
    height: 100%;
    border-radius: 50%;
    background-color: inherit;
    opacity: 0.75;
    animation: pulse-ring 1.5s cubic-bezier(0.215, 0.610, 0.355, 1) infinite;
    top: 0;
    left: 0;
}
</style>
