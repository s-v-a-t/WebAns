# WebAns

**WebAns** is a lightweight, secure, and standalone web interface for managing and running Ansible playbooks, written in PHP. The project is designed with a focus on simplicity of deployment, security, and ease of use.

## Features

### 🚀 Execution and Management
*   **Async Execution:** Playbooks run in the background (`nohup`), allowing long-running tasks without blocking the browser interface.
*   **Smart Targeting:** The system automatically analyzes the playbook before execution:
    *   *Dynamic Target:* If the playbook specifies `hosts: "{{ host }}"`, the interface allows selecting specific hosts from the inventory.
    *   *Static Target:* If a specific group is specified (e.g., `hosts: webservers`), host selection is blocked to prevent operator errors.
*   **Pre-flight Check:** A fast parallel TCP availability check of hosts is performed before execution to exclude unreachable nodes and speed up execution.

### 🛠 Editor and Inventory
*   **Inventory Management:** Full GUI for managing the `hosts.ini` file. Supports creating/deleting/editing groups and hosts.
*   **Access Management:** Interface for adding and managing user accounts.
*   **Built-in Editor:** Uses **CodeMirror** with YAML and INI syntax highlighting. The library is embedded in the project and works completely offline.
*   **Validation:** Automatic playbook syntax check (`ansible-playbook --syntax-check`) before saving.
*   **CRUD:** Manage playbook files and `ansible.cfg` configuration directly from the browser.

### 📊 Reporting and Logs
*   **Detailed Reports:** Parsing of Ansible JSON output into a convenient readable format (Play / Task / Recap).
*   **Live Update:** View execution logs in real-time (incremental data loading).
*   **Log Rotation:** Automatic cleanup of old reports (limit is configurable) to save disk space.

### 🌍 Internationalization
*   **Multi-language:** Comes with 18 languages out of the box (English, Russian, Spanish, French, German, Chinese, Portuguese, Italian, Japanese, Korean, Dutch, Polish, Turkish, Ukrainian, Vietnamese, Indonesian, Hindi, Swedish).
*   **Auto-detection:** Automatically selects the language based on browser settings.

### ⚙️ Configuration
*   **Customizable:** Configure report retention limit and custom Ansible binary path directly from the UI.

### 🔒 Security (Security First)
*   **Protection:** Implemented protection against CSRF, XSS, Path Traversal, and Shell Injection.
*   **Headers:** Strict security headers configured: CSP, HSTS, X-Frame-Options, X-Content-Type-Options.
*   **Isolation:** The application does not require `root` or `sudo` rights for the web server. Privilege escalation on target hosts is handled by Ansible (`become`).
*   **Sessions:** Secure cookie settings are used (`HttpOnly`, `Secure`, `SameSite=Strict`).

## Requirements

*   **OS:** Linux / macOS
*   **Web Server:** Apache / Nginx / Caddy
*   **PHP:** 7.4+ (`shell_exec` enabled, `json` extension loaded)
*   **Ansible:** Installed on the system (`ansible-playbook` must be available).

## Installation

1.  **Deployment:**
    Copy the project files to the root directory of your web server.

2.  **Permissions:**
    The web server must have write permissions to the project directory (to create the `ansible` working folder).
    ```bash
    # Example for Apache/Nginx on Linux
    chown -R www-data:www-data /path/to/webans
    chmod -R 750 /path/to/webans
    ```

3.  **First Login:**
    Open the application in a browser. You will see the administrator creation form.
    *   Enter the desired login and password.
    *   The system will automatically create the necessary directories and configuration files.

## Project Structure

*   **`includes/lib/init/`** — Configuration file templates (keys, configs) used during initialization.
*   **`ansible/`** — Working directory (created automatically). Stores:
    *   `hosts.ini` — Your inventory.
    *   `ansible.cfg` — Ansible configuration.
    *   `webans.cfg` — WebAns configuration (password hash, paths).
    *   `known_hosts` — SSH known hosts file to prevent MITM attacks.
    *   `reports/` — JSON reports and execution logs.
    *   `playbooks/` — Your playbooks (`*.yml`).
*   **`etc/`** — PHP logic core (file operations, command execution, report processing).
*   **`includes/`** — Frontend resources (JS, CSS, CodeMirror libraries) and HTML templates.
*   **`index.php`** — Single entry point (Controller).

## Localization

You can manually add a translation for the interface:
1.  Navigate to the `includes/lang/` directory.
2.  Copy the `en.php` file to a new file with your language code (e.g., `es.php`).
3.  Open the file and translate the values.
4.  The new language will automatically appear in the settings.

---

# WebAns (Russian)

**WebAns** — это легковесный, безопасный и автономный веб-интерфейс для управления и запуска плейбуков Ansible, написанный на PHP. Проект разработан с упором на простоту развертывания, безопасность и удобство использования.

## Возможности

