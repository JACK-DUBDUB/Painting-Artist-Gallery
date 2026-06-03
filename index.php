<?php
include 'functions.php';

// Connect to MySQL database
$pdo = pdo_connect_mysql();

// Get the page via GET request (URL param: page), if none exists default the page to 1
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;


// Hardcorded lines been removed //


// Get the search query if it exists
$search = isset($_GET['search']) ? trim($_GET['search']) : '';

// Prepare the base SQL query
$sql = "
    SELECT p.*, a.Name AS ArtistName, m.MediaType AS MediaName, s.StyleName AS StyleName
    FROM painting p
    LEFT JOIN artist a ON p.ArtistID = a.ArtistID
    LEFT JOIN media m ON p.MediaID = m.MediaID
    LEFT JOIN style s ON p.StyleID = s.StyleID
";

// Add search condition if search query exists
if ($search) {
    $sql .= " WHERE p.Title LIKE :search";
}



// Prepare the SQL statement
$stmt = $pdo->prepare($sql);


// Bind the search parameter if it exists
if ($search) {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}

// Execute the statement
$stmt->execute();

// Fetch the records
$paintingInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Get the total number of paintings for pagination
$num_paintings = $pdo->query("SELECT COUNT(*) FROM painting" . ($search ? " WHERE Title LIKE '%$search%'" : ''))->fetchColumn();


// Display the page
?>

<?=template_header('Home Page')?> <!-- 3/11 - Bozo moment, duplicate code for no hecking reason --> 

<div class="content">
<h1 class="text-center" id="page-title">Welcome Page</h1>
</div>

<?=template_footer()?>
