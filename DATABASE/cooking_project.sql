-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: Dec 07, 2025 at 02:48 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `cooking_project`
--

-- --------------------------------------------------------

--
-- Table structure for table `recipes`
--

CREATE TABLE `recipes` (
  `id` int(11) NOT NULL,
  `title` varchar(200) NOT NULL,
  `description` text DEFAULT NULL,
  `ingredients` text NOT NULL,
  `instructions` text NOT NULL,
  `image` varchar(255) DEFAULT NULL,
  `prep_time` int(11) DEFAULT NULL,
  `category` varchar(100) DEFAULT NULL,
  `difficulty` enum('سهل','متوسط','صعب') DEFAULT 'سهل',
  `created_at` timestamp NOT NULL DEFAULT current_timestamp(),
  `user_id` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `recipes`
--

INSERT INTO `recipes` (`id`, `title`, `description`, `ingredients`, `instructions`, `image`, `prep_time`, `category`, `difficulty`, `created_at`, `user_id`) VALUES
(1, 'معكرونة بالبشاميل', 'وصفة إيطالية شهيرة ولذيذة', '500غ معكرونة، 2 كوب حليب، 2 ملعقة زبدة، 2 ملعقة دقيق، ملح وفلفل', '1. اسلقي المعكرونة في ماء مغلي مملح لمدة 8 دقائق\r\n2. في مقلاة، ذوبي الزبدة ثم أضيفي الدقيق وقلبي\r\n3. أضيفي الحليب تدريجياً مع التحريك حتى تحصلي على صلصة كثيفة\r\n4. ضعي المعكرونة في طبق الخبز، صفي البشاميل فوقها\r\n5. اخبزي في فرن محمى على 180°م لمدة 20 دقيقة', '6933f64e11eb2_1765013070.jpg', 45, 'أطباق رئيسية', 'سهل', '2025-12-06 00:25:01', NULL),
(2, 'سلطة الخضار الطازجة', 'سلطة صحية وسريعة التحضير', 'خس، 2 حبة طماطم، خيار، بصل أحمر، زيت زيتون، ليمون، ملح، نعناع طازج', '1. اغسلي جميع الخضار جيداً تحت الماء الجاري\r\n2. قطّعي الخس، الطماطم، الخيار، والبصل شرائح رقيقة\r\n3. في وعاء الخلط، أضيفي الخضار المقطعة\r\n4. في كوب صغير، اخلطي زيت الزيتون، عصير الليمون، والملح\r\n5. صبي التتبيلة على السلطة وقلبي برفق\r\n6. زيني بأوراق النعناع الطازجة وقدمي فوراً', '6935699b0595e_1765108123.webp', 15, 'سلطات', 'سهل', '2025-12-06 00:25:01', NULL),
(3, 'كيك الشوكولاتة', 'كيك رطب وشهي', '2 كوب طحين، 1 كوب سكر، 3 بيضات، 1/2 كوب زيت، 1/2 كوب كاكاو، 1 كوب حليب، 1 ملعقة صغيرة بيكنج باودر، 1 ملعقة صغيرة فانيليا', '1. سخني الفرن على درجة 180°م\r\n2. في وعاء كبير، اخفقي البيض مع السكر حتى يصبح الخليط فاتح اللون\r\n3. أضيفي الزيت، الحليب، والفانيليا وقلبي جيداً\r\n4. في وعاء منفصل، اخلطي الطحين، الكاكاو، والبيكنج باودر\r\n5. أضيفي المكونات الجافة إلى الخليط السائل تدريجياً\r\n6. اسكبي الخليط في قالب مدهون بالزبدة ومرشوش بالطحين\r\n7. اخبزي لمدة 35-40 دقيقة أو حتى ينضج الكيك', '6935649497387_1765106836.jpg', 60, 'حلويات', 'متوسط', '2025-12-06 00:25:01', NULL),
(4, 'أرز بالدجاج', 'طبق عربي تقليدي', '2 كوب أرز بسمتي، 500غ دجاج مقطع، بصل كبير مفروم، 4 فصوص ثوم مفروم، بهارات مشكلة، ملح، فلفل أسود، زيت زيتون، ماء', '1. في قدر كبير، حمري البصل في الزيت حتى يذبل\r\n2. أضيفي الثوم وقلبي لدقيقة واحدة\r\n3. أضيفي قطع الدجاج وقلبي حتى تتحمر من جميع الجهات\r\n4. أضيفي البهارات، الملح، والفلفل\r\n5. أضيفي الأرز المغسول وقلبي لمدة دقيقتين\r\n6. أضيفي الماء (نسبة الماء للأرز 2:1)\r\n7. غطي القدر واتركيه على نار هادئة لمدة 20 دقيقة\r\n8. اتركيه يرتاح لمدة 10 دقائق قبل التقديم', '69356b2a0db95_1765108522.jpg', 50, 'أطباق رئيسية', 'سهل', '2025-12-06 00:25:01', NULL),
(5, 'شوربة الخضار', 'شوربة دافئة للشتاء', '2 حبة جزر، 2 حبة بطاطس، 1 كوسا، 1 بصل، 4 كوب مرقة دجاج، 1/2 كوب كريمة طبخ، ملح، فلفل، زعتر', '1. قشري وقطعي جميع الخضار إلى مكعبات صغيرة\r\n2. في قدر كبير، حمري البصل في ملعقة زيت زيتون\r\n3. أضيفي بقية الخضار وقلبي لمدة 5 دقائق\r\n4. أضيفي مرقة الدجاج واتركيها تغلي\r\n5. خففي النار واطبخي لمدة 25 دقيقة حتى تنضج الخضار\r\n6. استخدمي الخلاط اليدوي لهرس الشوربة حسب الرغبة (ناعمة أو خشنة)\r\n7. أضيفي الكريمة، الملح، الفلفل، والزعتر\r\n8. قدمي الشوربة ساخنة مع خبز محمص', '69356a4456341_1765108292.jpeg', 35, 'شوربات', 'سهل', '2025-12-06 00:25:01', NULL),
(6, 'بيتزا بالجبنة', 'بيتزا منزلية سهلة وسريعة', 'عجينة بيتزا جاهزة، 2 كوب جبنة موزاريلا، 1 كوب صلصة طماطم، فلفل حلو مقطع، زيتون أسود، زعتر، زيت زيتون', '1. سخني الفرن على درجة 200°م\r\n2. افردي عجينة البيتزا في صينية الخبز\r\n3. وزعي صلصة الطماطم بشكل متساوي\r\n4. رشي الجبنة الموزاريلا فوق الصلصة\r\n5. وزعي قطع الفلفل والزيتون\r\n6. رشي القليل من الزعتر وزيت الزيتون\r\n7. اخبزي في الفرن لمدة 15-20 دقيقة حتى تذوب الجبنة وتتحمر الحواف\r\n8. اخرجيها من الفرن وقطعيها مثلثات', '6935669b2ad75_1765107355.jpg', 30, 'أطباق رئيسية', 'سهل', '2025-12-06 00:25:01', NULL),
(7, 'عصير البرتقال الطازج', 'عصير منعش وصحي', '4 حبات برتقال، 1 ملعقة سكر (اختياري)، مكعبات ثلج، أوراق نعناع للتزيين', '1. قشري البرتقال وازيلي البذور\r\n2. ضعي البرتقال في الخلاط الكهربائي\r\n3. أضيفي السكر إذا رغبت\r\n4. اخفقي لمدة دقيقة واحدة\r\n5. صفي العصير من الشوائب\r\n6. اسكبي في أكواب مع مكعبات الثلج\r\n7. زيني بأوراق النعناع وقدمي فوراً', '69356bd799748_1765108695.jpg', 10, 'مشروبات', 'سهل', '2025-12-06 00:25:01', NULL);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `id` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `is_admin` tinyint(1) DEFAULT 0,
  `created_at` timestamp NOT NULL DEFAULT current_timestamp()
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`id`, `username`, `email`, `password`, `is_admin`, `created_at`) VALUES
(2, 'ahmed', 'ahmed@recipe.com', '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', 0, '2025-12-06 00:25:01'),
(4, 'noor', 'noor@gmail.com', '$2y$10$O6ZffoCUojF5.DSuhp5FM.cnbDkC7glJOv0oWuPsI72j6Q11jcFTm', 1, '2025-12-06 00:59:18'),
(5, 'naser', 'naser1@gmail.com', '$2y$10$OT1N/Ch2vd8a6jntpT.3rOX6vY4hKuIjtjfGhupslQ398J9ZYtcky', 0, '2025-12-06 07:11:01'),
(6, 'reem', 'reem@gmail.com', '$2y$10$2KPQ29dLjAMZGuHJCrfbPO8waB9MfBGO73BTU1IU9iGFyOWSVXkwW', 1, '2025-12-07 11:18:55');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `recipes`
--
ALTER TABLE `recipes`
  ADD PRIMARY KEY (`id`),
  ADD KEY `user_id` (`user_id`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `username` (`username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `recipes`
--
ALTER TABLE `recipes`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `recipes`
--
ALTER TABLE `recipes`
  ADD CONSTRAINT `recipes_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
