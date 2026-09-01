<?php

/* =====================================================
   START SESSION
===================================================== */

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}


/* =====================================================
   CHECK LOGIN
===================================================== */

if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit();
}


/* =====================================================
   DATABASE
===================================================== */

include "../config/database.php";


/* =====================================================
   STAFF INFORMATION
===================================================== */

$user_id = $_SESSION['user_id'];

$user_sql = "
    SELECT name, role
    FROM users
    WHERE id = '$user_id'
";

$user_result = mysqli_query($conn, $user_sql);

$user = mysqli_fetch_assoc($user_result);

$staff_name = $user['name'] ?? 'Staff';
$staff_role = $user['role'] ?? 'Staff';

$staff_initials = strtoupper(
    substr($staff_name, 0, 2)
);


/* =====================================================
   UPDATE BAGGAGE STATUS
===================================================== */

if (
    $_SERVER['REQUEST_METHOD'] === 'POST' &&
    isset($_POST['update_baggage_status'])
) {

    $bag_id = (int)($_POST['bag_id'] ?? 0);

    $status = trim(
        $_POST['baggage_status'] ?? ''
    );


    /* Allowed baggage statuses */

    $allowed_statuses = [
        "Checked",
        "Loaded",
        "In Transit",
        "Arrived",
        "Delivered"
    ];


    /* Validate status */

    if (
        $bag_id <= 0 ||
        !in_array($status, $allowed_statuses, true)
    ) {

        header(
            'Content-Type: application/json'
        );

        echo json_encode([
            "success" => false,
            "message" => "Invalid baggage information."
        ]);

        exit();
    }


    /* Update database */

    $update_sql = "
        UPDATE baggage
        SET baggage_status = ?
        WHERE id = ?
    ";


    $stmt = mysqli_prepare(
        $conn,
        $update_sql
    );


    if (!$stmt) {

        header(
            'Content-Type: application/json'
        );

        echo json_encode([
            "success" => false,
            "message" => "Database error."
        ]);

        exit();
    }


    mysqli_stmt_bind_param(
        $stmt,
        "si",
        $status,
        $bag_id
    );


    $success =
        mysqli_stmt_execute($stmt);


    mysqli_stmt_close($stmt);


    header(
        'Content-Type: application/json'
    );


    echo json_encode([
        "success" => $success,
        "status" => $status
    ]);

    exit();
}


/* =====================================================
   GET BAGGAGE STATUS CSS CLASS
===================================================== */

function getBaggageStatusClass($status)
{
    switch (strtolower(trim($status))) {

        case "checked":
        case "checked in":
            return "status-checked";

        case "loaded":
            return "status-loaded";

        case "in transit":
            return "status-transit";

        case "arrived":
            return "status-arrived";

        case "delivered":
            return "status-delivered";

        default:
            return "status-checked";
    }
}


/* =====================================================
   GET BAGGAGE DATA
===================================================== */

/*
   IMPORTANT:
   There is NO b.weight here because your
   baggage table does not have a weight column.
*/

$baggage_sql = "

    SELECT

        b.id,

        CONCAT(
            COALESCE(f.departure, 'DAC'),
            '-',
            LPAD(
                2083140 + b.id * 105,
                7,
                '0'
            )
        ) AS baggage_tag,


        COALESCE(
            u.name,
            'Passenger'
        ) AS passenger_name,


        COALESCE(
            f.flight_number,
            'BG-401'
        ) AS flight_number,


        COALESCE(
            f.departure,
            'DAC'
        ) AS departure,


        COALESCE(
            f.destination,
            'DXB'
        ) AS destination,


        COALESCE(
            b.baggage_status,
            'Checked'
        ) AS baggage_status


    FROM baggage b


    LEFT JOIN users u
        ON b.user_id = u.id


    LEFT JOIN bookings bk
        ON b.booking_id = bk.id


    LEFT JOIN flights f
        ON bk.flight_id = f.id


    ORDER BY b.id ASC

";


