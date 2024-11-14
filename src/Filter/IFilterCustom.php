<?php declare(strict_types = 1);

namespace Contributte\Datagrid\Filter;

use Contributte\Datagrid\Datagrid;

interface IFilterCustom
{
	public function getGrid(): Datagrid;

	public function getKey(): string;
}
