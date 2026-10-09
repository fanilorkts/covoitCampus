<?php

namespace App\Security\Voter;

use App\Entity\Utilisateurs;
use App\Entity\Vehicule;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Core\Authorization\Voter\Vote;
use Symfony\Component\Security\Core\Authorization\Voter\Voter;

/**
 * Seul le propriétaire d'un véhicule peut le modifier ou le supprimer.
 *
 * @extends Voter<string, Vehicule>
 */
final class VehiculeVoter extends Voter
{
    public const EDIT = 'VEHICULE_EDIT';
    public const DELETE = 'VEHICULE_DELETE';

    protected function supports(string $attribute, mixed $subject): bool
    {
        return \in_array($attribute, [self::EDIT, self::DELETE], true) && $subject instanceof Vehicule;
    }

    protected function voteOnAttribute(string $attribute, mixed $subject, TokenInterface $token, ?Vote $vote = null): bool
    {
        $user = $token->getUser();

        return $user instanceof Utilisateurs
            && $subject->getProprietaire()?->getId() === $user->getId();
    }
}