$baggage_result =
    mysqli_query(
        $conn,
        $baggage_sql
    );


/* =====================================================
   STORE DATA
===================================================== */

$bags = [];

$checked_count = 0;
$loaded_count = 0;
$transit_count = 0;
$arrived_count = 0;
$delivered_count = 0;


if ($baggage_result) {

    while (
        $bag = mysqli_fetch_assoc(
            $baggage_result
        )
    ) {

        $status =
            trim($bag['baggage_status']);


        /*
         * Convert old "Checked In"
         * records to "Checked" for display.
         */

        if ($status === "Checked In") {

            $status = "Checked";

            $bag['baggage_status'] =
                "Checked";
        }


        $bags[] = $bag;


        /* =================================================
           COUNT BAGGAGE STATUS
        ================================================= */

        if ($status === "Checked") {

            $checked_count++;

        }
        elseif ($status === "Loaded") {

            $loaded_count++;

        }
        elseif ($status === "In Transit") {

            $transit_count++;

        }
        elseif ($status === "Arrived") {

            $arrived_count++;

        }
        elseif ($status === "Delivered") {

            $delivered_count++;

        }

    }

}


$total_count =
    count($bags);

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        Baggage Handling - AeroPort
    </title>


    <link
        rel="stylesheet"
        href="../assets/css/dashboard.css"
    >

</head>


<body class="light-mode">


