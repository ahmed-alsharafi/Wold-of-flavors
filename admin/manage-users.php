
<?php
require_once '../includes/config.php';


if(!is_logged_in() || !is_admin()) {
    header('Location: ../login.php');
    exit();
}

$page_title = 'إدارة المستخدمين';
require_once 'header.php';


try {
    $stmt = $pdo->query("SELECT * FROM users ORDER BY created_at DESC");
    $users = $stmt->fetchAll();
} catch(PDOException $e) {
    die("خطأ في قاعدة البيانات: " . $e->getMessage());
}


if(isset($_GET['delete'])) {
    $user_id = intval($_GET['delete']);
    

    if($user_id == $_SESSION['user_id']) {
        echo show_message('error', 'لا يمكنك حذف حسابك الخاص!');
    } else {
        try {
            $stmt = $pdo->prepare("DELETE FROM users WHERE id = ?");
            $stmt->execute([$user_id]);
            
            echo show_message('success', 'تم حذف المستخدم بنجاح!');

            echo '<script>setTimeout(function(){ window.location.href = "manage-users.php"; }, 1500);</script>';
        } catch(PDOException $e) {
            echo show_message('error', 'خطأ في حذف المستخدم: ' . $e->getMessage());
        }
    }
}
?>

<div class="manage-users" style="direction: rtl; padding: 20px;">
    <h1 style="text-align: center; margin-bottom: 30px; color: #333;font-family: 'Tajawal',sans-serif;">
        <i class="fas fa-users"></i> إدارة جميع المستخدمين
    </h1>
    
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <a href="dashboard.php" class="btn btn-secondary" style="font-family: 'Tajawal',sans-serif;">
            <i class="fas fa-arrow-right"></i> العودة للوحة التحكم
        </a>
        <a href="add-user.php" class="btn btn-success" style="font-family: 'Tajawal',sans-serif;">
            <i class="fas fa-user-plus"></i> إضافة مستخدم جديد
        </a>
    </div>
    
    <!-- جدول المستخدمين -->
    <div style="background: white; padding: 20px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); overflow-x: auto;">
        <?php if(count($users) > 0): ?>
        <table style="width: 100%; border-collapse: collapse;">
            <thead>
                <tr style="background: #f8f9fa;">
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: right;font-family: 'Tajawal',sans-serif;">#</th>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: right;font-family: 'Tajawal',sans-serif;">اسم المستخدم</th>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: right;font-family: 'Tajawal',sans-serif;">البريد الإلكتروني</th>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: right;font-family: 'Tajawal',sans-serif;">الدور</th>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: right;font-family: 'Tajawal',sans-serif;">تاريخ التسجيل</th>
                    <th style="padding: 12px; border-bottom: 2px solid #dee2e6; text-align: right;font-family: 'Tajawal',sans-serif;">الإجراءات</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach($users as $user): ?>
                    <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 12px; text-align: center;"><?php echo $user['id']; ?></td>
                        <td style="padding: 12px;font-family: 'Tajawal',sans-serif;">
                            <strong><?php echo $user['username']; ?></strong>
                        </td>
                        <td style="padding: 12px;font-family: 'Tajawal',sans-serif;">
                            <?php echo $user['email']; ?>
                        </td>
                        <td style="padding: 12px;">
                            <?php if($user['is_admin']): ?>
                                <span style="background: #ff6b6b; color: white; padding: 5px 15px; border-radius: 15px; font-size: 0.8rem;">
                                    <i class="fas fa-crown"></i> مدير
                                </span>
                            <?php else: ?>
                                <span style="background: #6c757d; color: white; padding: 5px 15px; border-radius: 15px; font-size: 0.8rem;">
                                    <i class="fas fa-user"></i> مستخدم
                                </span>
                            <?php endif; ?>
                        </td>
                        <td style="padding: 12px;font-family: 'Tajawal',sans-serif;">
                            <?php echo date('Y-m-d', strtotime($user['created_at'])); ?>
                        </td>
                        <td style="padding: 12px; text-align: center;">
                            <a href="edit-user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning" title="تعديل">
                                <i class="fas fa-edit"></i>
                            </a>
                            <?php if($user['id'] != $_SESSION['user_id']): ?>
                                <a href="manage-users.php?delete=<?php echo $user['id']; ?>" 
                                   class="btn btn-sm btn-danger" 
                                   title="حذف"
                                   onclick="return confirm('هل أنت متأكد من حذف المستخدم <?php echo addslashes($user['username']); ?>؟')">
                                    <i class="fas fa-trash"></i>
                                </a>
                            <?php else: ?>
                                <span class="btn btn-sm btn-secondary" title="لا يمكن حذف حسابك">
                                    <i class="fas fa-user-shield"></i>
                                </span>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        <?php else: ?>
            <div style="text-align: center; padding: 40px 20px;">
                <i class="fas fa-users" style="font-size: 4rem; color: #ddd; margin-bottom: 20px;"></i>
                <h3 style="color: #666; margin-bottom: 15px;font-family: 'Tajawal',sans-serif;">لا يوجد مستخدمين</h3>
                <p style="color: #999; margin-bottom: 25px;font-family: 'Tajawal',sans-serif;">لم يتم تسجيل أي مستخدمين بعد.</p>
                <a href="add-user.php" class="btn btn-primary" style="font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-user-plus"></i> أضف أول مستخدم
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


