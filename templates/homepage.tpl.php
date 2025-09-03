<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Raffle</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <div class="container">
        <div class="logo-container">
            <img src="/assets/raffly.png" alt="Raffly Logo" class="site-logo">
        </div>
        
        <?php if (!empty($error)): ?>
            <div class="error"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>
        
        <form method="POST" class="raffle-form">
            <div class="form-group">
                <label for="title">Choose a name:</label>
                <input type="text" id="title" name="title" required maxlength="100" 
                       value="<?= htmlspecialchars($_POST['title'] ?? '') ?>">
            </div>
            
            <button type="submit" class="btn btn-primary">Create Raffle</button>
        </form>
        
    </div>
</body>
</html>
