<?php

namespace App\Tests\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class TravelControllerTest extends WebTestCase
{
    /**
     * @dataProvider travelCostDataProvider
     */
    public function testCalculateTravelCost(array $data, int $expectedCost): void
    {
        $client = static::createClient();

        $client->request('POST', '/calculate-travel-cost', [], [], [], json_encode($data));

        $this->assertResponseIsSuccessful();

        $response = json_decode($client->getResponse()->getContent(), true);

        $this->assertArrayHasKey('cost', $response);
        $this->assertEquals($expectedCost, $response['cost']);
    }

    /**
     * Случаи для вычисления возможности скидок для детей
     * @return array[]
     */
    public static function travelCostDataProvider(): array
    {
        return [
            'Adult' => [
                ['base_cost' => 10000, 'birthdate' => '01.01.1990', 'start_date' => '2024-01-01'],
                10000,
            ],
            'Child 3 years' => [
                ['base_cost' => 10000, 'birthdate' => '01.01.2021', 'start_date' => '2024-01-01'],
                8000,
            ],
            'Child 6 years' => [
                ['base_cost' => 10000, 'birthdate' => '01.01.2018', 'start_date' => '2024-01-01'],
                3000,
            ],
            'Child 6 years > 4500' => [
                ['base_cost' => 15000, 'birthdate' => '01.01.2018', 'start_date' => '2024-01-01'],
                4500,
            ],
            'Child 12 years' => [
                ['base_cost' => 10000, 'birthdate' => '01.01.2012', 'start_date' => '2024-01-01'],
                1000,
            ]
        ];
    }
}
