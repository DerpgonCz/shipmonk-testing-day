<?php

declare(strict_types=1);

namespace App\Validation;

use Symfony\Component\Validator\Constraint;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Validator\Exception\ValidationFailedException;
use Symfony\Component\Validator\Validation;

class PackagingRequestValidator
{
    /**
     * @throws \Symfony\Component\Validator\Exception\ValidationFailedException
     */
    public function validate(array $data): void
    {
        $validator = Validation::createValidator();
        $violations = $validator->validate($data, $this->getConstraint());

        if (count($violations) === 0) {
            return;
        }

        throw new ValidationFailedException($data, $violations);
    }

    private function getConstraint(): Constraint
    {
        return new Assert\Collection([
            'products' => [
                new Assert\Count(null, 1),
                new Assert\All([
                    new Assert\Collection([
                        'id' => new Assert\GreaterThanOrEqual(1),
                        'width' => new Assert\GreaterThan(0),
                        'height' => new Assert\GreaterThan(0),
                        'length' => new Assert\GreaterThan(0),
                        'weight' => new Assert\GreaterThan(0),
                    ]),
                ])
            ],
        ]);
    }
}
