<?php namespace Philf\Setting\interfaces;

/**
 * Class AdapterInterface
 * @package Philf\Setting\interfaces
 */
interface AdapterInterface
{

    public function load($configName): string;

    public function save($configName, $contents);

}