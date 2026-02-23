<?php
// validate.php - Versi PostgreSQL
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');

$key = $_GET['key'] ?? '';
$username = $_GET['username'] ?? '';

if (empty($key) || empty($username)) {
    die(json_encode([
        'valid' => false, 
        'message' => 'Key dan username harus diisi'
    ]));
}

// Konek ke database dari environment variable
$db_conn = pg_connect(getenv('DATABASE_URL'));

if (!$db_conn) {
    die(json_encode(['valid' => false, 'message' => 'Database error']));
}

// Cek key
$result = pg_query_params($db_conn, 
    "SELECT * FROM keys WHERE key_code = $1", 
    [$key]
);

if (pg_num_rows($result) == 0) {
    echo json_encode(['valid' => false, 'message' => 'Key tidak ditemukan']);
    exit;
}

$row = pg_fetch_assoc($result);

if ($row['used'] && $row['username'] == $username) {
    // Key udah dipake user ini
    echo json_encode(['valid' => true, 'message' => 'Key valid']);
}
elseif (!$row['used']) {
    // Key belum dipake, ikat ke username
    pg_query_params($db_conn,
        "UPDATE keys SET used = true, username = $1, used_at = NOW() WHERE key_code = $2",
        [$username, $key]
    );
    echo json_encode(['valid' => true, 'message' => "Key valid dan terikat ke @$username"]);
}
else {
    echo json_encode(['valid' => false, 'message' => 'Key sudah dipake user ' . $row['username']]);
}

pg_close($db_conn);
?>
