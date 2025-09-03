<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin - All Raffles</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <div class="container admin-container">
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
                                <td class="raffle-title"><?= htmlspecialchars($raffle['title']) ?></td>
                                <td class="raffle-id"><?= htmlspecialchars($raffle['id']) ?></td>
                                <td class="participant-count"><?= $raffle['participants'] ?></td>
                                <td class="winner-count"><?= $raffle['winners'] ?></td>
                                <td class="created-date"><?= date('M j, Y h:i', $raffle['lastUpdated']) ?></td>
                                <td class="actions">
                                    <a href="/signup/<?= $raffle['id'] ?>" class="btn btn-small">Sign Up</a>
                                    <a href="/raffle/<?= $raffle['id'] ?>" class="btn btn-small">Wheel</a>
                                    <form method="POST" style="display: inline;" 
                                          onsubmit="return confirm('Are you sure you want to delete this raffle?');">
                                        <input type="hidden" name="raffle_id" value="<?= htmlspecialchars($raffle['id']) ?>">
                                        <button type="submit" name="delete_raffle" class="btn btn-small btn-danger">Delete</button>
                                    </form>
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
</body>
</html>
