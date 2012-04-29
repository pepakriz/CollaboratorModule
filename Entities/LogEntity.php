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

/**
 * @author Josef Kříž <pepakriz@gmail.com>
 * @Entity(repositoryClass="\Venne\Doctrine\ORM\BaseRepository")
 * @Table(name="collaborationLog")
 */
class LogEntity extends BaseEntity
{


	/**
	 * @var \CollaboratorModule\Entities\UserEntity
	 * @ManyToOne(targetEntity="\CollaboratorModule\Entities\UserEntity")
	 * @JoinColumn(name="user_id", referencedColumnName="id", onDelete="CASCADE")
	 */
	protected $user;

	/** @Column(type="string") */
	protected $tag;

	/** @Column(type="datetime") */
	protected $last;

	/** @Column(type="integer") */
	protected $score;

	public function __construct(UserEntity $user, $tag, $score = 1)
	{
		parent::__construct();
		$this->user = $user;
		$this->tag = $tag;
		$this->score = $score;

		$this->updateDate();
	}

	public function updateDate()
	{
		$this->last = new \DateTime();
	}

	public function setScore($score)
	{
		$this->score = $score;
	}

	public function getScore()
	{
		return $this->score;
	}

	public function getLast()
	{
		return $this->last;
	}

	public function getTag()
	{
		return $this->tag;
	}


}
