<?php
if (!isset($con)) {
    include('../../config/connect.php');
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['cat_title'])) {
    $category_title = $_POST['cat_title'];

    // Check if category already exists
    $select_query = "SELECT * FROM categories WHERE category_title='$category_title'";
    $result_select = mysqli_query($con, $select_query);
    $number = mysqli_num_rows($result_select);

    if ($number > 0) {
        echo "<script>alert('Category already present');</script>";
    } else {
        // File upload handling
        $category_image = "";
        if(isset($_FILES['cat_image']) && $_FILES['cat_image']['error'] == 0) {
            $upload_dir = "../../assets/categories/";
            
            // Create directory if it doesn't exist
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            // Get file info
            $file_name = basename($_FILES["cat_image"]["name"]);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            // Generate a unique filename
            $new_file_name = uniqid() . '_' . time() . '.' . $file_ext;
            $target_file = $upload_dir . $new_file_name;
            
            // Allowed file types
            $allowed_types = array('jpg', 'jpeg', 'png', 'gif', 'svg');
            
            // Validate file type
            if(in_array($file_ext, $allowed_types)) {
                // Move uploaded file
                if(move_uploaded_file($_FILES["cat_image"]["tmp_name"], $target_file)) {
                    $category_image = $new_file_name;
                } else {
                    echo "<script>alert('Sorry, there was an error uploading your file.');</script>";
                }
            } else {
                echo "<script>alert('Sorry, only JPG, JPEG, PNG, GIF, and SVG files are allowed.');</script>";
            }
        }
        
        // Insert category with image
        if($category_image != "") {
            $insert_query = "INSERT INTO categories (category_title, category_image) VALUES ('$category_title', '$category_image')";
        } else {
            $insert_query = "INSERT INTO categories (category_title) VALUES ('$category_title')";
        }
        
        $result = mysqli_query($con, $insert_query);

        if ($result) {
            echo "<script>alert('Category created successfully');</script>";
        } else {
            echo "<script>alert('Error creating category: " . mysqli_error($con) . "');</script>";
        }
    }

    // Prevent form resubmission
    echo "<script>
        if (window.history.replaceState) {
            window.history.replaceState(null, null, window.location.href);
        }
    </script>";
}
?>

<div id="createCategory" class="modal createCategory">
    <!-- Modal content -->
    <div class="modal-content overflow-hidden p-4">
        <h1 class="text-[20px] text-[#262626] font-Onest font-medium text-center">Add Category</h1>
        <img src="../assets/global/close-circle.svg" alt="close" id="closeCC" class="w-[24px] md:w-[27px] cursor-pointer absolute top-4 right-4" />

        <form method="post" enctype="multipart/form-data">
            <div class="flex flex-col gap-4">
                <!-- Category Name -->
                <div class="flex flex-col gap-2">
                    <label class="font-[#2c2c2c] font-['Open Sans] text-[16px] font-medium">Category Name</label>
                    <input type="text" name="cat_title" placeholder="Enter category name" required class="p-2 placeholder:text-[#D9D9D9] border-[#E1E1E1] border-[1px] outline-none font-[#2c2c2c] font-['Open Sans] text-[16px]" />
                </div>
                
                <!-- Image Upload -->
                <div class="flex flex-col gap-2">
                    <label class="font-[#2c2c2c] font-['Open Sans] text-[16px] font-medium">Category Image</label>
                    <div class="flex flex-col gap-2">
                        <div class="w-full h-[120px] border-[1px] border-dashed border-[#E1E1E1] rounded-lg flex items-center justify-center relative">
                            <input type="file" name="cat_image" id="cat_image" accept="image/*" class="opacity-0 absolute inset-0 w-full h-full cursor-pointer z-10" onchange="previewImage(this)" />
                            <div id="upload-placeholder" class="flex flex-col items-center justify-center gap-2">
                                <img src="../../assets/global/folder-2.svg" alt="upload" class="w-[24px] h-[24px]" />
                                <span class="text-[14px] text-[#9A9A9A] font-['Open Sans']">Click to upload or drag and drop</span>
                                <span class="text-[12px] text-[#9A9A9A] font-['Open Sans']">SVG, PNG, JPG or GIF (max. 2MB)</span>
                            </div>
                            <div id="image-preview" class="hidden w-full h-full">
                                <img id="preview-img" src="#" alt="Preview" class="w-full h-full object-contain" />
                            </div>
                        </div>
                    </div>
                </div>
                
                <button type="submit" class="w-full text-center gap-2 px-4 py-2 bg-blue-900 text-white rounded-lg cursor-pointer">
                    Add
                </button>
            </div>
        </form>

        <button id="openCategoriesLists" class="ml-4 md:ml-0 w-[fit-content] shrink-0 flex items-center gap-2 px-4 py-2 bg-[#F3F3F3] text-[#262626] rounded-lg cursor-pointer mt-3">
            View all Categories
            <img src="../../assets/home/arrow-right.svg" />
        </button>
    </div>
</div>

<script>
// Image preview functionality
function previewImage(input) {
    const uploadPlaceholder = document.getElementById('upload-placeholder');
    const imagePreview = document.getElementById('image-preview');
    const previewImg = document.getElementById('preview-img');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            previewImg.src = e.target.result;
            uploadPlaceholder.classList.add('hidden');
            imagePreview.classList.remove('hidden');
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

// Reset form on modal close to clear image preview
document.getElementById('closeCC').addEventListener('click', function() {
    const form = document.querySelector('#createCategory form');
    const uploadPlaceholder = document.getElementById('upload-placeholder');
    const imagePreview = document.getElementById('image-preview');
    
    form.reset();
    uploadPlaceholder.classList.remove('hidden');
    imagePreview.classList.add('hidden');
});
</script>