<?php

namespace App\Controller;

use App\Entity\City;
use App\Entity\Driver;
use App\Entity\Order;
use App\Entity\Section;
use App\Entity\Sections;
use App\Entity\StatusHistory;
use App\Entity\User;
use App\Form\ChangePasswordType;
use App\Form\ChoseDriverType;
use App\Form\EditSecondAdminInformationsType;
use App\Form\Model\ChangePassword;
use App\Form\SectionType;
use App\Repository\SectionRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;
use Symfony\Component\Security\Core\Security;

/**
 * Class SecondAdminController
 * @package App\Controller
 * @Route("/second-admin")
 */
class SecondAdminController extends AbstractController
{
    private $security;
    private $session;
    private $manager;

    public function __construct(Security $security,SessionInterface $session , EntityManagerInterface $manager){
        $this->security = $security;
        $this->session = $session;
        $this->manager = $manager;
    }

    /**
     * @Route("/", name="dashboardSecondAdmin")
     */
    public function index(): Response
    {
        return $this->render('backOffice/secondAdmin/index.html.twig');
    }

    /**
     * @Route("/profile", name="second_admin_profile")
     * @param Request $request
     * @param UserPasswordEncoderInterface $encoder
     *
     * @return Response
     */
    public function profile(Request $request ,UserPasswordEncoderInterface $encoder): Response
    {
        $secondAdmin = $this->getUser();
        $form = $this->createForm(EditSecondAdminInformationsType::class,$secondAdmin);
        $form->handleRequest($request);

        $changePasswordModel = new ChangePassword();
        $formPassword = $this->createForm(ChangePasswordType::class,$changePasswordModel);
        $formPassword->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                $this->manager->persist($secondAdmin);
                $this->manager->flush();
                $this->addFlash('success','Informations Edited');
            }catch (\Exception $exception) {
                $this->addFlash('error','Error Informations Exception');
            }

