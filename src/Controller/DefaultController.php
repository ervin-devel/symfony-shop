<?php

namespace App\Controller;

use App\Entity\Product;
use App\Form\ProductFormType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DefaultController extends AbstractController
{
    public function __construct(private EntityManagerInterface $entityManager)
    {
    }

    #[Route('/', name: 'main_homepage')]
    public function index(): Response
    {
        $productList = $this->entityManager->getRepository(Product::class)->findAll();
        return $this->render('main/default/index.html.twig');
    }

    #[Route('/productAdd', name: 'productAdd')]
    public function productAdd()
    {
        $product = new Product();
        $product->setTitle('Product '.rand(1, 100));
        $product->setDescription('smth');
        $product->setPrice(10);
        $product->setQuantity(1);

        $this->entityManager->persist($product);
        $this->entityManager->flush();

        return $this->redirectToRoute('homepage');
    }

    #[Route('/productEdit/{id}', name: 'productEdit', methods: ['GET', 'POST'], requirements: ['id' => '\d+'])]
    public function editProduct(Request $request, Product $product): Response
    {
        $form = $this->createForm(ProductFormType::class, $product);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return $this->redirectToRoute('productEdit', ['id' => $product->getId()]);
        }

        return $this->render('main/default/edit_product.html.twig', ['form' => $form->createView()]);
    }

    #[Route('/productAdd', name: 'productAdd', methods: ['GET', 'POST'])]
    public function addProduct(Request $request): Response
    {
        $product = new Product();

        $form = $this->createForm(ProductFormType::class, $product);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $this->entityManager->persist($product);
            $this->entityManager->flush();

            return $this->redirectToRoute('homepage');
        }

        return $this->render('main/default/edit_product.html.twig', ['form' => $form]);
    }
}
