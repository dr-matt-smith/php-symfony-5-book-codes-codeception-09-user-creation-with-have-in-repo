<?php namespace App\Tests;
use App\Tests\AcceptanceTester;
use Codeception\Example;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserRoleFromCrudUSersCest
{

    // PRIVATE - this is a "helper" method, NOT called by Codeception
    /**
     * perform login with given email/password
     */
    private function loginWithRoleAdmin(AcceptanceTester $I)
    {
        $email = 'admin@admin.com';
        $password = 'admin';

        $I->amOnPage('/login');
        $I->expect('to successfully login as a ROLE_ADMIN user');
        $I->fillField('#inputEmail', $email);
        $I->fillField('#inputPassword', $password);
        $I->click('Login');

        // successful login
        $I->dontSee('Email could not be found.');
        $I->dontSee('Invalid credentials.');
    }

    // PRIVATE - this is a "helper" method, NOT called by Codeception
    /**
     * perform login with given email/password
     */
    private function createUser(AcceptanceTester $I, $email, $password, $role)
    {
        $this->loginWithRoleAdmin($I);

        $I->amOnPage('/user/new');
        $I->fillField('#user_email', $email);
        $I->fillField('#user_password', $password);
        $I->fillField('#user_role', $role);
        $I->click('Save');
    }


    // PRIVATE - this is a "helper" method, NOT called by Codeception
    /**
     * perform login with given email/password
     */
    private function login(AcceptanceTester $I, $email, $password)
    {
        $I->amOnPage('/login');
        $I->expect('redirect to Login page');
        $I->seeCurrentUrlEquals('/login');
        $I->fillField('#inputEmail', $email);
        $I->fillField('#inputPassword', $password);
        $I->click('Login');

        // successful login
        $I->dontSee('Email could not be found.');
        $I->dontSee('Invalid credentials.');
    }

    // PRIVATE - this is a "helper" method, NOT called by Codeception
    /**
     * perform login with given email/password
     */
    private function cannotLogin(AcceptanceTester $I, $email, $password)
    {
        $I->amOnPage('/login');
        $I->expect('redirect to Login page');
        $I->seeCurrentUrlEquals('/login');
        $I->fillField('#inputEmail', $email);
        $I->fillField('#inputPassword', $password);
        $I->click('Login');

        // successful login
        $I->expect('to see error message about unknown Email address');
        $I->see('Email could not be found.');
    }


    // PRIVATE - this is a "helper" method, NOT called by Codeception
    /**
     * ASSERT - do NOT see admin home link
     */
    private function dontSeeAdminHomeLink(AcceptanceTester $I)
    {
        $I->expect('NOT to see link to admin home');
        $I->dontSeeLink('admin home');
    }

    // PRIVATE - this is a "helper" method, NOT called by Codeception
    /**
     * ASSERT - see admin home link
     */
    private function seeAdminHomeLink(AcceptanceTester $I)
    {
        $I->expect('to see link to admin home');
        $I->seeLink('admin home');
    }

    // PRIVATE - this is a "helper" method, NOT called by Codeception
    /**
     * ACTION - click admin home link
     * ASSERT - see admin secrets
     */
    private function clickAdminHomeLinkAndSeeSecrets(AcceptanceTester $I)
    {
        $I->click('admin home');
        $I->expect('now be at admin home page');
        $I->seeCurrentUrlEquals('/admin');
        $I->see('here is the secret code to the safe');
    }

    // PRIVATE - this is a "helper" method, NOT called by Codeception
    /**
     * ACTION - click admin home link
     * ASSERT - see admin secrets
     */
    private function visitAdminSeeAccessDeniedMessage(AcceptanceTester $I)
    {
        $I->amOnPage('/admin');
        $I->expect('to see not authorised error');
        $I->see('access is denied for your request');

        // should NOT have access to admin page contents
        $I->dontSee('here is the secret code to the safe');
    }

    /**
     * @example(email="test1@test1.com", password="test1", role="ROLE_USER")
     * @example(email="test2@chesecake.ie", password="cheesecake", role="ROLE_ADMIN")
     */
    public function canCreateUserInDb(AcceptanceTester $I, Example $example)
    {
        $email = $example['email'];
        $password = $example['password'];
        $role = $example['role'];

        // count BEFORE user created
        $users = $I->grabEntitiesFromRepository('App\Entity\User');
        $numUsersBeforeCreate = count($users);

        // ACT - create user
        $this->createUser($I, $email, $password, $role);

        // count AFTER user created
        $users = $I->grabEntitiesFromRepository('App\Entity\User');
        $numUsersAfterCreate = count($users);

        // ASSERT
        $I->expect('there to be 1 more user in DB after CRUD creation');
        $I->assertEquals($numUsersAfterCreate, 1 + $numUsersBeforeCreate);
    }

    /**
     * @example(email="test1@test1.com", password="test1", role="ROLE_USER")
     * @example(email="test2@chesecake.ie", password="cheesecake", role="ROLE_ADMIN")
     */
    public function canCreateUserInDbAndSeeInRepository(AcceptanceTester $I, Example $example)
    {
        $email = $example['email'];
        $password = $example['password'];
        $role = $example['role'];

        // cannot seein DB BEFORE create user
        $I->cantSeeInRepository('App\Entity\User', [
            'email' => $email,
            'role' => $role
        ]);

        // ACT - create user
        $this->createUser($I, $email, $password, $role);

        // see in DB AFTER create user
        $I->seeInRepository('App\Entity\User', [
            'email' => $email,
            'role' => $role
        ]);
    }


    /**
     * @example(email="test1@test1.com", password="test1", role="ROLE_USER")
     * @example(email="test2@chesecake.ie", password="cheesecake", role="ROLE_ADMIN")
     */
    public function canLoginWithCreatedUser(AcceptanceTester $I, Example $example)
    {
        $email = $example['email'];
        $password = $example['password'];
        $role = $example['role'];

        // should NOT be able to login before creating user
        $I->expect('cannot login before creating user');
        $this->cannotLogin($I, $email, $password);

        // ACT - create user
        $this->createUser($I, $email, $password, $role);

        // ASSERT
        $I->expect('successful login after user crated');
        $this->login($I, $email, $password);
    }

    /**
     * @example(email="test2@chesecake.ie", password="cheesecake", role="ROLE_ADMIN")
     */
    public function newlyCreatedAdminRoleUserCanVisitAdminHomePage(AcceptanceTester $I, Example $example)
    {
        $email = $example['email'];
        $password = $example['password'];
        $role = $example['role'];

        // ACT - create user
        $this->createUser($I, $email, $password, $role);

        // login
        $this->login($I, $email, $password);

        // ASSERT: can access  secure pages
        $this->clickAdminHomeLinkAndSeeSecrets($I);
    }

}
