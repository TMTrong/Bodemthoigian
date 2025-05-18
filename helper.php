<?php
defined('_JEXEC') or die;

/**
 * Helper class cho Module Countdown Timer
 * Phiên bản 2.1.0 - Tích hợp đầy đủ tính năng
 */
class ModCountdownHelper
{
    /**
     * Tính toán thời gian từ các thành phần
     * @param int $days Số ngày
     * @param int $hours Số giờ
     * @param int $minutes Số phút
     * @param int $seconds Số giây
     * @return array Kết quả đã tính toán
     */
    public static function calculateInitialTime($days = 0, $hours = 0, $minutes = 0, $seconds = 0)
    {
        // Validate input
        $days = max(0, (int)$days);
        $hours = min(23, max(0, (int)$hours));
        $minutes = min(59, max(0, (int)$minutes));
        $seconds = min(59, max(0, (int)$seconds));

        $totalSeconds = ($days * 86400) + ($hours * 3600) + ($minutes * 60) + $seconds;

        return [
            'days' => $days,
            'hours' => $hours,
            'minutes' => $minutes,
            'seconds' => $seconds,
            'total_seconds' => $totalSeconds,
            'formatted' => sprintf("%02dd %02dh %02dm %02ds", $days, $hours, $minutes, $seconds)
        ];
    }

    /**
     * Kiểm tra và chuẩn bị cài đặt điều khiển
     * @param JRegistry $params Tham số module
     * @return array Trạng thái điều khiển
     */
    public static function getControlSettings($params)
    {
        return [
            'show_controls' => (bool)$params->get('show_controls', 1),
            'auto_start' => (bool)$params->get('auto_start', 1),
            'buttons' => [
                'start' => htmlspecialchars($params->get('start_text', '▶️ Bắt đầu'), ENT_QUOTES),
                'pause' => htmlspecialchars($params->get('pause_text', '⏸️ Tạm dừng'), ENT_QUOTES),
                'reset' => htmlspecialchars($params->get('reset_text', '🔄 Reset'), ENT_QUOTES)
            ]
        ];
    }

    /**
     * Xử lý kiểu hiển thị
     * @param string $style Kiểu hiển thị từ tham số
     * @return array Các class CSS tương ứng
     */
    public static function getDisplayStyle($style)
    {
        switch ($style) {
            case 'compact':
                return [
                    'container' => 'countdown-compact',
                    'digits' => 'compact-digits',
                    'labels' => 'compact-labels'
                ];
            case 'flip':
                return [
                    'container' => 'countdown-flip',
                    'digits' => 'flip-digit',
                    'labels' => 'flip-label'
                ];
            default:
                return [
                    'container' => 'countdown-default',
                    'digits' => 'default-digits',
                    'labels' => 'default-labels'
                ];
        }
    }

    /**
     * Xử lý AJAX request từ frontend
     * @param array $data Dữ liệu nhận được
     * @return bool Thành công hay thất bại
     */
    public static function handleAjaxRequest($data)
    {
        try {
            $db = JFactory::getDbo();
            $query = $db->getQuery(true)
                ->update('#__modules')
                ->set('params = ' . $db->quote(json_encode($data['params'])))
                ->where('id = ' . (int)$data['module_id']);

            $db->setQuery($query)->execute();
            
            // Ghi log thành công
            JLog::add('Countdown updated: Module ID ' . $data['module_id'], JLog::INFO, 'mod_countdown');
            return true;
            
        } catch (Exception $e) {
            JLog::add('Countdown AJAX error: ' . $e->getMessage(), JLog::ERROR, 'mod_countdown');
            return false;
        }
    }
}
