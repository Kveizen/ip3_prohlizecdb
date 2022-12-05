<html>
    <head>    
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css"
        integrity="sha384-BVYiiSIFeK1dGmJRAkycuHAHRg32OmUcww7on3RYdg4Va+PmSTsz/K68vbdEjh4u" crossorigin="anonymous">
    </head>
    <body class="container">

<?php
require_once "inc/db.inc.php";

$id = filter_input(INPUT_GET,
    'roomId',
    FILTER_VALIDATE_INT,
    ["options" => ["min_range"=> 1]]
);

if ($id === null || $id === false) {
    http_response_code(400);
    echo "400 Bad Request";
} else {
    $stmt = $pdo->prepare("SELECT * FROM room WHERE room_id=:roomId");
    $stmt->execute(['roomId' => $id]);
    $stmt2 = $pdo->prepare("SELECT employee.name, employee.surname, employee.wage, employee.employee_id FROM employee INNER JOIN room ON room.room_id =:roomId AND room.room_id = employee.room ");
    $stmt2->execute(['roomId' => $id]);

    $stmt3 = $pdo->prepare("SELECT employee.name, employee.surname, employee.employee_id FROM `key` klice JOIN employee ON klice.employee = employee.employee_id WHERE klice.room =:roomId ORDER BY employee.surname; ");
    $stmt3->execute(['roomId' => $id]);

    if ($stmt->rowCount() === 0) {
        http_response_code(404);
        echo "404 Not Found";
        die();
    } else {
        $r = $stmt->fetch();

        echo "<h2>Místnost č. " . $r->no . "</h2>";

        echo "<table class='table table-hover'>";
    
        echo "<tbody>";
        echo "<tr><th>Číslo</th>" . "<td>" . $r->no . "</td>" . "</tr>";
        echo "<tr><th>Název</th>" . "<td>" . $r->name . "</td>" . "</tr>";
        echo "<tr><th>Telefon</th>" . "<td>" . ($r->phone ?: "&mdash;&mdash;") . "</td>" . "</tr>";

        if($stmt2 -> rowCount() === 0){
            echo "<tr><th>Lidé</th>";
            echo "<td>";
            echo("-");
            echo "<tr><th>Průměrná mzda</th>";
            echo "<td>";
            echo("-");
        }
        else{
            echo "<tr><th>Lidé</th>";
            echo "<td>";
            $totalWage = 0;
            foreach ($stmt2 as $r) {
                $shortName = mb_substr($r -> name,0,1);
                echo "<a href='employee.php?employeeId={$r->employee_id}'>{$r->surname} {$shortName}.</a><br>";
                $totalWage += $r -> wage;
            }
            $employeeCount = $stmt2->rowCount();
            $averageWage = $totalWage / $employeeCount;
    
            
            echo "</td></tr>";
            echo "<tr><th>Průměrná mzda</th>" . "<td>" . number_format($averageWage, $decimals = 2, $decimal_separator = ".", $thousands_separator = ","
            ) . "</td>" . "</tr>";
        }
        }
        if($stmt3 -> rowCount() === 0){
            echo("<dt>Klíče</dt><dd>—</dd>");
        }
        else{
            echo "<tr><th>Klíče</th>";
            echo "<td>";
            while($r = $stmt3->fetch()){
                $shortName = mb_substr($r -> name,0,1);
                echo("<dd><a href='employee.php?employeeId={$r->employee_id}'>{$r->surname} {$shortName}.</a></dd>");
            }
        }
        }
        echo "</td></tr>";
        echo "</tbody>";
    
        echo "<table class='table table-hover'><tfoot><tr><td><a href='rooms.php'>🢀 Zpět na seznam místností</a></tr></td></tfoot></table>";
?>
</body>
</html>
