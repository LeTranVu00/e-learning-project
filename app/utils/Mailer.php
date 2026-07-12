<?php
// File: app/utils/Mailer.php

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

require_once __DIR__ . '/../../vendor/autoload.php';

class Mailer {
    
    private static function getMailerInstance() {
        $mail = new PHPMailer(true);
        try {
            // Server settings
            $mail->isSMTP();
            $mail->Host       = $_ENV['SMTP_HOST'];
            $mail->SMTPAuth   = true;
            $mail->Username   = $_ENV['SMTP_USER'];
            $mail->Password   = $_ENV['SMTP_PASS'];
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_SMTPS;
            $mail->Port       = $_ENV['SMTP_PORT'];
            $mail->CharSet    = 'UTF-8';

            // Recipients
            $mail->setFrom($_ENV['SMTP_USER'], $_ENV['SMTP_FROM_NAME'] ?? 'E-Learning System');

            return $mail;
        } catch (Exception $e) {
            return null;
        }
    }

    public static function sendWelcomeEmail($toEmail, $fullname) {
        $mail = self::getMailerInstance();
        if (!$mail) return false;

        try {
            $mail->addAddress($toEmail, $fullname);
            
            $mail->isHTML(true);
            $mail->Subject = 'Chào mừng bạn đến với E-Learning!';
            
            $body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;'>
                    <h2 style='color: #2563eb; text-align: center;'>Chào mừng {$fullname}!</h2>
                    <p>Cảm ơn bạn đã đăng ký tài khoản tại nền tảng học trực tuyến của chúng tôi.</p>
                    <p>Giờ đây bạn có thể khám phá hàng ngàn khóa học chất lượng và tham gia vào cộng đồng học viên lớn mạnh.</p>
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='http://localhost/e-learning-project/public/index.php?action=login' style='background-color: #2563eb; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Đăng Nhập Ngay</a>
                    </div>
                    <p style='color: #666; font-size: 14px;'>Chúc bạn có những giờ học thú vị và hiệu quả!</p>
                </div>
            ";
            
            $mail->Body = $body;
            $mail->AltBody = "Chào mừng {$fullname}! Cảm ơn bạn đã đăng ký tài khoản tại hệ thống của chúng tôi. Hãy đăng nhập để bắt đầu học tập.";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }

    public static function sendPasswordResetEmail($toEmail, $resetLink) {
        $mail = self::getMailerInstance();
        if (!$mail) return false;

        try {
            $mail->addAddress($toEmail);
            
            $mail->isHTML(true);
            $mail->Subject = 'Khôi phục mật khẩu - E-Learning System';
            
            $body = "
                <div style='font-family: Arial, sans-serif; max-width: 600px; margin: 0 auto; padding: 20px; border: 1px solid #ddd; border-radius: 10px;'>
                    <h2 style='color: #d97706; text-align: center;'>Khôi phục mật khẩu</h2>
                    <p>Chúng tôi nhận được yêu cầu khôi phục mật khẩu cho tài khoản của bạn.</p>
                    <p>Vui lòng click vào nút bên dưới để tiến hành đặt lại mật khẩu mới. Đường dẫn này chỉ có hiệu lực trong vòng 1 giờ.</p>
                    <div style='text-align: center; margin: 30px 0;'>
                        <a href='{$resetLink}' style='background-color: #d97706; color: white; padding: 12px 25px; text-decoration: none; border-radius: 5px; font-weight: bold;'>Đặt Lại Mật Khẩu</a>
                    </div>
                    <p style='color: #666; font-size: 14px;'>Nếu bạn không yêu cầu thay đổi mật khẩu, vui lòng bỏ qua email này.</p>
                </div>
            ";
            
            $mail->Body = $body;
            $mail->AltBody = "Để khôi phục mật khẩu, vui lòng truy cập đường dẫn sau: {$resetLink} (Đường dẫn có hiệu lực 1 giờ). Nếu bạn không yêu cầu thay đổi mật khẩu, vui lòng bỏ qua.";

            $mail->send();
            return true;
        } catch (Exception $e) {
            error_log("Message could not be sent. Mailer Error: {$mail->ErrorInfo}");
            return false;
        }
    }
}
?>
