<?php
function loadFlowers() {
    $json = file_get_contents("flowers.json");
    return json_decode($json, true);
}

$flowers = loadFlowers();
?>
<!DOCTYPE html>
<html>
<head>
    <title>Danh sách hoa</title>
    <style>
        .flower { border: 1px solid #ccc; width: 350px; padding: 10px; margin-bottom: 20px; }
        img { width: 100%; height: 200px; object-fit: cover; }
    </style>
</head>

<body>
<h1>🌸 Danh sách Hoa</h1>

<?php foreach ($flowers as $f): ?>
    <div class="flower">
        <img src="../images/<?php echo $f['image']; ?>">
        <h2><?php echo $f['name']; ?></h2>
        <p><?php echo $f['description']; ?></p>
    </div>
<?php endforeach; ?>

</body>
</html>
