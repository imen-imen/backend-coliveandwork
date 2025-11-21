<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Address;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    #[Route('/api/register/client', name: 'api_register_client', methods: ['POST'])]
    public function registerClient(
        Request $request,
        EntityManagerInterface $em,
        UserPasswordHasherInterface $passwordHasher,
        UserRepository $userRepository
    ): JsonResponse {

        $data = json_decode($request->getContent(), true);

        // Vérification email existant
        if ($userRepository->findOneBy(['email' => $data['email']])) {
            return new JsonResponse(['error' => 'Email already used'], 400);
        }

        // 📌 CREATION DE L'ADRESSE (ADAPTÉE A TON ENTITÉ Address)
        $address = new Address();
        $address->setStreetNumber($data['streetNumber'] ?? null);
        $address->setStreetName($data['streetName'] ?? null);
        $address->setPostalCode($data['postalCode'] ?? null);

        // ⚠️ Ton entité n'a PAS "city", donc on stocke dans otherCityName
        $address->setOtherCityName($data['city'] ?? null);

        $em->persist($address);

        // 📌 CREATION DU USER
        $user = new User();
        $user->setEmail($data['email']);
        $user->setFirstname($data['firstname']);
        $user->setLastname($data['lastname']);

        $user->setPhoneNumber($data['phone'] ?? null);

        // Date de naissance
        $user->setBirthDate(!empty($data['birthdate']) ? new \DateTime($data['birthdate']) : null);

        // Gender: Homme = true / Femme = false
        $user->setGender($data['gender'] === 'Homme' ? true : false);

        $user->setRoles(['ROLE_CLIENT']);
        $user->setIsEmailVerified(false);
        $user->setIsActive(true);

        // Associer l'adresse
        $user->setAddress($address);

        // Hash du password
        $hashedPassword = $passwordHasher->hashPassword($user, $data['password']);
        $user->setPassword($hashedPassword);

        $em->persist($user);
        $em->flush();

        return new JsonResponse([
            'message' => 'Client registered successfully',
            'userId' => $user->getId()
        ], 201);
    }
}
