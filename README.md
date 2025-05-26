# Flower Shop

This is a secure flower shop management web platform built using **PHP**, **JavaScript**, and **CSS**, running on a **XAMPP** stack with a **MySQL** database named `flower`.

## 💡 Features

This project implements multiple security measures to ensure safe and reliable web interactions:

- 🔒 **CSRF Protection**
- ✅ **Password Strength Validation**
- 🔐 **Hashed Passwords**
- 🧼 **Input Sanitization and Validation**
- 📌 **Prepared Statements** (to prevent SQL Injection)

## 🔑 Password Configuration

Sensitive credentials such as database passwords are stored in a **dedicated password file**.  
> ⚠️ **Make sure to keep this file secure and excluded from version control** (e.g., by adding it to `.gitignore`).

## 🤖 Google reCAPTCHA Integration

To protect against bots, Google reCAPTCHA is integrated into the registration and login forms.

- You **must update** the following:
  - `siteKey`
  - `secretKey`

These keys are located in the following files:
- `register_form.php`
- `login_form.php`

To get your reCAPTCHA keys, visit: [https://www.google.com/recaptcha](https://www.google.com/recaptcha)

## ⚙️ Technologies Used

- **PHP**
- **JavaScript**
- **CSS**
- **MySQL**
- **XAMPP** (Apache + MySQL)

## 📂 Database

Database name: **`flower`**

You can import the SQL structure into your MySQL server using phpMyAdmin or MySQL CLI.

---

Feel free to contribute or customize this project. Be sure to replace sensitive information and update security keys before deploying.
