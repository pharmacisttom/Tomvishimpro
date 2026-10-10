# TomvisHIMPro Windows XAMPP & Central MySQL Setup Guide (Thai / English)

## คู่มือการตั้งค่าเครื่องพัฒนา Windows XAMPP เชื่อมต่อ MySQL 192.168.111.240

### 1. ทดสอบการเชื่อมต่อเครือข่ายไปยัง MySQL Server 192.168.111.240
เปิด Windows PowerShell แล้วรันคำสั่ง:
```powershell
Test-NetConnection 192.168.111.240 -Port 3306
```
ผลลัพธ์ควรแสดง: `TcpTestSucceeded : True`

### 2. กำหนดค่าไฟล์ `.env` บนเครื่อง Local
คัดลอกไฟล์ตัวอย่าง `config/environments/.env.local.example` ไปเป็น `.env`:
```powershell
copy config\environments\.env.local.example .env
```
ตรวจสอบพารามิเตอร์ภายใน `.env`:
```ini
APP_NAME=TomvisHIMPro
APP_ENV=local
APP_DEBUG=true
APP_URL=http://localhost/tomvishimpro/public

DB_CONNECTION=mysql
DB_HOST=192.168.111.240
DB_PORT=3306
DB_DATABASE=tomvishimpro_dev
DB_USERNAME=tomvis_dev_app
DB_PASSWORD=YOUR_DEVELOPMENT_PASSWORD
```

### 3. รันคำสั่งตรวจสอบและทดสอบผ่าน Tomvis CLI
```cmd
php scripts/tomvis db check
php scripts/tomvis db ping
php scripts/tomvis db info
```

### 4. รัน Database Migrations บน Development Database
```cmd
php scripts/tomvis migrate
php scripts/tomvis seed
```
