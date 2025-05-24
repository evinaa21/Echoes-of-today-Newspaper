
// Create this new JavaScript file
document.addEventListener('DOMContentLoaded', function() {
    // Get article ID from meta tag or data attribute
    const articleId = document.querySelector('article').dataset.articleId;
    
    // Update view count via AJAX
    fetch('update_views.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            article_id: articleId
        })
    });
});