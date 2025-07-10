<?php

namespace App\Supports;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

class Mailer
{
    protected PHPMailer $mail;

    public function __construct()
    {
        $this->mail = new PHPMailer(true);

        $this->mail->isSMTP();
        $this->mail->Host       = 'smtp.gmail.com';
        $this->mail->SMTPAuth   = true;
        $this->mail->Username   = '3mirabo2909@gmail.com';
        $this->mail->Password   = 'vkwl guzx eyvg cnso';
        $this->mail->SMTPSecure = PHPMailer::ENCRYPTION_STARTTLS;
        $this->mail->Port       = 587;        
        $this->mail->CharSet    = 'UTF-8';
        $this->mail->setFrom($this->mail->Username, '일루코 오더 시스템');

        // Return-Path 설정 (bounce 수신용)
        $this->mail->Sender     = $this->mail->Username;
    }

    /**
     * 단일 메일 발송
     *
     * @param string $toEmail
     * @param string $toName
     * @param string $subject
     * @param string $body
     * @return bool
     */
    public function send(string $toEmail, string $toName, string $subject, string $body): bool
    {
        try {
            $this->mail->clearAllRecipients();
            $this->mail->addAddress($toEmail, $toName);
            $this->mail->Subject = $subject;
            
            if ($this->isHtmlContent($body)) {
                $this->mail->isHTML(true);
            } else {
                $this->mail->isHTML(false);
            }
            
            $this->mail->Body = $body;
            $this->mail->send();
            return true;
        } catch (Exception $e) {
            // 필요하다면 로깅
            // logger()->error($e->getMessage());
            return false;
        }
    }

    /**
     * 여러 명에게 동일 메일 발송
     *
     * @param array $recipients [['email' => ..., 'name' => ...], ...]
     * @param string $subject
     * @param string $body
     * @return int 성공적으로 보낸 수
     */
    public function sendBulk(array $recipients, string $subject, string $body, bool $useBcc = false): bool
    {
        try {
            $this->mail->clearAllRecipients();

            if ($useBcc) {
                // To 필드에 dummy 주소 넣거나, 관리자 본인
                $this->mail->addAddress($this->mail->Username, 'ILLUCO');
                foreach ($recipients as $r) {
                    $this->mail->addBCC($r['email'], $r['name']);
                }
            } else {
                foreach ($recipients as $r) {
                    $this->mail->addAddress($r['email'], $r['name']);
                }
            }

            if ($this->isHtmlContent($body)) {
                $this->mail->isHTML(true);
            } else {
                $this->mail->isHTML(false);
            }

            $this->mail->Subject = $subject;
            $this->mail->Body    = $body;
            $this->mail->send();

            return true;
        } catch (Exception $e) {
            // 필요하다면 로깅
            return false;
        }
    }

    private function isHtmlContent(string $content): bool
    {
        $dom = new \DOMDocument();

        libxml_use_internal_errors(true);
        $loaded = $dom->loadHTML($content, LIBXML_NOWARNING | LIBXML_NOERROR);
        libxml_clear_errors();

        return $loaded && $dom->getElementsByTagName('html')->length > 0;
    }

}
