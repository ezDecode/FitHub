<?php
/**
 * FitHub Gym Management System
 * Helper Functions & Utilities
 * 
 * Common PHP functions used throughout the application
 */

// Prevent direct access
if (!defined('APP_ROOT')) {
    define('APP_ROOT', dirname(__DIR__));
}

// ==============================================
// INPUT SANITIZATION FUNCTIONS
// ==============================================

/**
 * Sanitize string input
 * @param string $data Input data to sanitize
 * @return string Sanitized string
 */
function sanitizeString($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Sanitize email
 * @param string $email Email to sanitize
 * @return string|false Sanitized email or false
 */
function sanitizeEmail($email) {
    $email = filter_var($email, FILTER_SANITIZE_EMAIL);
    return filter_var($email, FILTER_VALIDATE_EMAIL) ? $email : false;
}

/**
 * Sanitize integer
 * @param mixed $number Number to sanitize
 * @return int Sanitized integer
 */
function sanitizeInt($number) {
    return filter_var($number, FILTER_SANITIZE_NUMBER_INT);
}

/**
 * Sanitize float
 * @param mixed $number Number to sanitize
 * @return float Sanitized float
 */
function sanitizeFloat($number) {
    return filter_var($number, FILTER_SANITIZE_NUMBER_FLOAT, FILTER_FLAG_ALLOW_FRACTION);
}

/**
 * Sanitize URL
 * @param string $url URL to sanitize
 * @return string|false Sanitized URL or false
 */
function sanitizeUrl($url) {
    $url = filter_var($url, FILTER_SANITIZE_URL);
    return filter_var($url, FILTER_VALIDATE_URL) ? $url : false;
}

/**
 * Sanitize array of strings
 * @param array $data Array to sanitize
 * @return array Sanitized array
 */
function sanitizeArray($data) {
    if (!is_array($data)) return [];
    return array_map('sanitizeString', $data);
}

// ==============================================
// VALIDATION FUNCTIONS
// ==============================================

/**
 * Validate email format
 * @param string $email Email to validate
 * @return bool True if valid, false otherwise
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

/**
 * Validate phone number (10-15 digits)
 * @param string $phone Phone number to validate
 * @return bool True if valid, false otherwise
 */
function validatePhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    $length = strlen($phone);
    return $length >= PHONE_MIN_LENGTH && $length <= PHONE_MAX_LENGTH;
}

/**
 * Validate date format (Y-m-d)
 * @param string $date Date to validate
 * @return bool True if valid, false otherwise
 */
function validateDate($date) {
    $d = DateTime::createFromFormat('Y-m-d', $date);
    return $d && $d->format('Y-m-d') === $date;
}

/**
 * Validate required fields
 * @param array $fields Array of field names
 * @param array $data Data array to check
 * @return array Array of missing fields
 */
function validateRequired($fields, $data) {
    $missing = [];
    foreach ($fields as $field) {
        if (!isset($data[$field]) || empty($data[$field])) {
            $missing[] = $field;
        }
    }
    return $missing;
}

/**
 * Validate string length
 * @param string $string String to validate
 * @param int $min Minimum length
 * @param int $max Maximum length
 * @return bool True if valid, false otherwise
 */
function validateLength($string, $min, $max) {
    $length = strlen($string);
    return $length >= $min && $length <= $max;
}

/**
 * Validate numeric value
 * @param mixed $value Value to validate
 * @return bool True if valid number, false otherwise
 */
function validateNumeric($value) {
    return is_numeric($value);
}

/**
 * Validate positive number
 * @param mixed $value Value to validate
 * @return bool True if positive, false otherwise
 */
function validatePositive($value) {
    return is_numeric($value) && $value > 0;
}

/**
 * Validate date range
 * @param string $startDate Start date
 * @param string $endDate End date
 * @return bool True if valid range, false otherwise
 */
function validateDateRange($startDate, $endDate) {
    if (!validateDate($startDate) || !validateDate($endDate)) {
        return false;
    }
    return strtotime($startDate) <= strtotime($endDate);
}

// ==============================================
// FILE UPLOAD FUNCTIONS
// ==============================================

/**
 * Validate file upload
 * @param array $file $_FILES array element
 * @param array $allowedTypes Allowed MIME types
 * @param int $maxSize Maximum file size in bytes
 * @return array Result with success status and message
 */
