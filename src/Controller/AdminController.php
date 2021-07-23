<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\City;
use App\Entity\Country;
use App\Entity\Gallery;
use App\Entity\Order;
use App\Entity\StatusHistory;
use App\Entity\User;
use App\Form\AdminType;
use App\Form\ChoseDriverType;
use App\Form\CountryType;
use App\Form\CityType;
use App\Form\EditCoverImageType;
use App\Form\EditProfileImageType;
use App\Form\EditSecondAdminInformationsType;
use App\Form\EditStoreInfoType;
use App\Form\EditUserPasswordType;
use App\Form\Model\SearchByCityOrCountry;
use App\Form\SearchByCityOrCountryType;
use App\Form\SecondAdminType;
use App\Service\ImageManager;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use mysql_xdevapi\Exception;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

/**
 * Class AdminController
 * @package App\Controller
 * @Route("/admin")
 */

class AdminController extends AbstractController
{
    private $security;
    private $session;

    /**
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * @var ImageManager
     */
    private $imageManager;


    public function __construct(
        Security $security,
        SessionInterface $session,
        EntityManagerInterface $manager,
        ImageManager $imageManager
    ){
        $this->security = $security;
        $this->session = $session;
        $this->manager = $manager;
        $this->imageManager = $imageManager;

    }

    //<editor-fold desc="Code dashboardAdmin">
    /**
     * @Route("/", name="dashboardAdmin")
     */
    public function index(): Response
    {
        return $this->render('backOffice/admin/index.html.twig');
    }
    //</editor-fold>

    //<editor-fold desc="Code register and login">
    /**
     * @Route("/login", name="adminLogin")
     */
    public function login(): Response
    {
        return $this->render('BackOffice/admin/login.html.twig');
    }

    /**
     * @Route("/register", name="adminRegister")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function register(Request $request, EntityManagerInterface $manager,UserPasswordEncoderInterface $encoder): Response
    {
        $user = new User();
        $form = $this->createForm(AdminType::class,$user);
        $form ->handleRequest($request);



        if ($form->isSubmitted() && $form->isValid()){
            try {
                $hash = $encoder->encodePassword($user,$user->getPassword());
                $user->setPassword($hash);
                $user->setRegistrationDate(new DateTime('now'));
                $user->setRoles(['ROLE_ADMIN']);
                $manager->persist($user);
                $manager->flush();

                $gallery = new Gallery();
                $gallery->setStore($user);
                $manager->persist($gallery);
                $manager->flush();

                return $this->redirectToRoute('adminLogin');

            }catch (Exception $ex){
                $this->addFlash('error','error');
            }
        }

        return $this->render('backOffice/admin/signUp.html.twig',[
            'form'=>$form->createView()
        ]);
    }
    //</editor-fold>



    //<editor-fold desc="Code Profile">

    /**
     * @Route("/profile",name="dashAdminProfile")
     * @param Request $request
     * @return Response
     */
    public function profile(Request $request):Response
    {
        /**
         * @var User $admin
         */
        $admin = $this->getUser();


        $formProfile = $this->createForm(EditProfileImageType::class,$admin);
        $formProfile->handleRequest($request);

        $formCover = $this->createForm(EditCoverImageType::class,$admin);
        $formCover->handleRequest($request);

        $form = $this->createForm(EditStoreInfoType::class,$admin);
        $form->handleRequest($request);

        if ($formProfile->isSubmitted() && $formProfile->isValid()) {
            $profileImage = $formProfile->get('brochureProfile')->getData();
            $this->imageManager->updateProfile($profileImage);
            return $this->redirectToRoute('dashAdminProfile');
        }
        else if ($formCover->isSubmitted() && $formCover->isValid()) {
            $coverImage = $formCover->get('brochureCover')->getData();
            $this->imageManager->updateCover($coverImage);
            return $this->redirectToRoute('dashAdminProfile');
        }
        else if ($form->isSubmitted() && $form->isValid()) {

            $this->imageManager->updateGallery($request);
            $this->manager->persist($admin);
            $this->manager->flush();
            $this->addFlash('success','Your information has been changed successfully');
            return $this->redirectToRoute('dashAdminProfile');
        }

        return $this->render('backOffice/admin/account/profile.html.twig',[
            'formProfile'=>$formProfile->createView(),
            'formCover'=>$formCover->createView(),
            'form'=>$form->createView(),
            'admin'=>$admin
        ]);
    }
    //</editor-fold>


