<?php
session_start();
include('config/db.php'); 

if (!isset($_SESSION['user_id'])) {
    header("Location: index.php"); // Must be logged in
    exit();
}


// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Handle search form submission
$search_results = [];
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $search_query = trim($_POST['search_query']);
    if (!empty($search_query)) {
        $search_query_like = "%" . $search_query . "%"; // Prepare for SQL LIKE

        // Query to search in Recipe title, Ingredients, and Category
        $query = "
            SELECT r.recipe_id, r.title, r.description, r.instructions, r.prep_time, r.cook_time, r.servings, r.category, 
                   r.nutrition_info,
                   -- average of user ratings (same value as the recipe page)
                   (SELECT ROUND(AVG(rt.rating), 1) FROM rate rt WHERE rt.recipe_id = r.recipe_id) AS rating,
                   -- full ingredient list, not just the ingredient that matched
                   (SELECT GROUP_CONCAT(i.name SEPARATOR ', ')
                      FROM menu m JOIN ingredients i ON m.ingredient_id = i.ingredient_id
                     WHERE m.recipe_id = r.recipe_id) AS ingredients
            FROM recipe r
            WHERE r.title LIKE ? OR r.category LIKE ?
               OR EXISTS (SELECT 1 FROM menu m2 JOIN ingredients i2 ON m2.ingredient_id = i2.ingredient_id
                           WHERE m2.recipe_id = r.recipe_id AND i2.name LIKE ?)
        ";

        $stmt = $conn->prepare($query);
        $stmt->bind_param("sss", $search_query_like, $search_query_like, $search_query_like); // Bind parameters
        $stmt->execute(); // Execute the prepared statement
        $result = $stmt->get_result();
        $search_results = $result->fetch_all(MYSQLI_ASSOC); // Fetch results as associative array
        $stmt->close(); // Close the prepared statement
    } else {
        echo "Please enter a search term.";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Recipes</title>
    <link rel="stylesheet" href="styles.css"> <!-- Optional: Add CSS for styling -->
</head>
<body>
    <header>
        <h1>Recipe Search</h1>
    </header>
    <main>
        <!-- Back to Recipes Button -->
        <a href="recipes.php" class="btn">Back to Recipes</a>

        <section class="search-form">
            <h2>Find Your Favorite Recipes</h2>
            <form action="" method="POST">
                <input type="text" name="search_query" placeholder="Search by recipe name or ingredients..." required>
                <button type="submit">Search</button>
            </form>
        </section>
        <section class="search-results">
            <h2>Search Results</h2>
            <?php if (!empty($search_results)): ?>
                <ul>
                    <?php foreach ($search_results as $recipe): ?>
                        <li>
                            <h3><?php echo htmlspecialchars($recipe['title']); ?></h3>
                            <p><strong>Description:</strong> <?php echo htmlspecialchars($recipe['description'] ?? ''); ?></p>
                            <p><strong>Ingredients:</strong> <?php echo htmlspecialchars($recipe['ingredients'] ?? ''); ?></p>
                            <p><strong>Prep Time:</strong> <?php echo htmlspecialchars($recipe['prep_time']); ?> minutes</p>
                            <p><strong>Cook Time:</strong> <?php echo htmlspecialchars($recipe['cook_time']); ?> minutes</p>
                            <p><strong>Servings:</strong> <?php echo htmlspecialchars($recipe['servings']); ?></p>
                            <p><strong>Category:</strong> <?php echo htmlspecialchars($recipe['category']); ?></p>
                            <p><strong>Rating:</strong> <?php echo $recipe['rating'] !== null ? htmlspecialchars($recipe['rating']) . ' / 5' : 'No ratings yet'; ?></p>
                            <p><strong>Nutrition Info:</strong> <?php echo htmlspecialchars($recipe['nutrition_info']); ?></p>
                            <a href="recipe_detail.php?id=<?php echo htmlspecialchars($recipe['recipe_id']); ?>">View Recipe</a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php else: ?>
                <p>No recipes found. Try searching for something else!</p>
            <?php endif; ?>
        </section>
    </main>
    <footer>
        <p>&copy; 2024 Recipe Manager. All rights reserved.</p>
    </footer>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>