function validateFileUpload($file, $allowedTypes = ALLOWED_IMAGE_TYPES, $maxSize = MAX_FILE_SIZE) {
    // Check if file was uploaded
    if (!isset($file) || $file['error'] === UPLOAD_ERR_NO_FILE) {
        return ['success' => false, 'message' => 'No file uploaded'];
    }
    
    // Check for upload errors
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'File upload error'];
    }
    
    // Check file size
    if ($file['size'] > $maxSize) {
        $maxSizeMB = $maxSize / 1048576;
        return ['success' => false, 'message' => "File size exceeds maximum allowed size of {$maxSizeMB}MB"];
    }
    
    // Check file type
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type'];
    }
    
    // Check file extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_IMAGE_EXTENSIONS)) {
        return ['success' => false, 'message' => 'Invalid file extension'];
    }
    
    return ['success' => true, 'message' => 'File is valid'];
}

/**
 * Upload file to specified directory
 * @param array $file $_FILES array element
 * @param string $uploadDir Upload directory path
 * @param string $prefix Optional filename prefix
 * @return array Result with success status, filename, and message
 */
function uploadFile($file, $uploadDir, $prefix = '') {
    // Validate file
    $validation = validateFileUpload($file);
    if (!$validation['success']) {
        return $validation;
    }
    
    // Create upload directory if it doesn't exist
    if (!file_exists($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate unique filename
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    $filename = $prefix . uniqid() . '_' . time() . '.' . $extension;
    $filepath = $uploadDir . '/' . $filename;
    
    // Move uploaded file
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return [
            'success' => true,
            'filename' => $filename,
            'filepath' => $filepath,
            'message' => 'File uploaded successfully'
        ];
    } else {
        return [
            'success' => false,
            'message' => 'Failed to upload file'
        ];
    }
}

/**
 * Delete file from directory
 * @param string $filepath Full path to file
 * @return bool True if deleted, false otherwise
 */
function deleteFile($filepath) {
    if (file_exists($filepath)) {
        return unlink($filepath);
    }
    return false;
}

// ==============================================
// DATE & TIME FUNCTIONS
// ==============================================

/**
 * Calculate age from date of birth
 * @param string $dob Date of birth (Y-m-d)
 * @return int Age in years
 */
function calculateAge($dob) {
    $birthDate = new DateTime($dob);
    $today = new DateTime('today');
    return $birthDate->diff($today)->y;
}

/**
 * Get date difference in days
 * @param string $date1 First date
 * @param string $date2 Second date
 * @return int Difference in days
 */
function getDateDifference($date1, $date2) {
    $d1 = new DateTime($date1);
    $d2 = new DateTime($date2);
    return $d1->diff($d2)->days;
}

/**
 * Add months to date
 * @param string $date Start date
 * @param int $months Number of months to add
 * @return string New date (Y-m-d)
 */
function addMonthsToDate($date, $months) {
    $dateObj = new DateTime($date);
    $dateObj->modify("+{$months} months");
    return $dateObj->format('Y-m-d');
}

/**
 * Format time elapsed (e.g., "2 hours ago")
 * @param string $datetime Datetime string
 * @return string Formatted elapsed time
 */
function timeElapsed($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'just now';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return $mins . ' minute' . ($mins > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return $hours . ' hour' . ($hours > 1 ? 's' : '') . ' ago';
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return $days . ' day' . ($days > 1 ? 's' : '') . ' ago';
    } else {
        return date('M j, Y', $timestamp);
    }
}

// ==============================================
// STRING MANIPULATION FUNCTIONS
// ==============================================

/**
 * Generate random string
 * @param int $length Length of string
 * @return string Random string
 */
function generateRandomString($length = 10) {
    return bin2hex(random_bytes($length / 2));
}

/**
 * Truncate string with ellipsis
 * @param string $string String to truncate
 * @param int $length Maximum length
 * @param string $append String to append
 * @return string Truncated string
 */
function truncateString($string, $length = 50, $append = '...') {
    if (strlen($string) <= $length) {
        return $string;
    }
    return substr($string, 0, $length) . $append;
}

/**
 * Slugify string (URL-friendly)
 * @param string $string String to slugify
 * @return string Slugified string
 */
function slugify($string) {
    $string = strtolower($string);
    $string = preg_replace('/[^a-z0-9-]/', '-', $string);
    $string = preg_replace('/-+/', '-', $string);
    return trim($string, '-');
}

/**
 * Get initials from name
 * @param string $name Full name
 * @return string Initials
 */
function getInitials($name) {
    $words = explode(' ', $name);
    $initials = '';
    foreach ($words as $word) {
        if (!empty($word)) {
            $initials .= strtoupper($word[0]);
        }
    }
    return substr($initials, 0, 2);
}

// ==============================================
// PAGINATION FUNCTIONS
// ==============================================

/**
 * Calculate pagination data
 * @param int $totalItems Total number of items
 * @param int $currentPage Current page number
 * @param int $itemsPerPage Items per page
 * @return array Pagination data
 */
function calculatePagination($totalItems, $currentPage = 1, $itemsPerPage = ITEMS_PER_PAGE) {
    $totalPages = ceil($totalItems / $itemsPerPage);
    $currentPage = max(1, min($currentPage, $totalPages));
    $offset = ($currentPage - 1) * $itemsPerPage;
    
    return [
        'total_items' => $totalItems,
        'total_pages' => $totalPages,
        'current_page' => $currentPage,
        'items_per_page' => $itemsPerPage,
        'offset' => $offset,
        'has_previous' => $currentPage > 1,
        'has_next' => $currentPage < $totalPages
    ];
}

/**
 * Generate pagination links
 * @param array $pagination Pagination data
 * @param string $baseUrl Base URL for pagination links
 * @return string HTML pagination links
 */
function generatePaginationLinks($pagination, $baseUrl) {
    if ($pagination['total_pages'] <= 1) {
        return '';
    }
    
    $html = '<div class="pagination">';
    
    // Previous button
    if ($pagination['has_previous']) {
        $prevPage = $pagination['current_page'] - 1;
        $html .= '<a href="' . $baseUrl . '?page=' . $prevPage . '" class="pagination-link">&laquo; Previous</a>';
    }
    
    // Page numbers
    $maxLinks = MAX_PAGINATION_LINKS;
    $startPage = max(1, $pagination['current_page'] - floor($maxLinks / 2));
    $endPage = min($pagination['total_pages'], $startPage + $maxLinks - 1);
    
    if ($startPage > 1) {
        $html .= '<a href="' . $baseUrl . '?page=1" class="pagination-link">1</a>';
        if ($startPage > 2) {
            $html .= '<span class="pagination-ellipsis">...</span>';
        }
    }
    
    for ($i = $startPage; $i <= $endPage; $i++) {
        $activeClass = ($i == $pagination['current_page']) ? ' active' : '';
        $html .= '<a href="' . $baseUrl . '?page=' . $i . '" class="pagination-link' . $activeClass . '">' . $i . '</a>';
    }
    
    if ($endPage < $pagination['total_pages']) {
        if ($endPage < $pagination['total_pages'] - 1) {
            $html .= '<span class="pagination-ellipsis">...</span>';
        }
        $html .= '<a href="' . $baseUrl . '?page=' . $pagination['total_pages'] . '" class="pagination-link">' . $pagination['total_pages'] . '</a>';
    }
    
    // Next button
    if ($pagination['has_next']) {
        $nextPage = $pagination['current_page'] + 1;
        $html .= '<a href="' . $baseUrl . '?page=' . $nextPage . '" class="pagination-link">Next &raquo;</a>';
    }
    
    $html .= '</div>';
    return $html;
}

// ==============================================
// ARRAY & DATA FUNCTIONS
// ==============================================

/**
 * Convert array to CSV
 * @param array $data Array of data
 * @param array $headers Optional headers
 * @return string CSV string
 */
function arrayToCSV($data, $headers = []) {
    $output = fopen('php://temp', 'r+');
    
    if (!empty($headers)) {
        fputcsv($output, $headers);
    }
    
    foreach ($data as $row) {
        fputcsv($output, $row);
    }
    
    rewind($output);
    $csv = stream_get_contents($output);
    fclose($output);
    
    return $csv;
}

/**
 * Search in multidimensional array
 * @param array $array Array to search
 * @param string $key Key to search in
 * @param mixed $value Value to search for
 * @return array Matching items
 */
function searchInArray($array, $key, $value) {
    return array_filter($array, function($item) use ($key, $value) {
        return isset($item[$key]) && $item[$key] == $value;
    });
}

// ==============================================
// DEBUGGING FUNCTIONS
// ==============================================

/**
 * Debug dump variable (development only)
 * @param mixed $var Variable to dump
 * @param bool $die Exit after dump
 */
function dd($var, $die = true) {
    if (ENVIRONMENT === 'development') {
        echo '<pre>';
        var_dump($var);
        echo '</pre>';
        if ($die) die();
    }
}

/**
 * Log debug message
 * @param mixed $message Message to log
 * @param string $type Log type
 */
function logDebug($message, $type = 'debug') {
    if (!defined('ENABLE_LOGGING') || !ENABLE_LOGGING) {
        return;
    }
    
    $logFile = LOGS_PATH . '/debug.log';
    $timestamp = date('Y-m-d H:i:s');
    
    if (is_array($message) || is_object($message)) {
        $message = print_r($message, true);
    }
    
    $logEntry = "[{$timestamp}] [{$type}] {$message}" . PHP_EOL;
    file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX);
}
?>
