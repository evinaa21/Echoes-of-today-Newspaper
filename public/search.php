<?php
// Include database connection
require_once '../includes/db_connection.php';
// Include weather function
require_once '../includes/weather.php';

// Get weather data for Tirana
$weather = getWeather('Tirana', 'AL');

// Get search query from URL
$query = isset($_GET['q']) ? trim($_GET['q']) : '';

// Redirect if empty query
if (empty($query)) {
    header('Location: index.php');
    exit();
}

// Get categories for navigation
$cat_query = "SELECT id, name, slug FROM categories WHERE is_active = 1 ORDER BY display_order ASC";
$categories = $conn->query($cat_query);

// Pagination settings
$articles_per_page = 10;
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

// Search query to find in title, excerpt, or content
$search_term = '%' . $conn->real_escape_string($query) . '%';

// Count total matching articles
$count_query = "SELECT COUNT(*) AS total 
                FROM articles a 
                WHERE a.status = 'published' 
                AND (a.title LIKE ? OR a.excerpt LIKE ? OR a.content LIKE ?)";
$stmt = $conn->prepare($count_query);
$stmt->bind_param("sss", $search_term, $search_term, $search_term);
$stmt->execute();
$count_result = $stmt->get_result();
$total_articles = $count_result->fetch_assoc()['total'];

// Calculate total pages
$total_pages = ceil($total_articles / $articles_per_page);
$page = min($page, max(1, $total_pages)); // Ensure page doesn't exceed total pages

// Get search results
$search_query = "SELECT a.*, c.name as category_name, c.slug as category_slug, 
                CONCAT(u.first_name, ' ', u.last_name) as author_name
                FROM articles a 
                JOIN categories c ON a.category_id = c.id 
                JOIN users u ON a.author_id = u.id 
                WHERE a.status = 'published' 
                AND (a.title LIKE ? OR a.excerpt LIKE ? OR a.content LIKE ?)
                ORDER BY $order_by 
                LIMIT ? OFFSET ?";
$stmt = $conn->prepare($search_query);
$stmt->bind_param("sssii", $search_term, $search_term, $search_term, $articles_per_page, $offset);
$stmt->execute();
$search_results = $stmt->get_result();

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
    global $query;
    return "search.php?q=" . urlencode($query) . "&sort=" . urlencode($sort) . "&page=" . $page;
}

// Function to generate sort URL
function getSortUrl($sort)
{
    global $query, $page;
    return "search.php?q=" . urlencode($query) . "&sort=" . urlencode($sort) . "&page=" . $page;
}

