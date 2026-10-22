<?php
require_once 'includes/config.php';
$page_title = 'البحث الذكي للوصفات';
require_once 'includes/header.php';


$results = [];
$search_performed = false;
$user_ingredients = [];


if(isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $search_query = clean_input($_GET['q']);
    $user_ingredients = explode(' ', $search_query);
    $user_ingredients = array_map('trim', $user_ingredients);
    $user_ingredients = array_filter($user_ingredients);
}


if($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['ingredients'])) {
    $search_performed = true;
    $user_ingredients = explode(',', $_POST['ingredients']);
    $user_ingredients = array_map('trim', $user_ingredients);
    $user_ingredients = array_filter($user_ingredients);
} 

elseif(isset($_GET['q']) && !empty(trim($_GET['q']))) {
    $search_performed = true;
}

if($search_performed && count($user_ingredients) > 0) {
    try {
        $sql = "SELECT *, 
                (";
        foreach($user_ingredients as $index => $ingredient) {
            if($index > 0) $sql .= " + ";
            $sql .= "CASE WHEN ingredients LIKE ? OR title LIKE ? THEN 1 ELSE 0 END";
        }
        $sql .= ") AS match_count,
                (";
        foreach($user_ingredients as $index => $ingredient) {
            if($index > 0) $sql .= " + ";
            $sql .= "CASE WHEN ingredients LIKE ? OR title LIKE ? THEN 1 ELSE 0 END";
        }
        $sql .= ") / " . (count($user_ingredients) * 2) . " * 100 AS match_percentage
                FROM recipes
                HAVING match_count > 0
                ORDER BY match_count DESC, prep_time ASC
                LIMIT 20";
        

        $params = [];
        foreach($user_ingredients as $ingredient) {
            $params[] = "%$ingredient%";
            $params[] = "%$ingredient%";
        }
        foreach($user_ingredients as $ingredient) {
            $params[] = "%$ingredient%";
            $params[] = "%$ingredient%";
        }
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $results = $stmt->fetchAll();
        
    } catch(PDOException $e) {
        echo show_message('error', 'خطأ في البحث: ' . $e->getMessage());
    }
}
?>



<section class = "container" id = "contact">


<div class="recipe-builder">
    <div style="color: black; padding: 40px; border-radius: 15px; margin-bottom: 30px;">
        <div class = "line center">
            <div></div>
            <span><i class = "fas fa-utensils"></i></span>
            <div></div>
        </div>    
    <h1 style="text-align: center; margin-bottom: 10px;font-family: 'Tajawal',sans-serif;"><i class="fas fa-magic"></i> باحث الوصفات الذكي</h1>
        <p style="text-align: center; opacity: 0.9; font-size: 1.1rem;font-family: 'Tajawal',sans-serif;">
            أدخل المكونات المتوفرة لديك، وسنقترح عليك أفضل الوصفات المناسبة!
        </p>
    </div>

    <!-- نموذج إدخال المكونات -->
    <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h3 style="color: #333; margin-bottom: 20px;font-family: 'Tajawal',sans-serif;"><i class="fas fa-plus-circle"></i> أضف المكونات المتوفرة لديك</h3>
        
        <form method="POST" id="smartForm">
            <div style="display: flex; gap: 10px; margin-bottom: 20px; flex-wrap: wrap;">



            <input type="text" id="ingredientInput" 
                placeholder="اكتب مكوناً (مثال: طماطم، دجاج، أرز...)" 
                style="flex: 1; min-width: 200px; padding: 12px 15px; border: 2px solid #ddd; border-radius: 8px; font-size: 1rem;font-family: 'Tajawal',sans-serif;"
                autocomplete="off"
                value="<?php echo isset($_GET['q']) ? htmlspecialchars($_GET['q']) : ''; ?>">
                <button type="button" onclick="addIngredient()" 
                        class="btn btn-primary"
                        style="display: flex; align-items: center; gap: 5px;font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-plus"></i> إضافة مكون
                </button>
            </div>
            
            <!-- عرض المكونات المضافة -->
            <div id="ingredientsContainer" style="margin-bottom: 20px; min-height: 50px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                <div id="ingredientsList" style="display: flex; flex-wrap: wrap; gap: 10px;"></div>
                <div id="noIngredientsMessage" style="text-align: center; color: #999; padding: 10px;font-family: 'Tajawal',sans-serif;">
                    لم تقم بإضافة أي مكونات بعد. ابدأ بإضافة المكونات المتوفرة لديك.
                </div>
            </div>
            
            <input type="hidden" name="ingredients" id="hiddenIngredients">
            
            <div style="text-align: center;">
                <button type="submit" class="btn btn-primary" style="padding: 12px 40px; font-size: 1.1rem;font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-search"></i> ابحث عن وصفات مناسبة
                </button>
                <button type="button" onclick="clearAllIngredients()" class="btn btn-primary" style="margin-right: 10px;font-family: 'Tajawal',sans-serif;background: #fa1414ff;">
                    <i class="fas fa-trash"></i> مسح الكل
                </button>
            </div>
        </form>
    </div>

    <!-- عرض النتائج -->
    <?php if($search_performed): ?>
        <div style="background: white; padding: 30px; border-radius: 10px; box-shadow: 0 5px 15px rgba(0,0,0,0.1);">
            <h3 style="color: #333; margin-bottom: 20px;font-family: 'Tajawal',sans-serif;">
                <i class="fas fa-search"></i> نتائج البحث
                <span style="font-size: 0.9rem; color: #666; font-weight: normal;">
                    (<?php echo count($results); ?> وصفة وجدت)
                </span>
            </h3>
            
            <?php if(count($results) > 0): ?>
                <div style="color: #666; margin-bottom: 20px; padding: 15px; background: #e7f3ff; border-radius: 8px;font-family: 'Tajawal',sans-serif;">
                    <i class="fas fa-info-circle"></i>
                    تم العثور على <?php echo count($results); ?> وصفة تحتوي على 
                    <?php echo count($user_ingredients ?? []); ?> من المكونات التي أدخلتها.
                </div>
                
                <!-- شريط ترتيب النتائج -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; padding: 15px; background: #f8f9fa; border-radius: 8px;">
                    <div style="color: #666;font-family: 'Tajawal',sans-serif;">
                        <i class="fas fa-sort"></i> ترتيب حسب:
                    </div>
                    <div>
                        <select id="sortResults" onchange="sortRecipes()" style="padding: 8px 15px; border: 1px solid #ddd; border-radius: 5px;font-family: 'Tajawal',sans-serif;">
                            <option value="match">الأكثر تطابقاً</option>
                            <option value="time">الأسرع تحضيراً</option>
                            <option value="easy">الأسهل</option>
                        </select>
                    </div>
                </div>
                
                <!-- قائمة النتائج -->
                <div id="resultsContainer">
                    <?php foreach($results as $recipe): ?>
                        <div class="result-card" 
                             data-match="<?php echo $recipe['match_percentage']; ?>"
                             data-time="<?php echo $recipe['prep_time']; ?>"
                             data-difficulty="<?php echo $recipe['difficulty'] == 'سهل' ? 1 : ($recipe['difficulty'] == 'متوسط' ? 2 : 3); ?>"
                             style="margin-bottom: 20px; padding: 20px; border: 1px solid #eee; border-radius: 10px; transition: all 0.3s;">
                            <div style="display: flex; gap: 20px; align-items: start;">
                                <?php if(!empty($recipe['image'])): ?>
                                    <img src="uploads/<?php echo $recipe['image']; ?>" 
                                         alt="<?php echo $recipe['title']; ?>"
                                         style="width: 120px; height: 120px; object-fit: cover; border-radius: 8px;">
                                <?php else: ?>
                                    <div style="width: 120px; height: 120px; background: linear-gradient(45deg, #ff6b6b, #ff8e53); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: white; font-size: 2rem;">
                                        <i class="fas fa-utensils"></i>
                                    </div>
                                <?php endif; ?>
                                
                                <div style="flex: 1;">
                                    <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                                        <h4 style="margin: 0; color: #333;font-family: 'Tajawal',sans-serif;">
                                            <a href="recipe-detail.php?id=<?php echo $recipe['id']; ?>" style="color: #333; text-decoration: none;font-family: 'Tajawal',sans-serif;">
                                                <?php echo $recipe['title']; ?>
                                            </a>
                                        </h4>
                                        
                                        <!-- نسبة التطابق -->
                                        <div style="background: #28a745; color: white; padding: 5px 15px; border-radius: 20px; font-weight: bold; font-size: 0.9rem;font-family: 'Tajawal',sans-serif;">
                                            <?php echo round($recipe['match_percentage']); ?>% تطابق
                                        </div>
                                    </div>
                                    
                                    <!-- معلومات الوصفة -->
                                    <div style="display: flex; gap: 15px; margin-bottom: 10px; color: #666; font-size: 0.9rem;">
                                        <span style="font-family: 'Tajawal',sans-serif;"><i class="far fa-clock"></i> <?php echo $recipe['prep_time']; ?> دقيقة</span>
                                        <span><i class="fas fa-bolt"></i> <?php echo $recipe['difficulty']; ?></span>
                                        <?php if(!empty($recipe['category'])): ?>
                                            <span style="font-family: 'Tajawal',sans-serif;"><i class="fas fa-tag"></i> <?php echo $recipe['category']; ?></span>
                                        <?php endif; ?>
                                    </div>
                                    
                                    <!-- المكونات المتطابقة -->
                                    <div style="margin-bottom: 15px;">
                                        <div style="font-size: 0.9rem; color: #666; margin-bottom: 5px;font-family: 'Tajawal',sans-serif;">
                                            <i class="fas fa-check-circle" style="color: #28a745;"></i>
                                            المكونات المتوفرة:
                                        </div>
                                        <div style="display: flex; flex-wrap: wrap; gap: 8px;font-family: 'Tajawal',sans-serif;">
                                            <?php
                                            foreach($user_ingredients as $ingredient):
                                                if(stripos($recipe['ingredients'], $ingredient) !== false):
                                            ?>
                                                <span style="background: #d4edda; color: #155724; padding: 4px 10px; border-radius: 15px; font-size: 0.85rem;font-family: 'Tajawal',sans-serif;">
                                                    <i class="fas fa-check"></i> <?php echo $ingredient; ?>
                                                </span>
                                            <?php
                                                endif;
                                            endforeach;
                                            ?>
                                        </div>
                                    </div>
                                    
                                    <!-- وصف مختصر -->
                                    <p style="color: #666; font-size: 0.9rem; line-height: 1.5; margin-bottom: 15px;font-family: 'Tajawal',sans-serif;">
                                        <?php 
                                        $desc = $recipe['description'];
                                        echo strlen($desc) > 150 ? substr($desc, 0, 150) . '...' : $desc;
                                        ?>
                                    </p>
                                    
                                    <a href="recipe-detail.php?id=<?php echo $recipe['id']; ?>" class="btn btn-primary" style="padding: 8px 20px;font-family: 'Tajawal',sans-serif;">
                                        <i class="fas fa-book-open"></i> عرض الوصفة الكاملة
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                

                
            <?php else: ?>
                <div style="text-align: center; padding: 40px 20px;">
                    <i class="fas fa-search" style="font-size: 4rem; color: #ddd; margin-bottom: 20px;"></i>
                    <h4 style="color: #666; margin-bottom: 15px;font-family: 'Tajawal',sans-serif;">لم نعثر على وصفات مناسبة</h4>
                    <p style="color: #999; margin-bottom: 20px;font-family: 'Tajawal',sans-serif;">
                        لم نتمكن من العثور على وصفات تحتوي على المكونات التي أدخلتها.<br>
                        حاول إضافة مكونات مختلفة أو تصفح <a href="recipes.php" style="font-family: 'Tajawal',sans-serif;">جميع الوصفات</a>.
                    </p>
                    <button onclick="clearAllIngredients()" class="btn btn-primary" style="font-family: 'Tajawal',sans-serif;">
                        <i class="fas fa-redo"></i> جرب مكونات مختلفة
                    </button>
                </div>
            <?php endif; ?>
        </div>
    <?php endif; ?>

    <!-- اقتراحات للمستخدم -->
    <?php if(!$search_performed): ?>
        <div style="background: #e7f3ff; padding: 25px; border-radius: 10px; margin-top: 30px;">
            <h4 style="color: #0056b3; margin-bottom: 15px;font-family: 'Tajawal',sans-serif;"><i class="fas fa-lightbulb"></i> نصائح للاستخدام</h4>
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 15px;">
                <div style="padding: 15px; background: white; border-radius: 8px;">
                    <div style="font-size: 1.5rem; color: #ff6b6b; margin-bottom: 10px;">
                        <i class="fas fa-list"></i>
                    </div>
                    <div style="font-weight: bold; color: #333; margin-bottom: 5px;font-family: 'Tajawal',sans-serif;">أدخل المكونات الأساسية</div>
                    <div style="color: #666; font-size: 0.9rem;font-family: 'Tajawal',sans-serif;">أدخل 3-5 مكونات رئيسية متوفرة لديك للحصول على أفضل النتائج</div>
                </div>
                <div style="padding: 15px; background: white; border-radius: 8px;">
                    <div style="font-size: 1.5rem; color: #28a745; margin-bottom: 10px;">
                        <i class="fas fa-clock"></i>
                    </div>
                    <div style="font-weight: bold; color: #333; margin-bottom: 5px;font-family: 'Tajawal',sans-serif;">وفر الوقت</div>
                    <div style="color: #666; font-size: 0.9rem;font-family: 'Tajawal',sans-serif;">ابحث عن وصفات تستخدم ما لديك بالفعل في المطبخ</div>
                </div>
                <div style="padding: 15px; background: white; border-radius: 8px;">
                    <div style="font-size: 1.5rem; color: #007bff; margin-bottom: 10px;">
                        <i class="fas fa-utensils"></i>
                    </div>
                    <div style="font-weight: bold; color: #333; margin-bottom: 5px;font-family: 'Tajawal',sans-serif;">اكتشف وصفات جديدة</div>
                    <div style="color: #666; font-size: 0.9rem;font-family: 'Tajawal',sans-serif;">تعرف على وصفات لم تكن تعلم أنها ممكنة بمكوناتك</div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>


</section>

<?php
require_once 'includes/footer.php';
?>
    

<script>
let ingredients = [];

function addIngredient() {
    const input = document.getElementById('ingredientInput');
    const ingredient = input.value.trim();
    
    if (ingredient && !ingredients.includes(ingredient)) {
        ingredients.push(ingredient);
        updateIngredientsList();
        input.value = '';
        input.focus();
    }
}

function removeIngredient(index) {
    ingredients.splice(index, 1);
    updateIngredientsList();
}

function updateIngredientsList() {
    const listDiv = document.getElementById('ingredientsList');
    const container = document.getElementById('ingredientsContainer');
    const noIngredientsMsg = document.getElementById('noIngredientsMessage');
    const hiddenInput = document.getElementById('hiddenIngredients');
    
    listDiv.innerHTML = '';
    
    if (ingredients.length > 0) {
        noIngredientsMsg.style.display = 'none';
        
        ingredients.forEach((ingredient, index) => {
            const badge = document.createElement('div');
            badge.style.cssText = `
                background: #ff6b6b;
                color: white;
                padding: 8px 15px;
                border-radius: 20px;
                display: flex;
                align-items: center;
                gap: 8px;
                font-size: 0.9rem;
            `;
            
            badge.innerHTML = `
                ${ingredient}
                <button type="button" onclick="removeIngredient(${index})" 
                        style="background: none; border: none; color: white; cursor: pointer; padding: 0; font-size: 0.8rem;">
                    <i class="fas fa-times"></i>
                </button>
            `;
            
            listDiv.appendChild(badge);
        });
        
        hiddenInput.value = ingredients.join(', ');
    } else {
        noIngredientsMsg.style.display = 'block';
        hiddenInput.value = '';
    }
}

function clearAllIngredients() {
    ingredients = [];
    updateIngredientsList();
    document.getElementById('ingredientInput').focus();
}

function sortRecipes() {
    const sortBy = document.getElementById('sortResults').value;
    const container = document.getElementById('resultsContainer');
    const cards = Array.from(container.getElementsByClassName('result-card'));
    
    cards.sort((a, b) => {
        if (sortBy === 'match') {
            return parseFloat(b.dataset.match) - parseFloat(a.dataset.match);
        } else if (sortBy === 'time') {
            return parseFloat(a.dataset.time) - parseFloat(b.dataset.time);
        } else if (sortBy === 'easy') {
            return parseFloat(a.dataset.difficulty) - parseFloat(b.dataset.difficulty);
        }
        return 0;
    });
    

    cards.forEach(card => container.appendChild(card));
}


document.getElementById('ingredientInput').addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        e.preventDefault();
        addIngredient();
    }
});


window.addEventListener('load', function() {
    const exampleIngredients = ['طماطم', 'بصل', 'دجاج', 'أرز', 'زيت', 'ملح'];
    const exampleBtn = document.createElement('button');
    exampleBtn.type = 'button';
    exampleBtn.className = 'btn btn-secondary';
    exampleBtn.innerHTML = '<i class="fas fa-lightbulb"></i> جرب أمثلة';
    exampleBtn.style.marginTop = '10px';
    exampleBtn.onclick = function() {
        ingredients = [...exampleIngredients];
        updateIngredientsList();
    };
    
    document.querySelector('.ingredients-input').appendChild(exampleBtn);
});


</script>




    <!-- jQuery -->
    <script src = "assets/jquery/jquery-3.5.1.js"></script>
    <!-- Fontawesome js -->
    <script src = "assets/font-awesome/all.js"></script>
    <!-- custom js file -->
    <script src="js/script.js"></script>