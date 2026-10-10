# TomvisHIMPro Healthcare Sandbox Studio Guide

## Overview
The Sandbox Studio provides a controlled, synthetic environment for developers, QA testers, and security auditors to test clinical features without accessing live hospital patient records.

## Clinical Test Scenarios
1. **Normal Patient (No Allergy):** `67000001` — Patient with no recorded drug allergies or severe renal impairment.
2. **Penicillin Allergy Patient:** `67000002` — Patient with documented severe Anaphylaxis to Penicillin G / Amoxicillin.
3. **Adverse Drug Reaction (ADR) History:** `67000003` — Patient with previous Stevens-Johnson Syndrome from Co-trimoxazole.
4. **Warfarin Anticoagulation:** `67000004` — Patient receiving Warfarin with high target INR monitoring requirement.
5. **Severe CKD (eGFR < 30):** `67000005` — Patient with Serum Creatinine 3.8 mg/dL and eGFR 18.5 requiring renal dose adjustments.

## Operating Instructions
- Open Sandbox Dashboard: `http://localhost/tomvishimpro/public/sandbox`
- Click any scenario to inspect synthetic dataset DTO structure.
- Click **Reset & Seed Dataset** to re-randomize patient records using seed `42`.
