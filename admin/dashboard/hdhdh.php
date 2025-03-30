   
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