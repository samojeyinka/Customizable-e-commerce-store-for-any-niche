
<?php
// Database configuration
$db_host = 'localhost';
$db_name = 'victosah';
$db_user = 'root'; // Change as needed
$db_pass = ''; // Change as needed

// Connect to database
try {
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}

// Check if profile table exists
function tableExists($pdo, $table) {
    try {
        $stmt = $pdo->query("SHOW TABLES LIKE '$table'");
        return $stmt->rowCount() > 0;
    } catch (Exception $e) {
        return false;
    }
}

$hasProfileTable = tableExists($pdo, 'profile');

// Fetch users with profiles
function getUsers($pdo, $hasProfileTable, $dateFilter = null) {
    try {
        // Always join with profile table, but use LEFT JOIN so we get all users
        $query = "
            SELECT u.id, u.email, u.verified_at, u.ip, u.status, u.last_login";
            
        if ($hasProfileTable) {
            $query .= ", p.first_name, p.last_name, p.phone";
        }
        
        $query .= " FROM users u";
        
        if ($hasProfileTable) {
            $query .= " LEFT JOIN profile p ON u.id = p.user_id";
        }
        
        // Apply date filter if provided
        if ($dateFilter) {
            $query .= " WHERE ";
            
            switch ($dateFilter) {
                case 'today':
                    $query .= "DATE(last_login) = CURDATE()";
                    break;
                case 'last7days':
                    $query .= "last_login >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
                    break;
                case 'last28days':
                    $query .= "last_login >= DATE_SUB(NOW(), INTERVAL 28 DAY)";
                    break;
            }
        }
        
        $query .= " ORDER BY u.id DESC LIMIT 25";
        
        $stmt = $pdo->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    } catch (Exception $e) {
        // Return empty array if there's an error
        return [];
    }
}

// Get number of total users
function getTotalUsers($pdo) {
    try {
        $stmt = $pdo->query("SELECT COUNT(*) FROM users");
        return $stmt->fetchColumn();
    } catch (Exception $e) {
        return 0;
    }
}

// Format datetime
function formatDate($date) {
    if (empty($date)) return 'N/A';
    return date('d/m/Y H:i:s', strtotime($date));
}

// Get user name - show first_name if available, otherwise "User ID"
function getUserName($user) {
    // If first_name exists, use it (with last_name if available)
    if (isset($user['first_name']) && !empty($user['first_name'])) {
        if (isset($user['last_name']) && !empty($user['last_name'])) {
            return $user['first_name'] . ' ' . $user['last_name'];
        }
        return $user['first_name'];
    }
    
    // If no profile data, just show email instead of User ID
    return isset($user['email']) ? $user['email'] : 'User ' . $user['id'];
}

// Delete user
if (isset($_POST['delete_user']) && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    try {
        $pdo->beginTransaction();
        
        // Check if profile exists for this user
        if ($hasProfileTable) {
            $stmt = $pdo->prepare("DELETE FROM profile WHERE user_id = ?");
            $stmt->execute([$userId]);
        }
        
        // Delete user
        $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        
        $pdo->commit();
        
        // Redirect to refresh the page
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } catch (Exception $e) {
        $pdo->rollBack();
        $error = "Could not delete user: " . $e->getMessage();
    }
}

// Disable/Enable user
if (isset($_POST['toggle_status']) && isset($_POST['user_id'])) {
    $userId = $_POST['user_id'];
    $newStatus = ($_POST['current_status'] == 'active') ? 'pending' : 'active';
    
    try {
        $stmt = $pdo->prepare("UPDATE users SET status = ? WHERE id = ?");
        $stmt->execute([$newStatus, $userId]);
        
        // Redirect to refresh the page
        header("Location: " . $_SERVER['PHP_SELF']);
        exit;
    } catch (Exception $e) {
        $error = "Could not update user status: " . $e->getMessage();
    }
}

// Check for date filter
$dateFilter = isset($_GET['date_filter']) ? $_GET['date_filter'] : null;

