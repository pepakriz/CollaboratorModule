<?php

/**
 * This file is part of the Venne:CMS (https://github.com/Venne)
 *
 * Copyright (c) 2011, 2012 Josef Kříž (http://www.josef-kriz.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace CollaboratorModule;

use Nette\Config\Compiler;
use Nette\Config\Configurator;
use Nette\DI\Container;
use CoreModule\NavigationEntity;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 */
class Module extends \Venne\Module\BaseModule
{


	/** @var string */
	protected $version = "2.0";

	/** @var string */
	protected $description = "Collaborator module for content";



	public function compile(Compiler $compiler)
	{
		$compiler->addExtension($this->getName(), new DI\CollaboratorExtension($this->getPath(), $this->getNamespace()));
	}


	public function getForm(Container $container)
	{
		return new ModuleForm($container->configFormMapper, $this->getName());
	}

}