<div class="app-layout">


    <!-- =====================================================
         SIDEBAR
    ====================================================== -->

    <aside class="sidebar">


        <div class="sidebar-top">


            <!-- LOGO -->

            <div class="logo">

                <div class="logo-icon">
                    ✈
                </div>


                <div>

                    <h2>
                        AeroPort
                    </h2>

                    <p>
                        Management System
                    </p>

                </div>

            </div>


            <!-- PROFILE -->

            <div class="profile">

                <div class="avatar">

                    <?= htmlspecialchars(
                        $staff_initials
                    ); ?>

                </div>


                <div>

                    <h3>

                        <?= htmlspecialchars(
                            $staff_name
                        ); ?>

                    </h3>


                    <span>

                        <?= htmlspecialchars(
                            $staff_role
                        ); ?>

                    </span>

                </div>

            </div>


            <!-- MENU TITLE -->

            <div class="title">
                STAFF OPERATIONS
            </div>


            <!-- NAVIGATION -->

            <nav>


                <a
                    href="dashboard.php"
                    class="menu"
                >
                    ▦ Dashboard
                </a>


                <a
                    href="flight_schedule.php"
                    class="menu"
                >
                    📅 Flight Schedules
                </a>


                <a
                    href="gate_assignment.php"
                    class="menu"
                >
                    🛫 Gate & Terminal
                </a>


                <a
                    href="baggage_status.php"
                    class="menu active"
                >
                    🧳 Baggage Handling
                </a>


            </nav>


        </div>


        <!-- =====================================================
             SIDEBAR BOTTOM
        ====================================================== -->

        <div class="sidebar-bottom">


            <p id="themeToggle">

                <span id="themeIcon">
                    🌙
                </span>


                <span id="themeText">
                    Dark Mode
                </span>

            </p>


            <p>

                <a
                    href="../logout.php"
                    class="sign-out"
                >
                    ↪ Sign Out
                </a>

            </p>


        </div>


    </aside>



    <!-- =====================================================
         MAIN CONTENT
    ====================================================== -->

    <main class="main">


        <!-- PAGE TITLE -->

        <div class="page-top-bar">


            <div>

                <h1>
                    Baggage Handling & Status
                </h1>


                <p class="sub">
                    Track and update baggage movement status
                </p>

            </div>


        </div>



        <!-- =====================================================
             STATISTICS
        ====================================================== -->

        <section class="baggage-stat-cards">


            <!-- CHECKED -->

            <div class="stat-card">

                <h4>
                    Checked
                </h4>


                <h2 class="text-blue">

                    <?= $checked_count; ?>

                </h2>

            </div>


            <!-- LOADED -->

            <div class="stat-card">

                <h4>
                    Loaded
                </h4>


                <h2 class="text-blue">

                    <?= $loaded_count; ?>

                </h2>

            </div>


            <!-- IN TRANSIT -->

            <div class="stat-card">

                <h4>
                    In Transit
                </h4>


                <h2 class="text-blue">

                    <?= $transit_count; ?>

                </h2>

            </div>


            <!-- ARRIVED -->

            <div class="stat-card">

                <h4>
                    Arrived
                </h4>


                <h2 class="text-blue">

                    <?= $arrived_count; ?>

                </h2>

            </div>


            <!-- DELIVERED -->

            <div class="stat-card">

                <h4>
                    Delivered
                </h4>


                <h2 class="text-blue">

                    <?= $delivered_count; ?>

                </h2>

            </div>


        </section>



        <!-- =====================================================
             SEARCH
        ====================================================== -->

        <div class="search-filter-container">


            <div class="search-input-wrapper">


                <span class="search-icon">
                    🔍
                </span>


                <input
                    type="text"
                    id="baggageSearch"
                    placeholder="Search by baggage tag, passenger name, or flight..."
                >


            </div>


            <div class="items-count-badge">

                <span id="itemsCount">

                    <?= $total_count; ?>

                </span>

                items

            </div>


        </div>



        <!-- =====================================================
             BAGGAGE TABLE
        ====================================================== -->

        <div class="baggage-table-card">


            <table class="baggage-table">


                <thead>

                    <tr>

                        <th>
                            BAGGAGE TAG
                        </th>


                        <th>
                            PASSENGER
                        </th>


                        <th>
                            FLIGHT
                        </th>


                        <th>
                            ROUTE
                        </th>


                        <th>
                            STATUS
                        </th>


                        <th>
                            ACTION
                        </th>

                    </tr>

                </thead>


                <tbody
                    id="baggageTableBody"
                >


                <?php if ($total_count > 0): ?>


                    <?php foreach ($bags as $bag): ?>


                        <?php

                        $badge_class =
                            getBaggageStatusClass(
                                $bag['baggage_status']
                            );


                        $tag_parts =
                            explode(
                                '-',
                                $bag['baggage_tag']
                            );

                        ?>


                        <tr
                            class="baggage-row"
                            data-id="<?= $bag['id']; ?>"
                        >


                            <!-- BAGGAGE TAG -->

                            <td>

                                <div class="baggage-tag-box">


                                    <span>

                                        <?= htmlspecialchars(
                                            $tag_parts[0]
                                        ); ?>–

                                    </span>


                                    <span>

                                        <?= htmlspecialchars(
                                            $tag_parts[1] ?? ''
                                        ); ?>

                                    </span>


                                </div>

                            </td>



                            <!-- PASSENGER -->

                            <td
                                class="cell-passenger"
                            >

                                <strong>

                                    <?= htmlspecialchars(
                                        $bag['passenger_name']
                                    ); ?>

                                </strong>

                            </td>



                            <!-- FLIGHT -->

                            <td
                                class="cell-flight"
                            >

                                <strong>

                                    <?= htmlspecialchars(
                                        $bag['flight_number']
                                    ); ?>

                                </strong>

                            </td>



                            <!-- ROUTE -->

                            <td
                                class="cell-route"
                            >

                                <?= htmlspecialchars(
                                    $bag['departure']
                                ); ?>


                                →


                                <?= htmlspecialchars(
                                    $bag['destination']
                                ); ?>


                            </td>



                            <!-- STATUS -->

                            <td>


                                <span
                                    class="baggage-badge <?= $badge_class; ?>"
                                >

                                    •

                                    <?= htmlspecialchars(
                                        $bag['baggage_status']
                                    ); ?>


                                </span>


                            </td>



                            <!-- ACTION -->

                            <td>


                                <button
                                    type="button"
                                    class="btn-edit-baggage"
                                    onclick="openBaggageEditModal(
                                        <?= $bag['id']; ?>,
                                        '<?= htmlspecialchars(
                                            $bag['baggage_status'],
                                            ENT_QUOTES
                                        ); ?>'
                                    )"
                                >

                                    Edit

                                </button>


                            </td>


                        </tr>


                    <?php endforeach; ?>


                <?php else: ?>


                    <tr>


                        <td
                            colspan="6"
                            style="
                                text-align:center;
                                padding:30px;
                            "
                        >

                            No baggage records found.


                        </td>


                    </tr>


                <?php endif; ?>


                </tbody>


            </table>


        </div>


    </main>


