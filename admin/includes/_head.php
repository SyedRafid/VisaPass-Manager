<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
    <meta name="description" content="">
    <meta name="author" content="">
    <meta name="theme-color" content="#3e454c">
    <title> <?php echo isset($title) ? $title : " INFORMATION MANAGEMENT SYSTEM"; ?> </title>
    <link rel="icon" href="assets/logo/logo.ico" type="image/png">
    <!-- Font awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/css/bootstrap.min.css">
    <link rel="stylesheet" type="text/css" href="https://cdn.datatables.net/1.11.5/css/jquery.dataTables.css">
    <!-- Bootstrap CSS -->
    <link rel="stylesheet" href="css/bootstrap.min.css">
    <link rel="stylesheet" href="css/style.css">
    <!-- Bootstrap Datatables -->
    <link rel="stylesheet" href="css/dataTables.bootstrap.min.css">
    <script src="https://cdn.jsdelivr.net/npm/apexcharts"></script>
    <!-- sweetalert2 -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    <!-- Select2 -->
    <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
    <link href="https://cdnjs.cloudflare.com/ajax/libs/select2/4.0.13/css/select2.min.css" rel="stylesheet" />

    <!-- Include Flatpickr CSS and JS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/flatpickr/dist/flatpickr.min.css">
    <script src="https://cdn.jsdelivr.net/npm/flatpickr"></script>

    <script type="text/javascript">
        function valid() {
            var newPassword = document.chngpwd.newpassword.value;
            var confirmPassword = document.chngpwd.confirmpassword.value;

            if (newPassword !== confirmPassword) {
                Swal.fire({
                    title: "Info",
                    text: "New Password and Confirm Password do not match!",
                    icon: "info",
                });
                document.chngpwd.confirmpassword.focus();
                return false;
            }
            return true;
        }
    </script>

    <style>
        .kola {
            display: flex;
            flex-wrap: wrap;
            align-items: stretch;
        }

        @media (min-width: 576px) {
            .col-sm-12 {
                flex-basis: 50%;
                max-width: 50%;
            }
        }

        @media (min-width: 768px) {
            .col-md-6 {
                flex-basis: 50%;
                max-width: 50%;
            }
        }

        @media (min-width: 992px) {
            .col-lg-4 {
                flex-basis: 33.33%;
                max-width: 33.33%;
            }
        }

        .card-custom {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: stretch;
            padding: 20px;
            background: linear-gradient(to bottom, #dc354500, #dc354530, #dc354573);
            /* reddish gradient */
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(255, 0, 0, 0.2), 0 8px 20px rgba(0, 0, 0, 0.2);
            /* 3D shadow effect */
            margin-bottom: 30px;
            transition: all 0.3s ease;
            min-height: 200px;
            width: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            border: 1px solid #eb6e5aeb;
            /* subtle border */
        }

        .card-customOreng {
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            align-items: stretch;
            padding: 20px;
            background: linear-gradient(to bottom, #f47c3c03, #f47c3c21, #f47c3c66);
            /* softened orange tones */
            border-radius: 10px;
            box-shadow: 0 3px 6px rgba(183, 108, 59, 0.2), 0 6px 12px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
            transition: all 0.3s ease;
            min-height: 200px;
            width: 100%;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            border: 1px solid #ffa144;
        }


        .card-custom:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(255, 0, 0, 0.3), 0 6px 15px rgba(0, 0, 0, 0.2);
        }

        .card-customOreng:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 16px rgba(221, 92, 6, 0.25), 0 4px 8px rgba(0, 0, 0, 0.1);
        }

        .circle {
            width: 65px;
            height: 65px;
            overflow: hidden;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .circle img {
            width: 100%;
            height: 100%;
            object-fit: cover;
            border: 2px solid #ddd;
        }

        .amount {
            font-size: 19px;
            color: #353b40;
            font-weight: bold;
            text-align: right;
            flex-shrink: 0;
            margin-bottom: auto;
        }

        .details {
            margin-top: 10px;
        }

        .details-title {
            font-weight: bold;
            font-size: 17px !important;
            ;
            color: #151618;
            margin-bottom: 5px;
            text-overflow: ellipsis;
            white-space: nowrap;
            overflow: hidden;
        }

        .details p {
            margin: 5px 0;
            font-size: 14px;
            color: #353b40;
        }

        .no-records {
            text-align: center;
            margin-top: 20px;
        }

        .months {
            color: #3e3f3a;
            font-size: 15px !important;
            font-weight: bold;
            text-align: right;
            flex-shrink: 0;
            margin-top: 15px;
        }

        .amount {
            font-size: 19px;
            color: #f00707;
            font-weight: bold;
            text-align: right;
            flex-shrink: 0;
            margin-bottom: auto;
        }

        .amount-circle-container {
            display: flex;
            align-items: center;
            justify-content: space-between;

            .pagination {
                margin-top: 20px;
                margin-bottom: 20px;
                width: 100%;
            }
        }

        .info-container {
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            gap: 10px;
            /* Adds spacing between columns */
            margin-top: 10px;
        }

        .info-item {
            flex: 1 1 calc(50% - 10px);
            /* Adjust for two equal-width items with spacing */
            display: flex;
            flex-direction: column;
        }

        @media (max-width: 576px) {
            .info-container {
                flex-wrap: nowrap;
                /* Keeps items side-by-side on mobile */
                justify-content: space-around;
            }

            .info-item {
                flex: 0 0 45%;
                /* Shrinks items for mobile spacing */
            }
        }

        #chartContainer {
            max-width: 300px;
            margin: 20px auto;
        }

        #chartContainer2 {
            max-width: 100%;
            width: 100%;
            margin: 20px auto;
        }

        .errorWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #dd3d36;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
            box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
        }

        .succWrap {
            padding: 10px;
            margin: 0 0 20px 0;
            background: #fff;
            border-left: 4px solid #5cb85c;
            -webkit-box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
            box-shadow: 0 1px 1px 0 rgba(0, 0, 0, .1);
        }

        .select2-container--default .select2-selection--single {
            height: 46px;
            border: 1px solid #ced4da;
            border-radius: 5px;
            padding: 5px 12px;
            font-size: 14px;
            line-height: 30px;
        }

        .select2-container--default .select2-selection--single .select2-selection__rendered {
            color: #495057;
            line-height: 38px;
        }

        .select2-container--default .select2-selection--single .select2-selection__arrow {
            height: 100%;
        }
    </style>
</head>