<?php
require_once '../config/database.php';
include 'header.php';

$user_id = (int)$_SESSION['user_id'];
$msg = "";


if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $item_name = $conn->real_escape_string(trim($_POST['item_name'] ?? ''));
    $lost_location = $conn->real_escape_string(trim($_POST['lost_location'] ?? ''));
    $description = $conn->real_escape_string(trim($_POST['description'] ?? ''));

    if (!empty($item_name) && !empty($lost_location)) {
        $ins = "INSERT INTO lost_items (user_id, item_name, description, lost_location, status, reported_date) 
                VALUES ($user_id, '$item_name', '$description', '$lost_location', 'Pending', NOW())";
        if ($conn->query($ins)) {
           
            $conn->query("INSERT INTO notifications (user_id, message, type, status, created_at) 
                         VALUES ($user_id, 'Report filed for missing item: $item_name. Airport desk is searching.', 'Lost Item', 'Unread', NOW())");
            
            $msg = "<div style='color: #059669; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 12px 16px; border-radius: 8px; margin-bottom: 20px;'>Report submitted successfully! Our security team has been notified.</div>";
        } else {
            $msg = "<div style='color: #dc2626; background: #fee2e2; padding: 12px; border-radius: 8px; margin-bottom: 20px;'>Failed to submit report: " . $conn->error . "</div>";
        }
    } else {
        $msg = "<div style='color: #dc2626; background: #fee2e2; padding: 12px; border-radius: 8px; margin-bottom: 20px;'>Please provide both item name and location.</div>";
    }
}


$items = $conn->query("SELECT * FROM lost_items WHERE user_id = $user_id ORDER BY id DESC");
?>

<style>
.lf-grid {
    display: grid;
    grid-template-columns: 1.2fr 0.8fr;
    gap: 24px;
    margin-top: 15px;
    margin-bottom: 30px;
}
.lf-card {
    background: #ffffff;
    border: 1px solid #e2e8f0;
    border-radius: 14px;
    padding: 24px;
    box-shadow: 0 2px 4px rgba(0,0,0,0.02);
}
.lf-card h3 {
    font-size: 16px;
    font-weight: 700;
    color: #0f172a;
    margin-bottom: 6px;
}
.lf-card p.desc {
    color: #64748b;
    font-size: 13px;
    margin-bottom: 20px;
}

.form-group {
    margin-bottom: 16px;
}
.form-group label {
    display: block;
    font-size: 13px;
    font-weight: 600;
    color: #334155;
    margin-bottom: 6px;
}
.form-control {
    width: 100%;
    padding: 11px 14px;
    background: #f8fafc;
    border: 1px solid #cbd5e1;
    border-radius: 8px;
    font-size: 14px;
    color: #0f172a;
    outline: none;
    transition: 0.2s;
}
.form-control:focus {
    border-color: #0284c7;
    background: #ffffff;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.1);
}

.helpdesk-box {
    background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
    border: 1px solid #bae6fd;
    border-radius: 12px;
    padding: 20px;
    margin-bottom: 18px;
}
.helpdesk-box h4 {
    color: #0369a1;
    font-size: 15px;
    margin-bottom: 8px;
    display: flex;
    align-items: center;
    gap: 8px;
}
.helpdesk-box p {
    font-size: 13px;
    color: #0c4a6e;
    line-height: 1.5;
}

.badge-pending { background: #fef3c7; color: #b45309; }
.badge-found { background: #dcfce7; color: #15803d; }
.badge-claimed { background: #e0f2fe; color: #0369a1; }
</style>

<h2>Lost & Found Assistance</h2>
<?php echo $msg; ?>

<div class="lf-grid">
    
   
    <div class="lf-card">
        <h3>🔍 Report a Missing Belonging</h3>
        <p class="desc">Provide specific details to help airport security locate your item quickly.</p>

        <form method="POST" action="lost_found.php">
            <div class="form-group">
                <label>Item Name / Title *</label>
                <input type="text" name="item_name" class="form-control" placeholder="e.g. Black Leather Wallet, Apple iPad" required>
            </div>

            <div class="form-group">
                <label>Approximate Lost Location *</label>
                <input type="text" name="lost_location" class="form-control" placeholder="e.g. Security Check Terminal 1, Gate 04, Baggage Belt" required>
            </div>

            <div class="form-group">
                <label>Detailed Description & Identifying Marks</label>
                <textarea name="description" rows="3" class="form-control" placeholder="Color, brand name, distinctive stickers, or unique contents..."></textarea>
            </div>

            <button type="submit" class="btn" style="padding: 11px 24px; font-size: 14px;">Submit Lost Report</button>
        </form>
    </div>

    
    <div>
        <div class="helpdesk-box">
            <h4>🛡 Airport Security Helpdesk</h4>
            <p>
                Found items are kept securely at the <strong>Ground Floor Terminal 1 Central Claim Desk</strong> for up to 30 days.
            </p>
            <div style="margin-top: 12px; font-size: 12px; color: #0369a1; font-weight: 600;">
                📞 Hotline: +880 1700-000000<br>
                ⏰ Service Hours: 24/7 Active
            </div>
        </div>

        <div class="lf-card">
            <h4 style="font-size: 14px; color: #0f172a; margin-bottom: 10px;">Steps for Recovery:</h4>
            <ul style="padding-left: 18px; font-size: 13px; color: #64748b; line-height: 1.6;">
                <li>Submit the form with precise identification details.</li>
                <li>Our team checks the airport central lost inventory.</li>
                <li>Receive an alert under <strong>Notifications</strong> once matched.</li>
            </ul>
        </div>
    </div>

</div>


<h3 style="margin-bottom: 12px; font-size: 18px;">My Reported Items History</h3>

<table>
    <thead>
        <tr>
            <th>Report ID</th>
            <th>Item Name</th>
            <th>Estimated Location</th>
            <th>Description</th>
            <th>Reported Date</th>
            <th>Status</th>
        </tr>
    </thead>
    <tbody>
        <?php if ($items && $items->num_rows > 0): ?>
            <?php while ($r = $items->fetch_assoc()): 
                $st = strtolower($r['status']);
                $badge_class = 'badge-pending';
                if ($st === 'found') $badge_class = 'badge-found';
                if ($st === 'claimed') $badge_class = 'badge-claimed';
            ?>
                <tr>
                    <td>#LF-<?php echo $r['id']; ?></td>
                    <td><strong><?php echo htmlspecialchars($r['item_name']); ?></strong></td>
                    <td><?php echo htmlspecialchars($r['lost_location']); ?></td>
                    <td><?php echo htmlspecialchars($r['description'] ?: 'No additional notes'); ?></td>
                    <td><?php echo htmlspecialchars($r['reported_date']); ?></td>
                    <td><span class="badge <?php echo $badge_class; ?>"><?php echo htmlspecialchars($r['status']); ?></span></td>
                </tr>
            <?php endwhile; ?>
        <?php else: ?>
            <tr>
                <td colspan="6" style="text-align: center; color: #64748b; padding: 25px;">
                    You have not reported any lost items.
                </td>
            </tr>
        <?php endif; ?>
    </tbody>
</table>

<?php include 'footer.php'; ?>