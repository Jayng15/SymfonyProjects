<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\UserProfile;
use App\Form\UserProfileType;
use App\Form\ProfileImageType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\UserProfileRepository;
use Doctrine\Persistence\ManagerRegistry;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\String\Slugger\SluggerInterface;

class SettingsProfileController extends AbstractController
{
    #[Route('/settings/profile', name: 'app_settings_profile')]
    #[IsGranted('IS_AUTHENTICATED_FULLY')]
    public function profile(
        Request $request,
        // UserProfileRepository $profiles,
        EntityManagerInterface $manager,
    ): Response
    {
        /** @var User $user */
        $user = $this->getUser();
        /** @var UserProfile $userProfile */
        $userProfile = $user->getUserProfile() ?? new UserProfile();

        $form = $this->createForm(UserProfileType::class, $userProfile);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $userProfile = $form->getData();
            $user->setUserProfile($userProfile);

            $manager->persist($user); 
            $manager->flush();
            
            $this->addFlash('success', 'Your personal profile has been updated');
            
            return $this->redirectToRoute('app_settings_profile');
        }
        return $this->render(
            'settings_profile/profile.html.twig',
            [
                'form' => $form->createView(),
            ]
        );
    }

    #[Route('settings/profile-image', name: 'app_settings_profile_image')]
    public function profileImage(
        Request $request,
        SluggerInterface $slugger,
        ManagerRegistry $manager
    ): Response {
        $form = $this->createForm(ProfileImageType::class);
        /** @var User $user */
        $user = $this->getUser();
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            $profileImageFile = $form->get('profileImage')->getData();

            if ($profileImageFile)
            {
                $originalFileName = pathinfo(
                    $profileImageFile->getClientOriginalName(),
                    PATHINFO_FILENAME
                );

                $safeFileName = $slugger->slug($originalFileName);
                $newFileName = $safeFileName . '-' . uniqid() . '.' . $profileImageFile->guessExtension();
            } 

            try {
                $profileImageFile->move(
                    $this->getParameter('profiles_directory'),
                    $newFileName
                    );
            } catch (FileException $e){
            }
            $profile = $user->getUserProfile() ?? new UserProfile();
            $profile->setImage($newFileName);
            $user->setUserProfile($profile);
            $manager->getManager()->flush();
            $this->addFlash('success', 'Your avatar has been updated');
            
            return $this->redirectToRoute('app_settings_profile_image');
        }

        return $this->render(
            'settings_profile/profile_image.html.twig',
            [
                'form' => $form->createView()
            ] 
        );
    }
}
