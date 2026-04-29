<?php
require_once 'db_connection.php';

$message = '';
$message_type = '';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $voter_id_number = mysqli_real_escape_string($conn, $_POST['voter_id_number']);
    $name = mysqli_real_escape_string($conn, $_POST['name']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    
    $check = mysqli_query($conn, "SELECT * FROM voters WHERE voter_id_number = '$voter_id_number' OR email = '$email'");
    
    if (mysqli_num_rows($check) > 0) {
        $message = "❌ Voter already exists!";
        $message_type = "danger";
    } else {
        mysqli_query($conn, "INSERT INTO voters (voter_id_number, name, email) VALUES ('$voter_id_number', '$name', '$email')");
        $message = "✅ Registered successfully!";
        $message_type = "success";
    }
}

$total = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM voters"))['c'];
$voted = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as c FROM voters WHERE has_voted=1"))['c'];
$not_voted = $total - $voted;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register Voter</title>
    <style>
        /* Simple inline CSS - No external files needed */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Arial, sans-serif;
            background: linear-gradient(135deg, #667eea, #764ba2);
            min-height: 100vh;
            padding: 20px;
        }
        
        /* Navigation */
        .navbar {
            background: white;
            padding: 15px 30px;
            border-radius: 12px;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0,0,0,0.1);
        }
        
        .nav-container {
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
        }
        
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #4361ee;
            text-decoration: none;
        }
        
        .nav-links {
            display: flex;
            gap: 20px;
            list-style: none;
        }
        
        .nav-links a {
            text-decoration: none;
            color: #333;
            padding: 8px 16px;
            border-radius: 8px;
            transition: 0.3s;
        }
        
        .nav-links a:hover {
            background: #4361ee;
            color: white;
        }
        
        /* Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
        }
        
        /* Two column layout */
        .two-columns {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
            margin-bottom: 30px;
        }
        
        @media (max-width: 768px) {
            .two-columns {
                grid-template-columns: 1fr;
            }
        }
        
        /* Cards */
        .card {
            background: white;
            border-radius: 16px;
            padding: 25px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
        }
        
        .card-title {
            font-size: 24px;
            font-weight: bold;
            color: #4361ee;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 3px solid #4361ee;
        }
        
        .card-title small {
            font-size: 12px;
            color: #666;
            display: block;
        }
        
        /* Form */
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            font-weight: 600;
            display: block;
            margin-bottom: 8px;
            color: #333;
        }
        
        input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            font-size: 16px;
            transition: 0.3s;
        }
        
        input:focus {
            border-color: #4361ee;
            outline: none;
        }
        
        .btn-register {
            background: linear-gradient(135deg, #4361ee, #7209b7);
            color: white;
            padding: 14px 25px;
            border: none;
            border-radius: 50px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            width: 100%;
            transition: 0.3s;
        }
        
        .btn-register:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(67,97,238,0.3);
        }
        
        /* Stats */
        .stat-box {
            background: white;
            border-radius: 16px;
            padding: 25px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 20px;
        }
        
        .stat-number {
            font-size: 48px;
            font-weight: bold;
            color: #4361ee;
        }
        
        .stat-label {
            color: #666;
            margin-top: 10px;
        }
        
        .stat-row {
            display: flex;
            justify-content: space-around;
            margin-top: 20px;
            padding-top: 20px;
            border-top: 1px solid #e0e0e0;
        }
        
        /* Table */
        .table-card {
            background: white;
            border-radius: 16px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }
        
        .table-header {
            background: linear-gradient(135deg, #4361ee, #7209b7);
            color: white;
            padding: 20px;
            font-size: 20px;
            font-weight: bold;
        }
        
        .table-body {
            padding: 20px;
            overflow-x: auto;
        }
        
        table {
            width: 100%;
            border-collapse: collapse;
        }
        
        th {
            background: #f8f9fa;
            padding: 12px;
            text-align: left;
            font-weight: bold;
        }
        
        td {
            padding: 12px;
            border-bottom: 1px solid #e0e0e0;
        }
        
        tr:hover {
            background: #f8f9fa;
        }
        
        .badge-voted {
            background: #d4edda;
            color: #155724;
            padding: 5px 12px;
            border-radius: 20px;
            display: inline-block;
        }
        
        .badge-not-voted {
            background: #fff3cd;
            color: #856404;
            padding: 5px 12px;
            border-radius: 20px;
            display: inline-block;
        }
        
        /* Alert */
        .alert {
            padding: 15px;
            border-radius: 12px;
            margin-bottom: 20px;
        }
        
        .alert-success {
            background: #d4edda;
            color: #155724;
        }
        
        .alert-danger {
            background: #f8d7da;
            color: #721c24;
        }
        
        /* Search */
        .search-box {
            padding: 10px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            width: 100%;
            max-width: 300px;
            margin-bottom: 20px;
        }
        
        /* DS Card */
        .ds-card {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            padding: 20px;
            border-radius: 16px;
        }
        
        .ds-card code {
            background: rgba(255,255,255,0.2);
            padding: 4px 8px;
            border-radius: 6px;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <div class="navbar">
        <div class="nav-container">
            <a href="index.php" class="logo">🗳️ E-Voting System</a>
            <ul class="nav-links">
                <li><a href="index.php">Home</a></li>
                <li><a href="register_simple.php">Register</a></li>
                <li><a href="add_candidate.php">Add Candidate</a></li>
                <li><a href="vote.php">Cast Vote</a></li>
                <li><a href="results.php">Results</a></li>
            </ul>
        </div>
    </div>
    
    <div class="container">
        <!-- Alert Message -->
        <?php if($message): ?>
            <div class="alert alert-<?php echo $message_type; ?>">
                <?php echo $message; ?>
            </div>
        <?php endif; ?>
        
        <!-- Two Column Layout -->
        <div class="two-columns">
            <!-- Left: Registration Form -->
            <div class="card">
                <div class="card-title">
                    📝 Register New Voter
                    <small>Adding Element to Voters Set V</small>
                </div>
                <form method="POST">
                    <div class="form-group">
                        <label>🆔 Voter ID Number</label>
                        <input type="text" name="voter_id_number" required placeholder="e.g., VOT2024001">
                    </div>
                    <div class="form-group">
                        <label>👤 Full Name</label>
                        <input type="text" name="name" required placeholder="Enter full name">
                    </div>
                    <div class="form-group">
                        <label>📧 Email Address</label>
                        <input type="email" name="email" required placeholder="email@example.com">
                    </div>
                    <button type="submit" class="btn-register">✅ Register Voter (Add to Set V)</button>
                </form>
            </div>
            
            <!-- Right: Statistics -->
            <div>
                <div class="stat-box">
                    <div class="stat-number"><?php echo $total; ?></div>
                    <div class="stat-label">Total Registered Voters |V|</div>
                    <div class="stat-row">
                        <div>
                            <div style="font-size: 24px; color: green;">✅ <?php echo $voted; ?></div>
                            <div>Voted</div>
                        </div>
                        <div>
                            <div style="font-size: 24px; color: orange;">⏳ <?php echo $not_voted; ?></div>
                            <div>Not Voted</div>
                        </div>
                    </div>
                </div>
                <div class="stat-box">
                    <div class="stat-number"><?php echo $total > 0 ? round(($voted/$total)*100) : 0; ?>%</div>
                    <div class="stat-label">Voter Turnout Rate</div>
                </div>
            </div>
        </div>
        
        <!-- Voters Table -->
        <div class="table-card">
            <div class="table-header">
                📋 Registered Voters (Current Set V) | |V| = <?php echo $total; ?>
            </div>
            <div class="table-body">
                <input type="text" id="searchInput" class="search-box" placeholder="🔍 Search voters by name or ID...">
                <table>
                    <thead>
                        <tr>
                            <th>Voter ID</th>
                            <th>Name</th>
                            <th>Email</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php
                        $result = mysqli_query($conn, "SELECT * FROM voters ORDER BY registration_date DESC");
                        while($row = mysqli_fetch_assoc($result)):
                        ?>
                        <tr>
                            <td><strong><?php echo htmlspecialchars($row['voter_id_number']); ?></strong></td>
                            <td><?php echo htmlspecialchars($row['name']); ?></td>
                            <td><?php echo htmlspecialchars($row['email']); ?></td>
                            <td>
                                <?php if($row['has_voted']): ?>
                                    <span class="badge-voted">✅ Voted</span>
                                <?php else: ?>
                                    <span class="badge-not-voted">⏳ Not Voted</span>
                                <?php endif; ?>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </div>
        
        <!-- Discrete Structures Card -->
        <div class="ds-card">
            <h3>🧮 Discrete Structures Concept Applied</h3>
            <p><strong>Set Theory:</strong> Voters Set V = {v₁, v₂, v₃, ...} where each voter is a unique element</p>
            <p><strong>Function:</strong> f: Voter ID → Voter Object (Injective Function)</p>
            <code>Domain: Voter IDs → Codomain: Voter Objects | |V| = <?php echo $total; ?></code>
        </div>
    </div>
    
    <script>
        // Search functionality
        const searchInput = document.getElementById('searchInput');
        if(searchInput) {
            searchInput.addEventListener('keyup', function() {
                let search = this.value.toLowerCase();
                let rows = document.querySelectorAll('tbody tr');
                rows.forEach(row => {
                    let text = row.textContent.toLowerCase();
                    row.style.display = text.indexOf(search) > -1 ? '' : 'none';
                });
            });
        }
    </script>
</body>
</html>