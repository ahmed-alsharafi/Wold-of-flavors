
<?php
require_once '../includes/config.php';


if(!is_logged_in() || !is_admin()) {
    header('Location: ../login.php');
    exit();
}

if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage-recipes.php');
    exit();
}

$recipe_id = intval($_GET['id']);


try {
    $stmt = $pdo->prepare("SELECT * FROM recipes WHERE id = ?");
    $stmt->execute([$recipe_id]);
    $recipe = $stmt->fetch();
    
    if(!$recipe) {
        header('Location: manage-recipes.php');
        exit();
    }
} catch(PDOException $e) {
    die("خطأ في قاعدة البيانات: " . $e->getMessage());
}

$page_title = 'تعديل وصفة';
require_once 'header.php';


$errors = [];
$success = false;

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $title = clean_input($_POST['title']);
    $description = clean_input($_POST['description']);
    $ingredients = clean_input($_POST['ingredients']);
    $instructions = clean_input($_POST['instructions']);
    $prep_time = intval($_POST['prep_time']);
    $category = clean_input($_POST['category']);
    $difficulty = clean_input($_POST['difficulty']);
    

    if(empty($title)) {
        $errors[] = 'العنوان مطلوب';
    }
    
    if(empty($ingredients)) {
        $errors[] = 'المكونات مطلوبة';
    }
    
    if(empty($instructions)) {
        $errors[] = 'طريقة التحضير مطلوبة';
    }
    
    if($prep_time <= 0) {
        $errors[] = 'وقت التحضير غير صحيح';
    }
    

    $image_name = $recipe['image'];
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024; // 5MB
        
        if(in_array($_FILES['image']['type'], $allowed_types)) {
            if($_FILES['image']['size'] <= $max_size) {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image_name = uniqid() . '_' . time() . '.' . $ext;
                $upload_path = '../uploads/' . $image_name;
                
                if(!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $errors[] = 'فشل في رفع الصورة';
                } else {
                    // حذف الصورة القديمة إذا كانت موجودة
                    if(!empty($recipe['image']) && file_exists('../uploads/' . $recipe['image'])) {
                        unlink('../uploads/' . $recipe['image']);
                    }
                }
            } else {
                $errors[] = 'حجم الصورة يجب أن يكون أقل من 5MB';
            }
        } else {
            $errors[] = 'نوع الصورة غير مدعوم. المسموح: JPG, PNG, GIF, WebP';
        }
    }
    

    if(empty($errors)) {
        try {
            $sql = "UPDATE recipes SET title = ?, description = ?, ingredients = ?, instructions = ?, 
                    image = ?, prep_time = ?, category = ?, difficulty = ? WHERE id = ?";
            
            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                $title,
                $description,
                $ingredients,
                $instructions,
                $image_name,
                $prep_time,
                $category,
                $difficulty,
                $recipe_id
            ]);
            
            $success = true;
            
            echo show_message('success', 'تم تحديث الوصفة بنجاح! <a href="../recipe-detail.php?id=' . $recipe_id . '">عرض الوصفة</a>');
            
        } catch(PDOException $e) {
            $errors[] = 'خطأ في تحديث الوصفة: ' . $e->getMessage();
        }
    }
    

    if(!empty($errors)) {
        foreach($errors as $error) {
            echo show_message('error', $error);
        }
    }
}


if($success) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM recipes WHERE id = ?");
        $stmt->execute([$recipe_id]);
        $recipe = $stmt->fetch();
    } catch(PDOException $e) {
        die("خطأ في قاعدة البيانات: " . $e->getMessage());
    }
}
?>

