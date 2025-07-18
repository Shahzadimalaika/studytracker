<?php
// contact.php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  $name = htmlspecialchars($_POST['name']);
  $email = htmlspecialchars($_POST['email']);
  $message = htmlspecialchars($_POST['message']);

  // You can save to DB or send email (for demo, just show message)
  $success = true;
}
?>
<!DOCTYPE html>
<html>
<head>
  <title>Contact Us</title>
  <link href="https://cdn.tailwindcss.com" rel="stylesheet">
</head>
<body class="bg-gray-100 p-6">
  <div class="max-w-xl mx-auto bg-white p-6 rounded shadow">
    <h1 class="text-2xl font-bold mb-4">Contact Us</h1>
    <?php if (!empty($success)): ?>
      <p class="text-green-600 mb-4">Thank you! Your message has been sent.</p>
    <?php endif; ?>
    <form method="POST" action="">
      <input name="name" placeholder="Your Name" required class="block w-full p-2 border mb-3 rounded" />
      <input name="email" type="email" placeholder="Your Email" required class="block w-full p-2 border mb-3 rounded" />
      <textarea name="message" placeholder="Your Message" required class="block w-full p-2 border mb-3 rounded"></textarea>
      <button type="submit" class="bg-blue-600 text-white px-4 py-2 rounded">Send</button>
    </form>
  </div>
</body>
</html>
