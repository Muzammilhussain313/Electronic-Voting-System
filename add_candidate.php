<?php
require_once 'db_connection.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $party = mysqli_real_escape_string($conn, $_POST['party']);
    $symbol = mysqli_real_escape_string($conn, $_POST['symbol']);
    
    $insert_query = "INSERT INTO candidates (name, party, symbol) VALUES ('$name', '$party', '$symbol')";
    if (mysqli_query($conn, $insert_query)) {
        $message = "✅ Candidate added successfully to Candidates Set C!";
        $message_type = "success";
    } else {
        $message = "Error: " . mysqli_error($conn);
        $message_type = "danger";
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Candidate - E-Voting System</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">🗳️ <span>E-Voting System</span></a>
            <ul class="nav-links">
                <li><a href="index.php">🏠 Home</a></li>
                <li><a href="register_voter.php">📝 Register Voter</a></li>
                <li><a href="add_candidate.php">👥 Add Candidate</a></li>
                <li><a href="vote.php">🗳️ Cast Vote</a></li>
                <li><a href="results.php">📊 Results</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <div class="card-header">
                👥 Add New Candidate - Adding Element to Candidates Set C
            </div>
            <div class="card-body">
                <?php if($message): ?>
                    <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
                <?php endif; ?>
                
                <form method="POST">
                    <div class="form-group">
                        <label>Candidate Name</label>
                        <input type="text" name="name" required placeholder="Full name of candidate">
                    </div>
                    
                    <div class="form-group">
                        <label>Political Party</label>
                        <input type="text" name="party" required placeholder="Party name">
                    </div>
                    
                    <div class="form-group">
                        <label>Election Symbol (Emoji or text)</label>
                        <input type="text" name="symbol" placeholder="e.g., 🌟, 🦁, ✋, ⭐">
                    </div>
                    
                    <button type="submit" class="btn btn-primary">➕ Add Candidate (Add to Set C)</button>
                </form>
                
                <div class="ds-info">
                    <h4>🧮 Discrete Structures Concept Applied:</h4>
                    <p><strong>Set Theory:</strong> Candidates Set C = {c₁, c₂, c₃, ...}. Each candidate is an element with attributes (name, party, symbol).</p>
                    <code>C = { (name₁, party₁, symbol₁), (name₂, party₂, symbol₂), ... }</code>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                📋 Current Candidates (Set C)
            </div>
            <div class="card-body">
                <table class="results-table">
                    <thead>
                        <tr>
                            <th>Symbol</th>
                            <th>Name</th>
                            <th>Party</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = mysqli_query($conn, "SELECT c.*, COUNT(v.vote_id) as votes FROM candidates c LEFT JOIN votes v ON c.candidate_id = v.candidate_id GROUP BY c.candidate_id");
                        while($row = mysqli_fetch_assoc($result)):
                        ?>
                        <tr>
                            <td style="font-size: 1.5rem;"><?php echo htmlspecialchars($row['symbol']); ?></td>
                            <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['party']); ?></td>
                            <td><?php echo $row['votes']; ?> vote(s)</td>
                        </tr>
                        <?php endwhile; ?>
                        <?php if(mysqli_num_rows($result) == 0): ?>
                        <tr>
                            <td colspan="4" style="text-align: center;">No candidates added yet</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>