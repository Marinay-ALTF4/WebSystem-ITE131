<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Simple Navbar</title>
</head>
<body>

  <div class="navbar">
    <a href="#">Home</a>
    <a href="#">About</a>
    <a href="#">Services</a>
    <a href="#">Contact</a>
    <?php
      $nav_items = [
        'Home' => '#',
        'About' => '#',
        'Services' => '#',
        'Contact' => '#'
      ];

      foreach ($nav_items as $name => $link) {
        echo "<a href=\"$link\">$name</a>";
      }
    ?>
  </div>

  <style>
    body {
      margin: 0;
      font-family: Arial, sans-serif;
    }

    .navbar {
      background-color: #000;
      padding: 10px 20px;
    }

    .navbar a {
      display: inline-block;
      color: black;
      text-decoration: none;
      padding: 12px 16px;
      font-size: 17px;
      text-align: right;
      border: white;
      border-radius: 500px;
      background-color: white;
    }

    .navbar a:hover {
      background-color: #333;
      color: white;
    }
  </style>

</body>
</html>
