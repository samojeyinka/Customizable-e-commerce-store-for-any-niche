<?php
session_start();
require_once "../../config/config.php";

// Check if admin is logged in
// if (!isset($_SESSION['admin_id'])) {
//     // Redirect to login page
//     header("Location: ../index.php");
//     exit();
// }

// Include the dashboard statistics code
include "dashboard_stats.php";


function getSalesStats($conn) {
    $statsData = array();
    
    // Get Lagos Store stats
    $lagosQuery = "SELECT 
                COUNT(*) as total_sales,
                SUM(order_total) as total_revenue,
                AVG(order_total) as avg_order_value
            FROM 
                orders 
            WHERE 
                pickup_location = 'Lagos Store'";
    
    $result = $conn->query($lagosQuery);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Let's assume 5% increase for Lagos (in a real app, you'd compare with previous period)
        $statsData[] = array(
            "location" => "Lagos Store",
            "sales" => $row["total_sales"],
            "revenue" => $row["total_revenue"],
            "avg_order" => $row["avg_order_value"],
            "growth" => 10,
            "growth_color" => "#39D959"
        );
    }
    
    // Get non-Lagos Store stats (NULL or other values)
    $nonLagosQuery = "SELECT 
                COUNT(*) as total_sales,
                SUM(order_total) as total_revenue,
                AVG(order_total) as avg_order_value
            FROM 
                orders 
            WHERE 
                pickup_location IS NULL OR pickup_location = '' OR pickup_location != 'Lagos Store'";
    
    $result = $conn->query($nonLagosQuery);
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        // Let's assume -5% for non-Lagos (in a real app, you'd compare with previous period)
        $statsData[] = array(
            "location" => "Non-Lagos Locations",
            "sales" => $row["total_sales"],
            "revenue" => $row["total_revenue"],
            "avg_order" => $row["avg_order_value"],
            "growth" => -5,
            "growth_color" => "#D93939"
        );
    }
    
    return $statsData;
}


function getNonLagosBreakdown($conn) {
    $breakdownData = array();
    
    $sql = "SELECT 
                COALESCE(pickup_location, 'Express Delivery') as location,
                COUNT(*) as total_sales,
                SUM(order_total) as total_revenue,
                AVG(order_total) as avg_order_value
            FROM 
                orders 
            WHERE 
                pickup_location IS NULL OR pickup_location = '' OR pickup_location != 'Lagos Store'
            GROUP BY 
                pickup_location
            ORDER BY 
                total_revenue DESC";
    
    $result = $conn->query($sql);
    
    if ($result->num_rows > 0) {
        while($row = $result->fetch_assoc()) {
            // For demo, alternate between positive and negative growth
            static $i = 0;
            $growth = ($i % 2 == 0) ? rand(-10, -1) : rand(1, 10);
            $growthColor = ($growth >= 0) ? "#39D959" : "#D93939";
            $i++;
            
            $breakdownData[] = array(
                "location" => $row["location"],
                "sales" => $row["total_sales"],
                "revenue" => $row["total_revenue"],
                "avg_order" => $row["avg_order_value"],
                "growth" => $growth,
                "growth_color" => $growthColor
            );
        }
    }
    
    return $breakdownData;
}

// Get data for display
$mainStats = getSalesStats($conn);
$locationBreakdown = getNonLagosBreakdown($conn);

?>



<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../style.css" />
    <link rel="stylesheet" href="../styles/styles.css" />
    <link rel="stylesheet" href="../styles/modal.css">
    <link rel="stylesheet" href="../styles/overlay.css">
    <link rel="stylesheet" href="../styles/dropdown.css" />
    <link rel="stylesheet" href="../styles/graph.css" />
    <link rel="stylesheet" href="../styles/dash.css" />
    <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/4.4.1/chart.umd.min.js"></script>
    <title>Document</title>

    <style>
        .modal.logout {
            padding-top: 10%;
        }

        .ordermenu-content {
            display: none;
            position: absolute;
            right: 0;
            z-index: 10;
            min-width: 160px;

        }
    </style>

</head>

<body>

