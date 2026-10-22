<?php
require_once '../includes/config.php';


if(!is_logged_in() || !is_admin()) {
    header('Location: ../login.php');
    exit();
}

$page_title = 'إضافة مستخدم جديد';
require_once 'header.php';


$errors = [];
$success = false;

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = clean_input($_POST['username']);
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $is_admin = isset($_POST['is_admin']) ? 1 : 0;
    

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
    
    if(empty($password)) {
        $errors[] = 'كلمة المرور مطلوبة';
    } elseif(strlen($password) < 6) {
        $errors[] = 'كلمة المرور يجب أن تكون 6 أحرف على الأقل';
    }
    
    if($password !== $confirm_password) {
        $errors[] = 'كلمتا المرور غير متطابقتين';
    }
    

    if(empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $email]);
            $existing_user = $stmt->fetch();
            
            if($existing_user) {
                $errors[] = 'اسم المستخدم أو البريد الإلكتروني موجود مسبقاً';
            }
        } catch(PDOException $e) {
            $errors[] = 'خطأ في التحقق من البيانات: ' . $e->getMessage();
        }
    }
    

    if(empty($errors)) {
        try {

            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            
            $sql = "INSERT INTO users (username, email, password, is_admin) VALUES (?, ?, ?, ?)";
            $stmt = $pdo->prepare($sql);
            $stmt->execute([$username, $email, $hashed_password, $is_admin]);
            
            $user_id = $pdo->lastInsertId();
            $success = true;
            
            echo '<div style="padding: 15px; background: #d4edda; color: #155724; 
                  border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 20px;font-family: \'Tajawal\',sans-serif;">
                  ✅ تم إضافة المستخدم بنجاح!
                  <br><small>يمكنك <a href="manage-users.php" style="color: #0b2e13; font-weight: bold;">عرض جميع المستخدمين</a> أو <a href="add-user.php" style="color: #0b2e13; font-weight: bold;">إضافة مستخدم جديد</a></small>
                  </div>';
            

            $_POST = [];
            
        } catch(PDOException $e) {
            $errors[] = 'خطأ في إضافة المستخدم: ' . $e->getMessage();
        }
    }
    

    if(!empty($errors) && !$success) {
        foreach($errors as $error) {
            echo '<div style="padding: 15px; background: #f8d7da; color: #721c24; 
                  border: 1px solid #f5c6cb; border-radius: 5px; margin-bottom: 20px;font-family: \'Tajawal\',sans-serif;">
                  ❌ ' . $error . '
                  </div>';
        }
    }
}
?>

