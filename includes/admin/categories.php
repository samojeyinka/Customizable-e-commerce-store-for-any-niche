<div id="categoryLists" class="modal categoryLists">
    <!-- Modal content -->
    <div class="modal-content overflow-hidden p-4">
        <h1 class="text-[20px] text-[#262626] font-Onest font-medium text-center">All Categories</h1>
        <i class="fa-solid fa-xmark text-[24px] cursor-pointer absolute top-4 right-4" id="closeCategoriesLists" alt="close"></i>

      
        <div class="w-full overflow-x-auto mt-3">
                <table cols="" class="w-full shrink-0">
                    <thead class="w-full bg-[#E7E7E7] text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-left border-b-1 border-[#E1E1E1]">
                        <thead class="w-full bg-[#E7E7E7] text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-left border-b-1 border-[#E1E1E1]">
                            <th class="text-nowrap p-2 flex items-center gap-2">
                                <input type="checkbox" />
                                <span class="text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Category</span>
                            </th>
                            <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">Date Added</th>
                            <th class="text-nowrap text-[#262626] text-[13px] md:text-[15px] font-medium font-['Open Sans']">
                                <i class="fa-solid fa-table-columns text-[20px]"></i>
                            </th>
                        </thead>

              <tbody>
              <?php
                $select_categories = "SELECT * FROM categories";
                $result_categories = mysqli_query($con, $select_categories);
                
                if (!$result_categories) {
                    echo "<tr><td colspan='4'>Error: " . mysqli_error($con) . "</td></tr>";
                } else {
                    if (mysqli_num_rows($result_categories) > 0) {
                        while ($row_data = mysqli_fetch_assoc($result_categories)) {
                            $category_title = $row_data['category_title'];
                            $category_id = $row_data['category_id'];
                            $category_image = isset($row_data['category_image']) ? $row_data['category_image'] : '';
                            ?>
                            <tr>
                            
                              


                                <td class="flex items-center gap-[10px] p-3">
                                    <input type="checkbox" class="border-[#E1E1E1]" />
                                    <div class="flex items-center gap-2">
                                    <?php if (!empty($category_image) && file_exists("../../assets/categories/" . $category_image)): ?>
                                        <div class="w-[68px] h-[46px] rounded-[4px] overflow-hidden">
                                            <img src="../../assets/categories/<?php echo $category_image; ?>" class="w-full h-full object-cover" alt="<?php echo $category_title; ?>" />
                                        </div>
                                    <?php else: ?>
                                        <div class="w-[68px] h-[46px] rounded-[4px] overflow-hidden bg-[#F3F3F3] flex items-center justify-center">
                                            <span class="text-[10px] text-[#9A9A9A]">No image</span>
                                        </div>
                                    <?php endif; ?>
                                        <div class="flex flex-col gap-[4px]">
                                            <span class="text-[#262626] text-[13px] md:text-[14px] font-regular font-['Open Sans']"><?php echo $category_title; ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td class="text-[#262626] text-[15px] md:text-[16px] font-['Open Sans'] font-regular text-nowrap">12/02/2045 09:00am</td>
                                <td class="relative">
                                    <i class="fa-solid fa-ellipsis-vertical text-[20px] cursor-pointer" onclick="openOrdermenu(this)"></i>
                                    <!-- Order Menu (specific to this row) -->
                                    <div class="ordermenu-content h-full bg-white border-[1px] border-[#E1E1E1] shadow-md p-4 rounded-[4px]">
                                        <div class="flex flex-col gap-3">
                                            <span class="text-[16px] font-medium text-[#262626] cursor-pointer" 
                                                  onclick="openEditCategory('<?php echo $category_id; ?>', '<?php echo $category_title; ?>', '<?php echo $category_image; ?>')">Edit</span>
                                            <a href="?delete_category=<?php echo $category_id; ?>" class="text-[16px] font-medium text-[#D93939]" 
                                              onclick="return confirm('Are you sure you want to delete this category?')">Delete</a>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                            <?php
                        }
                    } else {
                        echo "<tr><td colspan='4' class='p-3 text-center'>No categories found</td></tr>";
                    }
                }
                ?>
              </tbody>
                </table>
            </div>

    </div>
</div>