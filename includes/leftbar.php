<nav class="ts-sidebar">
    <ul class="ts-sidebar-menu">
        <li class="ts-label">Main</li>
        <li class="has-submenu">
            <a href="dashboard.php"><i class="fa-solid fa-house-chimney fa-2x"></i> &nbsp;<span class="menu-text">Dashboard</span></a>
        </li>
        <li class="has-submenu">
            <a href="javascript:void(0);" class="menu-toggle"><i class="fas fa-users fa-2x"></i> <span class="menu-text">Profile</span></a>
            <ul class="submenu">
                <li><a href="add_profile.php"><i class="fa fa-user fa-lg"></i>&nbsp;&nbsp;&nbsp;<span class="sub-menu-text">Add Profile</span></a></li>
                <li><a href="manage_profile.php"><i class="fa-solid fa-user-pen fa-lg"></i> <span class="sub-menu-text">Manage Profile</span></a></li>
            </ul>
        </li>
        <li class="has-submenu">
            <a href="javascript:void(0);" class="menu-toggle"><i class="fas fa-id-card fa-2x"></i> <span class="menu-text">Passport Record</span></a>
            <ul class="submenu">
                <li><a href="add_passport.php"><i class="fa-regular fa-calendar-check fa-lg"></i>&nbsp;&nbsp;&nbsp;<span class="sub-menu-text">Add Record</span></a></li>
                <li><a href="managePassRec.php"><i class="fa-solid fa-edit fa-lg"></i> <span class="sub-menu-text">Manage Record</span></a></li>
            </ul>
        </li>
        <li class="has-submenu">
            <a href="javascript:void(0);" class="menu-toggle"><i class="fas fa-globe-europe fa-2x"></i> <span class="menu-text">Visa Record</span></a>
            <ul class="submenu">
                <li><a href="add_visa_record.php"><i class="fa-regular fa-calendar-check fa-lg"></i>&nbsp;&nbsp;&nbsp;<span class="sub-menu-text">Add Record</span></a></li>

                <li><a href="manage_Visa_record.php"><i class="fa-solid fa-edit fa-lg"></i> <span class="sub-menu-text">Manage Record</span></a></li>
            </ul>
        </li>
        <li class="has-submenu">
            <a href="contactType.php"><i class="fa-solid fa-mobile-screen-button fa-2x"></i> <span class="menu-text">Contact Types</span></a>
        </li>
        <li class="has-submenu">
            <a href="Reports.php"><i class="fa-solid fa-file-lines fa-2x"></i> <span class="menu-text">Reports</span></a>
        </li>
    </ul>
</nav>

<style>
    /* Sidebar menu styling */
    .ts-sidebar-menu {
        list-style: none;
        padding: 0;
        margin: 0;
    }

    .ts-sidebar-menu ul {
        max-height: 0;
        overflow: hidden;
        transition: max-height 0.3s ease-out;
        /* Smooth transition effect */
    }

    .ts-sidebar-menu li {
        position: relative;
        /* Keep it for submenu positioning */
    }

    .ts-sidebar-menu a {
        display: block;
        padding: 15px;
        color: #fff;
        text-decoration: none;
    }

    .ts-sidebar-menu .submenu {
        display: none;
        /* Hide the submenu by default */
        list-style: none;
        padding: 0;
        margin: 0;
        background-color: #1f4443;
        /* Adjust background for submenu */
    }

    .menu-text {
        font-size: 18px;
        vertical-align: middle;
    }

    .sub-menu-text {
        font-size: 15px;
        vertical-align: middle;
    }
</style>

<script>
    document.querySelectorAll('.menu-toggle').forEach(item => {
        item.addEventListener('click', function() {
            const submenu = this.nextElementSibling;
            if (submenu.style.display === 'block') {
                submenu.style.display = 'none'; // Hide the submenu
            } else {
                submenu.style.display = 'block'; // Show the submenu
            }
        });
    });
</script>