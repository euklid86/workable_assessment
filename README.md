
# 📁 Workable CSV Integration API

This project provides an API endpoint that allows you to upload CSV files containing candidate information and automatically sync them with your [Workable](https://www.workable.com/) job postings using their API.

---

## 📌 Features

- Upload CSV files to import candidates
- Automatically maps candidates to job titles via Workable shortcodes
- Optionally posts candidates to the talent pool
- Logs all actions and errors
- Simple and extensible API architecture

---

## 🚀 Technologies Used

- **PHP 7.4+**
- **cURL**
- **Workable API v3**
- **PostgreSQL/MySQL (optional, if extended to log to DB)**

---

## 🗂 Project Structure

```
project-root/
├──apache
    └── vhost.conf
├──src
   ├── api/
   │   └── crud.php         # API routing configuration
   ├── classes/
   │   └── Workable.php     # Core logic for parsing CSV and interacting with Workable API
   ├── js/
   │   └── index.js         # JavaScript code base of the project
   ├── logs/
   │   └── candidates_import.log # Log file for all operations
   └── index.php                # Main entry point (API router)
├── docker-compose.yml
├── Dockerfile
└── README.md
```

---

## 🔧 Installation & Setup

1. **Clone the Repository**

   ```bash
   git clone https://your-repo-url.git
   cd your-repo-name
   ```

2. **Configure Your Environment**

   - Open `Workable.php`
   - Update the following properties:

     ```php
     protected $authorization = 'your_workable_api_token_here';
     protected $subdomain = 'your_subdomain_here';
     ```

3. **Deploy to a Server**

   - Install Docker
   - execute ``` docker-compose up --build ```
   - visit http://localhost:8080

---

## 📤 How to Use the API

### ✅ Using the web application form upload CSV to Workable
> **Note:** In order to import the candidates to talent pool you need to check the `Import to Talent Pool` checkbox

### ✅ Expected CSV Format

| Column Name                  | Example                    |
|-----------------------------|----------------------------|
| First Name                  | Jane                       |
| Last Name                   | Doe                        |
| Position                    | Frontend Developer         |
| Address, Zip, City, Country | 123 Main St, 90210, LA, US |
| Phone                       | 555-1234                   |
| Email                       | jane@example.com           |
| Education Level             | Bachelor’s Degree          |
| Education Institution Name  | University of Example      |
| Twitter Username            | @janedoe                   |
| Linkedin Url                | https://linkedin.com/in/janedoe |

> **Note:** Make sure the `Position` matches an existing job title in your Workable account.

---

## 📋 Response Format

All responses are JSON-encoded.

#### Success

```json
{
  "success": true
}
```

#### Error

```json
{
  "success": false,
  "message": "Description of error"
}
```

---

## 📚 Logging

Logs are stored in `logs/candidates_import.log` with timestamps.

---

## 🛠 Troubleshooting

- If you see `File upload failed`, verify the `file` key exists in the request.
- If the API fails silently, check the log file for errors.
- Ensure that the Workable API token and subdomain are correctly configured.