### 🚀 Запуск и Управление
*   **Асинхронный запуск:** Плейбуки выполняются в фоновом режиме (`nohup`), что позволяет запускать длительные задачи без блокировки интерфейса браузера.
*   **Умный таргетинг:** Система автоматически анализирует плейбук перед запуском:
    *   *Динамическая цель:* Если в плейбуке указано `hosts: "{{ host }}"`, интерфейс позволяет выбрать конкретные хосты из инвентаря.
    *   *Статическая цель:* Если указана конкретная группа (например, `hosts: webservers`), выбор хостов блокируется, чтобы предотвратить ошибки оператора.
*   **Pre-flight Check:** Перед запуском выполняется быстрая параллельная проверка доступности хостов по TCP, чтобы исключить недоступные узлы и ускорить выполнение.

### 🛠 Редактор и Инвентарь
*   **Управление инвентарем:** Полноценный GUI для управления файлом `hosts.ini`. Поддерживается создание/удаление/редактирование групп и хостов.
*   **Управление доступом:** Интерфейс для добавления и управления учетными записями пользователей.
*   **Встроенный редактор:** Используется **CodeMirror** с подсветкой синтаксиса YAML и INI. Библиотека встроена в проект и работает полностью офлайн.
*   **Валидация:** Автоматическая проверка синтаксиса плейбуков (`ansible-playbook --syntax-check`) перед сохранением.
*   **CRUD:** Управление файлами плейбуков и конфигурацией `ansible.cfg` прямо из браузера.

### 📊 Отчетность и Логи
*   **Детальные отчеты:** Парсинг JSON-вывода Ansible в удобный читаемый формат (Play / Task / Recap).
*   **Живое обновление:** Просмотр логов выполнения в реальном времени (инкрементальная загрузка данных).
*   **Ротация логов:** Автоматическая очистка старых отчетов (лимит настраивается) для экономии дискового пространства.

### 🌍 Локализация
*   **Мультиязычность:** Поддержка 18 языков "из коробки" (Английский, Русский, Испанский, Французский, Немецкий, Китайский, Португальский, Итальянский, Японский, Корейский, Нидерландский, Польский, Турецкий, Украинский, Вьетнамский, Индонезийский, Хинди, Шведский).
*   **Автоопределение:** Автоматический выбор языка на основе настроек браузера.

### ⚙️ Настройки
*   **Гибкость:** Настройка лимита хранения отчетов и пути к исполняемому файлу Ansible прямо из интерфейса.

### 🔒 Безопасность (Security First)
*   **Защита:** Реализована защита от CSRF, XSS, Path Traversal и Shell Injection.
*   **Headers:** Настроены строгие заголовки безопасности: CSP, HSTS, X-Frame-Options, X-Content-Type-Options.
*   **Изоляция:** Приложение не требует прав `root` или `sudo` для веб-сервера. Повышение привилегий на целевых хостах осуществляется средствами Ansible (`become`).
*   **Сессии:** Используются безопасные настройки cookie (`HttpOnly`, `Secure`, `SameSite=Strict`).

## Требования

*   **OS:** Linux / macOS
*   **Web Server:** Apache / Nginx / Caddy
*   **PHP:** 7.4+ (`shell_exec` включен, расширение `json` загружено)
*   **Ansible:** Установленный в системе (`ansible-playbook` должен быть доступен).

## Установка

1.  **Развертывание:**
    Скопируйте файлы проекта в корневую директорию вашего веб-сервера.

2.  **Права доступа:**
    Веб-сервер должен иметь права на запись в директорию проекта (для создания рабочей папки `ansible`).
    ```bash
    # Пример для Apache/Nginx на Linux
    chown -R www-data:www-data /path/to/webans
    chmod -R 750 /path/to/webans
    ```

3.  **Первый вход:**
    Откройте приложение в браузере. Вы увидите форму создания администратора.
    *   Введите желаемый логин и пароль.
    *   Система автоматически создаст необходимые директории и конфигурационные файлы.

## Структура проекта

*   **`includes/lib/init/`** — Шаблоны конфигурационных файлов (ключи, конфиги), используемые при инициализации.
*   **`ansible/`** — Рабочая директория (создается автоматически). Здесь хранятся:
    *   `hosts.ini` — Ваш инвентарь.
    *   `ansible.cfg` — Конфигурация Ansible.
    *   `webans.cfg` — Конфигурация WebAns (хеш пароля, пути).
    *   `known_hosts` — Файл известных хостов SSH для предотвращения MITM-атак.
    *   `reports/` — JSON-отчеты и логи выполнения.
    *   `playbooks/` — Ваши плейбуки (`*.yml`).
*   **`etc/`** — Ядро логики PHP (функции работы с файлами, запуска процессов, обработки отчетов).
*   **`includes/`** — Frontend-ресурсы (JS, CSS, библиотеки CodeMirror) и HTML-шаблоны.
*   **`index.php`** — Единая точка входа (Controller).

## Локализация

Вы можете самостоятельно добавить перевод интерфейса:
1.  Перейдите в директорию `includes/lang/`.
2.  Скопируйте файл `en.php` в новый файл с кодом вашего языка (например, `es.php`).
3.  Откройте файл и переведите значения.
4.  Новый язык автоматически появится в настройках.

---
*WebAns Project*
---