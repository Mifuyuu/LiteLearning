# LiteLearning ER Role Views

> This document keeps the real schema (`users` + `role`) but separates diagrams by role perspective for readability.

## 1) Core (Actual Schema)

```mermaid
erDiagram
    USERS {
        bigint id PK
        string email UK
        enum role
        string locale
        bigint coins
        bigint xp
        int level
    }

    CLASSROOMS {
        bigint id PK
        bigint teacher_id FK
        string name
        string slug UK
        string code UK
        bool is_archived
    }

    CLASSROOM_USER {
        bigint id PK
        bigint classroom_id FK
        bigint user_id FK
        enum role
        datetime joined_at
    }

    ASSIGNMENTS {
        bigint id PK
        bigint classroom_id FK
        bigint user_id FK
        enum type
        datetime due_date
        enum status
    }

    SUBMISSIONS {
        bigint id PK
        bigint assignment_id FK
        bigint user_id FK
        enum status
        int score
    }

    QUIZ_QUESTIONS {
        bigint id PK
        bigint assignment_id FK
    }

    QUIZ_RESPONSES {
        bigint id PK
        bigint quiz_question_id FK
        bigint submission_id FK
        bigint user_id FK
    }

    ANNOUNCEMENTS {
        bigint id PK
        bigint classroom_id FK
        bigint user_id FK
    }

    TOPICS {
        bigint id PK
        bigint classroom_id FK
    }

    COMMENTS {
        bigint id PK
        string commentable_type
        bigint commentable_id
        bigint user_id FK
    }

    ATTACHMENTS {
        bigint id PK
        string attachable_type
        bigint attachable_id
        bigint uploaded_by FK
    }

    ACHIEVEMENTS {
        bigint id PK
        string code UK
    }

    BADGES {
        bigint id PK
        string code UK
    }

    USER_ACHIEVEMENTS {
        bigint id PK
        bigint user_id FK
        bigint achievement_id FK
    }

    USER_BADGES {
        bigint id PK
        bigint user_id FK
        bigint badge_id FK
    }

    COIN_TRANSACTIONS {
        bigint id PK
        bigint user_id FK
        string reference_type
        bigint reference_id
    }

    CLASSROOM_SIDEBAR_PREFERENCES {
        bigint id PK
        bigint user_id FK
        bigint classroom_id FK
        bool is_pinned
        int position
    }

    USERS ||--o{ CLASSROOMS : teaches
    USERS ||--o{ CLASSROOM_USER : enrolls
    CLASSROOMS ||--o{ CLASSROOM_USER : has_members

    CLASSROOMS ||--o{ ASSIGNMENTS : has
    USERS ||--o{ ASSIGNMENTS : creates

    ASSIGNMENTS ||--o{ SUBMISSIONS : receives
    USERS ||--o{ SUBMISSIONS : submits

    ASSIGNMENTS ||--o{ QUIZ_QUESTIONS : has
    QUIZ_QUESTIONS ||--o{ QUIZ_RESPONSES : has
    SUBMISSIONS ||--o{ QUIZ_RESPONSES : includes
    USERS ||--o{ QUIZ_RESPONSES : answers

    CLASSROOMS ||--o{ ANNOUNCEMENTS : has
    USERS ||--o{ ANNOUNCEMENTS : posts

    CLASSROOMS ||--o{ TOPICS : has

    USERS ||--o{ USER_ACHIEVEMENTS : unlocks
    ACHIEVEMENTS ||--o{ USER_ACHIEVEMENTS : granted

    USERS ||--o{ USER_BADGES : earns
    BADGES ||--o{ USER_BADGES : granted

    USERS ||--o{ COIN_TRANSACTIONS : owns

    USERS ||--o{ COMMENTS : writes
    USERS ||--o{ ATTACHMENTS : uploads

    USERS ||--o{ CLASSROOM_SIDEBAR_PREFERENCES : configures
    CLASSROOMS ||--o{ CLASSROOM_SIDEBAR_PREFERENCES : pinned_for
```

## 2) Student View (Logical)

