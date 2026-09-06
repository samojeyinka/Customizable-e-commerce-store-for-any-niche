<?php
// File: admin/includes/notification-panel.php

// Include notifications functions if not already included
require_once __DIR__ . '/../../includes/notifications.php';

// Include database connection
include('../../config/connect.php');
// Include authentication utility



// Get unread count
$unread_count = get_unread_count($con, true);

// Get latest notifications for dropdown
$latest_notifications = get_notifications($con, true, null, 5, 0);
?>

<!-- Notification Icon in Header -->
<div class="relative">
    <button id="notificationButton" class="p-2 relative">
        <i class="fa-regular fa-bell text-[20px]" alt="Notifications"></i>
        <?php if ($unread_count > 0): ?>
            <span class="absolute top-1 right-1 bg-red-500 text-white rounded-full w-4 h-4 flex items-center justify-center text-[10px]">
                <?php echo $unread_count > 9 ? '9+' : $unread_count; ?>
            </span>
        <?php endif; ?>
    </button>
    
    <!-- Notification Dropdown -->
    <div id="notificationDropdown" class="hidden absolute right-0 mt-2 w-72 md:w-80 bg-white shadow-lg rounded-md p-2 z-50">
        <div class="flex items-center justify-between p-2 border-b border-[#E7E7E7]">
            <h3 class="text-[16px] font-medium">Notifications</h3>
            <?php if ($unread_count > 0): ?>
                <button id="markAllRead" class="text-[14px] text-blue-600 hover:text-blue-800">Mark all as read</button>
            <?php endif; ?>
        </div>
        
        <div class="max-h-80 overflow-y-auto">
            <?php if (empty($latest_notifications)): ?>
                <div class="p-4 text-center text-[#6B7280]">No notifications</div>
            <?php else: ?>
                <?php foreach ($latest_notifications as $notification): ?>
                    <div class="notification-item p-2 hover:bg-gray-50 border-b border-[#E7E7E7] <?php echo $notification['is_read'] ? '' : 'bg-blue-50'; ?>">
                        <div class="flex items-start gap-2">
                            <div class="w-8 h-8 rounded-full bg-blue-100 flex items-center justify-center flex-shrink-0">
                                <?php if ($notification['type'] === 'order'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-blue-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z" />
                                    </svg>
                                <?php elseif ($notification['type'] === 'return'): ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-orange-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 15v-1a4 4 0 00-4-4H8m0 0l3 3m-3-3l3-3" />
                                    </svg>
                                <?php else: ?>
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                    </svg>
                                <?php endif; ?>
                            </div>
                            <div class="flex-1">
                                <p class="text-[14px] font-medium"><?php echo htmlspecialchars($notification['title']); ?></p>
                                <p class="text-[13px] text-gray-600"><?php echo htmlspecialchars($notification['message']); ?></p>
                                <p class="text-[12px] text-gray-400 mt-1">
                                    <?php 
                                    $created_at = new DateTime($notification['created_at']);
                                    echo $created_at->format('d M, Y h:i A'); 
                                    ?>
                                </p>
                            </div>
                            <?php if (!$notification['is_read']): ?>
                                <button class="mark-read-btn p-1" data-id="<?php echo $notification['notification_id']; ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 text-gray-400 hover:text-gray-600" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
                                    </svg>
                                </button>
                            <?php endif; ?>
                        </div>
                    </div>
                <?php endforeach; ?>
                <div class="p-2 text-center">
                    <a href="notifications.php" class="text-[14px] text-blue-600 hover:text-blue-800">View all notifications</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const notificationButton = document.getElementById('notificationButton');
    const notificationDropdown = document.getElementById('notificationDropdown');
    const markAllReadButton = document.getElementById('markAllRead');
    const markReadButtons = document.querySelectorAll('.mark-read-btn');
    
    // Toggle dropdown
    notificationButton.addEventListener('click', function() {
        notificationDropdown.classList.toggle('hidden');
    });
    
    // Close dropdown when clicking outside
    document.addEventListener('click', function(event) {
        if (!notificationButton.contains(event.target) && !notificationDropdown.contains(event.target)) {
            notificationDropdown.classList.add('hidden');
        }
    });
    
    // Mark individual notification as read
    markReadButtons.forEach(button => {
        button.addEventListener('click', function() {
            const notificationId = this.getAttribute('data-id');
            markAsRead(notificationId, this);
        });
    });
    
    // Mark all as read
    if (markAllReadButton) {
        markAllReadButton.addEventListener('click', function() {
            markAllAsRead();
        });
    }
    
    // Function to mark individual notification as read
    function markAsRead(notificationId, buttonElement) {
        fetch('mark-notification-read.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/x-www-form-urlencoded',
            },
            body: 'notification_id=' + notificationId
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Find parent notification item and remove unread styling
                const notificationItem = buttonElement.closest('.notification-item');
                notificationItem.classList.remove('bg-blue-50');
                buttonElement.remove();
                
                // Update badge count
                updateBadgeCount(-1);
            }
        })
        .catch(error => console.error('Error:', error));
    }
    
    // Function to mark all notifications as read
    function markAllAsRead() {
        fetch('mark-all-notifications-read.php', {
            method: 'POST'
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Remove unread styling from all notifications
                document.querySelectorAll('.notification-item').forEach(item => {
                    item.classList.remove('bg-blue-50');
                });
                
                // Remove all mark-read buttons
                document.querySelectorAll('.mark-read-btn').forEach(btn => {
                    btn.remove();
                });
                
                // Hide the mark all read button
                markAllReadButton.style.display = 'none';
                
                // Reset badge count
                const badge = document.querySelector('#notificationButton span');
                if (badge) {
                    badge.remove();
                }
            }
        })
        .catch(error => console.error('Error:', error));
    }
    
    // Function to update badge count
    function updateBadgeCount(change) {
        const badge = document.querySelector('#notificationButton span');
        if (badge) {
            let count = parseInt(badge.textContent);
            if (isNaN(count)) {
                count = 10; // If displaying "9+", assume it's 10
            }
            
            count += change;
            
            if (count <= 0) {
                badge.remove();
                if (markAllReadButton) {
                    markAllReadButton.style.display = 'none';
                }
            } else {
                badge.textContent = count > 9 ? '9+' : count;
            }
        }
    }
});
</script>