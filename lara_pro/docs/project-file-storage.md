# Project file storage

Project files and staff-contract signed documents are stored on the `public_uploads` filesystem disk. Despite its legacy name, the disk now points to private persistent storage outside the deployed repository. Database paths remain relative logical paths:

```text
projects/files/{project_id}/{uuid}.{extension}
```

By default, Laravel resolves the persistent directory two levels above the application:

```text
../../turance-persistent-uploads
```

Configure an explicit absolute path in production so the location stays stable even if the deployment layout changes:

```dotenv
PERSISTENT_UPLOADS_PATH=/home/account/turance-persistent-uploads
```

## First production rollout

If the hosting platform replaces the application directory during deployment, preserve the current files **before pushing this storage change**:

1. Create the external persistent directory.
2. Copy the complete contents of `public/uploads` into it without changing the relative paths.
3. Set `PERSISTENT_UPLOADS_PATH` to that directory in the production `.env`.
4. Deploy the code and run `php artisan config:clear`.
5. Run `php artisan uploads:migrate-persistent` and review any missing-file warnings.

If the deploy process retains ignored files, steps 1–4 may happen with this release and the migration command can perform the verified move. Files already removed by an earlier deployment must be recovered from a server backup or re-uploaded; their database rows do not contain the file bytes.

Files previously stored in `public/uploads` remain readable and are moved into persistent storage when accessed. Before the next deployment, move every known project and signed-contract upload with:

```bash
php artisan uploads:migrate-persistent
```

Back up `public/uploads` before the first migration. The command verifies every copied file before deleting its legacy copy and reports database records whose bytes are already missing.

Uploads are served through the existing authenticated or share-token routes; no public storage link is required. The persistent directory must be writable by PHP and included in server backups.
