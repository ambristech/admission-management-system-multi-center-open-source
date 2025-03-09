# 🎓 StudentAdminSystem

![GitHub repo size](https://img.shields.io/github/repo-size/ambristech/StudentAdminSystem?style=for-the-badge)
![GitHub last commit](https://img.shields.io/github/last-commit/ambristech/StudentAdminSystem?style=for-the-badge)
![GitHub issues](https://img.shields.io/github/issues/ambristech/StudentAdminSystem?style=for-the-badge)

Welcome to **StudentAdminSystem**, a robust PHP-based web application designed to manage educational centers, students, universities, and courses efficiently! 🚀 Built with a modern stack and user-friendly interface, it caters to Super Admins, Center Admins, and Students with role-based access control.

---

## 🌟 Features

- **Role-Based Access:**
  - 👑 **Super Admin**: Manage everything - centers, universities, courses, and students.
  - 🏢 **Center Admin**: Handle students and view courses specific to their center.
  - 🎓 **Student**: View profile, admissions, and available courses.

- **Dynamic Management:**
  - 📚 Add courses with multiple universities and fees (no duplicates allowed!).
  - 🏫 Register and manage centers and students with file uploads (images, ID cards).
  - 🌐 Associate universities with courses seamlessly.

- **Security & Usability:**
  - 🔒 Secure login with password hashing.
  - 📱 Responsive design with Bootstrap 5.
  - 🛠️ Transactional database operations for data integrity.

---

## 🛠️ Tech Stack

| Technology       | Icon                                      | Purpose                  |
|------------------|-------------------------------------------|--------------------------|
| PHP              | <img src="https://img.icons8.com/color/48/000000/php.png" width="20"/> | Backend Logic            |
| MySQL            | <img src="https://img.icons8.com/color/48/000000/mysql.png" width="20"/> | Database Management      |
| Bootstrap 5      | <img src="https://img.icons8.com/color/48/000000/bootstrap.png" width="20"/> | Frontend Styling         |
| PDO              | <img src="https://img.icons8.com/ios-filled/50/000000/database.png" width="20"/> | Secure DB Connection     |
| HTML/CSS/JS      | <img src="https://img.icons8.com/color/48/000000/html-5.png" width="20"/> | Interface & Interactivity |

---

## 📋 Prerequisites

Before you begin, ensure you have:

- 🖥️ **XAMPP** or any PHP-enabled web server (PHP 7.4+).
- 🗄️ **MySQL** database server.
- 🌐 Web browser (Chrome, Firefox, etc.).
- 📦 Git installed to clone the repo.

---

## 🚀 Installation

1. **Clone the Repository:**
   ```bash
   git clone https://github.com/YOUR_USERNAME/StudentAdminSystem.git
   cd StudentAdminSystem


   📂 Project Structure

   StudentAdminSystem/
├── uploads/              # 📸 Stores images and ID cards
│   ├── centers/
│   ├── students/
│   └── id_cards/
├── nav.php               # 🧭 Navigation bar
├── db_connect.php        # 🔗 Database connection
├── login.php             # 🔐 Login page
├── dashboard.php         # 🏠 Main dashboard
├── add_center.php        # 🏢 Add center page
├── add_course.php        # 📚 Add course with universities
├── add_student.php       # 🎓 Add student page
├── center_*.php          # 🏫 Center admin pages
├── student_*.php         # 🎓 Student pages
└── database.sql          # 🗄️ Database schema

🌐 Connect
Follow us for updates:

<a href="https://github.com/ambristech"><img src="https://img.icons8.com/ios-filled/50/ffffff/github.png" width="20"/> GitHub</a>
<a href="https://twitter.com/YOUR_TWITTER"><img src="https://img.icons8.com/ios-filled/50/ffffff/twitter.png" width="20"/> Twitter</a>
<p align="center"> <strong>Made with 💙 by [Ambrish]</strong><br> <em>Happy Coding! 🚀</em> </p> ```
