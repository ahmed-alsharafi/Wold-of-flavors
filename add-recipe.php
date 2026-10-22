
<?php
require_once 'includes/config.php';

if(!is_logged_in()) {
    header('Location: login.php?redirect=add-recipe');
    exit();
}

$page_title = 'إضافة وصفة جديدة';
require_once 'includes/header.php';

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
    
    $image_name = '';
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
        $max_size = 5 * 1024 * 1024;
        
        if(in_array($_FILES['image']['type'], $allowed_types)) {
            if($_FILES['image']['size'] <= $max_size) {
                $ext = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
                $image_name = uniqid() . '_' . time() . '.' . $ext;
                $upload_path = 'uploads/' . $image_name;
                
                if(!move_uploaded_file($_FILES['image']['tmp_name'], $upload_path)) {
                    $errors[] = 'فشل في رفع الصورة';
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
            $sql = "INSERT INTO recipes (title, description, ingredients, instructions, image, prep_time, category, difficulty, user_id) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
            
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
                $_SESSION['user_id']
            ]);
            
            $recipe_id = $pdo->lastInsertId();
            $success = true;
            
            echo '<div style="padding: 15px; background: #d4edda; color: #155724; 
                  border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 20px;font-family: \'Tajawal\',sans-serif;">
                  تم إضافة الوصفة بنجاح! <a href="recipe-detail.php?id=' . $recipe_id . '" style="color: #0b2e13; font-weight: bold;">عرض الوصفة</a>
                  </div>';
            
            $_POST = [];
            
        } catch(PDOException $e) {
            $errors[] = 'خطأ في قاعدة البيانات: ' . $e->getMessage();
        }
    }
    
    if(!empty($errors) && !$success) {
        foreach($errors as $error) {
            echo '<div style="padding: 15px; background: #f8d7da; color: #721c24; 
                  border: 1px solid #f5c6cb; border-radius: 5px; margin-bottom: 20px;font-family: \'Tajawal\',sans-serif;">
                  ' . $error . '
                  </div>';
        }
    }
}
?>

<div class="add-recipe-page" style="direction:rtl;">
    <div style="color: black; padding: 30px; border-radius: 10px; margin-bottom: 30px;">
        <div class="line center">
            <div></div>
            <span><i class="fas fa-utensils"></i></span>
            <div></div>
        </div> 
        <h1 style="text-align: center; margin-bottom: 10px;font-family: 'Tajawal',sans-serif;"><i class="fas fa-plus-circle"></i> إضافة وصفة جديدة</h1>
        <p style="text-align: center; color: #666;font-family: 'Tajawal',sans-serif;">
            مرحباً <?php echo $_SESSION['username']; ?>، أضف وصفة جديدة لمشاركتها مع الآخرين
        </p>
    </div>
    <?php if(!$success): ?>
    <form method="POST" action="" enctype="multipart/form-data" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
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
                           value="<?php echo $_POST['title'] ?? ''; ?>"
                           style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;font-family: 'Tajawal',sans-serif;"
                           placeholder="مثال: معكرونة بالبشاميل الإيطالية">
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #555; font-weight: 600;font-family: 'Tajawal',sans-serif;">
                        وقت التحضير (دقيقة) *
                    </label>
                    <input type="number" name="prep_time" required min="1" max="480"
                           value="<?php echo $_POST['prep_time'] ?? '30'; ?>"
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
                        <option value="أطباق رئيسية" <?php echo ($_POST['category'] ?? '') == 'أطباق رئيسية' ? 'selected' : ''; ?>>أطباق رئيسية</option>
                        <option value="سلطات" <?php echo ($_POST['category'] ?? '') == 'سلطات' ? 'selected' : ''; ?>>سلطات</option>
                        <option value="حلويات" <?php echo ($_POST['category'] ?? '') == 'حلويات' ? 'selected' : ''; ?>>حلويات</option>
                        <option value="شوربات" <?php echo ($_POST['category'] ?? '') == 'شوربات' ? 'selected' : ''; ?>>شوربات</option>
                        <option value="مقبلات" <?php echo ($_POST['category'] ?? '') == 'مقبلات' ? 'selected' : ''; ?>>مقبلات</option>
                        <option value="مشروبات" <?php echo ($_POST['category'] ?? '') == 'مشروبات' ? 'selected' : ''; ?>>مشروبات</option>
                        <option value="وجبات سريعة" <?php echo ($_POST['category'] ?? '') == 'وجبات سريعة' ? 'selected' : ''; ?>>وجبات سريعة</option>
                    </select>
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 8px; color: #555; font-weight: 600;">
                        مستوى الصعوبة *
                    </label>
                    <select name="difficulty" required style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;">
                        <option value="سهل" <?php echo ($_POST['difficulty'] ?? '') == 'سهل' ? 'selected' : ''; ?>>سهل</option>
                        <option value="متوسط" <?php echo ($_POST['difficulty'] ?? '') == 'متوسط' ? 'selected' : ''; ?>>متوسط</option>
                        <option value="صعب" <?php echo ($_POST['difficulty'] ?? '') == 'صعب' ? 'selected' : ''; ?>>صعب</option>
                    </select>
                </div>
            </div>
            
            <div>
                <label style="display: block; margin-bottom: 8px; color: #555; font-weight: 600;font-family: 'Tajawal',sans-serif;">
                    وصف مختصر
                </label>
                <textarea name="description" rows="3"
                          style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; resize: vertical;font-family: 'Tajawal',sans-serif;"
                          placeholder="وصف قصير للوصفة..."><?php echo $_POST['description'] ?? ''; ?></textarea>
            </div>
        </div>

        <div style="margin-bottom: 30px;">
            <h3 style="color: #333; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #ff6b6b;font-family: 'Tajawal',sans-serif;">
                <i class="fas fa-shopping-basket" ></i> المكونات *
            </h3>
            
            <div style="margin-bottom: 15px;">
                <p style="color: #666; margin-bottom: 10px;font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-lightbulb"></i> أدخل كل مكون في سطر جديد مع الكمية
                </p>
                <textarea name="ingredients" required rows="8"
                          style="width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; resize: vertical;font-family: 'Tajawal',sans-serif;"
                          placeholder="مثال:
