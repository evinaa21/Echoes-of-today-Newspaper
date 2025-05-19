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
    const searchForm = document.querySelector('.search-box form');
    if (searchForm) {
        searchForm.addEventListener('submit', function(e) {
            // Don't prevent default - let the form submit normally
            // Or manually redirect if you want to control the process
            const searchTerm = this.querySelector('input').value.trim();
            if (!searchTerm) {
                e.preventDefault(); // Only prevent submission if empty
                alert('Please enter a search term');
            }
            // Otherwise let the form submit naturally to search.php
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

    // View switching
    const viewButtons = document.querySelectorAll('.view-option');
    const articlesContainer = document.getElementById('articlesContainer');
    
    if (viewButtons.length && articlesContainer) {
        viewButtons.forEach(button => {
            button.addEventListener('click', function() {
                // Remove active class from all buttons
                viewButtons.forEach(btn => btn.classList.remove('active'));
                // Add active class to clicked button
                this.classList.add('active');
                
                // Change view
                const viewType = this.dataset.view;
                articlesContainer.className = viewType === 'list' 
                    ? 'news-list category-list' 
                    : 'news-grid category-grid';
                
                // Save preference
                localStorage.setItem('preferred_view', viewType);
            });
        });
        
        // Load saved preference
        const savedView = localStorage.getItem('preferred_view');
        if (savedView) {
            const button = document.querySelector(`.view-option[data-view="${savedView}"]`);
            if (button) button.click();
        }
    }
});