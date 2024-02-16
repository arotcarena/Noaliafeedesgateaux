<?php

namespace App\Validator;

use Symfony\Bundle\SecurityBundle\Security;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\User\PasswordAuthenticatedUserInterface;

class CurrentPasswordValidator extends ConstraintValidator
{
    public function __construct(
        private UserPasswordHasherInterface $hasher,
        private Security $security
    )
    {
    }
    public function validate($value, Constraint $constraint)
    {
        /* @var App\Validator\CurrentPassword $constraint */

        if (null === $value || '' === $value) {
            return;
        }

        /** @var PasswordAuthenticatedUserInterface */
        $user = $this->security->getUser();
        if(!$this->hasher->isPasswordValid($user, $value))
        {
            $this->context->buildViolation($constraint->message)
            ->setParameter('{{ value }}', $value)
            ->addViolation();
        }
    }
}
