# Specter

**Minimalist Offensive Security & Reverse Shell Utility Interface**

---

## Overview

Exoshell is a lightweight, minimalist command execution and reverse shell control interface built into a single, self-contained PHP file.

Designed for penetration testers, security researchers, and cybersecurity instructors, it combines a terminal-style interface with integrated file editing and reverse shell capabilities.

## Features

- **Single-File Architecture:** All core functionality is contained in one PHP file.
- **Minimalist UI:** High-contrast black-and-white interface.
- **Nano Editor:** Edit and save files directly through the browser using `nano <filename>`.
- **Interactive Modal:** Configuration interface accessible through the `ⓘ` icon.
- **Dynamic Configuration:** Set listener IP and port at runtime.
- **Background Execution:** Supports background reverse shell processes.

## How It Works

### Command Execution

Execute Linux commands through the browser-based terminal and view their output directly in the interface.

### File Editing

Open files using:

```bash
nano filename.php
```

Edit the contents and click **SAVE** to apply changes.

### Reverse Shell

1. Click the `ⓘ` icon.
2. Enter the listener IP address.
3. Specify the listener port.
4. Click **CONNECT** to initiate the connection.


## Security Warning

> [!WARNING]
> Exoshell provides server-side command execution capabilities. Use it only in isolated laboratory environments or on explicitly authorized systems. Never expose the interface publicly without appropriate authentication and access controls.

## Disclaimer

Exoshell is developed strictly for educational purposes, authorized security auditing, and controlled penetration testing environments.

Users are responsible for ensuring lawful and authorized use. The author assumes no liability for misuse or damage resulting from this software.

---

<div align="left">

**_-INTELEON404_**

</div>
