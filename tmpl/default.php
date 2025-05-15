<?php
defined('_JEXEC') or die;

$defaultDays = $params->get('default_days', 0);
$defaultHours = $params->get('default_hours', 1);
$defaultMinutes = $params->get('default_minutes', 0);
$defaultSeconds = $params->get('default_seconds', 0);
?>

<div class="mod-countdown" id="mod-countdown-<?php echo $module->id; ?>">
    <div class="countdown-display">00d 00h 00m 00s</div>
    
    <div class="countdown-controls">
        <!-- Ô nhập thời gian tùy chỉnh -->
        <div class="time-inputs">
            <input type="number" class="countdown-input-days" min="0" value="<?php echo $defaultDays; ?>" placeholder="Ngày">
            <input type="number" class="countdown-input-hours" min="0" max="23" value="<?php echo $defaultHours; ?>" placeholder="Giờ">
            <input type="number" class="countdown-input-minutes" min="0" max="59" value="<?php echo $defaultMinutes; ?>" placeholder="Phút">
            <input type="number" class="countdown-input-seconds" min="0" max="59" value="<?php echo $defaultSeconds; ?>" placeholder="Giây">
        </div>
        
        <div class="buttons">
            <button class="btn-start">Bắt đầu</button>
            <button class="btn-pause">Tạm dừng</button>
            <button class="btn-reset">Reset</button>
        </div>
    </div>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    const moduleId = <?php echo $module->id; ?>;
    const container = document.getElementById(`mod-countdown-${moduleId}`);
    const displayEl = container.querySelector('.countdown-display');
    
    // Các ô nhập liệu
    const inputDays = container.querySelector('.countdown-input-days');
    const inputHours = container.querySelector('.countdown-input-hours');
    const inputMinutes = container.querySelector('.countdown-input-minutes');
    const inputSeconds = container.querySelector('.countdown-input-seconds');
    
    // Các nút điều khiển
    const btnStart = container.querySelector('.btn-start');
    const btnPause = container.querySelector('.btn-pause');
    const btnReset = container.querySelector('.btn-reset');

    let countdownInterval;
    let remainingSeconds = 0;
    let isPaused = false;

    // Định dạng thời gian: DDd HHh MMm SSs
    function formatTime(totalSeconds) {
        const days = Math.floor(totalSeconds / (3600 * 24));
        const hours = Math.floor((totalSeconds % (3600 * 24)) / 3600);
        const minutes = Math.floor((totalSeconds % 3600) / 60);
        const seconds = totalSeconds % 60;
        
        return `
            ${String(days).padStart(2, '0')}d 
            ${String(hours).padStart(2, '0')}h 
            ${String(minutes).padStart(2, '0')}m 
            ${String(seconds).padStart(2, '0')}s
        `.replace(/\s+/g, ' ');
    }

    // Bắt đầu đếm ngược
    function startCountdown() {
        clearInterval(countdownInterval);
        
        // Tính tổng số giây từ input
        const days = parseInt(inputDays.value) || 0;
        const hours = parseInt(inputHours.value) || 0;
        const minutes = parseInt(inputMinutes.value) || 0;
        const seconds = parseInt(inputSeconds.value) || 0;
        
        remainingSeconds = (days * 24 * 3600) + (hours * 3600) + (minutes * 60) + seconds;
        
        countdownInterval = setInterval(() => {
            if (!isPaused && remainingSeconds > 0) {
                remainingSeconds--;
                displayEl.textContent = formatTime(remainingSeconds);
                
                // Cập nhật các ô input khi đếm ngược
                const days = Math.floor(remainingSeconds / (3600 * 24));
                const hours = Math.floor((remainingSeconds % (3600 * 24)) / 3600);
                const minutes = Math.floor((remainingSeconds % 3600) / 60);
                const seconds = remainingSeconds % 60;
                
                inputDays.value = days;
                inputHours.value = hours;
                inputMinutes.value = minutes;
                inputSeconds.value = seconds;
            }
            
            if (remainingSeconds <= 0) {
                clearInterval(countdownInterval);
                displayEl.textContent = "00d 00h 00m 00s";
                displayEl.classList.add('expired');
            }
        }, 1000);
    }

    // Sự kiện nút Bắt đầu
    btnStart.addEventListener('click', () => {
        isPaused = false;
        btnPause.textContent = "Tạm dừng";
        displayEl.classList.remove('expired');
        startCountdown();
    });

    // Sự kiện nút Tạm dừng
    btnPause.addEventListener('click', () => {
        isPaused = !isPaused;
        btnPause.textContent = isPaused ? "Tiếp tục" : "Tạm dừng";
    });

    // Sự kiện nút Reset
    btnReset.addEventListener('click', () => {
        clearInterval(countdownInterval);
        isPaused = false;
        remainingSeconds = 0;
        displayEl.textContent = formatTime(0);
        btnPause.textContent = "Tạm dừng";
        displayEl.classList.remove('expired');
        
        // Reset về giá trị mặc định
        inputDays.value = <?php echo $defaultDays; ?>;
        inputHours.value = <?php echo $defaultHours; ?>;
        inputMinutes.value = <?php echo $defaultMinutes; ?>;
        inputSeconds.value = <?php echo $defaultSeconds; ?>;
    });

    // Khởi tạo với thời gian mặc định
    displayEl.textContent = formatTime(
        (<?php echo $defaultDays; ?> * 24 * 3600) + 
        (<?php echo $defaultHours; ?> * 3600) + 
        (<?php echo $defaultMinutes; ?> * 60) + 
        <?php echo $defaultSeconds; ?>
    );
});
</script>

<style>
.mod-countdown {
    font-family: Arial, sans-serif;
    max-width: 600px;
    margin: 0 auto;
    padding: 20px;
    background: #f8f9fa;
    border-radius: 10px;
    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
}

.countdown-display {
    font-size: 2.2rem;
    font-weight: bold;
    text-align: center;
    margin: 20px 0;
    color: #2c3e50;
    letter-spacing: 2px;
}

.countdown-display.expired {
    color: #e74c3c;
    animation: pulse 1s infinite;
}

@keyframes pulse {
    0% { opacity: 1; }
    50% { opacity: 0.5; }
    100% { opacity: 1; }
}

.time-inputs {
    display: flex;
    gap: 10px;
    justify-content: center;
    margin-bottom: 15px;
    flex-wrap: wrap;
}

.time-inputs input {
    width: 70px;
    padding: 10px;
    text-align: center;
    border: 1px solid #ddd;
    border-radius: 5px;
    font-size: 1rem;
}

.buttons {
    display: flex;
    gap: 10px;
    justify-content: center;
    flex-wrap: wrap;
}

.btn-start, .btn-pause, .btn-reset {
    padding: 10px 20px;
    border: none;
    border-radius: 5px;
    cursor: pointer;
    font-weight: bold;
    transition: all 0.3s;
    min-width: 100px;
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
    opacity: 0.9;
    transform: translateY(-2px);
}

@media (max-width: 480px) {
    .time-inputs input {
        width: 60px;
        padding: 8px;
    }
    
    .buttons {
        flex-direction: column;
        align-items: center;
    }
    
    .btn-start, .btn-pause, .btn-reset {
        width: 100%;
        margin-bottom: 5px;
    }
}
</style>