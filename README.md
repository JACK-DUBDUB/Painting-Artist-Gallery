# Painting Artist Gallery - ACME Arts

A dynamic web application for managing and displaying an art gallery collection. Built with PHP, MySQL, and Bootstrap, this project demonstrates full CRUD operations, user authentication, role-based access control (RBAC), and image handling.

## Description

**Painting Artist Gallery** is a full-stack PHP web application that serves as a complete art gallery management system. 

It allows:
- **Public visitors** to browse paintings and artists with powerful search functionality.
- **Registered members** to access personalized features.
- **Administrators** to fully manage the collection through a dedicated admin interface.

The project showcases professional web development practices including:
- Secure PDO database interactions
- Session-based authentication
- Role-based permissions (Admin vs Member)
- Responsive Bootstrap UI
- Reusable templating system
- Image upload and management support

This was developed as a coursework project demonstrating systems analysis, database design, and web application development skills.

## ✨ Features

- **Public Gallery**
  - Browse all paintings with rich details (artist name, media type, style)
  - Search paintings by title
  - Browse and search artists
  - Pagination support

- **User Authentication System**
  - Member registration
  - Secure login / logout
  - Session management
  - Role-based access control (role `2` = Administrator)

- **Admin Panel**
  - Dedicated admin interface
  - Full management of paintings and artists

- **Painting Management (CRUD)**
  - Create new paintings (with image upload)
  - View all paintings
  - Update painting details and images
  - Delete paintings

- **Artist Management (CRUD)**
  - Create, view, update, and delete artists

- **Member Features**
  - Account settings
  - Ticket / request system (`member_tickets.php`)

- **Technical Highlights**
  - Consistent navigation header and footer across all pages
  - Dynamic search form (paintings or artists)
  - Clean separation of concerns (functions.php contains shared logic)
  - Mobile-responsive design using Bootstrap 5

## 🛠 Tech Stack

| Layer       | Technology                  |
|-------------|-----------------------------|
| **Backend** | PHP 8+ (PDO)                |
| **Frontend**| HTML5, CSS3, Bootstrap 5, JavaScript |
| **Database**| MySQL / MariaDB             |
| **Server**  | Apache (XAMPP / WAMP / Laragon recommended) |

## 📁 Project Structure

```
Painting-Artist-Gallery/
├── admin/
│   ├── admin_interface.php      # Main admin dashboard
│   └── admin_requests.php       # Admin request handling
├── artist/
│   ├── artists.php              # List all artists + search
│   ├── create_artist.php        # Create new artist
│   ├── update_artist.php        # Edit artist
│   └── delete_artist.php        # Delete artist
├── member/
│   ├── member_login.php         # Member login
│   ├── member_logout.php        # Logout handler
│   ├── member_register.php      # New member registration
│   ├── member_settings.php      # Account settings
│   └── member_tickets.php       # Member support tickets/requests
├── painting/
│   ├── paintings.php            # List all paintings + search
│   ├── create_painting.php      # Add new painting (with image)
│   ├── update_painting.php      # Edit painting + image
│   └── delete_painting.php      # Remove painting
├── functions.php                # Core shared functions (DB, auth, templates)
├── index.php                    # Homepage / entry point
└── README.md
```

> **Note:** You will likely need to create an `images/` or `uploads/` folder (with write permissions) to store uploaded painting images.

## 🚀 Installation & Setup

### Prerequisites
- PHP 7.4 or higher with PDO MySQL extension enabled
- MySQL or MariaDB
- Apache web server (XAMPP, WAMP, or Laragon highly recommended for local development on Windows)

### Setup Steps

1. **Clone or Download the Repository**
   ```bash
   git clone https://github.com/JACK-DUBDUB/Painting-Artist-Gallery.git
   ```

2. **Place in Web Server Root**
   - Copy the `Painting-Artist-Gallery` folder into your web server's document root:
     - **XAMPP**: `C:/xampp/htdocs/`
     - **WAMP**: `C:/wamp64/www/`

3. **Create the Database**
   - Open **phpMyAdmin** (`http://localhost/phpmyadmin`)
   - Create a new database called **`painting_db`**
   - Import / create the required tables. The application expects at minimum:
     - `painting` (with foreign keys to artist, media, style + image path)
     - `artist`
     - `media`
     - `style`
     - `members` (or `users`) table containing: `memid`, `email`, `fullname`, `password` (hashed), `role`
   - Sample data is recommended for testing.

4. **Verify Database Connection**
   - The connection details are defined in `functions.php` inside the `pdo_connect_mysql()` function:
     ```php
     $host = 'localhost';
     $user = 'root';
     $pass = '';           // Update if you have a password
     $db   = 'painting_db';
     ```
   - Update these values if your local MySQL configuration differs.

5. **Create Upload Directory (Important)**
   ```bash
   mkdir uploads
   chmod 755 uploads     # or give write permissions via your OS
   ```
   Update any image path references in `create_painting.php` / `update_painting.php` if you use a different folder name.

6. **Run the Application**
   - Start Apache and MySQL in your control panel
   - Visit: `http://localhost/Painting-Artist-Gallery/`

## 📖 Usage

| User Type     | Capabilities                                      |
|---------------|---------------------------------------------------|
| **Visitor**   | Browse paintings & artists, use search            |
| **Member**    | Register, login, access member settings & tickets |
| **Admin**     | Full CRUD on paintings and artists via admin panel |

**Default Login:**
- No default accounts are included. Create a member account via the registration page, then manually set `role = 2` in the database for admin access (or implement an admin creation script).

## 🖼 Screenshots

- Add later...

## 👥 Contributors

- **Jack Du Boulay**
- **Keanu Farro** 
- **Voushika**

## 📚 Acknowledgments

- Developed for university coursework in web development and systems analysis & design.
- Core structure inspired by classic PHP + MySQL CRUD tutorial patterns.
- Uses Bootstrap 5 for rapid responsive UI development.
