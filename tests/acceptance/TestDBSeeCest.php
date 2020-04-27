<?php
namespace App\Tests;
use App\Entity\User;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class TestDBSeeCest
{
    private $passwordEncoder;

    /**
     * @param AcceptanceTester $I
     *
     * get and store Symfony password encoder object
     */
    public function _before(AcceptanceTester $I)
    {
        $this->passwordEncoder = $I->getPasswordEncoder();
    }

    // PRIVATE - this is a "helper" method, NOT called by Codeception
    /**
     * perform login with given email/password
     */
    private function createUserInDatabaseAndLogin(AcceptanceTester $I, $email, $role = 'ROLE_USER', $plainTextPassword)
    {
        $this->createUserInDatabase($I, $email, $role, $plainTextPassword);

        $I->amOnPage('/login');
        $I->expect('to successfully login as a ROLE_ADMIN user');
        $I->fillField('#inputEmail', $email);
        $I->fillField('#inputPassword', $plainTextPassword);
        $I->click('Login');

        // successful login
        $I->dontSee('Email could not be found.');
        $I->dontSee('Invalid credentials.');
    }

    private function createUserInDatabase(AcceptanceTester $I, $email, $role = 'ROLE_USER', $plainTextPassword)
    {
        $user = new User();
        $user->setEmail($email);
        $user->setRole($role);

        // password - and encoding
        /**
         * @var  UserPasswordEncoderInterface $passwordEncoder
         */
        $passwordEncoder = $this->passwordEncoder;
        $encodedPassword = $passwordEncoder->encodePassword($user, $plainTextPassword);
        $user->setPassword($encodedPassword);


        // from_support/Helper/Acceptance.php
        $I->haveInRepository('App\Entity\User', [
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'role' => $user->getRole()
        ]);
    }

    public function testInsertIntoDatabase(AcceptanceTester $I)
    {
        $users = $I->grabEntitiesFromRepository('App:User');
        $numUsersBefore = count($users);

        $I->seeInRepository('App:User', [
            'email' => 'matt.smith@smith.com'
        ]);

        $I->haveInRepository('App\Entity\User', [
            'email' => 'joe@joe.com',
            'password' => 'joe',
            'role' => 'ROLE_ADMIN',
        ]);

        $users = $I->grabEntitiesFromRepository('App:User');
        $numUsersAfter = count($users);


        $I->assertEquals($numUsersAfter, 1 + $numUsersBefore);

    }


    public function testEncodeIntoDatabase(AcceptanceTester $I)
    {
        $email = 'tester@test.com';
        $role = 'ROLE_ADMIN';
        $plainTextPassword = 'password';

        $user = new User();
        $user->setEmail($email);
        $user->setRole($role);

        // password - and encoding
        /**
         * @var  UserPasswordEncoderInterface $passwordEncoder
         */
        $passwordEncoder = $this->passwordEncoder;
        $encodedPassword = $passwordEncoder->encodePassword($user, $plainTextPassword);
        $user->setPassword($encodedPassword);


        // from_support/Helper/Acceptance.php
        $I->haveInRepository('App\Entity\User', [
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'role' => $user->getRole()
        ]);

        $I->seeInRepository('App:User', [
            'email' => $user->getEmail(),
            'password' => $user->getPassword(),
            'role' => $user->getRole()
        ]);

        var_dump("\n password = {$user->getPassword()}");
    }


    public function clickAdminHomeLinkAndSeeSecrets(AcceptanceTester $I)
    {
        $email = 'admin99@admin.com';
        $role = 'ROLE_ADMIN';
        $plainPassword = 'admin99';

        $this->createUserInDatabaseAndLogin($I, $email, $role, $plainPassword);

        $I->expect('now be at admin home page, and see secrets');
        $I->amOnPage('/admin');
        $I->see('here is the secret code to the safe');
    }

    public function notLoginSinceUserNotCreated(AcceptanceTester $I)
    {
        $email = 'admin99@admin.com';
        $plainPassword = 'admin99';


        $I->amOnPage('/login');
        $I->expect('NOT to successfully login as a ROLE_ADMIN user');
        $I->fillField('#inputEmail', $email);
        $I->fillField('#inputPassword', $plainPassword);
        $I->click('Login');

        // UN-successful login
        $I->see('Email could not be found.');

        // NOT be at admin home page
        $I->expect('NOT to now be at admin home page, and see secrets');
        $I->dontSeeCurrentUrlEquals('/admin');
        $I->dontSee('here is the secret code to the safe');
    }



}