<?php
include("./header.php");
include("./sidebar.php");
?>


    <div id="main" class="h-full px-5 pb-10 flex flex-col gap-3">

        <h1 class="md:hidden text-[18px] font-Onest font-semibold">Overview</h1>
        <!-- The sort by starts -->
        <div class="flex items-center gap-3 hidden">
            <span class="text-[#2c2c2c] text-[14px] md:text-[16px] font-Onest font-medium">Sort by:</span>

            <div class="flex items-center gap-2 md:gap-3 lg:gap-4">
                <div class="custom-dropdown">
                    <div class="md:min-w-[65px] lg:min-w-[70px] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 dropdown-toggle">
                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular"><?php echo $selected_year; ?></span>
                        <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                    </div>
                    <div class="dropdown-content">
                        <div class="flex items-center gap-3">
                            <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                                <?php 
                                $current_year = date('Y');
                                for ($year = $current_year; $year >= $current_year - 3; $year--) {
                                    echo '<div onclick="window.location.href=\'?year=' . $year . '&period=' . $time_period . '\'">' . $year . '</div>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="custom-dropdown">
                    <div class="md:min-w-[65px] lg:min-w-[70px] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 dropdown-toggle">
                        <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">
                            <?php 
                            $period_text = 'Last 28 days';
                            if ($time_period == 'today') $period_text = 'Today';
                            if ($time_period == 'last_7_days') $period_text = 'Last 7 days';
                            echo $period_text;
                            ?>
                        </span>
                        <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                    </div>
                    <div class="dropdown-content">
                        <div class="flex items-center gap-3">
                            <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                                <div onclick="window.location.href='?year=<?php echo $selected_year; ?>&period=today'">Today</div>
                                <div onclick="window.location.href='?year=<?php echo $selected_year; ?>&period=last_7_days'">Last 7 days</div>
                                <div onclick="window.location.href='?year=<?php echo $selected_year; ?>&period=last_28_days'">Last 28 days</div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- The sort by ends -->


        <!-- The stats starts -->
        <div class="flex flex-col md:flex-row items-center justify-between p-2 gap-3">
            <!-- Total Users Card -->
            <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                    <img src="../assets/dash/illu.svg" class="w-full h-full" />
                </div>

                <div class="flex flex-col gap-[1px]">
                    <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Total Users Registered</span>
                    <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']">
                        <?php echo number_format($user_stats['total_users']); ?>
                    </h2>

                    <div class="flex items-center gap-1">
                        <img src="../assets/dash/<?php echo $user_percentage >= 0 ? 'increase' : 'decrease'; ?>.svg" class="w-[20px] h-[20px]" />
                        <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']">
                            <span class="text-<?php echo $user_percentage >= 0 ? '[#39D959]' : '[#D93939]'; ?>">
                                <?php echo $user_percentage >= 0 ? '+' . abs($user_percentage) : '-' . abs($user_percentage); ?>%
                            </span> from last 28 days
                        </p>
                    </div>
                </div>
            </div>

            <!-- Total Sales Card -->
            <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                    <img src="../assets/dash/illu.svg" class="w-full h-full" />
                </div>

                <div class="flex flex-col gap-[1px]">
                    <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Total Sales</span>
                    <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']">
                        ₦<?php echo number_format($sales_stats['total_revenue'] ?? 0); ?>
                    </h2>

                    <div class="flex items-center gap-1">
                        <img src="../assets/dash/<?php echo $sales_percentage >= 0 ? 'increase' : 'decrease'; ?>.svg" class="w-[20px] h-[20px]" />
                        <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']">
                            <span class="text-<?php echo $sales_percentage >= 0 ? '[#39D959]' : '[#D93939]'; ?>">
                                <?php echo $sales_percentage >= 0 ? '+' . abs($sales_percentage) : '-' . abs($sales_percentage); ?>%
                            </span> from last 28 days
                        </p>
                    </div>
                </div>
            </div>

            <!-- Total Orders Card -->
            <div class="w-full flex items-start gap-2 px-6 py-3 bg-[#FBFBFB] border-[1px] border-[#EEEEEE] rounded-[8px]">
                <div class="w-[32px] h-[32px] md:w-[50px] md:h-[50px] rounded-[50%] overflow-hidden">
                    <img src="../assets/dash/order.svg" class="w-full h-full" />
                </div>

                <div class="flex flex-col gap-[1px]">
                    <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Total Orders</span>
                    <h2 class="text-[#1A237E] text-[18px] text-[22px] font-medium font-['Open Sans']">
                        <?php echo number_format($order_stats['total_orders']); ?>
                    </h2>

                    <div class="flex items-center gap-1">
                        <img src="../assets/dash/<?php echo $order_percentage >= 0 ? 'increase' : 'decrease'; ?>.svg" class="w-[20px] h-[20px]" />
                        <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']">
                            <span class="text-<?php echo $order_percentage >= 0 ? '[#39D959]' : '[#D93939]'; ?>">
                                <?php echo $order_percentage >= 0 ? '+' . abs($order_percentage) : '-' . abs($order_percentage); ?>%
                            </span> from last 28 days
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <!-- The stats ends -->


        <!-- The charts area starts -->
        <div class="flex items-start flex-col md:flex-row justify-between p-2 gap-3">
            <div class="w-full md:w-[66%] border-[1px] border-[#E7E7E7] rounded-[8px] p-2 flex flex-col gap-3">
                <div class="flex flex-col-reverse gap-2 md:flex-row md:items-center justify-between">

                    <div class="flex md:hidden items-center gap-1">
                        <img src="../assets/dash/<?php echo $sales_percentage >= 0 ? 'increase' : 'decrease'; ?>.svg" class="w-[20px] h-[20px]" />
                        <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']">
                            <span class="text-<?php echo $sales_percentage >= 0 ? '[#39D959]' : '[#D93939]'; ?>">
                                <?php echo $sales_percentage >= 0 ? '+' . abs($sales_percentage) : '-' . abs($sales_percentage); ?>%
                            </span> from last 28 days
                        </p>
                    </div>
                    <div class="flex items-center gap-[3px]">
                        <span class="text-[#262626] text-[14px] font-medium font-['Open Sans']">Revenue:</span>
                        <h2 class="text-[#1A237E] text-[16px] text-[20px] font-medium font-['Open Sans']">
                            ₦<?php echo number_format($sales_stats['total_revenue'] ?? 0); ?>
                        </h2>

          

                        <div class="hidden md:flex items-center gap-1">
                            <img src="../assets/dash/<?php echo $sales_percentage >= 0 ? 'increase' : 'decrease'; ?>.svg" class="w-[20px] h-[20px]" />
                            <p class="text-[#262626] text-[11px] text-[12px] font-regular font-['Open Sans']">
                                <span class="text-<?php echo $sales_percentage >= 0 ? '[#39D959]' : '[#D93939]'; ?>">
                                    <?php echo $sales_percentage >= 0 ? '+' . abs($sales_percentage) : '-' . abs($sales_percentage); ?>%
                                </span> from last 28 days
                            </p>
                        </div>
                    </div>


                    <div class="flex items-center gap-2 md:gap-2 lg:gap-4">
                      

                        <div class="custom-dropdown">
                            <div class="md:min-w-[65px] lg:min-w-[70px] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 dropdown-toggle">
                                <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">
                                    <?php echo $period_text; ?>
                                </span>
                                <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                            </div>
                            <div class="dropdown-content">
                                <div class="flex items-center gap-3">
                                    <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                                        <div onclick="window.location.href='?year=<?php echo $selected_year; ?>&period=today'">Today</div>
                                        <div onclick="window.location.href='?year=<?php echo $selected_year; ?>&period=last_7_days'">Last 7 days</div>
                                        <div onclick="window.location.href='?year=<?php echo $selected_year; ?>&period=last_28_days'">Last 28 days</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- The graph section starts -->
                <div class="w-full max-h-[300px] overflow-y-auto">
                    <div class="chart-container flex flex-col items-center">
                        <canvas id="lineChart"  labels: <?php echo $monthly_labels_json; ?>,
                        data: <?php echo $monthly_revenue_json; ?>,></canvas>
                    </div>
                </div>
            </div>

            <div class="w-full md:w-[34%] border-[1px] border-[#E7E7E7] rounded-[8px] p-2 flex flex-col gap-3">
                <div class="flex items-center gap-2 md:gap-2 lg:gap-4 mx-auto">
               

                    <div class="custom-dropdown">
                        <div class="md:min-w-[65px] lg:min-w-[70px] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2 dropdown-toggle">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-regular">
                                <?php echo $period_text; ?>
                            </span>
                            <img src="../assets/products/down.svg" class="arrow-down w-[12px] h-[6px]" />
                        </div>
                        <div class="dropdown-content">
                            <div class="flex items-center gap-3">
                                <div class="flex flex-col gap-3 text-[13px] text-[#262626] cursor-pointer">
                                    <div onclick="window.location.href='?year=<?php echo $selected_year; ?>&period=today'">Today</div>
                                    <div onclick="window.location.href='?year=<?php echo $selected_year; ?>&period=last_7_days'">Last 7 days</div>
                                    <div onclick="window.location.href='?year=<?php echo $selected_year; ?>&period=last_28_days'">Last 28 days</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="flex flex-col gap-2">
                    <span class="text-[#262626] text-[15px] md:text-[16px] font-Onest font-medium text-center">Mode of orders</span>
                </div>

                <div class="progress-container">
                    <svg class="progress-circle" width="170" height="170" viewBox="0 0 200 200">
                        <circle class="progress-background" cx="100" cy="100" r="85" />
                        <?php
                        // Calculate percentage for progress arc
                        $total_delivery = ($delivery_stats['total_delivery_orders'] > 0) ? $delivery_stats['total_delivery_orders'] : 1;
                        $express_percentage = ($delivery_stats['express_delivery'] / $total_delivery) * 100;
                        
                        // Calculate stroke-dashoffset (534 is the total circumference)
                        $offset = 534 - ($express_percentage / 100 * 534);
                        ?>
                        <path class="progress-arc"
                            d="M 100,100 m -85,0 a 85,85 0 1,1 170,0"
                            stroke-dasharray="534 534"
                            stroke-dashoffset="<?php echo $offset; ?>" />
                    </svg>
                    <div class="content">
                        <img src="../assets/dash/bag-happy.svg" class="bucket-icon" />
                        <span class="value"><?php echo number_format($delivery_stats['total_delivery_orders']); ?></span>
                    </div>
                </div>

                <div class="flex items-center justify-between">
                    <div class="flex items-start gap-1">
                        <img src="../assets/dash/blue.svg" />
                        <div class="flex flex-col gap-1">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium text-left">Express Delivery</span>
                            <span class="text-[#9A9A9A] text-[13px] md:text-[14px] font-Onest font-regular text-left">
                                <?php echo number_format($delivery_stats['express_delivery']); ?>
                            </span>
                        </div>
                    </div>

                    <div class="flex items-start gap-1">
                        <img src="../assets/dash/blue.svg" />
                        <div class="flex flex-col gap-1">
                            <span class="text-[#262626] text-[13px] md:text-[14px] font-Onest font-medium text-left">Pick Up</span>
                            <span class="text-[#9A9A9A] text-[13px] md:text-[14px] font-Onest font-regular text-left">
                                <?php echo number_format($delivery_stats['pickup_delivery']); ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- The charts area ends -->

         <!-- selling location and best selling products setion starts -->
         <div class="flex flex-col md:flex-row items-start gap-3 md:h-[394px]">
            <!-- The sales location starts -->
            <div class="w-full md:w-[50%] h-full overflow-y-auto p-3 border-[1px] border-[#E7E7E7] rounded-[8px]">
                <div class="flex items-center justify-between">

                    <div class="flex items-center gap-[3px]">
                        <span class="text-[#262626] text-[15px] md:text-[17px] font-medium font-['Open Sans']">Sales by Location</span>
                    </div>

                </div>

                <div class="w-full h-[1px] bg-[#E7E7E7] my-3"></div>

                <div class="w-full flex flex-col gap-2">

                 
                <?php if (count($mainStats) >= 2): 
    $lagos_data = $mainStats[0];
    $non_lagos_data = $mainStats[1];
    
    $lagos_revenue = $lagos_data["revenue"];
    $non_lagos_revenue = $non_lagos_data["revenue"];
    $lagosSales = $lagos_data["sales"];
    $nonLagosSales = $non_lagos_data["sales"];
    
    // Growth values
    $lagos_growth = $lagos_data["growth"] ?? 10; // Default to 10% if not set
    $non_lagos_growth = $non_lagos_data["growth"] ?? -5; // Default to -5% if not set
    
    // Get colors based on growth values
    $lagos_color = ($lagos_growth >= 0) ? "#39D959" : "#D93939";
    $non_lagos_color = ($non_lagos_growth >= 0) ? "#39D959" : "#D93939";
?>

<!-- Lagos Display -->
<div class="flex items-start justify-between gap-2">
    <div class="flex flex-col gap-[3px]">
        <p class="text-[#2c2c2c] text-[14px] text-[15px] font-medium font-['Open Sans']">Lagos</p>
        <p class="text-[#2c2c2c] text-[13px] text-[14px] font-regular font-['Open Sans']"><?php echo number_format($lagosSales); ?> sales</p>
    </div>

    <div class="flex items-center gap-[5px]">
        <p class="text-[#4B4B4B] text-[13px] text-[14px] font-regular font-['Open Sans']">₦<?php echo number_format($lagos_revenue); ?></p>
        <button class="w-[fit-content] bg-[<?php echo $lagos_color; ?>] py-1 px-3 rounded-[16px] text-[14px] text-white font-medium"><?php echo ($lagos_growth >= 0 ? '+' : '') . round($lagos_growth); ?>%</button>
    </div>
</div>

<!-- Non-Lagos Display -->
<div class="flex items-start justify-between gap-2">
    <div class="flex flex-col gap-[3px]">
        <p class="text-[#2c2c2c] text-[14px] text-[15px] font-medium font-['Open Sans']">Outside Lagos</p>
        <p class="text-[#2c2c2c] text-[13px] text-[14px] font-regular font-['Open Sans']"><?php echo number_format($nonLagosSales); ?> sales</p>
    </div>

    <div class="flex items-center gap-[5px]">
        <p class="text-[#4B4B4B] text-[13px] text-[14px] font-regular font-['Open Sans']">₦<?php echo number_format($non_lagos_revenue); ?></p>
        <button class="w-[fit-content] bg-[<?php echo $non_lagos_color; ?>] py-1 px-3 rounded-[16px] text-[14px] text-white font-medium"><?php echo ($non_lagos_growth >= 0 ? '+' : '') . round($non_lagos_growth); ?>%</button>
    </div>
</div>

<!-- Non-Lagos Breakdown Details -->
<?php if (!empty($locationBreakdown)): ?>
    <div class="w-full h-[1px] bg-[#E7E7E7] my-3"></div>
    <p class="text-[#2c2c2c] text-[14px] text-[15px] font-medium font-['Open Sans'] mb-2">Outside Lagos Breakdown</p>
    
    <?php foreach ($locationBreakdown as $location): ?>
        <div class="flex items-start justify-between gap-2 mb-2">
            <div class="flex flex-col gap-[3px]">
                <p class="text-[#2c2c2c] text-[14px] text-[15px] font-medium font-['Open Sans']"><?php echo htmlspecialchars($location["location"]); ?></p>
                <p class="text-[#2c2c2c] text-[13px] text-[14px] font-regular font-['Open Sans']"><?php echo number_format($location["sales"]); ?> sales</p>
            </div>

            <div class="flex items-center gap-[5px]">
                <p class="text-[#4B4B4B] text-[13px] text-[14px] font-regular font-['Open Sans']">₦<?php echo number_format($location["revenue"]); ?></p>
                <button class="w-[fit-content] bg-[<?php echo $location["growth_color"]; ?>] py-1 px-3 rounded-[16px] text-[14px] text-white font-medium"><?php echo ($location["growth"] >= 0 ? '+' : '') . $location["growth"]; ?>%</button>
            </div>
        </div>
    <?php endforeach; ?>
<?php endif; ?>

<?php endif; ?>
                </div>

            </div>
            <!-- The sales location ends -->

            <!-- The best selling products section starts -->
      
            <!-- The best selling products section ends -->

        </div>
        <!-- selling location and best selling products setion ends -->

    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('lineChart').getContext('2d');
    
    // Create gradient
    const gradient = ctx.createLinearGradient(0, 0, 0, 400);
    gradient.addColorStop(0, 'rgba(74, 222, 128, 0.4)');
    gradient.addColorStop(1, 'rgba(74, 222, 128, 0.05)');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?php echo $monthly_labels_json; ?>,
            datasets: [{
                label: 'Revenue',
                data: <?php echo $monthly_revenue_json; ?>,
                borderColor: '#4ade80',
                backgroundColor: gradient,
                fill: true,
                tension: 0.4,
                pointRadius: 0,
                pointHoverRadius: 6,
                pointBackgroundColor: '#4ade80',
                pointHoverBackgroundColor: '#4ade80',
                pointBorderColor: '#fff',
                pointHoverBorderColor: '#fff'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    display: false
                },
                tooltip: {
                    mode: 'index',
                    intersect: false,
                    callbacks: {
                        label: function(context) {
                            return '₦' + context.raw.toLocaleString();
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    grid: {
                        color: '#e5e5e5',
                        drawBorder: false
                    },
                    ticks: {
                        callback: function(value) {
                            return '₦' + value / 1000 + 'k';
                        },
                        stepSize: 2000
                    }
                },
                x: {
                    grid: {
                        display: false
                    }
                }
            },
            interaction: {
                intersect: false,
                mode: 'index'
            }
        }
    });
});
    </script>





    <script type="text/javascript" src="../functions/drop-select.js"></script>
    <script type="text/javascript" src="../functions/order.js"></script>
    <!-- <script type="text/javascript" src="../functions/dash.js"></script>  -->
    <script type="text/javascript" src="../functions/overlay.js"></script>
    <script type="text/javascript" src="../functions/nav.js"></script>

    <script>