500 غرام معكرونة
2 كوب حليب
2 ملعقة كبيرة زبدة
2 ملعقة كبيرة دقيق
ملح وفلفل حسب الرغبة"><?php echo $_POST['ingredients'] ?? ''; ?></textarea>
            </div>
            
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px;">
                <h4 style="color: #333; margin-bottom: 10px; font-size: 1rem;font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-tips"></i> نصائح لإدخال المكونات:
                </h4>
                <ul style="color: #666; padding-right: 20px; margin: 0;font-family: 'Tajawal',sans-serif;">
                    <li style="font-family: 'Tajawal',sans-serif;">اكتب كل مكون في سطر منفصل</li>
                    <li style="font-family: 'Tajawal',sans-serif;">اذكر الكمية بدقة (كوب، ملعقة، غرام)</li>
                    <li style="font-family: 'Tajawal',sans-serif;">استخدم مصطلحات واضحة ومفهومة</li>
                    <li style="font-family: 'Tajawal',sans-serif;">رتب المكونات حسب ترتيب الاستخدام</li>
                </ul>
            </div>
        </div>

        <div style="margin-bottom: 30px;">
            <h3 style="color: #333; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #007bff;font-family: 'Tajawal',sans-serif;">
                <i class="fas fa-list-ol"></i> طريقة التحضير *
            </h3>
            
            <div style="margin-bottom: 15px;font-family: 'Tajawal',sans-serif;">
                <p style="color: #666; margin-bottom: 10px;font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-lightbulb"></i> اكتب كل خطوة في سطر جديد
                </p>
                <textarea name="instructions" required rows="10"
                          style="width: 100%; padding: 15px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem; resize: vertical; font-family: monospace;font-family: 'Tajawal',sans-serif;"
                          placeholder="مثال:
