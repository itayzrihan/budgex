<?php
/**
 * Direct Enhanced Budget Page Template
 * This template loads when users access /budgexpage/{id}
 * It directly displays the enhanced budget page without routing complications
 */

get_header(); ?>

<div id="budgex-app-container" class="budgex-enhanced-direct">    <style>
        /* Enhanced budget page styles - CSS Variables */
        :root {
            --bg-primary: #f8fafc;
            --bg-secondary: #f1f5f9;
            --bg-card: #ffffff;
            --bg-input: #ffffff;
            --bg-button: #f8fafc;
            --bg-hover: #e2e8f0;
            --text-primary: #1e293b;
            --text-secondary: #64748b;
            --text-muted: #94a3b8;
            --accent-primary: #4f46e5;
            --accent-secondary: #6366f1;
            --accent-success: #10b981;
            --accent-warning: #f59e0b;
            --accent-danger: #ef4444;
            --border-primary: #e2e8f0;
            --border-secondary: #cbd5e1;
            --border-focus: #4f46e5;
            --shadow-sm: 0 1px 2px 0 rgb(0 0 0 / 0.05);
            --shadow-md: 0 4px 6px -1px rgb(0 0 0 / 0.1), 0 2px 4px -2px rgb(0 0 0 / 0.1);
            --shadow-lg: 0 10px 15px -3px rgb(0 0 0 / 0.1), 0 4px 6px -4px rgb(0 0 0 / 0.1);
            --shadow-xl: 0 20px 25px -5px rgb(0 0 0 / 0.1), 0 8px 10px -6px rgb(0 0 0 / 0.1);
            --radius-sm: 0.375rem;
            --radius-md: 0.5rem;
            --radius-lg: 0.75rem;
            --spacing-xs: 0.25rem;
            --spacing-sm: 0.5rem;
            --spacing-md: 1rem;
            --spacing-lg: 1.5rem;
            --spacing-xl: 2rem;
            --spacing-2xl: 3rem;
            --font-size-xs: 0.75rem;
            --font-size-sm: 0.875rem;
            --font-size-base: 1rem;
            --font-size-lg: 1.125rem;
            --font-size-xl: 1.25rem;
            --font-size-3xl: 1.875rem;
            --font-family-hebrew: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, sans-serif;
        }

        /* Base styles */
        .budgex-enhanced-direct {
            font-family: var(--font-family-hebrew);
            direction: rtl;
            text-align: right;
            background: var(--bg-primary);
            min-height: 100vh;
        }
        
        .budgex-enhanced-direct * {
            box-sizing: border-box;
        }
        
        .budgex-enhanced-main-content {
            max-width: 1400px;
            margin: 0 auto;
            padding: 20px;
            min-height: 500px;
        }

        /* Enhanced Budget Page Styles */
        .budgex-enhanced-budget-page {
            background: var(--bg-primary);
            color: var(--text-primary);
            min-height: 100vh;
            font-family: var(--font-family-hebrew);
        }

        .enhanced-budget-header {
            background: linear-gradient(135deg, var(--bg-secondary) 0%, var(--bg-card) 100%);
            border-bottom: 1px solid var(--border-primary);
            padding: var(--spacing-lg);
            margin-bottom: var(--spacing-xl);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-md);
        }

        .header-top {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: var(--spacing-lg);
            flex-wrap: wrap;
            gap: var(--spacing-md);
        }

        .breadcrumb {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
        }

        .breadcrumb-link {
            color: var(--accent-primary);
            text-decoration: none;
            display: flex;
            align-items: center;
            gap: var(--spacing-xs);
            transition: color 0.2s ease;
        }

        .breadcrumb-link:hover {
            color: var(--accent-secondary);
        }

        .breadcrumb-separator {
            color: var(--text-muted);
        }

        .breadcrumb-current {
            font-weight: 600;
        }

        .header-actions {
            display: flex;
            gap: var(--spacing-md);
            align-items: center;
            flex-wrap: wrap;
        }

        .action-button {
            display: inline-flex;
            align-items: center;
            gap: var(--spacing-xs);
            padding: var(--spacing-sm) var(--spacing-md);
            border: none;
            border-radius: var(--radius-md);
            font-size: var(--font-size-sm);
            font-weight: 500;
            cursor: pointer;
            transition: all 0.2s ease;
            text-decoration: none;
            white-space: nowrap;
        }

        .action-button.primary {
            background: var(--accent-primary);
            color: white;
        }

        .action-button.primary:hover {
            background: var(--accent-secondary);
            transform: translateY(-1px);
            box-shadow: var(--shadow-md);
        }

        .action-button.secondary {
            background: var(--bg-button);
            color: var(--text-primary);
            border: 1px solid var(--border-primary);
        }

        .action-button.secondary:hover {
            background: var(--bg-hover);
            border-color: var(--border-secondary);
        }

        .action-button.danger {
            background: var(--accent-danger);
            color: white;
        }

        .action-button.danger:hover {
            background: #dc2626;
            transform: translateY(-1px);
        }

        .dropdown {
            position: relative;
        }

        .dropdown-menu {
            position: absolute;
            top: 100%;
            right: 0;
            background: var(--bg-card);
            border: 1px solid var(--border-primary);
            border-radius: var(--radius-md);
            box-shadow: var(--shadow-lg);
            min-width: 200px;
            z-index: 1000;
            display: none;
        }

        .dropdown.active .dropdown-menu {
            display: block;
        }

        .dropdown-item {
            display: flex;
            align-items: center;
            gap: var(--spacing-sm);
            padding: var(--spacing-sm) var(--spacing-md);
            color: var(--text-primary);
            text-decoration: none;
            transition: background-color 0.2s ease;
        }

        .dropdown-item:hover {
            background: var(--bg-hover);
        }

        .dropdown-divider {
            height: 1px;
            background: var(--border-primary);
            margin: var(--spacing-xs) 0;
        }

        .header-main {
            display: flex;
            justify-content: space-between;
            align-items: flex-end;
            flex-wrap: wrap;
            gap: var(--spacing-md);
        }

        .budget-title {
            font-size: var(--font-size-3xl);
            font-weight: 700;
            margin: 0 0 var(--spacing-sm) 0;
            background: linear-gradient(135deg, var(--text-primary) 0%, var(--accent-primary) 100%);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            background-clip: text;
        }

        .budget-meta {
            display: flex;
            gap: var(--spacing-md);
            align-items: center;
            flex-wrap: wrap;
        }

        .budget-role-badge {
            padding: var(--spacing-xs) var(--spacing-sm);
            border-radius: var(--radius-sm);
            font-size: var(--font-size-xs);
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .budget-role-badge.owner {
            background: var(--accent-primary);
            color: white;
        }

        .budget-role-badge.admin {
            background: var(--accent-success);
            color: white;
        }

        .budget-role-badge.viewer {
            background: var(--bg-button);
            color: var(--text-secondary);
        }

        .budget-date, .shared-indicator {
            display: flex;
            align-items: center;
            gap: var(--spacing-xs);
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
        }

        .period-selector {
            background: var(--bg-input);
            border: 1px solid var(--border-primary);
            border-radius: var(--radius-md);
            padding: var(--spacing-sm) var(--spacing-md);
            color: var(--text-primary);
            font-size: var(--font-size-sm);
        }

        /* Dashboard Cards */
        .enhanced-dashboard-cards {
            margin-bottom: var(--spacing-xl);
        }

        .dashboard-alert {
            margin-bottom: var(--spacing-lg);
        }

        .alert {
            display: flex;
            gap: var(--spacing-sm);
            padding: var(--spacing-md);
            border-radius: var(--radius-md);
            border-left: 4px solid;
            margin-bottom: var(--spacing-md);
        }

        .alert-info {
            background: rgba(79, 70, 229, 0.1);
            border-left-color: var(--accent-primary);
        }

        .alert-warning {
            background: rgba(245, 158, 11, 0.1);
            border-left-color: var(--accent-warning);
        }

        .alert-content h4 {
            margin: 0 0 var(--spacing-xs) 0;
            font-size: var(--font-size-sm);
            font-weight: 600;
        }

        .alert-content p {
            margin: 0;
            font-size: var(--font-size-sm);
        }

        .dashboard-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr;
            gap: var(--spacing-lg);
            margin-bottom: var(--spacing-lg);
        }

        .dashboard-card {
            background: var(--bg-card);
            border: 1px solid var(--border-primary);
            border-radius: var(--radius-lg);
            overflow: hidden;
            transition: all 0.3s ease;
            box-shadow: var(--shadow-sm);
        }

        .dashboard-card:hover {
            transform: translateY(-2px);
            box-shadow: var(--shadow-lg);
        }

        .dashboard-card .card-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--spacing-lg);
            border-bottom: 1px solid var(--border-primary);
            background: var(--bg-secondary);
        }

        .dashboard-card .card-header h3 {
            margin: 0;
            font-size: var(--font-size-lg);
            font-weight: 600;
        }

        .card-icon {
            color: var(--accent-primary);
            font-size: var(--font-size-xl);
        }

        .dashboard-card .card-content {
            padding: var(--spacing-lg);
        }

        .budget-overview .card-content {
            display: flex;
            gap: var(--spacing-xl);
            align-items: center;
        }

        .progress-circle {
            position: relative;
            width: 120px;
            height: 120px;
            flex-shrink: 0;
        }

        .progress-text {
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            text-align: center;
        }

        .progress-text .percentage {
            display: block;
            font-size: var(--font-size-xl);
            font-weight: 700;
            color: var(--accent-primary);
        }

        .progress-text .label {
            font-size: var(--font-size-xs);
            color: var(--text-secondary);
        }

        .budget-amounts {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: var(--spacing-md);
        }

        .amount-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--spacing-sm) 0;
            border-bottom: 1px solid var(--border-primary);
        }

        .amount-item:last-child {
            border-bottom: none;
        }

        .amount-item .label {
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
        }

        .amount-item .value {
            font-weight: 600;
        }

        .amount-item .value.spent {
            color: var(--accent-danger);
        }

        .amount-item .value.remaining {
            color: var(--accent-success);
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: var(--spacing-md);
        }

        .stat-item {
            text-align: center;
        }

        .stat-value {
            font-size: var(--font-size-lg);
            font-weight: 700;
            color: var(--accent-primary);
            display: block;
        }

        .stat-label {
            font-size: var(--font-size-xs);
            color: var(--text-secondary);
            margin-top: var(--spacing-xs);
        }

        .activity-list {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-sm);
        }

        .activity-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--spacing-sm);
            background: var(--bg-secondary);
            border-radius: var(--radius-md);
        }

        .activity-details {
            flex: 1;
        }

        .activity-description {
            font-weight: 500;
            margin-bottom: var(--spacing-xs);
        }

        .activity-meta {
            display: flex;
            gap: var(--spacing-sm);
            font-size: var(--font-size-xs);
            color: var(--text-secondary);
        }

        .activity-amount {
            font-weight: 600;
            color: var(--accent-primary);
        }

        .no-activity {
            text-align: center;
            padding: var(--spacing-xl);
            color: var(--text-secondary);
        }

        .no-activity .dashicons {
            font-size: 48px;
            width: 48px;
            height: 48px;
            margin-bottom: var(--spacing-md);
        }

        /* Tab Navigation */
        .enhanced-tab-navigation {
            margin-bottom: var(--spacing-xl);
        }

        .tab-nav {
            display: flex;
            background: var(--bg-secondary);
            border-radius: var(--radius-lg);
            padding: var(--spacing-xs);
            gap: var(--spacing-xs);
            overflow-x: auto;
        }

        .tab-button {
            flex: 1;
            padding: var(--spacing-md);
            background: transparent;
            border: none;
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
            font-weight: 500;
            border-radius: var(--radius-md);
            cursor: pointer;
            transition: all 0.2s ease;
            white-space: nowrap;
        }

        .tab-button.active {
            background: var(--accent-primary);
            color: white;
            box-shadow: var(--shadow-md);
        }

        .tab-button:hover:not(.active) {
            background: var(--bg-hover);
            color: var(--text-primary);
        }

        /* Tab Content */
        .tab-content {
            display: none;
        }

        .tab-content.active {
            display: block;
        }

        /* Overview Layout */
        .overview-layout {
            display: grid;
            grid-template-columns: 2fr 1fr;
            gap: var(--spacing-xl);
        }

        .overview-main, .overview-sidebar {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-lg);
        }

        .section-card, .sidebar-card {
            background: var(--bg-card);
            border: 1px solid var(--border-primary);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        .section-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--spacing-lg);
            border-bottom: 1px solid var(--border-primary);
            background: var(--bg-secondary);
        }

        .section-header h3 {
            margin: 0;
            font-size: var(--font-size-lg);
            font-weight: 600;
        }

        .section-content {
            padding: var(--spacing-lg);
        }

        .view-all-link {
            color: var(--accent-primary);
            text-decoration: none;
            font-size: var(--font-size-sm);
            font-weight: 500;
        }

        .view-all-link:hover {
            color: var(--accent-secondary);
        }

        /* Chart Container */
        .chart-container {
            position: relative;
            height: 300px;
            margin: var(--spacing-lg) 0;
        }

        .chart-container canvas {
            max-width: 100%;
            max-height: 100%;
        }

        /* Table Styles */
        .outcomes-preview-table {
            width: 100%;
        }

        .table-header, .table-row {
            display: grid;
            grid-template-columns: 2fr 1fr 1fr 1fr;
            gap: var(--spacing-md);
            padding: var(--spacing-md);
            border-bottom: 1px solid var(--border-primary);
        }

        .table-header {
            background: var(--bg-secondary);
            font-weight: 600;
            font-size: var(--font-size-sm);
        }

        .table-row:hover {
            background: var(--bg-secondary);
        }

        .table-cell {
            display: flex;
            align-items: center;
        }

        .table-cell.amount {
            font-weight: 600;
            color: var(--accent-primary);
        }

        .table-cell.date {
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
        }

        .category-badge {
            background: var(--bg-secondary);
            color: var(--text-primary);
            padding: var(--spacing-xs) var(--spacing-sm);
            border-radius: var(--radius-sm);
            font-size: var(--font-size-xs);
            font-weight: 500;
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: var(--spacing-xl);
            color: var(--text-secondary);
        }

        .empty-state .dashicons {
            font-size: 64px;
            width: 64px;
            height: 64px;
            margin-bottom: var(--spacing-md);
            color: var(--text-muted);
        }

        .empty-state h4 {
            margin: 0 0 var(--spacing-sm) 0;
            color: var(--text-primary);
        }

        .empty-state p {
            margin: 0 0 var(--spacing-md) 0;
        }

        /* Health Indicator */
        .health-indicator {
            padding: var(--spacing-md);
            border-radius: var(--radius-md);
            margin-bottom: var(--spacing-md);
        }

        .health-indicator.healthy {
            background: rgba(16, 185, 129, 0.1);
            color: var(--accent-success);
        }

        .health-indicator.warning {
            background: rgba(245, 158, 11, 0.1);
            color: var(--accent-warning);
        }

        .health-indicator.danger {
            background: rgba(239, 68, 68, 0.1);
            color: var(--accent-danger);
        }

        .health-status {
            display: flex;
            align-items: center;
            gap: var(--spacing-xs);
            font-weight: 600;
        }

        .health-details {
            margin-top: var(--spacing-md);
        }

        .detail-item {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--spacing-sm) 0;
            border-bottom: 1px solid var(--border-primary);
        }

        .detail-item:last-child {
            border-bottom: none;
        }

        .detail-item .label {
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
        }

        .detail-item .value {
            font-weight: 500;
        }

        /* Category Breakdown */
        .categories-breakdown {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-md);
        }

        .category-item {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-xs);
        }

        .category-info {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .category-name {
            font-weight: 500;
        }

        .category-amount {
            font-weight: 600;
            color: var(--accent-primary);
        }

        .category-bar {
            height: 8px;
            background: var(--bg-secondary);
            border-radius: var(--radius-sm);
            overflow: hidden;
        }

        .category-progress {
            height: 100%;
            background: var(--accent-primary);
            transition: width 0.3s ease;
        }

        .category-percentage {
            font-size: var(--font-size-xs);
            color: var(--text-secondary);
            text-align: center;
        }

        /* Outcomes Layout */
        .outcomes-layout {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-lg);
        }

        .outcomes-header {
            background: var(--bg-card);
            border: 1px solid var(--border-primary);
            border-radius: var(--radius-lg);
            padding: var(--spacing-lg);
        }

        .search-filter-bar {
            display: flex;
            gap: var(--spacing-lg);
            align-items: center;
            flex-wrap: wrap;
        }

        .search-group {
            display: flex;
            gap: var(--spacing-xs);
            flex: 1;
            min-width: 250px;
        }

        .search-input {
            flex: 1;
            padding: var(--spacing-sm) var(--spacing-md);
            background: var(--bg-input);
            border: 1px solid var(--border-primary);
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-size: var(--font-size-sm);
        }

        .search-input:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .search-button {
            padding: var(--spacing-sm);
            background: var(--accent-primary);
            border: none;
            border-radius: var(--radius-md);
            color: white;
            cursor: pointer;
        }

        .filter-group {
            display: flex;
            gap: var(--spacing-sm);
            align-items: center;
        }

        .filter-select {
            padding: var(--spacing-sm) var(--spacing-md);
            background: var(--bg-input);
            border: 1px solid var(--border-primary);
            border-radius: var(--radius-md);
            color: var(--text-primary);
            font-size: var(--font-size-sm);
            min-width: 150px;
        }

        .action-group {
            display: flex;
            gap: var(--spacing-sm);
        }

        .filter-button {
            padding: var(--spacing-sm) var(--spacing-md);
            background: var(--bg-button);
            border: 1px solid var(--border-primary);
            border-radius: var(--radius-md);
            color: var(--text-secondary);
            cursor: pointer;
        }

        /* Outcomes Content */
        .outcomes-content {
            background: var(--bg-card);
            border: 1px solid var(--border-primary);
            border-radius: var(--radius-lg);
            overflow: hidden;
        }

        .outcomes-table-container {
            overflow-x: auto;
        }

        .outcomes-table {
            width: 100%;
            border-collapse: collapse;
        }

        .outcomes-table th,
        .outcomes-table td {
            padding: var(--spacing-md);
            text-align: right;
            border-bottom: 1px solid var(--border-primary);
        }

        .outcomes-table th {
            background: var(--bg-secondary);
            font-weight: 600;
            color: var(--text-primary);
        }

        .outcomes-table tbody tr:hover {
            background: var(--bg-secondary);
        }

        .outcomes-table .amount {
            font-weight: 600;
            color: var(--accent-primary);
        }

        .outcomes-table .date {
            color: var(--text-secondary);
            font-size: var(--font-size-sm);
        }

        .outcomes-table .actions {
            width: 120px;
        }

        .action-buttons {
            display: flex;
            gap: var(--spacing-xs);
            justify-content: center;
        }

        .action-buttons button {
            padding: var(--spacing-xs);
            background: transparent;
            border: 1px solid var(--border-primary);
            border-radius: var(--radius-sm);
            color: var(--text-secondary);
            cursor: pointer;
            transition: all 0.2s ease;
        }

        .action-buttons button:hover {
            background: var(--bg-hover);
            color: var(--text-primary);
        }

        .action-buttons .edit-outcome:hover {
            border-color: var(--accent-primary);
            color: var(--accent-primary);
        }

        .action-buttons .delete-outcome:hover {
            border-color: var(--accent-danger);
            color: var(--accent-danger);
        }

        .no-data {
            text-align: center;
            padding: var(--spacing-xl);
            color: var(--text-secondary);
        }

        /* Analysis Layout */
        .analysis-layout {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-lg);
        }

        .analysis-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: var(--spacing-lg);
        }

        .analysis-card {
            background: var(--bg-card);
            border: 1px solid var(--border-primary);
            border-radius: var(--radius-lg);
            overflow: hidden;
            box-shadow: var(--shadow-sm);
        }

        /* Planning, Management, Settings Layouts */
        .planning-layout, .management-layout, .settings-layout {
            display: flex;
            flex-direction: column;
            gap: var(--spacing-lg);
        }

        .planning-header, .management-header, .settings-header {
            text-align: center;
            margin-bottom: var(--spacing-lg);
        }

        .planning-header h2, .management-header h2, .settings-header h2 {
            margin: 0 0 var(--spacing-sm) 0;
            color: var(--text-primary);
        }

        .planning-header p, .management-header p, .settings-header p {
            margin: 0;
            color: var(--text-secondary);
        }

        .planning-content, .management-content, .settings-content {
            background: var(--bg-card);
            border: 1px solid var(--border-primary);
            border-radius: var(--radius-lg);
            padding: var(--spacing-xl);
        }

        /* Modal Styles */
        .modal-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.5);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10000;
        }

        .modal-content {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            box-shadow: var(--shadow-xl);
            max-width: 500px;
            width: 90%;
            max-height: 90vh;
            overflow-y: auto;
        }

        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: var(--spacing-lg);
            border-bottom: 1px solid var(--border-primary);
        }

        .modal-header h3 {
            margin: 0;
            font-size: var(--font-size-lg);
            font-weight: 600;
        }

        .modal-close {
            background: none;
            border: none;
            font-size: var(--font-size-xl);
            color: var(--text-secondary);
            cursor: pointer;
            padding: var(--spacing-xs);
        }

        .modal-close:hover {
            color: var(--text-primary);
        }

        .form-group {
            margin-bottom: var(--spacing-md);
        }

        .form-group label {
            display: block;
            margin-bottom: var(--spacing-xs);
            font-weight: 500;
            color: var(--text-primary);
        }

        .form-group input,
        .form-group textarea,
        .form-group select {
            width: 100%;
            padding: var(--spacing-sm) var(--spacing-md);
            border: 1px solid var(--border-primary);
            border-radius: var(--radius-md);
            background: var(--bg-input);
            color: var(--text-primary);
            font-size: var(--font-size-sm);
        }

        .form-group input:focus,
        .form-group textarea:focus,
        .form-group select:focus {
            outline: none;
            border-color: var(--accent-primary);
            box-shadow: 0 0 0 3px rgba(79, 70, 229, 0.1);
        }

        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: var(--spacing-md);
        }

        .modal-actions {
            display: flex;
            gap: var(--spacing-sm);
            justify-content: flex-end;
            padding: var(--spacing-lg);
            border-top: 1px solid var(--border-primary);
        }

        /* Loading Overlay */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.7);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 10001;
        }

        .loading-spinner {
            background: var(--bg-card);
            border-radius: var(--radius-lg);
            padding: var(--spacing-xl);
            text-align: center;
            box-shadow: var(--shadow-xl);
        }

        .spinner {
            width: 40px;
            height: 40px;
            border: 4px solid var(--border-primary);
            border-top: 4px solid var(--accent-primary);
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 0 auto var(--spacing-md) auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Responsive Design */
        @media (max-width: 1200px) {
            .dashboard-row {
                grid-template-columns: 1fr;
            }
            
            .overview-layout {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 768px) {
            .enhanced-budget-header {
                padding: var(--spacing-md);
            }
            
            .header-top {
                flex-direction: column;
                gap: var(--spacing-md);
                align-items: stretch;
            }
            
            .header-actions {
                justify-content: center;
            }
            
            .header-main {
                flex-direction: column;
                align-items: stretch;
                gap: var(--spacing-md);
            }
            
            .tab-nav {
                overflow-x: auto;
            }
            
            .search-filter-bar {
                flex-direction: column;
                align-items: stretch;
            }
            
            .form-row {
                grid-template-columns: 1fr;
            }
            
            .stats-grid {
                grid-template-columns: 1fr;
            }
            
            .analysis-cards {
                grid-template-columns: 1fr;
            }
        }

        @media (max-width: 480px) {
            .budgex-enhanced-main-content {
                padding: 10px;
            }
            
            .dashboard-card .card-content {
                padding: var(--spacing-md);
            }
            
            .budget-overview .card-content {
                flex-direction: column;
                text-align: center;
            }
            
            .table-header, .table-row {
                grid-template-columns: 1fr;
                gap: var(--spacing-xs);
            }
            
            .table-cell {
                padding: var(--spacing-xs) 0;
                border-bottom: 1px solid var(--border-primary);
            }
            
            .table-cell:last-child {
                border-bottom: none;
            }
            
            .modal-content {
                width: 95%;
                margin: var(--spacing-md);
            }
            
            .action-buttons {
                flex-direction: column;
                gap: 2px;
            }
        }
    </style>

    <div class="budgex-enhanced-header">
        <div class="breadcrumb">
            <a href="<?php echo home_url('/budgex/'); ?>">
                <span class="dashicons dashicons-arrow-right-alt"></span>
                <?php _e('התקציבים שלי', 'budgex'); ?>
            </a>
            <span style="margin: 0 10px; opacity: 0.7;">/</span>
            <span><?php _e('תקציב משופר', 'budgex'); ?></span>
        </div>
        <h1>💰 <?php _e('תקציב משופר - גישה ישירה', 'budgex'); ?></h1>
    </div>    <!-- Enhanced Budget Management Interface - Always Rendered -->
    <div class="budgex-enhanced-budget-page budgex-public-enhanced" data-budget-id="<?php echo esc_attr(get_query_var('budget_id', 'demo')); ?>" dir="rtl">
        
        <?php
        // Get the budget ID from the URL
        $budget_id = get_query_var('budget_id');
        $has_valid_data = false;
        $budget = null;
        $calculation = null;
        $outcomes = array();
        $user_role = 'viewer';
        $user_id = get_current_user_id();
        
        // Initialize required classes
        if (!class_exists('Budgex_Database')) {
            require_once BUDGEX_DIR . 'includes/class-database.php';
        }
        if (!class_exists('Budgex_Permissions')) {
            require_once BUDGEX_DIR . 'includes/class-permissions.php';
        }
        if (!class_exists('Budgex_Budget_Calculator')) {
            require_once BUDGEX_DIR . 'includes/class-budget-calculator.php';
        }
        
        $database = new Budgex_Database();
        $permissions = new Budgex_Permissions();
        $calculator = new Budgex_Budget_Calculator();
        
        // Try to load real data if budget ID exists and user has permission
        if ($budget_id && $permissions->can_view_budget($budget_id, $user_id)) {
            $budget = $database->get_budget($budget_id);
            if ($budget) {
                $calculation = $calculator->calculate_remaining_budget($budget_id);
                $outcomes = $database->get_budget_outcomes($budget_id);
                $user_role = $permissions->get_user_role_on_budget($budget_id, $user_id);
                $has_valid_data = true;
                
                // Enqueue enhanced budget styles and scripts
                wp_enqueue_style('budgex-enhanced-budget', BUDGEX_URL . 'public/css/budgex-enhanced-budget.css', array(), BUDGEX_VERSION);
                wp_enqueue_script('budgex-enhanced-budget', BUDGEX_URL . 'public/js/budgex-enhanced-budget.js', array('jquery'), BUDGEX_VERSION, true);
                wp_enqueue_script('chart-js', 'https://cdn.jsdelivr.net/npm/chart.js', array(), BUDGEX_VERSION, true);
            }
        }
        
        // If no valid data, create demo/placeholder data
        if (!$has_valid_data) {
            $budget = (object) array(
                'id' => 0,
                'budget_name' => $budget_id ? __('תקציב לא נמצא', 'budgex') : __('תקציב לדוגמא', 'budgex'),
                'monthly_budget' => 5000,
                'currency' => 'ILS',
                'created_at' => date('Y-m-d H:i:s'),
                'user_id' => $user_id
            );
            
            $calculation = array(
                'total_spent' => $budget_id ? 0 : 2450,
                'remaining' => $budget_id ? $budget->monthly_budget : 2550,
                'percentage' => $budget_id ? 0 : 49,
                'budget_details' => array(
                    'monthly_budget' => $budget->monthly_budget,
                    'additional_budget' => 0
                )
            );
            
            $outcomes = $budget_id ? array() : array(
                (object) array(
                    'id' => 1,
                    'amount' => 1200,
                    'category' => 'מזון',
                    'description' => 'קניות שבועיות',
                    'date' => date('Y-m-d'),
                    'created_at' => date('Y-m-d H:i:s')
                ),
                (object) array(
                    'id' => 2,
                    'amount' => 850,
                    'category' => 'תחבורה',
                    'description' => 'דלק וחניות',
                    'date' => date('Y-m-d', strtotime('-2 days')),
                    'created_at' => date('Y-m-d H:i:s')
                ),
                (object) array(
                    'id' => 3,
                    'amount' => 400,
                    'category' => 'בילויים',
                    'description' => 'קולנוע ומסעדות',
                    'date' => date('Y-m-d', strtotime('-3 days')),
                    'created_at' => date('Y-m-d H:i:s')
                )
            );
        }
        ?>

        <!-- Enhanced Header with Comprehensive Navigation -->
        <div class="enhanced-budget-header">
            <div class="header-top">
                <div class="breadcrumb">
                    <a href="<?php echo home_url('/budgex/'); ?>" class="breadcrumb-link">
                        <span class="dashicons dashicons-arrow-right-alt"></span>
                        <?php _e('התקציבים שלי', 'budgex'); ?>
                    </a>
                    <span class="breadcrumb-separator">/</span>
                    <span class="breadcrumb-current"><?php echo esc_html($budget->budget_name); ?></span>
                </div>
                
                <div class="header-actions">
                    <button type="button" class="action-button secondary" id="refresh-data">
                        <span class="dashicons dashicons-update"></span>
                        <?php _e('רענן נתונים', 'budgex'); ?>
                    </button>
                    
                    <?php if ($has_valid_data && ($user_role === 'owner' || $user_role === 'admin')): ?>
                        <button type="button" class="action-button primary" id="quick-add-outcome">
                            <span class="dashicons dashicons-plus-alt"></span>
                            <?php _e('הוסף הוצאה', 'budgex'); ?>
                        </button>
                    <?php endif; ?>
                    
                    <div class="dropdown">
                        <button type="button" class="action-button dropdown-toggle" id="budget-menu">
                            <span class="dashicons dashicons-menu"></span>
                            <?php _e('תפריט', 'budgex'); ?>
                        </button>
                        <div class="dropdown-menu">
                            <a href="#" class="dropdown-item" id="export-excel">
                                <span class="dashicons dashicons-download"></span>
                                <?php _e('ייצא לאקסל', 'budgex'); ?>
                            </a>
                            <a href="#" class="dropdown-item" id="print-budget">
                                <span class="dashicons dashicons-printer"></span>
                                <?php _e('הדפס דוח', 'budgex'); ?>
                            </a>
                            <?php if ($has_valid_data && $user_role === 'owner'): ?>
                                <div class="dropdown-divider"></div>
                                <a href="#" class="dropdown-item" id="budget-settings">
                                    <span class="dashicons dashicons-admin-settings"></span>
                                    <?php _e('הגדרות תקציב', 'budgex'); ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <div class="header-main">
                <div class="budget-title-section">
                    <h1 class="budget-title"><?php echo esc_html($budget->budget_name); ?></h1>
                    <div class="budget-meta">
                        <span class="budget-role-badge <?php echo $user_role; ?>">
                            <?php 
                            echo $user_role === 'owner' ? __('בעלים', 'budgex') : 
                                ($user_role === 'admin' ? __('מנהל', 'budgex') : __('צופה', 'budgex')); 
                            ?>
                        </span>
                        <span class="budget-date">
                            <?php printf(__('נוצר ב%s', 'budgex'), date('d/m/Y', strtotime($budget->created_at))); ?>
                        </span>
                        <?php if ($has_valid_data): ?>
                            <span class="shared-indicator">
                                <span class="dashicons dashicons-groups"></span>
                                <?php _e('משותף', 'budgex'); ?>
                            </span>
                        <?php endif; ?>
                    </div>
                </div>
                
                <div class="period-selector-container">
                    <select class="period-selector" id="period-selector">
                        <option value="current"><?php _e('החודש הנוכחי', 'budgex'); ?></option>
                        <option value="last30"><?php _e('30 ימים אחרונים', 'budgex'); ?></option>
                        <option value="custom"><?php _e('טווח מותאם', 'budgex'); ?></option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Enhanced Dashboard Cards -->
        <div class="enhanced-dashboard-cards">
            <!-- Status Alert -->
            <?php if (!$has_valid_data): ?>
                <div class="dashboard-alert">
                    <?php if (!$budget_id): ?>
                        <div class="alert alert-info">
                            <span class="dashicons dashicons-info"></span>
                            <div class="alert-content">
                                <h4><?php _e('ממשק לדוגמא', 'budgex'); ?></h4>
                                <p><?php _e('זהו ממשק לדוגמא של מערכת ניהול התקציב המשופרת. גש לתקציב אמיתי כדי לראות נתונים אמיתיים.', 'budgex'); ?></p>
                            </div>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <span class="dashicons dashicons-warning"></span>
                            <div class="alert-content">
                                <h4><?php _e('תקציב לא נמצא', 'budgex'); ?></h4>
                                <p><?php _e('התקציב שביקשת לא נמצא או שאין לך הרשאה לצפות בו.', 'budgex'); ?></p>
                                <a href="<?php echo home_url('/budgex/'); ?>" class="action-button primary" style="margin-top: 10px;">
                                    <?php _e('חזור לתקציבים', 'budgex'); ?>
                                </a>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>

            <div class="dashboard-row">
                <!-- Budget Overview Card -->
                <div class="dashboard-card budget-overview">
                    <div class="card-header">
                        <h3><?php _e('סקירת תקציב', 'budgex'); ?></h3>
                        <span class="card-icon dashicons dashicons-chart-pie"></span>
                    </div>
                    <div class="card-content">
                        <div class="progress-circle">
                            <svg class="progress-ring" width="120" height="120">
                                <circle cx="60" cy="60" r="50" fill="transparent" stroke="#e5e7eb" stroke-width="8"/>
                                <circle cx="60" cy="60" r="50" fill="transparent" stroke="#4f46e5" stroke-width="8" 
                                        stroke-dasharray="<?php echo 2 * pi() * 50; ?>" 
                                        stroke-dashoffset="<?php echo 2 * pi() * 50 * (1 - $calculation['percentage'] / 100); ?>"/>
                            </svg>
                            <div class="progress-text">
                                <span class="percentage"><?php echo number_format($calculation['percentage'], 1); ?>%</span>
                                <span class="label"><?php _e('מהתקציב', 'budgex'); ?></span>
                            </div>
                        </div>
                        
                        <div class="budget-amounts">
                            <div class="amount-item">
                                <span class="label"><?php _e('תקציב חודשי:', 'budgex'); ?></span>
                                <span class="value"><?php echo number_format($budget->monthly_budget); ?> <?php echo $budget->currency; ?></span>
                            </div>
                            <div class="amount-item">
                                <span class="label"><?php _e('נוצל:', 'budgex'); ?></span>
                                <span class="value spent"><?php echo number_format($calculation['total_spent']); ?> <?php echo $budget->currency; ?></span>
                            </div>
                            <div class="amount-item">
                                <span class="label"><?php _e('נותר:', 'budgex'); ?></span>
                                <span class="value remaining"><?php echo number_format($calculation['remaining']); ?> <?php echo $budget->currency; ?></span>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Quick Stats Card -->
                <div class="dashboard-card quick-stats">
                    <div class="card-header">
                        <h3><?php _e('סטטיסטיקות', 'budgex'); ?></h3>
                        <span class="card-icon dashicons dashicons-analytics"></span>
                    </div>
                    <div class="card-content">
                        <div class="stats-grid">
                            <div class="stat-item">
                                <div class="stat-value"><?php echo count($outcomes); ?></div>
                                <div class="stat-label"><?php _e('הוצאות החודש', 'budgex'); ?></div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value"><?php echo $calculation['total_spent'] > 0 ? number_format($calculation['total_spent'] / max(count($outcomes), 1)) : '0'; ?></div>
                                <div class="stat-label"><?php _e('ממוצע להוצאה', 'budgex'); ?></div>
                            </div>
                            <div class="stat-item">
                                <div class="stat-value"><?php echo $calculation['percentage'] > 100 ? __('חריגה', 'budgex') : __('בתקציב', 'budgex'); ?></div>
                                <div class="stat-label"><?php _e('סטטוס', 'budgex'); ?></div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Activity Card -->
                <div class="dashboard-card recent-activity">
                    <div class="card-header">
                        <h3><?php _e('פעילות אחרונה', 'budgex'); ?></h3>
                        <span class="card-icon dashicons dashicons-clock"></span>
                    </div>
                    <div class="card-content">
                        <?php if (!empty($outcomes)): ?>
                            <div class="activity-list">
                                <?php foreach (array_slice($outcomes, 0, 3) as $outcome): ?>
                                    <div class="activity-item">
                                        <div class="activity-details">
                                            <div class="activity-description"><?php echo esc_html($outcome->description); ?></div>
                                            <div class="activity-meta">
                                                <span class="activity-category"><?php echo esc_html($outcome->category); ?></span>
                                                <span class="activity-date"><?php echo date('d/m', strtotime($outcome->date)); ?></span>
                                            </div>
                                        </div>
                                        <div class="activity-amount">
                                            <?php echo number_format($outcome->amount); ?> <?php echo $budget->currency; ?>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        <?php else: ?>
                            <div class="no-activity">
                                <span class="dashicons dashicons-clipboard"></span>
                                <p><?php _e('אין הוצאות להצגה', 'budgex'); ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Enhanced Tab Navigation -->
        <div class="enhanced-tab-navigation">
            <nav class="tab-nav budget-tabs">
                <button class="tab-button active" data-tab="overview"><?php _e('סקירה כללית', 'budgex'); ?></button>
                <button class="tab-button" data-tab="outcomes"><?php _e('הוצאות', 'budgex'); ?></button>
                <button class="tab-button" data-tab="analysis"><?php _e('ניתוח', 'budgex'); ?></button>
                <?php if ($has_valid_data && ($user_role === 'owner' || $user_role === 'admin')): ?>
                    <button class="tab-button" data-tab="planning"><?php _e('תכנון', 'budgex'); ?></button>
                    <button class="tab-button" data-tab="management"><?php _e('ניהול', 'budgex'); ?></button>
                <?php endif; ?>
                <?php if ($has_valid_data && $user_role === 'owner'): ?>
                    <button class="tab-button" data-tab="settings"><?php _e('הגדרות', 'budgex'); ?></button>
                <?php endif; ?>
            </nav>
        </div>

        <!-- Tab Content Container -->
        <div class="tab-content-container">
            <!-- Overview Tab -->
            <div class="tab-content active" id="tab-overview">
                <div class="overview-layout">
                    <div class="overview-main">
                        <!-- Spending Chart Section -->
                        <div class="section-card">
                            <div class="section-header">
                                <h3><?php _e('גרף הוצאות', 'budgex'); ?></h3>
                                <div class="section-actions">
                                    <button class="action-button secondary" id="change-chart-type">
                                        <?php _e('שנה תצוגה', 'budgex'); ?>
                                    </button>
                                </div>
                            </div>
                            <div class="section-content">
                                <div class="chart-container">
                                    <canvas id="spending-chart"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Recent Outcomes Table -->
                        <div class="section-card">
                            <div class="section-header">
                                <h3><?php _e('הוצאות אחרונות', 'budgex'); ?></h3>
                                <a href="#" class="view-all-link" data-tab="outcomes"><?php _e('צפה בכל ההוצאות', 'budgex'); ?></a>
                            </div>
                            <div class="section-content">
                                <?php if (!empty($outcomes)): ?>
                                    <div class="outcomes-preview-table">
                                        <div class="table-header">
                                            <div class="table-cell"><?php _e('תיאור', 'budgex'); ?></div>
                                            <div class="table-cell"><?php _e('קטגוריה', 'budgex'); ?></div>
                                            <div class="table-cell"><?php _e('סכום', 'budgex'); ?></div>
                                            <div class="table-cell"><?php _e('תאריך', 'budgex'); ?></div>
                                        </div>
                                        <div class="table-body">
                                            <?php foreach (array_slice($outcomes, 0, 5) as $outcome): ?>
                                                <div class="table-row">
                                                    <div class="table-cell"><?php echo esc_html($outcome->description); ?></div>
                                                    <div class="table-cell">
                                                        <span class="category-badge"><?php echo esc_html($outcome->category); ?></span>
                                                    </div>
                                                    <div class="table-cell amount">
                                                        <?php echo number_format($outcome->amount); ?> <?php echo $budget->currency; ?>
                                                    </div>
                                                    <div class="table-cell date">
                                                        <?php echo date('d/m/Y', strtotime($outcome->date)); ?>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        </div>
                                    </div>
                                <?php else: ?>
                                    <div class="empty-state">
                                        <span class="dashicons dashicons-clipboard"></span>
                                        <h4><?php _e('אין הוצאות עדיין', 'budgex'); ?></h4>
                                        <p><?php _e('התחל להוסיף הוצאות כדי לעקוב אחר התקציב שלך', 'budgex'); ?></p>
                                        <?php if ($has_valid_data && ($user_role === 'owner' || $user_role === 'admin')): ?>
                                            <button class="action-button primary" id="add-first-outcome">
                                                <?php _e('הוסף הוצאה ראשונה', 'budgex'); ?>
                                            </button>
                                        <?php endif; ?>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                    
                    <div class="overview-sidebar">
                        <!-- Budget Health Card -->
                        <div class="sidebar-card">
                            <div class="card-header">
                                <h4><?php _e('בריאות תקציב', 'budgex'); ?></h4>
                            </div>
                            <div class="card-content">
                                <div class="health-indicator <?php echo $calculation['percentage'] > 90 ? 'danger' : ($calculation['percentage'] > 70 ? 'warning' : 'healthy'); ?>">
                                    <div class="health-status">
                                        <?php 
                                        if ($calculation['percentage'] > 100) {
                                            echo '<span class="dashicons dashicons-warning"></span>';
                                            _e('חריגה מהתקציב', 'budgex');
                                        } elseif ($calculation['percentage'] > 90) {
                                            echo '<span class="dashicons dashicons-info"></span>';
                                            _e('קרוב לגבול', 'budgex');
                                        } elseif ($calculation['percentage'] > 70) {
                                            echo '<span class="dashicons dashicons-warning"></span>';
                                            _e('זהירות', 'budgex');
                                        } else {
                                            echo '<span class="dashicons dashicons-yes"></span>';
                                            _e('תקציב בריא', 'budgex');
                                        }
                                        ?>
                                    </div>
                                </div>
                                <div class="health-details">
                                    <div class="detail-item">
                                        <span class="label"><?php _e('ימים נותרים:', 'budgex'); ?></span>
                                        <span class="value"><?php echo date('t') - date('j'); ?></span>
                                    </div>
                                    <div class="detail-item">
                                        <span class="label"><?php _e('ממוצע יומי:', 'budgex'); ?></span>
                                        <span class="value"><?php echo number_format($calculation['remaining'] / max(date('t') - date('j'), 1)); ?></span>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Category Breakdown Card -->
                        <div class="sidebar-card">
                            <div class="card-header">
                                <h4><?php _e('פילוח קטגוריות', 'budgex'); ?></h4>
                            </div>
                            <div class="card-content">
                                <?php if (!empty($outcomes)): ?>
                                    <div class="categories-breakdown">
                                        <?php
                                        $categories = array();
                                        foreach ($outcomes as $outcome) {
                                            if (!isset($categories[$outcome->category])) {
                                                $categories[$outcome->category] = 0;
                                            }
                                            $categories[$outcome->category] += $outcome->amount;
                                        }
                                        arsort($categories);
                                        $total_spent = array_sum($categories);
                                        
                                        foreach (array_slice($categories, 0, 5) as $category => $amount):
                                            $percentage = $total_spent > 0 ? ($amount / $total_spent) * 100 : 0;
                                        ?>
                                            <div class="category-item">
                                                <div class="category-info">
                                                    <span class="category-name"><?php echo esc_html($category); ?></span>
                                                    <span class="category-amount"><?php echo number_format($amount); ?> <?php echo $budget->currency; ?></span>
                                                </div>
                                                <div class="category-bar">
                                                    <div class="category-progress" style="width: <?php echo $percentage; ?>%"></div>
                                                </div>
                                                <div class="category-percentage"><?php echo number_format($percentage, 1); ?>%</div>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                <?php else: ?>
                                    <div class="no-data">
                                        <p><?php _e('אין נתונים להצגה', 'budgex'); ?></p>
                                    </div>
                                <?php endif; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Outcomes Tab -->
            <div class="tab-content" id="tab-outcomes">
                <div class="outcomes-layout">
                    <!-- Search and Filters -->
                    <div class="outcomes-header">
                        <div class="search-filter-bar">
                            <div class="search-group">
                                <input type="text" class="search-input" placeholder="<?php _e('חפש הוצאות...', 'budgex'); ?>" id="outcomes-search">
                                <button class="search-button" id="search-outcomes">
                                    <span class="dashicons dashicons-search"></span>
                                </button>
                            </div>
                            <div class="filter-group">
                                <select class="filter-select" id="category-filter">
                                    <option value=""><?php _e('כל הקטגוריות', 'budgex'); ?></option>
                                    <?php 
                                    $unique_categories = array_unique(array_column($outcomes, 'category'));
                                    foreach ($unique_categories as $category): 
                                    ?>
                                        <option value="<?php echo esc_attr($category); ?>"><?php echo esc_html($category); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="action-group">
                                <?php if ($has_valid_data && ($user_role === 'owner' || $user_role === 'admin')): ?>
                                    <button class="action-button primary" id="add-outcome-btn">
                                        <span class="dashicons dashicons-plus-alt"></span>
                                        <?php _e('הוסף הוצאה', 'budgex'); ?>
                                    </button>
                                <?php endif; ?>
                                <button class="filter-button" id="advanced-filters">
                                    <span class="dashicons dashicons-filter"></span>
                                    <?php _e('סינון מתקדם', 'budgex'); ?>
                                </button>
                            </div>
                        </div>
                    </div>

                    <!-- Outcomes Table -->
                    <div class="outcomes-content">
                        <div class="outcomes-table-container">
                            <table class="outcomes-table">
                                <thead>
                                    <tr>
                                        <th><?php _e('תיאור', 'budgex'); ?></th>
                                        <th><?php _e('קטגוריה', 'budgex'); ?></th>
                                        <th><?php _e('סכום', 'budgex'); ?></th>
                                        <th><?php _e('תאריך', 'budgex'); ?></th>
                                        <?php if ($has_valid_data && ($user_role === 'owner' || $user_role === 'admin')): ?>
                                            <th><?php _e('פעולות', 'budgex'); ?></th>
                                        <?php endif; ?>
                                    </tr>
                                </thead>
                                <tbody id="outcomes-table-body">
                                    <?php if (!empty($outcomes)): ?>
                                        <?php foreach ($outcomes as $outcome): ?>
                                            <tr data-outcome-id="<?php echo $outcome->id; ?>">
                                                <td><?php echo esc_html($outcome->description); ?></td>
                                                <td>
                                                    <span class="category-badge"><?php echo esc_html($outcome->category); ?></span>
                                                </td>
                                                <td class="amount">
                                                    <?php echo number_format($outcome->amount); ?> <?php echo $budget->currency; ?>
                                                </td>
                                                <td class="date">
                                                    <?php echo date('d/m/Y', strtotime($outcome->date)); ?>
                                                </td>
                                                <?php if ($has_valid_data && ($user_role === 'owner' || $user_role === 'admin')): ?>
                                                    <td class="actions">
                                                        <div class="action-buttons">
                                                            <button class="action-button secondary edit-outcome" data-outcome-id="<?php echo $outcome->id; ?>">
                                                                <span class="dashicons dashicons-edit"></span>
                                                            </button>
                                                            <button class="action-button danger delete-outcome" data-outcome-id="<?php echo $outcome->id; ?>">
                                                                <span class="dashicons dashicons-trash"></span>
                                                            </button>
                                                        </div>
                                                    </td>
                                                <?php endif; ?>
                                            </tr>
                                        <?php endforeach; ?>
                                    <?php else: ?>
                                        <tr>
                                            <td colspan="<?php echo $has_valid_data && ($user_role === 'owner' || $user_role === 'admin') ? '5' : '4'; ?>" class="no-data">
                                                <div class="empty-state">
                                                    <span class="dashicons dashicons-clipboard"></span>
                                                    <p><?php _e('אין הוצאות להצגה', 'budgex'); ?></p>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Analysis Tab -->
            <div class="tab-content" id="tab-analysis">
                <div class="analysis-layout">
                    <div class="analysis-cards">
                        <div class="analysis-card">
                            <div class="card-header">
                                <h3><?php _e('ניתוח הוצאות לפי קטגוריה', 'budgex'); ?></h3>
                            </div>
                            <div class="card-content">
                                <div class="chart-container">
                                    <canvas id="category-pie-chart"></canvas>
                                </div>
                            </div>
                        </div>
                        
                        <div class="analysis-card">
                            <div class="card-header">
                                <h3><?php _e('מגמת הוצאות', 'budgex'); ?></h3>
                            </div>
                            <div class="card-content">
                                <div class="chart-container">
                                    <canvas id="trend-line-chart"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Planning Tab (Admin/Owner only) -->
            <?php if ($has_valid_data && ($user_role === 'owner' || $user_role === 'admin')): ?>
                <div class="tab-content" id="tab-planning">
                    <div class="planning-layout">
                        <div class="planning-header">
                            <h2><?php _e('תכנון תקציבי', 'budgex'); ?></h2>
                            <p><?php _e('נהל הוצאות עתידיות וחוזרות', 'budgex'); ?></p>
                        </div>
                        
                        <div class="planning-content">
                            <div class="empty-state">
                                <span class="dashicons dashicons-calendar-alt"></span>
                                <h3><?php _e('תכנון תקציבי', 'budgex'); ?></h3>
                                <p><?php _e('כלי התכנון יהיה זמין בקרוב', 'budgex'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Management Tab (Admin/Owner only) -->
            <?php if ($has_valid_data && ($user_role === 'owner' || $user_role === 'admin')): ?>
                <div class="tab-content" id="tab-management">
                    <div class="management-layout">
                        <div class="management-header">
                            <h2><?php _e('ניהול תקציב', 'budgex'); ?></h2>
                            <p><?php _e('כלים מתקדמים לניהול התקציב', 'budgex'); ?></p>
                        </div>
                        
                        <div class="management-content">
                            <div class="empty-state">
                                <span class="dashicons dashicons-admin-tools"></span>
                                <h3><?php _e('כלי ניהול', 'budgex'); ?></h3>
                                <p><?php _e('כלי הניהול המתקדמים יהיו זמינים בקרוב', 'budgex'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Settings Tab (Owner only) -->
            <?php if ($has_valid_data && $user_role === 'owner'): ?>
                <div class="tab-content" id="tab-settings">
                    <div class="settings-layout">
                        <div class="settings-header">
                            <h2><?php _e('הגדרות תקציב', 'budgex'); ?></h2>
                            <p><?php _e('נהל את הגדרות התקציב שלך', 'budgex'); ?></p>
                        </div>
                        
                        <div class="settings-content">
                            <div class="empty-state">
                                <span class="dashicons dashicons-admin-settings"></span>
                                <h3><?php _e('הגדרות', 'budgex'); ?></h3>
                                <p><?php _e('הגדרות התקציב יהיו זמינות בקרוב', 'budgex'); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <!-- Quick Add Outcome Modal -->
        <?php if ($has_valid_data && ($user_role === 'owner' || $user_role === 'admin')): ?>
            <div class="modal-overlay" id="quick-add-outcome-modal" style="display: none;">
                <div class="modal-content">
                    <div class="modal-header">
                        <h3><?php _e('הוסף הוצאה חדשה', 'budgex'); ?></h3>
                        <button class="modal-close">&times;</button>
                    </div>
                    <form id="quick-add-outcome-form">
                        <div class="form-group">
                            <label for="quick_outcome_amount"><?php _e('סכום', 'budgex'); ?></label>
                            <input type="number" id="quick_outcome_amount" name="amount" step="0.01" required>
                        </div>
                        <div class="form-row">
                            <div class="form-group">
                                <label for="quick_outcome_category"><?php _e('קטגוריה', 'budgex'); ?></label>
                                <input type="text" id="quick_outcome_category" name="category" list="categories-list" required>
                                <datalist id="categories-list">
                                    <option value="מזון">
                                    <option value="תחבורה">
                                    <option value="בילויים">
                                    <option value="ביגוד">
                                    <option value="בריאות">
                                    <option value="חשבונות">
                                    <option value="קניות">
                                    <option value="אחר">
                                </datalist>
                            </div>
                            <div class="form-group">
                                <label for="quick_outcome_date"><?php _e('תאריך', 'budgex'); ?></label>
                                <input type="date" id="quick_outcome_date" name="date" value="<?php echo date('Y-m-d'); ?>" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="quick_outcome_description"><?php _e('תיאור', 'budgex'); ?></label>
                            <textarea id="quick_outcome_description" name="description" rows="3"></textarea>
                        </div>
                        <div class="modal-actions">
                            <button type="submit" class="action-button primary">
                                <span class="dashicons dashicons-plus-alt"></span>
                                <?php _e('הוסף הוצאה', 'budgex'); ?>
                            </button>
                            <button type="button" class="action-button secondary modal-close">
                                <?php _e('ביטול', 'budgex'); ?>
                            </button>
                        </div>
                        <input type="hidden" name="budget_id" value="<?php echo esc_attr($budget->id); ?>">
                    </form>
                </div>
            </div>
        <?php endif; ?>

        <!-- Loading Overlay -->
        <div id="loading-overlay" class="loading-overlay" style="display: none;">
            <div class="loading-spinner">
                <div class="spinner"></div>
                <p><?php _e('טוען...', 'budgex'); ?></p>
            </div>
        </div>    </div>
</div>

<!-- Chart.js and dependencies -->
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.min.js"></script>

<script>
// Enhanced Budget Management JavaScript - Complete Implementation
jQuery(document).ready(function($) {
    console.log('Enhanced Budget Direct Page Loaded');
    
    // Global variables
    let currentTab = 'overview';
    let charts = {};
    let budgetData = {
        id: <?php echo json_encode($budget->id); ?>,
        name: <?php echo json_encode($budget->budget_name); ?>,
        monthlyBudget: <?php echo $budget->monthly_budget; ?>,
        currency: <?php echo json_encode($budget->currency); ?>,
        totalSpent: <?php echo $calculation['total_spent']; ?>,
        remaining: <?php echo $calculation['remaining']; ?>,
        percentage: <?php echo $calculation['percentage']; ?>,
        hasValidData: <?php echo $has_valid_data ? 'true' : 'false'; ?>,
        userRole: <?php echo json_encode($user_role); ?>
    };
    
    // Initialize enhanced budget features
    initializeEnhancedBudgetPage();
    
    function initializeEnhancedBudgetPage() {
        setupTabNavigation();
        setupDropdowns();
        setupModals();
        setupFormHandlers();
        setupCharts();
        setupQuickActions();
        setupSearchAndFilters();
        
        console.log('Enhanced Budget Page Initialized', budgetData);
    }
    
    // Tab Navigation
    function setupTabNavigation() {
        $('.tab-button').on('click', function(e) {
            e.preventDefault();
            const tabId = $(this).data('tab');
            switchTab(tabId);
        });
        
        // Handle view-all links
        $('.view-all-link').on('click', function(e) {
            e.preventDefault();
            const tabId = $(this).data('tab');
            if (tabId) {
                switchTab(tabId);
            }
        });
    }
    
    function switchTab(tabId) {
        // Update tab buttons
        $('.tab-button').removeClass('active');
        $(`.tab-button[data-tab="${tabId}"]`).addClass('active');
        
        // Update tab content
        $('.tab-content').removeClass('active');
        $(`#tab-${tabId}`).addClass('active');
        
        currentTab = tabId;
        
        // Load tab-specific content
        loadTabContent(tabId);
        
        console.log('Switched to tab:', tabId);
    }
    
    function loadTabContent(tabId) {
        switch(tabId) {
            case 'overview':
                loadOverviewContent();
                break;
            case 'outcomes':
                loadOutcomesContent();
                break;
            case 'analysis':
                loadAnalysisContent();
                break;
            case 'planning':
                loadPlanningContent();
                break;
            case 'management':
                loadManagementContent();
                break;
            case 'settings':
                loadSettingsContent();
                break;
        }
    }
    
    // Dropdown functionality
    function setupDropdowns() {
        $('.dropdown-toggle').on('click', function(e) {
            e.preventDefault();
            e.stopPropagation();
            
            const dropdown = $(this).closest('.dropdown');
            const isActive = dropdown.hasClass('active');
            
            // Close all dropdowns
            $('.dropdown').removeClass('active');
            
            // Toggle current dropdown
            if (!isActive) {
                dropdown.addClass('active');
            }
        });
        
        // Close dropdowns when clicking outside
        $(document).on('click', function() {
            $('.dropdown').removeClass('active');
        });
        
        // Prevent dropdown close when clicking inside
        $('.dropdown-menu').on('click', function(e) {
            e.stopPropagation();
        });
    }
    
    // Modal functionality
    function setupModals() {
        // Open modals
        $('#quick-add-outcome, #add-outcome-btn, #add-first-outcome').on('click', function() {
            if (budgetData.hasValidData && (budgetData.userRole === 'owner' || budgetData.userRole === 'admin')) {
                $('#quick-add-outcome-modal').show();
                $('#quick_outcome_amount').focus();
            }
        });
        
        // Close modals
        $('.modal-close').on('click', function() {
            $(this).closest('.modal-overlay').hide();
        });
        
        // Close modal on overlay click
        $('.modal-overlay').on('click', function(e) {
            if (e.target === this) {
                $(this).hide();
            }
        });
        
        // Close modal on Escape key
        $(document).on('keydown', function(e) {
            if (e.key === 'Escape') {
                $('.modal-overlay').hide();
            }
        });
    }
    
    // Form handlers
    function setupFormHandlers() {
        $('#quick-add-outcome-form').on('submit', function(e) {
            e.preventDefault();
            
            if (!budgetData.hasValidData) {
                alert('<?php _e("זהו תקציב לדוגמא. לא ניתן להוסיף הוצאות אמיתיות.", "budgex"); ?>');
                return;
            }
            
            const formData = {
                action: 'budgex_add_outcome',
                nonce: '<?php echo wp_create_nonce("budgex_nonce"); ?>',
                budget_id: budgetData.id,
                amount: $('#quick_outcome_amount').val(),
                category: $('#quick_outcome_category').val(),
                description: $('#quick_outcome_description').val(),
                date: $('#quick_outcome_date').val()
            };
            
            showLoading();
            
            $.ajax({
                url: '<?php echo admin_url("admin-ajax.php"); ?>',
                type: 'POST',
                data: formData,
                success: function(response) {
                    hideLoading();
                    
                    if (response.success) {
                        $('#quick-add-outcome-modal').hide();
                        $('#quick-add-outcome-form')[0].reset();
                        $('#quick_outcome_date').val('<?php echo date("Y-m-d"); ?>');
                        
                        // Refresh page data
                        location.reload();
                    } else {
                        alert(response.data || '<?php _e("שגיאה בהוספת ההוצאה", "budgex"); ?>');
                    }
                },
                error: function() {
                    hideLoading();
                    alert('<?php _e("שגיאת רשת. אנא נסה שוב.", "budgex"); ?>');
                }
            });
        });
    }
    
    // Charts setup
    function setupCharts() {
        if (typeof Chart === 'undefined') {
            console.log('Chart.js not loaded');
            return;
        }
        
        // Initialize charts if data is available
        if (budgetData.hasValidData || <?php echo !empty($outcomes) ? 'true' : 'false'; ?>) {
            initializeSpendingChart();
            initializeCategoryChart();
            initializeTrendChart();
        }
    }
    
    function initializeSpendingChart() {
        const ctx = document.getElementById('spending-chart');
        if (!ctx) return;
        
        const chartData = {
            labels: [<?php 
                $recent_dates = array();
                foreach (array_slice($outcomes, 0, 7) as $outcome) {
                    $recent_dates[] = "'" . date('d/m', strtotime($outcome->date)) . "'";
                }
                echo implode(', ', $recent_dates);
            ?>],
            datasets: [{
                label: '<?php _e("הוצאות יומיות", "budgex"); ?>',
                data: [<?php 
                    $daily_amounts = array();
                    foreach (array_slice($outcomes, 0, 7) as $outcome) {
                        $daily_amounts[] = $outcome->amount;
                    }
                    echo implode(', ', $daily_amounts);
                ?>],
                backgroundColor: 'rgba(79, 70, 229, 0.2)',
                borderColor: 'rgba(79, 70, 229, 1)',
                borderWidth: 2,
                fill: true
            }]
        };
        
        charts.spending = new Chart(ctx, {
            type: 'line',
            data: chartData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + ' <?php echo $budget->currency; ?>';
                            }
                        }
                    }
                }
            }
        });
    }
    
    function initializeCategoryChart() {
        const ctx = document.getElementById('category-pie-chart');
        if (!ctx) return;
        
        // Calculate category data
        const categories = {};
        <?php foreach ($outcomes as $outcome): ?>
            const category = '<?php echo esc_js($outcome->category); ?>';
            const amount = <?php echo $outcome->amount; ?>;
            categories[category] = (categories[category] || 0) + amount;
        <?php endforeach; ?>
        
        const labels = Object.keys(categories);
        const data = Object.values(categories);
        const colors = [
            '#4F46E5', '#10B981', '#F59E0B', '#EF4444', '#8B5CF6',
            '#06B6D4', '#84CC16', '#F97316', '#EC4899', '#6366F1'
        ];
        
        charts.category = new Chart(ctx, {
            type: 'doughnut',
            data: {
                labels: labels,
                datasets: [{
                    data: data,
                    backgroundColor: colors.slice(0, labels.length),
                    borderWidth: 2,
                    borderColor: '#ffffff'
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    }
                }
            }
        });
    }
    
    function initializeTrendChart() {
        const ctx = document.getElementById('trend-line-chart');
        if (!ctx) return;
        
        // Create weekly trend data
        const weeks = ['שבוע 1', 'שבוע 2', 'שבוע 3', 'שבוע 4'];
        const weeklyData = [
            <?php echo $calculation['total_spent'] * 0.2; ?>,
            <?php echo $calculation['total_spent'] * 0.3; ?>,
            <?php echo $calculation['total_spent'] * 0.25; ?>,
            <?php echo $calculation['total_spent'] * 0.25; ?>
        ];
        
        charts.trend = new Chart(ctx, {
            type: 'line',
            data: {
                labels: weeks,
                datasets: [{
                    label: '<?php _e("הוצאות שבועיות", "budgex"); ?>',
                    data: weeklyData,
                    borderColor: '#10B981',
                    backgroundColor: 'rgba(16, 185, 129, 0.1)',
                    borderWidth: 3,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return value + ' <?php echo $budget->currency; ?>';
                            }
                        }
                    }
                }
            }
        });
    }
    
    // Quick actions
    function setupQuickActions() {
        $('#refresh-data').on('click', function() {
            location.reload();
        });
        
        $('#change-chart-type').on('click', function() {
            if (charts.spending) {
                const currentType = charts.spending.config.type;
                const newType = currentType === 'line' ? 'bar' : 'line';
                
                charts.spending.config.type = newType;
                charts.spending.update();
            }
        });
        
        $('#export-excel, #print-budget').on('click', function(e) {
            e.preventDefault();
            alert('<?php _e("תכונה זו תהיה זמינה בקרוב", "budgex"); ?>');
        });
        
        // Edit/Delete outcome buttons
        $(document).on('click', '.edit-outcome', function() {
            const outcomeId = $(this).data('outcome-id');
            alert('<?php _e("עריכת הוצאה תהיה זמינה בקרוב", "budgex"); ?>');
        });
        
        $(document).on('click', '.delete-outcome', function() {
            const outcomeId = $(this).data('outcome-id');
            
            if (!budgetData.hasValidData) {
                alert('<?php _e("זהו תקציב לדוגמא. לא ניתן למחוק הוצאות.", "budgex"); ?>');
                return;
            }
            
            if (confirm('<?php _e("האם אתה בטוח שברצונך למחוק הוצאה זו?", "budgex"); ?>')) {
                deleteOutcome(outcomeId);
            }
        });
    }
    
    // Search and filters
    function setupSearchAndFilters() {
        let searchTimeout;
        
        $('#outcomes-search').on('input', function() {
            clearTimeout(searchTimeout);
            const searchTerm = $(this).val().toLowerCase();
            
            searchTimeout = setTimeout(function() {
                filterOutcomes(searchTerm);
            }, 300);
        });
        
        $('#category-filter').on('change', function() {
            const category = $(this).val();
            filterOutcomesByCategory(category);
        });
        
        $('#search-outcomes').on('click', function() {
            const searchTerm = $('#outcomes-search').val().toLowerCase();
            filterOutcomes(searchTerm);
        });
        
        $('#advanced-filters').on('click', function() {
            alert('<?php _e("סינון מתקדם יהיה זמין בקרוב", "budgex"); ?>');
        });
    }
    
    function filterOutcomes(searchTerm) {
        if (!searchTerm) {
            $('#outcomes-table-body tr').show();
            return;
        }
        
        $('#outcomes-table-body tr').each(function() {
            const row = $(this);
            const description = row.find('td:first').text().toLowerCase();
            const category = row.find('.category-badge').text().toLowerCase();
            
            if (description.includes(searchTerm) || category.includes(searchTerm)) {
                row.show();
            } else {
                row.hide();
            }
        });
    }
    
    function filterOutcomesByCategory(category) {
        if (!category) {
            $('#outcomes-table-body tr').show();
            return;
        }
        
        $('#outcomes-table-body tr').each(function() {
            const row = $(this);
            const rowCategory = row.find('.category-badge').text();
            
            if (rowCategory === category) {
                row.show();
            } else {
                row.hide();
            }
        });
    }
    
    function deleteOutcome(outcomeId) {
        showLoading();
        
        $.ajax({
            url: '<?php echo admin_url("admin-ajax.php"); ?>',
            type: 'POST',
            data: {
                action: 'budgex_delete_outcome',
                nonce: '<?php echo wp_create_nonce("budgex_nonce"); ?>',
                outcome_id: outcomeId,
                budget_id: budgetData.id
            },
            success: function(response) {
                hideLoading();
                
                if (response.success) {
                    $(`tr[data-outcome-id="${outcomeId}"]`).fadeOut(300, function() {
                        $(this).remove();
                    });
                    
                    // Refresh page data
                    setTimeout(function() {
                        location.reload();
                    }, 500);
                } else {
                    alert(response.data || '<?php _e("שגיאה במחיקת ההוצאה", "budgex"); ?>');
                }
            },
            error: function() {
                hideLoading();
                alert('<?php _e("שגיאת רשת. אנא נסה שוב.", "budgex"); ?>');
            }
        });
    }
      // Loading functions
    function showLoading() {
        $('#loading-overlay').show();
    }
    
    function hideLoading() {
        $('#loading-overlay').hide();
    }
    
    // Modal utility functions
    function showModal(modalId) {
        $('#' + modalId).show();
    }
    
    function hideModal(modalId) {
        $('#' + modalId).hide();
    }
    
    // Tab content loaders
    function loadOverviewContent() {
        // Already loaded with PHP
        console.log('Overview content loaded');
    }
    
    function loadOutcomesContent() {
        // Already loaded with PHP
        console.log('Outcomes content loaded');
    }
    
    function loadAnalysisContent() {
        // Initialize analysis charts if not already done
        if (!charts.category) {
            setTimeout(initializeCategoryChart, 100);
        }
        if (!charts.trend) {
            setTimeout(initializeTrendChart, 100);
        }
        console.log('Analysis content loaded');
    }
    
    function loadPlanningContent() {
        console.log('Planning content loaded - placeholder');
    }
    
    function loadManagementContent() {
        console.log('Management content loaded - placeholder');
    }
    
    function loadSettingsContent() {
        console.log('Settings content loaded - placeholder');
    }
    
    // Period selector functionality
    $('#period-selector').on('change', function() {
        const period = $(this).val();
        console.log('Period changed to:', period);
        // TODO: Implement period filtering
    });
    
    // Initialize enhanced budget features if external script is available
    if (typeof window.initializeEnhancedBudgetPage === 'function') {
        window.initializeEnhancedBudgetPage();
    }
    
    console.log('Enhanced Budget Management System Ready!');
});
</script>

<?php get_footer(); ?>
