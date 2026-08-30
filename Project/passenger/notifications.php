<?php
require_once '../config/database.php';
include 'header.php';

$user_id = (int)$_SESSION['user_id'];

// Mark all as read when opened
$conn->query("UPDATE notifications SET status = 'Read' WHERE user_id = $user_id");

$res = $conn->query("SELECT * FROM notifications WHERE user_id = $user_id ORDER BY id DESC");
?>

<h2>Notifications & Alerts</h2>

<table>
    <thead>
        <tr>
            <th>Type</th>
            <th>Message</th>
            <th>Time</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($res && $res->num_rows > 0): ?>
            <?php while ($row = $res->fetch_assoc()): ?>
                <tr>
                    <td><span class="badge"><?php echo htmlspecialchars($row['type']); ?></span></td>
                    <td><?php echo htmlspecialchars($row['message']); ?></td>
                    <td><?php echo htmlspecialchars($row['created_at']); ?></td>
                    <td><?php echo htmlspecialchars($row['status']); ?></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="4" style="text-align: center; color: #64748b; padding: 20px;">No notifications found.</td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>