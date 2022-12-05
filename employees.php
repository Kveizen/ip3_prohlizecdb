<!DOCTYPE html>

<html>
<head>
    <meta charset="UTF-8">
    <!-- Bootstrap-->
    <!-- Latest compiled and minified CSS -->
    <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css" integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    <title>Připojení k DB</title>
</head>
<body class="container">
<?php

require_once "inc/db.inc.php";

$sortBy = filter_input(INPUT_GET, "sortBy");
$stmtStr = "";

function SortFunct($orderRow, $orderBy, $pdo) {
    return $pdo->query('SELECT employee.surname, employee.name, employee.job, employee.room, employee.employee_id, room.room_id, room.phone, room.name AS roomName, room.no 
    FROM employee 
    INNER JOIN room ON employee.room=room.room_id 
    ORDER BY ' . ' ' . $orderRow . ' ' . $orderBy);
}

switch($sortBy){
    case "name_asc": $stmt = SortFunct("name", "ASC", $pdo); break;
    case "name_desc": $stmt = SortFunct("name", "DESC", $pdo); break;
    case "num_asc": $stmt = SortFunct("no", "ASC", $pdo); break;
    case "num_desc": $stmt = SortFunct("no", "DESC", $pdo); break;
    case "phone_asc": $stmt = SortFunct("phone", "ASC", $pdo); break;
    case "phone_desc": $stmt = SortFunct("phone", "DESC", $pdo); break;
    default: $stmt = SortFunct("name", "ASC", $pdo); break;
}

if ($stmt->rowCount() == 0) {
    echo "Záznam neobsahuje žádná data";
} else {
    echo "<table class='table table-striped'>";
    echo "<tr>";
    echo "<tr>";
    echo "<th>Název<a href='?sortBy=name_asc'><span class='glyphicon glyphicon-arrow-down' aria-hidden='true'></span></a>
    <a href='?sortBy=name_desc'><span class='glyphicon glyphicon-arrow-up' aria-hidden='true'></span></a></th>";
    echo "<th>Číslo<a href='?sortBy=num_asc'><span class='glyphicon glyphicon-arrow-down' aria-hidden='true'></span></a>
    <a href='?sortBy=num_desc'><span class='glyphicon glyphicon-arrow-up' aria-hidden='true'></span></a></th>";
    echo "<th>Telefon<a href='?sortBy=phone_asc'><span class='glyphicon glyphicon-arrow-down' aria-hidden='true'></span></a>
    <a href='?sortBy=phone_desc'><span class='glyphicon glyphicon-arrow-up' aria-hidden='true'></span></a></th>";
    echo "</tr>";
    echo "</tr>";
    while ($row = $stmt->fetch()) {
        echo "<tr>";
        echo "<td><a href='employee.php?employeeId={$row->employee_id}'>{$row->name}</a></td><td>{$row->no}</td><td>{$row->phone}</td><td>{$row->job}</td>";
        echo "</tr>";
    }
    echo "</table>";
}
unset($stmt);
?>
</body>
</html>