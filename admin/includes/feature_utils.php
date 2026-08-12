<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!function_exists('qf_escape')) {
    function qf_escape($value): string {
        return htmlspecialchars((string)($value ?? ''), ENT_QUOTES, 'UTF-8');
    }
}

if (!function_exists('qf_int')) {
    function qf_int($value): int {
        return (int)($value ?? 0);
    }
}

if (!function_exists('qf_upload_image')) {
    function qf_upload_image(array $file, string $folder = 'uploads/homepage_media'): ?string {
        if (empty($file) || !isset($file['error']) || $file['error'] === UPLOAD_ERR_NO_FILE) {
            return null;
        }

        if ($file['error'] !== UPLOAD_ERR_OK) {
            throw new RuntimeException('Upload failed.');
        }

        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];
        $ext = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        if (!in_array($ext, $allowed, true)) {
            throw new RuntimeException('Only JPG, PNG, WEBP and GIF files are allowed.');
        }

        $root = dirname(__DIR__, 2);
        $uploadDir = rtrim($root . '/' . $folder, '/');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0775, true);
        }

        $filename = 'asset_' . bin2hex(random_bytes(8)) . '.' . $ext;
        $target = $uploadDir . '/' . $filename;

        if (!move_uploaded_file($file['tmp_name'], $target)) {
            throw new RuntimeException('Could not move uploaded file.');
        }

        return $folder . '/' . $filename;
    }
}

if (!function_exists('qf_status_badge')) {
    function qf_status_badge(string $status): array {
        $status = strtolower(trim($status));
        return match ($status) {
            'approved' => ['class' => 'bg-success', 'label' => 'Approved'],
            'rejected' => ['class' => 'bg-danger', 'label' => 'Rejected'],
            'pickup_scheduled' => ['class' => 'bg-primary', 'label' => 'Pickup Scheduled'],
            'received' => ['class' => 'bg-info text-dark', 'label' => 'Received'],
            'refunded' => ['class' => 'bg-success', 'label' => 'Refunded'],
            'completed' => ['class' => 'bg-secondary', 'label' => 'Completed'],
            default => ['class' => 'bg-warning text-dark', 'label' => 'Pending'],
        };
    }
}
?>
