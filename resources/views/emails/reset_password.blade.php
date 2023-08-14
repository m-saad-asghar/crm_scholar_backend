<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Password Reset</title>
</head>
<body>
    <table style="max-width: 600px; margin: 0 auto; padding: 20px; border-collapse: collapse;">
        <tr>
            <td style="background-color: #f2f2f2; padding: 10px; text-align: center;">
                <h2>Reset Your Password</h2>
            </td>
        </tr>
        <tr>
            <td style="padding: 20px;">
                <p>Hello {{ $data['user_name'] }},</p>
                <p>You are receiving this email because we received a password reset request for your account. If you did not request a password reset, please ignore this email.</p>
                <p>To reset your password, click on the button below:</p>
                <p style="text-align: center;">
                    <a href={{ $data['reset_password_link'] }} style="display: inline-block; background-color: #007BFF; color: #fff; text-decoration: none; padding: 10px 20px; border-radius: 5px;">Reset Password</a>
                </p>
                <p>If the button doesn't work, you can also copy and paste the following link into your web browser:</p>
                <p>{{ $data['reset_password_link'] }}</p>
                <p>If you have any questions or need assistance, please contact our support team at {{env('RESET_PASSWORD_SENDER_EMAIL')}}.</p>
                <p>Thank you!</p>
            </td>
        </tr>
        <tr>
            <td style="background-color: #f2f2f2; padding: 10px; text-align: center;">
                <p style="font-size: 12px;">This email was sent from <b>Scholar Publishers</b></p>
            </td>
        </tr>
    </table>
</body>
</html>