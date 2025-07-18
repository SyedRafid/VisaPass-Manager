<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
} else {
    $title = "Dashboard";
?>
    <!doctype html>
    <html lang="en">
    <?php include('includes/_head.php') ?>

    <body>
        <?php include('includes/header.php'); ?>
        <div class="ts-main-content">
            <?php include('includes/leftbar.php'); ?>
            <div class="content-wrapper">
                <div class="container-fluid">
                    <div class="row">
                        <div class="col-md-12">
                            <div class="panel panel-default mt-4">
                                <div class="panel-heading" style="text-align: center; font-size: 20px; padding: 13px;">Dashboard</div>
                                <div class="panel-body">

                                    <!-- Passport card -->
                                    <div style="font-size: 24px; font-weight: bold; font-family: cursive; background-color: #13acc591; color: #000000; border-radius: 10px; text-align: center; margin: 15px; margin-bottom: 30px; padding: 10px;">
                                        🔔 Upcoming Passport Expirations
                                    </div>
                                    <?php
                                    $query = "
                                    SELECT 
                                        p.*, 
                                        pi.*,
                                        ct.contType
                                    FROM 
                                        profile p
                                    LEFT JOIN
                                        contact_type ct ON p.cont_id = ct.cont_id
                                    LEFT JOIN 
                                        pass_info pi ON pi.pass_id = (
                                            SELECT pass_id 
                                            FROM pass_info 
                                            WHERE pro_id = p.pro_id 
                                            ORDER BY doe DESC 
                                            LIMIT 1
                                        )
                                    WHERE
                                        p.status = 'active'
                                        AND pi.doe <= DATE_ADD(NOW(), INTERVAL 1 YEAR)
                                    ORDER BY 
                                        pi.doe ASC";

                                    $stmt = $dbh->prepare($query);
                                    $stmt->execute();
                                    $results = $stmt->fetchAll(PDO::FETCH_OBJ);
                                    ?>
                                    <div class="kola">
                                        <?php if (count($results) > 0): ?>
                                            <?php foreach ($results as $row): ?>
                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <div class="card-custom<?php echo !empty($row->pStatus) ? 'Oreng' : ''; ?>"
                                                        onclick="showPassportOptions(<?php echo $row->pro_id; ?>, <?php echo $row->pass_id; ?>, '<?php echo $row->pStatus; ?>')">
                                                        <div class="amount-circle-container">
                                                            <div class="circle">
                                                                <img src="../assets/img/profile/<?php echo $row->proPhoto ? htmlentities($row->proPhoto) : 'no-product.png'; ?>" alt="Product Image">
                                                            </div>
                                                            <div class="amount">
                                                                <?php $doe = $row->doe;
                                                                $now = new DateTime();
                                                                $expiry = new DateTime($doe);

                                                                if ($expiry < $now) {
                                                                    $status = "Expired";
                                                                } else {
                                                                    $interval = $now->diff($expiry);
                                                                    $status = $interval->days . " days left";
                                                                }

                                                                echo htmlentities($status);
                                                                ?>
                                                            </div>
                                                        </div>
                                                        <div class="details">
                                                            <p class="details-title">Name: <?php echo htmlentities($row->name); ?></p>
                                                            <div class="info-container">
                                                                <div class="info-item">
                                                                    <p><strong>Issue:</strong>
                                                                        <?php
                                                                        if (!empty($row->doi)) {
                                                                            $date = new DateTime($row->doi);
                                                                            echo $date->format('jS M Y');
                                                                        } else {
                                                                            echo 'N/A';
                                                                        }
                                                                        ?>
                                                                    </p>
                                                                </div>
                                                                <div class="info-item">
                                                                    <p><strong>Expiry:</strong>
                                                                        <?php
                                                                        if (!empty($row->doe)) {
                                                                            $date = new DateTime($row->doe);
                                                                            echo $date->format('jS M Y');
                                                                        } else {
                                                                            echo 'N/A';
                                                                        }
                                                                        ?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                            <div class="info-container">
                                                                <div class="info-item">
                                                                    <p><strong>Nationality: </strong><?php echo htmlentities($row->nationality ?? 'N/A'); ?></p>
                                                                </div>
                                                                <div class="info-item">
                                                                    <p><strong>Passport:</strong> <?php echo htmlentities($row->passNo ?? 'N/A'); ?></p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="no-records col-12">
                                                <h4>You're all set!</h4>
                                                <p>There are no passports nearing expiry.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                    <!-- Visa card -->
                                    <div style="font-size: 24px; font-weight: bold; font-family: cursive; background-color: #13acc591; color: #000000; border-radius: 10px; text-align: center; margin: 15px; margin-bottom: 30px; padding: 10px;">
                                        🔔 Upcoming Visa Expirations
                                    </div>
                                    <?php
                                    $query = "
                                    SELECT 
                                        p.*, 
                                        vi.*,
                                        ct.contType,
                                        pi.passNo
                                    FROM 
                                        profile p
                                    LEFT JOIN
                                        contact_type ct ON p.cont_id = ct.cont_id
                                    LEFT JOIN 
                                        visa_info vi ON vi.visa_id = (
                                            SELECT visa_id 
                                            FROM visa_info 
                                            WHERE pro_id = p.pro_id 
                                            ORDER BY vDoe DESC 
                                            LIMIT 1
                                        )
                                    LEFT JOIN 
                                        pass_info pi ON vi.pass_id = pi.pass_id
                                    WHERE
                                        p.status = 'active'
                                        AND vi.vDoe <= DATE_ADD(NOW(), INTERVAL 3 MONTH)
                                    ORDER BY 
                                        vi.vDoe ASC";

                                    $stmt = $dbh->prepare($query);
                                    $stmt->execute();
                                    $results = $stmt->fetchAll(PDO::FETCH_OBJ);
                                    ?>
                                    <div class="kola">
                                        <?php if (count($results) > 0): ?>
                                            <?php foreach ($results as $row): ?>
                                                <div class="col-lg-4 col-md-6 col-sm-12">
                                                    <div class="card-custom<?php echo !empty($row->vStatus) ? 'Oreng' : ''; ?>"
                                                        onclick="showVisaOptions(<?php echo $row->pro_id; ?>, <?php echo $row->visa_id; ?>, <?php echo $row->pass_id; ?>, '<?php echo $row->vStatus; ?>')">
                                                        <div class="amount-circle-container">
                                                            <div class="circle">
                                                                <img src="../assets/img/profile/<?php echo $row->proPhoto ? htmlentities($row->proPhoto) : 'no-product.png'; ?>" alt="Product Image">
                                                            </div>
                                                            <div class="amount">
                                                                <?php $vDoe = $row->vDoe;
                                                                $now = new DateTime();
                                                                $expiry = new DateTime($vDoe);

                                                                if ($expiry < $now) {
                                                                    $status = "Expired";
                                                                } else {
                                                                    $interval = $now->diff($expiry);
                                                                    $status = $interval->days . " days left";
                                                                }

                                                                echo htmlentities($status);
                                                                ?>
                                                            </div>
                                                        </div>
                                                        <div class="details">
                                                            <p class="details-title">Name: <?php echo htmlentities($row->name); ?></p>
                                                            <div class="info-container">
                                                                <div class="info-item">
                                                                    <p><strong>Passport:</strong> <?php echo htmlentities($row->passNo ?? 'N/A'); ?></p>
                                                                </div>
                                                                <div class="info-item">
                                                                    <p><strong>Visa NO: </strong><?php echo htmlentities($row->visaNo ?? 'N/A'); ?></p>
                                                                </div>
                                                            </div>
                                                            <div class="info-container">
                                                                <div class="info-item">
                                                                    <p><strong>Issue:</strong>
                                                                        <?php
                                                                        if (!empty($row->vDoi)) {
                                                                            $date = new DateTime($row->vDoi);
                                                                            echo $date->format('jS M Y');
                                                                        } else {
                                                                            echo 'N/A';
                                                                        }
                                                                        ?>
                                                                    </p>
                                                                </div>
                                                                <div class="info-item">
                                                                    <p><strong>Expiry:</strong>
                                                                        <?php
                                                                        if (!empty($row->vDoe)) {
                                                                            $date = new DateTime($row->vDoe);
                                                                            echo $date->format('jS M Y');
                                                                        } else {
                                                                            echo 'N/A';
                                                                        }
                                                                        ?>
                                                                    </p>
                                                                </div>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </div>
                                            <?php endforeach; ?>
                                        <?php else: ?>
                                            <div class="no-records col-12">
                                                <h4>You're all set!</h4>
                                                <p>There are no visa nearing expiry.</p>
                                            </div>
                                        <?php endif; ?>
                                    </div>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php include('includes/_scripts.php') ?>

        <script>
            function showPassportOptions(proId, passId, pStatus) {
                // Conditionally disable the Apply button if pStatus is set
                const applyButtonText = pStatus ? '' : 'Apply'; // If pStatus is set, don't show the Apply button
                const isApplyDisabled = pStatus ? true : false; // Disable the button if pStatus exists

                Swal.fire({
                    title: 'Passport Options',
                    text: 'What would you like to do?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: applyButtonText,
                    cancelButtonText: 'Add New',
                    reverseButtons: true,
                    showConfirmButton: !isApplyDisabled // Hide Apply button if pStatus exists
                }).then((result) => {
                    if (result.isConfirmed && !pStatus) { // Proceed with Apply only if pStatus is not set
                        // Show warning before applying
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "This will mark the passport as applied. Do you want to continue?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, apply it!',
                            cancelButtonText: 'Cancel',
                            reverseButtons: true
                        }).then((warningResult) => {
                            if (warningResult.isConfirmed) {
                                // Apply passport update via AJAX, passing only pass_id
                                fetch('apply_passport.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded'
                                        },
                                        body: 'pass_id=' + encodeURIComponent(passId)
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            Swal.fire({
                                                title: 'Status Updated',
                                                text: 'Passport marked as applied.',
                                                icon: 'success',
                                                confirmButtonText: 'OK',
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location.reload();
                                                }
                                            });
                                        } else {
                                            Swal.fire('Error', data.message || 'Could not update status.', 'error');
                                        }
                                    });
                            } else {
                                // If canceled, just log that action
                                Swal.fire('Cancelled', 'Action was canceled.', 'info');
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        window.location.href = 'add_passport.php?id=' + proId;
                    }
                });
            }

            function showVisaOptions(proId, visaId, passId, vStatus) {
                // Conditionally disable the Apply button if vStatus is set
                const applyButtonText = vStatus ? '' : 'Apply'; // If vStatus is set, don't show the Apply button
                const isApplyDisabled = vStatus ? true : false; // Disable the button if vStatus exists

                Swal.fire({
                    title: 'Visa Options',
                    text: 'What would you like to do?',
                    icon: 'question',
                    showCancelButton: true,
                    confirmButtonText: applyButtonText,
                    cancelButtonText: 'Add New',
                    reverseButtons: true,
                    showConfirmButton: !isApplyDisabled // Hide Apply button if vStatus exists
                }).then((result) => {
                    if (result.isConfirmed && !vStatus) { // Proceed with Apply only if vStatus is not set
                        // Show warning before applying
                        Swal.fire({
                            title: 'Are you sure?',
                            text: "This will mark the visa as applied. Do you want to continue?",
                            icon: 'warning',
                            showCancelButton: true,
                            confirmButtonText: 'Yes, apply it!',
                            cancelButtonText: 'Cancel',
                            reverseButtons: true
                        }).then((warningResult) => {
                            if (warningResult.isConfirmed) {
                                // Apply visa update via AJAX, passing only pass_id
                                fetch('apply_visa.php', {
                                        method: 'POST',
                                        headers: {
                                            'Content-Type': 'application/x-www-form-urlencoded'
                                        },
                                        body: 'visa_id=' + encodeURIComponent(visaId)
                                    })
                                    .then(response => response.json())
                                    .then(data => {
                                        if (data.success) {
                                            Swal.fire({
                                                title: 'Status Updated',
                                                text: 'Visa marked as applied.',
                                                icon: 'success',
                                                confirmButtonText: 'OK',
                                            }).then((result) => {
                                                if (result.isConfirmed) {
                                                    window.location.reload();
                                                }
                                            });
                                        } else {
                                            Swal.fire('Error', data.message || 'Could not update status.', 'error');
                                        }
                                    });
                            } else {
                                // If canceled, just log that action
                                Swal.fire('Cancelled', 'Action was canceled.', 'info');
                            }
                        });
                    } else if (result.dismiss === Swal.DismissReason.cancel) {
                        window.location.href = 'add_visa_record.php?id=' + encodeURIComponent(proId) + '&pass_id=' + encodeURIComponent(passId);
                    }
                });
            }
        </script>

    </body>

    </html>
<?php } ?>