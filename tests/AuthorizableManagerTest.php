<?php

use Drewlabs\Auth\AuthorizableManager;
use Drewlabs\Auth\Tests\Stubs\Authenticatable;
use PHPUnit\Framework\TestCase;

class AuthorizableManagerTest extends TestCase
{
    public function test_authorizable_manager_is_administrator_returns_false_if_user_does_not_have_administrator_scopes()
    {

        // Initialize
        $user = Authenticatable::createFromAttributes(['authorizations' => ['all']]);

        $authorizable = new AuthorizableManager();

        // Act
        $result = $authorizable->isAdmin($user);

        // Assert
        $this->assertFalse($result);
    }

    public function test_authorizable_manager_is_administrator_returns_true_if_user_has_defined_admin_scopes()
    {

        // Initialize
        $user = Authenticatable::createFromAttributes(['authorizations' => ['all']]);

        $authorizable = new AuthorizableManager(['all']);

        // Act
        $result = $authorizable->isAdmin($user);

        // Assert
        $this->assertTrue($result);
    }

    public function test_authorizable_has_authorization_returns_true_if_user_has_scope()
    {
        // Initialize
        $user = Authenticatable::createFromAttributes(['authorizations' => ['app:posts:create', 'app:posts:list']]);

        $authorizable = new AuthorizableManager();

        // Act
        $result = $authorizable->hasAuthorization($user, 'app:posts:create');

        // Assert
        $this->assertTrue($result);
    }

    public function test_authorizable_has_authorization_returns_false_if_user_does_not_have_scope()
    {
        // Initialize
        $user = Authenticatable::createFromAttributes(['authorizations' => ['app:posts:create', 'app:posts:list']]);

        $authorizable = new AuthorizableManager();

        // Act
        $result = $authorizable->hasAuthorization($user, 'sys:users:create');

        // Assert
        $this->assertFalse($result);
    }

    public function test_authorizable_has_authorization_returns_true_if_user_has_at_least_on_scope_in_list_of_scopes()
    {
        // Initialize
        $user = Authenticatable::createFromAttributes(['authorizations' => ['app:posts:create']]);

        $authorizable = new AuthorizableManager();

        // Act
        $result = $authorizable->hasAuthorization($user, ['app:posts:create', 'app:posts:list']);

        // Assert
        $this->assertTrue($result);
    }

    public function test_authorizable_has_authorization_returns_false_if_user_does_not_have_any_of_provided_scopes()
    {
        // Initialize
        $user = Authenticatable::createFromAttributes(['authorizations' => ['app:posts:create', 'app:posts:list']]);

        $authorizable = new AuthorizableManager();

        // Act
        $result = $authorizable->hasAuthorization($user, ['app:posts:update', 'app:posts:delete']);

        // Assert
        $this->assertFalse($result);
    }


    public function test_authorizable_has_authorization_group_returns_true_if_user_has_scope()
    {
        // Initialize
        $user = Authenticatable::createFromAttributes(['authorization_groups' => ['USERSMAN']]);

        $authorizable = new AuthorizableManager();

        // Act
        $result = $authorizable->hasAuthorizationGroup($user, 'USERSMAN');

        // Assert
        $this->assertTrue($result);
    }

    public function test_authorizable_has_authorization_group_returns_false_if_user_does_not_have_scope()
    {
        // Initialize
        $user = Authenticatable::createFromAttributes(['authorization_groups' => ['USERSMAN']]);

        $authorizable = new AuthorizableManager();

        // Act
        $result = $authorizable->hasAuthorizationGroup($user, 'SYSADMIN');

        // Assert
        $this->assertFalse($result);
    }


    public function test_authorizable_has_authorization_group_returns_true_if_user_has_at_least_an_authorization_group_in_provided_list()
    {
        // Initialize
        $user = Authenticatable::createFromAttributes(['authorization_groups' => ['USERSMAN']]);

        $authorizable = new AuthorizableManager();

        // Act
        $result = $authorizable->hasAuthorizationGroup($user, ['USERSMAN', 'SYSADMIN']);

        // Assert
        $this->assertTrue($result);
    }

    public function test_authorizable_has_authorization_group_returns_false_if_user_does_not_have_any_scope_in_provided_list_of_scopes()
    {
        // Initialize
        $user = Authenticatable::createFromAttributes(['authorization_groups' => ['USERSMAN']]);

        $authorizable = new AuthorizableManager();

        // Act
        $result = $authorizable->hasAuthorizationGroup($user, ['SYSADMIN', 'AUTHORIZATIONSMAN']);

        // Assert
        $this->assertFalse($result);
    }


    public function test_authorizable_has_authorizations_returns_false_if_user_does_not_has_all_provided_authorizations_and_true_else()
    {
        // Initialize
        $user = Authenticatable::createFromAttributes(['authorizations' => ['app:posts:create', 'app:posts:list']]);
        $authorizable = new AuthorizableManager();

        // Act
        $result = $authorizable->hasAuthorizations($user, ['app:posts:create', 'app:posts:list', 'app:posts:delete']);

        // Assert
        $this->assertFalse($result);


        // Act
        $result2 = $authorizable->hasAuthorizations($user, ['app:posts:create', 'app:posts:list']);

        // Assert
        $this->assertTrue($result2);
    }


    public function test_authorizable_has_authorization_groups_returns_false_if_user_does_not_has_all_provided_authorization_groups_and_true_else()
    {
        // Initialize
        $user = Authenticatable::createFromAttributes(['authorization_groups' => ['APP POSTS MAN', 'APP ART. MAN']]);
        $authorizable = new AuthorizableManager();

        // Act
        $result = $authorizable->hasAuthorizationGroups($user, ['APP POSTS MAN', 'APP VISITS. MAN']);

        // Assert
        $this->assertFalse($result);

        // Act
        $result2 = $authorizable->hasAuthorizationGroups($user, ['APP POSTS MAN', 'APP ART. MAN']);

        // Assert
        $this->assertTrue($result2);
    }
}
