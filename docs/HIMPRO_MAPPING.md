# HIMPro HIS Mapping & Gateway Specification

## HIS Table Allowlist
To guarantee maximum read-only safety, HIMPro Gateway connections are restricted to an explicit table allowlist:
- `pt.pt` (Patient Demographic Master)
- `pt.ptallergy` (Drug Allergy Records)
- `opd.opd` (OPD Visit Master)
- `opd.odiag` (OPD Diagnosis)
- `opd.drug_order_opd` (OPD Medication Orders)
- `ipd.ipd` (IPD Admission Master)
- `ipd.drug_order_ipd` (IPD Medication Orders)
- `hosdata.user` (Hospital Staff User Master)
- `result_lab_opd` (OPD Lab Results)

## Preliminary Mapping Matrix

| Entity | Canonical DTO Field | Preliminary HIMPro Table | Preliminary Column Name |
| :--- | :--- | :--- | :--- |
| **Patient** | `hn` | `pt.pt` | `hn` |
| | `fullName` | `pt.pt` | `fname`, `lname` |
| | `birthDate` | `pt.pt` | `birthday` |
| | `sex` | `pt.pt` | `sex` |
| | `cid` | `pt.pt` | `pop_id` |
| **Allergy** | `hn` | `pt.ptallergy` | `hn` |
| | `drugCode` | `pt.ptallergy` | `dcode` |
| | `drugName` | `pt.ptallergy` | `dname` |
| | `symptom` | `pt.ptallergy` | `symptom` |
| | `severity` | `pt.ptallergy` | `severity` |
| **Visit** | `vn` | `opd.opd` | `vn` |
| | `hn` | `opd.opd` | `hn` |
| | `visitDate` | `opd.opd` | `vdate` |
| | `department` | `opd.opd` | `dep` |
