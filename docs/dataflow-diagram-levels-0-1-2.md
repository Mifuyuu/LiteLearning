# LiteLearning Data Flow Diagram

This document summarizes the system data flow for the current LiteLearning codebase.

The diagrams are based on the project routes, Livewire components, Eloquent models, and services currently implemented in this repository.

## Scope

Main sources used for this DFD:

- `routes/web.php`
- `app/Livewire/Auth/*`
- `app/Livewire/Classroom/*`
- `app/Livewire/Assignment/*`
- `app/Livewire/Material/*`
- `app/Livewire/Student/*`
- `app/Livewire/Admin/*`
- `app/Models/*`
- `app/Services/GamificationService.php`

## External Entities

- `E1 Student`
- `E2 Teacher`
- `E3 Admin`
- `E4 Object Storage (MinIO / S3)`
- `E5 Mail Service`

## Data Stores

- `D1 Users / OTP Verification`
- `D2 Classrooms / Memberships`
- `D3 Classwork Content`
  Includes `classwork_items`, `assignments`, `materials`, `announcements`, and `topics`.
- `D4 Submissions / Attendance`
- `D5 Attachment Metadata`
- `D6 Gamification / Store / Achievements`
- `D7 Bug Reports`

## Level 0

Level 0 shows LiteLearning as one system interacting with external actors and services.

```mermaid
flowchart LR
    E1["Student"]
    E2["Teacher"]
    E3["Admin"]
    E4["Object Storage\n(MinIO / S3)"]
    E5["Mail Service"]

    P0(("LiteLearning LMS"))

    E1 -->|"register, login, join class,\nview classwork, submit work,\nbuy items, report issues"| P0
    P0 -->|"dashboard, classroom content,\nfeedback, scores, coins,\nachievements, leaderboard"| E1

    E2 -->|"login, create classroom,\npublish content, grade,\nmanage roster"| P0
    P0 -->|"classroom status,\nstudent submissions,\ngradebook data"| E2

    E3 -->|"manage users, store items,\nachievements, reports"| P0
    P0 -->|"admin dashboard,\nreports, system records"| E3

    P0 -->|"upload files,\nrequest public URLs"| E4
    E4 -->|"stored object paths / URLs"| P0

    P0 -->|"send OTP email"| E5
    E5 -->|"delivery to user mailbox"| P0
```

## Level 1

Level 1 decomposes the LMS into its major business processes.

### Process List

- `P1 Authentication and Profile Management`
- `P2 Classroom and Membership Management`
- `P3 Learning Content Management`
- `P4 Submission and Assessment`
- `P5 Gamification and Rewards`
- `P6 Administration and Issue Handling`

```mermaid
flowchart TB
    E1["Student"]
    E2["Teacher"]
    E3["Admin"]
    E4["Object Storage\n(MinIO / S3)"]
    E5["Mail Service"]

    P1(("P1\nAuthentication and\nProfile Management"))
    P2(("P2\nClassroom and\nMembership Management"))
    P3(("P3\nLearning Content\nManagement"))
    P4(("P4\nSubmission and\nAssessment"))
    P5(("P5\nGamification and\nRewards"))
    P6(("P6\nAdministration and\nIssue Handling"))

    D1[("D1\nUsers / OTP")]
    D2[("D2\nClassrooms /\nMemberships")]
    D3[("D3\nClasswork Content")]
    D4[("D4\nSubmissions /\nAttendance")]
    D5[("D5\nAttachment Metadata")]
    D6[("D6\nGamification /\nStore / Achievements")]
    D7[("D7\nBug Reports")]

    E1 --> P1
    E2 --> P1
    E3 --> P1
    P1 <--> D1
    P1 --> E5

    E1 -->|"join class, view classes"| P2
    E2 -->|"create, update, archive classes"| P2
    E3 -->|"admin access to classrooms"| P2
    P2 <--> D2
    P2 <--> D1
    P2 --> P5

    E2 -->|"create assignments,\nmaterials, announcements"| P3
    E1 -->|"view content, comment"| P3
    P3 <--> D3
    P3 <--> D5
    P3 <--> E4

    E1 -->|"draft, upload, turn in work"| P4
    E2 -->|"review, score, return work"| P4
    P4 <--> D3
    P4 <--> D4
    P4 <--> D5
    P4 <--> E4
    P4 --> P5

    E1 -->|"purchase/equip items,\nview leaderboard"| P5
    E3 -->|"manage store and achievements"| P5
    P5 <--> D6
    P5 <--> D1

    E1 -->|"submit bug report"| P6
    E2 -->|"submit bug report"| P6
    E3 -->|"manage users and reports"| P6
    P6 <--> D1
    P6 <--> D2
    P6 <--> D6
    P6 <--> D7
```

### Level 1 Process Meaning

#### P1 Authentication and Profile Management

