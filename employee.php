<html>
    <head>    
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
        integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    </head>
    <body class="container">

<?php
require_once "inc/db.inc.php";

$id = filter_input(INPUT_GET,
    'employeeId',
    FILTER_VALIDATE_INT,
    ["options" => ["min_range"=> 1]]
);

if ($id === null || $id === false) {
    http_response_code(400);
    echo "400 Bad Request";
} else {
    $stmt = $pdo->prepare("SELECT * FROM employee WHERE employee_id=:employeeID");
    $stmt->execute(['employeeID' => $id]);
    $stmt2 = $pdo->prepare("SELECT * FROM room WHERE room_id=:roomId");
    $stmt2->execute(['roomId' => $id]);

    $stmt3 = $pdo ->prepare("SELECT room.name, room.room_id FROM `key` klic JOIN room ON klic.room = room.room_id WHERE klic.employee =:employeeId ORDER BY room.name");
    $stmt3->execute(['employeeId' => $id]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo "404 Not Found";
        die();
    } else {
        $e = $stmt->fetch();
        $r = $stmt2->fetch();

        echo "<h2>Karta osoby: " . $e->name . ' ' . $e->surname . "</h2>";

        echo "<table class='table table-hover'>";
    
        echo "<tbody>";
        echo "<tr><th>Jméno</th>" . "<td>" . $e->name . "</td>" . "</tr>";
        echo "<tr><th>Příjmení</th>" . "<td>" . $e->surname . "</td>" . "</tr>";
        echo "<tr><th>Pozice</th>" . "<td>" . $e->job . "</td>" . "</tr>";
        echo "<tr><th>Plat</th>" . "<td>" . $e->wage . "</td>" . "</tr>";

        if($stmt2 -> rowCount() === 0){
            echo "<tr><th>Místnosti</th>";
            echo "<td>";
            echo("-");
        }
        else{
            echo "<tr><th>Místnost</th>" . "<td><a href='room.php?roomId={$r->room_id}'>{$r->name}</a></td>" . "</tr>";
        }
        
        }
        if($stmt3 -> rowCount() === 0){
            echo("<dt>Klíče</dt><dd>—</dd>");
        }
        else{
            echo "<tr><th>Klíče</th>";
            echo "<td>";
            while($r = $stmt3->fetch()){
                echo("<dd><a href='room.php?roomId={$r->room_id}'>{$r->name}</a></dd>");
            }
        }
        }
        echo "</td></tr>";
        echo "</tbody>";
    
        echo "<table class='table table-hover'><tfoot><tr><td><a href='employees.php'>🢀 Zpět na seznam zaměstnanců</a></tr></td></tfoot></table>";
?>
</body>
</html>