1. اسلقي المعكرونة في ماء مغلي مملح لمدة 8 دقائق
2. في مقلاة، ذوبي الزبدة ثم أضيفي الدقيق وقلبي لمدة دقيقة
3. أضيفي الحليب تدريجياً مع التحريك حتى تحصلي على صلصة كثيفة
4. ضعي المعكرونة في طبق الخبز، صفي البشاميل فوقها
5. اخبزي في فرن محمى على 180°م لمدة 20 دقيقة"><?php echo $_POST['instructions'] ?? ''; ?></textarea>
            </div>
        </div>

        <div style="margin-bottom: 30px;">
            <h3 style="color: #333; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #6f42c1;font-family: 'Tajawal',sans-serif;">
                <i class="fas fa-camera"></i> صورة الوصفة
            </h3>
            
            <div style="border: 2px dashed #ddd; padding: 30px; text-align: center; border-radius: 10px; margin-bottom: 20px;">
                <i class="fas fa-cloud-upload-alt" style="font-size: 3rem; color: #6f42c1; margin-bottom: 15px;"></i>
                <p style="color: #666; margin-bottom: 20px;font-family: 'Tajawal',sans-serif;">
                    اسحب وأفلت الصورة هنا، أو انقر لاختيار ملف
                </p>
                <input type="file" name="image" id="imageUpload" accept="image/*" 
                       style="display: none;" onchange="previewImage(this)">
                <label for="imageUpload" class="btn btn-primary" style="cursor: pointer;font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-folder-open"></i> اختر صورة
                </label>
            </div>
            
            <div id="imagePreview" style="display: none; text-align: center; margin-top: 20px;">
                <img id="preview" src="#" alt="معاينة الصورة" 
                     style="max-width: 300px; max-height: 200px; border-radius: 8px; box-shadow: 0 3px 10px rgba(0,0,0,0.1);">
                <button type="button" onclick="removeImage()" class="btn btn-danger" style="margin-top: 10px;font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-trash"></i> إزالة الصورة
                </button>
            </div>
            
            <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-top: 20px;">
                <h4 style="color: #333; margin-bottom: 10px; font-size: 1rem;font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-info-circle"></i> متطلبات الصورة:
                </h4>
                <ul style="color: #666; padding-right: 20px; margin: 0;">
                    <li style="font-family: 'Tajawal',sans-serif;">الأنواع المسموحة: JPG, PNG, GIF, WebP</li>
                    <li style="font-family: 'Tajawal',sans-serif;">الحجم الأقصى: 5 ميجابايت</li>
                    <li style="font-family: 'Tajawal',sans-serif;">الأبعاد الموصى بها: 800×600 بكسل</li>
                    <li style="font-family: 'Tajawal',sans-serif;">الصورة يجب أن تكون واضحة وجذابة</li>
                </ul>
            </div>
        </div>

        <div style="display: flex; justify-content: center; gap: 15px; margin-top: 40px; padding-top: 20px; border-top: 1px solid #eee;">
            <button type="submit" class="btn btn-success" style="padding: 12px 40px; font-size: 1.1rem;font-family: 'Tajawal',sans-serif;">
                <i class="fas fa-check-circle"></i> نشر الوصفة
            </button>
            <button type="reset" class="btn btn-secondary" style="font-family: 'Tajawal',sans-serif;">
                <i class="fas fa-redo"></i> إعادة تعيين
            </button>
            <a href="recipes.php" class="btn btn-danger" style="font-family: 'Tajawal',sans-serif;">
                <i class="fas fa-times"></i> إلغاء
            </a>
        </div>
    </form>
    <?php endif; ?>

    <?php if(!$success): ?>
    <div style="background: #fff3cd; padding: 20px; border-radius: 10px; margin-top: 30px; border: 1px solid #ffeaa7;">
        <h4 style="color: #856404; margin-bottom: 10px;font-family: 'Tajawal',sans-serif;">
            <i class="fas fa-lightbulb"></i> نصائح لوصفة ناجحة:
        </h4>
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <div style="padding: 10px;">
                <div style="color: #856404; font-weight: bold; margin-bottom: 5px;font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-check"></i> كن دقيقاً
                </div>
                <div style="color: #856404; font-size: 0.9rem;font-family: 'Tajawal',sans-serif;">
                    اذكر الكميات بدقة لضمان نجاح الوصفة
                </div>
            </div>
            <div style="padding: 10px;">
                <div style="color: #856404; font-weight: bold; margin-bottom: 5px;font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-camera"></i> صور جذابة
                </div>
                <div style="color: #856404; font-size: 0.9rem;font-family: 'Tajawal',sans-serif;">
                    استخدم صوراً واضحة وذات إضاءة جيدة
                </div>
            </div>
            <div style="padding: 10px;">
                <div style="color: #856404; font-weight: bold; margin-bottom: 5px;font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-list-ol"></i> خطوات واضحة
                </div>
                <div style="color: #856404; font-size: 0.9rem;font-family: 'Tajawal',sans-serif;">
                    قسم العملية إلى خطوات سهلة المتابعة
                </div>
            </div>
            <div style="padding: 10px;">
                <div style="color: #856404; font-weight: bold; margin-bottom: 5px;font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-clock"></i> وقت دقيق
                </div>
                <div style="color: #856404; font-size: 0.9rem;font-family: 'Tajawal',sans-serif;">
                    حدد الوقت الحقيقي للتحضير والطهي
                </div>
            </div>
        </div>
    </div>
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

<?php require_once 'includes/footer.php'; ?>
<script src="assets/jquery/jquery-3.5.1.js"></script>
<!-- Fontawesome js --> 
<script src="assets/font-awesome/all.js"></script>
<!-- custom js file -->
<script src="js/script.js"></script>