# LiteLearning Single-Viewport Dashboard Design

## Objective

Redesign the student and teacher dashboards so the primary information fits within one viewport on laptop screens from 1366x768 upward. Mobile and tablet layouts may scroll vertically to preserve readability and touch targets.

The desktop dashboard uses the approved Option A layout with the profile card on the right:

1. Left column: primary progress metric and next actions.
2. Center column: one-year activity heatmap.
3. Right column: compact profile card and quick statistics.

## Desktop Layout

The available content area sits inside the existing application sidebar. The dashboard uses three columns:

- Left: approximately 24% of dashboard width.
- Center: flexible and approximately 50% of dashboard width.
- Right: approximately 24% of dashboard width.
- Gaps: 12-16px.

The main content height is calculated from the viewport after subtracting the application padding and compact dashboard heading. At 1366x768, all primary cards must remain visible without vertical page scrolling.

The existing large hero, separate stats row, duplicate classroom list, and promotional CTA are removed from the desktop dashboard. Navigation to those destinations remains available through the sidebar and compact links inside relevant cards.

## Shared Header

The dashboard starts with a compact greeting row containing:

- User greeting and one short contextual sentence.
- Existing notification/profile controls supplied by the application layout.

The heading must not become a hero section. Its purpose is orientation, not promotion.

## Student Dashboard

### Left Column

#### Level Progress

The level card displays:

- Current level.
- XP earned within the current level.
- XP required for the next level.
- Remaining XP.
- Gradient progress bar from brand purple to magenta.

The filled progress has a subtle liquid-like animation. The animation is decorative, uses transforms rather than layout-changing properties, and is disabled when `prefers-reduced-motion: reduce` is active.

#### Next Assignments

The next-actions card displays up to four upcoming assignments:

- Assignment title.
- Classroom name.
- Compact due-date state.
- Link to the assignment.

Overdue or urgent states use semantic warning/error styling. Empty state text confirms that no urgent work exists.

### Center Column

#### One-Year Learning Activity

The heatmap follows the visual convention of GitHub contributions:

- 53 week columns covering 371 calendar days.
- 7 day rows.
- Five intensity levels, including an empty state.
- Purple color scale.
- Month and weekday labels kept sparse to avoid visual noise.
- Tooltip or accessible title describing date and activity count.

Student activity includes:

- Assignment submissions.
- Attendance check-ins.
- Comments created by the student.
- Grading/returned events received by the student.

Activity is counted per calendar day in the `Asia/Bangkok` timezone. Multiple events on one day increase intensity. The intensity thresholds are based on the maximum daily count within the displayed period, while preserving a distinct non-zero first level.

Below the heatmap, show three compact summaries:

- Total activity during the one-year period.
- Activity during the current week.
- On-time submission percentage.

The dashboard does not claim a streak until the backend can derive it reliably from the same daily activity dataset.

### Right Column

#### Compact Profile

The profile card displays:

- Avatar and active avatar frame when present.
- Name.
- Student role.
- Number of active classrooms.
- Current level badge.

The entire card links to the profile page or includes a clear profile action.

#### Quick Statistics

A two-by-two grid displays:

- Coins.
- Achievements.
- Completed submissions.
- Average score.

These values are informational and do not duplicate large standalone statistic cards elsewhere.

## Teacher Dashboard

The teacher dashboard keeps the same spatial structure so the interface remains predictable between roles.

### Left Column

#### Pending Review Progress

The primary teacher card replaces student level progress and displays:

- Total turned-in submissions waiting for review.
- Number graded during the current week.
- Weekly review completion percentage, calculated as graded this week divided by graded this week plus currently pending.
- Gradient progress bar.

When no submissions are waiting, it shows a completed state rather than a zero-value warning.

#### Review Queue

The next-actions card displays up to four assignments with pending submissions, ordered by the oldest waiting submission:

- Assignment title.
- Classroom.
- Pending count.
- Link to grading or review.

