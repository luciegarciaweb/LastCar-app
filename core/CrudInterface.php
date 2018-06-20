<?php
/**
 * Created by PhpStorm.
 * User: padbrain
 * Date: 29/05/18
 * Time: 11:36
 */

namespace BWB\Framework\mvc;


use BWB\Framework\mvc\Models\EntityModel;

interface CrudInterface
{
    public function retrieve(EntityModel $pPointer);
    public function update(array $pProperties);
    public function delete(int $pId);
    public function create(array $pProperties);
}