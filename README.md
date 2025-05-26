# Echoes of Today - Newspaper Management System

A comprehensive web-based newspaper management system built with PHP, MySQL, and Bootstrap. This system provides a complete solution for managing news articles, categories, advertisements, and user roles in a modern newspaper website.

## 🌟 Features

### Public Features

- **Modern News Website**: Responsive design with breaking news, featured articles, and category-based browsing
- **Advanced Search**: Full-text search with highlighting and filtering options
- **Newsletter Subscription**: Email newsletter system with welcome emails and unsubscribe functionality
- **Weather Integration**: Real-time weather information display
- **Advertisement Management**: Banner and sidebar ad placement with click tracking
- **Article Viewing**: Individual article pages with view counting and social sharing

### Admin Dashboard

- **User Management**: Create and manage admin, journalist, and editor accounts
- **Article Management**: Review, approve, reject, and edit articles
- **Category Management**: Create and organize news categories
- **Advertisement Management**: Upload and manage banner/sidebar advertisements
- **Analytics & Reports**: View website statistics and generate reports
- **Staff Management**: Monitor journalist performance and article submissions

### Journalist Portal

- **Article Creation**: Rich text editor for creating news articles
- **Media Upload**: Image upload and management system
- **Article Management**: Edit pending and rejected articles
- **Profile Management**: Update personal information and credentials
- **Dashboard**: View article statistics and submission status

## 🛠️ Technology Stack

- **Backend**: PHP 7.4+
- **Database**: MySQL 5.7+
- **Frontend**: HTML5, CSS3, JavaScript, Bootstrap 5.3
- **Icons**: Font Awesome 6.4
- **Fonts**: Google Fonts (Roboto Condensed, Open Sans)
- **Server**: Apache (XAMPP recommended)

## 📋 Prerequisites

- XAMPP (Apache + MySQL + PHP)
- Web browser (Chrome, Firefox, Safari, Edge)
- Text editor or IDE (VS Code recommended)

## 🚀 Installation

### 1. Clone or Download

```bash
git clone <repository-url>
# or download and extract ZIP file
```

### 2. Setup XAMPP