### Center Column

#### One-Year Teaching Activity

Teacher activity includes:

- Assignments, materials, announcements, and attendance sessions created.
- Submission grading events within classrooms owned by the teacher.
- Comments created by the teacher.

The heatmap uses the same purple scale and accessibility behavior as the student version. Summary values are:

- Total teaching activity during one year.
- Activity during the current week.
- Reviews completed during the current week.

The current schema records `graded_at` but not `graded_by`. Version one therefore attributes grading activity to the owner of the classroom. It must not claim exact grader attribution when a co-teacher may have performed the review. Exact attribution requires a future `graded_by` relationship or dedicated activity event.

### Right Column

The compact teacher profile displays avatar, name, teacher role, and owned active classroom count.

The quick-stat grid displays:

- Active classrooms.
- Students.
- Published assignments.
- Pending reviews.

## Data Architecture

The existing `Dashboard` Livewire component remains the page owner, but data preparation is separated into focused private methods or a dedicated dashboard analytics service if the component remains too large after implementation.

The first implementation derives activity from existing tables and timestamps. It does not add a generic activity-log table. Queries aggregate within the 53-week date window and return a normalized structure:

```php
[
    'start_date' => '2026-03-16',
    'end_date' => '2026-06-07',
    'days' => [
        ['date' => '2026-03-16', 'count' => 2, 'level' => 2],
    ],
    'total' => 46,
    'current_week' => 5,
]
```

If future requirements include audit trails or event types that cannot be reconstructed reliably, a separate activity-event table can be designed later.

## Responsive Behavior

- `>= 1280px`: three-column, single-viewport layout.
- `768px-1279px`: two-column layout; profile/stat cards move below the primary cards and scrolling is allowed.
- `< 768px`: one-column layout with heatmap horizontally scrollable inside its card; page scrolling is allowed.

Cards must not shrink text or controls below readable sizes merely to avoid scrolling on smaller devices.

## Visual System

- Page background: existing light gray application background.
- Cards: white surfaces with subtle borders and restrained shadow.
- Primary color: existing LiteLearning purple.
- Heatmap: neutral empty cells plus four purple intensity levels.
- Level/review progress: purple-to-magenta gradient.
- Corner radius and spacing follow existing dashboard/classroom patterns.
- Animations remain subtle and respect reduced-motion settings.

Implementation should use daisyUI card, badge, avatar, progress, and tooltip patterns where appropriate, customized with existing Tailwind utilities and project color tokens.

## Accessibility

- Heatmap is not color-only: each cell exposes its date and count through accessible text.
- Purple shades maintain visible separation against the white card.
- Interactive cards and links have keyboard focus states.
- Progress bars include accessible labels and current/min/max values.
- Liquid animation is decorative and disabled for reduced motion.
- Empty and loading states use text, not only icons.

## Performance

- Aggregate only the 53-week date range.
- Avoid per-day and per-card queries.
- Eager-load relationships needed for upcoming/review items.
- Build the normalized heatmap server-side; Blade only renders the prepared cells.
- Limit next-action lists to four items.

## Testing

Feature tests cover:

- Student and teacher dashboards render role-appropriate cards.
- Student activity aggregates all four approved event categories.
- Teacher activity aggregates content, review, and comment events.
- Activity excludes events outside the 53-week window.
- Activity uses Bangkok calendar dates.
- Empty dashboard states render without errors.
- Pending review progress and student level progress are calculated correctly.

Visual verification covers:

- No vertical dashboard scrolling at 1366x768 and 1440x900 desktop viewports.
- Responsive stacking at tablet and mobile widths.
- Heatmap cells and tooltips remain legible.
- Reduced-motion mode disables the liquid animation.

## Out of Scope

- A permanent generic activity-log table.
- Real-time dashboard updates.
- User-configurable widgets.
- Year-long heatmap navigation.
- Forcing a no-scroll layout on mobile or tablet.
