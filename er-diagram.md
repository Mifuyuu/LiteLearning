# LiteLearning ER Diagram

```mermaid
erDiagram
    USERS {
        bigint id PK
        string name
        string email UK
        enum role
        string locale
        bigint coins
        bigint xp
        int level
        datetime created_at
    }

    CLASSROOMS {
        bigint id PK
        bigint teacher_id FK
        string name
        string slug UK
        string code UK
        string section
        string subject
        bool is_archived
        datetime created_at
    }

    CLASSROOM_USER {
        bigint id PK
        bigint classroom_id FK
        bigint user_id FK
        enum role
        datetime joined_at
    }

    ANNOUNCEMENTS {
        bigint id PK
        bigint classroom_id FK
        bigint user_id FK
        text content
        datetime created_at
    }

    ASSIGNMENTS {
        bigint id PK
        bigint classroom_id FK
        bigint user_id FK
        string title
        int max_score
        datetime due_date
        enum status
        enum type
        string topic
        datetime created_at
    }

    SUBMISSIONS {
        bigint id PK
        bigint assignment_id FK
        bigint user_id FK
        enum status
        int score
        datetime turned_in_at
        datetime graded_at
        datetime created_at
    }

    QUIZ_QUESTIONS {
        bigint id PK
        bigint assignment_id FK
        text question
        enum type
        int points
        int order
    }

    QUIZ_RESPONSES {
        bigint id PK
        bigint quiz_question_id FK
        bigint submission_id FK
        bigint user_id FK
        text answer
        bool is_correct
        int points_earned
    }

    TOPICS {
        bigint id PK
        bigint classroom_id FK
        string name
        int order
    }

    COMMENTS {
        bigint id PK
        string commentable_type
        bigint commentable_id
        bigint user_id FK
        text content
    }

    ATTACHMENTS {
        bigint id PK
        string attachable_type
        bigint attachable_id
        bigint uploaded_by FK
        string file_name
        string file_path
    }

    ACHIEVEMENTS {
        bigint id PK
        string code UK
        string name
        int coin_reward
        int xp_reward
    }

    BADGES {
        bigint id PK
        string code UK
        string name
        string color
    }

    USER_ACHIEVEMENTS {
        bigint id PK
        bigint user_id FK
        bigint achievement_id FK
        datetime unlocked_at
    }

    USER_BADGES {
        bigint id PK
        bigint user_id FK
        bigint badge_id FK
        datetime earned_at
    }

    COIN_TRANSACTIONS {
        bigint id PK
        bigint user_id FK
        int amount
        string type
        string source
        string reference_type
        bigint reference_id
        datetime happened_at
    }

    USERS ||--o{ CLASSROOMS : teaches
    USERS ||--o{ CLASSROOM_USER : enrollments
    CLASSROOMS ||--o{ CLASSROOM_USER : members

    CLASSROOMS ||--o{ ANNOUNCEMENTS : has
    USERS ||--o{ ANNOUNCEMENTS : posts

    CLASSROOMS ||--o{ ASSIGNMENTS : has
    USERS ||--o{ ASSIGNMENTS : creates

    ASSIGNMENTS ||--o{ SUBMISSIONS : receives
    USERS ||--o{ SUBMISSIONS : submits

    ASSIGNMENTS ||--o{ QUIZ_QUESTIONS : contains
    QUIZ_QUESTIONS ||--o{ QUIZ_RESPONSES : has
    SUBMISSIONS ||--o{ QUIZ_RESPONSES : includes
    USERS ||--o{ QUIZ_RESPONSES : answers

    CLASSROOMS ||--o{ TOPICS : has

    USERS ||--o{ COMMENTS : writes
    USERS ||--o{ ATTACHMENTS : uploads

    USERS ||--o{ USER_ACHIEVEMENTS : unlocks
    ACHIEVEMENTS ||--o{ USER_ACHIEVEMENTS : awarded

    USERS ||--o{ USER_BADGES : earns
    BADGES ||--o{ USER_BADGES : awarded

    USERS ||--o{ COIN_TRANSACTIONS : owns
```

## Notes

- `comments` and `attachments` are polymorphic (`commentable_type/commentable_id`, `attachable_type/attachable_id`), so their parent can be more than one table.
- `coin_transactions.reference_type/reference_id` is a generic reference (polymorphic-like), not a fixed foreign key.
- Diagram is based on current migrations in `database/migrations`.
