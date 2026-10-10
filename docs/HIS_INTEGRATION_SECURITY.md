# TomvisHIMPro HIS Gateway Security & Isolation Specification

## Gateway Read-Only Protections
1. **Dedicated Read-Only Account:** `tomvis_his_readonly` account grants ONLY `SELECT` permissions.
2. **Explicit Table Allowlist:** Gateway queries are restricted to allowlisted tables (`pt.pt`, `pt.ptallergy`, `opd.opd`, `opd.odiag`, `opd.drug_order_opd`, `ipd.ipd`, `ipd.drug_order_ipd`, `hosdata.user`, `result_lab_opd`).
3. **No Direct Module DB Access:** Modules MUST call `PatientRepositoryInterface`, `AllergyRepositoryInterface`, etc.
4. **Mock Default in Development:** Development environment defaults to `MockHISAdapter` and `SyntheticPatientGenerator`.
