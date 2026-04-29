<?php
require_once 'db_connection.php';

$message = '';
$message_type = '';

// Process vote submission
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['cast_vote'])) {
    $voter_id = $_SESSION['voter_id'];
    $candidate_id = mysqli_real_escape_string($conn, $_POST['candidate_id']);
    
    // Check if voter has already voted (Logic check)
    $check_vote = mysqli_query($conn, "SELECT * FROM votes WHERE voter_id = $voter_id");
    if (mysqli_num_rows($check_vote) == 0) {
        $insert_vote = "INSERT INTO votes (voter_id, candidate_id) VALUES ($voter_id, $candidate_id)";
        $update_voter = "UPDATE voters SET has_voted = TRUE WHERE voter_id = $voter_id";
        
        if (mysqli_query($conn, $insert_vote) && mysqli_query($conn, $update_voter)) {
            $message = "✅ Vote cast successfully! Thank you for voting.";
            $message_type = "success";
            // Clear session
            session_destroy();
            session_start();
        } else {
            $message = "❌ Error casting vote: " . mysqli_error($conn);
            $message_type = "danger";
        }
    } else {
        $message = "❌ You have already voted! (Each voter can vote only once)";
        $message_type = "danger";
    }
}

// Check if voter is logged in (session management)
if (!isset($_SESSION['voter_id'])) {
    // Show voter login form
    if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['voter_login'])) {
        $voter_id_number = mysqli_real_escape_string($conn, $_POST['voter_id_number']);
        $query = "SELECT * FROM voters WHERE voter_id_number = '$voter_id_number'";
        $result = mysqli_query($conn, $query);
        
        if (mysqli_num_rows($result) == 1) {
            $voter = mysqli_fetch_assoc($result);
            if ($voter['has_voted'] == 0) {
                $_SESSION['voter_id'] = $voter['voter_id'];
                $_SESSION['voter_name'] = $voter['name'];
                header("Location: vote.php");
                exit();
            } else {
                $message = "❌ You have already voted! (Each voter can vote only once - Function property)";
                $message_type = "danger";
            }
        } else {
            $message = "❌ Invalid Voter ID! (Element not found in Voters Set V)";
            $message_type = "danger";
        }
    }
    
    // Show login form
    ?>
    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Vote - E-Voting System</title>
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
                    🔐 Voter Authentication - Verify Element in Set V
                </div>
                <div class="card-body">
                    <?php if($message): ?>
                        <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
                    <?php endif; ?>
                    
                    <form method="POST">
                        <div class="form-group">
                            <label>Enter your Voter ID Number</label>
                            <input type="text" name="voter_id_number" required placeholder="e.g., VOT1001">
                            <small>Use the Voter ID you registered with</small>
                        </div>
                        <button type="submit" name="voter_login" class="btn btn-primary">🔓 Verify & Continue</button>
                    </form>
                    
                    <div class="ds-info">
                        <h4>🧮 Discrete Structures Concept Applied:</h4>
                        <p><strong>Set Membership & Logic:</strong> Verify if voter ∈ V (Voters Set) AND has_voted = FALSE</p>
                        <code>Propositional logic: p ∧ q where p = "voter exists", q = "has not voted"</code>
                        <p>If p ∧ q = TRUE → Voting allowed</p>
                    </div>
                </div>
            </div>
        </div>
        <script src="script.js"></script>
    </body>
    </html>
    <?php
    exit();
}

// Get candidates for voting
$candidates_query = "SELECT * FROM candidates";
$candidates_result = mysqli_query($conn, $candidates_query);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cast Vote - E-Voting System</title>
    <link rel="stylesheet" href="style.css">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
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
        <?php if($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>"><?php echo $message; ?></div>
        <?php endif; ?>
        
        <div class="card">
            <div class="card-header">
                🗳️ Cast Your Vote - Function f: V → C
            </div>
            <div class="card-body">
                <h3>Welcome, <?php echo htmlspecialchars($_SESSION['voter_name']); ?>!</h3>
                <p>Please select one candidate to vote for:</p>
                
                <form method="POST" onsubmit="return confirmVote()">
                    <input type="hidden" name="candidate_id" id="candidate_id">
                    
                    <div class="candidates-grid">
                        <?php while($candidate = mysqli_fetch_assoc($candidates_result)): ?>
                        <div class="candidate-card" onclick="selectCandidate(<?php echo $candidate['candidate_id']; ?>, this)">
                            <div class="candidate-symbol"><?php echo htmlspecialchars($candidate['symbol']); ?></div>
                            <div class="candidate-name"><?php echo htmlspecialchars($candidate['name']); ?></div>
                            <div class="candidate-party"><?php echo htmlspecialchars($candidate['party']); ?></div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    
                    <div style="text-align: center; margin-top: 2rem;">
                        <button type="submit" name="cast_vote" class="btn btn-success" id="voteBtn" disabled>✅ Confirm Vote</button>
                        <a href="logout.php" class="btn btn-danger">🚪 Cancel & Logout</a>
                    </div>
                </form>
                
                <div class="ds-info">
                    <h4>🧮 Discrete Structures Concept Applied:</h4>
                    <p><strong>Function:</strong> f: V → C where V is the set of voters and C is the set of candidates.</p>
                    <code>Each voter v ∈ V maps to exactly one candidate c ∈ C</code>
                    <p><strong>Property:</strong> This is a well-defined function (not one-to-one, but onto the set of voted candidates).</p>
                    <p><strong>Graph Theory:</strong> Each vote creates a directed edge from voter node to candidate node.</p>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>