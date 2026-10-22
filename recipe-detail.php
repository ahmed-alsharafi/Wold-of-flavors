


<?php
require_once 'includes/config.php';

require_once 'includes/header.php';

if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: recipes.php');
    exit();
}

$recipe_id = intval($_GET['id']);


try {
    $stmt = $pdo->prepare("SELECT * FROM recipes WHERE id = ?");
    $stmt->execute([$recipe_id]);
    $recipe = $stmt->fetch();
    
    if(!$recipe) {
        header('Location: recipes.php');
        exit();
    }
    

    $page_title = $recipe['title'];
    
} catch(PDOException $e) {
    die("خطأ في قاعدة البيانات: " . $e->getMessage());
}

?>

<section class = "container" id = "contact">
<div class="recipe-detail" style="direction:rtl;">
    <!-- صورة الوصفة -->
    <div style="position: relative; margin-bottom: 30px;">
        <?php if(!empty($recipe['image'])): ?>
            <img src="uploads/<?php echo $recipe['image']; ?>" 
                 alt="<?php echo $recipe['title']; ?>"
                 style="width: 100%; height: 400px; border-radius: 10px;">
        <?php else: ?>
            <div style="width: 100%; height: 300px; background: linear-gradient(45deg, #ff6b6b, #ff8e53); border-radius: 10px; display: flex; align-items: center; justify-content: center; color: white;">
                <i class="fas fa-utensils" style="font-size: 5rem;"></i>
            </div>
        <?php endif; ?>
        
        <div style="position: absolute; bottom: 20px; right: 20px; background: rgba(0,0,0,0.7); color: white; padding: 10px 20px; border-radius: 5px;font-family: 'Tajawal',sans-serif;">
            وقت التحضير: <?php echo $recipe['prep_time']; ?> دقيقة
        </div>
    </div>

    <!-- معلومات الوصفة -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <div>
            <h1 style="color: #333; margin-bottom: 5px;font-family: 'Tajawal',sans-serif;"><?php echo $recipe['title']; ?></h1>
            <div style="display: flex; gap: 15px; color: #666;">
                <span style="font-family: 'Tajawal',sans-serif;"> <?php echo $recipe['category'] ?? 'غير مصنف'; ?></span>
                <span style="font-family: 'Tajawal',sans-serif;"> <?php echo $recipe['difficulty']; ?></span>
                <span style="font-family: 'Tajawal',sans-serif;"> <?php echo date('Y-m-d', strtotime($recipe['created_at'])); ?></span>
            </div>
        </div>
        

    </div>

    <!-- الوصف -->
    <?php if(!empty($recipe['description'])): ?>
        <div style="background: #f8f9fa; padding: 20px; border-radius: 10px; margin-bottom: 30px;">
            <h3 style="color: #ff6b6b; margin-bottom: 10px;font-family: 'Tajawal',sans-serif;"> وصف الوصفة</h3>
            <p style="color: #555; line-height: 1.8;font-family: 'Tajawal',sans-serif;"><?php echo nl2br($recipe['description']); ?></p>
        </div>
    <?php endif; ?>

    <!-- المكونات وطريقة التحضير -->
    <div style="display: grid; gap: 30px; margin-bottom: 40px;">
        <!-- المكونات -->
        <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.08);">
            <h3 style="color: #ff6b6b; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #ff6b6b;font-family: 'Tajawal',sans-serif;">
               المكونات
            </h3>
            <div style="line-height: 2;font-family: 'Tajawal',sans-serif;">
                <?php 
                $ingredients = explode("\n", $recipe['ingredients']);
                foreach($ingredients as $ingredient):
                    if(trim($ingredient) !== ''):
                ?>
                    <div style="display: flex; align-items: center; padding: 8px 0; border-bottom: 1px dashed #eee;font-family: 'Tajawal',sans-serif;">
                       
                        <span style="color: #555;font-family: 'Tajawal',sans-serif;"><?php echo trim($ingredient); ?></span>
                    </div>
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
        </div>

        <!-- طريقة التحضير -->
        <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.08);">
            <h3 style="color: #ff6b6b; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #ff6b6b;font-family: 'Tajawal',sans-serif;">
                طريقة التحضير
            </h3>
            <div style="line-height: 1.8;font-family: 'Tajawal',sans-serif;">
                <?php 
                $steps = explode("\n", $recipe['instructions']);
                $step_number = 1;
                foreach($steps as $step):
                    if(trim($step) !== ''):
                ?>
                    <div style="margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px; border-right: 4px solid #ff6b6b;">
                        <div style="display: flex; align-items: center; margin-bottom: 10px;">
                            <span style="background: #ff6b6b; color: white; width: 30px; height: 30px; border-radius: 50%; display: flex; align-items: center; justify-content: center; font-weight: bold; margin-left: 10px;font-family: 'Tajawal',sans-serif;">
                                <?php echo $step_number++; ?>
                            </span>
                            <strong style="color: #333;font-family: 'Tajawal',sans-serif;">الخطوة <?php echo $step_number-1; ?></strong>
                        </div>
                        <p style="color: #555; margin: 0;font-family: 'Tajawal',sans-serif;"><?php echo trim($step); ?></p>
                    </div>
                <?php 
                    endif;
                endforeach; 
                ?>
            </div>
        </div>
    </div>
</div>

</section>

<?php require_once 'includes/footer.php'; ?>

    <!-- jQuery -->
    <script src = "assets/jquery/jquery-3.5.1.js"></script>
    <!-- Fontawesome js -->
    <script src = "assets/font-awesome/all.js"></script>
    <!-- custom js file -->
    <script src="js/script.js"></script>
