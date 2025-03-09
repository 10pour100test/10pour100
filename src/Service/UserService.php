<?php

namespace App\Service;

use App\Entity\User;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Email;

class UserService
{
    private UserPasswordHasherInterface $passwordHasher;
    private ValidatorInterface $validator;

    public function __construct(UserPasswordHasherInterface $passwordHasher, ValidatorInterface $validator)
    {
        $this->passwordHasher = $passwordHasher;
        $this->validator = $validator;
    }

    public function createUser(string $numeroTelephone, string $plainPassword): User
    {
        if (empty($plainPassword)) {
            throw new \InvalidArgumentException('Le mot de passe ne peut pas être vide.');
        }

        if (!preg_match('/^\+?[0-9]{10,15}$/', $numeroTelephone)) {
            throw new \InvalidArgumentException('Le numéro de téléphone est invalide.');
        }

        $user = new User();
        $user->setNumeroTelephonePrincipal($numeroTelephone);
        $hashedPassword = $this->passwordHasher->hashPassword($user, $plainPassword);
        $user->setPassword($hashedPassword);

        return $user;
    }

class EmailVerificationService
{
    public function __construct(private MailerInterface $mailer) {}

    public function sendOtp(string $email, string $otp): void
    {
        $message = (new Email())
            ->from('no-reply@10pour100.com')
            ->to($email)
            ->subject('Votre code de vérification')
            ->text("Votre code OTP est : $otp");

        $this->mailer->send($message);
    }
}
