<?php

declare(strict_types=1);

namespace App\Tests\DataLoader;

use App\Entity\LearningMaterialStatusInterface;

class LearningMaterialStatusData extends AbstractDataLoader
{
    protected function getData()
    {
        $arr = array();

        $arr[] = array(
            'id' => LearningMaterialStatusInterface::IN_DRAFT,
            'title' => 'Draft'
        );
        $arr[] = array(
            'id' => LearningMaterialStatusInterface::FINALIZED,
            'title' => 'Final'
        );
        $arr[] = array(
            'id' => LearningMaterialStatusInterface::REVISED,
            'title' => 'Revised'
        );

        return $arr;
    }

    public function create()
    {
        return array(
            'id' => 4,
            'title' => $this->faker->text(10)
        );
    }

    public function createInvalid()
    {
        return [];
    }
}
