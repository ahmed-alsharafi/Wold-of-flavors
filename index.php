<?php
require_once 'includes/config.php';
require_once 'includes/header.php';
?>

<section class="banner">
    <ul class="banner-slider">
        <li class="banner-item active-banner">
            <div class="container">
                <h1>Chase The Flavors</h1>
                <p class="text">food is the ingredient that binds us together.</p>
            </div>
        </li>
        <li class="banner-item">
            <div class="container">
                <h1>Pizza and divine taste</h1>
                <p class="text">your can't make everyone happy. you are not pizza.</p>
            </div>
        </li>
        <li class="banner-item">
            <div class="container">
                <h1>Paradise on your plate</h1>
                <p class="text">people who love to eat are always the best people.</p>
            </div>
        </li>
    </ul>
</section>

<section class="padding-y about" id="about">
    <div class="container">
        <div class="about-row">
            <div class="about-col-1">
                <img src="assets/images/about-img-1.jpg" alt="">
            </div>
            <div class="about-col-2">
                <div class="title">
                    <h2 style="font-family: 'Tajawal',sans-serif;">مرحباً بك في عالم النكهات</h2>
                    <div class="line center">
                        <div></div>
                        <span><i class="fas fa-utensils"></i></span>
                        <div></div>
                    </div>
                </div>
                <p class="text">في هذا الموقع ستجد وصفات طبخ متنوعة تجمع بين الأصالة والابتكار، لتلهمك في مطبخك كل يوم. هدفنا أن نجعل تجربة الطهي ممتعة وسهلة، سواء كنت مبتدئاً تبحث عن وصفات بسيطة أو محترفاً يريد أفكاراً جديدة. ستتعرف هنا على أسرار المكونات، طرق التقديم المميزة، ونصائح تجعل كل وجبة تجربة لا تُنسى.</p>
            </div>
            <div class="about-col-3">
                <img src="assets/images/about-img-2.jpg" alt="">
            </div>
        </div>
    </div>
</section>

<section class="padding-y service" id="service">
    <div class="container">
        <div class="title">
            <h2 style="font-family: 'Tajawal',sans-serif;">أنواع الوصفات المتوفرة</h2>
            <div class="line center">
                <div></div>
                <span><i class="fas fa-utensils"></i></span>
                <div></div>
            </div>
        </div>
        <div class="service-row">
            <div class="service-item">
                <span>
                    <i class="fas fa-birthday-cake"></i>
                </span>
                <h3 style="font-family: 'Tajawal',sans-serif;">الحلويات والمقبلات</h3>
            </div>

            <div class="service-item">
                <span>
                    <i class="fas fa-glass-cheers"></i>
                </span>
                <h3 style="font-family: 'Tajawal',sans-serif;">العصــائر</h3>
            </div>

            <div class="service-item">
                <span>
                    <i class="fas fa-pizza-slice"></i>
                </span>
                <h3 style="font-family: 'Tajawal',sans-serif;">المخبوزات والمعجنات</h3>
            </div>
        </div>
    </div>
</section>

<section class="openings padding-y" id="openings">
    <div class="container">
        <div class="title">
            <h2> </h2>
            <div class="line center">
                <div></div>
                <span><i class="fas fa-utensils"></i></span>
                <div></div>
            </div>
        </div>
        <div class="openings-row">
            <h1 style="font-family: 'Tajawal',sans-serif;">عالم النكهات</h1>
            <p class="text" style="font-family: 'Tajawal',sans-serif;">وصفات سهلة ... بطعم لا يُنسى</p>

            <ul>
                <li> . </li>
                <li> . </li>
                <li> . </li>
                <li> . </li>
            </ul>
        </div>
    </div>
</section>

<div class="padding-y menu" id="menu">
    <div class="container">
        <section class="search-section">
            <div class="containerrr">
                <h2>ابحث عن وصفة تحلم بها!</h2>
                <form action="recipe-builder.php" method="GET" class="search-box">
                    <input type="text" name="q" placeholder="اكتب اسم الوصفة أو المكونات..." required="">
                    <button type="submit"><i class="fas fa-search"></i> بحث</button>
                </form>
            </div>
        </section>

        <div class="title">
            <h2 style="font-family: 'Tajawal',sans-serif;">أحدث الوصفات</h2>
            <div class="line center">
                <div></div>
                <span><i class="fas fa-utensils"></i></span>
                <div></div>
            </div>
        </div>

        <section class="recipes-section">
            <div class="container">
                <div class="recipes-grid">
                <?php
                try {
                    $stmt = $pdo->query("SELECT * FROM recipes ORDER BY created_at DESC LIMIT 6");
                    $recipes = $stmt->fetchAll();
                    
                    if (count($recipes) > 0) {
                        foreach ($recipes as $recipe) {
                            $recipe_id = $recipe['id'];
                            $recipe_title = htmlspecialchars($recipe['title']);
                            $recipe_description = htmlspecialchars($recipe['description']);
                            $prep_time = $recipe['prep_time'];
                            $difficulty = $recipe['difficulty'];
                            $image = $recipe['image'];
                            
                            if (!empty($image) && file_exists("uploads/$image")) {
                                $image_path = "uploads/$image";
                            } else {
                                $image_path = "images/pic05.jpg";
                            }
                            ?>
                            <div class="recipe-card">
                                <img src="<?php echo $image_path; ?>" alt="<?php echo $recipe_title; ?>" class="recipe-image">
                                
                                <div class="recipe-info">
                                    <h3 class="recipe-title" style="font-family: 'Tajawal',sans-serif;"><?php echo $recipe_title; ?></h3>
                                    
                                    <div class="recipe-meta">
                                        <span style="font-family: 'Tajawal',sans-serif;"><i class="far fa-clock"></i> <?php echo $prep_time; ?> دقيقة</span>
                                        <span class="recipe-difficulty" style="font-family: 'Tajawal',sans-serif;"><?php echo $difficulty; ?></span>
                                    </div>
                                    
                                    <p class="recipe-description" style="font-family: 'Tajawal',sans-serif;">
                                        <?php 
                                        if (strlen($recipe_description) > 80) {
                                            echo substr($recipe_description, 0, 80) . '...';
                                        } else {
                                            echo $recipe_description;
                                        }
                                        ?>
                                    </p>
                                    
                                    <a href="recipe-detail.php?id=<?php echo $recipe_id; ?>" class="recipe-link" style="font-family: 'Tajawal',sans-serif;">
                                        <i class="fas fa-book-open"></i> عرض الوصفة
                                    </a>
                                </div>
                            </div>
                            <?php
                        }
                    } else {
                        ?>
                        <div class="no-recipes">
                            <p style="font-family: 'Tajawal',sans-serif;" >لا توجد وصفات بعد. كن أول من يضيف وصفة!</p>
                        </div>
                        <?php
                    }
                } catch(PDOException $e) {
                    echo '<div class="error">خطأ في تحميل الوصفات: ' . $e->getMessage() . '</div>';
                }
                ?>
                </div> 
                
                <div style="text-align: center; margin-top: 30px;">
                    <a href="recipes.php" class="btn-primary" style="display: inline-block; text-decoration: none;font-family: 'Tajawal',sans-serif;">
                        <i class="fas fa-list"></i> عرض جميع الوصفات
                    </a>
                </div>
            </div>
        </section>
    </div>
</div>

<?php
require_once 'includes/footer.php';
?>

<script src="assets/jquery/jquery-3.5.1.js"></script>
<!-- Fontawesome js -->
<script src="assets/font-awesome/all.js"></script>
<!-- custom js file -->
<script src="js/script.js"></script>