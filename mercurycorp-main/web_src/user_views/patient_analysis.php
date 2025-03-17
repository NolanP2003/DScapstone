<?php
include('../navbarFunctions.php');
// include('../head.php')

?>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet"
    integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"
    integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL"
    crossorigin="anonymous"></script>
  <!-- CSS Source-->
  <link href="../style.css" rel="stylesheet">
  <!-- Google Font API-->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&display=swap" rel="stylesheet">
  <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Abril+Fatface&family=Arima:wght@100..700&display=swap" rel="stylesheet">
  <!-- JavaScript Source-->
  <!-- <script src="main.js"></script> -->

    <script src="https://kit.fontawesome.com/d896ee4cb8.js" crossorigin="anonymous"></script>
    <title>Patient Analysis Window</title>
    <style>
        /* Inline CSS */
        li {
            display: flex;
            justify-content: space-between;
            align-items: center;
            padding: 10px;
            cursor: pointer;
            border-bottom: 1px solid #ddd;
            position: relative;
        }


        li i {
            font-size: 14px;
            color: gray;
            transition: transform 0.3s ease;
            position: absolute;
            right: 10px;
        }

        .patient-info {
            display: none;
            max-height: 0;
            overflow: hidden;
            transition: max-height 0.3s ease-out;
        }

        .patient-info.show {
            display: block;
            max-height: 500px;
        }

        li i.rotate {
            transform: rotate(180deg);
        }
    </style>
</head>
<body>
<header class="row">
        <div class="col-1">
          <img class="main_logo" src="../photos/mercuryCorpLogo.png" alt="MercuryCorp logo">
        </div>
        <div class="col">
          <h1 class = "abril-fatface-regular">Mercury</h1>
        </div>
</header> 
<nav class="navbar navbar-expand-lg" style="background-color: rgb(133, 161, 170); height: 70px;">
        <div class="container-fluid">
            <!-- Collapsible button on the left
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button> -->

            <!-- Navbar content collapses into a dropdown menu -->
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                <h3>Medical Records</h3>
                </ul>
                <ul class="navbar-nav ms-auto">
                   
                    <li class="nav-item"><a class="nav-link" href="nurse_dash.php">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
                </ul>
    </nav>


    <!-- Welcome Message -->
    <div class="container mt-4">
        <h2 class="text-center">Welcome, <?php echo htmlspecialchars($_SESSION['username'] ?? 'Nurse'); ?>!</h2>

        <!-- Patient List -->
        <h2>Patient List</h2>
    
    </div>

    <script>
        function toggleInfo(infoId) {
            const infoDiv = document.getElementById(infoId);
            const listItem = infoDiv.closest('li').querySelector('i');
            if (infoDiv) {
                infoDiv.classList.toggle('show');
                listItem.classList.toggle('rotate');
            }
        }
    </script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

     <!-- chatbot button so we can expand and collapse -->
<button id="chatbot-toggle" style="position: fixed; bottom: 20px; right: 20px; padding: 10px; background-color: blue; color: white; border: none; border-radius: 5px;">
    Chatbot
</button>

<!-- html for chatbot popup window -->
<div id="chatbot-popup" style="display: none; position: fixed; bottom: 150px; right: 20px; width: 380px; height: 500px; background: white; border-radius: 10px; box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3); border: 1px solid #ccc; overflow: hidden;">
    <div class="chat-header" style="background: blue; color: white; padding: 10px; font-size: 18px; font-weight: bold; text-align: center; display: flex; justify-content: space-between; align-items: center;">
        <span>Medical Chatbot</span>
        <button id="chatbot-close" style="background: none; border: none; color: white; font-size: 20px; cursor: pointer;">&times;</button>
    </div>

    <!-- use the chatbot.php file -->
    <iframe id="chatbot-frame" src="chatbot/chatbot.php" style="width: 100%; height: 100%; border: none;"></iframe>
</div>

<script>
    // listen to button clicks and show/hide the chatbot popup
    document.addEventListener("DOMContentLoaded", function() {
        const chatbotToggle = document.getElementById("chatbot-toggle");
        const chatbotPopup = document.getElementById("chatbot-popup");
        const chatbotClose = document.getElementById("chatbot-close");

        chatbotToggle.addEventListener("click", function() {
            chatbotPopup.style.display = chatbotPopup.style.display === "none" ? "block" : "none";
        });

        chatbotClose.addEventListener("click", function() {
            chatbotPopup.style.display = "none";
        });
        let isDragging = false;
        let offsetX, offsetY;

        const chatHeader = document.getElementById("chat-header");

        chatHeader.addEventListener("mousedown", function(e) {
            isDragging = true;
            offsetX = e.clientX - chatbotPopup.getBoundingClientRect().left;
            offsetY = e.clientY - chatbotPopup.getBoundingClientRect().top;

            chatHeader.style.cursor = 'grabbing';
        });

        document.addEventListener("mousemove", function(e) {
            if (isDragging) {
                chatbotPopup.style.left = e.clientX - offsetX + "px";
                chatbotPopup.style.top = e.clientY - offsetY + "px";
            }
        });

        document.addEventListener("mouseup", function() {
            isDragging = false;
            chatHeader.style.cursor = 'move';
        });
    });
</script>
    

    <footer>
  <p> 2024 Mercury Corp. All rights reserved.</p>
  <p>Follow us on social media!</p>
    <a href="https://github.com/Laneyeh">
  <img class="socialMediaIcon" src="../photos/facebook.png" alt="Facebook">
</a>
<a href="https://github.com/torrescschool">
  <img class="socialMediaIcon" src="../photos/instagram.png" alt="Instagram">
</a>
<a href="https://github.com/Mildred1999">
  <img class="socialMediaIcon" src="../photos/twitter.png" alt="Twitter">
</a>
</footer>
</body>
</html>
