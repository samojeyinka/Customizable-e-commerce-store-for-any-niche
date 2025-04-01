<div id="mySidenav" class="sidenav p-2 hidden md:flex flex-col justify-between gap-2">

<div class="flex flex-col gap-2">
    <a href="<?php echo DOMAIN; ?>/admin/dashboard/overview.php" class="nav-link active flex items-center gap-3" onclick="setActive(this)"><img src="<?php echo DOMAIN; ?>/admin/assets/dash/category.svg" class="activeicon w-[20px] h-[20px]" /> <img src="<?php echo DOMAIN; ?>/admin/assets/dash/category2.svg" class="nonactiveicon w-[20px] h-[20px]" /><span>Overview</span></a>
    <a href="<?php echo DOMAIN; ?>/admin/dashboard/products.php" class="nav-link  flex items-center gap-3" onclick="setActive(this)"><img src="<?php echo DOMAIN; ?>/admin/assets/dash/book (1).svg" class="activeicon w-[20px] h-[20px]" /> <img src="<?php echo DOMAIN; ?>/admin/assets/dash/book.svg" class="nonactiveicon w-[20px] h-[20px]" /><span>Products</span></a>
    <a href="<?php echo DOMAIN; ?>/admin/dashboard/orders.php" class="nav-link  flex items-center gap-3" onclick="setActive(this)"><img src="<?php echo DOMAIN; ?>/admin/assets/dash/bag-happy (2).svg" class="activeicon w-[20px] h-[20px]" /> <img src="<?php echo DOMAIN; ?>/admin/assets/dash/bag-happy (1).svg" class="nonactiveicon w-[20px] h-[20px]" /><span>Orders</span></a>
    <a href="<?php echo DOMAIN; ?>/admin/dashboard/users.php" class="nav-link  flex items-center gap-3" onclick="setActive(this)"><img src="<?php echo DOMAIN; ?>/admin/assets/dash/profile (2).svg" class="activeicon w-[20px] h-[20px]" /> <img src="<?php echo DOMAIN; ?>/admin/assets/dash/profile (1).svg" class="nonactiveicon w-[20px] h-[20px]" /><span>Users</span></a>
    <a href="<?php echo DOMAIN; ?>/admin/dashboard/admin-returns.php" class="nav-link  flex items-center gap-3" onclick="setActive(this)"><img src="<?php echo DOMAIN; ?>/assets/global/activereturn.svg" class="activeicon w-[16px] h-[16px]" /> <img src="<?php echo DOMAIN; ?>/assets/global/return.svg" class="nonactiveicon w-[16px] h-[16px]" /><span>Returns</span></a>
    <a href="<?php echo DOMAIN; ?>/admin/dashboard/issues.php" class="nav-link  flex items-center gap-3" onclick="setActive(this)"><img src="<?php echo DOMAIN; ?>/assets/global/activeissue.svg" class="activeicon w-[20px] h-[20px]" /> <img src="<?php echo DOMAIN; ?>/assets/global/issues.svg" class="nonactiveicon w-[20px] h-[20px]" /><span>Issues</span></a>
    
    <!-- <a href="./transactions.php" class="nav-link  flex items-center gap-3" onclick="setActive(this)"><img src="../assets/dash/receipt-minus (1).svg" class="activeicon w-[20px] h-[20px]" /> <img src="../assets/dash/receipt-minus.svg" class="nonactiveicon w-[20px] h-[20px]" /><span>Transactions</span></a> -->
</div>

<div class="flex flex-col gap-2 mb-7">
    <a href="./settings/options.php" class="nav-link  flex items-center gap-3" onclick="setActive(this)"><img src="<?php echo DOMAIN; ?>/admin/assets/dash/setting-2 (1).svg" class="activeicon w-[20px] h-[20px]" /> <img src="<?php echo DOMAIN; ?>/admin/assets/dash/setting-2.svg" class="nonactiveicon w-[20px] h-[20px]" /><span>Settings</span></a>
    <a href="../logout.php" class="cursor-pointer logout-text flex items-center gap-3" onclick="setActive(this)"><img src="<?php echo DOMAIN; ?>/admin/assets/dash/logout.svg" class="nonactiveicon w-[20px] h-[20px]" /><span class="text-[#D93939]">Logout</span></a>
</div>
</div>

