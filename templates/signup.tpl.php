<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign Up - <?= htmlspecialchars($raffleData['title']) ?></title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <div class="container">
        <h1><?= htmlspecialchars($raffleData['title']) ?></h1>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <?php if (!empty($success)): ?>
            <div class="success"><?= htmlspecialchars($success) ?></div>
        <?php endif; ?>
        
        <form method="POST" class="signup-form">
            <div class="form-group">
                <label for="name">Your Name:</label>
                <input type="text" id="name" name="name" required maxlength="20" 
                       placeholder="Enter your name" 
                       value="<?= htmlspecialchars($rawName ?? '') ?>">
            </div>
            
            <button type="submit" class="btn btn-primary">Join Raffle</button>
        </form>
        
        <div class="participants">
            <h3>Current Participants: <?= count($raffleData['participants']) ?></h3>
            <p>Participants are kept private until the raffle begins.</p>
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
