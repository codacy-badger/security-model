<?php

declare(strict_types=1);

namespace StephBug\SecurityModel\Guard\Authorization\Voter;

use StephBug\SecurityModel\Guard\Authentication\Token\Tokenable;

class RoleVoter extends AccessVoter
{
    /**
     * @var string
     */
    private $rolePrefix;

    public function __construct(string $rolePrefix)
    {
        $this->rolePrefix = $rolePrefix;
    }

    public function vote(Tokenable $token, object $subject, array $attributes): int
    {
        $vote = $this->abstain();
        $roles = $this->extractRoles($token);

        foreach ($attributes as $attribute) {
            if (0 !== strpos($attribute, $this->rolePrefix)) {
                continue;
            }

            $vote = $this->deny();

            foreach ($roles as $role) {
                if ($attribute === $role->getRole()) {
                    return $this->grant();
                }
            }
        }

        return $vote;
    }

    protected function extractRoles(Tokenable $token): array
    {
        return $token->getRoles()->all();
    }
}