<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Category;
use App\Entity\Order;
use App\Entity\StatusHistory;
use App\Entity\SubCategory;
use App\Entity\User;
use App\Entity\Product;
use App\Entity\Variant;
use App\Form\CategoryType;
use App\Form\ChoseDriverType;
use App\Form\EditStoreInfoType;
use App\Form\Model\AssertProductImage;
use App\Form\Model\SearchByCityOrCountry;
use App\Form\ProductType;
use App\Form\Model\CustomAssertImage;
use App\Form\SearchByCategoryType;
use App\Form\SearchByCityOrCountryType;
use App\Form\SubCategoryType;
use App\Form\VariantType;
use App\Repository\CategoryRepository;
use App\Repository\OrderRepository;
use App\Repository\SubCategoryRepository;
use App\Repository\VariantRepository;
use App\Service\ImageManager;
use DateTime;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Filesystem;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Symfony\Component\Validator\ConstraintViolationList;

/**
 * Class StoreController.
 *
 * @Route("/store")
 */

class StoreController extends AbstractController
{
    /**
     * @var Security
     */
    private $security;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var ImageManager
     */
    private $imageManager;


    /**
     * StoreController constructor.
     * @param Security $security
     * @param SessionInterface $session
     * @param EntityManagerInterface $manager
     * @param ImageManager $imageManager
     */
    public function __construct(
        Security $security,
        SessionInterface $session,
        EntityManagerInterface $manager,
        ImageManager $imageManager
    ) {
        $this->security = $security;
        $this->session = $session;
        $this->manager = $manager;
        $this->imageManager = $imageManager;
    }

    /**
     * @Route("/", name="dashboardStore")
     */
    public function index(): Response
    {
        /**
         * @var User $store
         */
        $store = $this->getUser();

        return $this->render('backOffice/store/index.html.twig',[
            'store'=>$store
        ]);
    }


    //<editor-fold desc="Code Profile">
    /**
     * @Route("/profile",name="dashStoreProfile")
     * @param Request $request
     *
     * @return Response
     */
    public function profile(Request $request):Response
    {
        /**
         * @var User $store
         */
        $store = $this->getUser();

        $form = $this->createForm(EditStoreInfoType::class,$store);
        $form->handleRequest($request);
        $file = new Filesystem();

        if ($form->isSubmitted() && $form->isValid()) {
            try {

                $this->imageManager->updateGallery($request);

                $profileImage = $form->get('brochureProfile')->getData();
                if ($profileImage) {
                    $oldFileName = $store->getProfileImage();
                    $newProfileImageFilename = uniqid().'.'.$profileImage->guessExtension();
                    try {
                        $profileImage->move($this->getParameter('profile_image_directory'),$newProfileImageFilename);
                        if(null != $oldFileName && true === $file->exists("images/profile_image/".$oldFileName))
                        {
                            $file->remove(['images/profile_image/'.$oldFileName]);
                        }
                        $store->setProfileImage($newProfileImageFilename);
                    } catch (\Exception $ex) {
                        $this->addFlash('error','Error Profile Img');
                    }
                }


                $coverImage = $form->get('brochureCover')->getData();
                if ($coverImage) {
                    $oldFileCoverName = $store->getCoverImage();
                    $newCoverImageFilename = uniqid().'.'.$coverImage->guessExtension();
                    try {
                        $coverImage->move($this->getParameter('cover_image_directory'),$newCoverImageFilename);

                        if(null != $oldFileCoverName && true === $file->exists("images/cover_image/".$oldFileCoverName))
                        {
                            $file->remove(['images/cover_image/'.$oldFileCoverName]);
                        }
                        $store->setCoverImage($newCoverImageFilename);
                    } catch (\Exception $ex) {
                        $this->addFlash('error','Error Cover Img');
                    }
                }

                $this->manager->persist($store);
                $this->manager->flush();
                $this->addFlash('success','Profile Updated');
            }catch (\Exception $ex) {
                $this->addFlash('error','Form Error');
            }
        }


        return $this->render('backOffice/store/account/profile.html.twig',[
            'form'=>$form->createView(),
            'admin'=>$store
        ]);
    }
    //</editor-fold>