// Get user data
$users = getUsers($pdo, $hasProfileTable, $dateFilter);
$totalUsers = getTotalUsers($pdo);
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <script src="https://unpkg.com/@tailwindcss/browser@4"></script>
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=League+Gothic&family=Montserrat:ital,wght@0,100..900;1,100..900&family=Onest:wght@100..900&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <title>Users</title>
    <style>
        .tab-active {
            background-color: #1A237E;
            color: white;
        }

        .tab-inactive {
            background-color: #F3F3F3;
            color: #262626;
        }

        .section-active {
            display: block;
        }

        .section-inactive {
            display: none;
        }

        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            overflow: auto;
            background-color: rgba(0, 0, 0, 0.4);
        }

        .modal-content {
            background-color: #fefefe;
            margin: 10% auto;
            padding: 20px;
            border-radius: 8px;
            width: 80%;
            max-width: 600px;
        }

        .ordermenu-content {
            display: none;
            position: absolute;
            right: 0;
            z-index: 10;
            width: 200px;
        }
        
        .text-green-600 {
            color: #16a34a;
        }
        
        .text-yellow-600 {
            color: #ca8a04;
        }
        
        .text-red-600 {
            color: #dc2626;
        }
        
        .bg-green-600 {
            background-color: #16a34a;
        }
        
        .bg-blue-900 {
            background-color: #1a237e;
        }
        
        /* Dropdown styles */
        .dropdown {
            position: relative;
            display: inline-block;
        }
        
        .dropdown-content {
            display: none;
            position: absolute;
            background-color: #f9f9f9;
            min-width: 160px;
            box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
            z-index: 1;
            border-radius: 4px;
            margin-top: 2px;
        }
        
        .dropdown-content a {
            color: black;
            padding: 12px 16px;
            text-decoration: none;
            display: block;
        }
        
        .dropdown-content a:hover {
            background-color: #f1f1f1;
        }
        
        .show {
            display: block;
        }
    </style>
</head>

