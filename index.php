<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Key Service</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
        }
        .container {
            background: rgba(0, 0, 0, 0.8);
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.5);
            text-align: center;
        }
        h1 {
            margin-bottom: 20px;
        }
        p {
            margin: 10px 0;
        }
        button {
            padding: 10px 20px;
            border: none;
            border-radius: 5px;
            background-color: #667eea;
            color: white;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s;
        }
        button:hover {
            background-color: #764ba2;
        }
        .key-display {
            margin-top: 20px;
            padding: 10px;
            background-color: #2d3748;
            border-radius: 5px;
            word-break: break-all;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>Welcome to boggle.cc key system service!</h1>

        <?php
        $banned_file = 'banned-ip.json';
        $ip_address = $_SERVER['REMOTE_ADDR'];

        $banned_ips = file_exists($banned_file) ? json_decode(file_get_contents($banned_file), true) : [];
        if (in_array($ip_address, $banned_ips)) {
            echo "<p>Your IP has been banned for bypassing: Time: Forever</p>";
            exit;
        }

        if (isset($_GET['hash'])) {
            $hash = $_GET['hash'];

            $auth_token = 'ANTI_BYPASSING_TOKEN'; // Replace with your actual token
            $api_url = "https://publisher.linkvertise.com/api/v1/anti_bypassing?token=" . $auth_token . "&hash=" . htmlspecialchars($hash);

            $ch = curl_init($api_url);
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_POST, true);
            curl_setopt($ch, CURLOPT_POSTFIELDS, [
                'token' => $auth_token,
                'hash' => $hash
            ]);
            
            $api_response = curl_exec($ch);
            curl_close($ch);

            if ($api_response === false) {
                echo "<p>Error: Failed to connect to Linkvertise. Please try again later.</p>";
            } else {
                $api_result = json_decode($api_response, true);

                if (isset($api_result['status']) && $api_result['status'] === true) {
                    $file_path = 'keys.json';

                    $key = bin2hex(random_bytes(16));
                    $expiration = time() + 86400;

                    $keys = file_exists($file_path) ? json_decode(file_get_contents($file_path), true) : [];
                    $keys[$key] = [
                        'ip_address' => $ip_address,
                        'expiration' => $expiration
                    ];

                    file_put_contents($file_path, json_encode($keys));

                    echo "<p class='key-display'>Key generated: <strong>" . htmlspecialchars($key) . "</strong></p>";
                    echo "<p>Access granted! You came through the Linkvertise target.</p>";
                } elseif (isset($api_result['status']) && $api_result['status'] === false) {
                    echo "<p>Secured Key is invalid or expired. Access denied.</p>";
                } else {
                    $banned_ips[] = $ip_address;
                    file_put_contents($banned_file, json_encode($banned_ips));

                    echo "<p>Your IP has been banned for bypassing: Time: Forever (" . date('Y-m-d H:i:s') . ")</p>";
                }
            }
        } else {
            echo '<p>To get the key, please complete the process at Linkvertise:</p>';
            echo '<form method="get" action="LINKVERTISE_URL">';
            echo '<button type="submit">Get Key</button>';
            echo '</form>';
        }
        ?>
    </div>
</body>
</html>