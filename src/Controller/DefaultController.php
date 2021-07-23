<?php
declare(strict_types=1);

namespace App\Controller;

use App\Entity\Cart;
use App\Entity\CartLine;
use App\Entity\Follower;
use App\Entity\Product;
use App\Entity\Category;
use App\Entity\Order;
use App\Entity\SponsoredRestaurant;
use App\Entity\StatusHistory;
use App\Entity\User;
use App\Entity\Variant;
use App\Form\AddToCartType;
use App\Form\CartType;
use App\Form\Model\SearchByCity;
use App\Form\Model\SearchByRestaurant;
use App\Form\OrderType;
use App\Form\SearchByChefType;
use App\Form\SearchByCityType;
use App\Form\SearchByRestaurantType;
use App\Form\SearchByStoreType;
use App\Repository\CategoryRepository;
use App\Repository\FollowerRepository;
use App\Repository\ProductRepository;
use App\Repository\UserRepository;
use App\Repository\VariantRepository;
use Doctrine\ORM\EntityManagerInterface;
use PayPalCheckoutSdk\Core\PayPalHttpClient;
use PayPalCheckoutSdk\Core\SandboxEnvironment;
use PayPalCheckoutSdk\Orders\OrdersCaptureRequest;
use PayPalCheckoutSdk\Orders\OrdersCreateRequest;
use PayPalHttp\HttpException;
use phpDocumentor\Reflection\Types\This;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormView;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;

/**
 * Class DefaultController
 * @package App\Controller
 */
