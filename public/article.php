<?php
// filepath: c:\xampp\htdocs\Echoes-of-today-Newspaper\public\article.php
// Include database connection
require_once '../includes/db_connection.php';
// Include weather function
require_once '../includes/weather.php';

// Get weather data for Tirana
$weather = getWeather('Tirana', 'AL');

// Get article slug from URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($slug)) {
    // Redirect to homepage if no slug provided
    header('Location: index.php');
    exit();
}

// Get categories for navigation
$cat_query = "SELECT id, name, slug FROM categories WHERE is_active = 1 ORDER BY display_order ASC";
$categories = $conn->query($cat_query);

// Get article details
$article_query = "SELECT a.*, c.name as category_name, c.slug as category_slug, 
                 CONCAT(u.first_name, ' ', u.last_name) as author_name,
                 u.profile_image as author_image, u.bio as author_bio 
                 FROM articles a 
                 JOIN categories c ON a.category_id = c.id 
                 JOIN users u ON a.author_id = u.id 
                 WHERE a.slug = ? AND a.status = 'published'";

$stmt = $conn->prepare($article_query);
$stmt->bind_param("s", $slug);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    // Article not found or not published
    header('Location: index.php');
    exit();
}

$article = $result->fetch_assoc();

// Update view count
$update_views = "UPDATE articles SET view_count = view_count + 1 WHERE id = ?";
$stmt = $conn->prepare($update_views);
$stmt->bind_param("i", $article['id']);
$stmt->execute();

// Get article tags
$tags_query = "SELECT t.name, t.id FROM tags t
               JOIN article_tags at ON t.id = at.tag_id
               WHERE at.article_id = ?";
$stmt = $conn->prepare($tags_query);
$stmt->bind_param("i", $article['id']);
$stmt->execute();
$tags_result = $stmt->get_result();

// Get related articles from the same category
$related_query = "SELECT a.id, a.title, a.slug, a.excerpt, a.featured_image, a.published_at,
                 CONCAT(u.first_name, ' ', u.last_name) as author_name 
                 FROM articles a 
                 JOIN users u ON a.author_id = u.id 
                 WHERE a.category_id = ? AND a.id != ? AND a.status = 'published'
                 ORDER BY a.published_at DESC LIMIT 3";
$stmt = $conn->prepare($related_query);
$stmt->bind_param("ii", $article['category_id'], $article['id']);
$stmt->execute();
$related_articles = $stmt->get_result();

// Get popular articles
$popular_query = "SELECT a.*, c.name as category_name, c.slug as category_slug 
                 FROM articles a 
                 JOIN categories c ON a.category_id = c.id 
                 WHERE a.status = 'published' 
                 ORDER BY a.view_count DESC LIMIT 5";
$popular_articles = $conn->query($popular_query);

// Get sidebar advertisement
$sidebar_ad_query = "SELECT * FROM advertisements 
                   WHERE ad_type = 'sidebar' AND is_active = 1 
                   AND NOW() BETWEEN start_date AND end_date 
                   ORDER BY RAND() LIMIT 1";
$sidebar_ad_result = $conn->query($sidebar_ad_query);
$sidebar_ad = $sidebar_ad_result->fetch_assoc();

// Get in-article advertisement
$article_ad_query = "SELECT * FROM advertisements 
                   WHERE ad_type = 'in-article' AND is_active = 1 
                   AND NOW() BETWEEN start_date AND end_date 
                   ORDER BY RAND() LIMIT 1";
$article_ad_result = $conn->query($article_ad_query);
$article_ad = $article_ad_result->fetch_assoc();

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