<div class="edit-recipe-page" style="direction: rtl; padding: 20px;">
    <div style="color: black; padding: 30px; border-radius: 10px; margin-bottom: 30px;">
        <h1 style="text-align: center; margin-bottom: 10px;font-family: 'Tajawal',sans-serif;">
            <i class="fas fa-edit"></i> تعديل وصفة
        </h1>
        <p style="text-align: center; color: #666;font-family: 'Tajawal',sans-serif;">
            تعديل وصفة: <?php echo $recipe['title']; ?>
        </p>
    </div>

    <?php if(!$success): ?>
    <form method="POST" action="" enctype="multipart/form-data" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
        <!-- معلومات الوصفة -->
        <div style="margin-bottom: 30px;">
            <h3 style="color: #333; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #a72828ff;font-family: 'Tajawal',sans-serif;">
                <i class="fas fa-info-circle"></i> معلومات الوصفة
            </h3>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #555; font-weight: 600;font-family: 'Tajawal',sans-serif;">
                        عنوان الوصفة *
                    </label>
                    <input type="text" name="title" required 
                           value="<?php echo $_POST['title'] ?? $recipe['title']; ?>"
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;font-family: 'Tajawal',sans-serif;"
                           placeholder="عنوان الوصفة">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #555; font-weight: 600;font-family: 'Tajawal',sans-serif;">
                        وقت التحضير (دقيقة) *
                    </label>
                    <input type="number" name="prep_time" required min="1" max="480"
                           value="<?php echo $_POST['prep_time'] ?? $recipe['prep_time']; ?>"
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;font-family: 'Tajawal',sans-serif;">
                </div>
            </div>
            
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #555; font-weight: 600;font-family: 'Tajawal',sans-serif;">
                        الفئة
                    </label>
                    <select name="category" style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;font-family: 'Tajawal',sans-serif;">
                        <option value="">اختر الفئة</option>
                        <option value="أطباق رئيسية" <?php echo (($_POST['category'] ?? $recipe['category']) == 'أطباق رئيسية') ? 'selected' : ''; ?>>أطباق رئيسية</option>
                        <option value="سلطات" <?php echo (($_POST['category'] ?? $recipe['category']) == 'سلطات') ? 'selected' : ''; ?>>سلطات</option>
                        <option value="حلويات" <?php echo (($_POST['category'] ?? $recipe['category']) == 'حلويات') ? 'selected' : ''; ?>>حلويات</option>
                        <option value="شوربات" <?php echo (($_POST['category'] ?? $recipe['category']) == 'شوربات') ? 'selected' : ''; ?>>شوربات</option>
                        <option value="مقبلات" <?php echo (($_POST['category'] ?? $recipe['category']) == 'مقبلات') ? 'selected' : ''; ?>>مقبلات</option>
                        <option value="مشروبات" <?php echo (($_POST['category'] ?? $recipe['category']) == 'مشروبات') ? 'selected' : ''; ?>>مشروبات</option>
                        <option value="وجبات سريعة" <?php echo (($_POST['category'] ?? $recipe['category']) == 'وجبات سريعة') ? 'selected' : ''; ?>>وجبات سريعة</option>
                    </select>
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #555; font-weight: 600;font-family: 'Tajawal',sans-serif;">
                        مستوى الصعوبة *
                    </label>
                    <select name="difficulty" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;font-family: 'Tajawal',sans-serif;">
                        <option value="سهل" <?php echo (($_POST['difficulty'] ?? $recipe['difficulty']) == 'سهل') ? 'selected' : ''; ?>>سهل</option>
                        <option value="متوسط" <?php echo (($_POST['difficulty'] ?? $recipe['difficulty']) == 'متوسط') ? 'selected' : ''; ?>>متوسط</option>
                        <option value="صعب" <?php echo (($_POST['difficulty'] ?? $recipe['difficulty']) == 'صعب') ? 'selected' : ''; ?>>صعب</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 8px; color: #555; font-weight: 600;font-family: 'Tajawal',sans-serif;">
                    وصف مختصر
                </label>
                <textarea name="description" rows="3"
                          style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; resize: vertical;font-family: 'Tajawal',sans-serif;"
                          placeholder="وصف قصير للوصفة..."><?php echo $_POST['description'] ?? $recipe['description']; ?></textarea>
            </div>
        </div>

        <!-- المكونات -->
        <div style="margin-bottom: 30px;">
            <h3 style="color: #333; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #ff6b6b;font-family: 'Tajawal',sans-serif;">
                <i class="fas fa-shopping-basket"></i> المكونات *
            </h3>
            
            <div style="margin-bottom: 15px;">
                <textarea name="ingredients" required rows="8"
                          style="width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; resize: vertical;font-family: 'Tajawal',sans-serif;"
                          placeholder="أدخل كل مكون في سطر جديد"><?php echo $_POST['ingredients'] ?? $recipe['ingredients']; ?></textarea>
            </div>
        </div>

        <!-- طريقة التحضير -->
        <div style="margin-bottom: 30px;">
            <h3 style="color: #333; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #007bff;font-family: 'Tajawal',sans-serif;">
                <i class="fas fa-list-ol"></i> طريقة التحضير *
            </h3>
            
            <div style="margin-bottom: 15px;">
                <textarea name="instructions" required rows="10"
                          style="width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; resize: vertical; font-family: monospace;font-family: 'Tajawal',sans-serif;"
                          placeholder="أدخل كل خطوة في سطر جديد"><?php echo $_POST['instructions'] ?? $recipe['instructions']; ?></textarea>
            </div>
        </div>

        <!-- صورة الوصفة -->
        <div style="margin-bottom: 30px;">
            <h3 style="color: #333; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #6f42c1;font-family: 'Tajawal',sans-serif;">
                <i class="fas fa-camera"></i> صورة الوصفة
            </h3>
            
            <?php if(!empty($recipe['image']) && file_exists('../uploads/' . $recipe['image'])): ?>
                <div style="text-align: center; margin-bottom: 20px;">
                    <p style="color: #666; margin-bottom: 10px;font-family: 'Tajawal',sans-serif;">الصورة الحالية:</p>
                    <img src="../uploads/<?php echo $recipe['image']; ?>" 
                         alt="<?php echo $recipe['title']; ?>"
                         style="max-width: 300px; max-height: 200px; border-radius: 8px; box-shadow: 0 3px 10px rgba(0,0,0,0.1);">
                </div>
            <?php endif; ?>
            
            <div style="border: 2px dashed #ddd; padding: 30px; text-align: center; border-radius: 10px; margin-bottom: 20px;">
                <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: #6f42c1; margin-bottom: 15px;"></i>
                <p style="color: #666; margin-bottom: 20px;font-family: 'Tajawal',sans-serif;">
                    اسحب وأفلت الصورة هنا، أو انقر لاختيار ملف لتغيير الصورة
                </p>
                <input type="file" name="image" id="imageUpload" accept="image/*" 
                       style="display: none;" onchange="previewImage(this)">
                <label for="imageUpload" class="btn btn-primary" style="cursor: pointer;font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-folder-open"></i> اختر صورة جديدة
                </label>
            </div>
            
            <div id="imagePreview" style="display: none; text-align: center; margin-top: 20px;">
                <img id="preview" src="#" alt="معاينة الصورة الجديدة" 
                     style="max-width: 300px; max-height: 200px; border-radius: 8px; box-shadow: 0 3px 10px rgba(0,0,0,0.1);">
                <button type="button" onclick="removeImage()" class="btn btn-danger" style="margin-top: 10px;font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-trash"></i> إزالة الصورة الجديدة
                </button>
            </div>
        </div>

        <!-- أزرار الإرسال -->
        <div style="display: flex; justify-content: center; gap: 15px; margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee;">
            <button type="submit" class="btn btn-success" style="padding: 12px 40px; font-size: 1.1rem;font-family: 'Tajawal',sans-serif;">
                <i class="fas fa-check-circle"></i> حفظ التعديلات
            </button>
            <a href="manage-recipes.php" class="btn btn-secondary" style="font-family: 'Tajawal',sans-serif;">
                <i class="fas fa-times"></i> إلغاء
            </a>
            <a href="../recipe-detail.php?id=<?php echo $recipe['id']; ?>" class="btn btn-primary" style="font-family: 'Tajawal',sans-serif;">
                <i class="fas fa-eye"></i> عرض الوصفة
            </a>
        </div>
    </form>
    <?php endif; ?>
</div>

<script>
function previewImage(input) {
    const preview = document.getElementById('preview');
    const previewDiv = document.getElementById('imagePreview');
    
    if (input.files && input.files[0]) {
        const reader = new FileReader();
        
        reader.onload = function(e) {
            preview.src = e.target.result;
            previewDiv.style.display = 'block';
        }
        
        reader.readAsDataURL(input.files[0]);
    }
}

function removeImage() {
    document.getElementById('imageUpload').value = '';
    document.getElementById('imagePreview').style.display = 'none';
}
</script>

    <!-- jQuery -->
    <script src = "../assets/jquery/jquery-3.5.1.js"></script>
    <!-- Fontawesome js -->
    <script src = "../assets/font-awesome/all.js"></script>
    <!-- custom js file -->
    <script src="../js/script.js"></script>
  </body>
</html>




