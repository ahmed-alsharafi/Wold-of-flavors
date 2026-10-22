

<?php
require_once '../includes/config.php';


if(!is_logged_in() || !is_admin()) {
    header('Location: ../login.php');
    exit();
}


if(!isset($_GET['id']) || !is_numeric($_GET['id'])) {
    header('Location: manage-users.php');
    exit();
}

$user_id = intval($_GET['id']);


try {
    $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
    $stmt->execute([$user_id]);
    $user = $stmt->fetch();
    
    if(!$user) {
        header('Location: manage-users.php');
        exit();
    }
} catch(PDOException $e) {
    die("خطأ في قاعدة البيانات: " . $e->getMessage());
}

$page_title = 'تعديل بيانات المستخدم';
require_once 'header.php';


$errors = [];
$success = false;

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = clean_input($_POST['username']);
    $email = clean_input($_POST['email']);
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    $change_password = isset($_POST['change_password']);
    

    if(empty($username)) {
        $errors[] = 'اسم المستخدم مطلوب';
    } elseif(strlen($username) < 3) {
        $errors[] = 'اسم المستخدم يجب أن يكون 3 أحرف على الأقل';
    }
    
    if(empty($email)) {
        $errors[] = 'البريد الإلكتروني مطلوب';
    } elseif(!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = 'البريد الإلكتروني غير صحيح';
    }
    

    if($change_password) {
        $password = $_POST['password'];
        $confirm_password = $_POST['confirm_password'];
        
        if(empty($password)) {
            $errors[] = 'كلمة المرور مطلوبة';
        } elseif(strlen($password) < 6) {
            $errors[] = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل';
        }
        
        if($password !== $confirm_password) {
            $errors[] = 'كلمتا المرور غير متطابقتين';
        }
    }
    

    if(empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE (username = ? OR email = ?) AND id != ?");
            $stmt->execute([$username, $email, $user_id]);
            $existing_user = $stmt->fetch();
            
            if($existing_user) {
                $errors[] = 'اسم المستخدم أو البريد الإلكتروني موجود مسبقاً لمستخدم آخر';
            }
        } catch(PDOException $e) {
            $errors[] = 'خطأ في التحقق من البيانات: ' . $e->getMessage();
        }
    }
    

    if(empty($errors)) {
        try {
            if($change_password) {

                $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                $sql = "UPDATE users SET username = ?, email = ?, password = ?, is_admin = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$username, $email, $hashed_password, $is_admin, $user_id]);
            } else {

                $sql = "UPDATE users SET username = ?, email = ?, is_admin = ? WHERE id = ?";
                $stmt = $pdo->prepare($sql);
                $stmt->execute([$username, $email, $is_admin, $user_id]);
            }
            
            $success = true;
            
            echo show_message('success', 'تم تحديث بيانات المستخدم بنجاح! <a href="manage-users.php">عرض جميع المستخدمين</a>');
            

            if($user_id == $_SESSION['user_id']) {
                $_SESSION['username'] = $username;
                $_SESSION['email'] = $email;
                $_SESSION['user_role'] = $is_admin ? 'admin' : 'user';
            }
            
        } catch(PDOException $e) {
            $errors[] = 'خطأ في تحديث المستخدم: ' . $e->getMessage();
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
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$user_id]);
        $user = $stmt->fetch();
    } catch(PDOException $e) {
        die("خطأ في قاعدة البيانات: " . $e->getMessage());
    }
}
?>

