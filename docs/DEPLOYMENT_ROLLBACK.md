# TomvisHIMPro Rollback & Emergency Recovery Guide

## Rollback Procedure

To revert to a previous application release:

### 1. Execute CLI Rollback Command
```cmd
php scripts/tomvis rollback
```

### 2. Execute Linux Shell Script
```bash
bash scripts/rollback.sh
```

### Safety Guarantees
- The `.env` file containing production credentials is **NEVER TOUCHED OR REMOVED**.
- Persistent user uploads (`storage/uploads`) and logs (`storage/logs`) are preserved in shared storage.
- Rollback re-points the web server symlink to the previous verified release commit without altering database records.
