<?php
// Include database connection
require_once '../includes/db_connection.php';
// Include weather function
require_once '../includes/weather.php';

// Get weather data for Tirana
$weather = getWeather('Tirana', 'AL');

// Get categories for navigation
$cat_query = "SELECT id, name, slug FROM categories WHERE is_active = 1 ORDER BY display_order ASC";
$categories = $conn->query($cat_query);

// Get featured article
$featured_query = "SELECT a.*, c.name as category_name, c.slug as category_slug, 
                  CONCAT(u.first_name, ' ', u.last_name) as author_name 
                  FROM articles a 
                  JOIN categories c ON a.category_id = c.id 
                  JOIN users u ON a.author_id = u.id 
                  WHERE a.is_featured = 1 AND a.status = 'published' 
                  ORDER BY a.published_at DESC LIMIT 1";
$featured_result = $conn->query($featured_query);
$featured_article = $featured_result->fetch_assoc();

// Get secondary featured articles
$secondary_query = "SELECT a.*, c.name as category_name, c.slug as category_slug, 
                   CONCAT(u.first_name, ' ', u.last_name) as author_name 
                   FROM articles a 
                   JOIN categories c ON a.category_id = c.id 
                   JOIN users u ON a.author_id = u.id 
                   WHERE a.is_featured = 0 AND a.status = 'published' 
                   ORDER BY a.published_at DESC LIMIT 5";
$secondary_articles = $conn->query($secondary_query);

// Get popular articles
$popular_query = "SELECT a.*, c.name as category_name, c.slug as category_slug 
                  FROM articles a 
                  JOIN categories c ON a.category_id = c.id 
                  WHERE a.status = 'published' 
                  ORDER BY a.view_count DESC LIMIT 5";
$popular_articles = $conn->query($popular_query);

// Get articles by category
$cat_articles = [];
$categories_list_query = "SELECT id, name, slug FROM categories WHERE is_active = 1 ORDER BY display_order ASC LIMIT 6";
$categories_list = $conn->query($categories_list_query);

while ($category = $categories_list->fetch_assoc()) {
    $cat_articles_query = "SELECT a.*, CONCAT(u.first_name, ' ', u.last_name) as author_name 
                          FROM articles a 
                          JOIN users u ON a.author_id = u.id 
                          WHERE a.category_id = {$category['id']} AND a.status = 'published' 
                          ORDER BY a.published_at DESC LIMIT 4";
    $cat_articles[$category['slug']] = [
        'info' => $category,  // This stores the category information including 'slug'
        'articles' => $conn->query($cat_articles_query)
    ];
}

// Get banner advertisement
$ad_query = "SELECT * FROM advertisements 
             WHERE ad_type = 'banner' AND is_active = 1 
             AND NOW() BETWEEN start_date AND end_date 
             ORDER BY RAND() LIMIT 1";
$ad_result = $conn->query($ad_query);
$banner_ad = $ad_result->fetch_assoc();

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

// Get breaking news - articles published within last 24 hours
$breaking_news_query = "SELECT a.title, a.slug, c.name as category_name 
                       FROM articles a 
                       JOIN categories c ON a.category_id = c.id 
                       WHERE a.status = 'published' 
                       AND a.published_at >= DATE_SUB(NOW(), INTERVAL 24 HOUR) 
                       ORDER BY a.published_at DESC 
                       LIMIT 5";
$breaking_news = $conn->query($breaking_news_query);

// Replace the existing getImagePath function with this one:
function getImagePath($imagePath, $default = 'https://source.unsplash.com/random/400x250/?news')
{
    // If no image path is provided, return the default placeholder
    if (empty($imagePath)) {
        return $default;
    }

    // Extract the filename safely
    $filename = basename($imagePath);

    // Full path on the server (outside public folder)
    $local_path = __DIR__ . '/../uploads/' . $filename;

    // If the file exists in the uploads directory, return image.php URL
    if (file_exists($local_path)) {
        return 'image.php?file=' . urlencode($filename);
    }

    // Fallback to default
    return $default;
}

