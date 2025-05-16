<?php
// Include database connection
require_once '../includes/db_connection.php';

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

// Get secondary articles
$secondary_query = "SELECT a.*, c.name as category_name, c.slug as category_slug 
                    FROM articles a 
                    JOIN categories c ON a.category_id = c.id 
                    WHERE a.is_featured = 0 AND a.status = 'published' 
                    ORDER BY a.published_at DESC LIMIT 2";
$secondary_articles = $conn->query($secondary_query);

// Get popular articles
$popular_query = "SELECT a.*, c.name as category_name 
                  FROM articles a 
                  JOIN categories c ON a.category_id = c.id 
                  WHERE a.status = 'published' 
                  ORDER BY a.view_count DESC LIMIT 4";
$popular_articles = $conn->query($popular_query);

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
        href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@400;700;900&family=Source+Sans+Pro:wght@300;400;600;700&display=swap"
        rel="stylesheet">
</head>

<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="date-weather">
                <span class="current-date"><?php echo date("l, F j, Y"); ?></span>
                <span class="weather">76°F New York</span>
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
                <h1>Echoes of Today</h1>
                <p class="tagline">The Voice of Our Times</p>
            </div>
            <div class="ad-space">
                <?php if ($banner_ad): ?>
                    <a href="<?php echo htmlspecialchars($banner_ad['redirect_url']); ?>" target="_blank">
                        <img src="<?php echo htmlspecialchars($banner_ad['image_path']); ?>"
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
                <?php while ($category = $categories->fetch_assoc()): ?>
                    <li><a href="category.php?slug=<?php echo htmlspecialchars($category['slug']); ?>">
                            <?php echo htmlspecialchars($category['name']); ?>
                        </a></li>
                <?php endwhile; ?>
            </ul>
            <div class="search-box">
                <input type="text" placeholder="Search...">
                <button><i class="fas fa-search"></i></button>
            </div>
        </div>
    </nav>

    <!-- Breaking News -->
    <div class="breaking-news">
        <div class="container">
            <div class="breaking-title">Breaking News</div>
            <div class="breaking-content">
                <marquee behavior="scroll" direction="left" onmouseover="this.stop();" onmouseout="this.start();">
                    <?php if ($featured_article): ?>
                        <?php echo htmlspecialchars($featured_article['title']); ?> •
                    <?php endif; ?>
                    Global Summit on Climate Change Results in Historic Agreement •
                    Tech Leaders Unveil Next Generation AI • Major Sports Championship Final Set for Weekend
                </marquee>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main>
        <div class="container">
            <!-- Hero Section -->
            <section class="hero">
                <?php if ($featured_article): ?>
                    <div class="featured-article">
                        <div class="featured-image">
                            <?php if ($featured_article['featured_image']): ?>
                                <img src="<?php echo htmlspecialchars($featured_article['featured_image']); ?>"
                                    alt="<?php echo htmlspecialchars($featured_article['title']); ?>">
                            <?php else: ?>
                                <img src="https://source.unsplash.com/random/800x450/?news,<?php echo htmlspecialchars($featured_article['category_slug']); ?>"
                                    alt="Featured Article">
                            <?php endif; ?>
                        </div>
                        <div class="featured-content">
                            <span
                                class="category"><?php echo htmlspecialchars($featured_article['category_name']); ?></span>
                            <h2><?php echo htmlspecialchars($featured_article['title']); ?></h2>
                            <p class="excerpt"><?php echo htmlspecialchars($featured_article['excerpt']); ?></p>
                            <span class="time"><?php echo time_elapsed_string($featured_article['published_at']); ?></span>
                            <span class="author">By <?php echo htmlspecialchars($featured_article['author_name']); ?></span>
                        </div>
                    </div>
                <?php endif; ?>

                <div class="secondary-articles">
                    <?php while ($article = $secondary_articles->fetch_assoc()): ?>
                        <article>
                            <?php if ($article['featured_image']): ?>
                                <img src="<?php echo htmlspecialchars($article['featured_image']); ?>"
                                    alt="<?php echo htmlspecialchars($article['title']); ?>">
                            <?php else: ?>
                                <img src="https://source.unsplash.com/random/400x250/?<?php echo htmlspecialchars($article['category_slug']); ?>"
                                    alt="<?php echo htmlspecialchars($article['category_name']); ?> News">
                            <?php endif; ?>
                            <span class="category"><?php echo htmlspecialchars($article['category_name']); ?></span>
                            <h3><?php echo htmlspecialchars($article['title']); ?></h3>
                            <span class="time"><?php echo time_elapsed_string($article['published_at']); ?></span>
                        </article>
                    <?php endwhile; ?>
                </div>
            </section>

            <div class="content-wrapper">
                <!-- Main News Section -->
                <section class="main-news">
                    <?php
                    // Get up to 2 categories with their articles
                    $cat_articles_query = "SELECT c.id, c.name, c.slug FROM categories c 
                                          WHERE c.is_active = 1 
                                          ORDER BY c.display_order ASC LIMIT 2";
                    $category_results = $conn->query($cat_articles_query);

                    while ($category = $category_results->fetch_assoc()):
                        // Get articles for this category
                        $articles_query = "SELECT * FROM articles 
                                          WHERE category_id = {$category['id']} AND status = 'published' 
                                          ORDER BY published_at DESC LIMIT 3";
                        $articles_result = $conn->query($articles_query);
                        ?>
                        <div class="news-category">
                            <div class="category-header">
                                <h2><?php echo htmlspecialchars($category['name']); ?></h2>
                                <a href="category.php?slug=<?php echo htmlspecialchars($category['slug']); ?>"
                                    class="view-all">View All</a>
                            </div>
                            <div class="news-grid">
                                <?php while ($article = $articles_result->fetch_assoc()): ?>
                                    <article class="news-item">
                                        <?php if ($article['featured_image']): ?>
                                            <img src="<?php echo htmlspecialchars($article['featured_image']); ?>"
                                                alt="<?php echo htmlspecialchars($article['title']); ?>">
                                        <?php else: ?>
                                            <img src="https://source.unsplash.com/random/300x200/?<?php echo htmlspecialchars($category['slug']); ?>"
                                                alt="<?php echo htmlspecialchars($category['name']); ?> News">
                                        <?php endif; ?>
                                        <h3><?php echo htmlspecialchars($article['title']); ?></h3>
                                        <p><?php echo htmlspecialchars($article['excerpt']); ?></p>
                                        <span class="time"><?php echo time_elapsed_string($article['published_at']); ?></span>
                                    </article>
                                <?php endwhile; ?>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </section>

                <!-- Sidebar -->
                <aside class="sidebar">
                    <!-- Popular News -->
                    <div class="sidebar-section popular-news">
                        <h3>Popular News</h3>
                        <div class="popular-list">
                            <?php while ($article = $popular_articles->fetch_assoc()): ?>
                                <article class="popular-item">
                                    <?php if ($article['featured_image']): ?>
                                        <img src="<?php echo htmlspecialchars($article['featured_image']); ?>"
                                            alt="<?php echo htmlspecialchars($article['title']); ?>">
                                    <?php else: ?>
                                        <img src="https://source.unsplash.com/random/100x100/?trending" alt="Popular News">
                                    <?php endif; ?>
                                    <div>
                                        <h4><?php echo htmlspecialchars($article['title']); ?></h4>
                                        <span
                                            class="time"><?php echo time_elapsed_string($article['published_at']); ?></span>
                                    </div>
                                </article>
                            <?php endwhile; ?>
                        </div>
                    </div>

                    <!-- Newsletter -->
                    <div class="sidebar-section newsletter">
                        <h3>Subscribe to Our Newsletter</h3>
                        <p>Stay updated with our latest news directly in your inbox.</p>
                        <form>
                            <input type="email" placeholder="Your Email Address" required>
                            <button type="submit">Subscribe</button>
                        </form>
                    </div>

                    <!-- Advertisement -->
                    <div class="sidebar-section ad">
                        <?php if ($sidebar_ad): ?>
                            <a href="<?php echo htmlspecialchars($sidebar_ad['redirect_url']); ?>" target="_blank">
                                <img src="<?php echo htmlspecialchars($sidebar_ad['image_path']); ?>"
                                    alt="<?php echo htmlspecialchars($sidebar_ad['name']); ?>"
                                    width="<?php echo $sidebar_ad['width']; ?>"
                                    height="<?php echo $sidebar_ad['height']; ?>">
                            </a>
                        <?php else: ?>
                            <div class="sidebar-ad">Advertisement</div>
                        <?php endif; ?>
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