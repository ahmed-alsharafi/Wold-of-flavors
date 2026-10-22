

<?php
require_once '../includes/config.php';


if(!is_logged_in() || !is_admin()) {
    header('Location: ../login.php');
    exit();
}

$page_title = 'لوحة تحكم الإدارة';
require_once 'header.php';


try {

    $users_stmt = $pdo->query("SELECT COUNT(*) as count FROM users");
    $users_count = $users_stmt->fetch()['count'];
    

    $recipes_stmt = $pdo->query("SELECT COUNT(*) as count FROM recipes");
    $recipes_count = $recipes_stmt->fetch()['count'];
    

    $today = date('Y-m-d');
    $today_stmt = $pdo->prepare("SELECT COUNT(*) as count FROM recipes WHERE DATE(created_at) = ?");
    $today_stmt->execute([$today]);
    $today_count = $today_stmt->fetch()['count'];
    

    $new_users_stmt = $pdo->prepare("SELECT COUNT(*) as count FROM users WHERE DATE(created_at) = ?");
    $new_users_stmt->execute([$today]);
    $new_users_count = $new_users_stmt->fetch()['count'];
    
} catch(PDOException $e) {
    die("خطأ في قاعدة البيانات: " . $e->getMessage());
}
?>

<div class="admin-dashboard" style="direction: rtl; padding: 20px;">
    <h1 style="text-align: center; margin-bottom: 30px; color: #333;font-family: 'Tajawal',sans-serif;">
        <i class="fas fa-tachometer-alt"></i> لوحة تحكم الإدارة
    </h1>
    

    <div class="stats-cards" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; margin-bottom: 40px;">
        <div style="background: linear-gradient(45deg, #007bff, #6610f2); color: white; padding: 25px; border-radius: 10px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            <div style="font-size: 2.5rem; margin-bottom: 10px;">
                <i class="fas fa-users"></i>
            </div>
            <h3 style="margin: 0; font-size: 1.5rem;"><?php echo $users_count; ?></h3>
            <p style="margin: 5px 0 0; opacity: 0.9;">إجمالي المستخدمين</p>
        </div>
        
        <div style="background: linear-gradient(45deg, #28a745, #20c997); color: white; padding: 25px; border-radius: 10px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            <div style="font-size: 2.5rem; margin-bottom: 10px;">
                <i class="fas fa-utensils"></i>
            </div>
            <h3 style="margin: 0; font-size: 1.5rem;"><?php echo $recipes_count; ?></h3>
            <p style="margin: 5px 0 0; opacity: 0.9;">إجمالي الوصفات</p>
        </div>
        
        <div style="background: linear-gradient(45deg, #ffc107, #fd7e14); color: white; padding: 25px; border-radius: 10px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            <div style="font-size: 2.5rem; margin-bottom: 10px;">
                <i class="fas fa-calendar-day"></i>
            </div>
            <h3 style="margin: 0; font-size: 1.5rem;"><?php echo $today_count; ?></h3>
            <p style="margin: 5px 0 0; opacity: 0.9;">وصفات اليوم</p>
        </div>
        
        <div style="background: linear-gradient(45deg, #dc3545, #e83e8c); color: white; padding: 25px; border-radius: 10px; text-align: center; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            <div style="font-size: 2.5rem; margin-bottom: 10px;">
                <i class="fas fa-user-plus"></i>
            </div>
            <h3 style="margin: 0; font-size: 1.5rem;"><?php echo $new_users_count; ?></h3>
            <p style="margin: 5px 0 0; opacity: 0.9;">مستخدمين جدد اليوم</p>
        </div>
    </div>

    
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); gap: 30px;">
        
        <!-- إدارة الوصفات -->
        <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            <h3 style="color: #ff6b6b; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #ff6b6b;font-family: 'Tajawal',sans-serif;">
                <i class="fas fa-utensils"></i> إدارة الوصفات
            </h3>
            
            <div style="margin-bottom: 15px;">
                <a href="manage-recipes.php" class="btn btn-primary" style="width: 100%; margin-bottom: 10px; text-align: right; display: block; padding: 12px;font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-list"></i> عرض جميع الوصفات
                </a>
                <a href="../add-recipe.php" class="btn btn-success" style="width: 100%; margin-bottom: 10px; text-align: right; display: block; padding: 12px;font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-plus"></i> إضافة وصفة جديدة
                </a>
            </div>
            
            <!-- أحدث الوصفات -->
            <?php
            try {
                $latest_recipes = $pdo->query("SELECT * FROM recipes ORDER BY created_at DESC LIMIT 5")->fetchAll();
            } catch(PDOException $e) {
                $latest_recipes = [];
            }
            ?>
            
            <h4 style="color: #333; margin: 20px 0 10px;font-family: 'Tajawal',sans-serif;">أحدث الوصفات</h4>
            <div style="max-height: 250px; overflow-y: auto;">
                <?php if(count($latest_recipes) > 0): ?>
                    <?php foreach($latest_recipes as $recipe): ?>
                        <div style="padding: 10px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-weight: bold;font-family: 'Tajawal',sans-serif;"><?php echo substr($recipe['title'], 0, 30); ?>...</div>
                                <div style="font-size: 0.8rem; color: #666;font-family: 'Tajawal',sans-serif;">
                                    <?php echo date('Y-m-d', strtotime($recipe['created_at'])); ?>
                                </div>
                            </div>
                            <div>
                                <a href="../recipe-detail.php?id=<?php echo $recipe['id']; ?>" class="btn btn-sm btn-primary" title="عرض">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <a href="edit-recipe.php?id=<?php echo $recipe['id']; ?>" class="btn btn-sm btn-warning" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #999; text-align: center; padding: 20px;font-family: 'Tajawal',sans-serif;">لا توجد وصفات</p>
                <?php endif; ?>
            </div>
        </div>
        
        <!-- إدارة المستخدمين -->
        <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            <h3 style="color: #007bff; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #007bff;font-family: 'Tajawal',sans-serif;">
                <i class="fas fa-users"></i> إدارة المستخدمين
            </h3>
            
            <div style="margin-bottom: 15px;">
                <a href="manage-users.php" class="btn btn-primary" style="width: 100%; margin-bottom: 10px; text-align: right; display: block; padding: 12px;font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-list"></i> عرض جميع المستخدمين
                </a>
                <a href="add-user.php" class="btn btn-success" style="width: 100%; margin-bottom: 10px; text-align: right; display: block; padding: 12px;font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-user-plus"></i> إضافة مستخدم جديد
                </a>
            </div>
            
            <!-- أحدث المستخدمين -->
            <?php
            try {
                $latest_users = $pdo->query("SELECT * FROM users ORDER BY created_at DESC LIMIT 5")->fetchAll();
            } catch(PDOException $e) {
                $latest_users = [];
            }
            ?>
            
            <h4 style="color: #333; margin: 20px 0 10px;font-family: 'Tajawal',sans-serif;">أحدث المستخدمين</h4>
            <div style="max-height: 250px; overflow-y: auto;">
                <?php if(count($latest_users) > 0): ?>
                    <?php foreach($latest_users as $user): ?>
                        <div style="padding: 10px; border-bottom: 1px solid #eee; display: flex; justify-content: space-between; align-items: center;">
                            <div>
                                <div style="font-weight: bold;font-family: 'Tajawal',sans-serif;"><?php echo $user['username']; ?></div>
                                <div style="font-size: 0.8rem; color: #666;font-family: 'Tajawal',sans-serif;">
                                    <?php echo $user['email']; ?>
                                    <?php if($user['is_admin']): ?>
                                        <span style="background: #ff6b6b; color: white; padding: 2px 8px; border-radius: 10px; font-size: 0.7rem; margin-right: 5px;">
                                            مدير
                                        </span>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <div>
                                <a href="edit-user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning" title="تعديل">
                                    <i class="fas fa-edit"></i>
                                </a>
                                <?php if($user['id'] != $_SESSION['user_id']): ?>
                                    <a href="delete-user.php?id=<?php echo $user['id']; ?>" 
                                       class="btn btn-sm btn-danger" 
                                       title="حذف"
                                       onclick="return confirm('هل أنت متأكد من حذف هذا المستخدم؟')">
                                        <i class="fas fa-trash"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p style="color: #999; text-align: center; padding: 20px;font-family: 'Tajawal',sans-serif;">لا توجد مستخدمين</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- لوحة التحكم السريعة -->
    <div style="background: white; padding: 25px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-top: 30px;">
        <h3 style="color: #28a745; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #28a745;font-family: 'Tajawal',sans-serif;">
            <i class="fas fa-cogs"></i> أدوات سريعة
        </h3>
        
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
            <a href="../index.php" class="btn btn-primary" style="padding: 12px; text-align: center;font-family: 'Tajawal',sans-serif;">
                <i class="fas fa-home"></i> العودة للرئيسية
            </a>
        </div>
    </div>
    
    <!-- ملاحظات سريعة -->
    <div style="background: #fff3cd; padding: 20px; border-radius: 10px; margin-top: 30px; border: 1px solid #ffeaa7;">
        <h4 style="color: #856404; margin-bottom: 10px;font-family: 'Tajawal',sans-serif;">
            <i class="fas fa-info-circle"></i> ملاحظات هامة:
        </h4>
        <ul style="color: #856404; padding-right: 20px;">
            <li style="margin-bottom: 8px;font-family: 'Tajawal',sans-serif;">لديك صلاحيات كاملة لإدارة الموقع</li>
            <li style="margin-bottom: 8px;font-family: 'Tajawal',sans-serif;">يمكنك تعديل أو حذف أي وصفة أو مستخدم</li>
            <li style="margin-bottom: 8px;font-family: 'Tajawal',sans-serif;">يوصى بعمل نسخة احتياطية دورية للبيانات</li>
            <li style="font-family: 'Tajawal',sans-serif;">احرص على تحديث كلمات المرور بانتظام</li>
        </ul>
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