?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Echoes of Today - Latest News & Updates</title>
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
                <h1>ECHOES OF TODAY</h1>
                <p class="tagline">The Voice of Our Times</p>
            </div>
            <div class="header-ad"></div>
            <div class="ad-space">
                <?php if ($banner_ad): ?>
                    <a href="ad_click.php?ad_id=<?php echo $banner_ad['id']; ?>" target="_blank"
                        data-ad-id="<?php echo $banner_ad['id']; ?>">
                        <?php
                        $img = getImagePath($banner_ad['image_path'], 'https://source.unsplash.com/random/728x90/?advertisement');
                        ?>
                        <img src="<?php echo htmlspecialchars($img); ?>"
                            alt="<?php echo htmlspecialchars($banner_ad['name']); ?>"
                            width="<?php echo $banner_ad['width']; ?>" height="<?php echo $banner_ad['height']; ?>">
                    </a>
                <?php else: ?>
                    <div class="ad-placeholder">Advertisement</div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav>
        <div class="container">
            <ul class="main-nav">
                <li class="active"><a href="index.php">Home</a></li>
                <?php
                // Reset categories query
                $categories = $conn->query($cat_query);
                $count = 0;
                $dropdown_items = array();

                // First collect all categories
                while ($category = $categories->fetch_assoc()) {
                    if ($count < 6) {
                        // First 6 categories go directly in the nav
                        ?>
                        <li><a href="category.php?slug=<?php echo htmlspecialchars($category['slug']); ?>">
                                <?php echo htmlspecialchars($category['name']); ?>
                            </a></li>
                        <?php
                    } else {
                        // Store the rest for the dropdown
                        $dropdown_items[] = $category;
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
                                <li><a href="category.php?slug=<?php echo htmlspecialchars($item['slug']); ?>">
                                        <?php echo htmlspecialchars($item['name']); ?>
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

    <!-- Breaking News -->
    <div class="breaking-news">
        <div class="container">
            <div class="breaking-title">Breaking News</div>
            <div class="breaking-content">
                <marquee behavior="scroll" direction="left" onmouseover="this.stop();" onmouseout="this.start();">
                    <?php
                    // If we have breaking news items from the last 24 hours
                    if ($breaking_news->num_rows > 0) {
                        $news_items = [];
                        while ($news_item = $breaking_news->fetch_assoc()) {
                            $news_items[] = '<a href="article.php?slug=' . htmlspecialchars($news_item['slug']) . '">' .
                                htmlspecialchars($news_item['title']) . '</a> <span class="breaking-category">' .
                                htmlspecialchars($news_item['category_name']) . '</span>';
                        }
                        echo implode(' <span class="breaking-news-separator">•</span> ', $news_items);
                    } else if ($featured_article) {
                        // Fallback to featured article if no breaking news
                        echo '<a href="article.php?slug=' . htmlspecialchars($featured_article['slug']) . '">' .
                            htmlspecialchars($featured_article['title']) . '</a>' .
                            ' <span class="breaking-category">' . htmlspecialchars($featured_article['category_name']) . '</span>';
                    } else {
                        // Final fallback
                        echo 'Welcome to Echoes of Today - Your Source for the Latest News and Updates';
                    }
                    ?>
                </marquee>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main>
        <div class="container">
            <!-- Front Page Grid -->
            <section class="front-page-grid">
                <!-- Left Side: Sliding Featured Stories -->
                <div class="left-content">
                    <?php
                    // Get multiple featured articles for slider
                    $slider_query = "SELECT a.*, c.name as category_name, c.slug as category_slug, 
                                   CONCAT(u.first_name, ' ', u.last_name) as author_name 
                                   FROM articles a 
                                   JOIN categories c ON a.category_id = c.id 
                                   JOIN users u ON a.author_id = u.id 
                                   WHERE a.status = 'published' 
                                   ORDER BY a.published_at DESC LIMIT 5";
                    $slider_articles = $conn->query($slider_query);

                    if ($slider_articles && $slider_articles->num_rows > 0):
                        ?>
                        <div class="main-story-featured">
                            <div class="featured-slider">
                                <div class="slider-wrapper" id="sliderWrapper">
                                    <?php
                                    $slide_index = 0;
                                    while ($article = $slider_articles->fetch_assoc()):
                                        ?>
                                        <div class="slide">
                                            <a href="article.php?slug=<?php echo htmlspecialchars($article['slug']); ?>"
                                                class="slide__link">
                                                <div class="slide__image-wrapper">
                                                    <?php
                                                    $img_src = 'https://source.unsplash.com/random/800x500/?news,' . htmlspecialchars($article['category_slug']);
                                                    if (!empty($article['featured_image'])) {
                                                        $image_path = getImagePath($article['featured_image'], '');
                                                        if (!empty($image_path)) {
                                                            $img_src = $image_path;
                                                        }
                                                    }
                                                    ?>
                                                    <img src="<?php echo htmlspecialchars($img_src); ?>"
                                                        alt="<?php echo htmlspecialchars($article['title']); ?>"
                                                        class="slide__image">
                                                </div>
                                                <div class="slide__content-overlay">
                                                    <span
                                                        class="slide__category <?php echo getCategoryClass($article['category_name']); ?>">
                                                        <?php echo htmlspecialchars($article['category_name']); ?>
                                                    </span>
                                                    <h2 class="slide__title">
                                                        <?php echo htmlspecialchars($article['title']); ?>
                                                    </h2>
                                                    <?php if (!empty($article['excerpt'])): ?>
                                                        <p class="slide__excerpt">
                                                            <?php echo htmlspecialchars($article['excerpt']); ?>
                                                        </p>
                                                    <?php endif; ?>
                                                    <div class="slide__meta">
                                                        <span class="slide__meta-item">
                                                            <i class="fas fa-clock"></i>
                                                            <?php echo time_elapsed_string($article['published_at']); ?>
                                                        </span>
                                                        <span class="slide__meta-item">
                                                            <i class="fas fa-user"></i>
                                                            <?php echo htmlspecialchars($article['author_name']); ?>
                                                        </span>
                                                    </div>
                                                </div>
                                            </a>
                                        </div>
                                        <?php
                                        $slide_index++;
                                    endwhile;
                                    ?>
                                </div>

                                <!-- Navigation Arrows -->
                                <button class="slider-arrow prev" onclick="previousSlide()">
                                    <i class="fas fa-chevron-left"></i>
                                </button>
                                <button class="slider-arrow next" onclick="nextSlide()">
                                    <i class="fas fa-chevron-right"></i>
                                </button>

                                <!-- Navigation Dots -->
                                <div class="slider-nav">
                                    <?php
                                    mysqli_data_seek($slider_articles, 0);
                                    $dot_index = 0;
                                    while ($slider_articles->fetch_assoc()):
                                        ?>
                                        <span class="nav-dot <?php echo $dot_index === 0 ? 'active' : ''; ?>"
                                            onclick="goToSlide(<?php echo $dot_index; ?>)"></span>
                                        <?php
                                        $dot_index++;
                                    endwhile;
                                    ?>
                                </div>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Right Side: Stacked Stories -->
                <?php
                // Get articles for right side (excluding the ones used in slider)
                $right_stories_query = "SELECT a.*, c.name as category_name, c.slug as category_slug, 
                                       CONCAT(u.first_name, ' ', u.last_name) as author_name 
                                       FROM articles a 
                                       JOIN categories c ON a.category_id = c.id 
                                       JOIN users u ON a.author_id = u.id 
                                       WHERE a.status = 'published' 
                                       ORDER BY a.published_at DESC LIMIT 4 OFFSET 5";
                $right_stories = $conn->query($right_stories_query);

                if ($right_stories && $right_stories->num_rows > 0):
                    ?>
                    <div class="top-stories-right">
                        <?php while ($article = $right_stories->fetch_assoc()): ?>
                            <article class="top-story-item">
                                <a href="article.php?slug=<?php echo htmlspecialchars($article['slug']); ?>"
                                    class="top-story-item__link">
                                    <div class="top-story-item__image-wrapper">
                                        <?php
                                        $img_src = 'https://source.unsplash.com/random/350x220/?' . htmlspecialchars($article['category_slug']);
                                        if (!empty($article['featured_image'])) {
                                            $image_path_check = getImagePath($article['featured_image'], '');
                                            if (!empty($image_path_check)) {
                                                $img_src = $image_path_check;
                                            }
                                        }
                                        ?>
                                        <img src="<?php echo htmlspecialchars($img_src); ?>"
                                            alt="<?php echo htmlspecialchars($article['title']); ?>"
                                            class="top-story-item__image">
                                    </div>
                                    <div class="top-story-item__content-overlay">
                                        <h3 class="top-story-item__title">
                                            <?php echo htmlspecialchars($article['title']); ?>
                                        </h3>
                                        <div class="top-story-item__meta">
                                            <span
                                                class="top-story-item__category <?php echo getCategoryClass($article['category_name']); ?>">
                                                <?php echo htmlspecialchars($article['category_name']); ?>
                                            </span>
                                            <span class="top-story-item__time">
                                                <?php echo time_elapsed_string($article['published_at']); ?>
                                            </span>
                                        </div>
                                    </div>
                                </a>
                            </article>
                        <?php endwhile; ?>
                    </div>
                <?php endif; ?>
            </section>

            <div class="two-column-layout">
                <div class="main-content">
                    <!-- Category News Sections -->
                    <?php foreach ($cat_articles as $slug => $category_data): ?>
                        <section class="news-section">
                            <div class="section-header">
                                <h2><?php echo htmlspecialchars($category_data['info']['name']); ?></h2>
                                <a href="category.php?slug=<?php echo htmlspecialchars($category_data['info']['slug']); ?>"
                                    class="view-all">
                                    View All <i class="fas fa-arrow-right"></i>
                                </a>
                            </div>
                            <div class="news-grid">
                                <?php while ($article = $category_data['articles']->fetch_assoc()): ?>
                                    <div class="news-item">
                                        <div class="news-item-image">
                                            <a href="article.php?slug=<?php echo htmlspecialchars($article['slug']); ?>">
                                                <?php if ($article['featured_image']): ?>
                                                    <img src="<?php
                                                    $img = getImagePath($article['featured_image']);
                                                    echo htmlspecialchars($img);
                                                    ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                                                <?php else: ?>
                                                    <img src="https://source.unsplash.com/random/300x200/?<?php echo htmlspecialchars($category_data['info']['slug']); ?>"
                                                        alt="<?php echo htmlspecialchars($category_data['info']['name']); ?> News">
                                                <?php endif; ?>
                                            </a>
                                        </div>
                                        <div class="news-item-content">
                                            <h3>
                                                <a href="article.php?slug=<?php echo htmlspecialchars($article['slug']); ?>">
                                                    <?php echo htmlspecialchars($article['title']); ?>
                                                </a>
                                            </h3>
                                            <div class="meta-info">
                                                <span
                                                    class="time"><?php echo time_elapsed_string($article['published_at']); ?></span>
                                                <span
                                                    class="author"><?php echo htmlspecialchars($article['author_name']); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            </div>
                        </section>
                    <?php endforeach; ?>
                </div>

                <!-- Sidebar -->
                <aside class="sidebar">
                    <!-- Popular News -->
                    <div class="sidebar-section">
                        <div class="sidebar-header">Most Read</div>
                        <div class="sidebar-content">
                            <?php while ($article = $popular_articles->fetch_assoc()): ?>
                                <div class="popular-item">
                                    <div class="popular-item-image">
                                        <a href="article.php?slug=<?php echo htmlspecialchars($article['slug']); ?>">
                                            <?php if ($article['featured_image']): ?>
                                                <img src="<?php
                                                $img = getImagePath($article['featured_image'], 'https://source.unsplash.com/random/100x100/?trending');
                                                echo htmlspecialchars($img);
                                                ?>" alt="<?php echo htmlspecialchars($article['title']); ?>">
                                            <?php else: ?>
                                                <img src="https://source.unsplash.com/random/100x100/?trending"
                                                    alt="Popular News">
                                            <?php endif; ?>
                                        </a>
                                    </div>
                                    <div class="popular-item-content">
                                        <h4>
                                            <a href="article.php?slug=<?php echo htmlspecialchars($article['slug']); ?>">
                                                <?php echo htmlspecialchars($article['title']); ?>
                                            </a>
                                        </h4>
                                        <span
                                            class="time"><?php echo time_elapsed_string($article['published_at']); ?></span>
                                        <span class="views"><i class="fas fa-eye"></i>
                                            <?php echo number_format($article['view_count']); ?></span>
                                    </div>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <!-- Newsletter -->
                    <div class="sidebar-section">
                        <div class="sidebar-header">Newsletter</div>
                        <div class="sidebar-content newsletter">
                            <p>Stay updated with our latest news directly in your inbox.</p>
                            <form id="newsletterForm">
                                <input type="email" id="newsletterEmail" placeholder="Your Email Address" required>
                                <button type="submit">Subscribe</button>
                            </form>
                            <div id="newsletterMessage" class="newsletter-message" style="display: none;"></div>
                        </div>
                    </div>

                    <!-- Advertisement -->
                    <div class="sidebar-section">
                        <div class="sidebar-header">Advertisement</div>
                        <div class="sidebar-content">
                            <?php if ($sidebar_ad): ?>
                                <a href="ad_click.php?ad_id=<?php echo $sidebar_ad['id']; ?>" target="_blank"
                                    class="sidebar-ad" data-ad-id="<?php echo $sidebar_ad['id']; ?>">
                                    <?php
                                    $img = getImagePath($sidebar_ad['image_path'], 'https://source.unsplash.com/random/300x250/?advertisement');
                                    ?>
                                    <img src="<?php echo htmlspecialchars($img); ?>"
                                        alt="<?php echo htmlspecialchars($sidebar_ad['name']); ?>"
                                        width="<?php echo $sidebar_ad['width']; ?>"
                                        height="<?php echo $sidebar_ad['height']; ?>">
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
                <p>&copy;
                    <?php echo date("Y"); ?> Echoes of Today. All Rights Reserved.
                </p>
            </div>
        </div>
    </footer>

    <script src="js/script.js"></script>
    <script src="js/slider.js"></script>
    <script src="js/newsletter.js"></script>
</body>

</html>

<?php
// Close database connection
$conn->close();
?>