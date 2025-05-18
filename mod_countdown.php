<?php
defined('_JEXEC') or die;

/**
 * Module Countdown Timer - Phiên bản 2.0.2
 * Tích hợp đầy đủ tính năng điều khiển và tự động chạy
 */

// Load helper class
require_once __DIR__ . '/helper.php';

// Lấy tham số từ cấu hình
$params = $params ?? JComponentHelper::getParams('mod_countdown');
$showControls = (bool) $params->get('show_controls', 1);

// Tính toán thời gian ban đầu (sử dụng helper)
$initialTime = ModCountdownHelper::calculateInitialTime(
    (int) $params->get('default_days', 0),
    (int) $params->get('default_hours', 1),
    (int) $params->get('default_minutes', 0),
    (int) $params->get('default_seconds', 0)
);

// Chuẩn bị dữ liệu cho template
$displayData = [
    'module_id'      => $module->id,
    'show_controls'  => $showControls,
    'initial_time'   => $initialTime,
    'button_texts'   => [
        'start' => htmlspecialchars($params->get('start_text', '▶️ Bắt đầu'), ENT_QUOTES),
        'pause' => htmlspecialchars($params->get('pause_text', '⏸️ Tạm dừng'), ENT_QUOTES),
        'reset' => htmlspecialchars($params->get('reset_text', '🔄 Reset'), ENT_QUOTES)
    ],
    'auto_start'     => true // Mặc định tự động chạy
];

// Render template
require JModuleHelper::getLayoutPath('mod_countdown', $params->get('layout', 'default'));
