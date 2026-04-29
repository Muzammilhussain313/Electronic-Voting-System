// =============================================
// ELECTRONIC VOTING SYSTEM - JAVASCRIPT
// Discrete Structures: Graph Visualization & Logic
// =============================================

// Global variables
let selectedCandidate = null;
let voteChart = null;

// =============================================
// CANDIDATE SELECTION FOR VOTING
// =============================================
function selectCandidate(candidateId, element) {
    // Remove selection from all cards (Set operation: clear all)
    document.querySelectorAll('.candidate-card').forEach(card => {
        card.classList.remove('selected');
    });
    
    // Add selection to clicked card (Set operation: add to selected set)
    element.classList.add('selected');
    selectedCandidate = candidateId;
    
    // Enable vote button (Logic: AND condition satisfied)
    const voteBtn = document.getElementById('voteBtn');
    if (voteBtn) {
        voteBtn.disabled = false;
        voteBtn.style.opacity = '1';
    }
}

// =============================================
// CONFIRM VOTE WITH LOGIC CHECK
// =============================================
function confirmVote() {
    // Propositional logic check: if (selectedCandidate != null) then allow vote
    if (!selectedCandidate) {
        showAlert('Please select a candidate first! (Selection required)', 'danger');
        return false;
    }
    
    // Boolean logic: AND condition (voter exists AND candidate selected)
    const confirmed = confirm(
        '🗳️ Confirm Your Vote\n\n' +
        'Remember: Each voter can vote only once!\n' +
        'This ensures that the voting function f: V → C is well-defined.'
    );
    
    if (confirmed) {
        document.getElementById('candidate_id').value = selectedCandidate;
        return true;
    }
    return false;
}

// =============================================
// SHOW ALERT MESSAGES
// =============================================
function showAlert(message, type) {
    const alertDiv = document.createElement('div');
    alertDiv.className = `alert alert-${type}`;
    alertDiv.innerHTML = `
        <strong>${type === 'success' ? '✓' : '⚠️'}</strong> 
        ${message}
    `;
    
    const container = document.querySelector('.container');
    if (container) {
        container.insertBefore(alertDiv, container.firstChild);
    }
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        alertDiv.style.opacity = '0';
        setTimeout(() => alertDiv.remove(), 300);
    }, 5000);
}

// =============================================
// FORM VALIDATION (LOGIC GATES)
// =============================================
function validateVoterForm() {
    const voterId = document.getElementById('voter_id_number')?.value;
    const name = document.getElementById('name')?.value;
    const email = document.getElementById('email')?.value;
    
    // AND logic: All conditions must be true
    if (!voterId || voterId.trim() === '') {
        showAlert('❌ Voter ID is required (p = false)', 'danger');
        return false;
    }
    
    if (!name || name.trim() === '') {
        showAlert('❌ Name is required (q = false)', 'danger');
        return false;
    }
    
    if (!email || email.trim() === '') {
        showAlert('❌ Email is required (r = false)', 'danger');
        return false;
    }
    
    // Email validation using regular expression
    const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (!emailPattern.test(email)) {
        showAlert('❌ Please enter a valid email address', 'danger');
        return false;
    }
    
    // All conditions satisfied (p ∧ q ∧ r = true)
    return true;
}

// =============================================
// FETCH AND UPDATE RESULTS (GRAPH DATA)
// =============================================
async function updateResults() {
    try {
        const response = await fetch('get_results_data.php');
        const data = await response.json();
        
        if (data.success) {
            updateResultsTable(data.results);
            updateChart(data.results);
            updateSetInformation(data.results);
        }
    } catch (error) {
        console.error('Error fetching results:', error);
    }
}

// =============================================
// UPDATE RESULTS TABLE WITH SET THEORY
// =============================================
function updateResultsTable(results) {
    const tbody = document.getElementById('results-tbody');
    if (!tbody) return;
    
    tbody.innerHTML = '';
    let maxVotes = 0;
    let winner = null;
    
    // Find winner (maximum out-degree in voting graph)
    results.forEach(result => {
        if (result.votes > maxVotes) {
            maxVotes = result.votes;
            winner = result;
        }
    });
    
    // Display results
    results.forEach(result => {
        const row = tbody.insertRow();
        const isWinner = (result === winner);
        row.innerHTML = `
            <td style="font-size: 1.5rem;">${result.symbol || '📌'}</td>
            <td><strong>${escapeHtml(result.name)}</strong></td>
            <td>${escapeHtml(result.party)}</td>
            <td>
                <span style="font-size: 1.25rem; font-weight: bold; color: var(--primary);">
                    ${result.votes}
                </span>
            </td>
            <td>
                ${isWinner ? '<span class="winner-badge">🏆 WINNER</span>' : ''}
                ${result.votes > 0 ? `<span style="font-size: 0.75rem;"> (${Math.round((result.votes / maxVotes) * 100)}%)</span>` : ''}
            </td>
        `;
    });
}

