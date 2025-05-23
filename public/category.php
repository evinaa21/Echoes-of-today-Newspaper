<?php
// Include database connection
require_once '../includes/db_connection.php';
// Include weather function
require_once '../includes/weather.php';

// Get weather data for Tirana
$weather = getWeather('Tirana', 'AL');

// Get category slug from URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($slug)) {
    // Redirect to homepage if no slug provided
    header('Location: index.php');
    exit();
}

// Get category details
$cat_query = "SELECT id, name, slug, description FROM categories WHERE slug = ? AND is_active = 1";
$stmt = $conn->prepare($cat_query);
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Category not found or not active
    header('Location: index.php');
    exit();
}

$category = $result->fetch_assoc();
$category_id = $category['id'];

// Get all categories for navigation
$all_categories_query = "SELECT id, name, slug FROM categories WHERE is_active = 1 ORDER BY display_order ASC";
$categories = $conn->query($all_categories_query);

// Pagination settings
$articles_per_page = 9;
$page = isset($_GET['page']) ? (int) $_GET['page'] : 1;
$page = max(1, $page); // Ensure page is at least 1
$offset = ($page - 1) * $articles_per_page;

// Sorting options
$sort_options = [
    'newest' => 'a.published_at DESC',
    'oldest' => 'a.published_at ASC',
    'most_viewed' => 'a.view_count DESC',
    'title_az' => 'a.title ASC',
    'title_za' => 'a.title DESC'
];

$sort = isset($_GET['sort']) && array_key_exists($_GET['sort'], $sort_options) ? $_GET['sort'] : 'newest';
$order_by = $sort_options[$sort];

// Count total articles in this category
$count_query = "SELECT COUNT(*) AS total FROM articles a WHERE a.category_id = ? AND a.status = 'published'";
$stmt = $conn->prepare($count_query);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$count_result = $stmt->get_result();
$total_articles = $count_result->fetch_assoc()['total'];

// Calculate total pages
$total_pages = ceil($total_articles / $articles_per_page);
$page = min($page, max(1, $total_pages)); // Ensure page doesn't exceed total pages

// Get featured article for this category (most recent with highest views)
$featured_query = "SELECT a.*, CONCAT(u.first_name, ' ', u.last_name) as author_name
                   FROM articles a 
                   JOIN users u ON a.author_id = u.id 
                   WHERE a.category_id = ? AND a.status = 'published'
                   ORDER BY a.view_count DESC, a.published_at DESC 
                   LIMIT 1";
$stmt = $conn->prepare($featured_query);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$featured_article = $stmt->get_result()->fetch_assoc();

// If we have a featured article, exclude it from the regular listing
$featured_exclusion = $featured_article ? "AND a.id != " . $featured_article['id'] : "";

// Get articles for this category
$articles_query = "SELECT a.*, CONCAT(u.first_name, ' ', u.last_name) as author_name 
                  FROM articles a 
                  JOIN users u ON a.author_id = u.id 
                  WHERE a.category_id = ? AND a.status = 'published' 
                  $featured_exclusion
                  ORDER BY $order_by 
                  LIMIT ? OFFSET ?";
$stmt = $conn->prepare($articles_query);
$stmt->bind_param("iii", $category_id, $articles_per_page, $offset);
$stmt->execute();
$articles = $stmt->get_result();

// Get popular articles from this category
$popular_query = "SELECT a.id, a.title, a.slug, a.excerpt, a.featured_image, a.published_at, a.view_count, 
                 CONCAT(u.first_name, ' ', u.last_name) as author_name 
                 FROM articles a 
                 JOIN users u ON a.author_id = u.id 
                 WHERE a.category_id = ? AND a.status = 'published'
                 ORDER BY a.view_count DESC 
                 LIMIT 5";
$stmt = $conn->prepare($popular_query);
$stmt->bind_param("i", $category_id);
$stmt->execute();
$popular_articles = $stmt->get_result();

// Get sidebar advertisement
$sidebar_ad_query = "SELECT * FROM advertisements 
                   WHERE ad_type = 'sidebar' AND is_active = 1 
                   AND NOW() BETWEEN start_date AND end_date 
                   ORDER BY RAND() LIMIT 1";
$sidebar_ad_result = $conn->query($sidebar_ad_query);
$sidebar_ad = $sidebar_ad_result->fetch_assoc();

