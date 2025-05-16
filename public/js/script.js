document.addEventListener('DOMContentLoaded', function() {
    // Current date for the top bar
    const now = new Date();
    const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
    const formattedDate = now.toLocaleDateString('en-US', options);
    
    const dateElement = document.querySelector('.current-date');
    if (dateElement) {
        dateElement.textContent = formattedDate;
    }

    // Sticky navigation
    const nav = document.querySelector('nav');
    const navTop = nav.offsetTop;
    
    function stickyNavigation() {
        if (window.scrollY >= navTop) {
            document.body.classList.add('fixed-nav');
            document.body.style.paddingTop = nav.offsetHeight + 'px';
        } else {
            document.body.classList.remove('fixed-nav');
            document.body.style.paddingTop = 0;
        }
    }
    
    window.addEventListener('scroll', stickyNavigation);

    // Search functionality
    const searchForm = document.querySelector('.search-box');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const searchTerm = this.querySelector('input').value.trim();
            if (searchTerm) {
                alert(`Search for: ${searchTerm}\nThis would normally redirect to search results.`);
                // In a real implementation, you would redirect to search results page
                // window.location.href = `/search?q=${encodeURIComponent(searchTerm)}`;
            }
        });
    }

    // Newsletter subscription
    const newsletterForms = document.querySelectorAll('.newsletter form');
    newsletterForms.forEach(form => {
        form.addEventListener('submit', function(e) {
            e.preventDefault();
            const email = this.querySelector('input[type="email"]').value.trim();
            if (email) {
                alert(`Thank you for subscribing with ${email}!\nYou would receive a confirmation email in a real implementation.`);
                this.reset();
            }
        });
    });

    // Add hover effect to news items
    const newsItems = document.querySelectorAll('.news-item, .popular-item, .secondary-articles article');
    newsItems.forEach(item => {
        item.addEventListener('mouseenter', function() {
            this.style.transform = 'translateY(-5px)';
            this.style.transition = 'transform 0.3s ease';
            this.style.boxShadow = '0 10px 20px rgba(0,0,0,0.1)';
        });
        
        item.addEventListener('mouseleave', function() {
            this.style.transform = 'translateY(0)';
            this.style.boxShadow = '';
        });
    });

    // Simulate loading more articles
    const viewAllLinks = document.querySelectorAll('.view-all');
    viewAllLinks.forEach(link => {
        link.addEventListener('click', function(e) {
            e.preventDefault();
            const categoryName = this.closest('.news-category').querySelector('h2').textContent;
            alert(`Loading more articles from ${categoryName} category...\nThis would normally load or redirect to more content.`);
        });
    });
});