<?php if (!defined('DOMAIN')) { require_once __DIR__ . '/../../config/config.php'; } ?>
<div id="mySidenav" class="sidenav p-2 flex flex-col justify-between gap-2">

    <div class="flex flex-col gap-4">
        <div class="flex flex-col gap-1 px-2">
            <p class="text-[11px] font-semibold uppercase tracking-[0.12em] text-gray-400 px-2 pt-2">Menu</p>
            <a href="<?php echo DOMAIN; ?>/admin/dashboard/overview.php" class="nav-link" onclick="setActive(this)">
                <i class="fa-solid fa-chart-pie"></i><span>Overview</span>
            </a>
            <a href="<?php echo DOMAIN; ?>/admin/dashboard/products.php" class="nav-link" onclick="setActive(this)">
                <i class="fa-solid fa-box"></i><span>Products</span>
            </a>
            <a href="<?php echo DOMAIN; ?>/admin/dashboard/orders.php" class="nav-link" onclick="setActive(this)">
                <i class="fa-solid fa-bag-shopping"></i><span>Orders</span>
            </a>
            <a href="<?php echo DOMAIN; ?>/admin/dashboard/users.php" class="nav-link" onclick="setActive(this)">
                <i class="fa-solid fa-user"></i><span>Users</span>
            </a>
            <a href="<?php echo DOMAIN; ?>/admin/dashboard/admin-returns.php" class="nav-link" onclick="setActive(this)">
                <i class="fa-solid fa-rotate-left"></i><span>Returns</span>
            </a>
            <a href="<?php echo DOMAIN; ?>/admin/dashboard/issues.php" class="nav-link" onclick="setActive(this)">
                <i class="fa-solid fa-circle-exclamation"></i><span>Issues</span>
            </a>
        </div>
    </div>

    <div class="flex flex-col gap-5 mb-4">
        <div class="border-t border-gray-100 mx-2"></div>
        <div class="flex flex-col gap-1 px-2">
            <a href="./settings/options.php" class="nav-link" onclick="setActive(this)">
                <i class="fa-solid fa-gear"></i><span>Settings</span>
            </a>
            <a href="../logout.php" class="nav-link nav-logout" onclick="setActive(this)">
                <i class="fa-solid fa-right-from-bracket"></i><span>Logout</span>
            </a>
        </div>
    </div>
</div>