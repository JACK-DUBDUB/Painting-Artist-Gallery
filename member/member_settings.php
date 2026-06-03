<?php
include '../functions.php';
// Connect to MySQL database
$pdo = pdo_connect_mysql();

$user = get_logged_in_user();
member_access($user); // Perform the check to deny access

$member_id = $user['memid']; // We need the member ID

$msg = '';

// Fetch dynamic subscription status
$current_status = [];
if ($member_id > 0) {
    $stmt = $pdo->prepare('SELECT Newsletter, NewsFlash FROM users WHERE MemberID = ?');
    $stmt->execute([$member_id]);
    $current_status = $stmt->fetch(PDO::FETCH_ASSOC);
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $request_type = isset($_POST['request_type']) ? trim($_POST['request_type']) : '';

    if ($request_type == 'subscribe_newsletter' || $request_type == 'subscribe_newsflash') {
        // Update subscription status directly
        $column = $request_type == 'subscribe_newsletter' ? 'Newsletter' : 'NewsFlash';
        $stmt = $pdo->prepare("UPDATE users SET $column = 1 WHERE MemberID = ?");
        $stmt->execute([$member_id]);
        $msg = "Your subscription status has been updated.";
    } else {
        // Check for existing unprocessed request of the same type with RequestStatus = 1
        $stmt = $pdo->prepare('SELECT * FROM user_requests WHERE MemberID = ? AND RequestType = ? AND RequestStatus = 1');
        $stmt->execute([$member_id, $request_type]);
        $existing_request = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($existing_request) {
            $msg = "You have already submitted a request to " . htmlspecialchars($request_type) . 
                   ".\nPlease wait for it to be processed.";
        } else {
            // Insert new user request
            if (!empty($request_type)) {
                $stmt = $pdo->prepare('INSERT INTO user_requests (MemberID, RequestType) VALUES (?, ?)');
                $stmt->execute([$member_id, $request_type]);
                $msg = "New user request has been added.";
            } else {
                $msg = "Please fill in all fields.";
            }
        }
    }
}
?>

<?=template_header('User Settings')?>
<div>
    <h2>ACCOUNT DETAILS</h2>
    <ul class="list-group">
        <li class="list-group-item"><strong>User ID:</strong> <?= htmlspecialchars($user['memid']) ?></li>
        <li class="list-group-item"><strong>Email:</strong> <?= htmlspecialchars($user['email']) ?></li>
        <li class="list-group-item"><strong>Name:</strong> <?= htmlspecialchars($user['fullname']) ?></li>
        <li class="list-group-item"><strong>Role:</strong> <?= htmlspecialchars($user['role'] == 2 ? 'Admin' : 'User') ?></li>
        <li class="list-group-item"><strong>NewsLetter:</strong> <?= htmlspecialchars($current_status['Newsletter'] == 1 ? 'Subscribed' : 'Not Subscribed') ?></li>
        <li class="list-group-item"><strong>NewsFlash:</strong> <?= htmlspecialchars($current_status['NewsFlash'] == 1 ? 'Subscribed' : 'Not Subscribed') ?></li>
    </ul>

    <form action="<?= htmlspecialchars($_SERVER["PHP_SELF"]) ?>" method="post">
        <div class="form-group">
            <h2>Account Requests</h2>
            <label for="request_type">Make an Account Request:</label>
            <select name="request_type" id="request_type" class="form-control" required>
                <option value="" disabled selected>Select</option>
                <?php if (isset($current_status['Newsletter']) && $current_status['Newsletter'] == 1): ?>
                    <option value="unsubscribe_newsletter">Unsubscribe from Newsletter</option>
                <?php elseif (isset($current_status['Newsletter']) && $current_status['Newsletter'] == 0): ?>
                    <option value="subscribe_newsletter">Subscribe to Newsletter</option>
                <?php endif; ?>
                <?php if (isset($current_status['NewsFlash']) && $current_status['NewsFlash'] == 1): ?>
                    <option value="unsubscribe_newsflash">Unsubscribe from Newsflash</option>
                <?php elseif (isset($current_status['NewsFlash']) && $current_status['NewsFlash'] == 0): ?>
                    <option value="subscribe_newsflash">Subscribe to Newsflash</option>
                <?php endif; ?>
                <option value="delete_account">Delete Account</option>
            </select>
        </div>
        <div>
            <!-- IM SORRY I HAD TO DO INLINE STYLES FORGIVE ME RHYS -->
            <input type="submit" class="btn btn-primary" value="Submit Request" style="top: 5px;">
        </div>
    </form>
</div>
<div>
    <?php if ($msg): ?>
        <div class="alert alert-info"><?= htmlspecialchars($msg) ?></div>
    <?php endif; ?>
</div>
<div>
    <a href="member_tickets.php" class="btn btn-primary">View Pending Tickets</a>
</div>

<?=template_footer()?>
