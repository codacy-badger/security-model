<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authorization\Voter;

use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

abstract class Voter extends AccessVoter
{
    public function vote(Tokenable $token, array $attributes, $subject = null): int
    {
        $vote = $this->abstain();

        foreach ($attributes as $attribute) {
            if (!$this->supports($attribute, $subject)) {
                continue;
            }

            $vote = $this->deny();

            if ($this->voteOn($attribute, $token, $subject)) {
                return $this->grant();
            }
        }

        return $vote;
    }

    abstract protected function supports(string $attribute, $subject): bool;

    abstract protected function voteOn(string $attribute, Tokenable $token, $subject): bool;
}