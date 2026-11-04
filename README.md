# 404XL TERMINAL

**Minimalist Offensive Security & Reverse Shell Utility Interface**

---

## Overview

**404XL TERMINAL** is a lightweight, high-contrast, and minimalist command execution and reverse shell control interface built into a single, self-contained PHP file. Designed specifically for penetration testers, security researchers, and cybersecurity instructors, it combines a clean terminal environment with an embedded background reverse shell capability.

---

## Key Features

* **Single-File Architecture:** Contains all terminal execution logic, nano editor emulation, and embedded reverse shell routines within one standalone script.
* **Minimalist UI:** Clean, distraction-free, high-contrast black-and-white interface using Space Grotesk and JetBrains Mono typography.
* **Integrated Nano Editor:** Built-in file editing capability (`nano filename`) allowing direct code modification and saving straight from the browser.
* **Stealth Modal Interface:** Clean header layout featuring an info trigger icon (`ⓘ`) that opens a centered, blurred-background modal for dynamic shell execution.
* **Dynamic IP & Port Configuration:** No hardcoded endpoints; configure your attacker listener IP and Port on the fly at runtime.
* **Immersive Blur Effect:** Activating options blurs the main terminal canvas to maintain focus during critical operations.

---

## How It Works

1. **Command Execution:** The core interface executes system commands securely via standard server execution functions.
2. **Interactive Nano Mode:** Type `nano <filename>` to open, edit, and save files dynamically within the web UI.
3. **Hidden Reverse Shell Trigger:**
   * Click the `ⓘ` icon in the header to launch the configuration overlay.
   * Enter your listener IP (e.g., `192.168.x.x`) and target port (e.g., `4444`).
   * Click **CONNECT** to spawn the background reverse shell process instantly.

---

## Usage Guide

### 1. Running Commands
Type any standard Linux command into the input field and press **RUN** (or hit `Enter`). The output will be displayed instantly inside the terminal window.

### 2. Editing Files
To edit or modify files on the target, use the built-in nano command syntax:
```bash
nano target_file.php

```

Make your changes in the text area and click **SAVE**.

### 3. Activating Reverse Shell

1. Click the `ⓘ` icon located in the top-right header.
2. Input your **Attacker IP** and **Port**.
3. Click **CONNECT** to establish the connection loop.

---

## Project Structure

```text
└── index.php        # All-in-one terminal interface & reverse shell logic

```

---

## Disclaimer

> *This tool is developed strictly for educational purposes, authorized security auditing, and penetration testing laboratory environments. Users assume full responsibility for any actions performed. The author accepts no liability for misuse.*

---

**THE INTELEON404**
