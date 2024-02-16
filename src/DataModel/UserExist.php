<?php
namespace App\DataModel;

use App\Validator\EmailExist;
use Symfony\Component\Validator\Constraints as Assert;

class UserExist
{
    #[EmailExist(['message' => 'L\'adresse email "{{ value }}" ne correspond à aucun compte utilisateur'])]
    #[Assert\NotBlank(message: 'Veuillez renseignez une adresse email')]
    public $email;
}