// Format article content with paragraphs
function formatArticleContent($content)
{
    // Split content by double newlines to create paragraphs
    $paragraphs = explode("\n\n", $content);
    $formatted = '';

    foreach ($paragraphs as $paragraph) {
        if (trim($paragraph) !== '') {
            $formatted .= '<p>' . htmlspecialchars($paragraph) . '</p>';
        }
    }

    return $formatted;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($article['title']); ?> - Echoes of Today</title>
    <meta name="description" content="<?php echo htmlspecialchars($article['excerpt']); ?>">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="css/article.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400;700&family=Open+Sans:wght@400;600;700&display=swap"
        rel="stylesheet">
    <!-- Open Graph tags for social sharing -->
    <meta property="og:title" content="<?php echo htmlspecialchars($article['title']); ?>">
    <meta property="og:description" content="<?php echo htmlspecialchars($article['excerpt']); ?>">
    <?php if ($article['featured_image']): ?>
        <meta property="og:image" content="<?php echo htmlspecialchars($article['featured_image']); ?>">
    <?php endif; ?>
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
                $categories = $conn->query($cat_query);
                $count = 0;
                $dropdown_items = array();

                // First collect all categories
                while ($category = $categories->fetch_assoc()) {
                    $is_active = ($category['slug'] == $article['category_slug']) ? 'class="active"' : '';

                    if ($count < 6) {
                        // First 6 categories go directly in the nav
                        ?>
                        <li <?php echo $is_active; ?>><a
                                href="category.php?slug=<?php echo htmlspecialchars($category['slug']); ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </a></li>
                        <?php
                    } else {
                        // Store the rest for the dropdown
                        $dropdown_items[] = [
                            'category' => $category,
                            'is_active' => $is_active
                        ];
                    }
                    $count++;
                }

                // If we have extra categories, add the dropdown
                if (!empty($dropdown_items)) {
                    ?>
                    <li class="dropdown">
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

    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <div class="container">
            <a href="index.php">Home</a> &raquo;
            <a
                href="category.php?slug=<?php echo htmlspecialchars($article['category_slug']); ?>"><?php echo htmlspecialchars($article['category_name']); ?></a>
            &raquo;
            <span><?php echo htmlspecialchars($article['title']); ?></span>
        </div>
    </div>

    <!-- Main Content -->
    <main>
        <div class="container">
            <div class="two-column-layout article-layout">
                <div class="main-content">
                    <article class="article-detail">
                        <header class="article-header">
                            <span class="category-label <?php echo getCategoryClass($article['category_name']); ?>">
                                <?php echo htmlspecialchars($article['category_name']); ?>
                            </span>
                            <h1 class="article-title"><?php echo htmlspecialchars($article['title']); ?></h1>
                            <div class="article-meta">
                                <div class="author-info">
                                    <?php if (!empty($article['author_image'])): ?>
                                        <img src="<?php echo htmlspecialchars($article['author_image']); ?>"
                                            alt="<?php echo htmlspecialchars($article['author_name']); ?>"
                                            class="author-image">
                                    <?php else: ?>
                                        <i class="fas fa-user-circle author-icon"></i>
                                    <?php endif; ?>
                                    <span class="author-name">By
                                        <?php echo htmlspecialchars($article['author_name']); ?></span>
                                </div>
                                <div class="article-date">
                                    <i class="far fa-calendar-alt"></i>
                                    <time datetime="<?php echo date('Y-m-d', strtotime($article['published_at'])); ?>">
                                        <?php echo date('F j, Y', strtotime($article['published_at'])); ?>
                                    </time>
                                </div>
                                <div class="article-views">
                                    <i class="far fa-eye"></i>
                                    <span><?php echo number_format($article['view_count']); ?> views</span>
                                </div>
                            </div>
                        </header>

                        <?php if ($article['featured_image']): ?>
                            <div class="article-featured-image">
                                <img src="<?php echo htmlspecialchars($article['featured_image']); ?>"
                                    alt="<?php echo htmlspecialchars($article['title']); ?>">
                                <div class="image-caption">Featured image:
                                    <?php echo htmlspecialchars($article['title']); ?>
                                </div>
                            </div>
                        <?php else: ?>
                            <div class="article-featured-image">
                                <img src="https://source.unsplash.com/random/1200x600/?<?php echo htmlspecialchars($article['category_slug']); ?>"
                                    alt="<?php echo htmlspecialchars($article['title']); ?>">
                                <div class="image-caption">Image: <?php echo htmlspecialchars($article['category_name']); ?>
                                    News</div>
                            </div>
                        <?php endif; ?>

                        <!-- Article excerpt/summary -->
                        <div class="article-excerpt">
                            <?php echo htmlspecialchars($article['excerpt']); ?>
                        </div>

                        <!-- Share buttons -->
                        <div class="share-buttons">
                            <span class="share-text">Share this article:</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>"
                                target="_blank" class="share-btn facebook">
                                <i class="fab fa-facebook-f"></i>
                            </a>
                            <a href="https://twitter.com/intent/tweet?url=<?php echo urlencode('https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>&text=<?php echo urlencode($article['title']); ?>"
                                target="_blank" class="share-btn twitter">
                                <i class="fab fa-twitter"></i>
                            </a>
                            <a href="https://wa.me/?text=<?php echo urlencode($article['title'] . ' - https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>"
                                target="_blank" class="share-btn whatsapp">
                                <i class="fab fa-whatsapp"></i>
                            </a>
                            <a href="mailto:?subject=<?php echo urlencode($article['title']); ?>&body=<?php echo urlencode('Check out this article: https://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI']); ?>"
                                class="share-btn email">
                                <i class="far fa-envelope"></i>
                            </a>
                        </div>

                        <!-- Article content -->
                        <div class="article-content">
                            <?php echo formatArticleContent($article['content']); ?>

                            <!-- In-article advertisement -->
                            <?php if ($article_ad): ?>
                                <div class="in-article-ad">
                                    <div class="ad-label">Advertisement</div>
                                    <a href="<?php echo htmlspecialchars($article_ad['redirect_url']); ?>" target="_blank">
                                        <img src="<?php echo htmlspecialchars($article_ad['image_path']); ?>"
                                            alt="<?php echo htmlspecialchars($article_ad['name']); ?>">
                                    </a>
                                </div>
                            <?php endif; ?>
                        </div>

                        <!-- Tags -->
                        <?php if ($tags_result->num_rows > 0): ?>
                            <div class="article-tags">
                                <span class="tags-label">Tags:</span>
                                <?php while ($tag = $tags_result->fetch_assoc()): ?>
                                    <a href="tag.php?id=<?php echo $tag['id']; ?>"
                                        class="tag"><?php echo htmlspecialchars($tag['name']); ?></a>
                                <?php endwhile; ?>
                            </div>
                        <?php endif; ?>

                        <!-- Author bio -->
                        <div class="author-bio">
                            <div class="author-image">
                                <?php if (!empty($article['author_image'])): ?>
                                    <img src="<?php echo htmlspecialchars($article['author_image']); ?>"
                                        alt="<?php echo htmlspecialchars($article['author_name']); ?>">
                                <?php else: ?>
                                    <i class="fas fa-user-circle"></i>
                                <?php endif; ?>
                            </div>
                            <div class="author-details">
                                <h3><?php echo htmlspecialchars($article['author_name']); ?></h3>
                                <p><?php echo !empty($article['author_bio']) ? htmlspecialchars($article['author_bio']) : 'Staff writer at Echoes of Today.'; ?>
                                </p>
                            </div>
                        </div>
                    </article>

                    <!-- Related articles -->
                    <?php if ($related_articles->num_rows > 0): ?>
                        <div class="related-articles">
                            <h2>Related Articles</h2>
                            <div class="news-grid">
                                <?php while ($related = $related_articles->fetch_assoc()): ?>
                                    <div class="news-item">
                                        <div class="news-item-image">
                                            <?php if (!empty($related['featured_image'])): ?>
                                                <img src="<?php echo htmlspecialchars($related['featured_image']); ?>"
                                                    alt="<?php echo htmlspecialchars($related['title']); ?>">
                                            <?php else: ?>
                                                <img src="https://source.unsplash.com/random/300x200/?<?php echo htmlspecialchars($article['category_slug']); ?>"
                                                    alt="Related article">
                                            <?php endif; ?>
                                        </div>
                                        <div class="news-item-content">
                                            <h3>
                                                <a href="article.php?slug=<?php echo htmlspecialchars($related['slug']); ?>">
                                                    <?php echo htmlspecialchars($related['title']); ?>
                                                </a>
                                            </h3>
                                            <div class="meta-info">
                                                <span
                                                    class="time"><?php echo time_elapsed_string($related['published_at']); ?></span>
                                                <span
                                                    class="author"><?php echo htmlspecialchars($related['author_name']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <aside class="sidebar">
                    <!-- Popular News -->
                    <div class="sidebar-section">
                        <div class="sidebar-header">Most Read</div>
                        <div class="sidebar-content">
                            <?php while ($popular = $popular_articles->fetch_assoc()): ?>
                                <div class="popular-item">
                                    <div class="popular-item-image">
                                        <?php if ($popular['featured_image']): ?>
                                            <img src="<?php echo htmlspecialchars($popular['featured_image']); ?>"
                                                alt="<?php echo htmlspecialchars($popular['title']); ?>">
                                        <?php else: ?>
                                            <img src="https://source.unsplash.com/random/100x100/?trending" alt="Popular News">
                                        <?php endif; ?>
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

                    <!-- Latest from category -->
                    <div class="sidebar-section">
                        <div class="sidebar-header">More in <?php echo htmlspecialchars($article['category_name']); ?>
                        </div>
                        <div class="sidebar-content">
                            <ul class="category-links">
                                <?php
                                $cat_latest_query = "SELECT id, title, slug, published_at FROM articles 
                                                    WHERE category_id = {$article['category_id']} AND id != {$article['id']} AND status = 'published'
                                                    ORDER BY published_at DESC LIMIT 5";
                                $cat_latest = $conn->query($cat_latest_query);
                                while ($cat_item = $cat_latest->fetch_assoc()):
                                    ?>
                                    <li>
                                        <a href="article.php?slug=<?php echo htmlspecialchars($cat_item['slug']); ?>">
                                            <?php echo htmlspecialchars($cat_item['title']); ?>
                                        </a>
                                        <span
                                            class="time"><?php echo time_elapsed_string($cat_item['published_at']); ?></span>
                                    </li>
                                <?php endwhile; ?>
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
                        $categories = $conn->query($cat_query);
                        while ($category = $categories->fetch_assoc()):
                            ?>
                            <li><a href="category.php?slug=<?php echo htmlspecialchars($category['slug']); ?>">
                                    <?php echo htmlspecialchars($category['name']); ?>
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