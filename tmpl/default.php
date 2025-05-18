<?php
defined('_JEXEC') or die;

// Lấy tham số từ module
$showControls = (bool) $params->get('show_controls', 1);
$autoStart = (bool) $params->get('auto_start', 1);
$buttonTexts = [
    'start' => $params->get('start_text', '▶️ Bắt đầu'),
    'pause' => $params->get('pause_text', '⏸️ Tạm dừng'),
    'reset' => $params->get('reset_text', '🔄 Reset'),
    'resume' => $params->get('resume_text', '▶️ Tiếp tục')
];

// Tính toán thời gian ban đầu
$initialSeconds = ($params->get('default_days', 0) * 86400)
                + ($params->get('default_hours', 1) * 3600)
                + ($params->get('default_minutes', 0) * 60)
                + $params->get('default_seconds', 0);
?>

<div class="mod-countdown" id="mod-countdown-<?php echo $module->id; ?>">
    <div class="countdown-display">
        <?php echo sprintf("%02dd %02dh %02dm %02ds", 
            $params->get('default_days', 0),
            $params->get('default_hours', 1),
            $params->get('default_minutes', 0),
            $params->get('default_seconds', 0)
        ); ?>
    </div>
    
    <?php if ($showControls) : ?>
    <div class="countdown-controls">
        <div class="time-inputs">
            <input type="number" class="countdown-input-days" min="0" 
                   value="<?php echo $params->get('default_days', 0); ?>" placeholder="Ngày">
            <input type="number" class="countdown-input-hours" min="0" max="23" 
                   value="<?php echo $params->get('default_hours', 1); ?>" placeholder="Giờ">
            <input type="number" class="countdown-input-minutes" min="0" max="59" 
                   value="<?php echo $params->get('default_minutes', 0); ?>" placeholder="Phút">
            <input type="number" class="countdown-input-seconds" min="0" max="59" 
                   value="<?php echo $params->get('default_seconds', 0); ?>" placeholder="Giây">
        </div>
        
        <div class="buttons">
            <button class="btn-start"><?php echo htmlspecialchars($buttonTexts['start']); ?></button>
            <button class="btn-pause"><?php echo htmlspecialchars($buttonTexts['pause']); ?></button>
            <button class="btn-reset"><?php echo htmlspecialchars($buttonTexts['reset']); ?></button>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const module = document.getElementById('mod-countdown-<?php echo $module->id; ?>');
    if (!module) return;

    // Elements
    const display = module.querySelector('.countdown-display');
    const daysInput = module.querySelector('.countdown-input-days');
    const hoursInput = module.querySelector('.countdown-input-hours');
    const minutesInput = module.querySelector('.countdown-input-minutes');
    const secondsInput = module.querySelector('.countdown-input-seconds');
    const startBtn = module.querySelector('.btn-start');
    const pauseBtn = module.querySelector('.btn-pause');
    const resetBtn = module.querySelector('.btn-reset');

    // State
    let interval;
    let remainingSeconds = <?php echo $initialSeconds; ?>;
    let isPaused = false;

    // Format time
    const formatTime = (seconds) => {
        const days = Math.floor(seconds / 86400);
        const hours = Math.floor((seconds % 86400) / 3600);
        const mins = Math.floor((seconds % 3600) / 60);
        const secs = seconds % 60;
        return `${String(days).padStart(2, '0')}d ${String(hours).padStart(2, '0')}h ${String(mins).padStart(2, '0')}m ${String(secs).padStart(2, '0')}s`;
    };

    // Update display
    const updateDisplay = () => {
        display.textContent = formatTime(remainingSeconds);
        if (daysInput) daysInput.value = Math.floor(remainingSeconds / 86400);
        if (hoursInput) hoursInput.value = Math.floor((remainingSeconds % 86400) / 3600);
        if (minutesInput) minutesInput.value = Math.floor((remainingSeconds % 3600) / 60);
        if (secondsInput) secondsInput.value = remainingSeconds % 60;
    };

    // Start countdown
    const startTimer = () => {
        clearInterval(interval);
        interval = setInterval(() => {
            if (!isPaused && remainingSeconds > 0) {
                remainingSeconds--;
                updateDisplay();
                if (remainingSeconds <= 0) {
                    clearInterval(interval);
                    display.classList.add('expired');
                }
            }
        }, 1000);
    };

    // Event listeners
    if (startBtn) {
        startBtn.addEventListener('click', () => {
            remainingSeconds = (parseInt(daysInput.value) || 0) * 86400 +
                             (parseInt(hoursInput.value) || 0) * 3600 +
                             (parseInt(minutesInput.value) || 0) * 60 +
                             (parseInt(secondsInput.value) || 0);
            isPaused = false;
            display.classList.remove('expired');
            startTimer();
        });
    }

    if (pauseBtn) {
        pauseBtn.addEventListener('click', () => {
            isPaused = !isPaused;
            pauseBtn.textContent = isPaused ? 
                "<?php echo htmlspecialchars($buttonTexts['resume']); ?>" : 
                "<?php echo htmlspecialchars($buttonTexts['pause']); ?>";
        });
    }

    if (resetBtn) {
        resetBtn.addEventListener('click', () => {
            clearInterval(interval);
            remainingSeconds = <?php echo $initialSeconds; ?>;
            isPaused = false;
            updateDisplay();
            display.classList.remove('expired');
            if (pauseBtn) pauseBtn.textContent = "<?php echo htmlspecialchars($buttonTexts['pause']); ?>";
        });
    }

    // Initialize
    updateDisplay();
    
    // Tự động chạy khi không hiển thị điều khiển và autoStart được bật
    <?php if (!$showControls && $autoStart) : ?>
    startTimer();
    <?php endif; ?>
});
</script>