<body class="relative">
    <div id="main" class="md:p-4 flex flex-col gap-3 bg-[#FAFAFA]">
        <div class="w-full rounded-[16px] bg-white mx-auto p-2">
            <h1 class="md:hidden text-[18px] font-semibold mb-3 md:mb-0">Users</h1>

            <div id="myBtn" class="w-full md:w-[274px] border-[1px] border-[#F3F3F3] cursor-pointer rounded-[8px] p-2 flex justify-between items-center">
                <h1 class="text-[16px] font-regular">Users Overview</h1>
                <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                    <polyline points="6 9 12 15 18 9"></polyline>
                </svg>
            </div>
        </div>

        <?php if (isset($error)): ?>
        <div class="w-full rounded-[8px] bg-red-100 border border-red-400 text-red-600 px-4 py-3">
            <strong>Error:</strong> <?= htmlspecialchars($error) ?>
        </div>
        <?php endif; ?>

        <div class="w-full rounded-[16px] bg-white mx-auto p-3">
            <div id="myBtn" class="w-full flex flex-col md:flex-row md:items-center gap-3 md:gap-5 justify-between">
                <div class="flex items-center gap-0">
                    <div class="w-full flex items-center gap-2 border-[1px] border-[#E1E1E1] rounded-[24px] p-2">
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <circle cx="11" cy="11" r="8"></circle>
                            <line x1="21" y1="21" x2="16.65" y2="16.65"></line>
                        </svg>
                        <input type="text" id="searchInput" placeholder="Search" class="w-full md:w-[250px] text-[14px] border-none outline-none placeholder:text-[#D9D9D9]" />
                    </div>
                </div>

                <div class="w-full flex items-center justify-between">
                    <div class="flex items-center gap-3">
                        <span class="text-[#2c2c2c] text-[14px] md:text-[16px] font-medium">Filter by:</span>
                        <svg xmlns="http://www.w3.org/2000/svg" width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="md:hidden">
                            <line x1="21" y1="6" x2="3" y2="6"></line>
                            <line x1="21" y1="12" x2="3" y2="12"></line>
                            <line x1="21" y1="18" x2="3" y2="18"></line>
                        </svg>

                        <div class="hidden md:flex items-center gap-2 md:gap-3 lg:gap-4">
                            <div class="dropdown">
                                <button onclick="toggleDropdown()" class="md:min-w-[65px] lg:min-w-[70px] rounded-[4px] border-[1px] border-[#C5C5C5] flex items-center justify-between py-1 px-2">
                                    <span class="text-[#262626] text-[13px] md:text-[14px] font-regular">Date</span>
                                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="6" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                        <polyline points="6 9 12 15 18 9"></polyline>
                                    </svg>
                                </button>
                                <div id="dateDropdown" class="dropdown-content">
                                    <a href="?date_filter=today">Today</a>
                                    <a href="?date_filter=last7days">Last 7 days</a>
                                    <a href="?date_filter=last28days">Last 28 days</a>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="flex items-center gap-1 cursor-pointer" onclick="clearFilters()">
                        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                            <path d="M18 6 6 18"></path>
                            <path d="m6 6 12 12"></path>
                        </svg>
                        <span class="text-[#262626] text-[14px] font-regular">Clear filter</span>
                    </div>
                </div>
            </div>

            <div class="overflow-x-auto mt-3 min-h-[20rem]">
                <table class="w-full shrink-0">
                    <thead class="w-full bg-[#E7E7E7] text-[#262626] text-[15px] md:text-[16px] font-regular text-left border-b-1 border-[#E1E1E1]">
                        <tr>
                            <th class="text-nowrap p-2 flex items-center gap-2">
                                <input type="checkbox" id="selectAll" />
                                <span class="text-[#262626] text-[13px] md:text-[15px] font-medium">Customer Name</span>
                            </th>
                            <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium">Email</th>
                            <?php if ($hasProfileTable): ?>
                            <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium">Phone</th>
                            <?php endif; ?>
                            <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium">Status</th>
                            <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium">Last Login</th>
                            <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium">
                                <svg xmlns="http://www.w3.org/2000/svg" width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="min-w-[24px] min-h-[24px]">
                                    <rect x="3" y="3" width="7" height="7"></rect>
                                    <rect x="14" y="3" width="7" height="7"></rect>
                                    <rect x="3" y="14" width="7" height="7"></rect>
                                    <rect x="14" y="14" width="7" height="7"></rect>
                                </svg>
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                    <?php if (count($users) > 0): ?>
                        <?php foreach ($users as $user): ?>
                            <tr>
                                <td class="flex items-center gap-[0px] p-3">
                                    <input type="checkbox" class="user-checkbox border-[#E1E1E1]" value="<?= $user['id'] ?>" />
                                    <span class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-regular px-2">
                                        <?= htmlspecialchars(getUserName($user)) ?>
                                    </span>
                                </td>
                                <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-regular px-2">
                                    <?= htmlspecialchars($user['email']) ?>
                                </td>
                                <?php if ($hasProfileTable): ?>
                                <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-regular px-2">
                                    <?= htmlspecialchars($user['phone'] ?? 'N/A') ?>
                                </td>
                                <?php endif; ?>
                                <td class="text-nowrap text-[#262626] text-[15px] md:text-[16px] font-regular px-2">
                                    <span class="<?= $user['status'] == 'active' ? 'text-green-600' : 'text-yellow-600' ?>">
                                        <?= htmlspecialchars(ucfirst($user['status'])) ?>
                                    </span>
                                </td>
                                <td class="text-[#262626] text-[15px] md:text-[16px] font-regular px-2">
                                    <?= formatDate($user['last_login']) ?>
                                </td>
                                <td class="relative">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="w-[20px] cursor-pointer" onclick="openOrdermenu(this)">
                                        <circle cx="12" cy="12" r="1"></circle>
                                        <circle cx="12" cy="5" r="1"></circle>
                                        <circle cx="12" cy="19" r="1"></circle>
                                    </svg>

                                    <!-- Order Menu (specific to this row) -->
                                    <div class="ordermenu-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                        <div class="flex flex-col gap-3">
                                            <span id="viewUser_<?= $user['id'] ?>" class="text-[16px] font-medium text-[#262626] cursor-pointer">View Details</span>
                                            <span id="toggleStatus_<?= $user['id'] ?>" class="text-[16px] font-medium text-[#E8B006] cursor-pointer">
                                                <?= $user['status'] == 'active' ? 'Disable account' : 'Enable account' ?>
                                            </span>
                                            <span id="deleteUser_<?= $user['id'] ?>" class="text-[16px] font-medium text-red-600 cursor-pointer">Delete account</span>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="<?= $hasProfileTable ? 6 : 5 ?>" class="p-4 text-center text-gray-500">No users found</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="w-[90%] md:w-full py-2 mx-auto flex flex-col gap-2 md:flex-row md:items-center justify-between">
            <span class="text-[#262626] text-[13px] md:text-[14px] font-regular cursor-pointer">
                Showing <?= count($users) ?> results from <?= $totalUsers ?>
            </span>
            <div class="w-full md:w-[fit-content] ml-auto flex items-center justify-between gap-5">
                <div class="flex items-center gap-2 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="6" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="15 18 9 12 15 6"></polyline>
                    </svg>
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-regular">Prev</span>
                </div>

                <div class="w-full flex items-center justify-between md:gap-6">
                    <span class="text-[#FFFFFF] rounded-[50%] py-1 px-[10px] text-[13px] md:text-[14px] font-regular cursor-pointer bg-blue-900">1</span>
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-regular cursor-pointer">2</span>
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-regular cursor-pointer">3</span>
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-regular cursor-pointer">4</span>
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-regular cursor-pointer">5</span>
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-regular cursor-pointer">...</span>
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-regular cursor-pointer">10</span>
                </div>

                <div class="flex items-center gap-2 cursor-pointer">
                    <svg xmlns="http://www.w3.org/2000/svg" width="6" height="11" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                        <polyline points="9 18 15 12 9 6"></polyline>
                    </svg>
                    <span class="text-[#262626] text-[13px] md:text-[14px] font-regular">Next</span>
                </div>
            </div>
        </div>
    </div>

    <!-- The delete account modal -->
    <div id="ticket" class="modal ticket">
        <div class="modal-content overflow-hidden px-5 py-10 flex flex-col">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" id="closeticket" class="w-[26px] md:w-[32px] cursor-pointer absolute top-10 right-4">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="m15 9-6 6"></path>
                <path d="m9 9 6 6"></path>
            </svg>

            <p class="text-red-600 text-[19px] md:text-[24px] font-medium text-center">
                Delete Account
            </p>

            <p class="w-[95%] md:w-[67%] mx-auto text-[15px] text-left md:text-[16px] font-medium text-[#262626] mt-2">
                This action is irreversible! Are you sure you want to proceed?
            </p>
            <p class="w-[95%] md:w-[67%] mx-auto text-[15px] text-left md:text-[16px] font-medium text-[#262626] mt-2">
                What happens when you delete a user account
            </p>

            <p class="w-[95%] md:w-[67%] mx-auto text-[15px] text-left md:text-[16px] font-regular text-[#777777] mt-2">
                • The account loses access permanently.<br />
                • All orders, saved items, and account data are erased.<br />
                • Any pending orders or transactions are automatically canceled.<br />
                • Reviews and ratings left by the user may also be removed.<br />
                • The user will be notified via email about their account status.
            </p>

            <div class="w-[95%] md:w-[67%] item mx-auto flex items-center gap-2 mt-2">
                <input type="checkbox" id="dontShowAgainDelete" />
                <p class="text-[15px] text-left md:text-[16px] font-medium text-[#262626]">
                    Don't see this again
                </p>
            </div>

            <form method="post" id="deleteForm">
                <input type="hidden" name="user_id" id="deleteUserId">
                <button type="submit" name="delete_user" class="w-full md:w-[67%] mx-auto text-[16px] font-regular py-2 px-6 bg-red-600 text-white rounded-[8px] mt-10 cursor-pointer">
                    Yes, Delete Account
                </button>
            </form>
        </div>
    </div>

    <!-- The disable account modal -->
    <div id="disable" class="modal ticket">
        <div class="modal-content overflow-hidden px-5 py-10 flex flex-col">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" id="closedisable" class="w-[26px] md:w-[32px] cursor-pointer absolute top-10 right-4">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="m15 9-6 6"></path>
                <path d="m9 9 6 6"></path>
            </svg>

            <p id="disableTitle" class="text-[#E8B006] text-[19px] md:text-[24px] font-medium text-center">
                Disable Account
            </p>

            <p id="disableMessage" class="w-[95%] md:w-[67%] mx-auto text-[15px] text-left md:text-[16px] font-medium text-[#262626] mt-2">
                Are you sure you want to disable this account? You can reactivate it anytime.
            </p>
            <p id="disableDescription" class="w-[95%] md:w-[67%] mx-auto text-[15px] text-left md:text-[16px] font-medium text-[#262626] mt-2">
                What happens when you disable a user account?
            </p>

            <p id="disableEffects" class="w-[95%] md:w-[67%] mx-auto text-[15px] text-left md:text-[16px] font-regular text-[#777777] mt-2">
                • The user cannot log in or access their account.<br />
                • Their orders, reviews, and history remain stored.<br />
                • Admins can reactivate the account at any time.<br />
                • The user will be notified via email about their account status.
            </p>

            <div class="w-[95%] md:w-[67%] item mx-auto flex items-center gap-2 mt-2">
                <input type="checkbox" id="dontShowAgainDisable" />
                <p class="text-[15px] text-left md:text-[16px] font-medium text-[#262626]">
                    Don't see this again
                </p>
            </div>

            <form method="post" id="toggleForm">
                <input type="hidden" name="user_id" id="toggleUserId">
                <input type="hidden" name="current_status" id="toggleUserStatus">
                <button type="submit" id="toggleButton" name="toggle_status" class="w-full md:w-[67%] mx-auto text-[16px] font-regular py-2 px-6 bg-[#E8B006] text-white rounded-[8px] mt-10 cursor-pointer">
                    Yes, Disable Account
                </button>
            </form>
        </div>
    </div>

    <!-- User details view (a simple modal for now) -->
    <div id="customeroverview" class="modal">
        <div class="modal-content overflow-hidden px-5 py-10 flex flex-col">
            <svg xmlns="http://www.w3.org/2000/svg" width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" id="closecustomeroverview" class="w-[26px] md:w-[32px] cursor-pointer absolute top-10 right-4">
                <circle cx="12" cy="12" r="10"></circle>
                <path d="m15 9-6 6"></path>
                <path d="m9 9 6 6"></path>
            </svg>

            <p class="text-blue-900 text-[19px] md:text-[24px] font-medium text-center mb-4">
                Customer Details
            </p>

            <div id="userDetailsContainer">
                <!-- User details will be loaded here -->
                <div class="text-center py-4">
                    <p>Loading user details...</p>
                </div>
            </div>
        </div>
    </div>

    <script>
        // Toggle dropdown
        function toggleDropdown() {
            document.getElementById("dateDropdown").classList.toggle("show");
        }

        // Close dropdown when clicking outside
        window.onclick = function(event) {
            if (!event.target.matches('.dropdown button')) {
                var dropdowns = document.getElementsByClassName("dropdown-content");
                for (var i = 0; i < dropdowns.length; i++) {
                    var openDropdown = dropdowns[i];
                    if (openDropdown.classList.contains('show')) {
                        openDropdown.classList.remove('show');
                    }
                }
            }
            
            // Close modals when clicking outside
            if (event.target == ticket) {
                ticket.style.display = "none";
            }
            if (event.target == disable) {
                disable.style.display = "none";
            }
            if (event.target == customeroverview) {
                customeroverview.style.display = "none";
            }
        }

        // Clear filters
        function clearFilters() {
            window.location.href = '<?= $_SERVER['PHP_SELF'] ?>';
        }

        // Select all checkbox functionality
        document.getElementById('selectAll').addEventListener('change', function() {
            const checkboxes = document.querySelectorAll('.user-checkbox');
            checkboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
        });

        // Search functionality
        document.getElementById('searchInput').addEventListener('keyup', function() {
            const searchValue = this.value.toLowerCase();
            const rows = document.querySelectorAll('tbody tr');
            
            rows.forEach(row => {
                if (!row.querySelector('td:first-child span')) return;
                
                const name = row.querySelector('td:first-child span').textContent.toLowerCase();
                const email = row.querySelector('td:nth-child(2)').textContent.toLowerCase();
                
                if (name.includes(searchValue) || email.includes(searchValue)) {
                    row.style.display = '';
                } else {
                    row.style.display = 'none';
                }
            });
        });

        // Open/close action menu for each row
        function openOrdermenu(element) {
            // Close all open menus first
            document.querySelectorAll('.ordermenu-content').forEach(menu => {
                menu.style.display = 'none';
            });
            
            // Open this menu
            const menu = element.nextElementSibling;
            menu.style.display = 'block';
            
            // Close when clicking outside
            document.addEventListener('click', function closeMenu(e) {
                if (!menu.contains(e.target) && e.target !== element) {
                    menu.style.display = 'none';
                    document.removeEventListener('click', closeMenu);
                }
            });
        }

        // Modal functionality
        var ticket = document.getElementById("ticket");
        var closeticket = document.getElementById("closeticket");
        var disable = document.getElementById("disable");
        var closedisable = document.getElementById("closedisable");
        var customeroverview = document.getElementById("customeroverview");
        var closecustomeroverview = document.getElementById("closecustomeroverview");

        closeticket.onclick = function() {
            ticket.style.display = "none";
        }

        closedisable.onclick = function() {
            disable.style.display = "none";
        }

        closecustomeroverview.onclick = function() {
            customeroverview.style.display = "none";
        }

        // Setup action event listeners
        document.addEventListener('DOMContentLoaded', function() {
            // Setup for each user row
            <?php foreach ($users as $user): ?>
                // View user details
                var viewUserBtn = document.getElementById('viewUser_<?= $user['id'] ?>');
                if (viewUserBtn) {
                    viewUserBtn.addEventListener('click', function() {
                        // Display user details
                        const userDetailsHtml = `
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                                <div>
                                    <h3 class="text-[16px] font-bold mb-2">Basic Information</h3>
                                    <p><strong>ID:</strong> <?= htmlspecialchars($user['id']) ?></p>
                                    <p><strong>Name:</strong> <?= htmlspecialchars(getUserName($user)) ?></p>
                                    <p><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></p>
                                    <?php if ($hasProfileTable): ?>
                                    <p><strong>Phone:</strong> <?= htmlspecialchars($user['phone'] ?? 'N/A') ?></p>
                                    <?php endif; ?>
                                    <p><strong>Status:</strong> <span class="<?= $user['status'] == 'active' ? 'text-green-600' : 'text-yellow-600' ?>"><?= ucfirst($user['status']) ?></span></p>
                                </div>
                                <div>
                                    <h3 class="text-[16px] font-bold mb-2">Account Information</h3>
                                    <p><strong>IP Address:</strong> <?= htmlspecialchars($user['ip']) ?></p>
                                    <p><strong>Last Login:</strong> <?= formatDate($user['last_login']) ?></p>
                                    <p><strong>Verified At:</strong> <?= formatDate($user['verified_at']) ?></p>
                                </div>
                            </div>
                        `;
                        
                        // Set the HTML content
                        document.getElementById('userDetailsContainer').innerHTML = userDetailsHtml;
                        
                        // Show the modal
                        customeroverview.style.display = "block";
                    });
                }

                // Toggle user status
                var toggleStatusBtn = document.getElementById('toggleStatus_<?= $user['id'] ?>');
                if (toggleStatusBtn) {
                    toggleStatusBtn.addEventListener('click', function() {
                        const isActive = '<?= $user['status'] ?>' === 'active';
                        
                        // Set user ID and status
                        document.getElementById('toggleUserId').value = <?= $user['id'] ?>;
                        document.getElementById('toggleUserStatus').value = '<?= $user['status'] ?>';
                        
                        // Update modal content based on current status
                        if (isActive) {
                            document.getElementById('disableTitle').textContent = 'Disable Account';
                            document.getElementById('disableTitle').className = 'text-[#E8B006] text-[19px] md:text-[24px] font-medium text-center';
                            document.getElementById('disableMessage').textContent = 'Are you sure you want to disable this account? You can reactivate it anytime.';
                            document.getElementById('disableDescription').textContent = 'What happens when you disable a user account?';
                            document.getElementById('disableEffects').innerHTML = `
                                • The user cannot log in or access their account.<br />
                                • Their orders, reviews, and history remain stored.<br />
                                • Admins can reactivate the account at any time.<br />
                                • The user will be notified via email about their account status.
                            `;
                            document.getElementById('toggleButton').textContent = 'Yes, Disable Account';
                            document.getElementById('toggleButton').className = 'w-full md:w-[67%] mx-auto text-[16px] font-regular py-2 px-6 bg-[#E8B006] text-white rounded-[8px] mt-10 cursor-pointer';
                        } else {
                            document.getElementById('disableTitle').textContent = 'Enable Account';
                            document.getElementById('disableTitle').className = 'text-green-600 text-[19px] md:text-[24px] font-medium text-center';
                            document.getElementById('disableMessage').textContent = 'Are you sure you want to enable this account?';
                            document.getElementById('disableDescription').textContent = 'What happens when you enable a user account?';
                            document.getElementById('disableEffects').innerHTML = `
                                • The user can log in and access their account again.<br />
                                • They regain access to their orders, saved items, and account data.<br />
                                • Any previous reviews and ratings will be visible again.<br />
                                • The user will be notified via email about their account status.
                            `;
                            document.getElementById('toggleButton').textContent = 'Yes, Enable Account';
                            document.getElementById('toggleButton').className = 'w-full md:w-[67%] mx-auto text-[16px] font-regular py-2 px-6 bg-green-600 text-white rounded-[8px] mt-10 cursor-pointer';
                        }
                        
                        // Show the modal
                        disable.style.display = "block";
                    });
                }

                // Delete user
                var deleteUserBtn = document.getElementById('deleteUser_<?= $user['id'] ?>');
                if (deleteUserBtn) {
                    deleteUserBtn.addEventListener('click', function() {
                        // Set user ID for deletion
                        document.getElementById('deleteUserId').value = <?= $user['id'] ?>;
                        
                        // Show delete confirmation modal
                        ticket.style.display = "block";
                    });
                }
            <?php endforeach; ?>
        });
    </script>
</body>
</html>