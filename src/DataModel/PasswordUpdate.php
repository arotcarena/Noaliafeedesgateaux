<?php
namespace App\DataModel;

use App\Validator\CurrentPassword;
use Symfony\Component\Validator\Constraints as Assert;

class PasswordUpdate
{
    #[CurrentPassword(['message' => 'Le mot de passe entré ne correspond pas à votre mot de passe actuel'])]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    public ?string $currentPassword;

    #[Assert\Length(
        min: 6,
        max: 50,
        minMessage: 'Le mot de passe doit comporter {{ limit }} caractères au minimum',
        maxMessage: 'Le mot de passe ne peut comporter plus de {{ limit }} caractères',
    )]
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    #[Assert\EqualTo(propertyPath: 'passwordConfirm', message: 'Les deux mots de passe ne correspondent pas')]
    public ?string $newPassword;

    
    #[Assert\NotBlank(message: 'Ce champ est obligatoire')]
    #[Assert\EqualTo(propertyPath: 'newPassword', message: 'Les deux mots de passe ne correspondent pas')]
    public ?string $passwordConfirm;
}