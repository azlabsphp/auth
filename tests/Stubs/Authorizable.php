<?php

namespace Drewlabs\Auth\Tests\Stubs;

/**
 * @property string[] authorizations
 * @property string[] authorization_groups
 */
trait Authorizable
{
    public function getAuthorizations(): array
    {
        return $this->authorizations ?? [];
    }

    public function getAuthorizationGroups(): array
    {
        return $this->authorization_groups ?? [];
    }

    public function setAuthorizations(array $value = [])
    {
        $this->authorizations = $value;
    }

    public function setAuthorizationGroups(array $value = [])
    {
        $this->authorization_groups = $value;
    }

    /**
     * Determine if the entity has a given ability.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function can($ability, $arguments = [])
    {
        return in_array($ability, $this->getAuthorizations());
    }

    /**
     * Determine if the entity does not have a given ability.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function cant($ability, $arguments = [])
    {
        return !$this->can($ability, $arguments);
    }

    /**
     * Determine if the entity does not have a given ability.
     *
     * @param  string  $ability
     * @param  array|mixed  $arguments
     * @return bool
     */
    public function cannot($ability, $arguments = [])
    {
        return $this->cant($ability, $arguments);
    }
}