<div class="add-user-page" style="direction: rtl; padding: 20px; max-width: 800px; margin: 0 auto;">

    <div style="text-align: center; margin-bottom: 40px;">
        <h1 style="color: #333; margin-bottom: 10px;font-family: 'Tajawal',sans-serif;">
            <i class="fas fa-user-plus"></i> إضافة مستخدم جديد
        </h1>
        <p style="color: #666;font-family: 'Tajawal',sans-serif;">
            أضف مستخدم جديد للنظام وحدد صلاحياته
        </p>
    </div>

    <?php if(!$success): ?>

    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
        <h3 style="color: #333; margin-bottom: 25px; padding-bottom: 10px; border-bottom: 2px solid #007bff;font-family: 'Tajawal',sans-serif;">
            <i class="fas fa-user-circle"></i> بيانات المستخدم الجديد
        </h3>
        
        <form method="POST" action="">

            <div style="margin-bottom: 30px;">
                <h4 style="color: #555; margin-bottom: 15px;font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-user"></i> معلومات الحساب
                </h4>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #555; font-weight: 600;font-family: 'Tajawal',sans-serif;">
                            اسم المستخدم *
                        </label>
                        <input type="text" name="username" required 
                               value="<?php echo $_POST['username'] ?? ''; ?>"
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;font-family: 'Tajawal',sans-serif;"
                               placeholder="مثال: reem123">
                        <small style="color: #888;font-family: 'Tajawal',sans-serif;">يجب أن يكون 3 أحرف على الأقل</small>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #555; font-weight: 600;font-family: 'Tajawal',sans-serif;">
                            البريد الإلكتروني *
                        </label>
                        <input type="email" name="email" required 
                               value="<?php echo $_POST['email'] ?? ''; ?>"
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;font-family: 'Tajawal',sans-serif;"
                               placeholder="مثال: user@example.com">
                    </div>
                </div>
                
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #555; font-weight: 600;font-family: 'Tajawal',sans-serif;">
                            كلمة المرور *
                        </label>
                        <input type="password" name="password" required 
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;font-family: 'Tajawal',sans-serif;"
                               placeholder="كلمة المرور">
                        <small style="color: #888;font-family: 'Tajawal',sans-serif;">يجب أن تكون 6 أحرف على الأقل</small>
                    </div>
                    
                    <div>
                        <label style="display: block; margin-bottom: 8px; color: #555; font-weight: 600;font-family: 'Tajawal',sans-serif;">
                            تأكيد كلمة المرور *
                        </label>
                        <input type="password" name="confirm_password" required 
                               style="width: 100%; padding: 12px; border: 1px solid #ddd; border-radius: 8px; font-size: 1rem;font-family: 'Tajawal',sans-serif;"
                               placeholder="أعد إدخال كلمة المرور">
                    </div>
                </div>
            </div>
            

            <div style="margin-bottom: 30px; padding: 20px; background: #f8f9fa; border-radius: 8px;">
                <h4 style="color: #555; margin-bottom: 15px;font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-user-shield"></i> صلاحيات المستخدم
                </h4>
                
                <div style="display: flex; align-items: center; gap: 15px; padding: 15px; background: white; border-radius: 8px; border: 1px solid #ddd;">
                    <div>
                        <input type="checkbox" name="is_admin" id="isAdmin"
                               <?php echo (isset($_POST['is_admin']) && $_POST['is_admin']) ? 'checked' : ''; ?>
                               style="width: 20px; height: 20px;">
                    </div>
                    <div>
                        <label for="isAdmin" style="font-weight: 600; color: #333; cursor: pointer;font-family: 'Tajawal',sans-serif;">
                            منح صلاحيات مدير
                        </label>
                        <p style="color: #666; margin: 5px 0 0; font-size: 0.9rem;font-family: 'Tajawal',sans-serif;">
                            عند تفعيل هذا الخيار، سيتمكن المستخدم من الوصول إلى لوحة التحكم وإدارة النظام بالكامل
                        </p>
                    </div>
                </div>
            </div>
            

            <div style="background: #e7f3ff; padding: 15px; border-radius: 8px; margin-bottom: 30px;">
                <h4 style="color: #0056b3; margin-bottom: 10px;font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-info-circle"></i> ملاحظات هامة
                </h4>
                <ul style="color: #0056b3; padding-right: 20px; margin: 0;">
                    <li style="font-family: 'Tajawal',sans-serif; margin-bottom: 5px;">سيتم تشفير كلمة المرور تلقائياً قبل تخزينها</li>
                    <li style="font-family: 'Tajawal',sans-serif; margin-bottom: 5px;">يمكن للمستخدم تعديل بياناته بعد تسجيل الدخول</li>
                    <li style="font-family: 'Tajawal',sans-serif;">يمكنك إدارة جميع المستخدمين من صفحة "إدارة المستخدمين"</li>
                </ul>
            </div>
            

            <div style="display: flex; justify-content: center; gap: 15px; margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee;">
                <button type="submit" class="btn btn-success" style="padding: 12px 40px; font-size: 1.1rem;font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-check-circle"></i> إضافة المستخدم
                </button>
                <button type="reset" class="btn btn-secondary" style="font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-redo"></i> إعادة تعيين
                </button>
                <a href="dashboard.php" class="btn btn-outline-secondary" style="font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-arrow-right"></i> العودة للوحة التحكم
                </a>
            </div>
        </form>
    </div>
    <?php endif; ?>
    

    <?php if(!$success): ?>
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px; margin-top: 30px;">
        <a href="manage-users.php" class="btn btn-outline-primary" style="text-align: center; padding: 15px;font-family: 'Tajawal',sans-serif;">
            <i class="fas fa-list"></i> عرض جميع المستخدمين
        </a>
        <a href="dashboard.php" class="btn btn-outline-info" style="text-align: center; padding: 15px;font-family: 'Tajawal',sans-serif;">
            <i class="fas fa-tachometer-alt"></i> لوحة التحكم
        </a>
        <a href="../index.php" class="btn btn-outline-secondary" style="text-align: center; padding: 15px;font-family: 'Tajawal',sans-serif;">
            <i class="fas fa-home"></i> الصفحة الرئيسية
        </a>
    </div>
    <?php else: ?>
    <div style="text-align: center; margin-top: 40px;">
        <a href="add-user.php" class="btn btn-primary" style="margin: 5px;font-family: 'Tajawal',sans-serif;">
            <i class="fas fa-user-plus"></i> إضافة مستخدم آخر
        </a>
        <a href="manage-users.php" class="btn btn-success" style="margin: 5px;font-family: 'Tajawal',sans-serif;">
            <i class="fas fa-list"></i> عرض جميع المستخدمين
        </a>
        <a href="dashboard.php" class="btn btn-info" style="margin: 5px;font-family: 'Tajawal',sans-serif;">
            <i class="fas fa-tachometer-alt"></i> العودة للوحة التحكم
        </a>
    </div>
    <?php endif; ?>
