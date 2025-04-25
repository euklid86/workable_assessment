# workable_assessment
Setup
install Docker and execute the bellow command
 - docker-compose up --build

Visit http://localhost:8080

# Workable Candidate Sync Tool

This PHP script allows bulk uploading of candidate data from a CSV file and posts them to your Workable job listings via the Workable API. It supports job-specific posting and optionally posting to your talent pool.

---

## 🚀 Features

- Upload and parse a CSV file of candidate data
- Automatically maps positions to job shortcodes from Workable
- Posts candidate profiles to job listings or the talent pool
- Supports education info and social profiles (Twitter, LinkedIn)
- Simple rate-limiting between API calls
- Logs all events for easy debugging

---

## 📦 Requirements

- PHP 7.4+
- cURL extension enabled
- Web server with file upload permissions
- A valid Workable API token
- A configured Workable subdomain
