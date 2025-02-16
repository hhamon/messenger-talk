<?php

declare(strict_types=1);

namespace App\Controller;

use App\Intercom\Message\SyncIntercomUserContactMessage;
use App\User\Form\UserRegistrationType;
use App\User\Message\SendWelcomeEmailMessage;
use App\User\Model\UserRegistration;
use App\User\RegisterUser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Messenger\MessageBusInterface;
use Symfony\Component\Routing\Attribute\Route;

final class RegisterUserController extends AbstractController
{
    public function __construct(
        private readonly RegisterUser $registerUser,
        private readonly MessageBusInterface $messageBus,
    ) {
    }

    #[Route('/registration', name: 'app_register_user', methods: ['GET', 'POST'])]
    public function __invoke(Request $request): Response
    {
        $form = $this->createForm(UserRegistrationType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UserRegistration $data */
            $data = $form->getData();

            $user = $this->registerUser->registerUser(
                $data->getEmail(),
                $data->getPassword(),
                $data->getGender(),
                $data->getFullName(),
                $data->getCountry(),
                $data->getBirthdate(),
            );

            $this->messageBus->dispatch(new SendWelcomeEmailMessage((string) $user->getId()));
            $this->messageBus->dispatch(new SyncIntercomUserContactMessage((string) $user->getId()));

            return $this->redirectToRoute('app_confirm_user_registration');
        }

        return $this->render('register_user/index.html.twig', [
            'form' => $form->createView(),
        ]);
    }
}
