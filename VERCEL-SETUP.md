# Hosting on Vercel (shbjakupceraja.vercel.app)

The site is PHP + MySQL. On Vercel it runs through `api/index.php`
(community runtime `vercel-php`), uploads go to Vercel Blob, and the
database lives on TiDB Cloud (free, MySQL-compatible).

## 1. Create the database (once)

1. Sign up at https://tidbcloud.com and create a **Serverless** (free) cluster.
   Pick region **Frankfurt (eu-central-1)**.
2. Open the cluster → **SQL Editor** and run:
   ```sql
   CREATE DATABASE jakup_ceraja;
   USE jakup_ceraja;
   ```
   then paste and run everything in `database/install.sql`.
3. Click **Connect** and note: host, user, password.

## 2. Add the database details to Vercel

Vercel → project **shbjakupceraja** → Settings → Environment Variables.
Add these for Production and Preview:

| Name      | Value                                  |
|-----------|----------------------------------------|
| `DB_HOST` | host from TiDB (`gateway01....tidbcloud.com`) |
| `DB_USER` | user from TiDB (`xxxx.root`)           |
| `DB_PASS` | password from TiDB (mark as Sensitive) |
| `DB_NAME` | `jakup_ceraja`                         |

Already set: `DB_PORT=4000`, `DB_SSL=1`, `BLOB_READ_WRITE_TOKEN`, `BLOB_BASE_URL`.

Then redeploy (Deployments → ⋯ → Redeploy).

## 3. First login

Open https://shbjakupceraja.vercel.app/admin/ — the first visit asks you to
create the admin account. After that it shows the normal login.

## Running locally (XAMPP)

Start MySQL in XAMPP, create database `jakup_ceraja`, import
`database/install.sql`, then from the project folder run:

```
C:\xampp\php\php.exe -S localhost:8090
```

and open http://localhost:8090.
