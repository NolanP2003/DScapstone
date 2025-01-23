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
    <title>Medical Records</title>
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
        <ul>
            <li onclick="toggleInfo('info1')">
            <span>Brown, Michael, ID: 089786756 </span>
            <i class="fas fa-chevron-down"></i>
                <div id="info1" class="patient-info">
                    <p class="roboto-black-italic">Michael Brown</p>
                    <p>Date of Birth: 09/18/1988</p>
                    <p>Height: 6'2"</p>
                    <p>Weight: 200lbs</p>
                    <a href="<?php echo url();?>medical_records/michael_brown.pdf" target="_blank">
                        <button>View Medical Record</button>
                    </a>
                </div>
            </li>
            <li onclick="toggleInfo('info2')">
            <span>Davis, Emily, ID: 987654321 </span>
                <i class="fas fa-chevron-down"></i>
                <div id="info2" class="patient-info">
                    <p class="roboto-black-italic">Emily Davis</p>
                    <p>Date of Birth: 03/10/2000</p>
                    <p>Height: 5'4"</p>
                    <p>Weight: 120lbs</p>
                    <a href="<?php echo url();?>medical_records/emily_davis.pdf" target="_blank">
                        <button >View Medical Record</button>
                    </a>
                </div>
            </li>
            <li onclick="toggleInfo('info3')">
            <span>Doe, John, ID: 123456789 </span>
                <i class="fas fa-chevron-down"></i>
                <div id="info3" class="patient-info">
                    <p class="roboto-black-italic">John Doe</p>
                    <p>Date of Birth: 01/15/1985</p>
                    <p>Height: 5'9"</p>
                    <p>Weight: 150lbs</p>
                    <a href="<?php echo url();?>medical_records/john_doe.pdf" target="_blank">
                        <button >View Medical Record</button>
                    </a>
                </div>
            </li>
            <li onclick="toggleInfo('info4')">
            <span>Green, Samuel, ID: 567474632 </span>
                <i class="fas fa-chevron-down"></i>
                <div id="info4" class="patient-info">
                    <p class="roboto-black-italic">Samuel Green</p>
                    <p>Date of Birth: 10/30/1976</p>
                    <p>Height: 5'11"</p>
                    <p>Weight: 180lbs</p>
                    <a href="<?php echo url();?>medical_records/samuel_green.pdf" target="_blank">
                        <button >View Medical Record</button>
                    </a>
                </div>
            </li>
            <li onclick="toggleInfo('info5')">
            <span>Johnson, Mary, ID: 314253647 </span>
                 <i class="fas fa-chevron-down"></i>
                <div id="info5" class="patient-info">
                    <p class="roboto-black-italic">Mary Johnson</p>
                    <p>Date of Birth: 07/22/1992</p>
                    <p>Height: 5'5"</p>
                    <p>Weight: 135lbs</p>
                    <a href="<?php echo url();?>medical_records/mary_johnson.pdf" target="_blank">
                        <button >View Medical Record</button>
                    </a>
                </div>
            </li>
        </ul>
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
