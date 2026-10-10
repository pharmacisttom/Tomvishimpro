# TomvisHIMPro Zero-Reconfiguration Deployment Policy & Guide

## Core Principles
1. **Single Source Code Base:** Exactly the same PHP code repository is used across Development, Staging, and Production.
2. **Untracked Environment File:** `.env` is listed in `.gitignore` and NEVER committed to Git.
3. **No Hardcoded Credentials:** Zero database host IP addresses or passwords inside PHP application logic.
4. **Persistent Server Config:** Pulling new code releases (`git pull`) never touches or rewrites `.env`.
5. **Runtime Isolation Protection:** Environment Guard actively validates database connection parameters before creating PDO instances.

## Workflow Example
```
[Local Dev XAMPP] ---> git push ---> [GitHub Repository] ---> git pull ---> [Production Server]
   (.env: tomvishimpro_dev)                                                   (.env: tomvishimpro)
```
Result: Code deploys cleanly without modifying connection strings or code files!
