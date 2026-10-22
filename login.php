<?php
require_once 'includes/config.php';
$page_title = 'تسجيل الدخول';
require_once 'includes/header.php';


$errors = [];
$success = false;

if($_SERVER['REQUEST_METHOD'] == 'POST') {

    $username = clean_input($_POST['username']);
    $password = $_POST['password'];
    

    if(empty($username)) {
        $errors[] = 'اسم المستخدم مطلوب';
    }
    
    if(empty($password)) {
        $errors[] = 'كلمة المرور مطلوبة';
    }
    

    if(empty($errors)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();
            
            if($user) {

                if(password_verify($password, $user['password'])) {

                    $_SESSION['user_id'] = $user['id'];
                    $_SESSION['username'] = $user['username'];
                    $_SESSION['email'] = $user['email'];
                    $_SESSION['user_role'] = $user['is_admin'] ? 'admin' : 'user';
                    
                    $success = true;
                    

                    echo '<div style="padding: 15px; background: #d4edda; color: #155724; 
                          border: 1px solid #c3e6cb; border-radius: 5px; margin-bottom: 20px;font-family: \'Tajawal\',sans-serif;">
                          تم تسجيل الدخول بنجاح! جاري تحويلك إلى الصفحة الرئيسية...
                          </div>';
                    

                    $redirect = isset($_GET['redirect']) ? $_GET['redirect'] . '.php' : 'index.php';
                    header("refresh:2;url=" . $redirect);
                    
                } else {
                    $errors[] = 'كلمة المرور غير صحيحة';
                }
            } else {
                $errors[] = 'اسم المستخدم غير موجود';
            }
            
        } catch(PDOException $e) {
            $errors[] = 'خطأ في النظام: ' . $e->getMessage();
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

<section class="padding-y contact" id="contact">
    <div class="container">

        <div class="title">
            <h2 style="font-family: 'Tajawal',sans-serif;">تسجيل الدخول</h2>
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
                <input type="password" class="form-control" name="password" required 
                       placeholder="كلمة المرور">
                <input type="submit" class="form-submit-btn" value="تسجيل الدخول">
            </form>
            <center>
                <p style="color: #666; margin-bottom: 15px;font-family: 'Tajawal',sans-serif;">
                    ليس لدي حساب؟ 
                    <a href="register.php" style="color: #ff6b6b; text-decoration: none; font-weight: 600;">
                        سجل الآن
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


<!-- jQuery -->
<script src="assets/jquery/jquery-3.5.1.js"></script>
<!-- Fontawesome js -->
<script src="assets/font-awesome/all.js"></script>
<!-- custom js file -->
<script src="js/script.js"></script>

