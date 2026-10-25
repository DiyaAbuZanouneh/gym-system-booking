# PulseFit — Gym Management Website

A simple gym website built with **PHP (OOP) + MySQL**, with no JavaScript. It has four kinds of visitors: Guest, Member, Trainer and Admin. The theme is dark and modern, with CSS animations, and works on desktop, tablet and mobile.

## Features

| Role | What they can do |
|---|---|
| **Guest** | Browse the Home page (hero, about, features, approved reviews), register, log in |
| **Member** | Browse and filter classes, book / cancel a class (once per class), edit profile, write a gym review |
| **Trainer** | Create classes (no time overlap), delete classes, see who booked, edit profile, write a gym review |
| **Admin** | Dashboard stats, activate / deactivate members and trainers, delete classes, show / hide / delete reviews |

Business rules: no double booking, no booking when the class is full, deleting a class removes its bookings, and a review appears on Home only after admin approval.

## Requirements

- XAMPP (PHP 8+ and MySQL / MariaDB)
- Internet connection (images from Unsplash and fonts from Google Fonts)

## Setup

1. Start **Apache** and **MySQL** in XAMPP.
2. Open phpMyAdmin and import `database/database.sql`. It creates the `gym_db` database, the tables and sample data.
3. Put the project folder in `C:\xampp\htdocs\PulseFit`.
4. Open http://localhost/PulseFit

Without Apache you can run PHP's built-in server from the project folder:

```bash
php -S localhost:8000
```

Database settings (host, name, user, password) are in `classes/Database.php`. The default is `root` with an empty password.

## Sample accounts

All passwords are `123456`.

| Role | Email |
|---|---|
| Admin | admin@gym.com |
| Trainer | omar@gym.com, sara@gym.com, khaled@gym.com |
| Member | ali@gym.com, lina@gym.com, noor@gym.com |
| Deactivated member | hadi@gym.com |

The sample class dates are relative to the day you import the database, so re-import it to refresh them.

## Project structure

```
PulseFit/
├── index.php                Home page
├── login.php / register.php / logout.php
├── classes.php              Member: browse and filter classes
├── class-details.php        Member: details and booking
├── profile.php              Member and trainer profile (+ booked classes)
├── my-classes.php           Trainer: create / delete classes
├── class-bookings.php       Trainer: who booked a class
├── admin/                   Admin pages (dashboard, members, trainers, classes, reviews)
├── classes/                 OOP classes (see below)
├── includes/                bootstrap, view helpers, header / footer
├── css/                     base.css (shared theme) + one CSS file per page
└── database/database.sql    Tables + sample data
```

### OOP classes (`classes/`)

| Class | Responsibility |
|---|---|
| `Database` | Single shared PDO connection (Singleton) |
| `Model` | Abstract parent that gives models the database |
| `Auth` | Login, logout, role protection, flash messages |
| `User` | Registration, profile, activation, trainer details |
| `GymClass` | Search / filter, create, delete, time-conflict check |
| `Booking` | Book (with row lock), cancel, list of members |
| `Review` | Create, approve / hide, delete |
| `Catalog` | Class types and Home feature cards |
| `Stats` | Admin dashboard numbers |

Classes are loaded automatically by the autoloader in `includes/bootstrap.php`, and every page starts with `require 'includes/bootstrap.php';`. Pages do not contain SQL — they call the classes.

## Notes

- Forms post to the same page, so there is no `action` folder.
- Because there is no JavaScript, the mobile menu, the Member / Trainer switch and the delete confirmations are done with CSS and HTML (`checkbox`, `radio`, `<details>`).
- Users have no profile image; an avatar with the user's initials is shown instead.
