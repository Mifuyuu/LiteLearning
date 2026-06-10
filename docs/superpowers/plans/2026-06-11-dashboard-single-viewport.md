# Single-Viewport Dashboard Implementation Plan

> **For agentic workers:** REQUIRED SUB-SKILL: Use superpowers:subagent-driven-development (recommended) or superpowers:executing-plans to implement this plan task-by-task. Steps use checkbox (`- [ ]`) syntax for tracking.

**Goal:** Replace the current scrolling dashboard with role-aware student and teacher dashboards that fit in one desktop viewport and include a one-year purple activity heatmap.

**Architecture:** Extract role-specific aggregation from the Livewire component into a focused `DashboardAnalyticsService`. Keep the Livewire component responsible for loading user/classroom context and passing normalized view data. Render one responsive Blade layout whose left and right cards vary by role while the center heatmap remains structurally shared.

**Tech Stack:** Laravel 12, Livewire 4, Eloquent, Blade, Tailwind CSS 4, daisyUI 5, PHPUnit.

---

### Task 1: Dashboard Analytics Service

**Files:**
- Create: `app/Services/DashboardAnalyticsService.php`
- Create: `tests/Feature/DashboardTest.php`

- [ ] Write failing tests for a normalized one-year student heatmap.
- [ ] Verify the tests fail because the service does not exist.
- [ ] Implement student activity aggregation from submissions and comments without double-counting attendance submissions.
- [ ] Add failing tests for teacher activity and pending-review progress.
- [ ] Implement teacher content, grading, and comment aggregation.
- [ ] Run `php artisan test tests/Feature/DashboardTest.php`.

### Task 2: Livewire Dashboard Data

**Files:**
- Modify: `app/Livewire/Dashboard.php`
- Test: `tests/Feature/DashboardTest.php`

- [ ] Add failing render tests for student and teacher role-specific dashboard content.
- [ ] Replace legacy dashboard analytics assembly with the new service.
- [ ] Build compact upcoming assignment and review queue collections.
- [ ] Run dashboard feature tests.

### Task 3: Single-Viewport Blade Layout

**Files:**
- Modify: `resources/views/livewire/dashboard.blade.php`
- Modify: `resources/css/app.css`

- [ ] Replace the hero/stats/multi-section layout with the approved three-column desktop grid.
- [ ] Implement student level card, upcoming work, profile, and quick stats.
- [ ] Implement teacher pending-review card, review queue, profile, and quick stats.
- [ ] Render the shared one-year heatmap with accessible date/count labels.
- [ ] Add the purple gradient liquid animation and reduced-motion fallback.
- [ ] Add tablet/mobile stacking and heatmap overflow behavior.
- [ ] Run `npm run build`.

### Task 4: Verification

**Files:**
- Test: `tests/Feature/DashboardTest.php`

- [ ] Run dashboard tests.
- [ ] Run the complete PHP test suite.
- [ ] Run `vendor/bin/pint --test`.
- [ ] Run `npm run build`.
- [ ] Run `git diff --check`.
- [ ] Inspect desktop at 1366x768 and 1440x900 and confirm no dashboard page scroll.
- [ ] Inspect tablet/mobile stacking and reduced-motion behavior.