1. Install XAMPP from [https://www.apachefriends.org/](https://www.apachefriends.org/)
2. Start Apache and MySQL services
3. Place project folder in `C:\xampp\htdocs\`

### 3. Database Setup

1. Open phpMyAdmin: `http://localhost/phpmyadmin`
2. Create a new database named `echoes_today_db`
3. Import the database file: `includes/echoes_today_db.sql`
4. Update database credentials in `includes/db_connection.php` if needed

### 4. Configuration

1. Update weather API settings in `includes/weather.php`
2. Configure email settings in `public/newsletter_subscribe.php`
3. Set proper file permissions for the `uploads/` directory

### 5. Access the System

- **Public Website**: `http://localhost/Echoes-of-today-Newspaper/public/`
- **Admin Panel**: `http://localhost/Echoes-of-today-Newspaper/admin/`
- **Journalist Panel**: `http://localhost/Echoes-of-today-Newspaper/journalist/`

## 🔐 Default Login Credentials

### Admin Access

- **Username**: `admin`
- **Password**: `admin123`
- **URL**: `/admin/`

### Journalist Access

- **Username**: `ssmith`
- **Password**: `password123`
- **URL**: `/journalist/`

> **⚠️ Important**: Change default passwords after first login!

## 📁 Project Structure

```
Echoes-of-today-Newspaper/
├── admin/                  # Admin dashboard
│   ├── css/               # Admin styles
│   ├── manage_articles.php
│   ├── manage_staff.php
│   ├── advertisement.php
│   └── ...
├── journalist/            # Journalist portal
│   ├── css/               # Journalist styles
│   ├── createNews.php
│   ├── allNews.php
│   ├── profile.php
│   └── ...
├── public/                # Public website
│   ├── css/               # Public styles
│   ├── js/                # JavaScript files
│   ├── index.php          # Homepage
│   ├── article.php        # Article view
│   ├── category.php       # Category pages
│   ├── search.php         # Search functionality
│   └── ...
├── includes/              # Shared files
│   ├── db_connection.php  # Database connection
│   ├── weather.php        # Weather API
│   └── echoes_today_db.sql # Database schema
├── uploads/               # File uploads
└── README.md
```

## 🎯 Key Features Explained

### Article Management Workflow

1. **Journalist** creates article → Status: `pending_review`
2. **Admin** reviews article → Approve (`published`) or Reject (`rejected`)
3. **Published** articles appear on public website
4. **Rejected** articles can be edited by journalist

### User Roles

- **Admin**: Full system access, user management, article approval
- **Journalist**: Create and edit articles, manage profile
- **Public**: Browse articles, search, subscribe to newsletter

### Advertisement System

- Upload banner and sidebar advertisements
- Set active dates and click tracking
- Random rotation of active ads
- Click-through tracking and analytics

## 🔧 Configuration Options

### Database Configuration (`includes/db_connection.php`)

```php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "echoes_today_db";
```

### Weather API (`includes/weather.php`)

```php
// Configure your weather API key and settings
$api_key = "your_weather_api_key";
```

### Email Settings (`public/newsletter_subscribe.php`)

```php
// Configure SMTP or mail settings for newsletters
$headers[] = "From: Echoes of Today <noreply@echoesoftoday.com>";
```

## 🎨 Customization

### Styling

- Main styles: `public/css/style.css`
- Article styles: `public/css/article.css`
- Admin styles: `admin/css/admin_style.css`
- Journalist styles: `journalist/css/journalist_style.css`

### Logo and Branding

- Update logo images in respective directories
- Modify header templates in each section
- Customize color schemes in CSS files

## 📊 Features Overview

### Public Website

- ✅ Homepage with featured articles
- ✅ Category-based article browsing
- ✅ Advanced search functionality
- ✅ Individual article pages
- ✅ Newsletter subscription
- ✅ Weather widget
- ✅ Advertisement placement
- ✅ Responsive design

### Admin Dashboard

- ✅ Article approval system
- ✅ User management
- ✅ Category management
- ✅ Advertisement management
- ✅ Analytics and reporting
- ✅ Staff performance tracking

### Journalist Portal

- ✅ Article creation and editing
- ✅ Media upload system
- ✅ Personal dashboard
- ✅ Profile management
- ✅ Article status tracking

## 🐛 Troubleshooting

### Common Issues

1. **Database Connection Error**

   - Check XAMPP MySQL service is running
   - Verify database credentials in `includes/db_connection.php`
   - Ensure database `echoes_today_db` exists

2. **Image Upload Issues**

   - Check `uploads/` directory permissions
   - Verify PHP upload settings in `php.ini`
   - Ensure sufficient disk space

3. **Newsletter Not Working**

   - Configure email settings in `newsletter_subscribe.php`
   - Check PHP mail configuration
   - Verify SMTP settings if using external mail service

4. **Weather Widget Not Showing**
   - Update weather API key in `includes/weather.php`
   - Check API endpoint and credentials

## 🔒 Security Features

- Password hashing using PHP's `password_hash()`
- SQL injection prevention with prepared statements
- XSS protection with `htmlspecialchars()`
- Session management for user authentication
- File upload validation and sanitization
- CSRF protection on forms

## 📈 Performance Optimization

- Optimized database queries with proper indexing
- Image compression and resizing
- CSS and JavaScript minification ready
- Caching strategies for frequent queries
- Responsive images for different screen sizes

## 🤝 Contributing

1. Fork the repository
2. Create a feature branch
3. Make your changes
4. Test thoroughly
5. Submit a pull request

## 📞 Support

For support and questions:

- **Email**: info@echoesoftoday.com
- **Phone**: +1-555-123-4567
- **Documentation**: Check inline code comments

## 📄 License

This project is licensed under the MIT License - see the LICENSE file for details.

## 🎉 Acknowledgments

- Bootstrap team for the responsive framework
- Font Awesome for the icon library
- Google Fonts for typography
- Unsplash for placeholder images
- PHP community for excellent documentation

---

**Echoes of Today** - _The Voice of Our Times_

> Built with ❤️ for modern newspaper management