// Time function to format "time ago"
function time_elapsed_string($datetime)
{
    $now = new DateTime;
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);

    if ($diff->y > 0)
        return $diff->y . ' year' . ($diff->y > 1 ? 's' : '') . ' ago';
    if ($diff->m > 0)
        return $diff->m . ' month' . ($diff->m > 1 ? 's' : '') . ' ago';
    if ($diff->d > 0)
        return $diff->d . ' day' . ($diff->d > 1 ? 's' : '') . ' ago';
    if ($diff->h > 0)
        return $diff->h . ' hour' . ($diff->h > 1 ? 's' : '') . ' ago';
    if ($diff->i > 0)
        return $diff->i . ' minute' . ($diff->i > 1 ? 's' : '') . ' ago';
    return 'just now';
}

// Get category class for styling
function getCategoryClass($categoryName)
{
    $name = strtolower($categoryName);
    if (strpos($name, 'politic') !== false)
        return 'category-politics';
    if (strpos($name, 'sport') !== false)
        return 'category-sports';
    if (strpos($name, 'entertain') !== false)
        return 'category-entertainment';
    if (strpos($name, 'business') !== false)
        return 'category-business';
    if (strpos($name, 'tech') !== false)
        return 'category-tech';
    if (strpos($name, 'health') !== false)
        return 'category-health';
    if (strpos($name, 'science') !== false)
        return 'category-science';
    if (strpos($name, 'world') !== false)
        return 'category-world';
    if (strpos($name, 'lifestyle') !== false)
        return 'category-lifestyle';
    return '';
}

// Function to generate pagination URL
function getPaginationUrl($page, $sort)
{
    global $slug;
    return "category.php?slug=" . urlencode($slug) . "&sort=" . urlencode($sort) . "&page=" . $page;
}

// Function to generate sort URL
function getSortUrl($sort)
{
    global $slug, $page;
    return "category.php?slug=" . urlencode($slug) . "&sort=" . urlencode($sort) . "&page=" . $page;
}

$categoryClass = getCategoryClass($category['name']);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($category['name']); ?> News - Echoes of Today</title>
    <meta name="description"
        content="Latest <?php echo htmlspecialchars($category['name']); ?> news and articles from Echoes of Today">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400;700&family=Open+Sans:wght@400;600;700&display=swap"
        rel="stylesheet">
</head>

