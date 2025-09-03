<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raffle Wheel - <?= htmlspecialchars($raffleData['title']) ?></title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <div class="container">
        <h1><?= htmlspecialchars($raffleData['title']) ?></h1>
        
        <div class="wheel-container">
            <?php if (empty($raffleData['participants'])): ?>
                <div class="no-participants">
                    <p>No participants available.</p>
                </div>
            <?php else: ?>
                <div class="wheel-wrapper">
                    <div class="wheel-pointer">
                        <div class="pointer-arrow"></div>
                    </div>
                    <canvas id="wheelCanvas" width="500" height="500">
                        Canvas not supported, use another browser.
                    </canvas>
                </div>
            <?php endif; ?>
        </div>
        
        <div class="controls">
            <?php if (!empty($raffleData['participants'])): ?>
                <button id="spinBtn" class="btn btn-primary btn-large">Spin the Wheel!</button>
            <?php endif; ?>
        </div>
        
        <div id="winner-display" class="winner-display" style="display: none;">
            <h2>🎉 Winner: <span id="winner-name"></span> 🎉</h2>
        </div>
        
        <div class="info-grid">
            <div class="info-card">
                <h3>Remaining Participants</h3>
                <p id="participant-count"><?= count($raffleData['participants']) ?></p>
            </div>
            
            <?php if (!empty($raffleData['winners'])): ?>
            <div class="info-card">
                <h3>Previous Winners</h3>
                <ul>
                    <?php foreach ($raffleData['winners'] as $winner): ?>
                        <li><?= htmlspecialchars($winner) ?></li>
                    <?php endforeach; ?>
                </ul>
            </div>
            <?php endif; ?>
        </div>
        
        <div class="qr-section">
            <h3>Share this Raffle</h3>
            <div class="qr-content">
                <div class="qr-code">
                    <img src="/qrcode/<?= $raffleId ?>" alt="QR Code for Raffle Signup" class="qr-image">
                </div>
                <div class="qr-info">
                    <p>Scan to join this raffle</p>
                    <a href="/signup/<?= $raffleId ?>" class="btn btn-secondary">
                        📝 Join Raffle
                    </a>
                </div>
            </div>
        </div>
        
    </div>

    <footer class="powered-by">
        <div class="powered-by-content">
            <span>Powered by</span>
            <img src="/assets/raffly.png" alt="Raffly" class="powered-by-logo">
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/gsap/2.1.3/TweenMax.min.js"></script>
    <script src="/assets/winwheel.min.js"></script>
    <script>
        const raffleId = '<?= $raffleId ?>';
        let isSpinning = false;

        // 8 distinct colors optimized for wheel visibility
        const colors = ['#FF6B6B', '#4ECDC4', '#45B7D1', '#96CEB4', '#FFEAA7', '#DDA0DD', '#FFB347', '#87CEEB'];
        const participants = <?= json_encode($raffleData['participants']) ?>;
        let lastColor = '';
        
        const segments = participants.map((participant, index) => {
            // Simple function to get random color avoiding adjacent duplicates
            function getRandom(colors, lastUsedColor) {
                if (index === 0) return colors[Math.floor(Math.random() * colors.length)];
                
                let randomColor;
                let attempts = 0;
                do {
                    randomColor = colors[Math.floor(Math.random() * colors.length)];
                    attempts++;
                } while (attempts < 10 && randomColor === lastUsedColor);
                
                return randomColor;
            }
            
            const selectedColor = getRandom(colors, lastColor);
            lastColor = selectedColor;
            
            return {
                'fillStyle': selectedColor,
                'text': participant,
                'textFontSize': 16,
                'textFillStyle': 'black'
            };
        });

        let theWheel = new Winwheel({
            'canvasId'     : 'wheelCanvas',
            'numSegments'  : segments.length,     
            'outerRadius'  : 250,  
            'textFontSize' : 28,   
            'segments'     : segments,
            'animation' :           
            {
                'type'     : 'spinToStop',
                'duration' : 5, 
                'spins'    : 8,
                'callbackFinished' : wheelStopped
            }
        });
            
        // Add click handler for spin button
        document.getElementById('spinBtn')?.addEventListener('click', spinWheel);
        
        
        async function spinWheel() {
            if (isSpinning || !theWheel) return;
            
            const spinBtn = document.getElementById('spinBtn');
            
            isSpinning = true;
            spinBtn.disabled = true;
            spinBtn.textContent = 'Spinning...';
            
            // Start the wheel spinning
            theWheel.startAnimation();
        }
        
        // Callback function called when wheel stops spinning
        async function wheelStopped(indicatedSegment) {
            let winner = null;
            let winningSegment = null;
            
            if (indicatedSegment && indicatedSegment.text) {
                winner = indicatedSegment.text;
                winningSegment = indicatedSegment;
            }                
            
            if (!winner) {
                console.error('Could not determine winner!');
                alert('Error: Could not determine winner!');
                return;
            }
            
            const winnerDisplay = document.getElementById('winner-display');
            const participantCount = document.getElementById('participant-count');
            
            try {
                // Send winner to backend
                const response = await fetch('/winner/' + raffleId, {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'winner=' + encodeURIComponent(winner)
                });
                
                const result = await response.json();
                
                if (result.error) {
                    alert(result.error);
                    return;
                }
                
                // Show winner
                document.getElementById('winner-name').textContent = winner;
                winnerDisplay.style.display = 'block';
                
                // Update participant count
                participantCount.textContent = result.remainingParticipants;
                
                // Reload page after showing winner for 3 seconds
                setTimeout(() => {
                    window.location.reload();
                }, 3000);
                
            } catch (error) {
                console.error('Error processing winner:', error);
                alert('Error occurred while processing the winner.');
            } finally {
                // Reset spin button
                const spinBtn = document.getElementById('spinBtn');
                isSpinning = false;
                spinBtn.disabled = false;
                spinBtn.textContent = 'Spin the Wheel!';
            }
        }
    </script>
    <script src="/assets/script.js"></script>
</body>
</html>