// =============================================
// UPDATE SET INFORMATION DISPLAY
// =============================================
function updateSetInformation(results) {
    const totalVotes = results.reduce((sum, r) => sum + r.votes, 0);
    const setInfoDiv = document.getElementById('set-info');
    
    if (setInfoDiv) {
        setInfoDiv.innerHTML = `
            <h4>📊 Discrete Structures Applied in Results:</h4>
            <ul style="margin-left: 1.5rem; line-height: 1.8;">
                <li><strong>Set Theory:</strong> |V| = Total Voters, |C| = ${results.length} Candidates</li>
                <li><strong>Voting Function:</strong> f: V → C (Each voter maps to exactly one candidate)</li>
                <li><strong>Graph Theory:</strong> Directed graph with ${totalVotes} edges (votes)</li>
                <li><strong>Winner:</strong> Candidate with maximum out-degree in the voting graph</li>
                <li><strong>Logic:</strong> ∀v ∈ V, ∃! c ∈ C such that v → c (Unique mapping)</li>
            </ul>
        `;
    }
}

// =============================================
// CREATE/UPDATE CHART (GRAPH VISUALIZATION)
// =============================================
function updateChart(results) {
    const ctx = document.getElementById('voteChart')?.getContext('2d');
    if (!ctx) return;
    
    const labels = results.map(r => `${r.name} (${r.party})`);
    const votes = results.map(r => r.votes);
    
    // Destroy existing chart if it exists
    if (voteChart) {
        voteChart.destroy();
    }
    
    // Create new chart
    voteChart = new Chart(ctx, {
        type: 'bar',
        data: {
            labels: labels,
            datasets: [{
                label: 'Votes Received (Out-degree in Voting Graph)',
                data: votes,
                backgroundColor: [
                    'rgba(67, 97, 238, 0.8)',
                    'rgba(114, 9, 183, 0.8)',
                    'rgba(6, 214, 160, 0.8)',
                    'rgba(255, 209, 102, 0.8)',
                    'rgba(239, 71, 111, 0.8)'
                ],
                borderColor: [
                    'rgb(67, 97, 238)',
                    'rgb(114, 9, 183)',
                    'rgb(6, 214, 160)',
                    'rgb(255, 209, 102)',
                    'rgb(239, 71, 111)'
                ],
                borderWidth: 2,
                borderRadius: 8
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: true,
            plugins: {
                legend: {
                    position: 'top',
                    labels: {
                        font: {
                            size: 12,
                            weight: 'bold'
                        }
                    }
                },
                title: {
                    display: true,
                    text: '📊 Voting Results - Directed Graph Visualization',
                    font: {
                        size: 16,
                        weight: 'bold'
                    }
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            return `Votes: ${context.raw} (Out-degree)`;
                        },
                        afterLabel: function(context) {
                            const total = votes.reduce((a, b) => a + b, 0);
                            const percentage = ((context.raw / total) * 100).toFixed(1);
                            return `Percentage: ${percentage}%`;
                        }
                    }
                }
            },
            scales: {
                y: {
                    beginAtZero: true,
                    title: {
                        display: true,
                        text: 'Number of Votes (Out-degree)',
                        font: {
                            weight: 'bold'
                        }
                    },
                    grid: {
                        dash: [5, 5]
                    }
                },
                x: {
                    title: {
                        display: true,
                        text: 'Candidates',
                        font: {
                            weight: 'bold'
                        }
                    }
                }
            }
        }
    });
}

// =============================================
// HELPER: ESCAPE HTML TO PREVENT XSS
// =============================================
function escapeHtml(str) {
    if (!str) return '';
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

// =============================================
// AUTO-REFRESH RESULTS PAGE
// =============================================
if (window.location.pathname.includes('results.php')) {
    // Refresh every 10 seconds
    setInterval(updateResults, 10000);
    // Initial load
    document.addEventListener('DOMContentLoaded', updateResults);
}

// =============================================
// SMOOTH SCROLLING FOR ANCHOR LINKS
// =============================================
document.querySelectorAll('a[href^="#"]').forEach(anchor => {
    anchor.addEventListener('click', function(e) {
        e.preventDefault();
        const target = document.querySelector(this.getAttribute('href'));
        if (target) {
            target.scrollIntoView({ 
                behavior: 'smooth',
                block: 'start'
            });
        }
    });
});

// =============================================
// CONSOLE LOG FOR DISCRETE STRUCTURES
// =============================================
console.log('🎓 Electronic Voting System Loaded');
console.log('Discrete Structures Implemented:');
console.log('  ✓ Set Theory (Voters Set, Candidates Set)');
console.log('  ✓ Functions (Voting Function f: V → C)');
console.log('  ✓ Graph Theory (Directed Voting Graph)');
console.log('  ✓ Propositional Logic (Eligibility Checking)');
console.log('  ✓ Relations (Voting Relation R ⊆ V × C)');