</div>



<!-- =====================================================
     HELP BUTTON
===================================================== -->

<div class="help-btn">
    ?
</div>



<!-- =====================================================
     EDIT BAGGAGE STATUS MODAL
===================================================== -->

<div
    class="modal-backdrop"
    id="baggageEditModal"
>


    <div class="modal-card">


        <!-- MODAL HEADER -->

        <div class="modal-header">


            <div>

                <h2>
                    Edit Baggage Status
                </h2>


                <p>
                    Update the current baggage status
                </p>

            </div>


            <button
                type="button"
                class="modal-close"
                onclick="closeBaggageEditModal()"
            >

                ×

            </button>


        </div>



        <!-- FORM -->

        <form
            id="baggageStatusForm"
        >


            <!-- BAGGAGE ID -->

            <input
                type="hidden"
                id="editBaggageId"
                name="bag_id"
            >



            <!-- STATUS -->

            <div class="modal-group">


                <label for="editBaggageStatus">

                    Status

                </label>


                <select
                    id="editBaggageStatus"
                    name="baggage_status"
                    required
                >


                    <option value="Checked">
                        Checked
                    </option>


                    <option value="Loaded">
                        Loaded
                    </option>


                    <option value="In Transit">
                        In Transit
                    </option>


                    <option value="Arrived">
                        Arrived
                    </option>


                    <option value="Delivered">
                        Delivered
                    </option>


                </select>


            </div>



            <!-- MODAL BUTTONS -->

            <div class="modal-actions">


                <button
                    type="button"
                    class="btn-cancel"
                    onclick="closeBaggageEditModal()"
                >

                    Cancel

                </button>


                <button
                    type="submit"
                    class="btn-save"
                >

                    Save

                </button>


            </div>


        </form>


    </div>


</div>

```html
<div class="modal-backdrop" id="baggageEditModal">
    <div class="modal-card">

        <div class="modal-header">
            <div>
                <h2>Edit Baggage Status</h2>
                <p>Update the current baggage status</p>
            </div>

            <button
                type="button"
                class="modal-close"
                onclick="closeBaggageEditModal()"
            >
                ×
            </button>
        </div>

        <form id="baggageStatusForm">

            <input
                type="hidden"
                id="editBaggageId"
                name="bag_id"
            >

            <div class="modal-group">
                <label for="editBaggageStatus">Status</label>

                <select
                    id="editBaggageStatus"
                    name="baggage_status"
                    required
                >
                    <option value="Checked">Checked</option>
                    <option value="Loaded">Loaded</option>
                    <option value="In Transit">In Transit</option>
                    <option value="Arrived">Arrived</option>
                    <option value="Delivered">Delivered</option>
                </select>
            </div>

            <div class="modal-actions">

                <button
                    type="button"
                    class="btn-cancel"
                    onclick="closeBaggageEditModal()"
                >
                    Cancel
                </button>

                <button
                    type="submit"
                    class="btn-save"
                >
                    Save
                </button>

            </div>

        </form>

    </div>
</div>
```
```php
<button
    type="button"
    class="btn-edit-baggage"
    onclick="openBaggageEditModal(
        <?= $bag['id']; ?>,
        '<?= htmlspecialchars($bag['baggage_status'], ENT_QUOTES); ?>'
    )"
>
    Edit
</button>
```




<!-- =====================================================
     DASHBOARD JS
===================================================== -->

<script src="../assets/js/dashboard.js"></script>


</body>

</html>