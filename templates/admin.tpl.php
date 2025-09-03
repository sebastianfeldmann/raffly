<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - All Raffles</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <div class="container">
        <h1>All Raffles</h1>
        
        <?php if (!empty($successMessage)): ?>
            <div class="success"><?= htmlspecialchars($successMessage) ?></div>
        <?php endif; ?>
        
        <?php if (empty($raffles)): ?>
            <div class="no-raffles">
                <p>No raffles found.</p>
                <a href="/" class="btn btn-primary">Create Your First Raffle</a>
            </div>
        <?php else: ?>
            <div class="raffles-table">
                <table>
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Raffle ID</th>
                            <th>Participants</th>
                            <th>Winners</th>
                            <th>Last Updated</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($raffles as $raffle): ?>
                            <tr>
                                <td class="raffle-title">
                                    <a href="/raffle/<?= $raffle['id'] ?>" class="raffle-title-link">
                                        <?= htmlspecialchars($raffle['title']) ?>
                                    </a>
                                </td>
                                <td class="raffle-id"><?= htmlspecialchars($raffle['id']) ?></td>
                                <td class="participant-count"><?= $raffle['participants'] ?></td>
                                <td class="winner-count"><?= $raffle['winners'] ?></td>
                                <td class="updated-date"><?= date('M j, Y h:i', $raffle['lastUpdated']) ?></td>
                                <td class="actions">
                                    <div class="dropdown">
                                        <button type="button" class="dropdown-toggle" onclick="toggleDropdown(event, '<?= $raffle['id'] ?>')">
                                            ⋯
                                        </button>
                                        <div class="dropdown-menu" id="dropdown-<?= $raffle['id'] ?>">
                                            <a href="/signup/<?= $raffle['id'] ?>" class="dropdown-item">
                                                📝 Sign Up
                                            </a>
                                            <a href="/raffle/<?= $raffle['id'] ?>" class="dropdown-item">
                                                🏆 Raffle
                                            </a>
                                            <form method="POST" class="dropdown-form" 
                                                  onsubmit="return confirm('Are you sure you want to delete this raffle?');">
                                                <input type="hidden" name="raffle_id" value="<?= htmlspecialchars($raffle['id']) ?>">
                                                <button type="submit" name="delete_raffle" class="dropdown-item dropdown-delete">
                                                    🗑️ Delete
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
        
        <div class="navigation">
            <a href="/" class="btn btn-primary">Create New Raffle</a>
        </div>
    </div>

    <footer class="powered-by">
        <div class="powered-by-content">
            <span>Powered by</span>
            <img src="/assets/raffly.png" alt="Raffly" class="powered-by-logo">
        </div>
    </footer>

    <script>
        function toggleDropdown(event, raffleId) {
            event.stopPropagation();
            
            // Close all other dropdowns
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                if (menu.id !== 'dropdown-' + raffleId) {
                    menu.classList.remove('show');
                    menu.classList.remove('dropdown-up');
                }
            });
            
            // Toggle current dropdown
            const dropdown = document.getElementById('dropdown-' + raffleId);
            const isVisible = dropdown.classList.contains('show');
            
            if (isVisible) {
                dropdown.classList.remove('show');
                dropdown.classList.remove('dropdown-up');
            } else {
                // Show dropdown temporarily to measure height
                dropdown.classList.add('show');
                
                // Check if dropdown would go off-screen
                const dropdownRect = dropdown.getBoundingClientRect();
                const windowHeight = window.innerHeight;
                
                // If dropdown goes below viewport, position it above
                if (dropdownRect.bottom > windowHeight - 10) {
                    dropdown.classList.add('dropdown-up');
                }
            }
        }

        // Close dropdowns when clicking outside
        document.addEventListener('click', function(event) {
            document.querySelectorAll('.dropdown-menu').forEach(menu => {
                menu.classList.remove('show');
                menu.classList.remove('dropdown-up');
            });
        });

        // Prevent dropdown from closing when clicking inside
        document.querySelectorAll('.dropdown-menu').forEach(menu => {
            menu.addEventListener('click', function(event) {
                event.stopPropagation();
            });
        });
    </script>
</body>
</html>
