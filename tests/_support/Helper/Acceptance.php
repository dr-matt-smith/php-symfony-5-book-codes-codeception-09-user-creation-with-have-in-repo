<?php
namespace App\Tests\Helper;

// here you can define custom actions
// all public methods declared in helper class will be available in $I

use App\Entity\User;
use \Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Codeception\Module;

class Acceptance extends Module
{

    /**
     * Create user or administrator and set auth cookie to client
     *
     * @param bool $admin
     */
    public function getPasswordEncoder()
    {
        /** @var \Codeception\Module\Symfony $symfony */
        try {
            $symfony = $this->getModule('Symfony');
        } catch (ModuleException $e) {
            $this->fail('Unable to get module \'Symfony\'');
        }
        /** @var \Codeception\Module\Doctrine2 $doctrine */
        try {
            $doctrine = $this->getModule('Doctrine2');
        } catch (ModuleException $e) {
            $this->fail('Unable to get module \'Doctrine2\'');
        }
        /** @var UserPasswordEncoderInterface $encoder */
        $encoder = $symfony->grabService('security.password_encoder');

        return $encoder;
    }

    /**
     * Create user or administrator and set auth cookie to client
     *
     * @param bool $admin
//     */
//    public function createUserWithEncodedPassword(User $user)
//    {
//        /** @var \Codeception\Module\Symfony $symfony */
//        try {
//            $symfony = $this->getModule('Symfony');
//        } catch (ModuleException $e) {
//            $this->fail('Unable to get module \'Symfony\'');
//        }
//        /** @var \Codeception\Module\Doctrine2 $doctrine */
//        try {
//            $doctrine = $this->getModule('Doctrine2');
//        } catch (ModuleException $e) {
//            $this->fail('Unable to get module \'Doctrine2\'');
//        }
//        /** @var UserPasswordEncoderInterface $encoder */
//        $encoder = $symfony->grabService('security.password_encoder');
//
//        $encodedPassword = $encoder->encodePassword($user, $user->getPassword()),
//        $user->setPassword($encodedPassword);
//
//        return $user;
//    }

//        /** @var Uuid $uuid */
//        $uuid = $doctrine->haveInRepository('App\Entity\User', [
//            'email' => 'testemail@example.com',
//            'role' => $user->getRole(),
//            'password' => $encoder
//                ->encodePassword($user, $user->getPassword()),
//        ]);
//        $user = $doctrine->grabEntityFromRepository('App\Entity\User', [
//            'id' => $user->getId()
//        ]);

        // login with this user authenticated
//        $token = new UsernamePasswordToken($user, null, 'main', $user->getRoles());
//        $symfony->grabService('security.token_storage')->setToken($token);
//        /** @var \Symfony\Component\HttpFoundation\Session\Session $session */
//        $session = $symfony->grabService('session');
//        $session->set('_security_main', serialize($token));
//        $session->save();
//        $cookie = new Cookie($session->getName(), $session->getId());
//        $symfony->client->getCookieJar()->set($cookie);

}