    //<editor-fold desc="Code country">
    /**
     * @Route("/countries", name="listCountries")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function listCounties(Request $request, EntityManagerInterface $manager): Response
    {
        $country = new Country();
        $form = $this->createForm(CountryType::class,$country);
        $form ->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()){
                $manager->persist($country);
                $manager->flush();

                return $this->redirectToRoute("listCountries");
            }
        }catch (Exception $ex){
            $this->addFlash('error','error');
        }

        $Countries = $this->getDoctrine()->getRepository(Country::class)->findAll();

        return $this->render('BackOffice/admin/country/listCountries.html.twig', [
            'Countries' => $Countries,
            'form'=>$form->createView(),
        ]);
    }


    /**
     * @Route("/countries/edit/{id}" , name="editCountry")
     * @param Country $country
     * @param Request $request
     * @return RedirectResponse|Response
     */
    public  function editCountry(Country $country,Request $request)
    {
        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(CountryType::class,new Country());
        $form ->handleRequest($request);
        $formEdit = $this->createForm(CountryType::class,$country);
        $formEdit ->handleRequest($request);

        try {
            if ($formEdit->isSubmitted() && $formEdit->isValid()){
                $entityManager->flush();
                return $this->redirectToRoute("listCountries");
            }
        }catch (Exception $ex){
            $this->addFlash('error','error');
        }
        $Countries = $this->getDoctrine()->getRepository(Country::class)->findAll();

        return $this->render('BackOffice/admin/country/listCountries.html.twig', [
            'Countries' => $Countries,
            'form'=>$form->createView(),
            'formEdit'=>$formEdit->createView(),
        ]);
    }

    /**
     * @Route("/countries/delete/{id}" , name="deleteCountry")
     * @param Country $country
     * @return RedirectResponse
     */
    public  function deleteCountry(Country $country)
    {
        $entityManager = $this->getDoctrine()->getManager();
       // $country = $entityManager->getRepository(Country::class)->find($id);
        $entityManager->remove($country);
        $entityManager->flush();

        return $this->redirectToRoute("listCountries");
    }
    //</editor-fold>

    //<editor-fold desc="Code City">
    /**
     * @Route("/cities", name="listCities")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function listCities(Request $request, EntityManagerInterface $manager): Response
    {
        $city = new City();
        $form = $this->createForm(CityType::class,$city);
        $form ->handleRequest($request);

        try {
            if ($form->isSubmitted() && $form->isValid()){
                $manager->persist($city);
                $manager->flush();
                return $this->redirectToRoute("listCities");
            }
        }catch (Exception $ex){
            $this->addFlash('error','error');
        }
        $cities = $this->getDoctrine()->getRepository(City::class)->findAll();

        return $this->render('BackOffice/admin/cities/listCities.html.twig', [
            'Cities' => $cities,
            'form'=>$form->createView(),
        ]);
    }

    /**
     * @Route("/cities/edit/{id}" , name="editCity")
     * @param City $city
     * @param Request $request
     * @return Response
     */
    public  function editCity(City $city,Request $request): Response
    {
        $entityManager = $this->getDoctrine()->getManager();
        $form = $this->createForm(CityType::class,new $city());
        $form ->handleRequest($request);
        $formEdit = $this->createForm(CityType::class,$city);
        $formEdit ->handleRequest($request);

        try {
            if ($formEdit->isSubmitted() && $formEdit->isValid()){
                $entityManager->flush();
                return $this->redirectToRoute("listCities");
            }
        }catch (Exception $ex){
            $this->addFlash('error','error');
        }

        $Cities = $this->getDoctrine()->getRepository(City::class)->findAll();

        return $this->render('BackOffice/admin/cities/listCities.html.twig', [
            'Cities' => $Cities,
            'form'=>$form->createView(),
            'formEdit'=>$formEdit->createView(),
        ]);
    }

    /**
     * @Route("/city/delete/{id}" , name="deleteCity")
     * @param City $city
     * @return RedirectResponse
     */
    public  function deleteCity(city $city)
    {
        $entityManager = $this->getDoctrine()->getManager();
        // $country = $entityManager->getRepository($city::class)->find($id);
        $entityManager->remove($city);
        $entityManager->flush();

        return $this->redirectToRoute("listCities");
    }
    //</editor-fold>


