<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raffly - <?= htmlspecialchars($raffleData['title']) ?></title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
    <div class="container">
        <div class="breadcrumb">
            <a href="/admin/raffles" class="breadcrumb-link">← All Raffles</a>
        </div>
        
        <div class="admin-header">
            <h1><?= htmlspecialchars($raffleData['title']) ?></h1>
        </div>
        
        <div class="admin-toolbar">
            <a href="/raffle/<?= $raffleId ?>" class="toolbar-action">
                <div class="toolbar-icon">🎡</div>
                <div class="toolbar-label">Start Raffle</div>
            </a>
            <a href="/signup/<?= $raffleId ?>" class="toolbar-action">
                <div class="toolbar-icon">📝</div>
                <div class="toolbar-label">Add Participants</div>
            </a>
            <button class="toolbar-action" onclick="copySignupUrl()">
                <div class="toolbar-icon">📋</div>
                <div class="toolbar-label">Copy Signup URL</div>
            </button>
            <button class="toolbar-action" onclick="showQrCode()">
                <div class="toolbar-icon">📱</div>
                <div class="toolbar-label">Show QR Code</div>
            </button>
        </div>
        
        <!-- QR Code Modal -->
        <div id="qrModal" class="qr-modal" onclick="closeQrModal()">
            <div class="qr-modal-content" onclick="event.stopPropagation()">
                <div class="qr-modal-header">
                    <h3>📱 Signup QR Code</h3>
                    <button onclick="closeQrModal()" class="qr-modal-close">×</button>
                </div>
                <div class="qr-modal-body">
                    <img src="/qrcode/<?= $raffleId ?>" alt="QR Code" class="qr-modal-image">
                    <p class="qr-modal-text">Scan to join: <?= htmlspecialchars($raffleData['title']) ?></p>
                </div>
            </div>
        </div>
        
        <!-- Manual Copy Modal -->
        <div id="copyModal" class="qr-modal" onclick="closeCopyModal()">
            <div class="qr-modal-content" onclick="event.stopPropagation()">
                <div class="qr-modal-header">
                    <h3>📋 Copy Signup URL</h3>
                    <button onclick="closeCopyModal()" class="qr-modal-close">×</button>
                </div>
                <div class="qr-modal-body">
                    <p class="copy-instructions">Copy the signup URL below:</p>
                    <div class="copy-url-container">
                        <input type="text" id="copyUrlInput" class="copy-url-input" readonly>
                        <button onclick="selectUrl()" class="btn btn-secondary copy-select-btn">Select All</button>
                    </div>
                    <p class="copy-help">Press <kbd>Ctrl+C</kbd> (or <kbd>⌘+C</kbd> on Mac) to copy</p>
                </div>
            </div>
        </div>
        
        <div class="info-grid">
            <div class="info-card">
                <h3>Participants (<?= count($raffleData['participants']) ?>)</h3>
                <ul id="participant-list">
                    <?php 
                    $sortedParticipants = $raffleData['participants'];
                    sort($sortedParticipants);
                    foreach ($sortedParticipants as $participant): ?>
                        <li class="participant-item" data-participant="<?= htmlspecialchars($participant) ?>">
                            <span class="participant-name"><?= htmlspecialchars($participant) ?></span>
                            <button class="btn-delete-participant" onclick="deleteParticipant('<?= htmlspecialchars($participant) ?>')" title="Delete Participant">
                                🗑️
                            </button>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
            
            <div class="info-card">
                <h3>Winners (<?= count($raffleData['winners']?? []) ?>)</h3>
                <?php if (!empty($raffleData['winners'])): ?>
                <ul>
                    <?php foreach ($raffleData['winners'] as $winner): ?>
                        <li class="participant-item" data-winner="<?= htmlspecialchars($winner) ?>">
                            <span class="participant-name"><?= htmlspecialchars($winner) ?></span>
                            <div class="winner-actions">
                                <button class="btn-delete-winner" onclick="deleteWinner('<?= htmlspecialchars($winner) ?>', true)" title="Restore to Participants">
                                    🔄
                                </button>
                                <button class="btn-delete-winner btn-delete-permanent" onclick="deleteWinner('<?= htmlspecialchars($winner) ?>', false)" title="Delete Permanently">
                                    🗑️
                                </button>
                            </div>
                        </li>
                    <?php endforeach; ?>
                </ul>
                <?php endif; ?>
            </div>
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

    <script>
        const raffleId = '<?= $raffleId ?>';
        
        // Delete participant function
        async function deleteParticipant(participantName) {
            if (!confirm('Are you sure you want to delete ' + participantName + '?')) {
                return;
            }
            
            try {
                const response = await fetch('/admin/participant/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'raffleID=' + encodeURIComponent(raffleId) + '&participant=' + encodeURIComponent(participantName)
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Remove the participant item from DOM
                    const participantItem = document.querySelector(`[data-participant="${participantName}"]`);
                    if (participantItem) {
                        participantItem.remove();
                    }
                    
                    // Update participant count in header
                    const participantHeader = document.querySelector('.info-card h3');
                    if (participantHeader) {
                        participantHeader.textContent = `Participants (${result.remainingParticipants})`;
                    }
                } else {
                    alert('Error: ' + (result.error || 'Failed to delete participant'));
                }
            } catch (error) {
                console.error('Error deleting participant:', error);
                alert('Error occurred while deleting participant.');
            }
        }
        
        // Delete winner function (restore to participants or delete permanently)
        async function deleteWinner(winnerName, backToParticipants) {
            const action = backToParticipants ? 'restore to participants' : 'delete permanently';
            if (!confirm('Are you sure you want to ' + action + ' ' + winnerName + '?')) {
                return;
            }
            
            try {
                const response = await fetch('/admin/winner/delete', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'raffleID=' + encodeURIComponent(raffleId) + '&winner=' + encodeURIComponent(winnerName) + '&backToParticipants=' + backToParticipants
                });
                
                const result = await response.json();
                
                if (result.success) {
                    // Remove the winner item from DOM
                    const winnerItem = document.querySelector(`[data-winner="${winnerName}"]`);
                    if (winnerItem) {
                        winnerItem.remove();
                    }
                    
                    // Update winner count in header
                    const winnerHeader = document.querySelector('.info-card h3');
                    if (winnerHeader && winnerHeader.textContent.includes('Winners')) {
                        winnerHeader.textContent = `Winners (${result.remainingWinners})`;
                    }
                    
                    // Update participant count and add to list (only if restored to participants)
                    if (backToParticipants) {
                        const participantHeader = document.querySelector('.info-card h3');
                        if (participantHeader && participantHeader.textContent.includes('Participants')) {
                            participantHeader.textContent = `Participants (${result.totalParticipants})`;
                        }
                        
                        // Add restored winner to participant list in alphabetical order
                        const participantList = document.getElementById('participant-list');
                        if (participantList) {
                            // Create new participant list item
                            const newParticipantItem = document.createElement('li');
                            newParticipantItem.className = 'participant-item';
                            newParticipantItem.setAttribute('data-participant', winnerName);
                            newParticipantItem.innerHTML = `
                                <span class="participant-name">${winnerName}</span>
                                <button class="btn-delete-participant" onclick="deleteParticipant('${winnerName.replace(/'/g, "\\'")}')" title="Delete Participant">
                                    🗑️
                                </button>
                            `;
                            
                            // Find correct alphabetical position
                            const existingItems = participantList.querySelectorAll('.participant-item');
                            let insertPosition = null;
                            
                            for (const item of existingItems) {
                                const existingName = item.querySelector('.participant-name').textContent;
                                if (winnerName.localeCompare(existingName) < 0) {
                                    insertPosition = item;
                                    break;
                                }
                            }
                            
                            // Insert at correct position
                            if (insertPosition) {
                                participantList.insertBefore(newParticipantItem, insertPosition);
                            } else {
                                participantList.appendChild(newParticipantItem);
                            }
                        }
                    }
                } else {
                    const errorAction = backToParticipants ? 'restore winner' : 'delete winner';
                    alert('Error: ' + (result.error || 'Failed to ' + errorAction));
                }
            } catch (error) {
                const errorAction = backToParticipants ? 'restoring' : 'deleting';
                console.error('Error ' + errorAction + ' winner:', error);
                alert('Error occurred while ' + errorAction + ' winner.');
            }
        }
        
        // Copy signup URL to clipboard with triple fallback
        async function copySignupUrl() {
            const button = event.currentTarget;
            const signupUrl = window.location.origin + '/signup/' + raffleId;

            // Method 1: Try modern Clipboard API
            try {
                await navigator.clipboard.writeText(signupUrl);
                showCopySuccess(button);
                return;
            } catch (error) {
                console.log('Modern clipboard failed, trying legacy method');
            }

            // Method 2: Try legacy execCommand
            if (tryLegacyCopy(signupUrl)) {
                showCopySuccess(button);
                return;
            }

            // Method 3: Show manual copy modal as last resort
            showManualCopyModal(signupUrl);
        }
        
        // Legacy copy method using execCommand
        function tryLegacyCopy(text) {
            try {
                // Create temporary input element
                const tempInput = document.createElement('input');
                tempInput.value = text;
                tempInput.style.position = 'absolute';
                tempInput.style.left = '-9999px';
                tempInput.style.opacity = '0';
                document.body.appendChild(tempInput);
                
                // Select and copy
                tempInput.select();
                tempInput.setSelectionRange(0, 99999);
                const success = document.execCommand('copy');
                
                // Clean up
                document.body.removeChild(tempInput);
                
                return success;
            } catch (error) {
                console.log('Legacy copy failed:', error);
                return false;
            }
        }
        
        // Show success feedback with icon morphing
        function showCopySuccess(button) {
            const originalIcon = button.querySelector('.toolbar-icon').textContent;
            const originalLabel = button.querySelector('.toolbar-label').textContent;
            
            // Morph icon and change text
            button.querySelector('.toolbar-icon').textContent = '✅';
            button.querySelector('.toolbar-label').textContent = 'Copied!';
            button.classList.add('toolbar-success');
            
            setTimeout(() => {
                button.querySelector('.toolbar-icon').textContent = originalIcon;
                button.querySelector('.toolbar-label').textContent = originalLabel;
                button.classList.remove('toolbar-success');
            }, 2000);
        }
        
        // Show manual copy modal
        function showManualCopyModal(url) {
            document.getElementById('copyUrlInput').value = url;
            document.getElementById('copyModal').classList.add('qr-modal-show');
            // Auto-select the URL for easy copying
            setTimeout(() => {
                document.getElementById('copyUrlInput').select();
            }, 100);
        }
        
        // Close manual copy modal
        function closeCopyModal() {
            document.getElementById('copyModal').classList.remove('qr-modal-show');
        }
        
        // Select URL in input field
        function selectUrl() {
            const input = document.getElementById('copyUrlInput');
            input.select();
            input.setSelectionRange(0, 99999); // For mobile devices
        }
        
        // Show QR code modal
        function showQrCode() {
            document.getElementById('qrModal').classList.add('qr-modal-show');
        }
        
        // Close QR code modal
        function closeQrModal() {
            document.getElementById('qrModal').classList.remove('qr-modal-show');
        }
        
        // Close modals on Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeQrModal();
                closeCopyModal();
            }
        });
    </script>
</body>
</html>
