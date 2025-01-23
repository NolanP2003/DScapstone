<?PHP
include("head.php");
?>
<body>
    <?PHP
    include("header.php");
    include("navbar1.php");
    ?>
      <main class="container my-4">
        <h1 class="arima-subtitle text-center">Welcome to Mercury Corporation!</h1>
        <h4 class="arima-subtitle text-center">Where patient care is our top priority</h4>
        <!-- Center Text Column -->
        <div class="d-flex justify-content-center my-4">
          <a href="login.php">
            <button class = "rounded-square-button">Click here to login!</button>
          </a>
        </div>
          <!-- images -->
          <div class="row justify-content-center g-4">
            <!-- Left Image Column -->
            <div class="col-6 col-md-4 text-center">
              <img class="img-fluid rounded" src="photos/stock1.jpg" alt="Photo 1">
            </div>
            <!-- Right Image Column -->
            <div class="col-6 col-md-4 text-center">
              <img class="img-fluid rounded" src="photos/stock2.jpg" alt="Photo 2">
            </div>
          </div>
     
      </main>
      
    
      <!-- Bootstrap JavaScript Bundle -->
      <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>
    </body>
    </html>

    <?PHP
    include("footer.php");
    ?>