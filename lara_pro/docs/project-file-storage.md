# Project file storage

Project handoff uploads use the same private `local` filesystem disk as signed contract documents. The database stores paths relative to the local disk root:

```text
projects/files/{project_id}/{uuid}.{extension}
```

The local disk root is:

```text
storage/app/private
```

Because this application bootstraps Laravel from the nested `lara_pro` directory, the local path is `lara_pro/storage/app/private`, not the repository root's `storage` directory. The PHP/web-server user must have read and write access to this existing storage directory.

A database row without its physical file cannot restore the missing file contents; those files must be re-uploaded or recovered from a server backup.

Project files are private and are served through the authenticated/share-token controller routes. `storage:link` is not required for them.
