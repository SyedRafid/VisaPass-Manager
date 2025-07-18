# VisaPass Manager

VisaPass Manager is a lightweight web-based application for managing user passport and visa records. Built with HTML, CSS, Bootstrap, PHP, and MySQL (phpMyAdmin), it helps HR/admins monitor expiring documents and update statuses efficiently.

---

## 📦 Features

### 🔍 User Side

- Search by passport number
- View limited passport & visa info: issue/expiry dates (no sensitive data)

### 🔐 Admin Side

- Secure admin login
- Change admin password

### 🛠 Dashboard

- See **Upcoming Passport Expirations** (within 1 year)
- See **Upcoming Visa Expirations** (within 3 months)
- Color-coded alerts:
  - Red = Expiring soon
  - Yellow = Renewal in progress
- Mark status: Applied for Renewal / Renewed
- Add new passport/visa info (Old entries remain in the system.)

### 👤 Profile Management

- Create profiles (user details, contact number, contract type)
- Modify profiles and control dashboard visibility (e.g., resigned user)

### 📘 Passport & Visa Management

- Add/view/manage passport info for a user
- Add/view/manage visa info linked to passport

### 📂 Contract Types

- Add or modify contract types used in profiles

### 📊 Reports

- View all active users with passport and visa images
- Full record with issue and expiry dates on one page

---

## 🧰 Tech Stack

| Area     | Technology                            |
| -------- | ------------------------------------- |
| Frontend | HTML5, CSS3, Bootstrap, JavaScript    |
| Backend  | PHP (procedural)                      |
| Database | MySQL via phpMyAdmin                  |
| Hosting  | Apache (XAMPP/LAMP or shared hosting) |

---

## 🚀 Setup Instructions

### ✅ Requirements

- PHP 7.4+
- MySQL
- Apache Server (e.g., XAMPP, WAMP, LAMP)
- phpMyAdmin (for DB import)

### 📥 1. Clone the Project

```bash
git clone https://github.com/SyedRafid/VisaPass-Manager.git
cd VisaPass-Manager
```

### 📂 2. Importing the Database using phpMyAdmin

This project uses a MySQL database named **`passport`**. To set it up locally, follow these steps:

1. **Create the Database:**

   - Open **phpMyAdmin** in your browser (e.g., http://localhost/phpmyadmin).
   - Click on the **Databases** tab.
   - In the "Create database" field, enter the name:
     ```
     passport
     ```
   - Choose the collation (e.g., `utf8mb4_general_ci`) and click **Create**.

2. **Import the SQL File:**

   - Click on the newly created `passport` database in phpMyAdmin.
   - Go to the **Import** tab.
   - Click **Choose File** and browse to the project folder's `database` directory.
   - Select the SQL file (e.g., `passport.sql`).
   - Click **Go** at the bottom to start the import.
   - Wait for the success message confirming the import.

### 🗝️ Admin Login (Default)

- **Email:** syed.shuvon@gmail.com
- **Password:** syed.shuvon@gmail.com

> ⚠️ This is the default admin account. Please log in and change the password immediately after setup for security.

---

## 🙏 Thank You!

Thank you for checking out VisaPass Manager!  
If you find this project useful, please consider giving it a ⭐️ on GitHub.

Feel free to open issues or submit pull requests — feedback and contributions are always welcome!

Happy coding — and best of luck managing passport and visa records efficiently! 🛂✨
