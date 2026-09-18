# Project payment receipt storage

Project payment receipts use the private `public_uploads` disk. Despite its historical name, this disk is not publicly accessible and serves files only through authenticated admin routes.

In production, set `PERSISTENT_UPLOADS_PATH` to an absolute directory outside the Git checkout and deployment release directory:

```dotenv
PERSISTENT_UPLOADS_PATH=/home/account/turance-persistent-uploads
```

The database stores only receipt metadata: project and optional invoice/staff links, amount, date, type, original filename, MIME type, byte size, and the relative storage path. It never stores receipt bytes or blobs.

Back up the configured persistent directory together with the database. A Git push does not include, replace, or remove this directory.
