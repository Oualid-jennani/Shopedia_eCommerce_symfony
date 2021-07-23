<?php

declare(strict_types=1);

namespace App\Controller;

use App\Entity\Order;
use App\Entity\StatusHistory;
use App\Entity\Transportation;
use App\Entity\User;
use App\Form\EditDriverTransportationType;
use App\Form\EditDriverType;
use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security as Sec ;


/**
 * Class DriverController.
 *
 * @Route("/driver")
 */
class DriverController extends AbstractController
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
     * @var ArrayCollection
     */
    private $formArray;

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
        $this->formArray = new ArrayCollection();
    }

    /**
     * @Route("/", name="dashboardDriver")
     */
    public function index(): Response
    {
        return $this->render('backOffice/driver/index.html.twig');
    }

    /**
     * @Route("/profile", name="dashDriverProfile")
     *
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function profile(Request $request,EntityManagerInterface $manager): Response
    {
        $user = $this->getDoctrine()
            ->getRepository(User::class)
            ->findOneBy([
                'id'=>$this->getUser(),
            ]);
        
        $form = $this->createForm(EditDriverType::class,$this->getUser());

        $form->handleRequest($request);

        if ($form->isSubmitted()) {
            if ($form->isValid()) {
                try {
                    $manager->persist($user);
                    $manager->flush();
                    $this->addFlash('success', 'Information Edited');

                    return $this->render('backOffice/driver/profile.html.twig', [
                        'user'=>$user,
                        'form'=>$form->createView()
                    ]);
                } catch (\Exception $exception) {
                    $this->addFlash('error', 'Error');
                }
            }
        }

        return $this->render('backOffice/driver/profile.html.twig',[
            'user'=>$user,
            'form'=>$form->createView(),
        ]);
    }


    // -------------  Transportation Management  ------------------------------

    /**
     * @Route("/transportation/{id}", name="addDriverTransportation")
     *
     * @param User                   $user    The user's instance
     * @param Request                $request The request object
     * @param EntityManagerInterface $manager The entity manager
     *
     * @return Response The response
     */
    public function addDriverTransportation(
        User $user,
        Request $request ,
        EntityManagerInterface $manager
    ): Response {
        if (null !== $user->getTransportation()) {
            $this->addFlash('warning', 'You have a Transportation');

            return $this->redirectToRoute('dashboardDriver');
        } else {
            $transportation = new Transportation();
            $form = $this->createForm(EditDriverTransportationType::class,$transportation);
            $form->handleRequest($request);

            if ($form->isSubmitted()) {
                if ($form->isValid()) {
                    try {
                        $transportation->setUser($user);
                        $manager->persist($transportation);
                        $manager->flush();
                        $this->addFlash('success', 'Transportation added');

                        return $this->redirectToRoute('dashboardDriver');
                    } catch (\Exception $exception) {
                        $this->addFlash('error', 'Error');
                    }
                }
            }

            return $this->render('backOffice/driver/addTransportation.html.twig',[
                'form'=>$form->createView()
            ]);
        }
    }


    //<editor-fold desc="Code orders">
    /**
     * @Route("/orders" , name="dashDriverOrders")
     */
    public function orders()
    {
        /**
         * @var User $driver
         */$driver = $this->getUser();

        $orders = $driver->getOrderDriver();
        //$orders = $this->getDoctrine()->getRepository(Order::class)->findByDriver($this->security->getUser());

        return $this->render('backOffice/driver/orders/listOrders.html.twig', [
            'orders'=>$orders
        ]);
    }

    /**
     * @Sec("is_granted('IS_AUTHENTICATED_FULLY') and user === order.getDriver()")
     * @Route("/orders/detail/{id}" , name="driverOrderDetail")
     * @param Order $order
     * @return Response
     */
    public function orderDetail(Order $order)
    {
        return $this->render('backOffice/driver/orders/orderDetail.html.twig', [
            'order'=>$order
        ]);
    }

    /**
     * @Sec("is_granted('IS_AUTHENTICATED_FULLY') and user === order.getDriver()")
     * @Route("/order/transit/{id}" , name="transitOrder")
     *
     * @param Order $order
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public  function transitOrder(Order $order,EntityManagerInterface $manager)
    {
        if($order->getStatus() == "VALIDATED") {
            $order->setStatus(Order::STATUS_IN_TRANSIT);
            $manager->persist($order);
            $manager->flush();

            $statusHistory = new StatusHistory();
            $statusHistory->setStatus(Order::STATUS_IN_TRANSIT);
            $statusHistory->setStatusDate(new \DateTime());
            $statusHistory->setStatusOrder($order);
            $manager->persist($statusHistory);
            $manager->flush();

            $this->addFlash('success','Order In Transit');
        }
        else{
            throw new NotFoundHttpException();
        }

        return $this->redirectToRoute('dashDriverOrders');
    }

    /**
     * @Sec("is_granted('IS_AUTHENTICATED_FULLY') and user === order.getDriver()")
     * @Route("/order/delivered/{id}" , name="deliveredOrder")
     *
     * @param Order $order
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public  function deliveredOrder(Order $order,EntityManagerInterface $manager)
    {
        if($order->getStatus() == "IN TRANSIT"){
            $order->setStatus(Order::STATUS_DELIVERED);
            $manager->persist($order);

            /**
             * @var User $driver
             */
            $driver = $this->getUser();
            $driver->setOccupied(false);
            $manager->persist($driver);

            $statusHistory = new StatusHistory();
            $statusHistory->setStatus(Order::STATUS_DELIVERED);
            $statusHistory->setStatusDate(new \DateTime());
            $statusHistory->setStatusOrder($order);
            $manager->persist($statusHistory);
            $manager->flush();

            $this->addFlash('success','Order Delivered');
        }
        else{
            throw new NotFoundHttpException();
        }


        return $this->redirectToRoute('dashDriverOrders');
    }

    /**
     * @Sec("is_granted('IS_AUTHENTICATED_FULLY') and user === order.getDriver()")
     * @Route("/order/returned/{id}" , name="returnedOrder")
     *
     * @param Order $order
     * @param EntityManagerInterface $manager
     * @return RedirectResponse
     */
    public  function returnedOrder(Order $order,EntityManagerInterface $manager)
    {
        if($order->getStatus() == "IN TRANSIT") {
            $order->setStatus(Order::STATUS_RETURNED);
            $manager->persist($order);

            /**
             * @var User $driver
             */
            $driver = $this->getUser();
            $driver->setOccupied(false);
            $manager->persist($driver);

            $statusHistory = new StatusHistory();
            $statusHistory->setStatus(Order::STATUS_RETURNED);
            $statusHistory->setStatusDate(new \DateTime());
            $statusHistory->setStatusOrder($order);
            $manager->persist($statusHistory);
            $manager->flush();

            $this->addFlash('success', 'Order Delivered');
        }
        else{
            throw new NotFoundHttpException();
        }

        return $this->redirectToRoute('dashDriverOrders');
    }

    //</editor-fold>

}
