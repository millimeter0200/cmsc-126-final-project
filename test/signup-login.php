<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>BooCAS - Sign Up / Log In</title>
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="styles.css">
  <!-- Load the Google Platform Library -->
  <script src="https://accounts.google.com/gsi/client" async defer></script>
</head>
<body>
  <header>
    <div class="logo">
      <img src="booCAS_logo.png" alt="booCAS Logo" class="logo-icon">
    </div>
    <nav>
      <ul>
        <li><a href="role-selection.html">Back to Home</a></li>
      </ul>
    </nav>
  </header>
  <main class="login-signup">
    <div class="signup-panel">
      <h2>Create an account</h2>
      <form>
        <label for="email">Email</label>
        <input type="email" id="email" placeholder="Enter your email">
        <label for="password">Password</label>
        <input type="password" id="password" placeholder="Enter your password">
        <button type="button" onclick="createAccount()">Create account</button>
        <!-- Google Sign-In Button -->
        <div id="g_id_onload"
             data-client_id="611794926642-0t4gk10vq8qgctfdum9q37k7t0bb211o.apps.googleusercontent.com"
             data-callback="handleCredentialResponse"
             data-auto_prompt="false">
        </div>
        <div class="g_id_signin google"
             data-type="standard"
             data-size="large"
             data-theme="outline"
             data-text="continue_with"
             data-shape="rectangular"
             data-logo_alignment="left">
        </div>
        <p>Already Have An Account? <a href="#">Log In</a></p>
      </form>
    </div>
  </main>
  <footer class="upv-footer">
  <div class="footer-row">
    <div class="footer-logos">
      <img src="upv-logo.png" alt="UPV Logo" class="footer-logo">
      <img src="dpsm-logo.png" alt="DPSM Logo" class="footer-logo">
    </div>

    <div class="footer-contact">
      <p><strong>UPV Division of Physical Sciences and Mathematics</strong><br>
      UPV CAS Building, 5023 Miagao, Iloilo<br>
      Tel. No.: (033) 315-9625 Local: 239<br>
      Email: <a href="mailto:psm.upvisayas@up.edu.ph">psm.upvisayas@up.edu.ph</a></p>
    </div>
  </div>

  <div class="footer-bottom">
    <p>&copy; 2024 Division of Physical Sciences and Mathematics. All rights reserved.</p>
  </div>
</footer>

  <script src="script.js"></script>
<script>(function(){function c(){var b=a.contentDocument||a.contentWindow.document;if(b){var d=b.createElement('script');d.innerHTML="window.__CF$cv$params={r:'93da717f8d434560',t:'MTc0Njg5MDY2NC4wMDAwMDA='};var a=document.createElement('script');a.nonce='';a.src='/cdn-cgi/challenge-platform/scripts/jsd/main.js';document.getElementsByTagName('head')[0].appendChild(a);";b.getElementsByTagName('head')[0].appendChild(d)}}if(document.body){var a=document.createElement('iframe');a.height=1;a.width=1;a.style.position='absolute';a.style.top=0;a.style.left=0;a.style.border='none';a.style.visibility='hidden';document.body.appendChild(a);if('loading'!==document.readyState)c();else if(window.addEventListener)document.addEventListener('DOMContentLoaded',c);else{var e=document.onreadystatechange||function(){};document.onreadystatechange=function(b){e(b);'loading'!==document.readyState&&(document.onreadystatechange=e,c())}}}})();</script></body>
</html>
