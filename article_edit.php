<?php
// Prepare a SQL statement to select all columns from the 'articles' table where the id matches the given id
$stmt = $pdo->prepare("SELECT * FROM articles WHERE id = ?");
$stmt->execute([$id]);
// Fetch the result of the query as an associative array and assign it to $article
$article = $stmt->fetch(PDO::FETCH_ASSOC);

// Check if the request method is POST
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Get the updated name and content from the POST data
    $updatedName = $_POST['name'];
    $updatedContent = $_POST['content'];
    // Check if the updated name is not empty
    if (!empty($updatedName)) {
        // Prepare a SQL statement to update the name and content of the article where the id matches the given id
        $updateStmt = $pdo->prepare("UPDATE articles SET name = ?, content = ? WHERE id = ?");
        $updateStmt->execute([$updatedName, $updatedContent, $id]);
        // Redirect to the articles page
        header("Location: ".$baseUrl."articles");
        exit;
    } else {
        // If the updated name is empty, display an error message
        echo "Article name cannot be empty.";
    }
}

// Get the base URL of the current page
$baseUrl = getBaseUrl();

// Start of the HTML document
echo '<!DOCTYPE html>';
echo '<html lang="en">';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '<title>Edit Article</title>';
echo '<link rel="stylesheet" href="../styles3.css">';
echo '</head>';
echo '<body>';
echo '<div class="article-edit-container">';

// Check if the article exists
if ($article) {
    // If the article exists, display a form to edit the article
    echo "<form class='article-edit-form' method='post'>";
    echo "<label for='name'>Name:</label>";
    echo "<input type='text' id='name' name='name' value='{$article['name']}' maxlength='32' required />";
    echo "<label for='content'>Content:</label>";
    echo "<textarea id='content' name='content' maxlength='1024'>{$article['content']}</textarea>";
    echo "<div class='form-actions'>";
    echo "<input type='submit' class='save-button' value='Save' />";
    echo "<a href='" . $baseUrl . "articles' class='back-button'>Back to articles</a>";
    echo "</div>";
    echo "</form>";
} else {
    // If the article does not exist, send a 404 status code and display an error message
    http_response_code(404);
    echo "<p>Article not found</p>";
}

echo '</div>';
echo '</body>';
echo '</html>';
?>