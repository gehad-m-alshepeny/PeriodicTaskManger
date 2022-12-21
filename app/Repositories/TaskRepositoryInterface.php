<?php

namespace App\Repositories;

/**
* Interface TaskRepositoryInterface
* @package App\Repositories
*/
interface TaskRepositoryInterface
{
    public function getAll();

    public function getById($id);

    public function save($data);

    public function update($data, $id);

    public function delete($id);
}