```mermaid
erDiagram
    USERS {
        bigint id PK
        enum role "student"
    }

    CLASSROOM_USER {
        bigint classroom_id FK
        bigint user_id FK
        enum role "student"
    }

    CLASSROOMS {
        bigint id PK
        string name
    }

    ASSIGNMENTS {
        bigint id PK
        bigint classroom_id FK
        datetime due_date
        enum type
    }

    SUBMISSIONS {
        bigint id PK
        bigint assignment_id FK
        bigint user_id FK
        enum status
        int score
    }

    QUIZ_RESPONSES {
        bigint id PK
        bigint submission_id FK
        bigint user_id FK
    }

    USER_ACHIEVEMENTS {
        bigint user_id FK
        bigint achievement_id FK
    }

    USER_BADGES {
        bigint user_id FK
        bigint badge_id FK
    }

    COIN_TRANSACTIONS {
        bigint user_id FK
        int amount
    }

    CLASSROOM_SIDEBAR_PREFERENCES {
        bigint user_id FK
        bigint classroom_id FK
        bool is_pinned
        int position
    }

    USERS ||--o{ CLASSROOM_USER : joins
    CLASSROOMS ||--o{ CLASSROOM_USER : membership

    CLASSROOMS ||--o{ ASSIGNMENTS : receives_work
    USERS ||--o{ SUBMISSIONS : submits
    ASSIGNMENTS ||--o{ SUBMISSIONS : has_submission
    SUBMISSIONS ||--o{ QUIZ_RESPONSES : includes

    USERS ||--o{ USER_ACHIEVEMENTS : unlocks
    USERS ||--o{ USER_BADGES : earns
    USERS ||--o{ COIN_TRANSACTIONS : gains_spends

    USERS ||--o{ CLASSROOM_SIDEBAR_PREFERENCES : pins
    CLASSROOMS ||--o{ CLASSROOM_SIDEBAR_PREFERENCES : display_order
```

## 3) Teacher View (Logical)

```mermaid
erDiagram
    USERS {
        bigint id PK
        enum role "teacher"
    }

    CLASSROOMS {
        bigint id PK
        bigint teacher_id FK
        string name
    }

    ANNOUNCEMENTS {
        bigint id PK
        bigint classroom_id FK
        bigint user_id FK
    }

    ASSIGNMENTS {
        bigint id PK
        bigint classroom_id FK
        bigint user_id FK
        enum type
        datetime due_date
    }

    SUBMISSIONS {
        bigint id PK
        bigint assignment_id FK
        bigint user_id FK
        enum status
        int score
    }

    TOPICS {
        bigint id PK
        bigint classroom_id FK
    }

    CLASSROOM_SIDEBAR_PREFERENCES {
        bigint user_id FK
        bigint classroom_id FK
        bool is_pinned
        int position
    }

    USERS ||--o{ CLASSROOMS : owns
    CLASSROOMS ||--o{ ANNOUNCEMENTS : posts_to
    USERS ||--o{ ANNOUNCEMENTS : writes

    CLASSROOMS ||--o{ ASSIGNMENTS : creates
    USERS ||--o{ ASSIGNMENTS : author
    ASSIGNMENTS ||--o{ SUBMISSIONS : grades

    CLASSROOMS ||--o{ TOPICS : organizes

    USERS ||--o{ CLASSROOM_SIDEBAR_PREFERENCES : pins
    CLASSROOMS ||--o{ CLASSROOM_SIDEBAR_PREFERENCES : ordered_in_sidebar
```

## 4) Admin View (Logical)

```mermaid
erDiagram
    USERS {
        bigint id PK
        enum role "admin"
    }

    CLASSROOMS {
        bigint id PK
        bigint teacher_id FK
    }

    ASSIGNMENTS {
        bigint id PK
        bigint classroom_id FK
    }

    SUBMISSIONS {
        bigint id PK
        bigint assignment_id FK
        bigint user_id FK
    }

    ACHIEVEMENTS {
        bigint id PK
        string code UK
    }

    BADGES {
        bigint id PK
        string code UK
    }

    USER_ACHIEVEMENTS {
        bigint user_id FK
        bigint achievement_id FK
    }

    USER_BADGES {
        bigint user_id FK
        bigint badge_id FK
    }

    COIN_TRANSACTIONS {
        bigint user_id FK
        int amount
        string reference_type
        bigint reference_id
    }

    USERS ||--o{ CLASSROOMS : manages
    CLASSROOMS ||--o{ ASSIGNMENTS : oversees
    ASSIGNMENTS ||--o{ SUBMISSIONS : monitors

    ACHIEVEMENTS ||--o{ USER_ACHIEVEMENTS : controls
    BADGES ||--o{ USER_BADGES : controls
    USERS ||--o{ COIN_TRANSACTIONS : audits
```

## Notes

- Physical DB remains single-table users with `role`.
- Role views are for communication/readability, not separate schemas.
- `comments` / `attachments` and `coin_transactions.reference_*` are polymorphic-like and intentionally simplified in role views.
