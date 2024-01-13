header('Access-Control-Allow-Origin: *');
header('Content-Type: application/json');

function hideCreateArticleForm() {
    var form = document.getElementById('createArticleForm');
    form.style.display = 'none';
}

document.addEventListener('DOMContentLoaded', function () {
    var nameInput = document.querySelector('input[name="name"]');
    var saveButton = document.querySelector('input[type="submit"]');

    function toggleSaveButton() {
        if (nameInput && saveButton) {
            saveButton.disabled = !nameInput.value.trim();
        }
    }

    if (nameInput) {
        nameInput.addEventListener('input', toggleSaveButton);
        toggleSaveButton(); // Run on page load
    }
});

function getBaseUrl() {
    return window.location.origin + window.location.pathname.split('/').slice(0, -1).join('/');
}

// Define a function named deleteArticle that takes an articleId as a parameter
function deleteArticle(articleId) {
    // Call the getBaseUrl function to get the base URL of the current page
    var baseUrl = getBaseUrl();

    // Display a confirmation dialog to the user. If the user clicks "OK", execute the code inside the if statement
    if (confirm('Are you sure you want to delete this article?')) {
        // Send a DELETE request to the server to delete the article with the given ID
        fetch(baseUrl + '/article-delete/' + articleId, { method: 'DELETE' })
            .then(response => {
                // If the server responded with a success status (HTTP status 200-299), execute the code inside the if statement
                if (response.ok) {
                    // Count the number of <li> elements inside <ul> elements on the page
                    var remainingArticles = document.querySelectorAll('ul li').length;

                    // If there's only one article left on the page, execute the code inside the if statement
                    if (remainingArticles <= 1) {
                        // Get the current page number from the URL's query string. If the page number isn't specified in the URL, default to 1
                        var currentPage = parseInt(new URLSearchParams(window.location.search).get('p')) || 1;

                        // Calculate the number of the previous page. It's either one less than the current page, or 1 if the current page is 1
                        var previousPage = Math.max(currentPage - 1, 1);

                        // Redirect the browser to the previous page
                        window.location.href = baseUrl + '/articles?p=' + previousPage;
                    } else {
                        // If there's more than one article left on the page, reload the current page
                        location.reload();
                    }
                } else {
                    // If the server responded with an error status (HTTP status 300-599), log an error message to the console
                    console.error('Error:', error);

                    // Display an alert dialog to the user with an error message
                    alert('Error occurred while deleting the article.');
                }
            });
    }
}

function showCreateArticleForm() {
    document.getElementById('createArticleDialog').style.display = 'block';
}

function hideCreateArticleForm() {
    document.getElementById('createArticleDialog').style.display = 'none';
}

function checkArticleName() {
    var nameInput = document.getElementById('articleName');
    var createButton = document.getElementById('createButton');
    createButton.disabled = !nameInput.value.trim();
}

// Define a function named createArticle
function createArticle() {
    // Get the value of the HTML element with the ID 'articleName', remove leading and trailing whitespace
    var name = document.getElementById('articleName').value.trim();

    // Call the getBaseUrl function to get the base URL of the current page
    const baseUrl = getBaseUrl();

    // If the name variable is not an empty string, execute the code inside the if statement
    if (name) {
        // Send a POST request to the server to create a new article
        // The body of the request is a JSON string that contains the name of the new article
        fetch(baseUrl + '/article-create', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ name: name })
        }).then(response => {
            // If the server responded with a redirect, change the current page to the redirect URL
            if (response.redirected) {
                window.location.href = response.url;
            }
        })
    }
}
