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
                <div class="ad-placeholder">Advertisement</div>
            </div>
        </div>
    </header>

    <!-- Navigation -->
    <nav>
        <div class="container">
            <ul class="main-nav">
                <li class="active"><a href="#">Home</a></li>
                <li><a href="#">Politics</a></li>
                <li><a href="#">Business</a></li>
                <li><a href="#">Technology</a></li>
                <li><a href="#">Health</a></li>
                <li><a href="#">Science</a></li>
                <li><a href="#">Sports</a></li>
                <li><a href="#">Entertainment</a></li>
                <li><a href="#">World</a></li>
                <li><a href="#">Opinion</a></li>
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
                    Global Summit on Climate Change Results in Historic Agreement • Tech Leaders Unveil Next Generation
                    AI • Major Sports Championship Final Set for Weekend • Economic Report Shows Surprising Growth
                </marquee>
            </div>
        </div>
    </div>

    <!-- Main Content -->
    <main>
        <div class="container">
            <!-- Hero Section -->
            <section class="hero">
                <div class="featured-article">
                    <div class="featured-image">
                        <img src="https://source.unsplash.com/random/800x450/?news,politics" alt="Featured Article">
                    </div>
                    <div class="featured-content">
                        <span class="category">Politics</span>
                        <h2>Landmark Legislation Passes After Marathon Debate in Congress</h2>
                        <p class="excerpt">After months of deliberation and a 14-hour session, lawmakers have finally
                            reached an agreement on the comprehensive reform package that addresses key issues facing
                            the nation.</p>
                        <span class="time">2 hours ago</span>
                        <span class="author">By John Smith</span>
                    </div>
                </div>
                <div class="secondary-articles">
                    <article>
                        <img src="https://source.unsplash.com/random/400x250/?business" alt="Business News">
                        <span class="category">Business</span>
                        <h3>Stock Markets Reach All-Time High After Fed Announcement</h3>
                        <span class="time">3 hours ago</span>
                    </article>
                    <article>
                        <img src="https://source.unsplash.com/random/400x250/?technology" alt="Technology News">
                        <span class="category">Technology</span>
                        <h3>Revolutionary New Device Promises to Transform Healthcare</h3>
                        <span class="time">5 hours ago</span>
                    </article>
                </div>
            </section>

            <div class="content-wrapper">
                <!-- Main News Section -->
                <section class="main-news">
                    <!-- News Category: World -->
                    <div class="news-category">
                        <div class="category-header">
                            <h2>World News</h2>
                            <a href="#" class="view-all">View All</a>
                        </div>
                        <div class="news-grid">
                            <article class="news-item">
                                <img src="https://source.unsplash.com/random/300x200/?world,global" alt="World News">
                                <h3>International Space Station Celebrates 25 Years in Orbit</h3>
                                <p>The orbiting laboratory marks quarter-century of continuous human presence in space,
                                    hosting over 3,000 scientific experiments.</p>
                                <span class="time">6 hours ago</span>
                            </article>
                            <article class="news-item">
                                <img src="https://source.unsplash.com/random/300x200/?europe" alt="Europe News">
                                <h3>European Union Unveils New Environmental Initiative</h3>
                                <p>The ambitious program aims to reduce carbon emissions by 55% before 2030 through
                                    coordinated efforts across member nations.</p>
                                <span class="time">8 hours ago</span>
                            </article>
                            <article class="news-item">
                                <img src="https://source.unsplash.com/random/300x200/?asia" alt="Asia News">
                                <h3>Historic Trade Agreement Signed Between Asian Nations</h3>
                                <p>The landmark deal removes tariffs on thousands of goods and creates the world's
                                    largest free trade zone.</p>
                                <span class="time">10 hours ago</span>
                            </article>
                        </div>
                    </div>

                    <!-- News Category: Technology -->
                    <div class="news-category">
                        <div class="category-header">
                            <h2>Technology</h2>
                            <a href="#" class="view-all">View All</a>
                        </div>
                        <div class="news-grid">
                            <article class="news-item">
                                <img src="https://source.unsplash.com/random/300x200/?tech" alt="Tech News">
                                <h3>Next-Gen Smartphone Revealed with Groundbreaking Features</h3>
                                <p>The latest flagship device introduces revolutionary camera technology and
                                    unprecedented battery life.</p>
                                <span class="time">4 hours ago</span>
                            </article>
                            <article class="news-item">
                                <img src="https://source.unsplash.com/random/300x200/?ai" alt="AI News">
                                <h3>AI Research Makes Breakthrough in Natural Language Processing</h3>
                                <p>New algorithm demonstrates human-like understanding of contextual language nuances,
                                    opening doors for improved digital assistants.</p>
                                <span class="time">7 hours ago</span>
                            </article>
                            <article class="news-item">
                                <img src="https://source.unsplash.com/random/300x200/?cybersecurity"
                                    alt="Cybersecurity News">
                                <h3>Major Cybersecurity Vulnerability Discovered in Popular Software</h3>
                                <p>Experts urge immediate updates as researchers find critical flaw that could affect
                                    millions of users worldwide.</p>
                                <span class="time">9 hours ago</span>
                            </article>
                        </div>
                    </div>
                </section>

                <!-- Sidebar -->
                <aside class="sidebar">
                    <!-- Popular News -->
                    <div class="sidebar-section popular-news">
                        <h3>Popular News</h3>
                        <div class="popular-list">
                            <article class="popular-item">
                                <img src="https://source.unsplash.com/random/100x100/?trending" alt="Popular News">
                                <div>
                                    <h4>Cultural Festival Attracts Record Number of Visitors</h4>
                                    <span class="time">1 day ago</span>
                                </div>
                            </article>
                            <article class="popular-item">
                                <img src="https://source.unsplash.com/random/100x100/?popular" alt="Popular News">
                                <div>
                                    <h4>Scientists Discover New Species in Deep Ocean Expedition</h4>
                                    <span class="time">2 days ago</span>
                                </div>
                            </article>
                            <article class="popular-item">
                                <img src="https://source.unsplash.com/random/100x100/?viral" alt="Popular News">
                                <div>
                                    <h4>Historic Building Restored After Decades of Neglect</h4>
                                    <span class="time">3 days ago</span>
                                </div>
                            </article>
                            <article class="popular-item">
                                <img src="https://source.unsplash.com/random/100x100/?famous" alt="Popular News">
                                <div>
                                    <h4>Award-Winning Film Director Announces Next Project</h4>
                                    <span class="time">4 days ago</span>
                                </div>
                            </article>
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
                        <div class="sidebar-ad">Advertisement</div>
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
                        <li><a href="#">Politics</a></li>
                        <li><a href="#">Business</a></li>
                        <li><a href="#">Technology</a></li>
                        <li><a href="#">Health</a></li>
                        <li><a href="#">Science</a></li>
                        <li><a href="#">Sports</a></li>
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