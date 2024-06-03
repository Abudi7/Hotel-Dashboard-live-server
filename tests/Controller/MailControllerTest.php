<?php
// tests/Controller/MailControllerTest.php
namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class MailControllerTest extends WebTestCase
{
    public function testSendEmail()
    {
        $client = static::createClient();
        $container = static::getContainer();
        
        /** @var MailerInterface $mailer */
        $mailer = $container->get(MailerInterface::class);

        $email = (new Email())
            ->from('info@mail.hotel-dashboard.at')
            ->to('casper.king14@gmail.com')
            ->subject('Test Email')
            ->text('This is a test email.');

        $mailer->send($email);

        $client->request('GET', '/send-email');

        $this->assertResponseIsSuccessful();
        $this->assertStringContainsString('Email sent successfully!', $client->getResponse()->getContent());
    }
}
