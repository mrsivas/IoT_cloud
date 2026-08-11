# MCEIoT

MCEIoT is a lightweight PHP-based IoT Device Management System designed for internal company use. It allows administrators to manage users and IoT devices while devices submit data through a simple HTTP GET API.

---

# Features

- User Authentication
- Dashboard
- User Management
- Device Management
- Device Assignment
- Device Data Logging
- HTTP GET API
- Change Password
- CoreUI Responsive Interface
- MySQL Database
- PHP 8.2

---

# Technology Stack

- PHP 8.2
- MySQL
- Bootstrap 5
- CoreUI
- JavaScript
- Chart.js

---

# Folder Structure

```
mceiot/

api/
assets/
config/
database/
devices/
includes/
uploads/
users/

index.php
login.php
logout.php
change-password.php
```

---

# Installation

1. Upload the project to your web server.
2. Create a MySQL database.
3. Import:

```
database/schema.sql
```

4. Import:

```
database/sample_data.sql
```

5. Update database credentials in:

```
config/database.php
```

6. Update application settings in:

```
config/config.php
```

7. Open:

```
http://your-server/mceiot
```

---

# Default Login

Email

```
admin@mceiot.local
```

Password

```
Admin@123
```

> Replace the sample password hash with a valid hash before using the sample data.

---

# Device API

Example

```
GET /api/device.php?device=<device_uuid>&value=<value>
```

Example Response

```json
{
    "success": true
}
```

---

# Device Types

| Type | Accepted Values |
|------|-----------------|
| Binary | 0 or 1 |
| Integer | Whole numbers |
| Decimal | Numeric values |

---

# User Roles

| Role | Permissions |
|------|-------------|
| Super Admin | Full Access |
| Admin | Manage Users and Devices |
| Device Owner | View Assigned Devices |
| Read Only | View Assigned Devices |

---

# Database Tables

- users
- devices
- device_user_mapping
- device_logs

---

# Security

- Password Hashing
- Session Authentication
- Role-Based Authorization
- PDO Prepared Statements
- Input Validation
- Output Escaping

---

# Browser Support

- Google Chrome
- Microsoft Edge
- Mozilla Firefox

---

# Requirements

- PHP 8.2+
- MySQL 8.0+
- Apache Web Server
- mod_rewrite enabled

---

# License

Internal Use Only

© MCEIoT