    //<editor-fold desc="Code Category">
    /**
     * @Route("/categories/list", name="listCategory")
     * @param CategoryRepository $categoryRepository
     * @return Response
     */
    public function listCategory(CategoryRepository $categoryRepository): Response
    {
        $categories = $categoryRepository->findByStore($this->getUser());
        return $this->render('BackOffice/store/categories/listCategories.html.twig', [
            'Categories' => $categories,
        ]);
    }

    /**
     * @Route("/categories/new", name="newCategory")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function newCategory(Request $request, EntityManagerInterface $manager): Response
    {
        $category = new Category();
        $form = $this->createForm(CategoryType::class,$category);
        $form ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            try {
                /** @var UploadedFile $brochureFile */
                $brochureFile = $form->get('brochure')->getData();
                if ($brochureFile) {
                    $newFilename = uniqid().'.'.$brochureFile->guessExtension();
                    try {
                        $brochureFile->move($this->getParameter('category_directory'),$newFilename);
                    } catch (Exception $ex){
                        $this->addFlash('error','error');
                    }
                    $category->setImageUrl($newFilename);
                }
                /** @var User $store */
                $store = $this->getUser();
                $category->setCreatedDate(new DateTime('now'));
                $category->setStore($store);
                $manager->persist($category);
                $manager->flush();
                $this->addFlash('success','Cuisine has been added');

            }catch (Exception $ex){
                $this->addFlash('error','error');
            }
        }

        return $this->render('BackOffice/store/categories/newCategory.html.twig', [
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/categories/edit/{id}" , name="editCategory")
     * @param Category $category
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return RedirectResponse|Response
     */
    public  function editCategory(Category $category, Request $request, EntityManagerInterface $manager){
        $form = $this->createForm(CategoryType::class,$category);
        $form ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            try {
                /** @var UploadedFile $brochureFile */
                $brochureFile = $form->get('brochure')->getData();
                if ($brochureFile) {
                    $oldFileName = $category->getImageUrl();
                    $newFilename = uniqid().'.'.$brochureFile->guessExtension();
                    $brochureFile->move($this->getParameter('category_directory'),$newFilename);
                    $file = new Filesystem();

                    if(true === $file->exists("images/categories/".$oldFileName))
                    {
                        $file->remove(['images/categories/'.$oldFileName]);
                    }

                    $category->setImageUrl($newFilename);
                }

                $manager->persist($category);
                $manager->flush();
                $this->addFlash('success','Cuisine has been Edited');

                return $this->redirectToRoute("listCategory");

            }catch (Exception $ex){
                $this->addFlash('error','error');
            }
        }

        return $this->render('BackOffice/store/categories/editCategory.html.twig', [
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/categories/delete/{id}" , name="deleteCategory")
     * @param Category $category
     * @return RedirectResponse
     */
    public function deleteCategory(Category $category)
    {
        $oldFileName = $category->getImageUrl();
        //--------------------------------------------------------------------------
        $file = new Filesystem();
        if(true === $file->exists("images/categories/".$oldFileName))
        {
            $file->remove(['images/categories/'.$oldFileName]);
        }
        //--------------------------------------------------------------------------

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($category);
        $entityManager->flush();

        return $this->redirectToRoute("listCategory");
    }
    //</editor-fold>


    //<editor-fold desc="Code Sub Category">
    /**
     * @Route("/category_{category}/subCategories/list", name="listSubCategory")
     * @param Category $category
     * @param SubCategoryRepository $subCategoryRepository
     * @param Request $request
     * @return Response
     */
    public function listSubCategory(Category $category,SubCategoryRepository $subCategoryRepository,Request $request): Response
    {
        $data = new SubCategory();
        $form = $this->createForm(SearchByCategoryType::class,$data);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                if (null != $data->getCategory()){
                    return $this->redirectToRoute('listSubCategory',['category' => $data->getCategory()->getId()]);
                }
            }
            catch (\Exception $exception) {
                $this->addFlash('error',$exception->getMessage());
            }
        }

        $subCategories = $subCategoryRepository->findByCategory($category);

        return $this->render('BackOffice/store/categories/subCategories/listSubCategories.html.twig', [
            'category' => $category,
            'subCategories' => $subCategories,
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/category_{category}/subCategories/new", name="newSubCategory")
     * @param Category $category
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function newSubCategory(Category $category,Request $request, EntityManagerInterface $manager): Response
    {
        $subCategory = new SubCategory();
        $subCategory->setCategory($category);
        $form = $this->createForm(SubCategoryType::class,$subCategory);
        $form ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            try {
                /** @var UploadedFile $brochureFile */
                $brochureFile = $form->get('brochure')->getData();
                if ($brochureFile) {
                    $newFilename = uniqid().'.'.$brochureFile->guessExtension();
                    try {
                        $brochureFile->move($this->getParameter('category_directory'),$newFilename);
                    } catch (Exception $ex){
                        $this->addFlash('error','error');
                    }
                    $subCategory->setImageUrl($newFilename);
                }

                $subCategory->setCreatedDate(new DateTime('now'));
                $manager->persist($subCategory);
                $manager->flush();
                $this->addFlash('success','Cuisine has been added');

                return $this->redirectToRoute('listSubCategory',['category' => $category->getId()]);

            }catch (Exception $ex){
                $this->addFlash('error','error');
            }
        }

        return $this->render('BackOffice/store/categories/subCategories/newSubCategory.html.twig', [
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/category_{category}/subCategories/edit/{id}" , name="editSubCategory")
     * @param Category $category
     * @param SubCategory $subCategory
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return RedirectResponse|Response
     */
    public  function editSubCategory(Category $category,SubCategory $subCategory, Request $request, EntityManagerInterface $manager){
        $form = $this->createForm(SubCategoryType::class,$subCategory);
        $form ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            try {
                /** @var UploadedFile $brochureFile */
                $brochureFile = $form->get('brochure')->getData();
                if ($brochureFile) {
                    $oldFileName = $subCategory->getImageUrl();
                    $newFilename = uniqid().'.'.$brochureFile->guessExtension();
                    $brochureFile->move($this->getParameter('category_directory'),$newFilename);
                    $file = new Filesystem();

                    if(true === $file->exists("images/categories/".$oldFileName))
                    {
                        $file->remove(['images/categories/'.$oldFileName]);
                    }

                    $subCategory->setImageUrl($newFilename);
                }

                $manager->persist($subCategory);
                $manager->flush();
                $this->addFlash('success','Cuisine has been Edited');

                return $this->redirectToRoute('listSubCategory',['category' => $category->getId()]);

            }catch (Exception $ex){
                $this->addFlash('error','error');
            }
        }

        return $this->render('BackOffice/store/categories/subCategories/editSubCategory.html.twig', [
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/category_{category}/subCategories/delete/{id}" , name="deleteSubCategory")
     * @param Category $category
     * @param SubCategory $subCategory
     * @return RedirectResponse
     */
    public function deleteSubCategory(Category $category,SubCategory $subCategory)
    {
        $oldFileName = $subCategory->getImageUrl();
        //--------------------------------------------------------------------------
        $file = new Filesystem();
        if(true === $file->exists("images/categories/".$oldFileName))
        {
            $file->remove(['images/categories/'.$oldFileName]);
        }
        //--------------------------------------------------------------------------

        $entityManager = $this->getDoctrine()->getManager();
        $entityManager->remove($subCategory);
        $entityManager->flush();

        return $this->redirectToRoute('listSubCategory',['category' => $category->getId()]);
    }
    //</editor-fold>

    //<editor-fold desc="Code Product">
    /**
     * @Route("/products/list", name="listProducts")
     */
    public function listProduct(): Response
    {
        $products = $this->getDoctrine()->getRepository(Product::class)->findAll();
        return $this->render('BackOffice/store/products/listProducts.html.twig', [
            'Products' => $products,
        ]);
    }

    /**
     * @Route("/products/new", name="newProduct")
     *
     * @param Request $request
     * @param ValidatorInterface $validator
     * @return Response
     */
    public function newProduct(Request $request,ValidatorInterface $validator): Response
    {
        $product = new Product();

        $form = $this->createForm(ProductType::class,$product,['category' => null]);
        $form ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $brochureFiles = $form->get('images')->getData();
            if ($brochureFiles) {
                $images = array();
                $countFlashImage = 0;
                /** @var UploadedFile $pic */
                foreach ($brochureFiles as $key=>$file){
                    $assertProductImage = new AssertProductImage();
                    $assertProductImage->setBrochure($file);

                    /** @var ConstraintViolationList $errors */
                    $errors = $validator->validate($assertProductImage);
                    if (count($errors) > 0) {
                        //... le cas d'erreur
                        $countFlashImage++;
                        foreach ($errors as $er){
                            $this->addFlash('errorImage', $er->getMessage());
                        }
                    } else {

                        $newFilename = uniqid().'.'.$file->guessExtension();
                        $file->move(
                            $this->getParameter('product_directory'),
                            $newFilename
                        );

                        if(count($images) < 5){
                            $images[] = $newFilename;
                        }else{break;}

                    }
                }
                $this->addFlash('countFlashImage', $countFlashImage);
                $product->setImages($images);
            }

            $product->setCreatedAt(new DateTime());
            $this->manager->persist($product);
            $this->manager->flush();
            $this->addFlash('success','Product has been added');
            return $this->redirectToRoute('listProducts');
        }

        return $this->render('BackOffice/store/products/newProduct.html.twig', [
            'form'=>$form->createView(),
        ]);

    }

    /**
     * @Route("/products/edit/{id}", name="editProduct")
     *
     * @param Product $product
     * @param Request $request
     * @return Response
     */
    public function editProduct(Product $product, Request $request): Response
    {
        $form = $this->createForm(ProductType::class,$product,['category' => $product->getCategory()]);
        $form ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){

            $this->imageManager->updateProductImages($request,$product);

            $this->manager->persist($product);
            $this->manager->flush();
            $this->addFlash('success','Product has been Edited');

            return $this->redirectToRoute('editProduct',['id' => $product->getId()]);
        }

        return $this->render('BackOffice/store/products/editProduct.html.twig', [
            'form' => $form->createView(),
            'product' => $product,
        ]);
    }

    /**
     * @Route("/products/delete/{id}" , name="deleteProduct")
     *
     * @param Product $product
     *
     * @return RedirectResponse
     */
    public  function deleteProduct(Product $product)
    {
        $oldFileName = $product->getImages();
        $FileSystem = new Filesystem();
        foreach ($oldFileName as $src ){
            if($FileSystem->exists('images/product/'.$src)){
                $FileSystem->remove(['images/product/'.$src]);
            }
        }
        $this->manager->remove($product);
        $this->manager->flush();

        return $this->redirectToRoute("listProducts");
    }

    //</editor-fold>

    //<editor-fold desc="Variant">

    /**
     * @param string $size
     * @return int
     */
    private function generateSort(string $size)
    {
        /** @var int $sort */

        switch ($size) {
            case 'standard':
                $sort = 1;
                break;
            case 'sm':
                $sort = 2;
                break;
            case 'm':
                $sort = 3;
                break;
            case 'l':
                $sort = 4;
                break;
            case 'xl':
                $sort = 5;
                break;
        }

        return $sort;
    }

    /**
     * @Route("/products/{id}/variants", name="listVariants")
     * @param int $id
     * @param Request $request
     * @return Response
     */
    public function listVariant(int $id,Request $request): Response
    {
        $variant = new Variant();
        $product = $this->getDoctrine()->getRepository(Product::class)->find($id);
        $variant->setProduct($product);

        $form = $this->createForm(VariantType::class,$variant);
        $form ->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()){
            if ($form->isSubmitted() && $form->isValid()){
                try {
                    $variant->setSort($this->generateSort($variant->getSize()));
                    $this->manager->persist($variant);
                    $this->manager->flush();
                    $this->addFlash('success','Variant has been added');

                }catch (\Exception $ex){
                    $this->addFlash('error','error');
                }
            }
        }
        $variants = $this->getDoctrine()->getRepository(Variant::class)->findByProduct($id);

        return $this->render('BackOffice/store/variants/listVariants.html.twig',[
            'Variants' => $variants,
            'form'=>$form->createView(),
        ]);
    }


    /**
     * @Route("/variants/edit", name="editVariant")
     * @param Request $request
     * @param ValidatorInterface $validator
     * @param VariantRepository $repository
     * @return Response
     */
    public function editVariant(
        Request $request,
        ValidatorInterface $validator,
        VariantRepository $repository
    ): Response {
        if ($request->isMethod('POST')) {
            $data = $request->request->get("variant");
            $id = $data['id'];
            /** @var Variant|null $variant */
            $variant = $repository->find($id);
            if (null === $variant) {
                throw $this->createNotFoundException();
            }
            $size = $data['size'];
            $price = floatval($data['price']);
            $oldPrice = floatval($data['oldPrice']);
            $variant->setSize($size)
                ->setPrice($price)
                ->setOldPrice($oldPrice);

            $variant->setSort($this->generateSort($size));
            /** @var ConstraintViolationList $errors */
            $errors = $validator->validate($variant);
            dump($errors);

            if (count($errors) > 0) {
                //... le cas d'erreur
                foreach ($errors as $er)
                    $this->addFlash('errorEdit', $er->getMessage());
            } else {
                $this->manager->persist($variant);
                $this->manager->flush();
                $this->addFlash('successEdit', 'Variant has been edited');
            }

        }

        $ref = $request->headers->get('referer');
        if (!is_string($ref) || $ref) {
            return $this->redirect($ref);
        }
    }


    /**
     * @Route("/variants/delete/{id}" , name="deleteVariant")
     * @param Variant $variant
     * @param Request $request
     * @return RedirectResponse
     */
    public  function deleteVariant(Variant $variant,Request $request)
    {
        $this->manager->remove($variant);
        $this->manager->flush();

        $ref = $request->headers->get('referer');
        if (!is_string($ref) || $ref) {
            return $this->redirect($ref);
        }
    }
    //</editor-fold>




    //<editor-fold desc="Code orders">
    /**
     * @Route("/orders" , name="storeOrders")
     * @param Request $request
     *
     * @return Response
     */
    public function orders(Request $request)
    {
        $data = new SearchByCityOrCountry();
        $form = $this->createForm(SearchByCityOrCountryType::class,$data);
        $form->handleRequest($request);
        $orders = [];
        try {
            if ($form->isSubmitted() && $form->isValid()) {
                if (null != $data->getCity() && null != $data->getCountry()){
                    $orders = $this->getDoctrine()->getRepository(Order::class)
                        ->findByCity($data->getCity());
                }elseif (null == $data->getCity() && null != $data->getCountry()){
                    $orders = $this->getDoctrine()->getRepository(Order::class)
                        ->findByCountry($data->getCountry());
                }else{
                    $orders = $this->getDoctrine()->getRepository(Order::class)->findAll();
                }
            }else{
                $orders = $this->getDoctrine()->getRepository(Order::class)->findAll();
            }

        }catch (\Exception $exception) {
            $this->addFlash('error',$exception->getMessage());
        }

        return $this->render('backOffice/store/orders/listOrders.html.twig', [
            'orders'=>$orders,
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/orders/detail/{id}" , name="adminOrderDetail")
     * @param Order $order
     * @return Response
     */
    public function orderDetail(Order $order)
    {
        return $this->render('backOffice/admin/orders/orderDetail.html.twig', [
            'order'=>$order
        ]);
    }

    /**
     * @Route("/order/cancel/{id}" , name="cancelOrder")
     *
     * @param Order $order
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public function cancelOrder(Order $order,EntityManagerInterface $manager)
    {
        if($order->getDriver() != null){
            $order->setStatus(Order::STATUS_CANCELED);
            $manager->persist($order);
            $manager->flush();

            $statusHistory = new StatusHistory();
            $statusHistory->setStatus(Order::STATUS_CANCELED);
            $statusHistory->setStatusDate(new \DateTime());
            $statusHistory->setStatusOrder($order);
            $manager->persist($statusHistory);
            $manager->flush();

            $this->addFlash('success','Order has been Canceled');
        }
        else{
            throw new NotFoundHttpException();
        }

        return $this->redirectToRoute('adminOrders');
    }

    /**
     * @Route("/orders/{id}/driver" , name="adminChoseDriver")
     * @param Order $order
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    public function orderChoseDriver(Order $order,EntityManagerInterface $manager, Request $request)
    {

        $form = $this->createForm(ChoseDriverType::class,$order,['city' => $order->getCity()]);
        $form->handleRequest($request);
        try {
            if ($form->isSubmitted() && $form->isValid()) {
                if($order->getStatus() != "CANCELED"){
                    $order->setStatus(Order::STATUS_VALIDATED);
                    $manager->persist($order);

                    $driver = $order->getDriver();
                    $driver->setOccupied(true);
                    $manager->persist($driver);

                    $manager->flush();

                    $statusHistory = new StatusHistory();
                    $statusHistory->setStatus(Order::STATUS_VALIDATED);
                    $statusHistory->setStatusDate(new \DateTime());
                    $statusHistory->setStatusOrder($order);
                    $manager->persist($statusHistory);
                    $manager->flush();

                    $this->addFlash('success','Driver has been added');
                }else{
                    throw new NotFoundHttpException();
                }

                return $this->redirectToRoute('adminOrders');
            }

        }catch (\Exception $exception) {
            $this->addFlash('error','Exception Error');
        }

        return $this->render('backOffice/admin/orders/orderChoseDriver.html.twig', [
            'city'=>$order->getCity(),
            'form'=>$form->createView()
        ]);
    }
    //</editor-fold>



    //<editor-fold desc="Country and City registration">
    /**
     * @param array $data A cities object
     *
     * @return array The list of cities converted to a simple array
     */
    private function convertToArray_(array $data)
    {
        $output = [];
        /** @var SubCategory $item */
        foreach ($data as $item) {
            $output[] = [
                'id' => $item->getId(),
                'name' => $item->getName(),
            ];
        }

        return $output;
    }

    /**
     * @Route("/subCategory", name="subCategory", options={"expose"=true})
     *
     * @param Request        $request    The request instance
     * @param SubCategoryRepository $repository The city's repository instance
     *
     * @throws NotFoundHttpException When calling this route without ajax
     *
     * @return JsonResponse A response
     */
    public function retrieveSubCategory(
        Request $request,
        SubCategoryRepository $repository
    ): JsonResponse {
        if ($request->isXmlHttpRequest()) {
            if ($request->isMethod('post')) {
                $category = $request->request->get('category');
                $subCategories = $repository->findByCategory($category);

                return new JsonResponse($this->convertToArray_($subCategories));
            }
        }

        // Simply throw a 404 error.
        throw $this->createNotFoundException('Page not found');
    }
    //</editor-fold>

}