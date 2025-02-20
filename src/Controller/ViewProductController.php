<?php

declare(strict_types=1);

namespace App\Controller;

use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
class ViewProductController extends AbstractController
{

    #[Route('product_piece/{id}', name: 'product_piece')]
    public function piece(int $id,
                          ProductRepository $productRepository)
    {
        $p = $productRepository->find($id);
        return $this->render('ProduitParBlock.html.twig', ["p" => $p]);


    }

    #[Route('/', name: 'home', methods: ['GET'])]
    public function index(ProductRepository $productRepository): Response
    {
        $products = $productRepository->findAll();

        // Process products to include base64-encoded images


        return $this->render('index.html.twig', [
            'products' => $products,
        ]);
    }


    #[Route('/products', name: 'products_by_category_page', methods: ['GET'])]
    public function showProductsByCategory(
        ProductRepository $productRepository,
        Request $request,
        PaginatorInterface $paginator
    ): Response {
        // Récupérer les paramètres de la requête
        $gamme = $request->query->get('gamme');
        $subcategorie = $request->query->get('subcategorie');
        $marqueFilter = $request->query->all('brand');
        $priceMin = $request->query->get('price_min');
        $priceMax = $request->query->get('price_max');

        // Normaliser 'marque' pour s'assurer qu'il est toujours un tableau
        if (is_string($marqueFilter)) {
            $marqueFilter = [$marqueFilter];
        } elseif (!is_array($marqueFilter)) {
            $marqueFilter = [];
        }

        // Rediriger vers l'accueil si aucun critère principal n'est fourni
        if ($gamme === null && $subcategorie === null) {
            return $this->redirectToRoute('home');
        }

        // Récupérer les marques distinctes pour le filtrage
        $brand = $productRepository->findDistinctMarques();

        // Construire les critères pour la requête
        $criteria = [];

        if ($gamme !== null) {
            $criteria['gamme'] = $gamme;
        }

        if ($subcategorie !== null) {
            $criteria['subcategorie'] = $subcategorie;
        }

        // Ajouter le filtre par marque si fourni
        if (count($marqueFilter) > 0) {
            $criteria['brand'] = $marqueFilter;
        }

        // Ajouter les filtres de prix si valides
        if ($priceMin !== null && is_numeric($priceMin)) {
            $criteria['price_min'] = (float) $priceMin;
        }
        if ($priceMax !== null && is_numeric($priceMax)) {
            $criteria['price_max'] = (float) $priceMax;
        }

        // Utiliser le repository pour récupérer les produits selon les critères
        $queryBuilder = $productRepository->findByCriteria($criteria);

        // Paginer les résultats
        $pagination = $paginator->paginate(
            $queryBuilder, // Objet QueryBuilder
            $request->query->getInt('page', 1), // Numéro de page actuel (par défaut 1)
            20 // Nombre d'éléments par page
        );

        // Traiter les produits pour inclure les images encodées en base64
        $processedProducts = array_map(function ($product) {
            return [
                'id' => $product->getId(),
                'title' => $product->getTitle(),
                'brand' => $product->getBrand(),
                'price' => $product->getPrice(),
                'imageUrl' => $product->getImageUrl(),
            ];
        }, $pagination->getItems());

        // Rendre le template avec les données de pagination et les filtres appliqués
        return $this->render('products.html.twig', [
            'filteredProducts' => $processedProducts,
            'gamme' => $gamme,
            'subcategorie' => $subcategorie,
            'marqueFilter' => $marqueFilter,
            'brand' => $brand,
            'priceMin' => $priceMin,
            'priceMax' => $priceMax,
            'pagination' => $pagination, // Passer l'objet pagination au template
        ]);
    }

    #[Route('/checkout', name: 'checkout', methods: ['GET'])]
    public function checkout() {
        return $this->render('checkout.html.twig');

    }


}