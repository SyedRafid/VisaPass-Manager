  <!-- Core -->
  <script src="js/jquery.min.js"></script>
  <script src="js/main.js"></script>
  <script src="js/jquery.dataTables.min.js"></script>
  <script src="js/dataTables.bootstrap.min.js"></script>
  <script src="js/Chart.min.js"></script>
  <script src="js/fileinput.js"></script>
  <script src="js/chartData.js"></script>
  <script src="https://code.jquery.com/jquery-2.2.4.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
  <script type="text/javascript" charset="utf8" src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script type="text/javascript" charset="utf8" src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/bootstrap-fileinput/5.2.2/js/fileinput.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/js/select2.min.js"></script>
  <!--Load Swal-->
  <?php if (isset($success)) { ?>
      <script>
          Swal.fire({
              title: "Success",
              text: "<?php echo $success; ?>",
              icon: "success",
          }).then((result) => {
              if (result.isConfirmed) {
                  window.location.href = "<?php echo $redirect; ?>";
              }
          });
      </script>
  <?php } ?>

  <?php if (isset($err)) { ?>
      <script>
          Swal.fire({
              title: "Failed",
              text: "<?php echo $err; ?>",
              icon: "error",
          }).then((result) => {
              if (result.isConfirmed) {
                  window.location.href = "<?php echo $redirect; ?>";
              }
          });
      </script>
  <?php } ?>

  <?php if (isset($err2)) { ?>
      <script>
          Swal.fire({
              title: "Failed",
              text: "<?php echo $err2; ?>",
              icon: "error"
          });
      </script>
  <?php } ?>

  <?php if (isset($info)) { ?>
      <script>
          Swal.fire({
              title: "Info",
              text: "<?php echo addslashes($info); ?>",
              icon: "info",
          }).then((result) => {
              if (result.isConfirmed) {
                  window.location.href = "<?php echo $redirect; ?>";
              }
          });
      </script>

  <?php } ?>