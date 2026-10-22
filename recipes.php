<?php
require_once 'includes/config.php';
$page_title = 'جميع الوصفات';
require_once 'includes/header.php';

$category = isset($_GET['category']) ? clean_input($_GET['category']) : '';
$difficulty = isset($_GET['difficulty']) ? clean_input($_GET['difficulty']) : '';
$time = isset($_GET['time']) ? intval($_GET['time']) : 0;

$sql = "SELECT * FROM recipes WHERE 1=1";
$params = [];

if (!empty($category)) {
    $sql .= " AND category = ?";
    $params[] = $category;
}

if (!empty($difficulty)) {
    $sql .= " AND difficulty = ?";
    $params[] = $difficulty;
}

if ($time > 0) {
    $sql .= " AND prep_time <= ?";
    $params[] = $time;
}

$sql .= " ORDER BY created_at DESC";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $recipes = $stmt->fetchAll();
    
    $categories_stmt = $pdo->query("SELECT DISTINCT category FROM recipes WHERE category IS NOT NULL ORDER BY category");
    $categories = $categories_stmt->fetchAll();
} catch(PDOException $e) {
    die("خطأ في قاعدة البيانات: " . $e->getMessage());
}
?>

<section class="container" id="contact">
    <div class="recipes-page">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
            <h1 style="font-family: 'Tajawal',sans-serif;"><i class="fas fa-list" ></i> جميع الوصفات</h1>
            <div style="font-size: 0.9rem; color: #666;">
                <i class="fas fa-info-circle"></i> عرض <?php echo count($recipes); ?> وصفة
            </div>
        </div>

        <div class="filters-section" style="background: white; padding: 20px; border-radius: 10px; margin-bottom: 30px; box-shadow: 0 3px 10px rgba(0,0,0,0.08);">
            <h3 style="margin-bottom: 15px; color: #ff6b6b;font-family: 'Tajawal',sans-serif;"><i class="fas fa-filter"></i> تصفية الوصفات</h3>
            <form method="GET" action="recipes.php" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
                <div>
                    <label style="display: block; margin-bottom: 5px; color: #666;font-family: 'Tajawal',sans-serif;">الفئة</label>
                    <select name="category" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;font-family: 'Tajawal',sans-serif;">
                        <option value="" >جميع الفئات</option>
                        <?php foreach($categories as $cat): ?>
                            <option value="<?php echo $cat['category']; ?>" <?php echo ($category == $cat['category']) ? 'selected' : ''; ?>>
                                <?php echo $cat['category']; ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; color: #666;font-family: 'Tajawal',sans-serif;">الصعوبة</label>
                    <select name="difficulty" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;font-family: 'Tajawal',sans-serif;">
                        <option value="">جميع المستويات</option>
                        <option value="سهل" <?php echo ($difficulty == 'سهل') ? 'selected' : ''; ?>>سهل</option>
                        <option value="متوسط" <?php echo ($difficulty == 'متوسط') ? 'selected' : ''; ?>>متوسط</option>
                        <option value="صعب" <?php echo ($difficulty == 'صعب') ? 'selected' : ''; ?>>صعب</option>
                    </select>
                </div>
                
                <div>
                    <label style="display: block; margin-bottom: 5px; color: #666;font-family: 'Tajawal',sans-serif;">الوقت (أقل من دقيقة)</label>
                    <select name="time" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 5px;font-family: 'Tajawal',sans-serif;">
                        <option value="0">لا يهم</option>
                        <option value="15" <?php echo ($time == 15) ? 'selected' : ''; ?>>15 دقيقة</option>
                        <option value="30" <?php echo ($time == 30) ? 'selected' : ''; ?>>30 دقيقة</option>
                        <option value="60" <?php echo ($time == 60) ? 'selected' : ''; ?>>ساعة</option>
                        <option value="120" <?php echo ($time == 120) ? 'selected' : ''; ?>>ساعتين</option>
                    </select>
                </div>
                
                <div style="align-self: end;">
                    <button type="submit" class="btn btn-primary" style="width: 100%;font-family: 'Tajawal',sans-serif;">
                        <i class="fas fa-filter"></i> تطبيق الفلتر
                    </button>
                    <a href="recipes.php" class="btn btn-secondary" style="width: 100%; margin-top: 10px; text-align: center; display: block;font-family: 'Tajawal',sans-serif;">
                        <i class="fas fa-redo"></i> إعادة تعيين
                    </a>
                </div>
            </form>
        </div>

        <?php if(count($recipes) > 0): ?>
            <div class="recipes-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(300px, 1fr)); gap: 25px;">
                <?php foreach($recipes as $recipe): ?>
                    <div class="recipe-card" style="background: white; border-radius: 10px; overflow: hidden; box-shadow: 0 5px 15px rgba(0,0,0,0.1); transition: transform 0.3s;">
                        <?php if(!empty($recipe['image'])): ?>
                            <img src="uploads/<?php echo $recipe['image']; ?>" 
                                 alt="<?php echo $recipe['title']; ?>"
                                 style="width: 100%; height: 200px; object-fit: cover;">
                        <?php else: ?>
                            <div style="width: 100%; height: 200px; background: linear-gradient(45deg, #ff6b6b, #ff8e53); display: flex; align-items: center; justify-content: center; color: white; font-size: 3rem;">
                                <i class="fas fa-utensils"></i>
                            </div>
                        <?php endif; ?>
                        
                        <div style="padding: 20px;">
                            <h3 style="margin-bottom: 10px; color: #333; font-size: 1.2rem;font-family: 'Tajawal',sans-serif;">
                                <?php echo $recipe['title']; ?>
                            </h3>
                            
                            <div style="display: flex; justify-content: space-between; color: #666; font-size: 0.9rem; margin-bottom: 10px;">
                                <span style="font-family: 'Tajawal',sans-serif;"><i class="far fa-clock"></i> <?php echo $recipe['prep_time']; ?> دقيقة</span>
                                <span style="background: #ff6b6b; color: white; padding: 3px 10px; border-radius: 15px; font-size: 0.8rem;">
                                    <?php echo $recipe['difficulty']; ?>
                                </span>
                            </div>
                            
                            <?php if(!empty($recipe['category'])): ?>
                                <div style="margin-bottom: 10px;">
                                    <span style="background: #e9ecef; color: #495057; padding: 3px 8px; border-radius: 15px; font-size: 0.8rem;font-family: 'Tajawal',sans-serif;">
                                        <i class="fas fa-tag"></i> <?php echo $recipe['category']; ?>
                                    </span>
                                </div>
                            <?php endif; ?>
                            
                            <p style="color: #666; margin-bottom: 15px; font-size: 0.9rem; line-height: 1.5;font-family: 'Tajawal',sans-serif;">
                                <?php 
                                $desc = $recipe['description'];
                                echo strlen($desc) > 80 ? substr($desc, 0, 80) . '...' : $desc;
                                ?>
                            </p>
                            
                            <div style="display: flex; gap: 10px;">
                                <a href="recipe-detail.php?id=<?php echo $recipe['id']; ?>" class="btn btn-primary" style="flex: 1; text-align: center;font-family: 'Tajawal',sans-serif;">
                                    <i class="fas fa-book-open"></i> عرض الوصفة
                                </a> 
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 60px 20px; background: white; border-radius: 10px; box-shadow: 0 3px 10px rgba(0,0,0,0.08);">
                <i class="fas fa-utensils" style="font-size: 4rem; color: #ddd; margin-bottom: 20px;"></i>
                <h3 style="color: #666; margin-bottom: 15px;font-family: 'Tajawal',sans-serif;">لا توجد وصفات</h3>
                <p style="color: #999; margin-bottom: 25px;font-family: 'Tajawal',sans-serif;">لم يتم العثور على وصفات تطابق بحثك.</p>
                <a href="add-recipe.php" class="btn btn-primary" style="font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-plus"></i> أضف أول وصفة
                </a>
            </div>
        <?php endif; ?>

        <?php if(count($recipes) > 12): ?>
            <div style="text-align: center; margin-top: 40px;">
                <button onclick="loadMore()" class="btn btn-secondary" style="padding: 12px 40px;">
                    <i class="fas fa-arrow-down"></i> تحميل المزيد
                </button>
            </div>
        <?php endif; ?>
    </div>

    <script>
    function loadMore() {
        alert('ميزة التحميل الإضافي ستكون في النسخة القادمة!');
    }
    </script>
</section>

<?php
require_once 'includes/footer.php';
?>

<script src="assets/jquery/jquery-3.5.1.js"></script>
<!-- Fontawesome js -->
<script src="assets/font-awesome/all.js"></script>
<!-- custom js file -->
<script src="js/script.js"></script>