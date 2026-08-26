# Project file storage

Project handoff uploads store their bytes in the private `project_file_contents` database table. The `project_files.path` value remains a logical path for file naming and compatibility:

```text
projects/files/{project_id}/{uuid}.{extension}
```

The local disk is retained as a fallback for older project-file records that were uploaded before database-backed content storage:

```text
storage/app/private
```

New project-file uploads do not depend on the release directory, an external storage root, or `storage:link`. The file bytes and metadata therefore remain available after the application is replaced by a Git deployment, provided the production database is persistent.

A database row without its content record cannot restore the missing file contents; those files must be re-uploaded or recovered from a server backup.

Project files are private and are served through the authenticated/share-token controller routes. Run the migration during deployment with `php artisan migrate --force` before using the upload controls.
