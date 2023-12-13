<?php

namespace App\Controller;

use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class TravelController
{
    /**
     * @param array $discountRules
     */
    public function __construct(protected array $discountRules = [
        3 => 0.8,
        6 => ['percentage' => 0.3, 'max_discount' => 4500],
        12 => 0.1,
    ]) {

    }

    /**
     * @Route("/calculate-travel-cost", methods={"POST"})
     */
    public function calculateTravelCost(Request $request): JsonResponse
    {
        // Получаем данные из запроса
        $requestData = json_decode($request->getContent(), true);

        $baseCost = $requestData['base_cost'];
        $birthdate = \DateTime::createFromFormat('d.m.Y', $requestData['birthdate']);
        $startDate = new \DateTime($requestData['start_date']);

        // Рассчитываем возраст участника
        $age = $startDate->diff($birthdate)->y;

        foreach ($this->discountRules as $ruleAge => $discount) {
            if ($age < $ruleAge) {
                if (is_array($discount)) {
                    $discountedCost = min($baseCost * $discount['percentage'], $discount['max_discount']);
                } else {
                    $discountedCost = $baseCost * $discount;
                }
                return new JsonResponse(['cost' => $discountedCost]);
            }
        }

        return new JsonResponse(['cost' => $baseCost]);
    }
}