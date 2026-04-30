RIETA Full Chatbot Install Pack

Official bot name: Rieta
Support email: support@unidagateway.co.tz

Features:
- Greeting first
- Shows common help areas as sample questions
- User can choose a topic or type a custom question
- If question is outside common areas, Rieta asks for:
  - full name
  - email
  - phone number
  - concern
- Then forwards/saves request for human support
- Saves support requests in rieta_support_requests
- Attempts to email support@unidagateway.co.tz using PHP mail()

Install:
1. Upload all files to project root.
2. Open:
   https://investoraccess.unidatechs.com/install_rieta_full_chatbot.php
3. Confirm OK results.
4. Delete:
   install_rieta_full_chatbot.php

If PHP mail() does not send on your hosting, the request is still saved in DB table:
rieta_support_requests
