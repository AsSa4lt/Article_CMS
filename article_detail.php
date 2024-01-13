<?php
// Prepare a SQL statement to select all columns from the 'articles' table where the id matches the given id
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");

// Execute the prepared statement, replacing the placeholder with the id
$stmt->execute([$id]);

// Fetch the result of the query as an associative array and assign it to $article
$article = $stmt->fetch(PDO::FETCH_ASSOC);

// Call the getBaseUrl function to get the base URL of the current page and assign it to $baseUrl
$baseUrl = getBaseUrl();

echo '<!DOCTYPE html>';
echo '<html lang="en">';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '<title>Article Detail</title>';
echo '<link rel="stylesheet" href="../styles2.css">';

echo '</head>';
echo '<body>';
echo '<div class="article-detail-container">';

if ($article) {
    echo "<h1 class='article-title'>{$article['name']}</h1>";
    echo "<div class='article-content'>{$article['content']}</div>";
    echo "<div class='article-actions'>";
    // Use the dynamic base URL for the Edit link
    echo "<a href='" . $baseUrl . "article-edit/{$id}' class='article-edit-button'>Edit</a>"; 
    // Use the dynamic base URL for the Back to articles link
    echo "<a href='" . $baseUrl . "articles' class='back-to-articles-button'>Back to articles</a>";  
    echo "</div>";
}  else {
    http_response_code(404);
    echo "<p>Article not found</p>";
}

echo '</div>'; // Close article-detail-container
echo '</body>';
echo '</html>';
?>
