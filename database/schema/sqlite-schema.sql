CREATE TABLE IF NOT EXISTS "migrations"(
  "id" integer primary key autoincrement not null,
  "migration" varchar not null,
  "batch" integer not null
);
CREATE TABLE IF NOT EXISTS "users"(
  "id" integer primary key autoincrement not null,
  "name" varchar not null,
  "email" varchar not null,
  "email_verified_at" datetime,
  "password" varchar not null,
  "role" varchar check("role" in('admin', 'teacher', 'student')) not null default 'student',
  "avatar" varchar,
  "bio" text,
  "remember_token" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  "locale" varchar not null default 'en',
  "theme" varchar not null default 'system',
  "ui_scale" integer not null default '100',
  "coins" integer not null default '0',
  "xp" integer not null default '0',
  "level" integer not null default '1'
);
CREATE UNIQUE INDEX "users_email_unique" on "users"("email");
CREATE TABLE IF NOT EXISTS "password_reset_tokens"(
  "email" varchar not null,
  "token" varchar not null,
  "created_at" datetime,
  primary key("email")
);
CREATE TABLE IF NOT EXISTS "sessions"(
  "id" varchar not null,
  "user_id" integer,
  "ip_address" varchar,
  "user_agent" text,
  "payload" text not null,
  "last_activity" integer not null,
  primary key("id")
);
CREATE INDEX "sessions_user_id_index" on "sessions"("user_id");
CREATE INDEX "sessions_last_activity_index" on "sessions"("last_activity");
CREATE TABLE IF NOT EXISTS "cache"(
  "key" varchar not null,
  "value" text not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE INDEX "cache_expiration_index" on "cache"("expiration");
CREATE TABLE IF NOT EXISTS "cache_locks"(
  "key" varchar not null,
  "owner" varchar not null,
  "expiration" integer not null,
  primary key("key")
);
CREATE INDEX "cache_locks_expiration_index" on "cache_locks"("expiration");
CREATE TABLE IF NOT EXISTS "jobs"(
  "id" integer primary key autoincrement not null,
  "queue" varchar not null,
  "payload" text not null,
  "attempts" integer not null,
  "reserved_at" integer,
  "available_at" integer not null,
  "created_at" integer not null
);
CREATE INDEX "jobs_queue_index" on "jobs"("queue");
CREATE TABLE IF NOT EXISTS "job_batches"(
  "id" varchar not null,
  "name" varchar not null,
  "total_jobs" integer not null,
  "pending_jobs" integer not null,
  "failed_jobs" integer not null,
  "failed_job_ids" text not null,
  "options" text,
  "cancelled_at" integer,
  "created_at" integer not null,
  "finished_at" integer,
  primary key("id")
);
CREATE TABLE IF NOT EXISTS "failed_jobs"(
  "id" integer primary key autoincrement not null,
  "uuid" varchar not null,
  "connection" text not null,
  "queue" text not null,
  "payload" text not null,
  "exception" text not null,
  "failed_at" datetime not null default CURRENT_TIMESTAMP
);
CREATE UNIQUE INDEX "failed_jobs_uuid_unique" on "failed_jobs"("uuid");
CREATE TABLE IF NOT EXISTS "classrooms"(
  "id" integer primary key autoincrement not null,
  "teacher_id" integer not null,
  "name" varchar not null,
  "section" varchar,
  "subject" varchar,
  "description" text,
  "code" varchar not null,
  "cover_image" varchar,
  "theme_color" varchar not null default '#4F46E5',
  "is_archived" tinyint(1) not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  "slug" varchar not null default '',
  foreign key("teacher_id") references "users"("id") on delete cascade
);
CREATE UNIQUE INDEX "classrooms_code_unique" on "classrooms"("code");
CREATE TABLE IF NOT EXISTS "classroom_user"(
  "id" integer primary key autoincrement not null,
  "classroom_id" integer not null,
  "user_id" integer not null,
  "role" varchar check("role" in('student', 'co-teacher')) not null default 'student',
  "joined_at" datetime not null default CURRENT_TIMESTAMP,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("classroom_id") references "classrooms"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE UNIQUE INDEX "classroom_user_classroom_id_user_id_unique" on "classroom_user"(
  "classroom_id",
  "user_id"
);
CREATE TABLE IF NOT EXISTS "announcements"(
  "id" integer primary key autoincrement not null,
  "classroom_id" integer not null,
  "user_id" integer not null,
  "content" text not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("classroom_id") references "classrooms"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "comments"(
  "id" integer primary key autoincrement not null,
  "commentable_type" varchar not null,
  "commentable_id" integer not null,
  "user_id" integer not null,
  "content" text not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "comments_commentable_type_commentable_id_index" on "comments"(
  "commentable_type",
  "commentable_id"
);
CREATE TABLE IF NOT EXISTS "assignments"(
  "id" integer primary key autoincrement not null,
  "classroom_id" integer not null,
  "user_id" integer not null,
  "title" varchar not null,
  "description" text,
  "instructions" text,
  "max_score" integer not null default '100',
  "due_date" datetime,
  "status" varchar check("status" in('draft', 'published', 'closed')) not null default 'draft',
  "type" varchar check("type" in('assignment', 'quiz', 'material')) not null default 'assignment',
  "topic" varchar,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("classroom_id") references "classrooms"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "submissions"(
  "id" integer primary key autoincrement not null,
  "assignment_id" integer not null,
  "user_id" integer not null,
  "content" text,
  "status" varchar check("status" in('assigned', 'turned_in', 'graded', 'returned')) not null default 'assigned',
  "score" integer,
  "feedback" text,
  "turned_in_at" datetime,
  "graded_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("assignment_id") references "assignments"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE UNIQUE INDEX "submissions_assignment_id_user_id_unique" on "submissions"(
  "assignment_id",
  "user_id"
);
CREATE TABLE IF NOT EXISTS "attachments"(
  "id" integer primary key autoincrement not null,
  "attachable_type" varchar not null,
  "attachable_id" integer not null,
  "file_name" varchar not null,
  "file_path" varchar not null,
  "file_type" varchar not null,
  "file_size" integer not null,
  "uploaded_by" integer not null,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("uploaded_by") references "users"("id") on delete cascade
);
CREATE INDEX "attachments_attachable_type_attachable_id_index" on "attachments"(
  "attachable_type",
  "attachable_id"
);
CREATE TABLE IF NOT EXISTS "quiz_questions"(
  "id" integer primary key autoincrement not null,
  "assignment_id" integer not null,
  "question" text not null,
  "type" varchar check("type" in('multiple_choice', 'true_false', 'short_answer', 'essay')) not null default 'multiple_choice',
  "options" text,
  "correct_answer" text,
  "points" integer not null default '1',
  "order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("assignment_id") references "assignments"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "quiz_responses"(
  "id" integer primary key autoincrement not null,
  "quiz_question_id" integer not null,
  "submission_id" integer not null,
  "user_id" integer not null,
  "answer" text,
  "is_correct" tinyint(1),
  "points_earned" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("quiz_question_id") references "quiz_questions"("id") on delete cascade,
  foreign key("submission_id") references "submissions"("id") on delete cascade,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE TABLE IF NOT EXISTS "topics"(
  "id" integer primary key autoincrement not null,
  "classroom_id" integer not null,
  "name" varchar not null,
  "order" integer not null default '0',
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("classroom_id") references "classrooms"("id") on delete cascade
);
CREATE UNIQUE INDEX "classrooms_slug_unique" on "classrooms"("slug");
CREATE TABLE IF NOT EXISTS "achievements"(
  "id" integer primary key autoincrement not null,
  "code" varchar not null,
  "name" varchar not null,
  "description" varchar,
  "icon" varchar,
  "coin_reward" integer not null default '0',
  "xp_reward" integer not null default '0',
  "is_active" tinyint(1) not null default '1',
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "achievements_code_unique" on "achievements"("code");
CREATE TABLE IF NOT EXISTS "badges"(
  "id" integer primary key autoincrement not null,
  "code" varchar not null,
  "name" varchar not null,
  "description" varchar,
  "icon" varchar,
  "color" varchar,
  "created_at" datetime,
  "updated_at" datetime
);
CREATE UNIQUE INDEX "badges_code_unique" on "badges"("code");
CREATE TABLE IF NOT EXISTS "user_achievements"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "achievement_id" integer not null,
  "unlocked_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("achievement_id") references "achievements"("id") on delete cascade
);
CREATE UNIQUE INDEX "user_achievements_user_id_achievement_id_unique" on "user_achievements"(
  "user_id",
  "achievement_id"
);
CREATE TABLE IF NOT EXISTS "user_badges"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "badge_id" integer not null,
  "earned_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("badge_id") references "badges"("id") on delete cascade
);
CREATE UNIQUE INDEX "user_badges_user_id_badge_id_unique" on "user_badges"(
  "user_id",
  "badge_id"
);
CREATE TABLE IF NOT EXISTS "coin_transactions"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "amount" integer not null,
  "type" varchar not null default 'earn',
  "source" varchar,
  "reference_type" varchar,
  "reference_id" integer,
  "metadata" text,
  "happened_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade
);
CREATE INDEX "coin_transactions_reference_type_reference_id_index" on "coin_transactions"(
  "reference_type",
  "reference_id"
);
CREATE TABLE IF NOT EXISTS "classroom_sidebar_preferences"(
  "id" integer primary key autoincrement not null,
  "user_id" integer not null,
  "classroom_id" integer not null,
  "is_pinned" tinyint(1) not null default '0',
  "position" integer,
  "pinned_at" datetime,
  "created_at" datetime,
  "updated_at" datetime,
  foreign key("user_id") references "users"("id") on delete cascade,
  foreign key("classroom_id") references "classrooms"("id") on delete cascade
);
CREATE UNIQUE INDEX "classroom_sidebar_preferences_user_id_classroom_id_unique" on "classroom_sidebar_preferences"(
  "user_id",
  "classroom_id"
);
CREATE INDEX "classroom_sidebar_preferences_user_id_is_pinned_position_index" on "classroom_sidebar_preferences"(
  "user_id",
  "is_pinned",
  "position"
);

INSERT INTO migrations VALUES(1,'0001_01_01_000000_create_users_table',1);
INSERT INTO migrations VALUES(2,'0001_01_01_000001_create_cache_table',1);
INSERT INTO migrations VALUES(3,'0001_01_01_000002_create_jobs_table',1);
INSERT INTO migrations VALUES(4,'2025_01_01_000001_create_classrooms_table',1);
INSERT INTO migrations VALUES(5,'2025_01_01_000002_create_announcements_table',1);
INSERT INTO migrations VALUES(6,'2025_01_01_000003_create_assignments_table',1);
INSERT INTO migrations VALUES(7,'2025_01_01_000004_create_submissions_table',1);
INSERT INTO migrations VALUES(8,'2025_01_01_000005_create_attachments_table',1);
INSERT INTO migrations VALUES(9,'2025_01_01_000006_create_quizzes_table',1);
INSERT INTO migrations VALUES(10,'2025_01_01_000007_create_topics_table',1);
INSERT INTO migrations VALUES(11,'2026_02_11_153842_add_slug_to_classrooms_table',2);
INSERT INTO migrations VALUES(12,'2026_02_11_163322_add_locale_to_users_table',3);
INSERT INTO migrations VALUES(13,'2026_02_15_154355_add_theme_to_users_table',4);
INSERT INTO migrations VALUES(14,'2026_02_15_120001_add_gamification_to_users_table',5);
INSERT INTO migrations VALUES(15,'2026_02_15_120002_create_gamification_tables',5);
INSERT INTO migrations VALUES(16,'2026_02_15_210001_add_ui_scale_to_users_table',6);
INSERT INTO migrations VALUES(17,'2026_02_15_220001_create_classroom_sidebar_preferences_table',7);
INSERT INTO migrations VALUES(18,'2026_02_16_000001_create_role_profiles_and_move_student_gamification',8);
INSERT INTO migrations VALUES(19,'2026_02_17_000001_revert_role_profile_schema_to_users_gamification',9);
