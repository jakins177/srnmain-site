# Configuration

## Environment variables

Mail notifications for contact form submissions use the following environment variables (typically defined in `app.env`).
The PHPMailer library required to send SMTP messages is bundled in `lib/PHPMailer`, so no additional Composer packages are necessary beyond the existing Stripe dependency.

| Variable | Description |
| --- | --- |
| `SMTP_HOST` | SMTP server hostname (e.g., `smtp.hostinger.com`). |
| `SMTP_PORT` | Port for the SMTP server (`465` for SSL or `587` for STARTTLS). |
| `SMTP_USER` | SMTP username, usually the full email address. |
| `SMTP_PASS` | SMTP password for the mailbox. |
| `SMTP_ENCRYPTION` | Encryption method (`ssl` or `tls`). Leave empty for none. |
| `SMTP_FROM_ADDRESS` | Optional override for the `From` address. Defaults to `SMTP_USER` if omitted. |
| `SMTP_FROM_NAME` | Optional display name for the `From` address. |
| `CONTACT_REPLY_TO` | Optional override for the reply-to address on notification emails. Defaults to the submitter's email. |
| `CONTACT_NOTIFICATION_RECIPIENTS` | Comma-separated list of inboxes that should receive contact form notifications. |

All other existing database and Stripe settings continue to be read from `app.env` via `config/config.php`.
