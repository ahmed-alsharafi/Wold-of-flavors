<!DOCTYPE html>
<html>
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title><?php echo isset($page_title) ? $page_title . ' - موقع الوصفات' : 'موقع وصفات الطبخ'; ?></title>
    <meta name="description" content="">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="preconnect" href="https://fonts.gstatic.com">
<link href="https://fonts.googleapis.com/css2?family=Roboto+Mono:wght@700&display=swap" rel="stylesheet">
    <link rel = "stylesheet" href = "../assets/font-awesome/all.css">
    <link rel="stylesheet" href="../css/style.css">
  </head>
  <body>

    <header>
      <nav class = "navbar">
        <div class = "container">
          <div class = "brand-and-toggler">
            <a href = "index.php" class = "navbar-brand">
              <img src = "../assets/images/logo-png.png" alt = "logo">
              <h2 style="font-family: 'Tajawal',sans-serif;">عالم النكهات</h2>
            </a>
            <button type = "button" id = "navbar-toggler">
              <i class = "fas fa-bars"></i>
            </button>
          </div>
          <div class = "navbar-collapse">
            <ul class = "navbar-nav">
              <li>
                <a href = "../index.php" style="font-family: 'Tajawal',sans-serif;">الرئيسية</a>
              </li>
              <li>
                <a href = "../recipes.php" style="font-family: 'Tajawal',sans-serif;">جميع الوصفات</a>
              </li>
              <li>
                <a href = "../recipe-builder.php" style="font-family: 'Tajawal',sans-serif;">البحث الذكي</a>
              </li>
              <li>
                <a href = "../add-recipe.php" style="font-family: 'Tajawal',sans-serif;">اضافة وصفة</a>
              </li>
              <?php if(is_logged_in() && is_admin()): ?>
                <li>
                  <a href = "dashboard.php" style="font-family: 'Tajawal',sans-serif;">لوحة التحكم</a>
                </li>
              <?php endif; ?>

            <?php if(is_logged_in()): ?>
                <li>
                    <a href="#" style="font-family: 'Tajawal',sans-serif; color: #ff6b6b;">
                        <i class="fas fa-user"></i> <?php echo $_SESSION['username']; ?>
                    </a>
                </li>
                <li>
                    <a href="../logout.php" style="font-family: 'Tajawal',sans-serif;">
                        <i class="fas fa-sign-out-alt"></i> خروج
                    </a>
                </li>
            <?php else: ?>
                <li>
                    <a href="../login.php" style="font-family: 'Tajawal',sans-serif;">تسجيل دخول</a>
                </li>
                <li>
                    <a href="../register.php" style="font-family: 'Tajawal',sans-serif;">تسجيل</a>
                </li>
            <?php endif; ?>
            </ul>
          </div>
        </div>
      </nav>
    </header>
