# Gmail Setup Guide (Universal) 📧

This method works for **Local Testing** 

## Step 1: Get Your Secret App Password

**You CANNOT use your normal Gmail password.** You must generate a special code.

1.  Go to **[Google Account Security](https://myaccount.google.com/security)**.
2.  Enable **2-Step Verification** (if not on).
3.  Search for **"App passwords"**.
4.  Create a new one:
    *   **App name**: "TFMS"
    *   **Result**: Copy the 16-character code (e.g., `abcd efgh ijkl mnop`).

## Step 2: Configure Locally (Check if it works)

1.  Open the file named `.env` in your project folder (VS Code).
2.  Replace the text with your details:
    ```ini
    EMAIL_HOST_USER=your_real_email@gmail.com
    EMAIL_HOST_PASSWORD=the_16_char_code_from_step_1
    DEFAULT_FROM_EMAIL=TFMS Support <your_real_email@gmail.com>
    ```
3.  **Test It**:
    Run in terminal: `python3 manage.py shell`
    Then paste:
    ```python
    from django.core.mail import send_mail
    send_mail('Test Local', 'If you read this, Gmail is working!', 'me@gmail.com', ['junaed.test@gmail.com'])
    ```

