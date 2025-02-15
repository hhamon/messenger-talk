<?php

declare(strict_types=1);

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ShowUserProfileController extends AbstractController
{
    #[Route('/profile', name: 'app_show_user_profile')]
    public function index(): Response
    {
        return $this->render('user/profile.html.twig');
    }
}
