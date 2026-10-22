
<?php
require_once '../includes/config.php';


if(!is_logged_in() || !is_admin()) {
    header('Location: ../login.php');
    exit();
}

$page_title = 'إدارة الوصفات';
require_once 'header.php';


try {
    $stmt = $pdo->query("SELECT r.*, u.username FROM recipes r LEFT JOIN users u ON r.user_id = u.id ORDER BY r.created_at DESC");
    $recipes = $stmt->fetchAll();
} catch(PDOException $e) {
    die("خطأ في قاعدة البيانات: " . $e->getMessage());
}


if(isset($_GET['delete'])) {
    $recipe_id = intval($_GET['delete']);
    
    try {
        $stmt = $pdo->prepare("DELETE FROM recipes WHERE id = ?");
        $stmt->execute([$recipe_id]);
        
        echo show_message('success', 'تم حذف الوصفة بنجاح!');

        echo '<script>setTimeout(function(){ window.location.href = "manage-recipes.php"; }, 1500);</script>';
    } catch(PDOException $e) {
        echo show_message('error', 'خطأ في حذف الوصفة: ' . $e->getMessage());
    }
}
?>

<div class="manage-recipes" style="direction: rtl; padding: 20px;">
    <h1 style="text-align: center; margin-bottom: 30px; color: #333;font-family: 'Tajawal',sans-serif;">
        <i class="fas fa-utensils"></i> إدارة جميع الوصفات
    </h1>
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <a href="dashboard.php" class="btn btn-secondary" style="font-family: 'Tajawal',sans-serif;">
            <i class="fas fa-arrow-right"></i> العودة للوحة التحكم
        </a>
        <a href="../add-recipe.php" class="btn btn-success" style="font-family: 'Tajawal',sans-serif;">
            <i class="fas fa-plus"></i> إضافة وصفة جديدة
        </a>
    </div>
    

    <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); overflow-x: auto;">
        <?php if(count($recipes) > 0): ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: right;font-family: 'Tajawal',sans-serif;">#</th>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: right;font-family: 'Tajawal',sans-serif;">العنوان</th>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: right;font-family: 'Tajawal',sans-serif;">المستخدم</th>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: right;font-family: 'Tajawal',sans-serif;">الفئة</th>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: right;font-family: 'Tajawal',sans-serif;">الصعوبة</th>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: right;font-family: 'Tajawal',sans-serif;">التاريخ</th>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: right;font-family: 'Tajawal',sans-serif;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($recipes as $recipe): ?>
                    <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 12px; text-align: center;"><?php echo $recipe['id']; ?></td>
                        <td style="padding: 12px;font-family: 'Tajawal',sans-serif;">
                            <strong><?php echo $recipe['title']; ?></strong><br>
                            <small style="color: #666;"><?php echo $recipe['prep_time']; ?> دقيقة</small>
                        </td>
                        <td style="padding: 12px;font-family: 'Tajawal',sans-serif;">
                            <?php echo $recipe['username'] ?? 'غير معروف'; ?>
                        </td>
                        <td style="padding: 12px;font-family: 'Tajawal',sans-serif;">
                            <?php echo $recipe['category']; ?>
                        </td>
                        <td style="padding: 12px;">
                            <span style="background: #ff6b6b; color: white; padding: 3px 10px; border-radius: 15px; font-size: 0.8rem;">
                                <?php echo $recipe['difficulty']; ?>
                            </span>
                        </td>
                        <td style="padding: 12px;font-family: 'Tajawal',sans-serif;">
                            <?php echo date('Y-m-d', strtotime($recipe['created_at'])); ?>
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <a href="../recipe-detail.php?id=<?php echo $recipe['id']; ?>" class="btn btn-sm btn-primary" title="عرض">
                                <i class="fas fa-eye"></i>
                            </a>
                            <a href="edit-recipe.php?id=<?php echo $recipe['id']; ?>" class="btn btn-sm btn-warning" title="تعديل">
                                <i class="fas fa-edit"></i>
                            </a>
                            <a href="manage-recipes.php?delete=<?php echo $recipe['id']; ?>" 
                               class="btn btn-sm btn-danger" 
                               title="حذف"
                               onclick="return confirm('هل أنت متأكد من حذف وصفة <?php echo addslashes($recipe['title']); ?>؟')">
                                <i class="fas fa-trash"></i>
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div style="text-align: center; padding: 40px 20px;">
                <i class="fas fa-utensils" style="font-size: 4rem; color: #ddd; margin-bottom: 20px;"></i>
                <h3 style="color: #666; margin-bottom: 15px;font-family: 'Tajawal',sans-serif;">لا توجد وصفات</h3>
                <p style="color: #999; margin-bottom: 25px;font-family: 'Tajawal',sans-serif;">لم يتم إضافة أي وصفات بعد.</p>
                <a href="../add-recipe.php" class="btn btn-primary" style="font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-plus"></i> أضف أول وصفة
                </a>
            </div>
        <?php endif; ?>
    </div>
</div>


    <!-- jQuery -->
    <script src = "../assets/jquery/jquery-3.5.1.js"></script>
    <!-- Fontawesome js -->
    <script src = "../assets/font-awesome/all.js"></script>
    <!-- custom js file -->
    <script src="../js/script.js"></script>
  </body>
</html>


