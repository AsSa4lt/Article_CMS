<?php
// Pagination setup
$perPage = 10;
$page = $_GET['p'] ?? 1;
$offset = ($page - 1) * $perPage;

// Fetch articles from the database
$stmt = $pdo->prepare("SELECT * FROM articles ORDER BY id DESC LIMIT :offset, :perPage");
$stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
$stmt->bindParam(':perPage', $perPage, PDO::PARAM_INT);
$stmt->execute();
$articles = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Calculate the total number of pages
$totalStmt = $pdo->query("SELECT COUNT(*) FROM articles");
$totalArticles = $totalStmt->fetchColumn();
$totalPages = $totalArticles > 0 ? ceil($totalArticles / $perPage) : 0;

// If there are no articles, set the page to 0
if ($totalArticles == 0) {
    $page = 0;
}

// If the page number is greater than the total number of pages, redirect to the last page
if ($page > $totalPages && $totalPages > 0) {
    header("Location: " . getBaseUrl() . "/articles?p=" . $totalPages);
    exit;
}

echo '<!DOCTYPE html>';
echo '<html lang="en">';
echo '<head>';
echo '<meta charset="UTF-8">';
echo '<meta name="viewport" content="width=device-width, initial-scale=1.0">';
echo '<title>Article List</title>';
echo '<link rel="stylesheet" href="styles.css">';
echo '</head>';
echo '<body>';
echo '<div class="article-list-container">';
echo '<h1>Article List</h1>';
echo '<ul class="article-list">';


$baseUrl = getBaseUrl(); // Store the base URL in a variable
foreach ($articles as $article) {
    echo '<li>';
    echo '<span class="article-name">' . htmlspecialchars($article['name']) . '</span>';
    echo '<div class="article-actions">';
    echo '<a class="article-action" href="' . $baseUrl . 'article/' . htmlspecialchars($article['id']) . '">Show</a>';
    echo '<a class="article-action" href="' . $baseUrl . 'article-edit/' . htmlspecialchars($article['id']) . '">Edit</a>';
    echo '<button class="article-action delete" onclick="deleteArticle(' . htmlspecialchars($article['id']) . ')">Delete</button>';
    echo '</div>';
    echo '</li>';
}


echo '</ul>';
echo '<div class="pagination-container">';

// Start Pagination Links
echo '<div class="pagination">';

if ($page > 1) {
    echo '<a href="'. $baseUrl .'articles?p=' . ($page - 1) . '">Previous</a>';
}

if ($page < $totalPages) {
    echo '<a href="'. $baseUrl .'articles?p=' . ($page + 1) . '">Next</a>';
}

echo '</div>'; // Close Pagination Links

// Page Count and Create Article Button
echo '<div class="page-count-create">';
echo '<span class="page-count">Page ' . $page . ' of ' . $totalPages . '</span>';
echo '<button class="create-article-button" onclick="showCreateArticleForm()">Create Article</button>';
echo '</div>'; // Close Page Count and Create Article Button

echo '</div>'; // Close Pagination Container

echo '<div id="createArticleDialog" class="modal" style="display:none;">';
echo '<form id="createArticleForm">';
echo '<label for="articleName">Name:</label><br>';
echo '<input type="text" id="articleName" name="name" maxlength="32" required oninput="checkArticleName()"><br>';
echo '<input type="button" id="createButton" value="Create" onclick="createArticle()" disabled>';
echo '<input type="button" value="Cancel" onclick="hideCreateArticleForm()">';
echo '</form>';
echo '</div>';

echo '<script src="./script.js"></script>';
echo '</body>';
echo '</html>';
