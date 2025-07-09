
# 📚 University Lesson Organizer System

This is a simple PHP web system built to organize and store all lesson materials (PDFs, videos, links, etc.) for university modules. Inspired by Moodle,It works similar to Moodle in some ways, but is more focused on easy self-reference and finding specific lessons quickly.
I created it because saving files in folders by module name gets messy over time. This system helps to keep everything clean, searchable, and better organized. Now I'm sharing it so you can use it too!

## 💡 Key Features

- Store and organize lessons by module, week, and type (PDF, video, YouTube, etc.)
- Self-hosted on a local WAMP/XAMPP server
- Shareable with friends — just clone and set up
- Supports file uploads up to ~1 GB

---

## 🖥️ Requirements

- Local server (WAMP or XAMPP) with PHP and MySQL
- Browser (Chrome, Firefox, etc.)
- SQL script (provided) to auto-create the database
- Folder write permission for `uploads/`

---

## 🚀 Installation Guide

### 1. Clone or Download the Project

Place the project folder inside your WAMP/XAMPP `www/` or `htdocs/` directory.

### 2. Create `uploads` Folder

Inside the project folder, create:

```
/uploads
```

This folder is required to store uploaded lesson files.

### 3. Update PHP Settings for Large File Uploads

To support uploading large files (up to 1GB), edit your `php.ini`:

```
upload_max_filesize = 1024M
post_max_size = 1024M
max_execution_time = 300
```

> After editing, restart Apache from the WAMP/XAMPP control panel.

### 4. Import the Database

- Open **phpMyAdmin**
- Create a new database: `bitlessons`
- Import the file: `setup.sql`

This creates the required tables (`modules`, `lessons`) and structure.

### 5. Configure Database Connection

In your project’s `dbconn.php`, set the correct database name:

```php
$con = new mysqli("localhost", "root", "", "bitlessons");
```

---



## 👨‍🎓 For Friends and Classmates

You can:
- Clone this project
- Run the SQL script
- Add your own materials
- Use it to organize and revise your lessons easily

---

## 👤 Author

Made by: **[Sameera Perera]**  
University: **[University of Moratuwa]**


Feel free to share or improve it for personal academic use. 
