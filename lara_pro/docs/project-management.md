# Project management module

The project-management workspace lives under the existing authenticated admin shell at `/admin/project-management`. It extends the existing `projects` record used by staff contracts and secure project files; it does not replace that legacy workflow.

## Installation

```powershell
cd C:\wamp64\www\turancetech\lara_pro
php artisan migrate --force
php artisan db:seed --class=ProjectManagementSeeder
php artisan optimize:clear
```

Run the development seed only against a development database. It creates sample clients, admin sub-accounts, projects, board columns, tasks, memberships, and an active sprint.

To roll back the module migration before deployment of dependent data:

```powershell
php artisan migrate:rollback --step=1
```

## Permission model

The module uses the existing `projects` admin permission and `EnsureLuxuryQuoteAdminAuthenticated` middleware. Full administrators can access every project. Limited accounts can access projects where they are a member or manager; legacy projects without members remain visible to accounts that already have the existing `projects` permission. Project mutations are rechecked in the controller and API layer.

| Account | Project access | Delivery actions |
| --- | --- | --- |
| Full admin | All projects and reports | Projects, members, tasks, workflows, files, time, archive/delete |
| Project-enabled sub-account | Member/manager projects | Tasks, comments, attachments, time, board movement, permitted planning actions |
| Other sub-account | No project-management routes | Denied by existing permission middleware |

## JSON endpoints

All endpoints require the existing admin session, the `projects` permission, and CSRF protection because they are hosted inside the current web stack.

Base URL: `/admin/project-management/api`

- `GET|POST /projects`, `GET /projects/{project}`
- `GET|POST|DELETE /projects/{project}/members`, `DELETE /projects/{project}/members/{user}`
- `GET|POST /projects/{project}/columns`, `PUT|DELETE /columns/{column}`
- `POST /projects/{project}/tasks`, `PATCH /tasks/{task}`, `PATCH /tasks/{task}/move`
- `GET|POST /projects/{project}/comments`, `GET|POST /projects/{project}/tasks/{task}/comments`
- `POST /projects/{project}/milestones`, `POST /projects/{project}/sprints`
- `POST /tasks/{task}/time`, `POST /tasks/{task}/checklists`, `POST /tasks/{task}/checklist-items`
- `GET|POST /tasks/{task}/attachments`
- `GET /projects/{project}/activity`, `GET /reports`, `GET /search`
- `GET /notifications`, `PATCH /notifications/{notification}/read`

The browser board uses the web endpoint `PATCH /admin/project-management/tasks/{task}/move` for optimistic drag/drop updates and keyboard move controls.

## Data model

The module migration creates clients, project membership, board columns, labels, tasks, task collaborators/watchers/labels, milestones, sprints, checklists, dependencies, comments, private task attachments, time entries, immutable activity logs, database notifications, and saved filters. Project progress is task-derived by default and can be switched to an authorized manual percentage.

Rich text is intentionally stored as sanitized plain text in this first module slice. Files use the existing private `local` disk and authenticated download routes; no predictable public attachment URL is exposed.
