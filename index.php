<?php
// Enable the display of errors
ini_set('display_errors', 1);

// Set the error reporting level to report all errors
error_reporting(E_ALL);

// Require the db_config.php file and assign the returned value to $dbConfig
$dbConfig = require 'db_config.php';

// Try to execute the code inside the try block
try {
    // Create a new PDO instance to connect to the MySQL database
    // The DSN (Data Source Name) is constructed using the host and dbname values from $dbConfig
    // The username and password for the database are also taken from $dbConfig
    $pdo = new PDO("mysql:host={$dbConfig['host']};dbname={$dbConfig['dbname']}", $dbConfig['username'], $dbConfig['password']);

    // Set the error mode to exceptions, so that PDO will throw exceptions when there's an error
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // If there's a PDOException (an error with PDO), execute the code inside the catch block
    // In this case, it stops the script execution and displays an error message
    die("Database connection failed: " . $e->getMessage());
}

// Dynamic URL parsing
$requestUri = $_SERVER['REQUEST_URI'];
$urlPath = parse_url($requestUri, PHP_URL_PATH);

// Determine the page and ID from the URL
$urlSegments = explode('/', trim($urlPath, '/'));
// Identify the position of the script name (e.g., index.php) in the URL
$scriptNamePosition = array_search(basename($_SERVER['SCRIPT_NAME']), $urlSegments);

// Remove the segments before and including the script name
$urlSegments = array_slice($urlSegments, $scriptNamePosition + 1);

// Determine the page and ID from the remaining segments
$page = $urlSegments[1] ?? '';
$id = $urlSegments[2] ?? '';

// Define a function named getBaseUrl
function getBaseUrl() {
    // Check if HTTPS is used, if it is, set the protocol to 'https', otherwise set it to 'http'
    $protocol = (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? "https" : "http";
    
    // Get the host name from the server superglobal
    $host = $_SERVER['HTTP_HOST'];
    
    // Get the directory name of the script being executed
    $scriptName = dirname($_SERVER['SCRIPT_NAME']);

    // Return the base URL, which is a combination of protocol, host, and script name
    return $protocol . "://" . $host . $scriptName . '/';
}

//In this code, ? is a placeholder that gets replaced by the value of $id when the statement is executed. 
//Because the SQL command is compiled first before the data is inserted, the data can't be interpreted as SQL code, which prevents SQL injection.
$baseUrl = getBaseUrl(); // Store the base URL in a variable
switch ($page) {
    case 'articles':
        require 'articles.php';
        break;
    case 'article':
        require 'article_detail.php';
        break;
    case 'article-edit':
        require 'article_edit.php';
        break;
    // If the first segment of the URL is 'article-delete', execute the code inside this case
    case 'article-delete':
        // Get the third segment of the URL. If it doesn't exist, default to an empty string
        $id = $urlSegments[2] ?? '';

        // Prepare a SQL statement to delete an article from the 'articles' table where the id matches the given id
        $stmt = $pdo->prepare("DELETE FROM articles WHERE id = ?");

        // Execute the prepared statement, replacing the placeholder with the id
        $stmt->execute([$id]);

        // Send a response to the client saying that the article was deleted
        echo 'Article deleted';
        break;

    // If the first segment of the URL is 'article-create', execute the code inside this case
    case 'article-create':
        // Get the request body as a JSON string and decode it into a PHP array
        $input = json_decode(file_get_contents('php://input'), true);

        // If the 'name' key in the input array is not empty, execute the code inside the if statement
        if (!empty($input['name'])) {
            // Prepare a SQL statement to insert a new article into the 'articles' table with the given name and an empty content
            $stmt = $pdo->prepare("INSERT INTO articles (name, content) VALUES (?, '')");

            // Execute the prepared statement, replacing the placeholder with the name
            $stmt->execute([$input['name']]);

            // Get the id of the last inserted row
            $newArticleId = $pdo->lastInsertId();

            // Send a response to the client with the id of the new article as a JSON string
            echo json_encode(['id' => $newArticleId]);

            // Redirect the client to the edit page of the new article
            header("Location: $baseUrl./article-edit/$newArticleId");
        } else {
            // If the 'name' key in the input array is empty, send a response to the client with an error message as a JSON string
            echo json_encode(['error' => 'Article name is required']);
        }

        // Stop the script execution
        exit;
    default:
        require 'articles.php';
        break;
}