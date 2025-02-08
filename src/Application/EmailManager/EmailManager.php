<?php

namespace App\Application\EmailManager;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// mail : smtpprovider180@gmail.com
// mdp : CoinkCoink123
// app mdp : qpjh nfug cyva snyq

final class EmailManager
{
    private const MY_EMAIL = 'smtpprovider180@gmail.com';
    private const MY_APP_PASSWORD = 'qpjhnfugcyvasnyq'; // hahaha i dont give a shit

    /**
     * Sends an email to the specified address using PHPMailer.
     *
     * @param string $email   The recipient's email address.
     * @param string $subject The email subject.
     * @param string $content The email content.
     *
     * @return bool Returns true if the email was successfully sent.
     *
     * @throws \InvalidArgumentException If any parameter is empty or the email address is invalid.
     * @throws \RuntimeException If the email fails to send.
     */
    public function send(string $email, string $subject, string $content): bool
    {
        // Validate that none of the parameters are empty.
        if (empty($email) || empty($subject) || empty($content))
        {
            throw new \InvalidArgumentException('Email, subject, and content must not be empty.');
        }

        // Validate the email format.
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            throw new \InvalidArgumentException("Invalid email address: $email");
        }

        // Instantiate PHPMailer
        $mail = new PHPMailer(true);

        try
        {
            echo "[ INFO] Sending email to " . $email . PHP_EOL;
            // Set PHPMailer to use SMTP
            $mail->isSMTP();
            $mail->Host       = 'smtp.gmail.com';
            $mail->SMTPAuth   = true;
            $mail->Username   = self::MY_EMAIL;
            $mail->Password   = self::MY_APP_PASSWORD;
            $mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS; // Use TLS encryption
            $mail->Port       = 587; // TCP port for TLS

            // Set sender information
            $mail->setFrom(self::MY_EMAIL, 'CoinkCoink');

            // Add recipient
            $mail->addAddress($email);

            // Set email format to plain text (set to true for HTML emails)
            $mail->isHTML(false);
            $mail->Subject = $subject;
            $mail->Body    = $content;

            // Send the email
            $mail->send();

            return true;
        }
        catch (Exception $e)
        {
            throw new \RuntimeException("Failed to send email to $email: " . $mail->ErrorInfo);
        }
    }
}