<?php
session_start();
include('includes/config.php');

if (strlen($_SESSION['alogin']) == 0) {
    header('location:index.php');
    exit();
}
$title = "Report";
?>
<!doctype html>
<html lang="en" class="no-js">
<?php include('includes/_head.php'); ?>

<body>
    <?php include('includes/header.php'); ?>
    <div class="ts-main-content">
        <?php include('includes/leftbar.php'); ?>
        <div class="content-wrapper">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-12">
                        <h2 class="page-title">Reports</h2>

                        <div class="panel panel-default">
                            <div class="panel-heading">Listed Reports</div>
                            <div class="panel-body">
                                <div class="row">
                                    <div class="table-responsive">
                                        <table id="zctb" class="display table table-striped table-bordered table-hover" cellspacing="0" width="100%">
                                            <thead>
                                                <tr style="text-align: center;">
                                                    <th>#</th>
                                                    <th>Photo</th>
                                                    <th>Name</th>
                                                    <th>Passport</th>
                                                    <th>Date of Issue</th>
                                                    <th>Date of Expiry</th>
                                                    <th>Visa</th>
                                                    <th>Date of Issue</th>
                                                    <th>Date of Expiry</th>
                                                    <th>Contact Info</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php
                                                $sql = "
                                                       SELECT 
                                                    p.*, 
                                                    pi.*,
                                                    vi.*,
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
                                                LEFT JOIN 
                                                    visa_info vi ON vi.visa_id = (
                                                        SELECT visa_id 
                                                        FROM visa_info 
                                                        WHERE pro_id = p.pro_id 
                                                        ORDER BY vDoe DESC 
                                                        LIMIT 1
                                                    )
                                                WHERE
                                                    p.status = 'active'; 
                                                ";
                                                $query = $dbh->prepare($sql);
                                                $query->execute();
                                                $results = $query->fetchAll(PDO::FETCH_OBJ);
                                                $cnt = 1;
                                                if ($query->rowCount() > 0) {
                                                    foreach ($results as $result) {
                                                ?>
                                                        <tr style="text-align: center;">
                                                            <td><?php echo htmlentities($cnt); ?></td>
                                                            <td>
                                                                <?php
                                                                $image = ($result->proPhoto);
                                                                ?>
                                                                <div style="display: inline-block; border: 1px solid #6a25d7; border-radius: 10px; padding: 5px; background-color: #f9f9f9; box-shadow: -3px 3px 10px rgba(0,0,0,0.34); width: 100px; height: 100px; overflow: hidden; text-align: center;">
                                                                    <img src="<?php echo !empty($image) ? '../assets/img/profile/' . htmlentities($image) : '../assets/img/profile/no-image.ico'; ?>"
                                                                        alt="Additional Image"
                                                                        style="max-width: 100%; max-height: 100%; object-fit: contain; border-radius: 8px;">
                                                                </div>
                                                            </td>
                                                            <td><?php echo htmlentities($result->name); ?></td>
                                                            <td>
                                                                <?php if (!empty($result->passNo)): ?>
                                                                    <?php echo htmlentities($result->passNo); ?>
                                                                <?php endif; ?>

                                                                <?php if (!empty($result->passPhoto)): ?>
                                                                    <br><br>
                                                                    <a href="../assets/img/passport/<?php echo htmlentities($result->passPhoto); ?>"
                                                                        target="_blank"
                                                                        class="btn btn-sm btn-info"
                                                                        style="font-style: normal; font-size: 12px; padding: 5px 10px; margin-top: 10px;">
                                                                        <i class="fa fa-eye"></i>&nbsp; VIEW
                                                                    </a>
                                                                <?php endif; ?>

                                                                <?php if (empty($result->passNo) && empty($result->passPhoto)): ?>
                                                                    <span style="color: #999;">Not available</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                if (!empty($result->doi)) {
                                                                    $date = new DateTime($result->doi);
                                                                    echo $date->format('jS M Y');
                                                                } else {
                                                                    echo '<span style="color: #999;">Not available</span>';
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                if (!empty($result->doe)) {
                                                                    $date = new DateTime($result->doe);
                                                                    echo $date->format('jS M Y');
                                                                } else {
                                                                    echo '<span style="color: #999;">Not available</span>';
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php if (!empty($result->visaNo)): ?>
                                                                    <?php echo htmlentities($result->visaNo); ?>
                                                                <?php endif; ?>

                                                                <?php if (!empty($result->visaImage)): ?>
                                                                    <br><br>
                                                                    <a href="../assets/img/visa/<?php echo htmlentities($result->visaImage); ?>"
                                                                        target="_blank"
                                                                        class="btn btn-sm btn-info"
                                                                        style="font-style: normal; font-size: 12px; padding: 5px 10px; margin-top: 10px;">
                                                                        <i class="fa fa-eye"></i>&nbsp; VIEW
                                                                    </a>
                                                                <?php endif; ?>

                                                                <?php if (empty($result->visaNo) && empty($result->visaImage)): ?>
                                                                    <span style="color: #999;">Not available</span>
                                                                <?php endif; ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                if (!empty($result->vDoi)) {
                                                                    $date = new DateTime($result->vDoi);
                                                                    echo $date->format('jS M Y');
                                                                } else {
                                                                    echo '<span style="color: #999;">Not available</span>';
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php
                                                                if (!empty($result->vDoe)) {
                                                                    $date = new DateTime($result->vDoe);
                                                                    echo $date->format('jS M Y');
                                                                } else {
                                                                    echo '<span style="color: #999;">Not available</span>';
                                                                }
                                                                ?>
                                                            </td>
                                                            <td>
                                                                <?php if (!empty($result->pNumber) && !empty($result->contType)): ?>
                                                                    <strong><?php echo htmlentities(ucfirst($result->contType)); ?>:</strong>
                                                                    <br><br>
                                                                    <?php echo htmlentities($result->pNumber); ?>
                                                                <?php else: ?>
                                                                    <span style="color: #999;">Not available</span>
                                                                <?php endif; ?>
                                                            </td>
                                                        </tr>
                                                <?php $cnt = $cnt + 1;
                                                    }
                                                } ?>
                                            </tbody>
                                        </table>
                                    </div> <!-- End table-responsive -->
                                </div> <!-- End row -->
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </div>

    <!-- Loading Scripts -->
    <?php include('includes/_scripts.php'); ?>
</body>

</html>