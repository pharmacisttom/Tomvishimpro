# TomvisHIMPro API Reference v1.0

## Base URL: `/api/v1`

### 1. System Health Check
- **Endpoint:** `GET /api/v1/health`
- **Response:**
  ```json
  {
      "status": "success",
      "message": "System operational.",
      "data": {
          "framework": "TomvisHIMPro Framework",
          "version": "1.0.0",
          "status": "healthy"
      }
  }
  ```

### 2. Search Patients
- **Endpoint:** `GET /api/v1/patients?q={query}`
- **Parameters:** `q` (HN or Name, string, min 2 chars)

### 3. Get Patient Details by HN
- **Endpoint:** `GET /api/v1/patients/{hn}`
- **Parameters:** `hn` (string, e.g. `67000001`)

### 4. Naranjo ADR Probability Calculator
- **Endpoint:** `POST /api/v1/allergy/naranjo`
- **Request Body (Form / JSON):** `q1`..`q10` scores
- **Response:** Total score and probability category (`Definite`, `Probable`, `Possible`, `Unlikely`).

### 5. Medication Reconciliation BPMH
- **Endpoint:** `GET /api/v1/medrec/bpmh/{hn}`

### 6. OWASP ZAP Config Export
- **Endpoint:** `GET /api/v1/security/zap-config`
