<?php

namespace App\Entity;


class PasswordForgot
{    
    
    private $email;
 

    public function getEmail(): ?string
    {
        return $this->email;
    }

    public function setEmail(string $email): self
    {
        $this->email = $email;

        return $this;
    }
}
