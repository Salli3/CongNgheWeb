<?php
// Đường dẫn file CSV
$csvFile = __DIR__ . '/65HTTT_Danh_sach_diem_danh.csv';

// Mở file
if (!file_exists($csvFile)) {
    die("File CSV không tồn tại.");
}

// Đọc dữ liệu CSV
$rows = [];
if (($handle = fopen($csvFile, "r")) !== false) {
    // Lấy tiêu đề cột
    $header = fgetcsv($handle, 1000, ",");
    // Lấy các dòng dữ liệu
    while (($data = fgetcsv($handle, 1000, ",")) !== false) {
        $rows[] = array_combine($header, $data);
    }
    fclose($handle);
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Danh sách sinh viên</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #ccc; padding: 8px; text-align: left; }
        th { background: #f0f0f0; }
    </style>
</head>
<body>
    <h1>Danh sách sinh viên</h1>
    <table>
        <thead>
            <tr>
                <?php foreach ($header as $col): ?>
                    <th><?php echo htmlspecialchars($col); ?></th>
                <?php endforeach; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($rows as $row): ?>
                <tr>
                    <?php foreach ($header as $col): ?>
                        <td><?php echo htmlspecialchars($row[$col]); ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</body>
</html>
