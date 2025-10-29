<?php
$error_code = $_GET['code'] ?? '404';
$error_messages = [
    '404' => 'Page Not Found',
    '500' => 'Internal Server Error',
    '403' => 'Access Forbidden'
];

$error_title = $error_messages[$error_code] ?? 'Error';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $error_code; ?> - <?php echo $error_title; ?></title>
    <link rel="stylesheet" href="assets/css/fonts.css?v=3.1">
    <link rel="stylesheet" href="assets/css/style.css?v=3.1">
    <link rel="stylesheet" href="https://fonts.googleapis.com/css2?family=Material+Symbols+Rounded:opsz,wght,FILL,GRAD@20..48,100..700,0..1,-50..200" />
    <link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 24 24'><text y='20' font-size='20'>🏋️</text></svg>">
</head>
<body>
    <div class="error-container">
        <div class="error-content">
            <h1 class="error-code"><?php echo $error_code; ?></h1>
            <h2 class="error-title"><?php echo $error_title; ?></h2>
            <p class="error-description">
                <?php
                switch($error_code) {
                    case '404':
                        echo "The page you're looking for doesn't exist or has been moved.";
                        break;
                    case '500':
                        echo "Something went wrong on our end. Please try again later.";
                        break;
                    case '403':
                        echo "You don't have permission to access this resource.";
                        break;
                    default:
                        echo "An unexpected error occurred.";
                }
                ?>
            </p>
            <div class="error-actions">
                <a href="index.php" class="btn btn-primary">
                    <span>Go Home</span>
                </a>
                <a href="javascript:history.back()" class="btn btn-secondary">
                    <span>← Go Back</span>
                </a>
            </div>
        </div>
    </div>
    
    <style>
        .error-container {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: var(--black);
            padding: var(--space-4);
        }
        
        .error-content {
            text-align: center;
            max-width: 500px;
        }
        
        .error-icon {
            font-size: var(--text-6xl);
            margin-bottom: var(--space-6);
        }
        
        .error-code {
            font-size: var(--text-6xl);
            font-weight: var(--font-semibold);
            color: var(--white);
            margin-bottom: var(--space-4);
            line-height: 1;
        }
        
        .error-title {
            font-size: var(--text-2xl);
            font-weight: var(--font-semibold);
            color: var(--gray-300);
            margin-bottom: var(--space-4);
        }
        
        .error-description {
            font-size: var(--text-lg);
            color: var(--gray-400);
            margin-bottom: var(--space-8);
            line-height: 1.6;
        }
        
        .error-actions {
            display: flex;
            gap: var(--space-4);
            justify-content: center;
            flex-wrap: wrap;
        }
    </style>
</body>
</html>
