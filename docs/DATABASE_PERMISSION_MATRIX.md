# TomvisHIMPro Database Permission & Privilege Matrix

## Account Privileges Matrix

| User Account | Target Database | Allowed Operations | Forbidden Operations |
| :--- | :--- | :--- | :--- |
| `tomvis_dev_app` | `tomvishimpro_dev` | `CREATE`, `DROP`, `ALTER`, `SELECT`, `INSERT`, `UPDATE`, `DELETE` | Access to `tomvishimpro` (Prod) |
| `tomvis_staging_app` | `tomvishimpro_staging` | `SELECT`, `INSERT`, `UPDATE`, `DELETE`, `CREATE` | Access to `tomvishimpro` (Prod) |
| `tomvis_prod_app` | `tomvishimpro` | `SELECT`, `INSERT`, `UPDATE`, `DELETE` | `DROP TABLE`, `TRUNCATE` |
| `tomvis_his_readonly` | `himpro_his` | `SELECT` (Allowlisted tables only) | `INSERT`, `UPDATE`, `DELETE`, `DROP` |
