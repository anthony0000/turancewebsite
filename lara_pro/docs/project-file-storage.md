# Project file storage

Project handoff uploads use the `project-files` filesystem disk. The database stores paths relative to that disk root:

```text
projects/files/{project_id}/{uuid}.{extension}
```

By default, the disk root is:

```text
storage/app/private
```

Because this application bootstraps Laravel from the nested `lara_pro` directory, the default local path is `lara_pro/storage/app/private`, not the repository root's `storage` directory.

## Production requirement

Set `PROJECT_FILES_ROOT` in the live `.env` file to an absolute directory that survives deployments, for example:

```dotenv
PROJECT_FILES_ROOT=/home/example/shared/turance-project-files
```

Create that directory and grant the PHP/web-server user read and write access. After changing the environment, clear or rebuild the Laravel configuration cache:

```bash
php artisan config:clear
php artisan config:cache
```

The stored database path remains relative, so existing files can be moved to the new root while preserving their `projects/files/...` paths. A database row without its physical file cannot restore the missing file contents; those files must be re-uploaded or recovered from a server backup.

Project files are private and are served through the authenticated/share-token controller routes. `storage:link` is not required for them.
