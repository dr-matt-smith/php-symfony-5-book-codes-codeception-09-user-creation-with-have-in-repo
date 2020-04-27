<?php namespace App\Tests;
use App\Tests\AcceptanceTester;
use Codeception\Example;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;


class UserRoleFromFixturesCest
{
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
     * @example(email="user@user.com", password="user")
     */
    public function validUserRoleUserCannotSeeLinkToAdminHomePage(AcceptanceTester $I, Example $example)
    {
        // ARRANGE: login
        $email = $example['email'];
        $password = $example['password'];
        $this->login($I, $email, $password);

        // ASSERT: NOT authorised
        $this->dontSeeAdminHomeLink($I);
    }

    /**
     * @example(email="user@user.com", password="user")
     */
    public function validUserRoleUserGetsAccessDeniedMessageWhenTryVisitAdminHomePage(AcceptanceTester $I, Example $example)
    {
        // ARRANGE: login
        $email = $example['email'];
        $password = $example['password'];
        $this->login($I, $email, $password);

        // ASSERT: NOT authorised - error message when try to visit admin after login
        $this->visitAdminSeeAccessDeniedMessage($I);
    }

    /**
     * @example(email="matt.smith@smith.com", password="smith")
     * @example(email="admin@admin.com", password="admin")
     */
    public function validAdminRoleUserCanSeeLinkToAdminHomePage(AcceptanceTester $I, Example $example)
    {
        // (1) arrange - login
        $email = $example['email'];
        $password = $example['password'];
        $this->login($I, $email, $password);

        // ASSERT: authorised
        $this->seeAdminHomeLink($I);
    }

    /**
     * @example(email="matt.smith@smith.com", password="smith")
     * @example(email="admin@admin.com", password="admin")
     */
    public function validAdminRoleUserCanVisitAdminHomePage(AcceptanceTester $I, Example $example)
    {
        // (1) arrange - login
        $email = $example['email'];
        $password = $example['password'];
        $this->login($I, $email, $password);

        // ASSERT: can access  secure pages
        $this->clickAdminHomeLinkAndSeeSecrets($I);
    }
}