    //<editor-fold desc="Code Driver">
    /**
     * @Route("/drivers/" , name="dashAdminDrivers")
     * @param Request $request
     *
     * @return Response
     */
    public function drivers(Request $request)
    {
        $data = new SearchByCityOrCountry();
        $form = $this->createForm(SearchByCityOrCountryType::class,$data);
        $form->handleRequest($request);
        $drivers = [];
        try {
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    if (null != $data->getCity() && null!= $data->getCountry()){
                        $drivers = $this->getDoctrine()->getRepository(User::class)->findDriversByCity($data->getCity());
                    }elseif (null == $data->getCity() && null != $data->getCountry()){
                        $drivers = $this->getDoctrine()->getRepository(Order::class)->findDriversByCountry($data->getCountry());
                    }else{
                        $drivers = $this->getDoctrine()->getRepository(User::class)->findUsersByRole("ROLE_DRIVER");
                    }
                }catch (\Exception $ex ) {
                    $this->addFlash('error',$ex->getMessage());
                }
            }else{
                $drivers = $this->getDoctrine()->getRepository(User::class)->findUsersByRole("ROLE_DRIVER");
            }

        }catch (\Exception $exception) {
            $this->addFlash('error',$exception->getMessage());
        }

        return $this->render('backOffice/admin/drivers/drivers.html.twig', [
            'drivers'=>$drivers,
            'form'=>$form->createView()
        ]);
    }
    //</editor-fold>

    //<editor-fold desc="Code Restaurant">
    /**
     * @Route("/restaurants/" , name="adminRestaurants")
     * @param Request $request
     *
     * @return Response
     */
    public function adminRestaurant(Request $request)
    {
        $data = new SearchByCityOrCountry();
        $form = $this->createForm(SearchByCityOrCountryType::class,$data);
        $form->handleRequest($request);
        $restaurants = [];
        try {
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    if (null != $data->getCity() && null!= $data->getCountry()){
                        $restaurants = $this->getDoctrine()->getRepository(User::class)->findRestaurantByCity($data->getCity());
                    }elseif (null == $data->getCity() && null != $data->getCountry()){
                        $restaurants = $this->getDoctrine()->getRepository(Order::class)->findRestaurantByCountry($data->getCountry());
                    }else{
                        $restaurants = $this->getDoctrine()->getRepository(User::class)->findUsersByRole("ROLE_RESTAURANT");
                    }
                }catch (\Exception $ex ) {
                    $this->addFlash('error',$ex->getMessage());
                }
            }else{
                $restaurants = $this->getDoctrine()->getRepository(User::class)->findUsersByRole("ROLE_RESTAURANT");
            }

        }catch (\Exception $exception) {
            $this->addFlash('error',$exception->getMessage());
        }


        return $this->render('backOffice/admin/restaurant/restaurant.html.twig', [
            'restaurants'=>$restaurants,
            'form'=>$form->createView()
        ]);
    }
    //</editor-fold>


    //<editor-fold desc="Second Admin Part">
    /**
     * @Route("/new-second-admin",name="newSecondAdmin")
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return Response
     */
    public function newSecondAdmin(
        EntityManagerInterface $manager,
        Request $request,
        UserPasswordEncoderInterface $encoder ) {

        $secondAdmin  = new User();
        $form = $this->createForm(SecondAdminType::class,$secondAdmin);
        $form->handleRequest($request);
        try {
            if ($form->isSubmitted() && $form->isValid()) {
                $hash = $encoder->encodePassword($secondAdmin,$secondAdmin->getPassword());
                $secondAdmin->setPassword($hash);
                $secondAdmin->setRegistrationDate(new DateTime('now'));
                $secondAdmin->setRoles(['ROLE_SECOND_ADMIN']);
                $manager->persist($secondAdmin);
                $manager->flush();
                $this->addFlash('success','Second Admin added');

                return $this->render('backOffice/admin/secondAdmin/newSecondAdmin.html.twig',[
                    'form'=>$form->createView()
                ]);
            }

        }catch (\Exception $exception) {
            $this->addFlash('error','Exception Error');
        }

        return $this->render('backOffice/admin/secondAdmin/newSecondAdmin.html.twig',[
            'form'=>$form->createView()
        ]);
    }

    /**
     * @Route("/list-second-admin",name="listSecondAdmin")
     * @param Request $request
     *
     * @return Response
     */
    public function secondAdminList(Request $request) {
        $data = new SearchByCityOrCountry();
        $form = $this->createForm(SearchByCityOrCountryType::class,$data);
        $form->handleRequest($request);
        $secondAdmins = [];
        try {
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    if (null != $data->getCity() && null!= $data->getCountry()){
                        $secondAdmins = $this->getDoctrine()->getRepository(User::class)
                                        ->findSecondAdminByCity($data->getCity());
                    }elseif (null == $data->getCity() && null != $data->getCountry()){
                        $secondAdmins = $this->getDoctrine()->getRepository(Order::class)
                                        ->findSecondAdminByCountry($data->getCountry());
                    }else{
                        $secondAdmins = $this->getDoctrine()->getRepository(User::class)
                                        ->findUsersByRole("ROLE_SECOND_ADMIN");
                    }
                }catch (\Exception $ex ) {
                    $this->addFlash('error',$ex->getMessage());
                }
            }else{
                $secondAdmins = $this->getDoctrine()->getRepository(User::class)
                                ->findUsersByRole("ROLE_SECOND_ADMIN");
            }

        }catch (\Exception $exception) {
            $this->addFlash('error',$exception->getMessage());
        }

        return $this->render('backOffice/admin/secondAdmin/listSecondAdmin.html.twig',[
            'secondAdmins'=>$secondAdmins
        ]);
    }

    /**
     * @Route("/edit-second-admin/{id}",name="editSecondAdmin")
     * @param User $secondAdmin
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     * @return Response
     */
    public function editSecondAdminList(
        User $secondAdmin ,
        EntityManagerInterface $manager ,
        Request $request ,
        UserPasswordEncoderInterface $encoder) {

        $role = $secondAdmin->getRoles();
        if ($role[0]=="ROLE_SECOND_ADMIN") {

            $form = $this->createForm(EditSecondAdminInformationsType::class,$secondAdmin);
            $formPassword = $this->createForm(EditUserPasswordType::class,$secondAdmin);
            $form->handleRequest($request);
            $formPassword->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $manager->persist($secondAdmin);
                    $manager->flush();
                    $this->addFlash('success', "Information's Edited");
                }catch (\Exception $exception) {
                    $this->addFlash('error','Server Form Error');
                }
            }elseif ($formPassword->isSubmitted() && $formPassword->isValid()) {
                try {
                    $hash = $encoder->encodePassword($secondAdmin, $secondAdmin->getPassword());
                    $secondAdmin->setPassword($hash);
                    $manager->persist($secondAdmin);
                    $manager->flush();
                    $this->addFlash('success', 'Password Changed');

                } catch (\Exception $exception) {
                    $this->addFlash('error', 'Error');
                }
            }

            return $this->render('backOffice/admin/secondAdmin/editSecondAdmin.html.twig',[
                'form'=>$form->createView(),
                'formPass'=> $formPassword->createView()
            ]);
        }else {
            throw new NotFoundHttpException();
        }
    }
    //</editor-fold>

    //<editor-fold desc="Code orders">
    /**
     * @Route("/orders" , name="adminOrders")
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

        return $this->render('backOffice/admin/orders/listOrders.html.twig', [
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



    /**
     * @Route("/customers",name="customers_list")
     * @param Request $request
     *
     * @return Response
     */
    public function customersList(Request $request) {
        $data = new SearchByCityOrCountry();
        $form = $this->createForm(SearchByCityOrCountryType::class,$data);
        $form->handleRequest($request);
        $customers = [];
        try {
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    if (null != $data->getCity() && null!= $data->getCountry()){
                        $customers = $this->getDoctrine()->getRepository(User::class)->findCustomerByCity($data->getCity());
                    }elseif (null == $data->getCity() && null != $data->getCountry()){
                        $customers = $this->getDoctrine()->getRepository(Order::class)->findCustomerByCountry($data->getCountry());
                    }else{
                        $customers = $this->getDoctrine()->getRepository(User::class)->findUsersByRole("ROLE_CUSTOMER");
                    }
                }catch (\Exception $ex ) {
                    $this->addFlash('error',$ex->getMessage());
                }
            }else{
                $customers = $this->getDoctrine()->getRepository(User::class)->findUsersByRole("ROLE_CUSTOMER");
            }

        }catch (\Exception $exception) {
            $this->addFlash('error',$exception->getMessage());
        }


        return $this->render('backOffice/admin/customer/customers_list.html.twig', [
            'customers'=>$customers,
            'form'=>$form->createView()
        ]);
    }

}
