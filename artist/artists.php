<?php
/*
KETA GROUP PROJECT
30074191 / Keanu Farro
Jack
Voushikha
SOURCE CODE: https://codeshack.io/crud-application-php-pdo-mysql/
*/
include '../functions.php';

// Connect to MySQL database
$pdo = pdo_connect_mysql();

// Get the page via GET request (URL param: page), default to 1
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;

// Get the search query if it exists
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
// Get filter parameters
$filter_type = isset($_GET['filter_type']) ? $_GET['filter_type'] : '';
$filter_value = isset($_GET['filter_value']) ? $_GET['filter_value'] : '';

// Reset filter_value if the filter_type is changed
if (isset($_GET['previous_filter_type']) && $_GET['previous_filter_type'] != $filter_type) {
    $filter_value = '';
}

// Store the current filter type as the previous filter type for the next request
$previous_filter_type = $filter_type;

// Prepare the SQL statement to get records from the artist table
$sql = "SELECT ArtistID, Name, LifeSpan, Nationality, SmallImage, LargeImage FROM artist";

// See if the Create, Update and Delete Functions should be hidden or not
$hideCUD = Hide_Actions();

// method to decide whether to hide the Create, Update, and delete functions if the user logged in is Admin or not
function Hide_Actions() { 
    // NOTE: role 1 = user, role 2 = admin

    // See what user is currently logged in (call get logged in user method from functions.php)
    $user = get_logged_in_user();

    // Default to hiding CUD actions
    $hideCUD = true;

    // check to see if the current user is an admin
    if (isset($user['role']) && $user['role'] == 2) {
        // if the user is an admin make sure the CUD functions are visible
        $hideCUD = false;
    }
   
    return $hideCUD;
 }


// Check if there is a search query and add it to the SQL query
$conditions = [];
if ($search) {
    $conditions[] = "Name LIKE :search";
}

// Check if there is a filter condition (e.g., lifespan or nationality) and add it to the SQL query
if ($filter_type && $filter_value) {
    switch ($filter_type) {
        case 'lifespan':
            $conditions[] = "LifeSpan = :filter_value";
            break;
        case 'nationality':
            $conditions[] = "Nationality = :filter_value";
            break;
    }
}

// If there are conditions, append them to the SQL query with WHERE and AND
if (count($conditions) > 0) {
    $sql .= " WHERE " . implode(" AND ", $conditions);
}

// Prepare the SQL statement
$stmt = $pdo->prepare($sql);

// Bind the parameters if they exist
if ($search) {
    $stmt->bindValue(':search', "%$search%", PDO::PARAM_STR);
}

if ($filter_type && $filter_value) {
    $stmt->bindValue(':filter_value', htmlspecialchars($filter_value), PDO::PARAM_STR);
}

// Execute the statement
$stmt->execute();

// Fetch the records
$artistInfo = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Fetch distinct lifespans and nationalities for filter options
$lifespans = $pdo->query("SELECT DISTINCT LifeSpan FROM artist ORDER BY LifeSpan DESC")->fetchAll(PDO::FETCH_ASSOC);
$nationalities = $pdo->query("SELECT DISTINCT Nationality FROM artist ORDER BY Nationality DESC")->fetchAll(PDO::FETCH_ASSOC);
?>

<?=template_header('Artists')?>

<div class="content read">
    <h2>Artist Details</h2>
    <!-- hide create artist function if user is not admin, show create artist function if user is admin -->
    <?php if (!$hideCUD): ?>
    <a href="create_artist.php" class="create-artist"><i class="fas fa-plus fa-xs"></i> Create New Artist</a>
    <?php endif; ?>
    <!--
        
    Filter Dropdown  
    This was a complete fuck around because we can't use JS.
        
    We want to select the filter type, when we do, it refreshes the page with the selected filter 
    so that the filter value options are updated with the unique values of the specified filter.
    This is done at the onchange="this.form.submit()

    Then once we have selected a value we press apply, the value should be updated so that it only
    displays artists with the specified values. i.e. Dutch will only show artists with a Dutch nationality.
    -->
    <form method="GET" action="artists.php">
        <input type="hidden" name="previous_filter_type" value="<?=$filter_type?>">
        <label for="filter_type">Filter Type:</label>
        <select name="filter_type" id="filter_type" onchange="this.form.submit()">
            <option value="">Select</option>
            <option value="lifespan" <?= $filter_type == 'lifespan' ? 'selected' : '' ?>>Lifespan</option>
            <option value="nationality" <?= $filter_type == 'nationality' ? 'selected' : '' ?>>Nationality</option>
        </select>

        <label for="filter_value">Filter By:</label>
        <select name="filter_value" id="filter_value">
            <option value="">Select</option>
            <?php
            if ($filter_type == 'lifespan') {
                foreach ($lifespans as $lifespan) {
                    echo '<option value="' . htmlspecialchars($lifespan['LifeSpan']) . '" ' . ($filter_value == $lifespan['LifeSpan'] ? 'selected' : '') . '>' . htmlspecialchars($lifespan['LifeSpan']) . '</option>';
                }
            } elseif ($filter_type == 'nationality') {
                foreach ($nationalities as $nationality) {
                    echo '<option value="' . htmlspecialchars($nationality['Nationality']) . '" ' . ($filter_value == $nationality['Nationality'] ? 'selected' : '') . '>' . htmlspecialchars($nationality['Nationality']) . '</option>';
                }
            }
            ?>
        </select>
        <button type="submit">Apply Filter</button>
    </form>

    <table>
        <thead>
            <tr>
            <th scope="col">ArtistID</th>
            <th scope="col">Name</th>
            <th scope="col">LifeSpan</th>
            <th scope="col">Nationality</th>
            <th scope="col">Image</th>
        <!-- If user is admin then display the update and delete actions -->
                <?php if (!$hideCUD): ?>
                <th scope="col">Actions</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($artistInfo as $artist): ?>
            <tr>
                <td><?=$artist['ArtistID']?></td> 
                <td><?=$artist['Name']?></td> 
                <td><?=$artist['LifeSpan']?></td>
                <td><?=$artist['Nationality']?></td>
                <td>
                    <!-- 13/11/2024 create div with class of image-container -->
                <div class="image-container">
                    <!-- Small image shown on default in the table-->
                    <img src="data:image/png;base64,<?= base64_encode($artist['SmallImage']) ?>" alt="Small image" width="50" height="50" class="small-image">

                    <!-- Large image will be displayed when hovered over -->
                    <img src="data:image/png;base64,<?= base64_encode($artist['LargeImage']) ?>" alt="Large image" class="large-image">
                </div>
            </td>
                <!-- If user is admin then display the update and delete actions -->
                <?php if (!$hideCUD): ?>
                <td class="actions">
                    <a href="update_artist.php?id=<?=$artist['ArtistID']?>" class="edit"><i class="fas fa-pen fa-xs"></i></a>
                    <a href="delete_artist.php?id=<?=$artist['ArtistID']?>" class="trash"><i class="fas fa-trash fa-xs"></i></a>
                </td>
                <?php endif; ?>
            </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>

<?=template_footer()?>
