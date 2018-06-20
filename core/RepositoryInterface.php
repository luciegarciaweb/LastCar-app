<?php
/**
 * Created by PhpStorm.
 * User: padbrain
 * Date: 29/05/18
 * Time: 11:36
 */

namespace BWB\Framework\mvc;

use BWB\Framework\mvc\Models\EntityModel;

interface RepositoryInterface
{
    public function getAll(EntityModel $pPointer);
    public function getAllBy(array $pPropVal);
}