<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="date-weather">
                <span class="current-date"><?php echo date("l, F j, Y"); ?></span>
                <span class="weather">
                    <?php echo $weather['temp']; ?>°C Tirana
                    <i class="fas fa-<?php echo getWeatherIcon($weather['condition']); ?>"></i>
                </span>
            </div>
            <div class="social-links">
                <a href="#"><i class="fab fa-facebook-f"></i></a>
                <a href="#"><i class="fab fa-twitter"></i></a>
                <a href="#"><i class="fab fa-instagram"></i></a>
                <a href="#"><i class="fab fa-youtube"></i></a>
            </div>
        </div>
    </div>

    <!-- Header -->
    <header>
        <div class="container">
            <div class="logo">
                <h1><a href="index.php">ECHOES TODAY</a></h1>
                <p class="tagline">The Voice of Our Times</p>
            </div>
            <div class="header-ad"></div>
            <div class="ad-space">
                <div class="ad-placeholder">Advertisement</div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav>
        <div class="container">
            <ul class="main-nav">
                <li><a href="index.php">Home</a></li>
                <?php
                // Reset categories query
                $categories = $conn->query($all_categories_query);
                $count = 0;
                $dropdown_items = array();

                // First collect all categories
                while ($cat = $categories->fetch_assoc()) {
                    $is_active = ($cat['slug'] == $slug) ? 'class="active"' : '';

                    if ($count < 6) {
                        // First 6 categories go directly in the nav
                        ?>
                        <li <?php echo $is_active; ?>><a href="category.php?slug=<?php echo htmlspecialchars($cat['slug']); ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </a></li>
                        <?php
                    } else {
                        // Store the rest for the dropdown
                        $dropdown_items[] = [
                            'category' => $cat,
                            'is_active' => $is_active
                        ];
                    }
                    $count++;
                }

                // If we have extra categories, add the dropdown
                if (!empty($dropdown_items)) {
                    // Check if any dropdown item is active
                    $dropdown_active = '';
                    foreach ($dropdown_items as $item) {
                        if (!empty($item['is_active'])) {
                            $dropdown_active = 'class="active"';
                            break;
                        }
                    }
                    ?>
                    <li class="dropdown" <?php echo $dropdown_active; ?>>
                        <a href="#" class="dropdown-toggle">More <i class="fas fa-caret-down"></i></a>
                        <ul class="dropdown-content">
                            <?php foreach ($dropdown_items as $item) { ?>
                                <li <?php echo $item['is_active']; ?>><a
                                        href="category.php?slug=<?php echo htmlspecialchars($item['category']['slug']); ?>">
                                        <?php echo htmlspecialchars($item['category']['name']); ?>
                                    </a></li>
                            <?php } ?>
                        </ul>
                    </li>
                    <?php
                }
                ?>
            </ul>
            <div class="search-box">
                <form action="search.php" method="GET">
                    <input type="text" name="q" placeholder="Search..." required>
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <div class="container">
            <!-- Category Header -->
            <div class="category-header">
                <h1 class="category-title">
                    <span class="category-label <?php echo $categoryClass; ?>">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </span>
                    News
                </h1>
                <?php if (!empty($category['description'])): ?>
                    <p class="category-description"><?php echo htmlspecialchars($category['description']); ?></p>
                <?php endif; ?>
            </div>

            <div class="two-column-layout">
                <div class="main-content">
                    <!-- Sort Controls - Improved Version -->
                    <div class="sort-controls">
                        <div class="sort-by">
                            <span>Sort by:</span>
                            <div class="sort-options">
                                <a href="<?php echo getSortUrl('newest'); ?>"
                                    class="sort-option <?php echo ($sort === 'newest') ? 'active' : ''; ?>">
                                    <i class="fas fa-clock"></i> Newest
                                </a>
                                <a href="<?php echo getSortUrl('oldest'); ?>"
                                    class="sort-option <?php echo ($sort === 'oldest') ? 'active' : ''; ?>">
                                    <i class="fas fa-history"></i> Oldest
                                </a>
                                <a href="<?php echo getSortUrl('most_viewed'); ?>"
                                    class="sort-option <?php echo ($sort === 'most_viewed') ? 'active' : ''; ?>">
                                    <i class="fas fa-eye"></i> Most Viewed
                                </a>
                                <a href="<?php echo getSortUrl('title_az'); ?>"
                                    class="sort-option <?php echo ($sort === 'title_az') ? 'active' : ''; ?>">
                                    <i class="fas fa-sort-alpha-down"></i> Title A-Z
                                </a>
                                <a href="<?php echo getSortUrl('title_za'); ?>"
                                    class="sort-option <?php echo ($sort === 'title_za') ? 'active' : ''; ?>">
                                    <i class="fas fa-sort-alpha-up"></i> Title Z-A
                                </a>
                            </div>
                        </div>
                        <div class="view-options">
                            <button class="view-option active" data-view="grid">
                                <i class="fas fa-th"></i> Grid
                            </button>
                            <button class="view-option" data-view="list">
                                <i class="fas fa-list"></i> List
                            </button>
                        </div>
                    </div>

                    <?php if ($total_articles === 0): ?>
                        <!-- No articles message -->
                        <div class="no-articles">
                            <i class="fas fa-newspaper"></i>
                            <h3>No articles found</h3>
                            <p>There are currently no published articles in this category.</p>
                        </div>
                    <?php else: ?>
                        <!-- Featured Category Article -->
                        <?php if ($featured_article): ?>
                            <div class="featured-category-article">
                                <div class="featured-image">
                                    <a href="article.php?slug=<?php echo htmlspecialchars($featured_article['slug']); ?>">
                                        <?php if ($featured_article['featured_image']): ?>
                                            <img src="<?php echo htmlspecialchars($featured_article['featured_image']); ?>"
                                                alt="<?php echo htmlspecialchars($featured_article['title']); ?>">
                                        <?php else: ?>
                                            <img src="https://source.unsplash.com/random/1200x600/?<?php echo htmlspecialchars($slug); ?>"
                                                alt="<?php echo htmlspecialchars($category['name']); ?>">
                                        <?php endif; ?>
                                    </a>
                                </div>
                                <div class="featured-content">
                                    <h2>
                                        <a href="article.php?slug=<?php echo htmlspecialchars($featured_article['slug']); ?>">
                                            <?php echo htmlspecialchars($featured_article['title']); ?>
                                        </a>
                                    </h2>
                                    <p class="featured-excerpt"><?php echo htmlspecialchars($featured_article['excerpt']); ?>
                                    </p>
                                    <div class="meta-info">
                                        <span
                                            class="time"><?php echo time_elapsed_string($featured_article['published_at']); ?></span>
                                        <span class="author">By
                                            <?php echo htmlspecialchars($featured_article['author_name']); ?></span>
                                        <span class="views"><i class="fas fa-eye"></i>
                                            <?php echo number_format($featured_article['view_count']); ?></span>
                                    </div>
                                </div>
                            </div>
                        <?php endif; ?>

                        <!-- Articles Grid / List -->
                        <div id="articlesContainer" class="news-grid category-grid">
                            <?php if ($articles->num_rows > 0): ?>
                                <?php while ($article = $articles->fetch_assoc()): ?>
                                    <div class="news-item">
                                        <div class="news-item-image">
                                            <a href="article.php?slug=<?php echo htmlspecialchars($article['slug']); ?>">
                                                <?php if ($article['featured_image']): ?>
                                                    <img src="<?php echo htmlspecialchars($article['featured_image']); ?>"
                                                        alt="<?php echo htmlspecialchars($article['title']); ?>">
                                                <?php else: ?>
                                                    <img src="https://source.unsplash.com/random/300x200/?<?php echo htmlspecialchars($slug); ?>"
                                                        alt="<?php echo htmlspecialchars($category['name']); ?>">
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                        <div class="news-item-content">
                                            <h3>
                                                <a href="article.php?slug=<?php echo htmlspecialchars($article['slug']); ?>">
                                                    <?php echo htmlspecialchars($article['title']); ?>
                                                </a>
                                            </h3>
                                            <p class="news-excerpt"><?php echo htmlspecialchars($article['excerpt']); ?></p>
                                            <div class="meta-info">
                                                <span
                                                    class="time"><?php echo time_elapsed_string($article['published_at']); ?></span>
                                                <span class="author">By
                                                    <?php echo htmlspecialchars($article['author_name']); ?></span>
                                                <span class="views"><i class="fas fa-eye"></i>
                                                    <?php echo number_format($article['view_count']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php endif; ?>
                        </div>

                        <!-- Pagination -->
                        <?php if ($total_pages > 1): ?>
                            <div class="pagination">
                                <?php if ($page > 1): ?>
                                    <a href="<?php echo getPaginationUrl(1, $sort); ?>" class="pagination-link first">
                                        <i class="fas fa-angle-double-left"></i> First
                                    </a>
                                    <a href="<?php echo getPaginationUrl($page - 1, $sort); ?>" class="pagination-link prev">
                                        <i class="fas fa-angle-left"></i> Prev
                                    </a>
                                <?php endif; ?>

                                <?php
                                $range = 2;
                                $startPage = max(1, $page - $range);
                                $endPage = min($total_pages, $page + $range);

                                if ($startPage > 1) {
                                    echo '<span class="pagination-ellipsis">...</span>';
                                }

                                for ($i = $startPage; $i <= $endPage; $i++) {
                                    $activeClass = $i == $page ? 'current' : '';
                                    echo '<a href="' . getPaginationUrl($i, $sort) . '" class="pagination-link ' . $activeClass . '">' . $i . '</a>';
                                }

                                if ($endPage < $total_pages) {
                                    echo '<span class="pagination-ellipsis">...</span>';
                                }
                                ?>

                                <?php if ($page < $total_pages): ?>
                                    <a href="<?php echo getPaginationUrl($page + 1, $sort); ?>" class="pagination-link next">
                                        Next <i class="fas fa-angle-right"></i>
                                    </a>
                                    <a href="<?php echo getPaginationUrl($total_pages, $sort); ?>" class="pagination-link last">
                                        Last <i class="fas fa-angle-double-right"></i>
                                    </a>
                                <?php endif; ?>
                            </div>
                        <?php endif; ?>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <aside class="sidebar">
                    <!-- Most Popular in Category -->
                    <div class="sidebar-section">
                        <div class="sidebar-header">Most Popular in <?php echo htmlspecialchars($category['name']); ?>
                        </div>
                        <div class="sidebar-content">
                            <?php while ($popular = $popular_articles->fetch_assoc()): ?>
                                <div class="popular-item">
                                    <div class="popular-item-image">
                                        <a href="article.php?slug=<?php echo htmlspecialchars($popular['slug']); ?>">
                                            <?php if ($popular['featured_image']): ?>
                                                <img src="<?php echo htmlspecialchars($popular['featured_image']); ?>"
                                                    alt="<?php echo htmlspecialchars($popular['title']); ?>">
                                            <?php else: ?>
                                                <img src="https://source.unsplash.com/random/100x100/?<?php echo htmlspecialchars($slug); ?>"
                                                    alt="Popular <?php echo htmlspecialchars($category['name']); ?> News">
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    <div class="popular-item-content">
                                        <h4>
                                            <a href="article.php?slug=<?php echo htmlspecialchars($popular['slug']); ?>">
                                                <?php echo htmlspecialchars($popular['title']); ?>
                                            </a>
                                        </h4>
                                        <span
                                            class="time"><?php echo time_elapsed_string($popular['published_at']); ?></span>
                                        <span class="views"><i class="fas fa-eye"></i>
                                            <?php echo number_format($popular['view_count']); ?></span>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <!-- Categories - Improved Version -->
                    <div class="sidebar-section">
                        <div class="sidebar-header">Browse Categories</div>
                        <div class="sidebar-content">
                            <ul class="category-links enhanced">
                                <?php
                                $categories = $conn->query($all_categories_query);
                                while ($cat = $categories->fetch_assoc()) {
                                    // Get category class for styling
                                    $catClass = getCategoryClass($cat['name']);

                                    // Count articles in this category
                                    $count_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM articles WHERE category_id = ? AND status = 'published'");
                                    $count_stmt->bind_param("i", $cat['id']);
                                    $count_stmt->execute();
                                    $count_result = $count_stmt->get_result();
                                    $article_count = $count_result->fetch_assoc()['total'];

                                    // Determine if this is the active category
                                    $active = $cat['slug'] === $slug ? ' active' : '';

                                    echo '<li class="' . $active . '">';
                                    echo '<a href="category.php?slug=' . htmlspecialchars($cat['slug']) . '">';
                                    echo '<span class="cat-color ' . $catClass . '"></span>';
                                    echo '<span class="cat-name">' . htmlspecialchars($cat['name']) . '</span>';
                                    echo '<span class="cat-count">' . $article_count . '</span>';
                                    echo '</a></li>';
                                }
                                ?>
                            </ul>
                        </div>
                    </div>

                    <!-- Newsletter -->
                    <div class="sidebar-section">
                        <div class="sidebar-header">Newsletter</div>
                        <div class="sidebar-content newsletter">
                            <p>Stay updated with our latest news directly in your inbox.</p>
                            <form>
                                <input type="email" placeholder="Your Email Address" required>
                                <button type="submit">Subscribe</button>
                            </form>
                        </div>
                    </div>

                    <!-- Advertisement -->
                    <div class="sidebar-section">
                        <div class="sidebar-header">Advertisement</div>
                        <div class="sidebar-content">
                            <?php if ($sidebar_ad): ?>
                                <a href="<?php echo htmlspecialchars($sidebar_ad['redirect_url']); ?>" target="_blank"
                                    class="sidebar-ad">
                                    <img src="<?php echo htmlspecialchars($sidebar_ad['image_path']); ?>"
                                        alt="<?php echo htmlspecialchars($sidebar_ad['name']); ?>">
                                </a>
                            <?php else: ?>
                                <div class="sidebar-ad">Advertisement</div>
                            <?php endif; ?>
                        </div>
                    </div>
                </aside>
            </div>
        </div>
    </main>

    <!-- Footer -->
    <footer>
        <div class="container">
            <div class="footer-content">
                <div class="footer-section about">
                    <h3>About Echoes of Today</h3>
                    <p>Delivering accurate, timely, and compelling news stories from around the globe. Our dedicated
                        team of journalists works tirelessly to keep you informed on matters that shape our world.</p>
                    <div class="contact">
                        <span><i class="fas fa-phone"></i> +1-555-123-4567</span>
                        <span><i class="fas fa-envelope"></i> info@echoesoftoday.com</span>
                    </div>
                </div>

                <div class="footer-section links">
                    <h3>Quick Links</h3>
                    <ul>
                        <li><a href="#">About Us</a></li>
                        <li><a href="#">Contact Us</a></li>
                        <li><a href="#">Privacy Policy</a></li>
                        <li><a href="#">Terms of Service</a></li>
                        <li><a href="#">Advertise With Us</a></li>
                        <li><a href="#">Careers</a></li>
                    </ul>
                </div>

                <div class="footer-section categories">
                    <h3>Categories</h3>
                    <ul>
                        <?php
                        // Reset categories query
                        $categories = $conn->query($all_categories_query);
                        while ($cat = $categories->fetch_assoc()):
                            ?>
                            <li><a href="category.php?slug=<?php echo htmlspecialchars($cat['slug']); ?>">
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </a></li>
                        <?php endwhile; ?>
                    </ul>
                </div>

                <div class="footer-section subscribe">
                    <h3>Stay Connected</h3>
                    <p>Follow us on social media for the latest updates</p>
                    <div class="social-links">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-youtube"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; <?php echo date("Y"); ?> Echoes of Today. All Rights Reserved.</p>
            </div>
        </div>
    </footer>

    <script src="js/script.js"></script>
</body>

</html>