
# 🍽️ MESSMATE - Food-back Hub

![MESSMATE Banner](https://img.shields.io/badge/MESSMATE-Hostel%20Mess%20Feedback-brightgreen?style=for-the-badge)
![PHP](https://img.shields.io/badge/PHP-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-005C84?style=for-the-badge&logo=mysql&logoColor=white)
![JavaScript](https://img.shields.io/badge/JavaScript-F7DF1E?style=for-the-badge&logo=javascript&logoColor=black)

**A comprehensive mess management and feedback system designed for Simsang Hostel**

Transform your hostel mess experience with real-time feedback, menu management, and data-driven insights to improve food quality and student satisfaction.

---

## 📋 Table of Contents

- [About the Project](#about-the-project)
- [Features](#features)
- [Screenshots](#screenshots)
- [Tech Stack](#tech-stack)
- [Getting Started](#getting-started)
  - [Prerequisites](#prerequisites)
  - [Installation](#installation)
  - [Database Setup](#database-setup)
- [Usage](#usage)
- [Project Structure](#project-structure)
- [API Endpoints](#api-endpoints)
- [Roadmap](#roadmap)
- [Contributing](#contributing)
- [License](#license)
- [Contact](#contact)

---

## 🎯 About the Project

**MESSMATE (Food-back Hub)** is a modern web application designed to bridge the communication gap between hostel students and mess management. It provides a platform for students to share feedback about food quality, view weekly menus, and track the status of their submissions, while giving administrators powerful tools to manage menus and analyze feedback trends.

### Problem Statement

Hostel students often face challenges communicating food quality issues to mess management, leading to:
- Unresolved complaints
- Poor food quality continuation
- Lack of transparency in feedback handling
- No data-driven insights for improvement

### Our Solution

MESSMATE provides:
- ✅ **Real-time feedback submission** with photo uploads
- ✅ **Transparent status tracking** (Pending → Viewed → Resolved)
- ✅ **Automated email notifications** on status updates
- ✅ **Dynamic menu management** for administrators
- ✅ **Analytics dashboard** with rating trends and insights
- ✅ **Weekly menu display** with special meal highlights

---

## ✨ Features

### For Students:
- 📝 **Submit Detailed Feedback** - Rate meals with 1-5 stars, add comments, and upload photos
- 📊 **Track Feedback Status** - Monitor if your feedback is pending, viewed, or resolved
- 🍽️ **View Weekly Menu** - See daily breakfast, lunch, and dinner menus
- 🔔 **Smart Notifications** - Get today's menu banner on every login
- 😄 **Interactive Rating System** - Fun meme reactions based on your rating
- 📧 **Email Updates** - Receive notifications when feedback status changes

### For Administrators:
- 🎯 **Comprehensive Dashboard** - Overview of total feedback, pending items, and average ratings
- 📈 **Analytics & Insights** - Interactive charts showing:
  - Rating distribution (pie chart)
  - Day-wise performance (line chart)
  - Meal-time comparisons (bar chart)
  - Best and worst-rated meals
- 🍴 **Menu Management** - Update weekly menus with a user-friendly interface
- ✏️ **Status Updates** - Mark feedback as Pending, Viewed, or Resolved
- 🔐 **Secure Authentication** - Admin login with password hashing
- 📧 **Automated Emails** - Notifications sent to students on status updates

---

## 📸 Screenshots

### Student Interface
![Student Dashboard](screenshots/student-dashboard.png)
*Modern, intuitive interface for submitting feedback*

### Admin Dashboard
![Admin Dashboard](screenshots/admin-dashboard.png)
*Powerful analytics with interactive charts*

### Menu Management
![Menu Management](screenshots/menu-management.png)
*Easy-to-use menu editor for all 21 meals*

---

## 🛠️ Tech Stack

### Frontend:
- **HTML5** - Semantic markup
- **CSS3** - Modern styling with animations, gradients, glassmorphism
- **JavaScript (ES6+)** - Dynamic interactions and API calls
- **Chart.js** - Interactive data visualizations
- **Google Fonts (Poppins)** - Modern typography

### Backend:
- **PHP 8.0+** - Server-side logic
- **MySQL** - Relational database
- **PHPMailer** - Email notifications

### Libraries & Tools:
- **Chart.js v4.4.0** - Data visualization
- **PHPMailer** - SMTP email sending
- **bcrypt** - Password hashing

---

## 🚀 Getting Started

### Prerequisites

Before you begin, ensure you have the following installed:
- **XAMPP** (or WAMP/LAMP) - Apache, PHP 8.0+, MySQL
- **Web Browser** - Chrome, Firefox, Edge, or Safari
- **Text Editor** - VS Code, Sublime Text, or any code editor

### Installation

1. **Clone the repository**
git clone https://github.com/yourusername/messmate-foodback-hub.git
cd messmate-foodback-hub

text

2. **Move to XAMPP htdocs**
Windows
copy -r messmate-foodback-hub C:/xampp/htdocs/

macOS/Linux
cp -r messmate-foodback-hub /Applications/XAMPP/htdocs/

text

3. **Start XAMPP Services**
- Start **Apache**
- Start **MySQL**

### Database Setup

1. **Open phpMyAdmin**
- Navigate to `http://localhost/phpmyadmin`

2. **Create Database**
CREATE DATABASE foodhub;

text

3. **Import Tables**
- Run the following SQL commands or import `database/schema.sql`:

-- Feedback Table
CREATE TABLE feedback (
id INT AUTO_INCREMENT PRIMARY KEY,
name VARCHAR(100) NOT NULL,
student_id VARCHAR(100),
meal_day VARCHAR(20) NOT NULL,
meal_time VARCHAR(20) NOT NULL,
rating INT NOT NULL,
comment TEXT NOT NULL,
image_path VARCHAR(255),
status VARCHAR(20) DEFAULT 'Pending',
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Admin Users Table
CREATE TABLE admin_users (
id INT AUTO_INCREMENT PRIMARY KEY,
username VARCHAR(50) UNIQUE NOT NULL,
password VARCHAR(255) NOT NULL,
email VARCHAR(100),
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- Menu Items Table
CREATE TABLE menu_items (
id INT AUTO_INCREMENT PRIMARY KEY,
day_of_week VARCHAR(20) NOT NULL,
meal_time VARCHAR(20) NOT NULL,
food_items TEXT NOT NULL,
is_special TINYINT(1) DEFAULT 0,
created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
UNIQUE KEY unique_meal (day_of_week, meal_time)
);

text

4. **Create Default Admin User**
INSERT INTO admin_users (username, password, email) VALUES
('admin', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 'admin@simsanghostel.edu');
-- Default password: admin123

text

5. **Configure Database Connection**
- Open `db.php` and update credentials if needed:
$conn = new mysqli("localhost", "root", "", "foodhub");

text

6. **Configure Email (Optional)**
- Edit `email_config.php` with your SMTP settings:
'smtp_username' => 'your-email@gmail.com',
'smtp_password' => 'your-app-password',

text

### Running the Application

1. **Access the Student Portal**
http://localhost/messmate-foodback-hub/index.html

text

2. **Access the Admin Panel**
http://localhost/messmate-foodback-hub/admin/login.php

text
- **Username:** `admin`
- **Password:** `admin123`

---

## 💻 Usage

### Student Workflow:
1. Open the website
2. View today's menu in the notification banner
3. Navigate to "Rate Food" tab
4. Fill in your details (name, email, day, meal time)
5. Rate the food (1-5 stars)
6. Add comments and optional photo
7. Submit feedback
8. Track status in "Feedback Status" tab

### Admin Workflow:
1. Login to admin panel
2. View dashboard with analytics
3. Review feedback in the table
4. Update status (Pending → Viewed → Resolved)
5. Student receives email notification
6. Manage weekly menu via "Manage Menu" button
7. View trends and make data-driven improvements

---

## 📁 Project Structure

messmate-foodback-hub/
│
├── admin/
│ ├── auth_check.php # Session validation
│ ├── dashboard.php # Admin dashboard with analytics
│ ├── login.php # Admin login page
│ ├── logout.php # Logout handler
│ ├── manage_menu.php # Menu management interface
│ ├── style.css # Admin panel styles
│ └── update_status.php # Feedback status updater
│
├── uploads/ # User-uploaded images
│
├── db.php # Database connection
├── feedback.php # Feedback submission API
├── fetch_feedback.php # Fetch recent feedback
├── get_menu.php # Menu data API
├── check_feedback_status.php # Status checker for notifications
├── email_config.php # Email SMTP settings
├── email_helper.php # Email sending functions
│
├── index.html # Main student interface
├── style.css # Student interface styles
├── menu.js # Menu loading and rendering
│
└── README.md # This file

text

---

## 🔌 API Endpoints

### Feedback APIs
- `POST /feedback.php` - Submit new feedback
- `GET /fetch_feedback.php` - Get recent feedback list
- `GET /check_feedback_status.php?email={email}` - Check user's feedback status

### Menu APIs
- `GET /get_menu.php` - Fetch weekly menu from database

### Admin APIs
- `POST /admin/update_status.php` - Update feedback status

---

## 🗺️ Roadmap

### Completed ✅
- [x] Student feedback submission system
- [x] Admin authentication
- [x] Analytics dashboard with charts
- [x] Menu management system
- [x] Email notifications
- [x] Status tracking

### In Progress 🚧
- [ ] Mobile app version
- [ ] Push notifications
- [ ] PDF report generation

### Future Features 🚀
- [ ] Student login system
- [ ] Complaint categorization
- [ ] Multi-language support
- [ ] Dark mode
- [ ] Ingredient allergen alerts
- [ ] Nutrition information display
- [ ] Bulk feedback export (CSV/Excel)
- [ ] SMS notifications
- [ ] Integration with mess billing system

---

## 🤝 Contributing

Contributions are what make the open-source community amazing! Any contributions you make are **greatly appreciated**.

1. Fork the Project
2. Create your Feature Branch (`git checkout -b feature/AmazingFeature`)
3. Commit your Changes (`git commit -m 'Add some AmazingFeature'`)
4. Push to the Branch (`git push origin feature/AmazingFeature`)
5. Open a Pull Request

---

## 📜 License

Distributed under the MIT License. See `LICENSE` for more information.

---

## 📞 Contact

**Your Name** - [@yourtwitter](https://twitter.com/yourtwitter) - your.email@example.com

**Project Link:** [https://github.com/yourusername/messmate-foodback-hub](https://github.com/yourusername/messmate-foodback-hub)

---

## 🙏 Acknowledgments

- [Chart.js](https://www.chartjs.org/) - Beautiful charts
- [PHPMailer](https://github.com/PHPMailer/PHPMailer) - Email functionality
- [Google Fonts](https://fonts.google.com/) - Poppins font
- [Imgflip](https://imgflip.com/) - Meme images
- Simsang Hostel students for valuable feedback

---

## 📊 Statistics

![GitHub stars](https://img.shields.io/github/stars/yourusername/messmate-foodback-hub?style=social)
![GitHub forks](https://img.shields.io/github/forks/yourusername/messmate-foodback-hub?style=social)
![GitHub issues](https://img.shields.io/github/issues/yourusername/messmate-foodback-hub)

---

**Made with ❤️ for better campus food experience**
