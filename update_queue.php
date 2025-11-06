<?php
// update_queue.php
// Recalculates queue positions and estimated times.
// Can be included or called directly.
// Requires db_connect.php and session if used directly.

include 'db_connect.php';

// Average duration per trial in minutes (tweak as needed)
$AVG_MINUTES = 5;

/**
 * Recalculate queue for a specific shop.
 * @param mysqli $conn
 * @param string $shop_name
 */
function recalcQueueForShop($conn, $shop_name) {
    global $AVG_MINUTES;

    // Get shop info
    $shop_q = "SELECT total_rooms, occupied_rooms FROM shop_status WHERE shop_name = ?";
    $stmt = $conn->prepare($shop_q);
    $stmt->bind_param('s', $shop_name);
    $stmt->execute();
    $res = $stmt->get_result();
    if ($res->num_rows == 0) {
        return; // no shop config
    }
    $shop = $res->fetch_assoc();
    $T = (int)$shop['total_rooms'];
    $O = (int)$shop['occupied_rooms'];
    $available = $T - $O;
    if ($available < 0) $available = 0;

    // Fetch waiting queue for this shop ordered by creation time (FIFO)
    $q = "SELECT * FROM queue WHERE shop_name = ? AND status IN ('Waiting','Available') ORDER BY created_at ASC, id ASC";
    $stmt2 = $conn->prepare($q);
    $stmt2->bind_param('s', $shop_name);
    $stmt2->execute();
    $res2 = $stmt2->get_result();

    $index = 1;
    while ($row = $res2->fetch_assoc()) {
        $id = (int)$row['id'];
        // position in queue is the index
        $position = $index;

        if ($position <= $available && $available > 0) {
            // If there is capacity for this person now
            $status = 'Available';
            $estimated = '0 minutes';
        } else {
            // People ahead of this person who will be served before them
            // compute how many "rounds" of average duration until their turn,
            // using T parallel rooms.
            // number_ahead = position - available
            $numAhead = $position - $available;
            if ($numAhead < 1) $numAhead = 1;
            // rounds = ceil(numAhead / T)
            if ($T > 0) {
                $rounds = (int) ceil($numAhead / $T);
            } else {
                // No rooms at all, very large estimate
                $rounds = 9999;
            }
            $minutes = $rounds * $AVG_MINUTES;
            $status = 'Waiting';
            $estimated = $minutes . ' minutes';
        }

        // Update queue record
        $update = "UPDATE queue SET position_in_queue = ?, status = ?, estimated_time = ? WHERE id = ?";
        $u_stmt = $conn->prepare($update);
        $u_stmt->bind_param('issi', $position, $status, $estimated, $id);
        $u_stmt->execute();

        $index++;
    }

    $stmt2->close();
    $stmt->close();
}

// If called directly via GET with shop parameter, update that shop
if (php_sapi_name() !== 'cli' && isset($_GET['shop'])) {
    $shopName = $_GET['shop'];
    recalcQueueForShop($conn, $shopName);
    header("Location: admin_dashboard.php");
    exit();
}

// If called directly without shop, recalc for all shops
if (php_sapi_name() !== 'cli' && !isset($_GET['shop'])) {
    $shopList = $conn->query("SELECT shop_name FROM shop_status");
    while ($s = $shopList->fetch_assoc()) {
        recalcQueueForShop($conn, $s['shop_name']);
    }
    header("Location: admin_dashboard.php");
    exit();
}
