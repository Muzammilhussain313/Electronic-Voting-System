<?php
require_once 'db_connection.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Election Results - E-Voting System</title>
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
        <div class="card">
            <div class="card-header">
                📊 Election Results - Voting Graph Analysis
            </div>
            <div class="card-body">
                <?php
                // Get results with vote counts
                $results_query = "
                    SELECT c.*, COUNT(v.vote_id) as votes 
                    FROM candidates c 
                    LEFT JOIN votes v ON c.candidate_id = v.candidate_id 
                    GROUP BY c.candidate_id 
                    ORDER BY votes DESC
                ";
                $results = mysqli_query($conn, $results_query);
                
                $total_votes = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as total FROM votes"))['total'];
                ?>
                
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-number"><?php echo $total_votes; ?></div>
                        <div class="stat-label">Total Votes Cast</div>
                        <small>|E| = Number of edges in voting graph</small>
                    </div>
                    <div class="stat-card">
                        <div class="stat-number"><?php echo mysqli_num_rows($results); ?></div>
                        <div class="stat-label">Total Candidates</div>
                        <small>|C| = Cardinality of Candidates Set</small>
                    </div>
                </div>
                
                <table class="results-table">
                    <thead>
                        <tr>
                            <th>Symbol</th>
                            <th>Candidate Name</th>
                            <th>Party</th>
                            <th>Votes Received</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody id="results-tbody">
                        <?php 
                        $max_votes = 0;
                        $winner = null;
                        $results_data = [];
                        
                        // First pass to find winner
                        mysqli_data_seek($results, 0);
                        while($row = mysqli_fetch_assoc($results)) {
                            if($row['votes'] > $max_votes) {
                                $max_votes = $row['votes'];
                                $winner = $row;
                            }
                            $results_data[] = $row;
                        }
                        
                        // Display results
                        foreach($results_data as $row):
                        ?>
                        <tr>
                            <td style="font-size: 1.5rem;"><?php echo htmlspecialchars($row['symbol']); ?></td>
                            <td><strong><?php echo htmlspecialchars($row['name']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['party']); ?></td>
                            <td>
                                <span style="font-size: 1.25rem; font-weight: bold; color: #4361ee;">
                                    <?php echo $row['votes']; ?>
                                </span>
                                <?php if($total_votes > 0): ?>
                                <small>(<?php echo round(($row['votes'] / $total_votes) * 100, 1); ?>%)</small>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if($winner && $row['candidate_id'] == $winner['candidate_id'] && $row['votes'] > 0): ?>
                                    <span class="winner-badge">🏆 WINNER</span>
                                <?php elseif($row['votes'] == 0): ?>
                                    <span style="color: #999;">No votes yet</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endforeach; ?>
                        <?php if(empty($results_data)): ?>
                        <tr>
                            <td colspan="5" style="text-align: center;">No candidates available</td>
                        </tr>
                        <?php endif; ?>
                    </tbody>
                </table>
                
                <?php if($winner): ?>
                <div class="alert alert-success" style="margin-top: 1.5rem;">
                    <strong>🏆 WINNER ANNOUNCEMENT:</strong> 
                    <?php echo htmlspecialchars($winner['name']); ?> from 
                    <?php echo htmlspecialchars($winner['party']); ?> wins with 
                    <?php echo $winner['votes']; ?> votes!
                </div>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                📈 Voting Graph Visualization
            </div>
            <div class="card-body">
                <div class="chart-container">
                    <canvas id="voteChart"></canvas>
                </div>
                
                <div id="set-info" class="ds-info">
                    <!-- Set information will be dynamically updated by JavaScript -->
                </div>
            </div>
        </div>
        
        <div class="card">
            <div class="card-header">
                🧮 Discrete Structures Analysis
            </div>
            <div class="card-body">
                <div class="ds-info">
                    <h4>📌 Set Theory Analysis</h4>
                    <?php
                    $voters_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM voters"))['count'];
                    $voted_count = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM votes"))['count'];
                    $non_voters = $voters_count - $voted_count;
                    ?>
                    <ul>
                        <li><strong>Universal Set U:</strong> All registered voters (|V| = <?php echo $voters_count; ?>)</li>
                        <li><strong>Set of Voters who voted:</strong> V_voted ⊆ V (|V_voted| = <?php echo $voted_count; ?>)</li>
                        <li><strong>Set of Non-voters:</strong> V \ V_voted (|V_non| = <?php echo $non_voters; ?>)</li>
                        <li><strong>Set of Candidates:</strong> C (|C| = <?php echo mysqli_num_rows($results); ?>)</li>
                    </ul>
                    
                    <h4 style="margin-top: 1rem;">⚡ Function Analysis</h4>
                    <ul>
                        <li><strong>Voting Function f:</strong> V_voted → C</li>
                        <li><strong>Domain of f:</strong> V_voted (<?php echo $voted_count; ?> voters)</li>
                        <li><strong>Codomain:</strong> C (All candidates)</li>
                        <li><strong>Range:</strong> Candidates who received at least one vote</li>
                        <li><strong>Property:</strong> f is a well-defined function (each voter maps to exactly one candidate)</li>
                    </ul>
                    
                    <h4 style="margin-top: 1rem;">📊 Graph Theory Analysis</h4>
                    <ul>
                        <li><strong>Directed Graph G = (V ∪ C, E)</strong></li>
                        <li><strong>Vertices:</strong> <?php echo $voters_count + mysqli_num_rows($results); ?> total nodes</li>
                        <li><strong>Edges:</strong> <?php echo $voted_count; ?> directed edges (votes)</li>
                        <li><strong>Out-degree of candidates:</strong> Number of votes received</li>
                        <li><strong>Winner:</strong> Candidate with maximum out-degree</li>
                    </ul>
                    
                    <h4 style="margin-top: 1rem;">🧠 Logic Analysis</h4>
                    <ul>
                        <li><strong>Voting Eligibility:</strong> p ∧ q → vote_allowed</li>
                        <li>Where p = "voter_id exists in database"</li>
                        <li>Where q = "has_voted = FALSE"</li>
                        <li><strong>Unique Vote Constraint:</strong> ∀v ∈ V, ¬(voted(v) ∧ voted_again(v))</li>
                    </ul>
                </div>
            </div>
        </div>
    </div>

    <script src="js/script.js"></script>
    <script>
        // Initialize chart with PHP data
        document.addEventListener('DOMContentLoaded', function() {
            const resultsData = <?php
                $chart_data = [];
                mysqli_data_seek($results, 0);
                while($row = mysqli_fetch_assoc($results)) {
                    $chart_data[] = [
                        'name' => $row['name'],
                        'party' => $row['party'],
                        'symbol' => $row['symbol'],
                        'votes' => (int)$row['votes']
                    ];
                }
                echo json_encode($chart_data);
            ?>;
            
            updateChart(resultsData);
            updateSetInformation(resultsData);
        });
    </script>
</body>
</html>