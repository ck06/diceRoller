<?php

declare(strict_types=1);

namespace App\Controller\Stats;

use App\Constants\StatGeneratorConstants;
use App\Service\DiceRoller;
use App\Service\DiceStringParser;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route(path="/api/stats")
 */
class ApiStatsController extends AbstractController
{
    public function __construct(private DiceStringParser $parser, private DiceRoller $roller)
    {
    }

    /**
     * @Route("/roll/{diceString}", name="roll_dice")
     */
    public function roll(string $diceString = "4d6dl1"): JsonResponse
    {
        if (!$this->parser->supports($diceString)) {
            return new JsonResponse(["Error" => 'Unsupported dice string'], 500);
        }

        $diceRollConfigs = $this->parser->parse($diceString);
        $diceRollResult = $this->roller->setConfig($diceRollConfigs)->roll();

        return new JsonResponse($diceRollResult->toArray(), $diceRollResult->isSuccessful() ? 200 : 500);
    }

    /**
     * @Route("/array", name="standard_array")
     */
    public function array(): JsonResponse
    {
        return new JsonResponse(StatGeneratorConstants::getStandardArray());
    }
}