            return $this->render('backOffice/secondAdmin/account/profile.html.twig',[
                'form'=>$form->createView(),
                'formPass'=>$formPassword->createView(),
                'user'=>$secondAdmin
            ]);
        }elseif ($formPassword->isSubmitted() && $formPassword->isValid()) {

            try {
                $hash = $encoder->encodePassword($secondAdmin,$changePasswordModel->getNewPassword());
                $secondAdmin->setPassword($hash);
                $this->manager->persist($secondAdmin);
                $this->manager->flush();

                $this->addFlash('success', 'Password Changed');
            } catch (\Exception $exception) {
                $this->addFlash('error','Pass Error Exception');
            }
        }

        return $this->render('backOffice/secondAdmin/account/profile.html.twig',[
            'form'=>$form->createView(),
            'formPass'=>$formPassword->createView(),
            'user'=>$secondAdmin
        ]);
    }

    /**
     * @Route("/drivers", name="second_admin_drivers")
     */
    public function drivers() {
        /**
         * @var User $secondAdmin
         */
        $secondAdmin = $this->getUser();
        $city = $secondAdmin->getCity();
        $drivers = $this->getDoctrine()->getRepository(User::class)->findDriversByCity($city);

        return $this->render('backOffice/secondAdmin/drivers/drivers.html.twig',[
            'drivers'=>$drivers
        ]);

    }
    /**
     * @Route("/drivers-pending", name="second_admin_drivers_pending")
     */
    public function driversPending() {
        /**
         * @var User $secondAdmin
         */
        $secondAdmin = $this->getUser();
        $city = $secondAdmin->getCity();
        $drivers = $this->getDoctrine()->getRepository(User::class)->findDriversPendingByCity($city);

        return $this->render('backOffice/secondAdmin/drivers/drivers_pending.html.twig',[
            'drivers'=>$drivers
        ]);

    }

    /**
     * @Route("/drivers/active/{id}",name="second_admin_drivers_active")
     * @param User $driver
     * @param EntityManagerInterface $manager
     * @param Request $request
     *
     * @return Response
     */
    public function activeDrivers(User $driver, EntityManagerInterface $manager,Request $request) {
        $role = $driver->getRoles();
        /**
         * @var User $secondAdmin
         */
        $secondAdmin = $this->getUser();
        dump($role);
        if($driver->getCity() === $secondAdmin->getCity() && $role[0] == "ROLE_DRIVER" ){
            $driver->setStatus('active');
            $manager->persist($driver);
            $manager->flush();
            $this->addFlash('success','Driver approved');
        }else {
            throw new  NotFoundHttpException();
        }
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }
    /**
     * @Route("/drivers/active/{id}",name="second_admin_drivers_cancel")
     * @param User $driver
     * @param EntityManagerInterface $manager
     * @param Request $request
     *
     * @return RedirectResponse|NotFoundHttpException
     */
    public function cancelDrivers(User $driver, EntityManagerInterface $manager,Request $request) {
        $role = $driver->getRoles();
        /**
         * @var User $secondAdmin
         */
        $secondAdmin = $this->getUser();
        if($driver->getCity() === $secondAdmin->getCity() && $role[0] == "ROLE_DRIVER" ){
            $driver->setStatus('block');
            $manager->persist($driver);
            $manager->flush();
            $this->addFlash('success','Driver canceled');
        }else {
            throw new  NotFoundHttpException();
        }
        $referer = $request->headers->get('referer');
        return $this->redirect($referer);
    }

    /**
     * @Route("/restaurants", name="secondAdminRestaurants")
     */
    public function restaurants() {
        /**
         * @var User $secondAdmin
         */
        $secondAdmin = $this->getUser();
        $city = $secondAdmin->getCity();
        $restaurants = $this->getDoctrine()->getRepository(User::class)->findRestaurantByCity($city);

        return $this->render('backOffice/secondAdmin/restaurant/restaurant.html.twig', [
            'restaurants'=>$restaurants
        ]);
    }

    /**
     * @Route("/localisation-drivers", name="second_admin_localisationDrivers")
     * @return Response
     */
    public function localisationDriver() {
        /**
         * @var User $secondAdmin
         */
        $secondAdmin = $this->getUser();
        $city = $secondAdmin->getCity();
        $drivers = $this->getDoctrine()->getRepository(User::class)->findDriversByCity($city);

        return $this->render('backOffice/secondAdmin/drivers/localisationDrivers.html.twig',[
            'drivers'=>$drivers
        ]);
    }


    //<editor-fold desc="Code orders">
    /**
     * @Route("/orders" , name="secondAdminOrders")
     */
    public function orders()
    {
        /**
         * @var User $secondAdmin
         */
        $secondAdmin = $this->getUser();
        /**
         * @var City $city
         */
        $city = $secondAdmin->getCity();

        $orders = $this->getDoctrine()->getRepository(Order::class)->findByCity($city);

        return $this->render('backOffice/secondAdmin/orders/listOrders.html.twig', [
            'orders'=>$orders
        ]);
    }

    /**
     * @Route("/orders/detail/{id}" , name="secondAdminOrderDetail")
     * @param Order $order
     * @return Response
     */
    public function orderDetail(Order $order)
    {
        if ($order->getCity() != $this->getUser()->getCity()) throw new NotFoundHttpException();
        return $this->render('backOffice/secondAdmin/orders/orderDetail.html.twig', [
            'order'=>$order
        ]);
    }


    /**
     * @Route("/order/cancel/{id}" , name="secondAdminCancelOrder")
     *
     * @param Order $order
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public  function cancelOrder(Order $order,EntityManagerInterface $manager)
    {
        if ($order->getCity() != $this->getUser()->getCity()) throw new NotFoundHttpException();
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

        return $this->redirectToRoute('secondAdminOrders');
    }

    /**
     * @Route("/orders/{id}/driver" , name="secondAdminChoseDriver")
     * @param Order $order
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return Response
     */
    public function orderChoseDriver(Order $order,EntityManagerInterface $manager, Request $request)
    {
        if ($order->getCity() != $this->getUser()->getCity()) throw new NotFoundHttpException();
        $form = $this->createForm(ChoseDriverType::class,$order,['city' => $order->getCity()]);
        $form->handleRequest($request);
        try {
            if ($form->isSubmitted() && $form->isValid()) {
                if($order->getStatus() == "CANCELED"){
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
                }
                else{
                    throw new NotFoundHttpException();
                }

                return $this->redirectToRoute('secondAdminOrders');
            }
        }catch (\Exception $exception) {
            $this->addFlash('error','Exception Error');
        }

        return $this->render('backOffice/secondAdmin/orders/orderChoseDriver.html.twig', [
            'city'=>$order->getCity(),
            'form'=>$form->createView(),
        ]);
    }
    //</editor-fold>

    /**
     * @Route("/sections",name="second_admin_sections")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function sections(Request $request , EntityManagerInterface $manager) {

        $section = new Section();
        $form = $this->createForm(SectionType::class,$section);
        $form->handleRequest($request);
        if ( $form->isSubmitted() && $form->isValid()) {
            try {
                $section->setCity($this->getUser()->getCity());
                $manager->persist($section);
                $manager->flush();
                $this->addFlash('success','Section added');
            }catch (\Exception $ex) {
                $this->addFlash('error','Exception Error');
            }
        }
        $sections = $this->getDoctrine()->getRepository(Section::class)
                        ->findByCity($this->getUser()->getCity());
        return $this->render('backOffice/secondAdmin/sections/sections.html.twig',[
            'form'=>$form->createView(),
            'sections'=>$sections
        ]);
    }

    /**
     * @Route("/edit-section/{id}",name="second_admin_edit_section")
     * @param Request $request
     * @param Section $section
     * @param EntityManagerInterface $manager
     *
     * @return Response
     */
    public function editSections(Request $request ,Section $section, EntityManagerInterface $manager) {
        if ($this->getUser()->getCity() === $section->getCity()){
            $form = $this->createForm(SectionType::class,$section);
            $form->handleRequest($request);
            if ($form->isSubmitted() && $form->isValid()) {
                try {
                    $manager->persist($section);
                    $manager->flush();
                    $this->addFlash('success','Section Edited');
                    return  $this->redirectToRoute('second_admin_sections');
                }catch (\Exception $ex) {
                    $this->addFlash('error','Exception Error');
                }
            }
            return $this->render('backOffice/secondAdmin/sections/edit_section.html.twig',[
                'form'=>$form->createView()
            ]);
        }
        throw  new NotFoundHttpException();
    }

    /**
     * @Route("/assing-drivers-to-section",name="second_admin_assign_drivers_to_section",methods={"GET","POST"})
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserRepository $repository
     * @param SectionRepository $sectionRepository
     */
    public function assignDriversToSection (
        Request $request,
        EntityManagerInterface $manager ,
        UserRepository $repository ,
        SectionRepository $sectionRepository) {

        $query = $request->query->get('checkbox');
        $drivers = [];

        $form = $this->createFormBuilder()
            ->add('sections', EntityType::class,[
                'class' => Section::class,
                'choice_label' => 'name',
                'choices'=>$sectionRepository->findByCity($this->getUser()->getCity())
            ])
            ->getForm();
        $form->handleRequest($request);
        $data = $form->getData();

        if ($form->isSubmitted() && $form->isValid()) {
            try {
                foreach ($query as $id){
                    $driver = $repository->findDriversById($id);
                    $drivers []=$driver;
                    /**
                     * @var User $driver
                     */
                    $driver->setSection($data['sections']);
                    $manager->persist($driver);
                    $manager->flush();
                }
                $this->addFlash('success','success');
            }catch (\Exception $exception){
                $this->addFlash('error','Exception Error');
            }
        }else{
            foreach ($query as $id){
                $driver = $repository->findDriversById($id);
                $drivers []=$driver;
            }
        }

        return $this->render('backOffice/secondAdmin/drivers/assign_drivers_to_section.html.twig',[
            'form'=>$form->createView(),
            'drivers'=>$drivers
        ]);
    }
}