// Function to highlight search terms in text
function highlightSearchTerm($text, $query)
{
    $terms = explode(' ', $query);
    $text = htmlspecialchars($text);

    foreach ($terms as $term) {
        if (strlen($term) > 2) { // Only highlight terms longer than 2 characters
            $term = preg_quote($term, '/');
            $text = preg_replace("/($term)/i", "<mark>$1</mark>", $text);
        }
    }

    return $text;
}
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search: <?php echo htmlspecialchars($query); ?> - Echoes of Today</title>
    <meta name="description"
        content="Search results for '<?php echo htmlspecialchars($query); ?>' from Echoes of Today">
    <link rel="stylesheet" href="css/style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link
        href="https://fonts.googleapis.com/css2?family=Roboto+Condensed:wght@400;700&family=Open+Sans:wght@400;600;700&display=swap"
        rel="stylesheet">
    <style>
        /* Additional search-specific styles */
        .search-header {
            margin-bottom: 20px;
        }

        .search-title {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .search-info {
            color: var(--light-text);
            margin-bottom: 20px;
        }

        .search-highlight {
            background-color: rgba(255, 230, 0, 0.2);
            padding: 0 2px;
        }

        mark {
            background-color: rgba(255, 230, 0, 0.4);
            padding: 0 2px;
        }

        .results-summary {
            margin-bottom: 20px;
            color: var(--light-text);
            font-size: 0.95rem;
        }

        .results-summary .sort-info {
            color: var(--primary-color);
            font-weight: 500;
        }

        .search-result {
            margin-bottom: 25px;
            padding-bottom: 25px;
            border-bottom: 1px solid var(--border-color);
        }

        .search-result:last-child {
            border-bottom: none;
        }

        .result-category {
            margin-bottom: 8px;
        }

        .result-title {
            font-size: 1.4rem;
            margin-bottom: 10px;
        }

        .result-excerpt {
            color: var(--text-color);
            margin-bottom: 10px;
        }

        .result-meta {
            display: flex;
            gap: 15px;
            color: var(--light-text);
            font-size: 0.9rem;
        }
    </style>
</head>

<body>
    <!-- Top Bar -->
    <div class="top-bar">
        <div class="container">
            <div class="date-weather">
                <span class="current-date"><?php echo date("l, F j, Y"); ?></span>
                <span class="weather">
                    <?php echo $weather['temp']; ?>°C Tirana
                    <i class="fas fa-<?php echo $weather['icon']; ?>"></i>
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
                while ($cat = $categories->fetch_assoc()) {
                    if ($count < 6) {
                        // First 6 categories go directly in the nav
                        ?>
                        <li><a href="category.php?slug=<?php echo htmlspecialchars($cat['slug']); ?>">
                                <?php echo htmlspecialchars($cat['name']); ?>
                            </a></li>
                        <?php
                    } else {
                        // Store the rest for the dropdown
                        $dropdown_items[] = $cat;
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
                    <input type="text" name="q" placeholder="Search..." value="<?php echo htmlspecialchars($query); ?>"
                        required>
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main>
        <div class="container">
            <div class="two-column-layout">
                <div class="main-content">
                    <!-- Search Header -->
                    <div class="search-header">
                        <h1 class="search-title">Search Results</h1>
                        <p class="search-info">
                            Showing results for <strong>"<?php echo htmlspecialchars($query); ?>"</strong>
                        </p>
                    </div>

                    <!-- Sort Controls -->
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
                    </div>

                    <!-- Results Summary -->
                    <div class="results-summary">
                        <p>
                            <?php if ($total_articles === 0): ?>
                                No articles found matching your search
                            <?php else: ?>
                                Found <?php echo $total_articles; ?> article<?php echo $total_articles > 1 ? 's' : ''; ?>
                                matching your search
                                · Showing <?php echo min($articles_per_page, $total_articles - $offset); ?> of
                                <?php echo $total_articles; ?>

                                <?php if ($sort !== 'newest'): ?>
                                    <span class="sort-info">
                                        · Sorted by: <?php
                                        echo match ($sort) {
                                            'oldest' => 'Oldest first',
                                            'most_viewed' => 'Most viewed',
                                            'title_az' => 'Title (A-Z)',
                                            'title_za' => 'Title (Z-A)',
                                            default => 'Newest first'
                                        };
                                        ?>
                                    </span>
                                <?php endif; ?>
                            <?php endif; ?>
                        </p>
                    </div>

                    <?php if ($search_results->num_rows > 0): ?>
                        <!-- Search Results -->
                        <div class="search-results">
                            <?php while ($result = $search_results->fetch_assoc()): ?>
                                <div class="search-result">
                                    <div class="result-category">
                                        <a href="category.php?slug=<?php echo htmlspecialchars($result['category_slug']); ?>">
                                            <span
                                                class="category-label <?php echo getCategoryClass($result['category_name']); ?>">
                                                <?php echo htmlspecialchars($result['category_name']); ?>
                                            </span>
                                        </a>
                                    </div>
                                    <h2 class="result-title">
                                        <a href="article.php?slug=<?php echo htmlspecialchars($result['slug']); ?>">
                                            <?php echo highlightSearchTerm($result['title'], $query); ?>
                                        </a>
                                    </h2>
                                    <p class="result-excerpt">
                                        <?php echo highlightSearchTerm($result['excerpt'], $query); ?>
                                    </p>
                                    <div class="result-meta">
                                        <span class="time"><?php echo time_elapsed_string($result['published_at']); ?></span>
                                        <span class="author">By <?php echo htmlspecialchars($result['author_name']); ?></span>
                                        <span class="views"><i class="fas fa-eye"></i>
                                            <?php echo number_format($result['view_count']); ?></span>
                                    </div>
                                </div>
                            <?php endwhile; ?>
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
                    <?php else: ?>
                        <!-- No Results Message -->
                        <div class="no-articles">
                            <i class="fas fa-search"></i>
                            <h3>No results found</h3>
                            <p>We couldn't find any articles matching your search term
                                "<strong><?php echo htmlspecialchars($query); ?></strong>".</p>
                            <p>Suggestions:</p>
                            <ul>
                                <li>Check your spelling</li>
                                <li>Try using different keywords or more general terms</li>
                                <li>Browse our <a href="index.php">homepage</a> or <a href="#"
                                        onclick="history.back(); return false;">go back</a> to your previous page</li>
                            </ul>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Sidebar -->
                <aside class="sidebar">
                    <!-- Search Tips -->
                    <div class="sidebar-section">
                        <div class="sidebar-header">Search Tips</div>
                        <div class="sidebar-content">
                            <ul>
                                <li>Use specific keywords for more accurate results</li>
                                <li>Search for phrases by putting them in quotes</li>
                                <li>Include both general and specific terms</li>
                                <li>Check spelling of unusual or technical terms</li>
                            </ul>
                        </div>
                    </div>

                    <!-- Categories -->
                    <div class="sidebar-section">
                        <div class="sidebar-header">Browse Categories</div>
                        <div class="sidebar-content">
                            <ul class="category-links enhanced">
                                <?php
                                $categories = $conn->query($cat_query);
                                while ($cat = $categories->fetch_assoc()) {
                                    // Get category class for styling
                                    $catClass = getCategoryClass($cat['name']);

                                    // Count articles in this category
                                    $count_stmt = $conn->prepare("SELECT COUNT(*) AS total FROM articles WHERE category_id = ? AND status = 'published'");
                                    $count_stmt->bind_param("i", $cat['id']);
                                    $count_stmt->execute();
                                    $count_result = $count_stmt->get_result();
                                    $article_count = $count_result->fetch_assoc()['total'];

                                    echo '<li>';
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
                        $categories = $conn->query($cat_query);
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