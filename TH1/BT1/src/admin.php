<?php
//xu li du lieu

function loadFlowers() {
    $json = file_get_contents("flowers.json");
    return json_decode($json, true);
}

function saveFlowers($flowers) {
    file_put_contents("flowers.json", json_encode($flowers, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
}

//load du lieu
$flowers = loadFlowers();

//them
if (isset($_POST['add'])) {
    $id = end($flowers)['id'] + 1;
    $name = $_POST['name'];
    $desc = $_POST['description'];

    //up load anh
    $imgName = $_FILES['image']['name'];
    move_uploaded_file($_FILES['image']['tmp_name'], "images/" . $imgName);

    $flowers[] = [
        "id" => $id,
        "name" => $name,
        "description" => $desc,
        "image" => $imgName
    ];

    saveFlowers($flowers);
    header("Location: admin.php");
    exit;
}

//xoa
if (isset($_GET['delete'])) {
    $id = $_GET['delete'];

    $flowers = array_filter($flowers, fn($f) => $f['id'] != $id);

    saveFlowers(array_values($flowers));
    header("Location: admin.php");
    exit;
}

//cap nhat
if (isset($_POST['update'])) {
    $id = $_POST['id'];

    foreach ($flowers as &$f) {
        if ($f['id'] == $id) {
            $f['name'] = $_POST['name'];
            $f['description'] = $_POST['description'];

            if ($_FILES['image']['name'] !== "") {
                $imgName = $_FILES['image']['name'];
                move_uploaded_file($_FILES['image']['tmp_name'], "images/" . $imgName);
                $f['image'] = $imgName;
            }
            break;
        }
    }

    saveFlowers($flowers);
    header("Location: admin.php");
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Quản lý hoa</title>
    <style>
        table { border-collapse: collapse; width: 100%; }
        th, td { border: 1px solid #333; padding: 8px; text-align: center; }
        img { width: 100px; height: 80px; object-fit: cover; }
        form { margin: 20px 0; }
    </style>
</head>

<body>
<h1>🔧 Quản lý Hoa</h1>

<hr>

<!-- form them hoa -->
<h2>➕ Thêm Hoa Mới</h2>
<form method="POST" enctype="multipart/form-data">
    Tên hoa: <input type="text" name="name" required><br><br>
    Mô tả: <textarea name="description" required></textarea><br><br>
    Ảnh: <input type="file" name="image" required><br><br>
    <button name="add">Thêm</button>
</form>

<hr>

<!-- bang hien thi -->
<h2>📋 Danh sách Hoa</h2>

<table>
    <tr>
        <th>Ảnh</th>
        <th>Tên</th>
        <th>Mô tả</th>
        <th>Sửa</th>
        <th>Xóa</th>
    </tr>

    <?php foreach ($flowers as $f): ?>
        <tr>
            <td><img src="../images/<?php echo $f['image']; ?>"></td>
            <td><?php echo $f['name']; ?></td>
            <td><?php echo $f['description']; ?></td>
            <td>
                <!-- nut sua -->
                <button onclick="showEdit(<?php echo $f['id']; ?>)">Sửa</button>
            </td>
            <td>
                <a onclick="return confirm('Xóa hoa này?');"
                   href="admin.php?delete=<?php echo $f['id']; ?>">Xóa</a>
            </td>
        </tr>

        <!-- form sua -->
        <tr id="edit-<?php echo $f['id']; ?>" style="display:none;">
            <td colspan="5">
                <form method="POST" enctype="multipart/form-data">
                    <input type="hidden" name="id" value="<?php echo $f['id']; ?>">

                    Tên hoa: <input type="text" name="name" value="<?php echo $f['name']; ?>"><br><br>
                    Mô tả: <textarea name="description"><?php echo $f['description']; ?></textarea><br><br>

                    Ảnh mới (tùy chọn): <input type="file" name="image"><br><br>

                    <button name="update">Cập nhật</button>
                </form>
            </td>
        </tr>
    <?php endforeach; ?>
</table>

<script>
function showEdit(id) {
    document.getElementById('edit-' + id).style.display =
        document.getElementById('edit-' + id).style.display === 'none'
        ? 'table-row' : 'none';
}
</script>

</body>
</html>
