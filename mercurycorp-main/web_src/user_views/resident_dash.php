<?php
//include('../includes/auth.php');

//if (!isResident()) {
    //header("Location: /views/login.php");
   // exit;
//}
?>

<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resident Dashboard</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
        integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
        integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    <!-- Custom CSS -->
    <link href="../style.css" rel="stylesheet">
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&family=Arima:wght@100..700&display=swap" rel="stylesheet">
    <!-- Font Awesome -->
    <script src="https://kit.fontawesome.com/d896ee4cb8.js" crossorigin="anonymous"></script>
</head>

<body>

    <!-- Full-screen loading animation -->
    <div id="loading-screen">
        <div id="logo-container">
            <img src="../photos/mercuryCorpLogo.png" alt="Mercury Logo" id="loading-logo">
            <div class="circle large-circle"></div>
            <div class="circle large-circle"></div>
            <div class="circle large-circle"></div>
        </div>
    </div>

    <script>
        // Hide the loading screen after the page fully loads
        window.addEventListener("load", function() {
            const loadingScreen = document.getElementById("loading-screen");
            loadingScreen.style.animation = "zoomOut 1s ease forwards";
            setTimeout(() => {
                loadingScreen.style.display = "none";
            }, 1000);
        });
    </script>

    <!-- Header -->
    <header class="row">
        <div class="col-1">
          <img class="main_logo" src="../photos/mercuryCorpLogo.png" alt="MercuryCorp logo">
        </div>
        <div class="col">
          <h1 class = "abril-fatface-regular">Mercury</h1>
        </div>
      </header>
</head>
<body>
    <h1>Resident Dashboard</h1>

    <h2>Your Schedule</h2>
    <ul>
        <li>Breakfast - 8:00 AM</li>
        <li>Activity: Yoga - 10:00 AM</li>
    </ul>

    <a href="/public/index.php">Logout</a>
</body>
</html>
<?PHP
    include "../footer.php";
?>
