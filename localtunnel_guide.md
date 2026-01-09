# 🌐 Localtunnel & App Setup Guide

This guide explains exactly how to run your TFMS application and share it via the internet using **3 separate terminals**.

> **⚠️ Crucial Rule**: Your computer **CANNOT** go to sleep (suspend) while this is running. If it sleeps, the connection breaks. We use a special command below to prevent this.

---

## 🖥️ The 3-Terminal Setup

You need to open **3 separate terminal tabs or windows** inside your project folder (`tfms`).

### 1️⃣ Terminal 1: The Backend (Laravel)
This runs the PHP logic and connects to the database.

**Command:**
```bash
php artisan serve
```
**Why?** This starts the web server.
**Note the Port:** Look at the output. It usually says `http://127.0.0.1:8000`. If it says `8001` or `8002`, **remember that number**.

---

### 2️⃣ Terminal 2: The Frontend (Vite)
This compiles your CSS and JavaScript files instantly.

**Command:**
```bash
npm run dev
```
**Why?** Without this, your design (Tailwind CSS) won't load, and the site will look broken.

---

### 3️⃣ Terminal 3: The Tunnel (Public Access)
This creates the magic link that lets others access your local site.

**Command:**
*(Replace `8000` with the actual port from Terminal 1 if it's different)*
```bash
caffeinate -i npx localtunnel --port 8000
```

**Breakdown:**
*   `caffeinate -i`: **Forces your Mac to stay awake.** Your screen can turn off, but the internet connection stays alive.
*   `npx localtunnel`: The tool that shares your site.
*   `--port 8000`: Must match the backend port from Terminal 1.

**Result:**
It will give you a URL like: `https://dark-moon-12.loca.lt`.
**Share this URL** with others.

---

## 🛑 How to Stop
To stop everything, go to each terminal and press:
**`Ctrl + C`**

---

## ❓ Troubleshooting

### "419 Page Expired" Error?
This happens if **Terminal 3** is pointing to the wrong port.
*   Check **Terminal 1**. Does it say `Server running on ...:8002`?
*   If yes, stop **Terminal 3** and run: `caffeinate -i npx localtunnel --port 8002`.

### Mac Security Warning on the Public Link
When you first click the `loca.lt` link, you might see a warning page.
*   **Fix:** Click "Click to Continue" or enter the public IP displayed on the page (it usually tells you what IP it expects).