<div class="edit-user-page" style="direction: rtl; padding: 20px;">
    <div style="color: black; padding: 30px; border-radius: 10px; margin-bottom: 30px;">
        <h1 style="text-align: center; margin-bottom: 10px;font-family: 'Tajawal',sans-serif;">
            <i class="fas fa-user-edit"></i> تعديل بيانات المستخدم
        </h1>
        <p style="text-align: center; color: #666;font-family: 'Tajawal',sans-serif;">
            تعديل بيانات المستخدم: <?php echo $user['username']; ?>
        </p>
    </div>

    <?php if(!$success): ?>
    <form method="POST" action="" style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto;">
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: #555; font-weight: 600;font-family: 'Tajawal',sans-serif;">
                اسم المستخدم *
            </label>
            <input type="text" name="username" required 
                   value="<?php echo $_POST['username'] ?? $user['username']; ?>"
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;font-family: 'Tajawal',sans-serif;"
                   placeholder="أدخل اسم المستخدم">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: block; margin-bottom: 8px; color: #555; font-weight: 600;font-family: 'Tajawal',sans-serif;">
                البريد الإلكتروني *
            </label>
            <input type="email" name="email" required 
                   value="<?php echo $_POST['email'] ?? $user['email']; ?>"
                   style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;font-family: 'Tajawal',sans-serif;"
                   placeholder="أدخل البريد الإلكتروني">
        </div>
        
        <div style="margin-bottom: 20px;">
            <label style="display: flex; align-items: center; gap: 10px; color: #555; font-weight: 600;font-family: 'Tajawal',sans-serif;">
                <input type="checkbox" name="change_password" id="changePassword"
                       style="width: 20px; height: 20px;">
                تغيير كلمة المرور
            </label>
        </div>
        
        <div id="passwordFields" style="display: none; margin-bottom: 20px;">
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; color: #555; font-weight: 600;font-family: 'Tajawal',sans-serif;">
                    كلمة المرور الجديدة *
                </label>
                <input type="password" name="password" 
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;font-family: 'Tajawal',sans-serif;"
                       placeholder="أدخل كلمة المرور الجديدة">
            </div>
            
            <div style="margin-bottom: 15px;">
                <label style="display: block; margin-bottom: 8px; color: #555; font-weight: 600;font-family: 'Tajawal',sans-serif;">
                    تأكيد كلمة المرور الجديدة *
                </label>
                <input type="password" name="confirm_password" 
                       style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;font-family: 'Tajawal',sans-serif;"
                       placeholder="أعد إدخال كلمة المرور الجديدة">
            </div>
        </div>
        
        <div style="margin-bottom: 30px;">
            <label style="display: flex; align-items: center; gap: 10px; color: #555; font-weight: 600;font-family: 'Tajawal',sans-serif;">
                <input type="checkbox" name="is_admin" 
                       <?php echo (isset($_POST['is_admin']) ? $_POST['is_admin'] : $user['is_admin']) ? 'checked' : ''; ?>
                       style="width: 20px; height: 20px;">
                صلاحيات مدير
            </label>
        </div>
        
        <!-- معلومات إضافية -->
        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
            <h4 style="color: #333; margin-bottom: 10px; font-size: 1rem;font-family: 'Tajawal',sans-serif;">
                <i class="fas fa-info-circle"></i> معلومات المستخدم:
            </h4>
            <ul style="color: #666; padding-right: 20px; margin: 0;">
                <li style="font-family: 'Tajawal',sans-serif;">رقم المستخدم: <?php echo $user['id']; ?></li>
                <li style="font-family: 'Tajawal',sans-serif;">تاريخ التسجيل: <?php echo date('Y-m-d', strtotime($user['created_at'])); ?></li>
                <?php if($user_id == $_SESSION['user_id']): ?>
                    <li style="font-family: 'Tajawal',sans-serif; color: #ff6b6b;"><strong>أنت تقوم بتعديل حسابك الخاص</strong></li>
                <?php endif; ?>
            </ul>
        </div>
        
        <!-- أزرار الإرسال -->
        <div style="display: flex; justify-content: center; gap: 15px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
            <button type="submit" class="btn btn-success" style="padding: 12px 40px; font-size: 1.1rem;font-family: 'Tajawal',sans-serif;">
                <i class="fas fa-check-circle"></i> حفظ التعديلات
            </button>
            <a href="manage-users.php" class="btn btn-secondary" style="font-family: 'Tajawal',sans-serif;">
                <i class="fas fa-times"></i> إلغاء
            </a>
        </div>
    </form>
    <?php endif; ?>
</div>

<script>
document.getElementById('changePassword').addEventListener('change', function() {
    const passwordFields = document.getElementById('passwordFields');
    const passwordInputs = passwordFields.querySelectorAll('input[name="password"], input[name="confirm_password"]');
    
    if(this.checked) {
        passwordFields.style.display = 'block';
        passwordInputs.forEach(input => input.required = true);
    } else {
        passwordFields.style.display = 'none';
        passwordInputs.forEach(input => input.required = false);
    }
});
</script>


    <!-- jQuery -->
    <script src = "../assets/jquery/jquery-3.5.1.js"></script>
    <!-- Fontawesome js -->
    <script src = "../assets/font-awesome/all.js"></script>
    <!-- custom js file -->
    <script src="../js/script.js"></script>
  </body>
</html>

