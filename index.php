<?php
require_once 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Electronic Voting System | Discrete Structures Project</title>
    <link rel="stylesheet" href="style.css">
</head>
<body>
    <nav class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">
                🗳️ <span>E-Voting System</span>
            </a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="register_voter.php">Register Voter</a></li>
                <li><a href="add_candidate.php">Add Candidate</a></li>
                <li><a href="vote.php">Cast Vote</a></li>
                <li><a href="results.php">Results</a></li>
            </ul>
        </div>
    </nav>

    <div class="container">
        <div class="card">
            <div class="card-header">
                🎓 Electronic Voting System - Discrete Structures Project
            </div>
            <div class="card-body">
                <h2>Welcome to the Advanced Voting System</h2>
                <p style="margin-top: 1rem; line-height: 1.6;">
                    This system demonstrates various concepts of <strong>Discrete Structures</strong> including:
                </p>
                
                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem; margin: 2rem 0;">
                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px;">
                        <h3>📌 Sets</h3>
                        <p>Set of Voters, Set of Candidates, Set of Casted Votes</p>
                        <code>V = {v₁, v₂, v₃, ...}<br>C = {c₁, c₂, c₃, ...}</code>
                    </div>
                    
                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px;">
                        <h3>🔗 Relations & Functions</h3>
                        <p>Voting Relation R ⊆ V × C<br>One-to-One Function: f(v) → c</p>
                    </div>
                    
                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px;">
                        <h3>📊 Graph Theory</h3>
                        <p>Directed Graph G(V, E) where edges represent votes from voters to candidates</p>
                    </div>
                    
                    <div style="background: #f8f9fa; padding: 1rem; border-radius: 8px;">
                        <h3>🧮 Logic</h3>
                        <p>Propositional logic for eligibility checking<br>p: voter exists ∧ q: has not voted → can vote</p>
                    </div>
                </div>
                
                <div style="text-align: center; margin-top: 2rem;">
                    <a href="register_voter.php" class="btn btn-primary">Get Started → Register as Voter</a>
                    <a href="results.php" class="btn btn-success" style="margin-left: 1rem;">View Live Results</a>
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                📈 System Statistics
            </div>
            <div class="card-body">
                <?php
                // Get statistics using SQL queries (Set operations)
                $total_voters = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM voters"))['count'];
                $total_candidates = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM candidates"))['count'];
                $total_votes = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM votes"))['count'];
                $turnout = $total_voters > 0 ? round(($total_votes / $total_voters) * 100, 2) : 0;
                ?>
                
                <div style="display: grid; grid-template-columns: repeat(4, 1fr); gap: 1rem; text-align: center;">
                    <div>
                        <h3><?php echo $total_voters; ?></h3>
                        <p>Total Voters</p>
                    </div>
                    <div>
                        <h3><?php echo $total_candidates; ?></h3>
                        <p>Candidates</p>
                    </div>
                    <div>
                        <h3><?php echo $total_votes; ?></h3>
                        <p>Votes Cast</p>
                    </div>
                    <div>
                        <h3><?php echo $turnout; ?>%</h3>
                        <p>Turnout Rate</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>