function openNotification() {
    document.getElementById("notification").classList.toggle("shownotification");
    console.log("not clicked")
}




function toggleNav() {
    var sidenav = document.getElementById("mySidenav");
    var main = document.getElementById("main");


    if (sidenav.style.width === "200px" || sidenav.style.width === "") {
        sidenav.style.width = "0px";
        main.style.marginLeft = "0px";
        main.style.width = "100vw"
        sidenav.style.left = "-20px"
    } else {
        sidenav.style.width = "200px";
        main.style.marginLeft = "200px";
        main.style.width = "calc(100vw - 200px)"
        sidenav.style.left = "0px"
    }
}




        // Toggle menu open/close
        function openOrdermenu(element) {
            const menu = element.nextElementSibling;
            const isOpen = menu.style.display === 'block';

            // First close all menus
            document.querySelectorAll('.ordermenu-content').forEach(m => {
                m.style.display = 'none';
            });

            // If the clicked menu wasn't open, then open it
            if (!isOpen) {
                menu.style.display = 'block';

                // Position check (for bottom items)
                const rect = element.getBoundingClientRect();
                const spaceBelow = window.innerHeight - rect.bottom;

                if (spaceBelow < 200) {
                    menu.style.bottom = '100%';
                    menu.style.top = 'auto';
                } else {
                    menu.style.top = '100%';
                    menu.style.bottom = 'auto';
                }
            }

            // Stop event propagation to prevent immediate closing
            event.stopPropagation();
        }

        // Add this to your script to close menus when clicking elsewhere
        document.addEventListener('click', function(event) {
            // Only close if click is not on an action button
            if (!event.target.closest('[onclick="openOrdermenu(this)"]')) {
                document.querySelectorAll('.ordermenu-content').forEach(menu => {
                    menu.style.display = 'none';
                });
            }
        });


        var logout = document.getElementById("logout");
        var logmeout = document.getElementById("logmeout");
        var closelo = document.getElementById("closelo");



        logmeout.onclick = function() {
            logout.style.display = "block";
            console.log("log out now")
        }


        closelo.onclick = function() {
            logout.style.display = "none";
        }


        

    </script>
</body>

</html>