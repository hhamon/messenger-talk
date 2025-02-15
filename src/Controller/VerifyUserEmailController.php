<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\UserRepository;
use App\User\UserEmailVerifier;
use DomainException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Uid\Uuid;
use Symfony\Contracts\Translation\TranslatorInterface;
use SymfonyCasts\Bundle\VerifyEmail\Exception\VerifyEmailExceptionInterface;

final class VerifyUserEmailController extends AbstractController
{
    public function __construct(
        private readonly UserEmailVerifier $userEmailVerifier,
        private readonly UserRepository $userRepository,
        private readonly TranslatorInterface $translator,
    ) {
    }

    #[Route('/verify-email', name: 'app_verify_user_email', methods: ['GET'])]
    public function __invoke(Request $request): Response
    {
        $id = $request->query->getString('id');

        if ($id === '') {
            throw new BadRequestHttpException();
        }

        try {
            $user = $this->userRepository->byId(Uuid::fromString($id));
        } catch (DomainException $e) {
            throw $this->createNotFoundException($e->getMessage(), previous: $e);
        }

        try {
            $this->userEmailVerifier->handleEmailConfirmation($request, $user);
        } catch (VerifyEmailExceptionInterface $exception) {
            $this->addFlash('verify_email_error', $this->translator->trans($exception->getReason(), [], 'VerifyEmailBundle'));

            return $this->redirectToRoute('app_register_user');
        }

        $this->addFlash('success', 'Your email address has been verified.');

        if ($this->isGranted('ROLE_USER')) {
            return $this->redirectToRoute('app_show_user_profile');
        }

        return $this->redirectToRoute('app_login');
    }
}
