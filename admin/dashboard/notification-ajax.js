/**
 * Enhanced notification AJAX functionality
 * This script adds AJAX support to avoid page reloads when performing actions
 */
document.addEventListener('DOMContentLoaded', function() {
    // Set up AJAX for mark as read/unread actions
    setupNotificationAjax();
    
    // Set up AJAX for real-time notification count updates
    setupNotificationCounter();
});

/**
 * Set up AJAX for notification actions
 */
function setupNotificationAjax() {
    // Delegate event listener for all notification action links
    document.addEventListener('click', function(event) {
        // Check if the clicked element is a notification action link
        const actionLink = event.target.closest('a[href*="notification_action"]');
        
        if (actionLink) {
            event.preventDefault();
            
            // Extract action URL
            const actionUrl = actionLink.getAttribute('href');
            
            // Make AJAX request
            fetch(actionUrl, {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(() => {
                // Update UI based on action
                const action = actionUrl.includes('action=read') ? 'read' : 'unread';
                const notificationItem = actionLink.closest('.w-full.flex.flex-col.gap-2.rounded-\\[4px\\]');
                
                if (notificationItem) {
                    if (action === 'read') {
                        notificationItem.classList.remove('bg-[#EEEEEE]');
                        notificationItem.classList.add('bg-white');
                        
                        // Update the action link to "Mark as unread"
                        actionLink.textContent = 'Mark as unread';
                        actionLink.setAttribute('href', actionLink.getAttribute('href').replace('action=read', 'action=unread'));
                    } else {
                        notificationItem.classList.remove('bg-white');
                        notificationItem.classList.add('bg-[#EEEEEE]');
                        
                        // Update the action link to "Mark as read"
                        actionLink.textContent = 'Mark as read';
                        actionLink.setAttribute('href', actionLink.getAttribute('href').replace('action=unread', 'action=read'));
                    }
                    
                    // Close the dropdown menu
                    const dropdownMenu = actionLink.closest('.not-content');
                    if (dropdownMenu) {
                        dropdownMenu.style.display = 'none';
                    }
                    
                    // Update notification counter
                    updateNotificationCounter();
                }
            })
            .catch(error => {
                console.error('Error updating notification:', error);
            });
        }
    });
    
    // Handle "Mark all as read" button
    const markAllReadBtn = document.querySelector('a[href*="notification_action=read_all"]');
    if (markAllReadBtn) {
        markAllReadBtn.addEventListener('click', function(event) {
            event.preventDefault();
            
            // Make AJAX request
            fetch(this.getAttribute('href'), {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.text())
            .then(() => {
                // Update all unread notifications to read
                document.querySelectorAll('.bg-[#EEEEEE]').forEach(item => {
                    item.classList.remove('bg-[#EEEEEE]');
                    item.classList.add('bg-white');
                    
                    // Update action links
                    const actionLink = item.querySelector('a[href*="action=read"]');
                    if (actionLink) {
                        actionLink.textContent = 'Mark as unread';
                        actionLink.setAttribute('href', actionLink.getAttribute('href').replace('action=read', 'action=unread'));
                    }
                });
                
                // Update notification counter
                updateNotificationCounter(0);
            })
            .catch(error => {
                console.error('Error marking all as read:', error);
            });
        });
    }
}

/**
 * Set up notification counter updates
 */
function setupNotificationCounter() {
    // Periodically check for new notifications
    setInterval(updateNotificationCounter, 60000); // Check every minute
}

/**
 * Update notification counter in the header
 */
function updateNotificationCounter(count = null) {
    // Find notification counter in header
    const notificationCounter = document.querySelector('.header-notification-counter');
    
    if (notificationCounter) {
        if (count !== null) {
            // Update with provided count
            if (count > 0) {
                notificationCounter.textContent = count > 99 ? '99+' : count;
                notificationCounter.style.display = 'flex';
            } else {
                notificationCounter.style.display = 'none';
            }
        } else {
            // Fetch current count via AJAX
            fetch('includes/get-notification-count.php', {
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => response.json())
            .then(data => {
                if (data.count > 0) {
                    notificationCounter.textContent = data.count > 99 ? '99+' : data.count;
                    notificationCounter.style.display = 'flex';
                } else {
                    notificationCounter.style.display = 'none';
                }
            })
            .catch(error => {
                console.error('Error updating notification counter:', error);
            });
        }
    }
}