<style>
.mod-countdown {
    font-family: 'Arial', sans-serif;
    max-width: 600px;
    margin: 20px auto;
    padding: 20px;
    background: #ffffff;
    border-radius: 10px;
    box-shadow: 0 2px 15px rgba(0,0,0,0.1);
    text-align: center;
}

.countdown-display {
    font-size: 2.5rem;
    font-weight: bold;
    color: #2c3e50;
    margin: 15px 0;
    letter-spacing: 1px;
}

.countdown-display.expired {
    color: #e74c3c;
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0%, 100% { opacity: 1; }
    50% { opacity: 0.6; }
}

.countdown-controls {
    margin-top: 20px;
}

.time-inputs {
    display: flex;
    justify-content: center;
    gap: 10px;
    margin-bottom: 15px;
    flex-wrap: wrap;
}

.time-inputs input {
    width: 70px;
    padding: 10px;
    border: 1px solid #ddd;
    border-radius: 5px;
    text-align: center;
    font-size: 1rem;
}

.buttons {
    display: flex;
    justify-content: center;
    gap: 10px;
    flex-wrap: wrap;
}

.btn-start, .btn-pause, .btn-reset {
    padding: 12px 25px;
    border: none;
    border-radius: 5px;
    font-weight: bold;
    cursor: pointer;
    transition: all 0.3s;
    font-size: 1rem;
}

.btn-start {
    background: #27ae60;
    color: white;
}

.btn-pause {
    background: #f39c12;
    color: white;
}

.btn-reset {
    background: #e74c3c;
    color: white;
}

.btn-start:hover, .btn-pause:hover, .btn-reset:hover {
    transform: translateY(-2px);
    box-shadow: 0 4px 8px rgba(0,0,0,0.1);
}

/* Ẩn input khi tắt điều khiển */
<?php if (!$showControls) : ?>
.time-inputs {
    display: none !important;
}
<?php endif; ?>

@media (max-width: 600px) {
    .countdown-display {
        font-size: 1.8rem;
    }
    
    .time-inputs input {
        width: 60px;
        padding: 8px;
    }
    
    .buttons {
        flex-direction: column;
    }
    
    .btn-start, .btn-pause, .btn-reset {
        width: 100%;
    }
}
</style>