</div>


<script>

document.querySelector('input[name="password"]').addEventListener('input', function(e) {
    const password = e.target.value;
    const strength = calculatePasswordStrength(password);
    updatePasswordStrengthIndicator(strength);
});

function calculatePasswordStrength(password) {
    let strength = 0;
    
    if(password.length >= 8) strength += 1;
    if(password.length >= 12) strength += 1;
    if(/[A-Z]/.test(password)) strength += 1;
    if(/[a-z]/.test(password)) strength += 1;
    if(/[0-9]/.test(password)) strength += 1;
    if(/[^A-Za-z0-9]/.test(password)) strength += 1;
    
    return Math.min(strength, 5);
}

function updatePasswordStrengthIndicator(strength) {
    let indicator = document.getElementById('passwordStrength');
    if(!indicator) {
        indicator = document.createElement('div');
        indicator.id = 'passwordStrength';
        indicator.style.marginTop = '5px';
        document.querySelector('input[name="password"]').parentNode.appendChild(indicator);
    }
    
    const strengthText = ['ضعيفة جداً', 'ضعيفة', 'متوسطة', 'جيدة', 'قوية', 'قوية جداً'];
    const strengthColors = ['#dc3545', '#ff6b6b', '#ffc107', '#28a745', '#20c997', '#007bff'];
    
    indicator.innerHTML = `
        <div style="font-size: 0.85rem; color: ${strengthColors[strength]}; font-weight: bold;font-family: 'Tajawal',sans-serif;">
            <i class="fas fa-shield-alt"></i> قوة كلمة المرور: ${strengthText[strength]}
        </div>
        <div style="height: 5px; background: #eee; border-radius: 3px; margin-top: 5px; overflow: hidden;">
            <div style="width: ${(strength / 5) * 100}%; height: 100%; background: ${strengthColors[strength]};"></div>
        </div>
    `;
}


document.querySelector('input[name="confirm_password"]').addEventListener('input', function(e) {
    const password = document.querySelector('input[name="password"]').value;
    const confirmPassword = e.target.value;
    const matchIndicator = document.getElementById('passwordMatch');
    
    if(!matchIndicator) {
        const indicator = document.createElement('div');
        indicator.id = 'passwordMatch';
        indicator.style.marginTop = '5px';
        e.target.parentNode.appendChild(indicator);
    }
    
    if(confirmPassword === '') {
        matchIndicator.innerHTML = '';
    } else if(password === confirmPassword) {
        matchIndicator.innerHTML = '<div style="color: #28a745; font-size: 0.85rem;font-family: \'Tajawal\',sans-serif;"><i class="fas fa-check-circle"></i> كلمتا المرور متطابقتان</div>';
    } else {
        matchIndicator.innerHTML = '<div style="color: #dc3545; font-size: 0.85rem;font-family: \'Tajawal\',sans-serif;"><i class="fas fa-times-circle"></i> كلمتا المرور غير متطابقتان</div>';
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