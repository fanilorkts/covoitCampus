<?php

namespace App\Tests\Entity;

use App\Entity\Vehicule;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;
use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\ConstraintValidator;
use Symfony\Component\Validator\ConstraintValidatorFactory;
use Symfony\Component\Validator\ConstraintValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationListInterface;
use Symfony\Component\Validator\Validation;

final class VehiculeTest extends TestCase
{
    #[DataProvider('plaques')]
    public function testImmatriculationEstNormalisee(string $saisie, string $attendu): void
    {
        $this->assertSame($attendu, (new Vehicule())->setImmatriculation($saisie)->getImmatriculation());
    }

    /** @return iterable<string, array{string, string}> */
    public static function plaques(): iterable
    {
        yield 'minuscules' => ['ab-123-cd', 'AB-123-CD'];
        yield 'espaces' => [' ab 123  cd ', 'AB-123-CD'];
        yield 'déjà valide' => ['AB-123-CD', 'AB-123-CD'];
    }

    public function testCouleurVideDevientNull(): void
    {
        $this->assertNull((new Vehicule())->setCouleur('   ')->getCouleur());
        $this->assertSame('Blanc', (new Vehicule())->setCouleur(' Blanc ')->getCouleur());
    }

    public function testValidationAccepteUnVehiculeCorrect(): void
    {
        $this->assertCount(0, $this->valider($this->vehiculeValide()));
    }

    public function testValidationRefuseUneAnneeHorsLimites(): void
    {
        $this->assertCount(1, $this->valider($this->vehiculeValide()->setAnnee(1900)));
        $this->assertCount(1, $this->valider($this->vehiculeValide()->setAnnee((int) date('Y') + 5)));
        $this->assertCount(0, $this->valider($this->vehiculeValide()->setAnnee(2020)));
    }

    public function testValidationRefusePlaqueInvalideEtPlacesHorsLimites(): void
    {
        $this->assertCount(1, $this->valider($this->vehiculeValide()->setImmatriculation('AB@123')));
        $this->assertCount(1, $this->valider($this->vehiculeValide()->setNbPlaces(1)));
        $this->assertCount(1, $this->valider($this->vehiculeValide()->setNbPlaces(12)));
    }

    private function vehiculeValide(): Vehicule
    {
        return (new Vehicule())
            ->setMarque('Volkswagen')
            ->setModele('Golf')
            ->setImmatriculation('AB-123-CD')
            ->setNbPlaces(5);
    }

    private function valider(Vehicule $vehicule): ConstraintViolationListInterface
    {
        // UniqueEntity a besoin de la base : on le remplace par un validateur sans effet pour ce test unitaire.
        $factory = new class extends ConstraintValidatorFactory {
            public function getInstance(Constraint $constraint): ConstraintValidatorInterface
            {
                if ($constraint instanceof UniqueEntity) {
                    return new class extends ConstraintValidator {
                        public function validate(mixed $value, Constraint $constraint): void
                        {
                        }
                    };
                }

                return parent::getInstance($constraint);
            }
        };

        return Validation::createValidatorBuilder()
            ->enableAttributeMapping()
            ->setConstraintValidatorFactory($factory)
            ->getValidator()
            ->validate($vehicule);
    }
}
