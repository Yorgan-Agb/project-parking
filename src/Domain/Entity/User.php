<?php

declare(strict_types=1);

namespace App\Domain\Entity;

class User
{
    public readonly string $id;
    public readonly string $email;
    public readonly string $password;
    public readonly string $firstName;
    public readonly string $lastName;

    public function __construct(string $id, string $email, string $password, string $firstName, string $lastName)
    {
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new \InvalidArgumentException('Invalid email address');
        }

        $this->id = $id;
        $this->email = $email;
        $this->password = $password;
        $this->firstName = $firstName;
        $this->lastName = $lastName;
    }
}
