<div id="brandLists" class="modal brandLists">
    <!-- Modal content -->
    <div class="modal-content overflow-hidden p-4">
        <h1 class="text-[20px] text-[#262626] font-Onest font-medium text-center">All Tags</h1>
        <i class="fa-solid fa-xmark text-[24px] cursor-pointer absolute top-4 right-4" id="closeBrandLists" alt="close"></i>
      
        <div class="w-full overflow-x-auto mt-3">
            <table cols="" class="w-full shrink-0">
                <thead class="w-full bg-[#E7E7E7] text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-left border-b-1 border-[#E1E1E1]">
                    <th class="text-nowrap p-2 flex items-center gap-2">
                        <span class="text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Tag</span>
                    </th>
                    <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Logo</th>
                    <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Date Added</th>
                    <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">
                        <i class="fa-solid fa-table-columns text-[20px]"></i>
                    </th>
                </thead>
                <tbody>
                <?php
                $select_brands = "SELECT * FROM brands ORDER BY created_at DESC";
                $result_brands = mysqli_query($con, $select_brands);
                
                if (!$result_brands) {
                    echo "<tr><td colspan='4'>Error: " . mysqli_error($con) . "</td></tr>";
                } else {
                    if (mysqli_num_rows($result_brands) > 0) {
                        while ($row_data = mysqli_fetch_assoc($result_brands)) {
                            $brand_title = $row_data['brand_title'];
                            $brand_id = $row_data['brand_id'];
                            $brand_image = isset($row_data['brand_image']) ? $row_data['brand_image'] : '';
                            
                            // Format the date and time
                            $date_added = isset($row_data['date_added']) ? $row_data['date_added'] : '';
                            $formatted_date = '';
                            
                            if (!empty($date_added)) {
                                $date_obj = new DateTime($date_added);
                                $formatted_date = $date_obj->format('d/m/Y h:i a');
                            } else {
                                $formatted_date = "N/A";
                            }
                            ?>
                            <tr>
                                <td class="flex items-center gap-[10px] p-3">
                                    <div class="flex flex-col gap-[4px]">
                                        <span class="text-[#262626] text-[13px] md:text-[14px] font-regular font-['Open Sans']"><?php echo $brand_title; ?></span>
                                    </div>
                                </td>
                                <td class="p-2">
                                    <?php if (!empty($brand_image) && file_exists("../../assets/brands/" . $brand_image)): ?>
                                        <div class="w-[68px] h-[46px] rounded-[4px] overflow-hidden">
                                            <img src="../../assets/brands/<?php echo $brand_image; ?>" class="w-full h-full object-contain" alt="<?php echo $brand_title; ?>" />
                                        </div>
                                    <?php else: ?>
                                        <div class="w-[68px] h-[46px] rounded-[4px] overflow-hidden bg-[#F3F3F3] flex items-center justify-center">
                                            <span class="text-[10px] text-[#9A9A9A]">No logo</span>
                                        </div>
                                    <?php endif; ?>
                                </td>
                                <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-nowrap"><?php echo $formatted_date; ?></td>
                                <td class="relative">
                                    <i class="fa-solid fa-ellipsis-vertical text-[20px] cursor-pointer" onclick="openOrdermenu(this)"></i>
                                    <!-- Order Menu (specific to this row) -->
                                    <div class="ordermenu-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                        <div class="flex flex-col gap-3">
                                            <span class="text-[16px] font-medium text-[#262626] cursor-pointer" 
                                                  onclick="openEditBrand('<?php echo $brand_id; ?>', '<?php echo $brand_title; ?>', '<?php echo $brand_image; ?>')">Edit</span>
                                            <a href="?delete_brand=<?php echo $brand_id; ?>" class="text-[16px] font-medium text-[#D93939]" 
                                              onclick="return confirm('Are you sure you want to delete this brand?')">Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='4' class='p-3 text-center'>No Tags found</td></tr>";
                    }
                }
                ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

