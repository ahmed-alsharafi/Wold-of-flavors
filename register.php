
<?php
require_once 'includes/config.php';
$page_title = 'إنشاء حساب جديد';
require_once 'includes/header.php';

$errors = [];
$success = false;

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = clean_input($_POST['username']);
    $email = clean_input($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
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
            $stmt->execute([$username, $email, $hashed_password, false]);
            
            $user_id = $pdo->lastInsertId();
            
            $_SESSION['user_id'] = $user_id;
            $_SESSION['username'] = $username;
            $_SESSION['email'] = $email;
            $_SESSION['user_role'] = 'user';
            
            $success = true;
            
            echo show_message('success', 'تم إنشاء حسابك بنجاح! جاري تحويلك إلى الصفحة الرئيسية...');
            
            header("refresh:3;url=index.php");
            
        } catch(PDOException $e) {
            $errors[] = 'خطأ في إنشاء الحساب: ' . $e->getMessage();
        }
    }
    
    if(!empty($errors) && !$success) {
        foreach($errors as $error) {
            echo show_message('error', $error);
        }
    }
}
?>

<section class="padding-y contact" id="contact">
    <div class="container">
        <div class="title">
            <h2 style="font-family: 'Tajawal',sans-serif;">إنشاء حساب جديد</h2>
            <div class="line center">
                <div></div>
                <span><i class="fas fa-utensils"></i></span>
                <div></div>
            </div>
        </div>
        
        <?php if(!$success): ?>
        <div class="contact-row-1">
            <form class="contact-form" method="POST" action="">
                <input type="text" class="form-control" name="username" required 
                       placeholder="اسم المستخدم" value="<?php echo $_POST['username'] ?? ''; ?>">
                <input type="email" class="form-control" name="email" required 
                       placeholder="البريد الإلكتروني" value="<?php echo $_POST['email'] ?? ''; ?>">
                <input type="password" class="form-control" name="password" required 
                       placeholder="كلمة المرور">
                <input type="password" class="form-control" name="confirm_password" required 
                       placeholder="أعد كلمة المرور">
                <input type="submit" class="form-submit-btn" value="إنشاء حساب">
            </form>
            <center>
                <p style="color: #666; margin-bottom: 15px;font-family: 'Tajawal',sans-serif;">
                    لديك حساب بالفعل؟ 
                    <a href="login.php" style="color: #ff6b6b; text-decoration: none; font-weight: 600;">
                        سجل دخولك
                    </a>
                </p>
            </center>
        </div>
        <?php endif; ?>
    </div>
</section>

<?php
require_once 'includes/footer.php';
?>

<script src="assets/jquery/jquery-3.5.1.js"></script>
<!-- Fontawesome js -->
<script src="assets/font-awesome/all.js"></script>
<!-- custom js file -->
<script src="js/script.js"></script>