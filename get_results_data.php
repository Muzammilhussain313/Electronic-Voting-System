<?php
header('Content-Type: application/json');
require_once 'db_connection.php';

$query = "
    SELECT c.candidate_id, c.name, c.party, c.symbol, COUNT(v.vote_id) as votes 
    FROM candidates c 
    LEFT JOIN votes v ON c.candidate_id = v.candidate_id 
    GROUP BY c.candidate_id 
    ORDER BY votes DESC
";

$result = mysqli_query($conn, $query);
$results = [];

while($row = mysqli_fetch_assoc($result)) {
    $results[] = [
        'candidate_id' => $row['candidate_id'],
        'name' => $row['name'],
        'party' => $row['party'],
        'symbol' => $row['symbol'],
        'votes' => (int)$row['votes']
    ];
}

echo json_encode([
    'success' => true,
    'results' => $results
]);
?>