- Handles login with rate limiting.
- Handles registration with OTP verification.
- Stores user records and profile updates.
- Uploads avatar and cover images through object storage.

#### P2 Classroom and Membership Management

- Teacher creates classrooms.
- Student joins classrooms using a 6-character code.
- System checks access and role membership.
- Classroom membership events can trigger gamification rewards.

#### P3 Learning Content Management

- Teacher creates assignments, materials, and announcements.
- Classwork is organized through `classwork_items` and optional topics.
- Files are uploaded to S3/MinIO and attachment metadata is stored in the database.
- Published content is retrieved for classroom stream and work pages.

#### P4 Submission and Assessment

- Student creates drafts, uploads submission files, and turns in work.
- Teacher reviews submissions, assigns scores, and returns work.
- Submission status and feedback are stored for dashboards and grade views.

#### P5 Gamification and Rewards

- Awards coins and XP for student actions.
- Unlocks achievements.
- Supports store purchases, item equip, and leaderboard display.

#### P6 Administration and Issue Handling

- Admin manages users, store items, achievements, and bug reports.
- Students and teachers can submit bug reports.
- Admin reads system-wide operational data from core stores.

## Level 2

This Level 2 diagram decomposes `P4 Submission and Assessment`, because it is the main transactional process in the LMS.

```mermaid
flowchart LR
    E1["Student"]
    E2["Teacher"]
    E4["Object Storage\n(MinIO / S3)"]

    P41(("4.1\nValidate Access and\nLoad Assignment"))
    P42(("4.2\nSave Draft and\nUpload Evidence"))
    P43(("4.3\nTurn In or\nUnsubmit Work"))
    P44(("4.4\nRetrieve Submission\nfor Review"))
    P45(("4.5\nGrade and Return\nSubmission"))
    P46(("4.6\nReward Student\nProgress"))

    D2[("D2\nClassrooms /\nMemberships")]
    D3[("D3\nClasswork Content")]
    D4[("D4\nSubmissions")]
    D5[("D5\nAttachment Metadata")]
    D6[("D6\nGamification")]

    E1 -->|"open assignment"| P41
    P41 <--> D2
    P41 <--> D3
    P41 <--> D4
    P41 -->|"assignment details,\ncurrent submission state"| E1

    E1 -->|"draft text,\nfile upload"| P42
    P42 <--> D4
    P42 <--> D5
    P42 <--> E4
    P42 -->|"saved draft,\nfile list"| E1

    E1 -->|"turn in / unsubmit"| P43
    P43 <--> D4
    P43 -->|"updated submission status"| E1
    P43 --> P46

    E2 -->|"open grading page"| P44
    P44 <--> D3
    P44 <--> D4
    P44 -->|"submission content,\nattachments, status"| E2

    E2 -->|"score, feedback,\nreturn action"| P45
    P45 <--> D4
    P45 -->|"graded result"| E2
    P45 -->|"score, feedback,\nreturned status"| E1

    P46 <--> D6
    P46 -->|"coins, xp,\nachievements"| E1
```

### Level 2 Narrative

#### 4.1 Validate Access and Load Assignment

- Checks that the assignment belongs to the classroom.
- Checks that the user can access the classroom.
- Loads the current submission record for the student if it already exists.

#### 4.2 Save Draft and Upload Evidence

- Creates or updates the student submission record.
- Stores submission files in object storage.
- Stores file metadata in the attachments table.

#### 4.3 Turn In or Unsubmit Work

- Changes submission state between `assigned` and `turned_in`.
- Writes submission timestamps.
- Sends reward trigger only when a first valid turn-in happens.

#### 4.4 Retrieve Submission for Review

- Loads submission, assignment, and user data for the teacher.
- Supports classroom-level grading and review screens.

#### 4.5 Grade and Return Submission

- Stores score, feedback, grading timestamp, and returned status.
- Makes the assessed result visible to the student.

#### 4.6 Reward Student Progress

- Awards coins and XP for turn-in events.
- Unlocks achievements when rule thresholds are reached.

## Code Mapping

These files are the main implementation points behind the diagrams:

- Authentication: `app/Livewire/Auth/Login.php`, `app/Livewire/Auth/Register.php`
- Classroom lifecycle: `app/Livewire/Classroom/Create.php`, `app/Livewire/Classroom/JoinClassroom.php`, `app/Livewire/Classroom/Show.php`, `app/Livewire/Classroom/Work.php`
- Content lifecycle: `app/Livewire/Assignment/Create.php`, `app/Livewire/Material/Create.php`
- Submission and grading: `app/Livewire/Assignment/Show.php`, `app/Livewire/Assignment/Grade.php`
- Profile and files: `app/Livewire/Profile.php`
- Gamification: `app/Services/GamificationService.php`, `app/Livewire/Student/*`
- Administration: `app/Livewire/Admin/*`, `app/Livewire/ReportBug.php`