class DefaultController extends AbstractController
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
     * StoreController constructor.
     * @param Security $security
     * @param SessionInterface $session
     * @param EntityManagerInterface $manager
     */
    public function __construct(Security $security,SessionInterface $session,EntityManagerInterface $manager) {
        $this->security = $security;
        $this->session = $session;
        $this->manager = $manager;
    }

    //<editor-fold desc="Code index">
    /**
     * @Route("/", name="index")
     *
     * @param Request $request
     * @param CategoryRepository $categoryRepository
     * @param UserRepository $userRepository
     * @param ProductRepository $productRepository
     * @return Response the response
     */
    public function index(
        Request $request,
        CategoryRepository $categoryRepository,
        UserRepository $userRepository,
        ProductRepository $productRepository
    ): Response
    {
        $store = $userRepository->findStore();
        $categories = $categoryRepository->findAll();
        $products = $productRepository->findAll();

        return $this->render('frontOffice/default/index.html.twig', [
            'Store' => $store,
            'Categories' => $categories,
            'Products' => $products,
        ]);
    }

    //</editor-fold>





    //<editor-fold desc="Code cart and product">
    /**
     * @Route("/product/{slug}", name="product")
     * @param Product $product
     * @param Request $request
     * @param SessionInterface $session
     * @param VariantRepository $variantRepository
     * @return Response the response
     */
    public function product(Product $product, Request $request, SessionInterface $session, VariantRepository $variantRepository): Response
    {
        /** @var Cart|null $cart */
        if ($session->has('CART') === true) {
            $cart = $session->get('CART');
        } else {
            $cart = new Cart();
            $session->set('CART', $cart);
        }

        $cartLine = new CartLine();
        $form = $this->createForm(AddToCartType::class, $cartLine);
        $form->handleRequest($request);
        if ($form->isSubmitted() === true && $form->isValid() === true) {

            $data = $request->request->get("variant");
            /** @var Variant|null $variant */
            $variant = $variantRepository->find($data['id']);
            if (null === $variant) {
                throw $this->createNotFoundException();
            }
            if (!$session->has('COUNTRY')){
                if ($variant->getProduct()){
                    //$session->set('COUNTRY',$variant->getProduct()->getCategory()->getStore()->getCity()->getCountry()->getName());
                }else{
                    //$session->set('COUNTRY','notSet');
                }
            }
            $cartLine->setVariant($variant);
            $cart->addCartLine($cartLine);
            $session->set('CART', $cart);

            $this->addFlash('success','Product is Added to Cart');

        }

        return $this->render('frontOffice/default/product.html.twig', [
            'Product' => $product,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/cart", name="cart")
     * @param Request $request
     * @param SessionInterface $session
     * @return Response the response
     */
    public function cart(Request $request,SessionInterface $session): Response
    {
        /** @var Cart|null $cart */
        if ($session->has('CART') === true) {
            $cart = $session->get('CART');
        }
        if (null === $cart) {
            return $this->render('frontOffice/default/cart.html.twig');
        }

        if($cart->getCartLines()->count() == 0){
            return $this->render('frontOffice/default/cart.html.twig');
        }

        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);
        if ($form->isSubmitted() === true) {
            if ($form->isValid() === true) {
                $session->set('CART', $cart);
                if($cart->getCartLines()->count() == 0){
                    return $this->redirectToRoute('index');
                }
            }
        }

        return $this->render('frontOffice/default/cart.html.twig', [
            'cart' => $cart,
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/header-cart", name="headerCart")
     * @param Request $request
     * @param SessionInterface $session
     * @return Response the response
     */
    public function headerCart(Request $request,SessionInterface $session): Response
    {
        /** @var Cart|null $cart */


        if ($session->has('CART') === true) {
            $cart = $session->get('CART');
        } else {
            $cart = new Cart();
            $session->set('CART', $cart);
        }

        $form = $this->createForm(CartType::class, $cart);
        $form->handleRequest($request);
        if ($form->isSubmitted() === true && $form->isValid() === true) {
            $session->set('CART', $cart);
            if($cart->getCartLines()->count() == 0){
                return $this->redirectToRoute('index');
            }

            $ref = $request->headers->get('referer');
            if (!is_string($ref) || $ref) {
                return $this->redirect($ref);
            }
        }

        return $this->render('frontOffice/master/cart.html.twig', [
            'cart' => $cart,
            'form' => $form->createView(),
        ]);
    }
    //</editor-fold>

    //<editor-fold desc="Code checkout">
    /**
     * @Route("/checkout", name="checkout")
     * @param Request $request
     * @param SessionInterface $session
     * @param EntityManagerInterface $manager
     * @param UserRepository $userRepository
     * @param VariantRepository $variantRepository
     * @return Response the response
     */
    public function checkout(
        Request $request,
        SessionInterface $session,
        EntityManagerInterface $manager,
        UserRepository $userRepository,
        VariantRepository $variantRepository
    ): Response
    {
        /** @var Cart|null $cart */
        if ($session->has('CART') === true) {
            $cart = $session->get('CART');
        }
        if (null === $cart) {
            throw new $this->createNotFoundException();
        }

        if($cart->getCartLines()->count() == 0){
            throw new $this->createNotFoundException();
        }

        $stores = [];
        $storesCardLines = [];

        $lines = $cart->getCartLines();
        foreach ($lines as $line){
            /** @var Variant|null $variant */
            $variant = $line->getVariant();

            /** @var User|null $restaurant */
            if ($variant->getProduct() != null){
                $store = $variant->getProduct()->getCategory()->getStore();
            }
            else{
                throw new $this->createNotFoundException();
            }

            $stores[] = $store->getId();
            $storesCardLines[] = [
                'store' => $store->getId(),
                'variants' => $variant->getId(),
                'quantity' => $line->getQuantity(),
            ];

        }

        $order = new Order();
        $form = $this->createForm(OrderType::class, $order);
        $form->handleRequest($request);
        if ($form->isSubmitted() === true && $form->isValid() === true) {

            if($this->security->getUser() != null){
                $customer = $userRepository->find($this->security->getUser());
                $order->setCustomer($customer);
                $order->setFullName($customer->getFullName());
                $order->setPhone($customer->getContactPhone());
            }
            $order->setCity($userRepository->find($stores[0])->getCity());
            $order->setStatus(Order::STATUS_INITIATED);

            $trackNumber = "#".strtoupper(uniqid());
            $order->setOrderDate(new \DateTime());
            $order->setTrackNumber($trackNumber);
            $manager->persist($order);
            $manager->flush();
            $session->remove('COUNTRY');
            $session->remove('CART');

            foreach (array_unique($stores) as $store)
            {
                $newCart = new Cart();
                $newCart->setOrderCart($order);
                $newCart->setStore($userRepository->find($store));
                $manager->persist($newCart);
                $manager->flush();

                foreach ($storesCardLines as $row)
                {
                    if($store == $row['store']){
                        $variant = $variantRepository->find($row['variants']);
                        $line = new CartLine();
                        $line->setCart($newCart)->setVariant($variant)->setQuantity($row['quantity']);
                        $manager->persist($line);
                        $manager->flush();
                    }
                }
            }

            $status = new StatusHistory();
            $status->setStatus(Order::STATUS_INITIATED);
            $status->setStatusDate(new \DateTime());
            $status->setStatusOrder($order);
            $manager->persist($status);
            $manager->flush();

            return $this->redirectToRoute('checkoutComplete');
        }

        return $this->render('frontOffice/default/checkout.html.twig', [
            'cart' => $cart,
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/checkout_complete", name="checkoutComplete")
     *
     * @return Response the response
     */
    public function checkoutComplete(): Response
    {
        return $this->render('frontOffice/default/checkoutComplete.html.twig', []);
    }
    //</editor-fold>



    /**
     * Paypal Orders Checkout Function
     * @Route("/paypal-create-payement", name="paypal_create_payament")
     * @param SessionInterface $session
     *
     * @return JsonResponse
     */
    public function paypalCreateOrder(SessionInterface $session) {

        $clientId = "AZvdHvxArQ6e9N1xtCO-Hc8X6oFGVxizZfnkJvDEYGF4zP727c1NjVD5lGbbDxi4QjGraqrFQ_cxcZNm";
        $clientSecret = "EANsINF_SmRiv25XBD0g5dIDdKmcoQCIpiaGhcazF45itNfSSW1t-Hm1NXoF5C7QxCt094i8KjaCGgn8";
        $env = new SandboxEnvironment($clientId,$clientSecret);
        $client = new PayPalHttpClient($env);
        $req = new OrdersCreateRequest();
        $req->prefer('return=representation');

        // Get Orders from cart
        /** @var Cart|null $cart */
        if ($session->has('CART') === true) {
            $cart = $session->get('CART');
        }
        if (null === $cart) {
            throw new $this->createNotFoundException();
        }
        if($cart->getCartLines()->count() == 0){
            throw new $this->createNotFoundException();
        }
        $lines = $cart->getCartLines();
        $items = [];
        $menuName="";
        $qty = 1;
        $description = "";
        $currency = "USD";
        /*switch ($city = $session->get('CART')) {
            case "Morroco":
                $currency ='MAD';
                break;
            case "Canada":
                $currency='USD';
                break;
            default:
                $currency='USD';
        }*/
        foreach ($lines as $line) {
            /** @var Variant|null $variant */
            $variant = $line->getVariant();
            $qty= $line->getQuantity();
            $price = $variant->getPrice();
            $description = $variant->getSize();

            /** @var User|null $restaurant */
            if ($variant->getMenu() != null) {
                $restaurant = $variant->getMenu()->getUser();
                $menuName = $variant->getMenu()->getName();
            } elseif ($variant->getSubMenu() != null) {
                $restaurant = $variant->getSubMenu()->getMenu()->getUser();
                $menuName = $variant->getSubMenu()->getName();
            } else {
                throw new NotFoundHttpException();
            }

            $item = [
                'name' => $menuName,
                'description' => $description,
                'unit_amount' =>
                    [
                        'currency_code' => $currency,
                        'value' => $price,
                    ],
                'quantity' => $qty,
            ];
            $items[]= $item;
        }
        $req->body =  array(
            'intent' => 'CAPTURE',
            'application_context' =>
                array(
                    'brand_name' => 'Foodepia',
                    'landing_page' => 'BILLING',
                    'user_action' => 'PAY_NOW',
                ),
            'purchase_units' =>
                array(
                    0 =>
                        array(
                            'amount' =>
                                array(
                                    'currency_code' => $currency,
                                    'value' => $cart->total(),
                                    'breakdown' =>
                                        array(
                                            'item_total' =>
                                                array(
                                                    'currency_code' => $currency,
                                                    'value' => $cart->total(),
                                                ),
                                        ),
                                ),
                            'items' => $items,
                        ),
                ),
        );
        try {
            // Call API with your client and get a response for your call
            $response = $client->execute($req);
            return new JsonResponse($response);
            // If call returns body in response, you can get the deserialized version from the result attribute of the response
        }catch (HttpException $ex) {
            $response = $ex->getMessage();
            $response .= $ex->statusCode ;
            return new JsonResponse($response);
        }
    }

    /**
     * Capture Paypal Order
     * @Route("/paypal-capture-order", name="paypal_capture_order")
     * @param Request $req
     *
     * @return JsonResponse
     */
    public function captureOrder(Request $req) {
        $clientId = "AZvdHvxArQ6e9N1xtCO-Hc8X6oFGVxizZfnkJvDEYGF4zP727c1NjVD5lGbbDxi4QjGraqrFQ_cxcZNm";
        $clientSecret = "EANsINF_SmRiv25XBD0g5dIDdKmcoQCIpiaGhcazF45itNfSSW1t-Hm1NXoF5C7QxCt094i8KjaCGgn8";
        $data = $req->request->all();
        $env = new SandboxEnvironment($clientId,$clientSecret);
        $client = new PayPalHttpClient($env);
        $request = new OrdersCaptureRequest($data['paymentID']);
        $request->prefer('return=representation');
        try {
            // Call API with your client and get a response for your call
            $response = $client->execute($request);
            // If call returns body in response, you can get the deserialized version from the result attribute of the response
            return new JsonResponse($response);
        }catch (HttpException $ex) {
            $message = $ex->getMessage();
            return new JsonResponse($message,400);
        }
    }


    /**
     * @Route("/add-follower/{store}", name="addFollower")
     * @param User $store
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public function addFollower(user $store,Request $request,EntityManagerInterface $manager) {

        $follower = new Follower();
        /** @var User $customer */
        $customer = $this->getUser();
        if($customer !== $store){
            $follower->setCustomer($customer);
            $follower->setStore($store);
            $manager->persist($follower);
            $manager->flush();
        }
        else{
            throw new $this->createNotFoundException();
        }

        $ref = $request->headers->get('referer');
        if (!is_string($ref) || $ref) {
            return $this->redirect($ref);
        }
    }

    /**
     * @Route("/remove-follower/{id}", name="removeFollower")
     * @param Follower $follower
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public function removeFollower(Follower $follower,Request $request,EntityManagerInterface $manager) {

        $manager->remove($follower);
        $manager->flush();

        $ref = $request->headers->get('referer');
        if (!is_string($ref) || $ref) {
            return $this->redirect($ref);
        }

    }


    //<editor-fold desc="Contact">
    /**
     * @Route("/contact",name="contact")
     */
    public function contact() {
        return $this->render('frontOffice/default/contact.html.twig');
    }
    //</editor-fold>

    /**
     * @Route("change-locale/{locale}" , name="change_locale")
     * @param $locale
     * @param Request $request
     */
    public function changeLocale($locale,Request $request) {

        $request->getSession()->set('_locale',$locale);
        $request->setLocale($locale);
        return $this->redirect($request->headers->get('referer'));
    }
}
