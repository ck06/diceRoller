<?php

declare(strict_types=1);

namespace App\Controller\Stats;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/stats")
 */
class FrontendController extends AbstractController
{
    /**
     * @Route("/", name="main_index")
     */
    public function index(): Response
    {
        return $this->render('Stats/index.html.twig');
    }

    /**
     * @Route("/buy", name="point_buy_index")
     */
    public function buyPage(): Response
    {
        return $this->render('Stats/buy.html.twig');
    }

    /**
     * @Route("/roll", name="dice_roll_index")
     */
    public function rollPage(): Response
    {
        return $this->render('Stats/roll.html.twig');
    }
}
