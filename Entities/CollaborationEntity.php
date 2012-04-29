<?php

/**
 * This file is part of the Venne:CMS (https://github.com/Venne)
 *
 * Copyright (c) 2011, 2012 Josef Kříž (http://www.josef-kriz.cz)
 *
 * For the full copyright and license information, please view
 * the file license.txt that was distributed with this source code.
 */

namespace CollaboratorModule\Entities;

use Venne\Doctrine\ORM\BaseEntity;
use CoreModule\PageEntity;

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 * @Entity(repositoryClass="\Venne\Doctrine\ORM\BaseRepository")
 * @Table(name="collaboration")
 */
class CollaborationEntity extends BaseEntity
{


	/**
	 * @var \CoreModule\Entities\PageEntity
	 * @ManyToOne(targetEntity="\CoreModule\Entities\PageEntity", cascade={"persist"})
	 * @JoinColumn(name="page_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $page;

	/** @Column(type="boolean", name="`show`") */
	protected $show;

	/** @Column(type="boolean", name="`use`") */
	protected $use;



	public function __construct($name = "")
	{
		$this->show = true;
		$this->use = true;
	}



	public function getPage()
	{
		return $this->page;
	}



	public function setPage($page)
	{
		$this->page = $page;
	}



	public function getShow()
	{
		return $this->show;
	}



	public function setShow($show)
	{
		$this->show = $show;
	}



	public function getUse()
	{
		return $this->use;
	}



	public function setUse($use)
	{
		$this->use = $use;
	}

}
