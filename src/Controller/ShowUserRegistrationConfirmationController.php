<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ShowUserRegistrationConfirmationController extends AbstractController
{
    #[Route('/registration/confirmation', name: 'app_confirm_user_registration', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        return $this->render('register_user/confirmation.html.twig');
    }
}
