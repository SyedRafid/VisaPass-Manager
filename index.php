<!doctype html>
<html lang="en" class="no-js">

<?php
session_start();
include('includes/config.php');
$title = "PAVIMS";
include('includes/_head.php');
?>

<body style="background: linear-gradient(to right, #0f3927, #206a43, #2c9644); font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
  <div class="container d-flex justify-content-center align-items-center" style="min-height: 100vh;">
    <div class="card p-5 shadow-lg" style="max-width: 600px; width: 100%; border-radius: 15px; background-color: #ffffffdd;">
      <h2 class="text-center text-dark mb-3" style="font-weight: bold;">PAVIMS</h2>
      <p class="text-center text-muted mb-4">Passport and Visa Info Management System</p>

      <div class="alert alert-success text-center" role="alert">
        Enter your Passport Number to check its expiration status along with any associated Visa details.
      </div>

      <form id="contactForm">
        <div class="form-group mb-3">
          <label class="form-label" for="passport">Passport Number</label>
          <input type="search" id="passportno" name="passportno" class="form-control" placeholder="e.g. A1234567">
        </div>
        <button class="btn btn-success btn-block w-100" name="login" type="submit">Search</button>
      </form>

      <!-- Sticky Login Button -->
      <a href="login.php" class="btn btn-success position-fixed fw-bold shadow"
        style="bottom: 20px; right: 20px; border-radius: 30px; z-index: 1050;">
        Login
      </a>

      <div class="mt-4">
        <h6>Why use PAVIMS?</h6>
        <ul>
          <li>Check when your passport will expire.</li>
          <li>Track your visa's validity easily.</li>
          <li>Stay notified before your documents expire.</li>
        </ul>
      </div>

      <footer class="text-center mt-5">
        <small>&copy; 2025-<?php echo date("Y") ?> PAVIMS. All rights reserved.</small>
      </footer>
    </div>
  </div>

  <!-- Loading Scripts -->

  <?php include('includes/_scripts.php'); ?>
  <script>
    document
      .getElementById("contactForm")
      .addEventListener("submit", function(event) {
        event.preventDefault(); // Prevent form submission

        const passportno = document.getElementById("passportno").value; // Get passport number

        if (!passportno.trim()) {
          Swal.fire({
            title: "Input Error!",
            text: "Please enter a passport number.",
            icon: "warning",
            confirmButtonText: "OK",
          });
          return;
        }

        // Send the passport number to a PHP script using Fetch API
        fetch("submit.php", {
            method: "POST",
            headers: {
              "Content-Type": "application/x-www-form-urlencoded",
            },
            body: "passportno=" + encodeURIComponent(passportno),
          })
          .then((response) => response.json())
          .then((data) => {
            if (data.success) {
              // Define the table content for the popup
              const tableContent = `
  <div style="max-width: 500px; margin: 20px auto; font-family: Arial, sans-serif; color: #222;">
    <div style="text-align: center; background: linear-gradient(to right,rgb(68, 241, 109), #28a745); padding: 15px; border-radius: 10px 10px 0 0;">
      <h3 style="margin: 0; color: white;">Your Info</h3>
    </div>
    <div style="background: #fff; border: 1px solid #eee; border-top: none; border-radius: 0 0 10px 10px;">
      ${[
        { label: "Name", value: data.name },
        { label: "Nationality", value: data.nationality },
        { label: "Passport No", value: data.passport },
        { label: "Passport Issue Date", value: data.doi },
        { label: "Passport Expiry Date", value: data.doe },
        { label: "Visa No", value: data.visaNo },
        { label: "Visa Issue Date", value: data.vDoi },
        { label: "Visa Expiry Date", value: data.vDoe },
      ]
        .map(
          (item) => `
          <div style="padding: 14px 16px; border-bottom: 1px solid #f0f0f0;">
            <div style="font-size: 14px; color: #666;">${item.label}</div>
            <div style="font-size: 16px; font-weight: bold;">${item.value || "N/A"}</div>
          </div>
        `
        )
        .join("")}
    </div>
  </div>
`;

              // Use Swal.fire to display the popup with the table content and dynamic border color
              Swal.fire({
                title: "Record Found!",
                html: tableContent,
                imageUrl: `assets/img/profile/${data.imageBase64 || 'default.png'}`,
                imageWidth: 110,
                imageHeight: 140,
                confirmButtonText: "Great!",
                customClass: {
                  popup: "border-popup",
                },
              });
              document.querySelector(
                ".swal2-popup"
              ).style.border = `5px solid #0ea60e`;
            } else {
              Swal.fire({
                title: "Error!",
                text: data.message ||
                  "An error occurred while retrieving the data.",
                icon: "error",
                confirmButtonText: "Try Again",
              });
            }
          })
          .catch((error) => {
            console.error("Error:", error);
            Swal.fire({
              title: "Error!",
              text: "Something went wrong. Please try again later.",
              icon: "error",
              confirmButtonText: "Try Again",
            });
          });
      });
  </script>
</body>

</html>