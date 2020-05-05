<?php

namespace tests\Form;

use App\Entity\User;
use App\Form\UserEditType;
use Symfony\Component\Form\Extension\Validator\ValidatorExtension;
use Symfony\Component\Form\Test\TypeTestCase;
use Symfony\Component\Validator\Validation;

class UserEditTypeTest extends TypeTestCase
{

  protected function getExtensions()
    {
        $validator = Validation::createValidator();

        return [
            new ValidatorExtension($validator),
        ];
    }

    public function testSubmitValidData()
    {
        $user = new User();
        $form = $this->factory->create(UserEditType::class, $user);
        $formData = array(
            'username' => 'username',
            'password' => array(
                'first' => 'password',
                'second' => 'password',
            ),
            'email' => 'email@email.fr',
            'roles' => ['ROLE_USER']
        );
        $form->submit($formData);


        $this->assertSame($user, $form->getData());

        $this->assertSame('username', $user->getUsername());
        $this->assertSame('password', $user->getPassword());
        $this->assertSame('email@email.fr', $user->getEmail());

    }
}
