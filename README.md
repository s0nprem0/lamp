# LAMP Starter

A highly optimized, production-ready **LAMP (Linux, Apache, MariaDB, PHP)** development environment containerized with Docker. This setup minimizes startup overhead by baking core database extensions into a custom build layer and isolates services within a dedicated bridge network.

## 🏗️ Architecture

The stack consists of three core services isolated within a custom bridge network (`lamp_network`):

- **Web Server (`lamp_hub_web`)**: Apache running PHP 8.4. Automatically builds a local `Dockerfile` to compile database extensions natively.
- **Database Server (`lamp_hub_db`)**: MariaDB 10.11 utilizing a persistent named volume (`db_data`) and configured with automated healthchecks.
- **Database Management (`lamp_hub_pma`)**: phpMyAdmin providing an intuitive GUI layer, decoupled from hardcoded credentials for increased security.

---

## 🛠️ Prerequisites

Ensure you have the following installed on your host system:

- [Docker Desktop](https://www.docker.com/products/docker-desktop) or Docker Engine with the Compose plugin.
- `git` for version control.

---

## 🚀 Quick Start

### 1. Clone & Clean Up

If you have an older or conflicting Docker configuration running on these ports, stop it and clear out legacy volumes first:

```bash
docker compose down --volumes --remove-orphans

```

### 2. File Structure Setup

Ensure your local project directory matches the following layout:

```text
.
├── docker-compose.yml
├── Dockerfile
├── .gitignore
├── README.md
└── www/
    └── index.php

```

### 3. Spin Up the Stack

Run the following command to build the custom PHP image and launch the containers in the background:

```bash
docker compose up -d --build

```

- The `--build` flag ensures that the PHP database extensions are compiled and cached on the first run. Subsequent bootups will be nearly instantaneous.

---

## 🔑 Port Mapping & Access

Once the containers are running and the database reports a `(healthy)` status via `docker compose ps`, you can access the following services:

| Service                | Protocol / Port    | URL / Host Address                                                             |
| ---------------------- | ------------------ | ------------------------------------------------------------------------------ |
| **Apache Web Server**  | HTTP (Port `80`)   | [http://localhost](https://www.google.com/search?q=http://localhost)           |
| **phpMyAdmin Gateway** | HTTP (Port `8080`) | [http://localhost:8080](https://www.google.com/search?q=http://localhost:8080) |
| **MariaDB Database**   | TCP (Port `3306`)  | `127.0.0.1:3306` (Localhost access only)                                       |

### Database Authentication (via phpMyAdmin)

- **Server:** `db`
- **Username:** `user` (or your customized `${MYSQL_USER}`)
- **Password:** `userpass` (or your customized `${MYSQL_PASSWORD}`)
- _Alternatively, log in as `root` using the root password defined in your environment variables._

---

## ⚙️ Environment Configuration

### Database Connection Example (`www/index.php`)

To connect your backend PHP code to the MariaDB instance, use the internal service hostname `db` instead of `localhost`:

```php
<?php
$host = 'db'; // Docker internal network service name
$user = 'user';
$password = 'userpass';
$database = 'testdb';

$conn = new mysqli($host, $user, $password, $database);

if ($conn->connect_error) {
    die("❌ Database connection failed: " . $conn->connect_error);
}
echo "✅ Environment integrated successfully! PHP 8.4 is communicating with MariaDB.";
?>

```

---

## 🐳 Useful Commands

- **View Live Container Logs:**

```bash
docker compose logs -f

```

- **Shut Down Services Safely:**

```bash
docker compose down

```

- **Rebuild and Restart (After modifying the Dockerfile):**

```bash
docker compose up -d --build
```
