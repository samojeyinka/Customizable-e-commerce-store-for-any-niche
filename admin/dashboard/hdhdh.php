   <div style="margin-top: 20px;">
                    <h3 style="margin: 0 0 16px 0; font-size: 16px;">Revenue Distribution</h3>
                    
                    <?php if (count($mainStats) >= 2): 
                        $lagos_revenue = $mainStats[0]["revenue"];
                        $non_lagos_revenue = $mainStats[1]["revenue"];
                        $total_revenue = $lagos_revenue + $non_lagos_revenue;
                        $lagos_percentage = ($total_revenue > 0) ? ($lagos_revenue / $total_revenue * 100) : 0;
                        $non_lagos_percentage = 100 - $lagos_percentage;
                    ?>
                    <div style="background-color: #f0f0f0; height: 24px; border-radius: 12px; overflow: hidden; margin-bottom: 8px;">
                        <div style="width: <?php echo $lagos_percentage; ?>%; height: 100%; background-color: #39D959; float: left;"></div>
                        <div style="width: <?php echo $non_lagos_percentage; ?>%; height: 100%; background-color: #4B4B4B; float: left;"></div>
                    </div>
                    <div style="display: flex; justify-content: space-between;">
                        <div>
                            <span style="display: inline-block; width: 12px; height: 12px; background-color: #39D959; margin-right: 4px;"></span>
                            Lagos Store (<?php echo round($lagos_percentage); ?>%)
                        </div>
                        <div>
                            <span style="display: inline-block; width: 12px; height: 12px; background-color: #4B4B4B; margin-right: 4px;"></span>
                            Non-Lagos (<?php echo round($non_lagos_percentage); ?>%)
                        </div>
                    </div>
                    <?php endif; ?>
                </div>