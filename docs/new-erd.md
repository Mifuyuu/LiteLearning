# LiteLearning — Entity Relationship Diagram (post-CTI refactor)

## classwork_items (Class Table Inheritance parent)

| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| type | enum('assignment','material') | discriminator |
| classroom_id | bigint FK → classrooms.id | cascade delete |
| user_id | bigint FK → users.id | cascade delete |
| topic_id | bigint FK → topics.id nullable | null on delete |
| title | varchar(255) | |
| slug | varchar(32) unique | globally unique across all classwork |
| description | longtext nullable | |
| created_at | timestamp | |
| updated_at | timestamp | |

## assignments (CTI child — assignment-specific columns only)

| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| classwork_item_id | bigint FK → classwork_items.id unique | cascade delete; 1:1 |
| max_score | int default 100 | |
| exp_reward | int unsigned default 0 | |
| coin_reward | int unsigned default 0 | |
| due_date | datetime nullable | |
| status | enum('draft','published','closed') | default draft |
| type | varchar | default 'question' |
| allow_late_submission | boolean default true | |
| created_at | timestamp | |
| updated_at | timestamp | |

## materials (CTI child — material-specific columns only)

| Column | Type | Notes |
|--------|------|-------|
| id | bigint PK | |
| classwork_item_id | bigint FK → classwork_items.id unique | cascade delete; 1:1 |
| created_at | timestamp | |
| updated_at | timestamp | |

## Relationships

```
classrooms ──< classwork_items >── topics
                    |
          ┌─────────┴──────────┐
          │                    │
       assignments          materials
          │
       submissions (assignment_id FK)
       attendance_sessions (assignment_id FK)
```

- `classwork_items.classroom_id` → `classrooms.id`
- `classwork_items.user_id` → `users.id`
- `classwork_items.topic_id` → `topics.id` (nullable)
- `assignments.classwork_item_id` → `classwork_items.id` (1:1 unique)
- `materials.classwork_item_id` → `classwork_items.id` (1:1 unique)
- `submissions.assignment_id` → `assignments.id`
- `attendance_sessions.assignment_id` → `assignments.id`
